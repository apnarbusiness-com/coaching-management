# Multi-Tenancy SaaS Migration Plan

> **Project:** Coaching Management System
> **Branch:** `multi-tenancy-saas`
> **Date:** 2026-06-05
> **Status:** 🟢 Active

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Tech Decisions & Rationale](#2-tech-decisions--rationale)
3. [Phase 0: Foundation & Core Structure](#3-phase-0-foundation--core-structure)
4. [Phase 1: Multi-Tenant Auth & Onboarding](#4-phase-1-multi-tenant-auth--onboarding)
5. [Phase 2: Feature Gating & Usage Limits](#5-phase-2-feature-gating--usage-limits)
6. [Phase 3: Tenant Admin & Settings](#6-phase-3-tenant-admin--settings)
7. [Phase 4: Super Admin Dashboard & Management](#7-phase-4-super-admin-dashboard--management)
8. [Phase 5: Hardening, Audit & Deployment](#8-phase-5-hardening-audit--deployment)
9. [Future / Deferred (Payment & Billing)](#9-future--deferred-payment--billing)
10. [Risks & Mitigation](#10-risks--mitigation)
11. [Glossary](#11-glossary)

---

## 1. Architecture Overview

### Strategy: Single Database with Discriminator Column (`tenant_id`)

**Why NOT separate databases:**
- Hosting cost for 100+ separate DBs is prohibitive
- Migration complexity is significantly higher
- Cross-tenant reporting (super admin) becomes a nightmare
- Backup/restore per DB adds operational overhead

**Why NOT schema-based:**
- MySQL-only approach (no PostgreSQL-specific features)
- Schema-per-tenant doesn't play well with Laravel migrations

### Tenant Resolution: Hybrid (Subdomain + Session)

**Priority order:**
1. **Subdomain** — `{tenant}.yourapp.com` routes to tenant (production)
2. **Session** — logged-in user's session stores their tenant context
3. **Query param** — `?__tenant={slug}` for dev testing

This means users can access via main domain too — as long as they're logged in, their session remembers which tenant they belong to.

```
https://{tenant}.yourapp.com    → Auto-resolve tenant from subdomain
https://yourapp.com/login       → Login → session stores tenant context
https://yourapp.com/dashboard   → Uses session tenant
?__tenant=coaching-slug         → Query param override for testing
https://superadmin.yourapp.com  → Super Admin Panel
```

### Data Isolation Model

```
┌─────────────────────────────────────────────────────────┐
│                    master Database                       │
│                                                         │
│  ┌─────────────┐   ┌──────────────────────────────────┐ │
│  │  system_*    │   │  tenant-scoped tables            │ │
│  │  tables      │   │  (all have tenant_id)            │ │
│  │              │   │                                  │ │
│  │  tenants     │   │  users, batches, students        │ │
│  │  plans       │   │  teachers, earnings, expenses    │ │
│  │  features    │   │  monthly_dues, attendances       │ │
│  │  permissions │   │  roles, sections, shifts         │ │
│  │  roles       │   │  classes, subjects, etc.         │ │
│  └─────────────┘   └──────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

- **System tables** (no `tenant_id`): `tenants`, `plans`, `features`, `plan_features`
- **Tenant-scoped tables** (all have `tenant_id`): Every existing business table

### User Model Changes

| Current | Future |
|---------|--------|
| Single `users` table | `users` table with `tenant_id` (nullable for super admins) |
| Roles are global | Roles PER TENANT + system roles for super admins |
| One level of admins | Two levels: **Super Admin** + **Tenant Admin** |

### Super Admin vs Tenant Admin

| Capability | Super Admin | Tenant Admin |
|---|---|---|
| View all tenants | ✅ | ❌ |
| Create/Manage plans | ✅ | ❌ |
| View any tenant's data | ✅ | ❌ |
| Manage tenant admins | ✅ | ❌ |
| Manage their coaching | ❌ | ✅ |
| View their users/students | ❌ | ✅ |
| Configure their settings | ❌ | ✅ |

---

## 2. Tech Decisions & Rationale

| Decision | Choice | Why |
|----------|--------|-----|
| **Tenant package** | **Custom (no package)** | Full control, matches existing architecture |
| **DB strategy** | Single DB + `tenant_id` | Cost-effective, simple migration |
| **Tenant resolution** | Hybrid (subdomain + session) | Subdomain for production, session fallback for dev & easy access |
| **Feature flags** | Database-driven | Dynamic limits per plan |
| **Caching** | Tenant-prefixed keys | Isolated cache per tenant |
| **File storage** | Local (tenant-prefixed) | Isolated per tenant |

**No new composer packages needed for now.** We use existing infrastructure.

---

## 3. Phase 0: Foundation & Core Structure

> **Goal:** Set up tenant infrastructure: models, migrations, middleware, global scoping
> **Risk level:** 🟡 Medium

### 3.1 Create Tenant & Plan Models

- [x] **3.1.1** Create `tenants` table migration
- [x] **3.1.2** Create `plans` table migration
- [x] **3.1.3** Create `plan_features` table migration
- [x] **3.1.4** Create `Tenant` model
- [x] **3.1.5** Create `Plan` model
- [x] **3.1.6** Create `PlanFeature` model

### 3.2 Add tenant_id to Existing Tables

- [x] **3.2.1** Create migration: `add_tenant_id_to_existing_tables`
  - Add `tenant_id` (nullable, FK) to ALL these tables:
    - `users`, `roles`, `permissions`, `permission_role`, `role_user`
    - `sections`, `shifts`, `academic_classes`
    - `student_basic_infos`, `student_details_informations`
    - `batches`, `batch_student_basic_info`, `batch_subject`, `batch_teacher`
    - `teachers`, `subjects`, `subject_teacher`
    - `earnings`, `earning_categories`
    - `expenses`, `expense_categories`
    - `teachers_payments`, `teacher_payment_transactions`
    - `student_monthly_dues`, `student_other_dues`
    - `batch_attendances`
    - `class_rooms`, `academic_backgrounds`
    - `student_flags`, `student_flag_assignments`
    - `student_admission_applications`
    - `cash_books`, `cash_book_transactions`
    - `dashboard_widget_configs`
    - `settings`, `audit_logs`
  - Add indexes on `tenant_id` for all tables
  - Add foreign key constraints

### 3.3 Global Scoping Implementation

- [x] **3.3.1** Create `App\Scopes\TenantScope` global scope
- [x] **3.3.2** Create `App\Traits\BelongsToTenant` trait
- [x] **3.3.3** Add `BelongsToTenant` trait to ALL existing models (except system models)
- [ ] **3.3.4** (Future) Handle `withoutTenant()` for super admin queries

### 3.4 Tenant Resolver Middleware

- [x] **3.4.1** Create `App\Http\Middleware\IdentifyTenant` middleware
  ```php
  // Logic:
  // 1. Extract subdomain from Host header
  // 2. If subdomain exists + is not 'www' or 'superadmin':
  //    - Query tenants table by slug = subdomain
  //    - If not found → 404
  //    - If suspended → show suspended page
  //    - Set app(tenant) = found tenant
  //    - Store tenant_slug in session
  //    - Set tenant timezone & locale
  // 3. If 'superadmin' subdomain → set super_admin context, clear session tenant
  // 4. If NO subdomain (main domain access):
  //    a. Check ?__tenant={slug} query param → use that tenant
  //    b. Check session('tenant_slug') → use stored tenant
  //    c. Check if authenticated user belongs to a tenant → use that
  // 5. If still no tenant → no tenant scope (super admin / dev view)
  ```
## 5. Phase 2: Feature Gating & Usage Limits

> **Goal:** Enforce plan limits (max students, teachers, batches, etc.) and show usage on dashboard
> **Risk level:** 🟢 Low

### 5.1 FeatureLimitService

- [x] **5.1.1** Create `App\Services\FeatureLimitService`
  - `canCreate(resource)` — checks if count < max allowed
  - `currentCount(resource)` — cached count per tenant
  - `maxAllowed(resource)` — from tenant's plan features
  - `usagePercentage(resource)` — for progress bars
  - `getAllUsage()` — returns all resources with current/max/percentage
  - `clearCache(resource)` / `clearAllCache()` — cache invalidation

### 5.2 Add Feature Checks to Controllers

- [x] **5.2.1** `StudentBasicInfoController@store` — check `students` limit
- [x] **5.2.2** `TeacherController@store` — check `teachers` limit
- [x] **5.2.3** `BatchController@store` — check `batches` limit
- [x] **5.2.4** `AcademicClassController@store` — check `classes` limit
- [x] **5.2.5** `SectionController@store` — check `sections` limit
- [x] **5.2.6** `SubjectsController@store` — check `subjects` limit

### 5.3 Usage Dashboard Widget

- [x] **5.3.1** Register `usage_summary` widget in `config/dashboard_widgets.php`
- [x] **5.3.2** Add `getUsageSummaryData()` to `DashboardWidgetService`
- [x] **5.3.3** Add HTML card + AJAX script in `home.blade.php`

### 5.4 Cache Invalidation Hooks

- [x] **5.4.1** Add cache invalidation calls in model boot/events for created/deleted
- [ ] **5.4.2** Verify cache clears after store/delete operations (manual verify)

## 6. Phase 3: Tenant Admin & Settings

> **Goal:** Allow tenant admins to manage their tenant profile and configure basic branding/settings
> **Risk level:** 🟢 Low

### 6.1 Tenant Profile Management

- [x] **6.1.1** Create `TenantProfileController` with `edit()` + `update()` methods
- [x] **6.1.2** Create `UpdateTenantProfileRequest` with validation rules
- [x] **6.1.3** Create `resources/views/admin/tenantProfile/edit.blade.php` view
  - Institution Details (name, slug read-only, email, phone, address)
  - Regional Settings (timezone selector, locale selector)
  - Plan Information card (plan name, status, registration date)

### 6.2 Tenant Settings (Dynamic Branding)

- [x] **6.2.1** Create `TenantSettingsController` with `edit()` + `update()` methods
- [x] **6.2.2** Create `resources/views/admin/tenantSettings/edit.blade.php` view
- [x] **6.2.3** Add `setting()` helper usage in layouts:
  - `head.blade.php` — `<title>` uses `setting('site_title')` with fallback to `trans('panel.site_title')`
  - `header.blade.php` — brand link uses `setting('site_title')` with fallback
  - `admin.blade.php` — footer copyright uses `setting('site_title')` with fallback

### 6.3 Permissions & Menu

- [x] **6.3.1** Add `tenant_profile_edit` (ID 118) and `tenant_settings_edit` (ID 119) permissions to `PermissionsTableSeeder`
- [x] **6.3.2** Add "Settings" dropdown to sidebar menu with Profile and General Settings links

### 6.4 Routes

- [x] **6.4.1** `GET/PUT admin/tenant-profile` — `TenantProfileController@edit` / `@update`
- [x] **6.4.2** `GET/PUT admin/tenant-settings` — `TenantSettingsController@edit` / `@update`

```
## 7. Phase 4: Super Admin Dashboard & Management

> **Goal:** Provide super admin with oversight of all tenants and plans
> **Risk level:** 🟢 Low

### 7.1 Super Admin Dashboard

- [x] **7.1.1** Create `SuperAdmin\DashboardController@index` — overview stats
- [x] **7.1.2** Create `admin.superAdmin.dashboard.blade.php` view
  - Stats cards: total tenants, active, suspended, users, plans
  - Plan distribution chart
  - Recent tenants list

### 7.2 Tenant Management

- [x] **7.2.1** Create `SuperAdmin\TenantManagementController` (index/show/edit/update/destroy)
- [x] **7.2.2** Create views:
  - `index.blade.php` — paginated table with name, plan, users, status, actions
  - `show.blade.php` — contact info + system info sections
  - `edit.blade.php` — form for name, email, phone, address, timezone, locale, status, plan

### 7.3 Plan Management

- [x] **7.3.1** Create `SuperAdmin\PlanManagementController` (full CRUD)
- [x] **7.3.2** Create views:
  - `index.blade.php` — card grid with tenant count, edit/delete actions
  - `create.blade.php` — plan details + inline feature limit editor (9 features)
  - `edit.blade.php` — same form pre-populated

### 7.4 Routes & Middleware

- [x] **7.4.1** All routes under `admin/super-admin/` prefix with `superadmin` middleware
- [x] **7.4.2** Route names: `admin.super-admin.dashboard`, `.tenants.*`, `.plans.*`

### 7.5 Menu

- [x] **7.5.1** "Super Admin" dropdown in sidebar (visible only to `isSuperAdmin()`)
  - Dashboard, Tenants, Plans links

### 7.6 Authorization

- [x] **7.6.1** Routes protected by existing `SuperAdmin` middleware (`isSuperAdmin()` check)
- [x] **7.6.2** Menu gated by `auth()->user()?->isSuperAdmin()`

## 8. Phase 5: Hardening, Audit & Deployment

> **Goal:** Secure tenant data isolation, configure deployment environment, document operations
> **Risk level:** 🟢 Low

### 8.1 Subdomain Resolution Configuration

- [x] **8.1.1** Add `'domain' => env('APP_DOMAIN', 'localhost')` to `config/app.php`
- [x] **8.1.2** Add `APP_DOMAIN` and documentation to `.env.example`

### 8.2 Media Library Tenant Isolation

- [x] **8.2.1** Publish `config/media-library.php` with custom path generator
- [x] **8.2.2** Create `App\Support\MediaLibrary\TenantPathGenerator`
  - Stores files under `tenant/{slug}/{media_id}/` for tenant uploads
  - Uses `system/{media_id}/` for system-level uploads (no tenant context)
- [x] **8.2.3** Add `MEDIA_DISK` to `.env.example`

### 8.3 Super Admin Data Access

- [x] **8.3.1** Add `static::withoutTenant()` helper to `BelongsToTenant` trait
  - Removes `TenantScope` global scope for cross-tenant queries
  - Usage: `StudentBasicInfo::withoutTenant()->where('tenant_id', $id)->get()`
- [x] **8.3.2** Update tenant `show` view to display student/teacher/batch counts using `withoutTenant()`

### 8.4 Deployment Documentation

- [x] **8.4.1** Create `only-for-dev/DEPLOYMENT.md` with:
  - Environment configuration guide
  - Nginx/Apache wildcard subdomain setup
  - Migration & seeding commands
  - Queue worker setup
  - Security checklist

```
Phase 0 ─── Foundation (models, migrations, scoping, middleware) ✓
     │
Phase 1 ─── Auth & Onboarding (registration, login, user mgmt) ✓
     │
Phase 2 ─── Feature Gating (limits, usage dashboard) ✓
     │
Phase 3 ─── Tenant Settings (branding, preferences) ✓
     │
Phase 4 ─── Super Admin Dashboard (management) ✓
     │
Phase 5 ─── Hardening & Deployment (audit, security, perf) ✓
     │
         🏁 All Phases Complete
```

> **Status:** ✅ All 6 phases complete.
