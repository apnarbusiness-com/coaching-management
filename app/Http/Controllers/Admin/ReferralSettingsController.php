<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\StudentBasicInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReferralSettingsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('referral_settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $users = User::with('student', 'roles')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('id', $search)
                      ->orWhereHas('student', function ($q) use ($search) {
                          $q->where('roll', 'like', "%{$search}%")
                            ->orWhere('id_no', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                      });
                });
            })
            ->when($filter === 'active', fn($q) => $q->where('wallet_access', true))
            ->when($filter === 'inactive', fn($q) => $q->where('wallet_access', false))
            ->orderBy('name')
            ->paginate(30)
            ->appends(['filter' => $filter, 'search' => $search]);

        $batches = Batch::where('status', 1)->orderBy('batch_name')->get(['id', 'batch_name']);

        return view('admin.referralSettings.index', compact('users', 'batches', 'filter', 'search'));
    }

    public function toggle(User $user)
    {
        abort_if(Gate::denies('referral_settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->wallet_access = !$user->wallet_access;
        $user->save();

        return back()->with('status', "Wallet access " . ($user->wallet_access ? 'enabled' : 'disabled') . " for {$user->name}.");
    }

    public function batchToggle(Request $request)
    {
        abort_if(Gate::denies('referral_settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'action' => ['required', 'in:enable,disable'],
        ]);

        $value = $validated['action'] === 'enable';
        User::whereIn('id', $validated['user_ids'])->update(['wallet_access' => $value]);

        return back()->with('status', count($validated['user_ids']) . " users updated.");
    }

    public function batchWiseToggle(Request $request)
    {
        abort_if(Gate::denies('referral_settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'batch_id' => ['required', 'exists:batches,id'],
            'action' => ['required', 'in:enable,disable'],
        ]);

        $studentIds = StudentBasicInfo::whereHas('batches', function ($q) use ($validated) {
            $q->where('batch_id', $validated['batch_id']);
        })->pluck('user_id')->filter();

        $value = $validated['action'] === 'enable';
        User::whereIn('id', $studentIds)->update(['wallet_access' => $value]);

        return back()->with('status', $studentIds->count() . " students from batch updated.");
    }

    public function enableAll()
    {
        abort_if(Gate::denies('referral_settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        User::query()->update(['wallet_access' => true]);

        return back()->with('status', 'Wallet access enabled for all users.');
    }

    public function disableAll()
    {
        abort_if(Gate::denies('referral_settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        User::query()->update(['wallet_access' => false]);

        return back()->with('status', 'Wallet access disabled for all users.');
    }
}
