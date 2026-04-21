# Social Media as a Service Module

## Overview

The Social Media module lets you compose, schedule, and publish posts to **LinkedIn**, **Facebook Pages**, and **Instagram Business** accounts — all from the admin dashboard. Posts can include images sourced from your CMS job photo library or client file uploads. A built-in scheduler publishes posts automatically at the time you choose.

---

## Multi-Tenant Compatibility

The module is **fully multi-tenant compatible**. Admin routes (`/admin/social-media/*`) are loaded inside the `['tenant', 'tenantOnly', 'tenantUser']` middleware group, meaning:

- Each tenant's social accounts, posts, and publish history are completely isolated in their own database.
- One tenant cannot see or publish to another tenant's accounts.
- The feature flag (`FEATURE_SOCIAL_MEDIA`) can be set per-tenant by toggling it in that tenant's `.env` or environment configuration.

---

## Enabling the Module

1. Add the feature flag to your `.env`:

   ```env
   FEATURE_SOCIAL_MEDIA=true
   ```

2. Clear config cache:

   ```bash
   php artisan config:clear
   ```

3. Run the database migrations (first-time setup only):

   ```bash
   php artisan migrate
   ```

4. Ensure a queue worker is running (posts are published via queued jobs):

   ```bash
   php artisan queue:work
   ```

5. Ensure the scheduler is running (dispatches due posts every minute):

   ```bash
   php artisan schedule:run
   # or via a cron: * * * * * cd /path/to/project && php artisan schedule:run
   ```

---

## Platform Configuration

Credentials for each platform are stored as **server-side environment variables** in `.env`. There is currently no UI for users to enter their own keys — credentials must be set by whoever manages the server/deployment.

### Facebook & Instagram (Meta Graph API)

Facebook and Instagram share a single Meta app credential set.

```env
META_APP_ID=your_meta_app_id
META_APP_SECRET=your_meta_app_secret
META_REDIRECT_URI=https://yourdomain.com/social/meta/callback
```

**What you need:**
- A Meta Developer account at [developers.facebook.com](https://developers.facebook.com)
- A Meta App with **Facebook Login** and **Instagram Basic Display / Instagram Graph API** products enabled
- The **Page ID** and **Page Access Token** for the Facebook Page you want to post to
- For Instagram: an **Instagram Business Account** linked to your Facebook Page, and its **Instagram User ID**

**How tokens are stored:**
Once a social account is connected, the system stores:
- `access_token` — the user-level OAuth token
- `page_access_token` — the long-lived Page Access Token (Meta pages are valid for ~60 days)
- `platform_page_id` — the Page or IG Business Account ID

Meta page access tokens are refreshed automatically via `/me/accounts` when a publish attempt detects a token problem.

---

### LinkedIn

```env
LINKEDIN_CLIENT_ID=your_linkedin_client_id
LINKEDIN_CLIENT_SECRET=your_linkedin_client_secret
LINKEDIN_REDIRECT_URI=https://yourdomain.com/social/linkedin/callback
```

**What you need:**
- A LinkedIn Developer account at [developer.linkedin.com](https://developer.linkedin.com)
- An App with the **Share on LinkedIn** and **Sign In with LinkedIn** products enabled
- For **Organization pages**: the `r_organization_social` and `w_organization_social` scopes
- For **personal profiles**: the `w_member_social` scope

**How tokens are stored:**
- `access_token` — the OAuth bearer token
- `refresh_token` — used to automatically refresh expired tokens
- `token_expires_at` — the expiry datetime; the system refreshes automatically before publishing
- `platform_user_id` / `platform_page_id` — the LinkedIn Person URN or Organization URN

---

## Adding a Social Account (Current Flow)

Currently, accounts are added **manually** through the admin UI. An OAuth callback flow can be wired up when ready. To add an account:

1. Navigate to **Admin → Social Media → Accounts**
2. Click **Connect Account**
3. Select the platform, enter the account name and URL
4. Save — you can update the underlying token fields directly in the database or via a future OAuth flow

---

## Creating and Publishing Posts

### Creating a Post

1. Navigate to **Admin → Social Media → Posts → New Post**
2. Write your caption (supports all plain text; LinkedIn max 3,000 characters; Meta max 63,206 characters)
3. Select one or more **connected accounts** to publish to — you can cross-post to multiple platforms at once
4. Optionally select images from your CMS job photo library
5. Optionally set a **Scheduled At** date/time — if set, the post will be published automatically at that time; if left blank, it saves as a draft
6. Optionally check **Requires Approval** — the post will not publish until an admin approves it

### Post Statuses

| Status | Meaning |
|--------|---------|
| `draft` | Saved but not yet scheduled or published |
| `scheduled` | Will publish at the `scheduled_at` time |
| `publishing` | Currently being dispatched to platform APIs |
| `published` | Successfully published to all targets |
| `failed` | One or more targets failed after retries |
| `cancelled` | Manually cancelled before publishing |

### Retrying Failed Posts

If a platform target fails (e.g. due to a token error or API outage), you can retry from the post detail screen. Each target supports up to **3 retry attempts** with exponential back-off (5 min → 15 min → 45 min between attempts).

---

## How Scheduling Works

The `social:publish-scheduled` Artisan command runs **every minute** via Laravel's built-in scheduler. It:

1. Finds all `scheduled` posts whose `scheduled_at` is now or in the past
2. Skips any post that has `requires_approval = true` but hasn't been approved yet
3. Marks each post as `publishing` to prevent double-dispatch
4. Dispatches a `PublishSocialPostJob` for each pending target (one job per platform per post)

Each job uses **idempotency keys** to prevent duplicate publishes even if a job is re-queued by the queue driver.

---

## Images & Media

Posts can include images sourced from two places:

- **CMS Images** — photos already in your job photo library (uploaded via the CMS module)
- **Client Files** — images from client file uploads (image MIME types only)

Platform limits:
- LinkedIn: up to **9 images** per post (uploaded via LinkedIn's Asset Upload API)
- Facebook: up to **10 images** per post (uploaded via Graph API as unpublished photos, then attached)
- Instagram: **1 image** for a single post, **2–10 images** for a carousel; at least 1 image is required

---

## Publish Audit Trail

Every publish attempt (success or failure) is logged to the `social_publish_attempts` table with:
- Platform, timestamp, status (published / failed)
- API response body
- Error code and message (on failure)
- Idempotency key

You can view the full attempt history for any post on its detail screen in the admin.

---

## Feature Flag Reference

| Flag | Default | Description |
|------|---------|-------------|
| `FEATURE_SOCIAL_MEDIA` | `false` | Enable/disable the entire Social Media module |

Config key: `config('business.features.social_media')`

When `false`, all social media routes return 404 and the scheduler command does nothing.
