#!/usr/bin/env node
/**
 * prtl7 CRM — MCP Server
 *
 * Exposes CRM data (clients, leads, bookings, CMS pages) to Claude via the
 * Model Context Protocol. Communicates with the Laravel app's internal API
 * protected by a shared secret token (MCP_SECRET).
 *
 * Configuration (env vars):
 *   PRTL7_URL        — Base URL of the Laravel app  (default: http://prtl7-app.test)
 *   PRTL7_MCP_SECRET — Shared secret token matching the Laravel MCP_SECRET env var
 */

import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import { z } from "zod";

// ---------------------------------------------------------------------------
// Config
// ---------------------------------------------------------------------------

const BASE_URL = (process.env.PRTL7_URL ?? "http://prtl7-app.test").replace(/\/$/, "");
const SECRET = process.env.PRTL7_MCP_SECRET ?? "";

if (!SECRET) {
  process.stderr.write("ERROR: PRTL7_MCP_SECRET env var is required\n");
  process.exit(1);
}

// ---------------------------------------------------------------------------
// HTTP helper
// ---------------------------------------------------------------------------

async function api(
  method: "GET" | "POST" | "PATCH",
  path: string,
  body?: Record<string, unknown>
): Promise<unknown> {
  const url = `${BASE_URL}/mcp/v1${path}`;
  const res = await fetch(url, {
    method,
    headers: {
      Authorization: `Bearer ${SECRET}`,
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: body ? JSON.stringify(body) : undefined,
  });

  const text = await res.text();
  let json: unknown;
  try {
    json = JSON.parse(text);
  } catch {
    throw new Error(`Non-JSON response (${res.status}): ${text.slice(0, 300)}`);
  }

  if (!res.ok) {
    const msg = (json as Record<string, unknown>)?.message ?? res.statusText;
    throw new Error(`API ${res.status}: ${msg}`);
  }

  return json;
}

function qs(params: Record<string, string | number | boolean | undefined>): string {
  const p = new URLSearchParams();
  for (const [k, v] of Object.entries(params)) {
    if (v !== undefined && v !== "") p.set(k, String(v));
  }
  const str = p.toString();
  return str ? `?${str}` : "";
}

// ---------------------------------------------------------------------------
// Tool definitions
// ---------------------------------------------------------------------------

const TOOLS = [
  // ── Clients ──────────────────────────────────────────────────────────────
  {
    name: "list_clients",
    description: "List CRM clients. Optionally search by name/email/phone or filter to active only.",
    inputSchema: {
      type: "object",
      properties: {
        search:      { type: "string",  description: "Search name, email, or phone" },
        active_only: { type: "boolean", description: "Return only active clients" },
        limit:       { type: "number",  description: "Max results (default 50)" },
      },
    },
  },
  {
    name: "get_client",
    description: "Get full details of a single client by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Client ID" },
      },
      required: ["id"],
    },
  },
  {
    name: "create_client",
    description: "Create a new CRM client record.",
    inputSchema: {
      type: "object",
      properties: {
        name:             { type: "string",  description: "Full name (required)" },
        email:            { type: "string",  description: "Email address (required, must be unique)" },
        phone:            { type: "string",  description: "Phone number" },
        property_address: { type: "string",  description: "Service/property address" },
        notes:            { type: "string",  description: "Internal notes" },
        is_active:        { type: "boolean", description: "Active status (default true)" },
      },
      required: ["name", "email"],
    },
  },
  {
    name: "update_client",
    description: "Update an existing client's details.",
    inputSchema: {
      type: "object",
      properties: {
        id:               { type: "number",  description: "Client ID (required)" },
        name:             { type: "string",  description: "Full name" },
        phone:            { type: "string",  description: "Phone number" },
        property_address: { type: "string",  description: "Service/property address" },
        notes:            { type: "string",  description: "Internal notes" },
        is_active:        { type: "boolean", description: "Active status" },
      },
      required: ["id"],
    },
  },

  // ── Leads ─────────────────────────────────────────────────────────────────
  {
    name: "list_leads",
    description: "List lead form submissions. Filter by date range or search by name/email.",
    inputSchema: {
      type: "object",
      properties: {
        search: { type: "string", description: "Search name or email" },
        from:   { type: "string", description: "Start date (YYYY-MM-DD)" },
        to:     { type: "string", description: "End date (YYYY-MM-DD)" },
        limit:  { type: "number", description: "Max results (default 50)" },
      },
    },
  },
  {
    name: "get_lead",
    description: "Get full details of a lead submission including all form data.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Lead ID" },
      },
      required: ["id"],
    },
  },

  // ── Bookings ──────────────────────────────────────────────────────────────
  {
    name: "list_bookings",
    description: "List bookings. Filter by status (pending/confirmed/cancelled), date range, or search.",
    inputSchema: {
      type: "object",
      properties: {
        status: { type: "string",  description: "pending | confirmed | cancelled" },
        from:   { type: "string",  description: "Start date (YYYY-MM-DD)" },
        to:     { type: "string",  description: "End date (YYYY-MM-DD)" },
        search: { type: "string",  description: "Search customer name or email" },
        limit:  { type: "number",  description: "Max results (default 50)" },
      },
    },
  },
  {
    name: "get_booking",
    description: "Get full details of a single booking.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Booking ID" },
      },
      required: ["id"],
    },
  },
  {
    name: "create_booking",
    description: "Create a new booking.",
    inputSchema: {
      type: "object",
      properties: {
        customer_name:    { type: "string", description: "Customer full name (required)" },
        customer_email:   { type: "string", description: "Customer email (required)" },
        customer_phone:   { type: "string", description: "Customer phone" },
        booking_date:     { type: "string", description: "Date in YYYY-MM-DD (required)" },
        booking_time:     { type: "string", description: "Time in HH:MM 24h (required)" },
        duration:         { type: "number", description: "Duration in minutes (default 30)" },
        property_address: { type: "string", description: "Service address" },
        notes:            { type: "string", description: "Internal notes" },
        staff_id:         { type: "number", description: "Assigned staff user ID" },
      },
      required: ["customer_name", "customer_email", "booking_date", "booking_time"],
    },
  },
  {
    name: "update_booking_status",
    description: "Update a booking's status to pending, confirmed, or cancelled.",
    inputSchema: {
      type: "object",
      properties: {
        id:     { type: "number", description: "Booking ID (required)" },
        status: { type: "string", description: "pending | confirmed | cancelled (required)" },
        notes:  { type: "string", description: "Optional notes about the status change" },
      },
      required: ["id", "status"],
    },
  },

  // ── CMS Pages ─────────────────────────────────────────────────────────────
  {
    name: "list_cms_pages",
    description: "List CMS pages. Optionally filter to published only or search by title/slug.",
    inputSchema: {
      type: "object",
      properties: {
        published_only: { type: "boolean", description: "Only return published pages" },
        search:         { type: "string",  description: "Search title or slug" },
        limit:          { type: "number",  description: "Max results (default 50)" },
      },
    },
  },
  {
    name: "get_cms_page",
    description: "Get full content of a CMS page including body HTML and form config.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Page ID" },
      },
      required: ["id"],
    },
  },
  {
    name: "create_cms_page",
    description: "Create a new CMS page. The slug is auto-generated from the title if not provided.",
    inputSchema: {
      type: "object",
      properties: {
        title:                   { type: "string",  description: "Page title (required)" },
        slug:                    { type: "string",  description: "URL slug (auto-generated if omitted)" },
        body_content:            { type: "string",  description: "Main HTML body content" },
        head_content:            { type: "string",  description: "HTML injected into <head>" },
        cta_text:                { type: "string",  description: "Call-to-action button label" },
        cta_url:                 { type: "string",  description: "Call-to-action URL" },
        is_published:            { type: "boolean", description: "Publish immediately (default false)" },
        show_navbar:             { type: "boolean", description: "Show site navbar (default true)" },
        show_footer:             { type: "boolean", description: "Show site footer (default true)" },
        has_form:                { type: "boolean", description: "Include a lead capture form" },
        notification_email:      { type: "string",  description: "Email for form submission alerts" },
        form_submit_button_text: { type: "string",  description: "Form submit button label" },
        form_success_message:    { type: "string",  description: "Message shown after form submit" },
      },
      required: ["title"],
    },
  },
  {
    name: "update_cms_page",
    description: "Update an existing CMS page's content or settings.",
    inputSchema: {
      type: "object",
      properties: {
        id:                      { type: "number",  description: "Page ID (required)" },
        title:                   { type: "string",  description: "Page title" },
        body_content:            { type: "string",  description: "Main HTML body content" },
        head_content:            { type: "string",  description: "HTML injected into <head>" },
        cta_text:                { type: "string",  description: "CTA button label" },
        cta_url:                 { type: "string",  description: "CTA URL" },
        is_published:            { type: "boolean", description: "Publish/unpublish" },
        show_navbar:             { type: "boolean", description: "Show site navbar" },
        show_footer:             { type: "boolean", description: "Show site footer" },
        has_form:                { type: "boolean", description: "Toggle lead capture form" },
        notification_email:      { type: "string",  description: "Email for form alerts" },
        form_submit_button_text: { type: "string",  description: "Form submit button label" },
        form_success_message:    { type: "string",  description: "Post-submit message" },
      },
      required: ["id"],
    },
  },

  // ── Users ─────────────────────────────────────────────────────────────────
  {
    name: "list_users",
    description: "List system users. Filter by role (user/client/company_administrator).",
    inputSchema: {
      type: "object",
      properties: {
        role:   { type: "string", description: "user | client | company_administrator" },
        search: { type: "string", description: "Search name or email" },
        limit:  { type: "number", description: "Max results (default 50)" },
      },
    },
  },
  {
    name: "get_user",
    description: "Get details of a single system user by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "User ID" },
      },
      required: ["id"],
    },
  },
] as const;

// ---------------------------------------------------------------------------
// Server setup
// ---------------------------------------------------------------------------

const server = new Server(
  { name: "prtl7-crm", version: "1.0.0" },
  { capabilities: { tools: {} } }
);

server.setRequestHandler(ListToolsRequestSchema, async () => ({
  tools: TOOLS.map((t) => ({
    name: t.name,
    description: t.description,
    inputSchema: t.inputSchema,
  })),
}));

server.setRequestHandler(CallToolRequestSchema, async (req) => {
  const { name, arguments: args } = req.params;
  const a = (args ?? {}) as Record<string, unknown>;

  try {
    let result: unknown;

    switch (name) {
      // ── Clients ──
      case "list_clients":
        result = await api("GET", `/clients${qs({ search: a.search as string, active_only: a.active_only as boolean, limit: a.limit as number })}`);
        break;
      case "get_client":
        result = await api("GET", `/clients/${a.id}`);
        break;
      case "create_client":
        result = await api("POST", "/clients", a);
        break;
      case "update_client": {
        const { id, ...body } = a;
        result = await api("PATCH", `/clients/${id}`, body);
        break;
      }

      // ── Leads ──
      case "list_leads":
        result = await api("GET", `/leads${qs({ search: a.search as string, from: a.from as string, to: a.to as string, limit: a.limit as number })}`);
        break;
      case "get_lead":
        result = await api("GET", `/leads/${a.id}`);
        break;

      // ── Bookings ──
      case "list_bookings":
        result = await api("GET", `/bookings${qs({ status: a.status as string, from: a.from as string, to: a.to as string, search: a.search as string, limit: a.limit as number })}`);
        break;
      case "get_booking":
        result = await api("GET", `/bookings/${a.id}`);
        break;
      case "create_booking":
        result = await api("POST", "/bookings", a);
        break;
      case "update_booking_status": {
        const { id, ...body } = a;
        result = await api("PATCH", `/bookings/${id}/status`, body);
        break;
      }

      // ── CMS ──
      case "list_cms_pages":
        result = await api("GET", `/cms/pages${qs({ published_only: a.published_only as boolean, search: a.search as string, limit: a.limit as number })}`);
        break;
      case "get_cms_page":
        result = await api("GET", `/cms/pages/${a.id}`);
        break;
      case "create_cms_page":
        result = await api("POST", "/cms/pages", a);
        break;
      case "update_cms_page": {
        const { id, ...body } = a;
        result = await api("PATCH", `/cms/pages/${id}`, body);
        break;
      }

      // ── Users ──
      case "list_users":
        result = await api("GET", `/users${qs({ role: a.role as string, search: a.search as string, limit: a.limit as number })}`);
        break;
      case "get_user":
        result = await api("GET", `/users/${a.id}`);
        break;

      default:
        return { content: [{ type: "text", text: `Unknown tool: ${name}` }], isError: true };
    }

    return {
      content: [{ type: "text", text: JSON.stringify(result, null, 2) }],
    };
  } catch (err) {
    const message = err instanceof Error ? err.message : String(err);
    return {
      content: [{ type: "text", text: `Error: ${message}` }],
      isError: true,
    };
  }
});

// ---------------------------------------------------------------------------
// Start
// ---------------------------------------------------------------------------

const transport = new StdioServerTransport();
await server.connect(transport);
process.stderr.write("prtl7 MCP server running\n");
