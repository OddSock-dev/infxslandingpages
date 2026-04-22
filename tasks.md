# Zoho Landing Pages — Task Checklist

> Linked to: session plan.md (C:/Users/justp/.copilot/session-state/c767821a-a4ec-46d0-8c5c-567b47ce8ee2/plan.md)
> Domain: zoho.infxsolutions.co.za
> Stack: Nuxt 3 (SSG) + Laravel 13 (API + Filament admin)

---

## Phase 0: Foundation & Infrastructure ✅

- [x] Add `docker-compose.yml` (MariaDB 10.11 only — no Redis needed on this stack)
- [x] Start Docker MariaDB, verify connectivity
- [x] Switch Laravel DB: SQLite → MariaDB (`DB_*` env vars)
- [x] Switch cache store: `database` → `file` (`CACHE_STORE=file` — simpler, no infra dep)
- [x] Queue driver stays as `database` — already the starter kit default, no Redis needed
- [x] Strip Inertia/Vue boilerplate from Laravel (resources/js/, web.php Inertia routes, Inertia middleware)
- [x] Strip Inertia middleware from `bootstrap/app.php`
- [x] Clean up `package.json` at repo root (remove Inertia/Vue deps — Nuxt will be in apps/frontend)
- [x] Install Filament (resolved to v5.6 — Filament 3.x only supports Laravel ≤12)
- [x] Run `php artisan filament:install --panels --no-interaction`
- [x] Configure Filament panel: id=`admin`, path=`/admin`
- [x] Extend `pnpm-workspace.yaml` to include `apps/*`
- [x] Initialize Nuxt 4 in `apps/frontend/` via `nuxi init`
- [x] Install Nuxt modules: `@tailwindcss/vite` (v4), `@pinia/nuxt@0.11.3`, `@vueuse/nuxt@14.2.1`
- [x] Configure `nuxt.config.ts`: prerender all routes, runtimeConfig API base URL, Tailwind v4 via Vite plugin
- [x] PHPStan level 10 — zero errors (added `vendor/pestphp/pest/extension.neon` to includes)
- [x] `php artisan test --compact` — 1 test passing
- [x] Verify: MariaDB Docker up, Laravel Herd serving, Nuxt `nuxt prepare` clean

---

## Phase 1: Laravel Data Layer ✅

- [x] `php artisan make:migration create_pages_table`
- [x] `php artisan make:migration create_journeys_table`
- [x] `php artisan make:migration create_journey_answers_table`
- [x] `php artisan make:migration create_submissions_table`
- [x] `php artisan make:migration create_crm_sync_attempts_table`
- [x] `php artisan make:model Page --factory`
- [x] `php artisan make:model Journey --factory`
- [x] `php artisan make:model JourneyAnswer --factory`
- [x] `php artisan make:model Submission --factory`
- [x] `php artisan make:model CrmSyncAttempt --factory`
- [x] Add typed casts, fillable, relationships to all models
- [x] Add `encrypted` cast to `Submission::pii_json` (encrypted:array) and `CrmSyncAttempt` payloads
- [x] Add `expires_at` scope and ULID generation to `Journey`
- [x] Create `DatabaseSeeder` to seed 4 default `pages` records (idempotent via updateOrCreate)
- [x] `php artisan migrate --seed` — verify clean run
- [x] `vendor/bin/phpstan analyse --no-progress` — zero errors
- [x] `vendor/bin/pint --dirty` — zero errors

---

## Phase 2: Laravel API Layer ✅

- [x] Create `routes/api.php` with all 4 endpoints
- [x] `php artisan make:controller Api/FunnelController`
- [x] `php artisan make:controller Api/JourneyController`
- [x] `php artisan make:controller Api/PageConfigController`
- [x] `php artisan make:request QualifyRequest`
- [x] `php artisan make:request SubmitRequest`
- [x] Create `app/DTOs/QualificationAnswersData.php`
- [x] Create `app/DTOs/SubmissionData.php`
- [x] Create `app/DTOs/PrefillData.php`
- [x] Create `app/Services/QualificationService.php` (routing logic)
- [x] Create `app/Services/PrefillService.php` (safe field extraction)
- [x] Create `app/Services/SubmissionRecorder.php` (save + dispatch)
- [x] Configure CORS: `config/cors.php` — allow `localhost:3000`, `zoho.infxsolutions.co.za`
- [x] Class-based feature tests (PHPUnit style — avoids PHPStan `$this: TestCall` cascade)
- [x] `php artisan test --compact` — 25/25 pass
- [x] `vendor/bin/phpstan analyse --no-progress` — zero errors

---

## Phase 3: Zoho Integration Pipeline ✅

- [x] Create `app/Integrations/Zoho/ZohoCrmClient.php` (OAuth2 + HTTP, Cache::lock for token refresh)
- [x] Create `app/Integrations/Zoho/LeadPayloadMapper.php` (DTO → Zoho schema)
- [x] Create `app/Actions/Zoho/SubmitLeadToZohoAction.php` (attempt lifecycle, DB::transaction)
- [x] `php artisan make:job SyncSubmissionToZohoJob`
- [x] Configure job: retries=3, backoff=[30,60,120], ShouldBeUniqueUntilProcessing, idempotency check
- [x] Wire `SubmissionRecorder` to dispatch `SyncSubmissionToZohoJob`
- [x] Create `database/migrations/create_zoho_credentials_table.php` + `ZohoCredential` model (key-value DB store)
- [x] Add Zoho config block to `config/services.php`; add `ZOHO_*` env vars to `.env.example`
- [x] `php artisan make:test --pest ZohoSyncJobTest` → 4 tests
- [x] `php artisan make:test --pest SubmitLeadToZohoActionTest` → 4 tests
- [x] `php artisan test --compact` — 33/33 pass
- [x] `vendor/bin/phpstan analyse --no-progress` — zero errors

---

## Phase 4: Filament Admin ✅

- [x] `php artisan make:filament-resource Page --no-interaction`
- [x] Customize `PageResource` form: page_key (disabled on edit + unique), page_type Select, slug (unique), is_active Toggle, SEO section, CTA section
- [x] Customize `PagesTable`: columns (page_key, page_type badge, slug, is_active icon, updated_at), EditAction + DeleteBulkAction
- [x] `php artisan make:filament-resource Submission --view --no-interaction`
- [x] Make `SubmissionResource` read-only: remove create/edit from getPages(), remove CreateAction from ListSubmissions, remove EditAction from ViewSubmission
- [x] `SubmissionsTable`: ViewAction only, columns (id, pii_json.email display-only, product_key, crm_status badge, submitted_at)
- [x] `SubmissionInfolist`: PII fields (name, email, phone, company) + metadata (product_key, crm_status, submitted_at, journey_id)
- [x] `php artisan make:filament-resource CrmSyncAttempt --view --no-interaction`
- [x] Make `CrmSyncAttemptResource` read-only: remove create/edit from getPages(), remove CreateAction from ListCrmSyncAttempts, remove EditAction from ViewCrmSyncAttempt
- [x] `CrmSyncAttemptsTable`: ViewAction + Retry action (visible on Failed only, requiresConfirmation, dispatches SyncSubmissionToZohoJob), columns
- [x] `CrmSyncAttemptInfolist`: attempt fields + error details section
- [x] `php artisan make:filament-widget SubmissionStatsWidget --stats-overview --no-interaction`
- [x] `SubmissionStatsWidget`: 4 stats (Total, Pending, Synced, Failed), polling disabled
- [x] Add `SubmissionStatsWidget` to `AdminPanelProvider` widgets array
- [x] Create `config/admin.php` (ADMIN_EMAIL, ADMIN_PASSWORD, ADMIN_NAME)
- [x] `php artisan make:seeder AdminUserSeeder` — `User::updateOrCreate` via `Config::string()`
- [x] Update `DatabaseSeeder` to call `AdminUserSeeder`
- [x] Add `ADMIN_*` vars to `.env.example`
- [x] `FilamentPageResourceTest`: 8 tests (render list/create/edit, create/update/validation/unique)
- [x] `php artisan test --compact` — 42/42 pass
- [x] `vendor/bin/phpstan analyse --no-progress` — zero errors
- [x] `vendor/bin/pint --dirty` — clean

---

## Phase 5: Nuxt Foundation

- [ ] Configure `nuxt.config.ts`: prerender, modules, runtimeConfig
- [ ] Create `apps/frontend/layouts/default.vue`
- [ ] Create `apps/frontend/layouts/minimal.vue`
- [ ] Create `apps/frontend/components/layout/AppHeader.vue`
- [ ] Create `apps/frontend/components/layout/AppFooter.vue`
- [ ] Create `apps/frontend/components/layout/SiteNav.vue`
- [ ] Create `apps/frontend/composables/useSeo.ts`
- [ ] Scaffold all 7 pages with basic structure and SEO meta
- [ ] Add placeholder OG images (`assets/images/og/*.jpg`)
- [ ] Add `public/robots.txt`
- [ ] Verify `nuxt generate` — all routes prerendered without errors

---

## Phase 6: Nuxt Components & Form Layer

- [ ] Create `sections/HeroSection.vue`
- [ ] Create `sections/FeatureGrid.vue`
- [ ] Create `sections/CtaBlock.vue`
- [ ] Create `sections/FaqSection.vue`
- [ ] Create `sections/TrustBadges.vue`
- [ ] Create `sections/TestimonialBlock.vue`
- [ ] Create `form/FormField.vue` (text, email, phone, select, radio, checkbox)
- [ ] Create `form/FormStep.vue`
- [ ] Create `form/MultiStepForm.vue` (step orchestrator + progress indicator)
- [ ] Create `form/QualificationForm.vue` (calls useQualify)
- [ ] Create `form/ProductForm.vue` (calls useSubmit)
- [ ] Create `stores/journey.ts` (Pinia store)
- [ ] Create `composables/useJourney.ts`
- [ ] Create `composables/usePrefill.ts`
- [ ] Create `composables/useQualify.ts`
- [ ] Create `composables/useSubmit.ts`
- [ ] Create `composables/useTracking.ts`
- [ ] TypeScript types: `types/journey.ts`, `types/prefill.ts`, `types/submission.ts`, `types/seo.ts`

---

## Phase 7: Nuxt-Laravel Integration

- [ ] Set `NUXT_PUBLIC_API_BASE_URL` in `apps/frontend/.env` (local: `https://infxslandingpages.test`)
- [ ] Verify CORS: Laravel allows `localhost:3000` and target domain
- [ ] Wire `QualificationForm` → `useQualify` → POST `/api/funnel/qualify` → redirect `?t=TOKEN`
- [ ] Wire product page: read `?t` → `useJourney` store + cookie
- [ ] Wire `usePrefill` on product page: hydrate `ProductForm` prefill fields
- [ ] Wire `ProductForm` → `useSubmit` → POST `/api/funnel/submit` → redirect `/thanks`
- [ ] Test Flow A: landing → qualify → route → product page (prefilled) → submit → thanks
- [ ] Test Flow B: direct product page → submit → thanks
- [ ] Test token expiry: expired token → graceful degradation (blank form, no error crash)
- [ ] Verify all API error states handled gracefully

---

## Phase 8: Content & SEO Polish

- [ ] Replace placeholder content with real Zoho copy (user to supply)
- [ ] Final OG images per page (user to supply)
- [ ] JSON-LD structured data on product pages (Organization + WebPage)
- [ ] Verify all meta tags in page source
- [ ] Test OG: Facebook Sharing Debugger + Twitter Card Validator
- [ ] Add PageSense/analytics script via `useHead` in `app.vue`
- [ ] Verify `sitemap.xml` generated and accurate

---

## Phase 9: Hardening & Production Prep

- [ ] Rate limiting: throttle middleware on `POST /api/funnel/*` (e.g. 5/min per IP)
- [ ] Honeypot field in forms (anti-bot)
- [ ] Queue: tune retries/backoff, configure dead-letter channel notification
- [ ] Nuxt build-time config fetch: `nuxt generate` calls `/api/config/pages/*`
- [ ] Comprehensive Pest feature tests for all API flows
- [ ] GitHub Actions CI: PHP (PHPStan + Pint + Pest) + Nuxt (lint + types:check + build)
- [ ] Production env checklist: APP_ENV=production, APP_DEBUG=false, CORS origins locked, SSL, encrypted PII key
- [ ] Document final API contract in README or `docs/`
