# New Updates & Features Tracker

> Track all new features/fixes implemented on `dev-branch` after 2026-06-06.  
> When working on `multi-tenancy-saas`, refer to this file to manually port the same features.

---

## 2026-06-09 — Referral Settings Page (Access Control)

### New
- **`ReferralSettingsController`** — admin page to control wallet access
- **`resources/views/admin/referralSettings/index.blade.php`** — search, single toggle, batch toggle, batch-wise toggle, enable all
- **Migration:** `add_wallet_access_to_users` — `wallet_access` boolean (default false)
- **Permission:** `referral_settings_access` (id: 118)

### Modified
- **`User` model** — `wallet_access` added to `$fillable`
- **`ReferralService@lookupReferrer`** — now checks `wallet_access === true`; returns null if inactive
- **`ReferralService@processReferralReward`** — skips if referrer `wallet_access` is false
- **`WalletController`** — middleware checks `wallet_access`, blocks if false
- **`menu.blade.php`** — "My Wallet" only shows if `auth()->user()->wallet_access`; added "Referral Settings" menu item
- **`routes/web.php`** — 5 new referral-settings routes

### Behavior
- Deactivated users: wallet hidden, code returns "invalid" on admission form
- Admin can toggle single user, batch select, batch-wise (all students in a batch), or enable all

---

## 2026-06-06 — Referral + Wallet System

### New Database Tables (6 migrations)
- `wallets` — user wallet balances
- `wallet_transactions` — credit, debit, withdraw logs
- `withdraw_requests` — user withdrawal requests (pending/approved/rejected)
- `referral_campaigns` — campaign config (reward amount, dates)

### Modified Tables
- `users` — added `referral_code` column (unique)
- `student_admission_applications` — added `referral_code` + `referred_by_user_id` (FK)

### New Models (4)
- `Wallet`, `WalletTransaction`, `WithdrawRequest`, `ReferralCampaign`

### New Traits/Services (3)
- `Traits\HasReferralCode` — auto-generates unique referral code on user creation
- `Services\ReferralService` — looks up referrer by code, processes reward based on active campaign
- `Services\WalletService` — getOrCreateWallet, credit, debit

### Modified Models
- `User` — uses `HasReferralCode` trait, added `wallet()` and `referredApplications()` relationships
- `StudentAdmissionApplication` — added `referredBy()` relationship, added referral fields to `$fillable`

### New Controllers (4)
- `WalletController` — user wallet page + withdraw request form
- `Admin\WalletController` — admin wallet list, details, manual adjust
- `Admin\WithdrawRequestController` — approve/reject withdrawals
- `Admin\ReferralCampaignController` — full CRUD for campaigns

### Modified Controllers (2)
- `AdmissionApplicationController@store` — captures referral code, looks up referrer
- `AdmissionApplicationController@checkReferral` — AJAX endpoint for live code validation
- `Admin\AdmissionApplicationsController@approve` — triggers wallet reward after creating student

### New Views (8)
- `admin/wallet/index.blade.php` — user wallet dashboard
- `admin/wallet/withdraw.blade.php` — withdrawal form
- `admin/wallets/index.blade.php` — admin wallet list
- `admin/wallets/show.blade.php` — wallet details + transactions
- `admin/wallets/adjust.blade.php` — manual balance adjust
- `admin/withdrawRequests/index.blade.php` — manage withdrawals with approve/reject modal
- `admin/referralCampaigns/index.blade.php` — campaign list
- `admin/referralCampaigns/create.blade.php` — create campaign
- `admin/referralCampaigns/edit.blade.php` — edit campaign

### Modified Views (3)
- `admission/public.blade.php` — added "Referral Code" input field + JS for live validation
- `admin/admissionApplications/show.blade.php` — shows referral info section
- `partials/menu.blade.php` — added "My Wallet", "Wallets", "Withdraw Requests", "Referral Campaigns" menu items

### Routes Added
- `GET /admission/check-referral` — public AJAX check
- `GET/POST /admin/wallet` — user wallet
- `GET/POST /admin/wallet/withdraw` — withdrawal
- `GET /admin/wallets` — admin wallet list
- `GET/POST /admin/wallets/{id}/adjust` — adjust balance
- `GET/POST /admin/withdraw-requests/{id}/approve|reject` — manage withdrawals
- `Resource /admin/referral-campaigns` — full CRUD

### Seeders
- `ReferralAndWalletSeeder` — generates referral codes + wallets for all existing users
- `DefaultReferralCampaignSeeder` — creates "HSC-26 Farewell Referral" campaign (500 TK)

### Documentation
- `only-for-dev/REFERRAL-WALLET-GUIDE.md` — full user guide

---

## How to Use

1. Check this file to see what's new on `dev-branch`
2. Go to `multi-tenancy-saas` branch
3. Manually implement each feature (adapt to multi-tenant architecture)
4. Append new entries here as we build more
