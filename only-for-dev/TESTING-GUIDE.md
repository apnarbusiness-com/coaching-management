# Testing Guide — Multi-Tenancy SaaS

> Comprehensive test scenarios covering all 6 phases.

---

## Prerequisites

```bash
php artisan migrate
php artisan db:seed --class=PermissionsTableSeeder
php artisan db:seed --class=PlanSeeder
php artisan db:seed --class=SuperAdminSeeder
php artisan tenancy:migrate-existing-data
```

---

## 📋 Phase 0 & 1: Foundation, Auth & Onboarding

### Test 1: Tenant Registration

**URL:** `http://localhost/dev-coaching-management/public/tenant/register`

1. Fill the form:
   - Coaching Center Name: `Test Coaching`
   - Subdomain: `test-coaching` (watch AJAX check turn green)
   - Your Name: `Admin User`
   - Email: `admin@test.com`
   - Phone: `017xxxxxxx`
   - Password: `123456` / Confirm: `123456`
   - Select a Plan (e.g., Basic)
2. Click "Create Coaching Center"
3. ✅ Auto-login + redirect to `/admin` dashboard
4. ✅ "Welcome! Your coaching center is ready." message
5. ✅ Dashboard shows empty data (new tenant)

### Test 2: Data Isolation

1. Logout → Login with original **Apnar-Coaching** admin
2. ✅ Dashboard shows 387 students, 15 batches
3. Logout → Login with `admin@test.com` / `123456`
4. ✅ Dashboard shows 0 students, 0 batches → **Isolation confirmed**

### Test 3: Session-Based Tenant Access

1. Logout completely
2. Login on main domain (no subdomain) with any tenant admin
3. ✅ Only that tenant's data visible
4. ✅ Session stores `tenant_slug` correctly

### Test 4: Super Admin Access

1. Login: `superadmin@admin.com` / password set during seeding
2. ✅ Sees ALL data (no tenant scope applied)

### Test 5: Tenant Switching (Super Admin)

```bash
curl -X POST http://localhost/dev-coaching-management/public/admin/switch-tenant/apnar-coaching \
  -b "laravel_session=YOUR_SESSION" -c cookies.txt
```

1. ✅ Session switches to Apnar-Coaching
2. Refresh dashboard → only Apnar-Coaching data

### Test 6: Query Param Override

- `http://localhost/admin?__tenant=apnar-coaching` → Apnar-Coaching data
- `http://localhost/admin?__tenant=test-coaching` → Test Coaching data
- ✅ Remove param → back to session tenant

### Test 7: Subdomain (Local via hosts file)

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 apnar-coaching.test
127.0.0.1 test-coaching.test
```
Set `.env`: `APP_DOMAIN=test`

- `http://apnar-coaching.test/` → Apnar-Coaching
- `http://test-coaching.test/` → Test Coaching

---

## 📋 Phase 2: Feature Gating & Usage Limits

### Test 8: Plan Limits Enforced

**Scenario:** Basic plan allows max 50 students, 5 teachers, 5 batches.

1. Login as Test Coaching admin (Basic plan)
2. Go to Students → try creating the 51st student
3. ✅ `Student limit reached for your plan. Please upgrade.` error
4. Go to Teachers → try creating the 6th teacher
5. ✅ `Teacher limit reached for your plan. Please upgrade.` error
6. Go to Batches → try creating the 6th batch
7. ✅ `Batch limit reached for your plan. Please upgrade.` error
8. Repeat for Classes (10 max), Sections (20 max), Subjects (20 max)

### Test 9: Unlimited Plan (Enterprise)

1. Assign Enterprise plan to a tenant via Super Admin panel
2. Login as that tenant's admin
3. ✅ Can create unlimited students/teachers/batches (no limit errors)

### Test 10: Usage Dashboard Widget

1. Login as any tenant admin
2. Go to Dashboard
3. ✅ "Plan Usage Summary" widget visible
4. ✅ Shows progress bars for Students, Teachers, Batches, Classes, Sections, Subjects
5. ✅ Each shows current / max count + percentage bar
6. ✅ Color-coded: green (<70%), amber (70-90%), red (>90%)

### Test 11: Cache Invalidation

1. Create a new student → usage widget should update
2. ✅ Widget count increases on next load
3. Delete a student → widget count decreases
4. ✅ Cache clears automatically via model events

---

## 📋 Phase 3: Tenant Admin & Settings

### Test 12: Tenant Profile

1. Login as any tenant admin
2. Go to Settings → Profile
3. ✅ Form shows: Institution Name, Slug (read-only), Email, Phone, Address
4. ✅ Regional Settings: Timezone dropdown (all PHP timezones), Locale
5. ✅ Plan Information card shows current plan, status, registration date
6. Edit Name, set Timezone to `Asia/Dhaka`, update
7. ✅ "Tenant profile updated successfully." message
8. ✅ Changes persist on reload

### Test 13: General Settings (Site Title)

1. Go to Settings → General Settings
2. ✅ "Site Title" field visible
3. Set Site Title to `My Academy`
4. ✅ "Settings updated successfully."
5. Refresh page → browser tab title shows `My Academy`
6. ✅ Header branding shows `My Academy`
7. ✅ Footer shows `My Academy`

### Test 14: Dynamic Branding Persistence

1. Logout → Login again
2. ✅ Site title still shows `My Academy` (persisted in settings table)

---

## 📋 Phase 4: Super Admin Dashboard & Management

### Test 15: Super Admin Dashboard

1. Login as `superadmin@admin.com`
2. ✅ Sidebar shows "Super Admin" dropdown
3. Click Super Admin → Dashboard
4. ✅ Stats: Total Tenants, Active, Suspended, Total Users, Plans
5. ✅ Plan Distribution list
6. ✅ Recent Tenants list

### Test 16: Tenant Management (Super Admin)

1. Super Admin → Tenants
2. ✅ Paginated table: Name, Plan, Users count, Status, Registered date
3. Click "View" on a tenant
4. ✅ Contact info, System info, Quick stats (Students/Teachers/Batches counts)
5. Click "Edit" → change plan, status, name
6. ✅ Update succeeds, changes reflected in table

### Test 17: Suspend a Tenant

1. Super Admin → Tenants → Edit
2. Set Status to "Suspended"
3. ✅ Tenant shows "suspended" in list
4. ✅ That tenant's users can no longer access (IdentifyTenant blocks suspended)

### Test 18: Plan Management (Super Admin)

1. Super Admin → Plans
2. ✅ Card grid: Basic, Premium, Enterprise with tenant counts
3. Click "New Plan" → Create a plan with custom limits
4. ✅ Plan appears in grid
5. Edit existing plan → change feature limits
6. ✅ Updates apply immediately
7. Enterprise has `unlimited (-1)` for all features → ✅ verified

### Test 19: Non-Super Admin Cannot Access

1. Login as a regular tenant admin
2. Try visiting `/admin/super-admin/tenants`
3. ✅ `403 Forbidden` — SuperAdmin middleware blocks access
4. ✅ "Super Admin" dropdown not visible in sidebar

---

## 📋 Phase 5: Hardening & Deployment

### Test 20: Tenant File Isolation

1. Login as Tenant A → upload a student profile image
2. Note the file URL
3. Login as Tenant B → try accessing Tenant A's file URL directly
4. ✅ Files stored in `tenant/{slug}/` paths (not shared)
5. ❌ Note: Media library doesn't enforce access control on URLs — rely on path isolation

### Test 21: withoutTenant() Scope Bypass

1. Login as super admin
2. Any query using `Model::withoutTenant()->where('tenant_id', X)->get()` works
3. ✅ Verified in Tenant detail view (shows student/teacher/batch counts)

### Test 22: APP_DOMAIN Config

1. Check `config/app.php` → `'domain' => env('APP_DOMAIN', 'localhost')`
2. ✅ Key exists
3. Check `.env.example` → `APP_DOMAIN=localhost` with documentation
4. ✅ Documented

### Test 23: Deployment Guide

1. ✅ `SETUP.md` at root — covers local + production
2. ✅ `README.md` — professional project overview
3. ✅ `only-for-dev/DEPLOYMENT.md` — production checklist

---

## 📋 Full Regression: End-to-End Flow

### Test 24: Complete New Tenant Journey

1. Visit `/tenant/register`
2. Register as `New Academy` with subdomain `new-academy`, Basic plan
3. ✅ Auto-login, dashboard empty
4. Go to Settings → Profile → set timezone, update
5. Go to Settings → General → set Site Title to `New Academy`
6. ✅ Branding updates immediately
7. Create a Section, Shift, Class, Subject, Room
8. ✅ Feature limits not yet hit (Basic allows 50 students)
9. Create a Batch, enroll a Student, add a Teacher
10. ✅ All created successfully
11. Dashboard shows usage widget with 1/50 students, 1/5 teachers, etc.

### Test 25: Super Admin Oversight

1. Login as super admin
2. Dashboard shows `New Academy` in recent tenants
3. Tenants list shows all 3 tenants (Apnar-Coaching, Test Coaching, New Academy)
4. Plans show correct distribution
5. Edit `New Academy` → change to Premium plan
6. ✅ Logged in as New Academy admin → now has 200 student limit

---

## 📋 Common Issues

| Issue | Fix |
|-------|-----|
| "Tenant not found" | Check `tenants` table has the slug |
| No data showing | Check user has `tenant_id` set |
| Super admin sees no data | Super admins have `tenant_id = null` — they see ALL data |
| 403 Forbidden | Missing permission — run `PermissionsTableSeeder` |
| Blank page after login | `php artisan optimize:clear` |
| Feature limits not working | Verify `PlanSeeder` ran and tenant has `plan_id` set |
