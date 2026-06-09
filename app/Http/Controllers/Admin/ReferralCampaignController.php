<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReferralCampaignController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaigns = ReferralCampaign::latest()->paginate(20);

        return view('admin.referralCampaigns.index', compact('campaigns'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.referralCampaigns.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reward_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ReferralCampaign::create($validated);

        return redirect()->route('admin.referral-campaigns.index')
            ->with('status', 'Referral campaign created successfully.');
    }

    public function edit(ReferralCampaign $referralCampaign)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.referralCampaigns.edit', compact('referralCampaign'));
    }

    public function update(Request $request, ReferralCampaign $referralCampaign)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reward_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $referralCampaign->update($validated);

        return redirect()->route('admin.referral-campaigns.index')
            ->with('status', 'Referral campaign updated successfully.');
    }

    public function destroy(ReferralCampaign $referralCampaign)
    {
        abort_if(Gate::denies('user_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $referralCampaign->delete();

        return redirect()->route('admin.referral-campaigns.index')
            ->with('status', 'Referral campaign deleted.');
    }
}
