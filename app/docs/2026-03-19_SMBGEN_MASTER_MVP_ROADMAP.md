# smbgen Master MVP Roadmap

Date: 2026-03-19
Branch Target: feature/gap-coverage-foundation
Status: Planning document for phased MVP execution

## Purpose

This roadmap defines the path from the current smbgen codebase to a fully working MVP built around a modular product portfolio.

The near-term goal is not to finish every promised feature across every product. The near-term goal is to establish the core platform architecture that allows products to be installed, enabled, entitled, branded, and surfaced consistently across:

- smbgen internal deployment
- white-label client deployments such as Portal7
- customer subscriptions to individual products such as EXTREME
- future managed-service and self-serve product delivery

This document is the master task list for that work.

## Executive Summary

The current codebase already contains partial foundations for:

- multi-tenant and agency concepts
- tenant module visibility
- EXTREME as a module-like product
- early SIGNAL, RELAY, and SURGE models/controllers/jobs
- queue-backed patterns for always-on functionality

The main blocker is not the total absence of foundations. The main blocker is the lack of a single product registry and entitlement model that ties together:

- installed modules
- deployment-level visibility
- tenant-level enablement
- client/user-level access
- branding aliases per deployment
- admin and client UI rendering

The first implementation priority is therefore:

1. establish smbgen-core as the platform shell
2. build a product registry and module architecture
3. wire admin and client product surfaces to that registry
4. make EXTREME the first fully subscription-aware product surface
5. make SIGNAL the first fully operational product module, starting with LinkedIn company page connection and automated posting

## Product Model

### Platform Layers

The platform should be separated into three layers.

#### 1. smbgen-core

Core owns shared platform concerns:

- authentication and identity
- billing primitives and subscriptions
- clients and user relationships
- files and messaging
- navigation shells and dashboard shells
- product registry
- module loading
- entitlement checks
- deployment branding and aliases
- queue and scheduling infrastructure
- audit logging and activity tracking

#### 2. Product Modules

Each product is a module with its own routes, services, UI, policies, jobs, and storage as needed.

Initial module lineup:

- SIGNAL
- RELAY
- SURGE
- CAST
- VAULT
- EXTREME

Each module must declare:

- module slug
- product name
- optional deployment alias name
- icon token
- accent color token
- short description
- admin route
- client route
- tenant route if applicable
- install status check
- configuration status check
- entitlement requirements
- dependencies on core features

#### 3. Deployment Profile

Deployment profile determines how smbgen is presented in a specific environment.

Examples:

- internal smbgen deployment shows EXTREME as EXTREME
- Portal7 deployment can show the same underlying product as Clean Slate
- a client deployment can hide products not relevant to that deployment

Deployment profile controls:

- brand name
- product aliases
- visible products
- default enabled products
- navigation choices
- public marketing references

## Core Architectural Rules

These rules should govern all implementation work.

### Rule 1: Installed, Enabled, and Entitled are different

Each product must support three independent states.

- Installed: code exists in the deployment
- Enabled: deployment wants the product exposed in the UI
- Entitled: the current tenant, client, or user has access

### Rule 2: Product definition is not branding

EXTREME and Clean Slate should not be separate underlying modules if they are the same product capability. Clean Slate should be an alias/presentation layer applied by a deployment profile.

### Rule 3: Admin, tenant, and client surfaces all read from one registry

Do not hardcode per-product cards separately in multiple views. Use one shared registry/service that all surfaces render from.

### Rule 4: Queue-backed behavior is mandatory for always-on features

Any social scheduling, drip email, lead nurturing, workflow execution, or platform automation must run through queues and scheduler infrastructure.

### Rule 5: The first MVP is architecture plus one real product

The first real proof point is SIGNAL with LinkedIn company page posting, not six half-built product shells.

## MVP Definition

The fully working MVP for this roadmap means all of the following are true.

### Platform MVP

- smbgen-core exists conceptually in code organization and shared services
- a product registry drives admin and client product cards
- products can appear or disappear based on deployment config and entitlements
- admin has a dedicated Products area in navigation
- admin dashboard shows product/module status widget
- client portal shows only relevant product/service cards plus files, payments, and subscription info
- EXTREME has a working subscription entry surface as a first productized offering
- queues, scheduler, and background job flow are operational

### SIGNAL MVP

- LinkedIn OAuth connection exists
- LinkedIn company page selection exists
- account/token storage is robust enough for scheduled posting
- admin can create a LinkedIn post and schedule it
- queued publishing runs and handles success/failure states
- admin sees connection and publishing status in SIGNAL
- entitled users see SIGNAL as an active product card

### EXTREME MVP for subscription interface

- users can discover EXTREME as a product
- users can subscribe to EXTREME
- client portal shows EXTREME subscription/access status clearly
- deployment aliasing allows EXTREME to be presented differently in white-label contexts later

## Current State Snapshot

### Already Present

- tenant module arrays and plan-driven module assignment
- tenant module dashboard and tenant module routes
- early admin surfaces for SIGNAL, RELAY, SURGE
- EXTREME/CleanSlate module service provider and routes
- agency portal and managed site concepts
- jobs for social publishing, email sequence steps, and lead scoring
- deal, social post, email sequence, and related models

### Missing or Incomplete

- central product registry service
- module manifest pattern reused across all products
- deployment aliasing and branding layer for products
- unified entitlement system across tenant/client/user
- admin Products page
- dashboard product status widget
- client portal product card architecture
- real LinkedIn integration for SIGNAL
- hardened queue/scheduler operations and monitoring
- productized subscription experience across more than EXTREME

## Phase Plan

## Phase 0: Architectural Decisions and Inventory

Goal: lock the platform model before expanding features.

Deliverables:

- confirm module vocabulary: product, module, deployment profile, entitlement
- confirm whether EXTREME and Clean Slate are one module with aliasing
- define who can hold entitlements: tenant, client, user, subscription, or account
- define whether tenant modules and client product access share one source of truth or separate tables
- document MVP acceptance criteria for SIGNAL and EXTREME

Master tasks:

- [ ] Define canonical product slugs for all current and future products
- [ ] Define product registry shape as config, class registry, or manifest objects
- [ ] Define deployment profile model and storage location
- [ ] Define entitlement ownership model
- [ ] Define branding alias rules for products per deployment
- [ ] Define visibility resolution order: installed -> enabled -> entitled -> visible
- [ ] Define queue and scheduler baseline required before automation features ship
- [ ] Decide whether tenant plan logic remains temporary or is replaced by product entitlements

Exit criteria:

- one agreed architecture document exists and no further product UI work is started without it

## Phase 1: smbgen-core Product Registry Foundation

Goal: create the shared module infrastructure that all products use.

Deliverables:

- product registry service
- shared product metadata structure
- visibility and entitlement resolution service
- reusable product card/view model for admin and client surfaces
- deployment-aware product aliasing support

Master tasks:

- [ ] Create a ProductDefinition structure for slug, name, alias, icon, color, routes, description, and status callbacks
- [ ] Create a ProductRegistry service that returns installed products
- [ ] Create a ProductVisibility service that resolves installed, enabled, entitled, and visible state
- [ ] Create a deployment branding/alias service for product labels and card text
- [ ] Add config for deployment-level visible products and aliases
- [ ] Add tests for registry resolution and visibility rules
- [ ] Refactor existing tenant module card data to read from the registry instead of hardcoded arrays
- [ ] Refactor tenant sidebar module links to read from the registry instead of hardcoded conditionals
- [ ] Add a core product card partial/component reusable across admin and client surfaces

Exit criteria:

- the system can render product cards from one source of truth without hardcoded per-view module arrays

## Phase 2: Admin Products Area and Dashboard Status Widget

Goal: give admins a dedicated place to manage and understand product availability.

Deliverables:

- new Products page in admin navigation
- product cards with status badges
- dashboard widget summarizing product health/status

Master tasks:

- [ ] Add Products page route and controller in admin area
- [ ] Add Products item to admin navbar/sidebar
- [ ] Build Products page card grid using the core product card data
- [ ] Show per-product badges for installed, enabled, configured, and entitled-ready
- [ ] Show route links into each product admin surface when installed
- [ ] Add per-product notes for missing configuration or setup blockers
- [ ] Add product status widget to admin dashboard home page
- [ ] Add counts for active products, blocked products, and products needing setup
- [ ] Add tests for Products page and dashboard widget visibility

Exit criteria:

- admins can open one Products area and see the entire portfolio and each product's current state

## Phase 3: Client Portal Simplification and Product Access Model

Goal: make the client portal cleaner and product-aware.

Target client portal experience:

- Files
- Payments
- Subscription
- Your Products

Deliverables:

- simplified client dashboard
- product/service cards for entitled offerings
- subscription visibility for EXTREME and future services

Master tasks:

- [ ] Audit current client dashboard and remove nonessential internal-style widgets
- [ ] Build a client-facing product cards section driven from the registry
- [ ] Add subscription summary card showing what the client is subscribed to
- [ ] Show EXTREME as a subscribable product in client portal
- [ ] Support deployment-specific product aliasing in client views
- [ ] Ensure products can be hidden completely for client deployments where not relevant
- [ ] Preserve files, payments, and messaging access where required
- [ ] Add tests for client product visibility and subscription display

Exit criteria:

- a client logs in and sees only relevant services/products plus core account actions

## Phase 4: EXTREME as First Productized Subscription Interface

Goal: make EXTREME the first polished product experience in the portfolio.

Deliverables:

- EXTREME discoverability in Products area and client portal
- consistent subscription/access language
- alignment between EXTREME and future reusable subscription logic

Master tasks:

- [ ] Audit current CleanSlate/EXTREME routes, controllers, and views for reuse as the product template
- [ ] Align naming so EXTREME is the canonical product and Clean Slate can be a deployment alias where needed
- [ ] Add product metadata for EXTREME to the registry
- [ ] Show subscription state and onboarding state in EXTREME product card/status details
- [ ] Expose EXTREME in client portal as an active or subscribable product
- [ ] Identify reusable subscription patterns that future products can adopt
- [ ] Add tests covering EXTREME product visibility and access flow

Exit criteria:

- EXTREME works as the first clean example of a productized offering on smbgen-core

## Phase 5: SIGNAL MVP Foundation

Goal: build the real technical foundation for SIGNAL as the first operational module.

Deliverables:

- SIGNAL product metadata in the registry
- upgraded social account model for external platform auth
- LinkedIn integration architecture
- robust publishing pipeline

Master tasks:

- [ ] Audit existing SIGNAL data model and identify missing fields for external auth and page publishing
- [ ] Extend social account storage to support provider-specific credential metadata safely
- [ ] Decide whether LinkedIn account/page data should live in generic social tables or LinkedIn-specific tables
- [ ] Add SIGNAL product definition to the registry
- [ ] Add SIGNAL setup status checks for connection, page selection, and queue readiness
- [ ] Create service abstractions for social providers and posting
- [ ] Create LinkedIn-specific provider service and publisher service
- [ ] Add tests for SIGNAL setup status and product visibility

Exit criteria:

- SIGNAL is represented as a real installable/configurable product in the platform

## Phase 6: SIGNAL LinkedIn Company Page Connection

Goal: connect a LinkedIn company page as the first live social automation capability.

Deliverables:

- LinkedIn OAuth flow
- token persistence and refresh logic
- company page selection UI
- connection status in admin SIGNAL area

Master tasks:

- [ ] Review LinkedIn API requirements and app setup constraints for company page publishing
- [ ] Add LinkedIn app credentials configuration support
- [ ] Build OAuth start and callback routes/controllers
- [ ] Store account tokens securely and track expiration metadata
- [ ] Fetch available LinkedIn organizations/pages for the connected account
- [ ] Add UI to select default LinkedIn company page
- [ ] Display connected/disconnected/expired states in SIGNAL admin UI
- [ ] Handle reconnect and disconnect flows cleanly
- [ ] Add tests for connection flow, callback handling, and selected page persistence

Exit criteria:

- admin can connect LinkedIn and select the target company page for posting

## Phase 7: SIGNAL Scheduled Posting MVP

Goal: ship the first real automated social workflow.

Deliverables:

- create/edit/schedule post flow for LinkedIn
- queued publisher job with real LinkedIn API call
- success/failure lifecycle tracking
- basic post history/status UI

Master tasks:

- [ ] Refactor social post create flow to support provider-specific validation and scheduling
- [ ] Add publishable payload formatting for LinkedIn posts
- [ ] Replace stub publish job behavior with real LinkedIn publishing call
- [ ] Add retry/backoff and failure logging strategy
- [ ] Add scheduler/queue setup instructions and operational checks
- [ ] Add status timestamps and failure messages to SIGNAL UI
- [ ] Add basic filters for draft, scheduled, published, and failed posts
- [ ] Add tests for scheduled dispatch, publishing success, and failure handling

Exit criteria:

- admin can create a LinkedIn company page post, schedule it, and observe queued publishing outcomes

## Phase 8: Queue, Scheduler, and Operations Hardening

Goal: make automation reliable enough to support SIGNAL and future products.

Deliverables:

- known-good queue configuration
- known-good scheduler configuration
- operational visibility for failed jobs and automation readiness

Master tasks:

- [ ] Audit current queue driver and worker expectations across environments
- [ ] Verify scheduler is configured and documented for local and production
- [ ] Add failed job handling and monitoring baseline
- [ ] Add queue health indicators to admin Products page or dashboard widget
- [ ] Add per-product readiness checks for queue-dependent products
- [ ] Decide whether Horizon is required for MVP or deferred to post-MVP
- [ ] Add minimal operational documentation for workers and scheduled tasks
- [ ] Add tests for queue-dispatched workflows where practical

Exit criteria:

- always-on product features are supported by known operational infrastructure

## Phase 9: RELAY, SURGE, VAULT, CAST Foundation Alignment

Goal: bring the rest of the portfolio into the same module model even if their full feature depth comes later.

Deliverables:

- all products registered consistently
- all products display correctly in admin and client surfaces
- all products have setup and status metadata

Master tasks:

- [ ] Add RELAY product definition to registry
- [ ] Add SURGE product definition to registry
- [ ] Add CAST product definition to registry
- [ ] Add VAULT product definition to registry
- [ ] Define minimal setup status callbacks for each
- [ ] Replace hardcoded product copy/icons/colors in multiple views with registry-driven rendering
- [ ] Align product descriptions with actual build state to avoid overstating capabilities
- [ ] Add tests for portfolio rendering consistency

Exit criteria:

- every product appears through the same architecture even if only SIGNAL and EXTREME are feature-rich in MVP

## Phase 10: Agency and White-Label Readiness Foundation

Goal: make the platform safe to extend into Portal7-style branded deployments without forking product logic.

Deliverables:

- deployment profile support
- deployment aliasing for product names
- visibility control per deployment

Master tasks:

- [ ] Define deployment profile storage and resolution strategy
- [ ] Add product alias support per deployment profile
- [ ] Add deployment-level visible products configuration
- [ ] Ensure admin/client product rendering respects deployment profile
- [ ] Ensure internal smbgen can show EXTREME while client deployments can show Clean Slate alias if needed
- [ ] Add tests for deployment-specific naming and visibility

Exit criteria:

- product identity is stable, branding is deployment-specific, and white-labeling does not require duplicated code paths

## Product-by-Product MVP Scope

## SIGNAL

MVP includes:

- LinkedIn connection
- LinkedIn company page selection
- create post
- schedule post
- queue publish
- status tracking

Deferred after MVP:

- Instagram publishing
- X publishing
- Facebook publishing
- brand voice tuning
- analytics and engagement CRM sync
- multi-platform content calendars

## RELAY

MVP foundation only:

- registered product
- existing sequence concepts cleaned up
- product visibility and setup status

Deferred after MVP:

- full nurture engine
- broadcast campaigns
- segmentation
- AI email copy generation
- deliverability dashboards beyond current basics

## SURGE

MVP foundation only:

- registered product
- deal pipeline and lead scoring surfaces aligned under the product model

Deferred after MVP:

- paid campaign automation
- outbound automation
- referral loops
- partnership tracking

## CAST

MVP foundation only:

- registered product
- site/managed-site concepts aligned to module architecture

Deferred after MVP:

- multi-brand self-serve editing polish
- design system tooling
- SEO/PageSpeed audit reporting

## VAULT

MVP foundation only:

- registered product
- client file/document surface aligned with product visibility

Deferred after MVP:

- approvals
- version history
- automated follow-ups from CRM

## EXTREME

MVP includes:

- first polished subscription-aware product card and access flow
- reusable productized subscription model
- aliasable branding for future deployment use

Deferred after MVP:

- GitHub push
- terminal stream
- agency seats
- stack presets
- one-click deploy
- generated app multi-tenancy scaffolding

## Master Task List by Priority

### Priority 1: Must happen before meaningful product work

- [ ] Finalize modular architecture decisions
- [ ] Build product registry and visibility services
- [ ] Refactor admin/tenant/client product rendering to registry-driven cards
- [ ] Add admin Products page and dashboard widget
- [ ] Harden queue and scheduler readiness checks

### Priority 2: First productized customer experience

- [ ] Align EXTREME as the first productized subscription interface
- [ ] Add EXTREME card and status into client portal
- [ ] Simplify client portal to files, payments, subscription, and products

### Priority 3: First true operational module

- [ ] Build SIGNAL LinkedIn connection flow
- [ ] Add LinkedIn company page selection
- [ ] Ship scheduled LinkedIn posting via queue
- [ ] Add SIGNAL operational status UI

### Priority 4: Platform consistency across the rest of the lineup

- [ ] Register RELAY, SURGE, CAST, VAULT in the product registry
- [ ] Replace hardcoded module metadata in all remaining surfaces
- [ ] Add deployment aliasing for branded installs such as Portal7

### Priority 5: Post-MVP expansion

- [ ] Expand RELAY into real campaigns and segmentation
- [ ] Expand SURGE into full pipeline automation
- [ ] Expand SIGNAL to more platforms and analytics
- [ ] Expand VAULT into approvals/versioning
- [ ] Expand EXTREME into GitHub/deploy/streaming features

## Suggested Execution Order for Tasking

When work resumes, task in this order.

### Sprint A: Core module architecture

- product registry
- visibility service
- admin Products page
- admin dashboard widget
- tenant/client card refactor

### Sprint B: Client portal and EXTREME alignment

- client portal simplification
- EXTREME product card/access state
- subscription presentation cleanup
- deployment alias groundwork

### Sprint C: SIGNAL integration foundation

- data model updates
- LinkedIn auth architecture
- SIGNAL setup states
- queue readiness checks

### Sprint D: SIGNAL live MVP

- LinkedIn OAuth
- page selection
- scheduled posting
- status tracking
- failure/retry handling

### Sprint E: Remaining product alignment

- RELAY/SURGE/CAST/VAULT registry integration
- deployment aliasing
- white-label controls

## Acceptance Criteria for MVP Completion

MVP is complete when all of the following are true.

- smbgen-core drives all product visibility from a single registry
- admin has a Products page and dashboard module status widget
- client portal shows product/service cards instead of mixed internal tooling
- EXTREME is available as a clean subscription-aware product experience
- deployment branding can rename a product without duplicating module logic
- SIGNAL supports LinkedIn company page connection and scheduled posting
- queue and scheduler infrastructure are operational enough to support automation
- product cards can be hidden or shown based on deployment and entitlement state

## Risks and Watchouts

### Risk: hardcoding product logic in views again

Mitigation:

- force all product surfaces to render from the registry

### Risk: mixing deployment branding with product identity

Mitigation:

- use deployment aliases, not new module slugs

### Risk: trying to fully build every product before proving one real module

Mitigation:

- make SIGNAL the first operational module and EXTREME the first subscription product

### Risk: queue-dependent promises without worker discipline

Mitigation:

- ship queue operations baseline before claiming always-on automation

### Risk: overcomplicating entitlement ownership too early

Mitigation:

- start with a clear minimal entitlement model and expand only after the first product flows work

## Immediate Next Task Recommendation

When this roadmap is picked up again, the first implementation ticket should be:

Build the product registry and visibility service, then refactor admin, tenant, and client product cards to render from that single source of truth.

That is the keystone task that unlocks:

- the new admin Products page
- the dashboard widget
- deployment-specific product visibility
- EXTREME productization cleanup
- SIGNAL MVP integration without more hardcoded duplication
