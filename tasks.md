# INFX Landing Pages — Task Checklist

> Linked to: session plan.md (C:\Users\justp\.copilot\session-state\5c569e46-12ac-4e3f-8171-4f038c25f271\plan.md)
> Domain: zoho.infxsolutions.co.za
> Stack: Laravel 13 (Blade marketing site + file-based Livewire interactions + direct table persistence + Filament admin)

---

## Phase 0: Delivered platform foundation ✅

- [x] Provision the Laravel platform, database, queue, cache, and Filament admin surface
- [x] Build the data layer, funnel APIs, journey tracking, and Zoho CRM integration
- [x] Serve the public experience from Blade views in `resources/views/pages`
- [x] Move the qualification and consultation flows into file-based Livewire components in `resources/views/components/marketing`
- [x] Keep frontend assets limited to `resources/css/app.css` and `resources/js/app.js`

---

## Phase 1: Frontend migration cleanup ✅

- [x] Remove the obsolete Nuxt/Vue frontend from `apps/frontend/`
- [x] Remove stale Nuxt/Vue/Inertia repo metadata, skills, reference docs, and lockfile entries
- [x] Update CI, manifests, and config comments to target the Blade + Livewire + Vite frontend only
- [x] Reconcile `plan.md`, `tasks.md`, `AGENTS.md`, and `.github/copilot-instructions.md` with the shipped architecture

---

## Phase 2: Direct public funnel ownership ✅

- [x] Confirm the public qualifier writes directly to `journeys` and `journey_answers`
- [x] Confirm the public consultation form writes directly to `submissions` and links the active journey when present
- [x] Keep qualification context and safe prefill inside the Livewire-driven public flow instead of depending on frontend API calls
- [x] Realign public-flow tests and repo guidance around the direct Blade + Livewire persistence path

---

## Phase 3: Remaining hardening backlog

- [x] Reduce repo-wide PHPStan drift in `app/Support/PageCatalog.php` and the file-based Livewire marketing components
- [x] Add anti-bot protection and review rate limiting for funnel endpoints
- [ ] Review analytics/PageSense requirements for the Blade frontend
- [ ] Finalize production environment and deployment checklist
- [ ] Decide whether to retire the remaining compatibility `/api` funnel endpoints or keep them as supported integrations
- [ ] Document the public journey flow and API contract once requirements settle

---

## Phase 4: Landing page content and creative refresh

- [x] Replace fallback page copy with content adapted from `Reference\INFX LEAD MAGNET LANDING PAGE.md`, the product reference docs, and `Reference\FAQ SECTION (ADD TO WEBSITE).md`
- [x] Redesign `/` so the hero, trust signals, mid-page sections, and CTA hierarchy match a premium lead-generation experience
- [x] Redesign each product page so `zoho-one`, `zoho-marketing-plus`, and `zoho-workplace` stand on their own as direct-entry landing pages
- [x] Optimise and integrate the available Zoho SVGs, partner artwork, and INFX logo assets from `Reference\`
- [x] Keep landing-page body content code-managed in `App\Support\PageCatalog`
- [x] Polish contrast, headline wrapping, section surfaces, and single-brand header treatment across the public marketing pages
- [x] Refine the hero/header system so the header overlays the hero as a compact rounded white-leaning panel, scrolls away on downward movement, returns on upward movement, keeps gradients/headline contrast under control, and simplifies the footer brand lockup across public pages
- [x] Add a stricter public-page hero headline fit pass so long landing and product headlines stay visually contained beside the funnel/forms
- [x] Simplify the shared public hero by removing non-essential badge/highlight/stat rows so the headline, copy, and CTAs stay inside the dark readable field
- [x] Rework the homepage hero so the diagnostic card stays overlaid on the hero edge with stronger z-indexing and cleaner title/copy proportioning, eliminating the dead desktop hero space without breaking the intended composition
- [x] Replace internal process and architecture language in public copy with customer-facing marketing copy and sync the DB-backed page records to the refreshed `PageCatalog` content
- [x] Sharpen free-trial framing, product SVG usage, and weak media so qualification flows lead cleanly into trial-ready product pages

---

## Phase 5: Diagnostic qualification redesign

- [x] Define the main landing-page question set, routing weights, and tie-breaker rules using the lead-magnet reference as the starting point
- [x] Rebuild the homepage qualifier from a product picker into a multi-step recommendation flow that captures richer `journey_answers`
- [x] Add lighter product-page qualifying questions so direct visitors are still filtered before submitting a consultation request
- [x] Carry safe qualification context into the final consultation step and enrich the Zoho CRM lead description/meta payload
- [x] Expand feature tests around recommendation, routing, prefill, and context handoff
- [x] Refresh funnel question copy, routed recommendation messaging, and gated product CTA handoff

---

## Phase 6: Funnel security and anti-spam hardening

- [x] Integrate Cloudflare Turnstile for all public Livewire funnel submissions
- [x] Add rate limiting, duplicate-submission guards, and abuse review to the funnel paths
- [x] Review consent copy, privacy copy, and data-handling boundaries after the new journey flow is in place
- [x] Confirm anti-spam and throttling behavior across both the homepage and product-page flows

---

## Phase 7: DB-backed Zoho connection management

- [x] Move Zoho client credentials and related connection settings from env-backed config to encrypted database-backed storage
- [x] Build a Zoho-only Filament settings flow for connection details, scopes, token state, and health visibility
- [x] Add a Filament-managed Zoho OAuth authorization-code bootstrap flow for the initial connection
- [x] Refactor the Zoho integration layer to resolve persistent runtime settings safely
- [x] Add tests for encrypted credential storage, callback handling, token refresh, and failure paths

---

## Phase 8: Filament observability and operations

- [x] Expand the admin dashboard with funnel conversion, qualification drop-off, attribution, sync health, and lead-quality metrics
- [x] Add richer failed-push inspection plus retry or requeue controls inside Filament
- [x] Surface token expiry, connection health, and last-success indicators for operators
- [x] Track failed API pushes and operational anomalies without leaking secrets or raw PII
- [ ] Review alerting and reporting requirements for admins once the revised funnel is in place

---

## Phase 9: Final verification and rollout

- [x] Reconcile `plan.md`, `tasks.md`, and shipped behavior after implementation
- [x] Run Pint, PHPStan, targeted tests, and frontend build after each change set
- [x] Perform browser validation of the revised public journeys and Filament admin workflows
- [ ] Finalize deployment and runtime notes once credential management and bot protection are locked
