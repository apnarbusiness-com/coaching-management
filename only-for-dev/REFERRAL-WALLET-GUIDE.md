# Referral + Wallet System — User Guide

## Overview

Any user (student, teacher, staff) can refer new students. When a referred person submits an admission application using the referrer's code and later gets approved, the referrer earns cash in their wallet.

---

## How It Works

```
1. User gets a unique referral code (auto-generated)
2. User shares their referral code with friends/family
3. New applicant fills the public admission form at /admission
4. Applicant enters referral code in the "Referral Code" field
5. Admin reviews and approves the application
6. System credits reward to referrer's wallet
7. Referrer can request withdrawal from wallet
8. Admin approves withdrawal and pays manually
```

---

## For Users (Students/Teachers)

### Finding Your Referral Code
1. Login to your account
2. Click **My Wallet** in the sidebar menu
3. Your referral code is shown in your profile info
4. You can also find it in your user profile

### Checking Your Wallet
1. Click **My Wallet** in the sidebar
2. See your current balance, total earned, total withdrawn
3. View transaction history
4. See your withdraw request history

### Requesting Withdrawal
1. Go to **My Wallet**
2. Click **Request Withdrawal** (only visible if balance > 0)
3. Enter:
   - **Amount** (max = current balance)
   - **Payment Method** (bKash, Nagad, Rocket, Bank)
   - **Account Number** (optional)
   - **Phone Number**
4. Submit — admin will review and approve/reject

---

## For Admins

### 1. Creating a Referral Campaign
1. Go to **User Management → Referral Campaigns**
2. Click **+ New Campaign**
3. Set:
   - **Campaign Name** (e.g., "HSC-26 Farewell Referral")
   - **Reward Amount** (e.g., 500 TK)
   - **Duration** (optional start/end dates)
   - **Active** toggle
4. Save — campaign is now active

> **Note:** Only active campaigns within the date range will reward referrals. If no campaign is active, no reward is given.

### 2. Viewing Referral Info on Applications
1. Go to **Student Information → Admission Applications**
2. Click **View** on any application
3. Scroll to bottom — if a referral code was used, you'll see:
   - The referral code entered
   - Who referred the applicant
   - The referrer's current wallet balance

### 3. Managing Wallets
1. Go to **User Management → Wallets**
2. See all user wallets with balances
3. Click **View** to see transaction history
4. Click **Adjust Balance** to manually credit/debit

### 4. Processing Withdraw Requests
1. Go to **User Management → Withdraw Requests**
2. Pending requests appear first
3. Actions:
   - **Approve** — system deducts from wallet, marks as approved
   - **Reject** — enter a reason, request is rejected, balance stays
4. After approving, pay the user manually (bKash/Bank/etc.)

---

## Key Routes

| URL | Purpose | Access |
|-----|---------|--------|
| `/admission` | Public admission form | Public |
| `/admission/check-referral?code=XXX` | AJAX referral code validation | Public |
| `/admin/wallet` | User's own wallet | Auth |
| `/admin/wallet/withdraw` | Withdraw request form | Auth |
| `/admin/wallets` | Admin wallet list | Admin |
| `/admin/wallets/{id}` | Single wallet details | Admin |
| `/admin/wallets/{id}/adjust` | Manual balance adjustment | Admin |
| `/admin/withdraw-requests` | Withdraw request management | Admin |
| `/admin/referral-campaigns` | Campaign CRUD | Admin |

---

## Database Tables

| Table | Purpose |
|-------|---------|
| `wallets` | One wallet per user (balance, total_earned, total_withdrawn) |
| `wallet_transactions` | Credit/debit/withdraw log |
| `withdraw_requests` | User withdrawal requests with status |
| `referral_campaigns` | Campaign configuration (reward amount, dates) |

### Modified Tables

| Table | Changes |
|-------|---------|
| `users` | Added `referral_code` (unique) |
| `student_admission_applications` | Added `referral_code` + `referred_by_user_id` (FK to users) |

---

## Implementation Notes for Multi-Tenancy Port

When porting to `multi-tenancy-saas` branch:

1. Add `referral_code` column to `users` table
2. Add `referral_code` + `referred_by_user_id` to `student_admission_applications`
3. Create the 4 new tables (wallets, wallet_transactions, withdraw_requests, referral_campaigns)
4. Copy all models, services, traits from `dev-branch`
5. Copy controllers (WalletController, Admin/WalletController, Admin/WithdrawRequestController, Admin/ReferralCampaignController)
6. Copy views (admin/wallet/*, admin/wallets/*, admin/withdrawRequests/*, admin/referralCampaigns/*)
7. Modify `AdmissionApplicationController@store` to handle referral_code
8. Modify `AdmissionApplicationsController@approve` to call ReferralService
9. Modify admission show view to display referral info
10. Add routes
11. Add menu items
12. Run `ReferralAndWalletSeeder` to assign codes to existing users
13. Create at least one active campaign
