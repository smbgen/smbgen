# Git Sync Strategy — prtl7-app ↔ smbgen

## Overview

This repo (`L7-Media-Labs/prtl7-app`) is James's canonical project.
Alex also maintains a personal dev repo (`smbgen/smbgen`) and shares work bidirectionally.

The two `main` branches have **intentionally diverged** — smbgen/main carries its own
independent commits (branding, demos, README, etc.) that don't belong in prtl7-app.
Merging mains directly will always risk conflicts as both repos accumulate independent
history. The solution is to **never sync mains directly** — use feature branches instead.

---

## Remotes

```
origin  → https://github.com/L7-Media-Labs/prtl7-app.git  (fetch + push)
smbgen  → https://github.com/smbgen/smbgen.git            (fetch + push)
```

`git push` with no arguments only hits `origin`. smbgen always requires an explicit command.

---

## The Core Rule

> **Feature branches are the unit of sharing between repos — not `main`.**

Never merge `main → main` across repos. Always push the feature branch to both remotes
and let each repo merge it into their own main independently.

---

## Standard Workflow: Sharing Work to smbgen

### 1. Work on a feature branch (always)

```bash
git checkout -b feature/some-improvement main
# make changes, commit
git push origin feature/some-improvement
```

### 2. Merge into prtl7/main as normal

```bash
git checkout main
git merge feature/some-improvement --no-ff
git push origin main
```

### 3. Push the feature branch (not main) to smbgen

```bash
git push smbgen feature/some-improvement
```

### 4. Merge into smbgen/main without touching prtl7/main

```bash
git checkout -b smbgen-main-sync smbgen/main
git merge feature/some-improvement --no-ff
git push smbgen HEAD:main
git checkout main
git branch -d smbgen-main-sync
```

**Why this works:** The feature branch has no knowledge of either repo's diverged main
history. Each repo merges it independently — no conflicts, clean history.

---

## Pulling Work FROM smbgen into prtl7-app

```bash
git fetch smbgen
git checkout -b feature/from-smbgen smbgen/feature/whatever
# review, then merge into main as normal
git checkout main
git merge feature/from-smbgen --no-ff
git push origin main
```

---

## For Small Fixes (cherry-pick)

If a fix was committed directly to `main` without a feature branch, cherry-pick it:

```bash
git log --oneline main | head -10   # find the commit hash(es)

git checkout -b smbgen-patch smbgen/main
git cherry-pick <commit-hash>
git push smbgen HEAD:main
git checkout main && git branch -d smbgen-patch
```

---

## What to Avoid

| ❌ Causes merge conflicts | ✅ Use instead |
|--------------------------|---------------|
| `git merge main` across repos | Push feature branch to both repos |
| `git push smbgen main` | Cherry-pick or feature branch merge |
| Committing directly to `main` | Always use `feature/*` branches |

---

## When to Intentionally Sync main to smbgen

Only do this deliberately, after confirming both sides are compatible
(e.g. after a major release where you want smbgen to catch up fully):

```bash
git push smbgen main --force-with-lease   # overwrites smbgen/main — destructive
```

Do NOT do this routinely. Confirm with the team before force-pushing smbgen/main.

---

## Google Drive Integration (planned)

Two integration points under consideration:

### 1. Admin / Org Drive — Package Ingestion from Drive

Instead of uploading a ZIP, admin picks a Google Drive folder → files fetched and
run through `PackageIngestService` exactly as today.

**Flow:**
```
Admin → "Import from Drive" → OAuth (drive.readonly)
→ Google Picker → Select folder
→ App downloads files → PackageIngestService::parseFromDrive()
→ Review screen → Save package
```

**What's needed:**
- Google Picker API integration in the package create form
- `PackageIngestService::parseFromDrive(string $folderId, GoogleCredential $creds)`
- Expand admin OAuth scopes: add `drive.readonly`
- Store `source_drive_folder_id` on the package for re-sync

### 2. Client Spaces — Shared Drive Folder per Client

Each client gets a linked Google Drive folder. When a package is portal-ready,
deliverables are synced there automatically.

**Recommended approach (Option A — admin-managed):**
- Admin links a Drive folder ID to each client record
- App pushes portal-promoted files to that folder on package status change
- Client accesses via the shared Drive link — no client OAuth needed

**DB changes needed:**
```
clients
  + google_drive_folder_id   string nullable
  + google_drive_folder_url  string nullable

packages
  + source_drive_folder_id   string nullable
  + drive_synced_at          timestamp nullable
```

**Build order:**
1. Expand OAuth scopes (`drive.readonly` + `drive`)
2. Google Picker integration in package create form
3. `parseFromDrive()` in PackageIngestService
4. Client Drive folder linking (field on client edit form)
5. Auto-sync on package status change

**Already in place:**
- `google/apiclient ^2.18` installed
- `GoogleCredential` model (access_token, refresh_token per user)
- `GoogleDriveService.php` — basic Drive upload service
- Calendar OAuth already requests `drive.file` scope
- `clients.google_id` + `clients.google_linked_at` columns exist

---

## Presentations / Packages Module (Phase 1 + 2 — complete)

Merged into `main` via the `presentations` branch.

**Features:**
- Package ingestion from ZIP or multi-file upload
- Auto-classification: `HTML_PRESENTATION`, `HTML_EMAIL`, `PDF_DOCUMENT`,
  `MARKDOWN_RESEARCH`, `JSON_DATA`, `WORD_DOCUMENT`, `POWERPOINT`
- 3-tab package detail: Deliverables, Research, Email Templates
- Full-screen preview modal:
  - HTML/Email: sandboxed iframe
  - PDF: plain iframe (browser native renderer)
  - Word (.docx): mammoth.js renders DOCX → HTML client-side
  - Markdown: marked.js with `.prose-dark` styles
  - JSON: green monospace pre block
  - PowerPoint: download panel (no browser renderer)
- Email Composer integration: "Use in Compose" pre-fills subject, body, recipient
- Portal promote toggle per file
- Packages nav link in admin layout

**Phase 3 (planned):**
- Magic link generation + token table for client portal access
- Client portal view (promoted deliverables only)
- MCP exposure
- Google Drive ingestion (see above)
