<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UARUser;
use App\Models\UAR;
use Illuminate\Support\Facades\Auth;

class UARUserController extends Controller
{
    public function show($uar_id)
    {
        $uar = UAR::with('users')->findOrFail($uar_id);
        return view('uar.review', compact('uar'));
    }

    public function approve($id)
    {
        $uarUser = UARUser::findOrFail($id);
        $uar = UAR::findOrFail($uarUser->uar_id);
        $userId = auth()->id();

        if ($uar->primary_reviewer_id === $userId) {
            $uarUser->update([
                'primary_reviewer_id' => $userId,
                'primary_reviewed_at' => now(),
                'primary_review_status' => 'Approved',
            ]);
        } elseif ($uar->secondary_reviewer_id === $userId) {
            $uarUser->update([
                'secondary_reviewer_id' => $userId,
                'secondary_reviewed_at' => now(),
                'secondary_review_status' => 'Approved',
            ]);
        } else {
            return redirect()->back()->with('error', 'You are not authorized to approve this user.');
        }

        $this->checkAndUpdateUARStatus($uar);

        if ($this->isReviewerDone($uar, $userId)) {
            return redirect()->route('dashboard')->with('success', 'All users reviewed. Redirected to dashboard.');
        }

        return redirect()->back()->with('success', 'User access approved successfully!');
    }

    public function reject($id)
    {
        $uarUser = UARUser::findOrFail($id);
        $uar = UAR::findOrFail($uarUser->uar_id);
        $userId = auth()->id();

        if ($uar->primary_reviewer_id === $userId) {
            $uarUser->update([
                'primary_reviewer_id' => $userId,
                'primary_reviewed_at' => now(),
                'primary_review_status' => 'Rejected',
            ]);
        } elseif ($uar->secondary_reviewer_id === $userId) {
            $uarUser->update([
                'secondary_reviewer_id' => $userId,
                'secondary_reviewed_at' => now(),
                'secondary_review_status' => 'Rejected',
            ]);
        } else {
            return redirect()->back()->with('error', 'You are not authorized to reject this user.');
        }

        $this->checkAndUpdateUARStatus($uar);

        if ($this->isReviewerDone($uar, $userId)) {
            return redirect()->route('dashboard')->with('success', 'All users reviewed. Redirected to dashboard.');
        }

        return redirect()->back()->with('success', 'User access rejected successfully!');
    }

    public function approveAll($uar_id)
    {
        $uar = UAR::findOrFail($uar_id);
        $userId = auth()->id();

        if ($uar->primary_reviewer_id === $userId) {
            UARUser::where('uar_id', $uar_id)->update([
                'primary_reviewer_id' => $userId,
                'primary_reviewed_at' => now(),
                'primary_review_status' => 'Approved',
            ]);
        } elseif ($uar->secondary_reviewer_id === $userId) {
            UARUser::where('uar_id', $uar_id)->update([
                'secondary_reviewer_id' => $userId,
                'secondary_reviewed_at' => now(),
                'secondary_review_status' => 'Approved',
            ]);
        } else {
            return redirect()->back()->with('error', 'You are not authorized to approve all users.');
        }

        $this->checkAndUpdateUARStatus($uar);

        return redirect()->route('dashboard')->with('success', 'All users approved successfully and you are redirected to dashboard!');
    }

    private function checkAndUpdateUARStatus(UAR $uar)
    {
        $users = $uar->users;

        $allPrimaryReviewed = $users->every(fn($user) => in_array($user->primary_review_status, ['Approved', 'Rejected']));
        $allSecondaryReviewed = $users->every(fn($user) => in_array($user->secondary_review_status, ['Approved', 'Rejected']));

        if ($allPrimaryReviewed && $allSecondaryReviewed) {
            $anyRejected = $users->contains(function ($user) {
                return $user->primary_review_status === 'Rejected' || $user->secondary_review_status === 'Rejected';
            });

            $uar->status = $anyRejected ? 'awaiting_removal' : 'complete';
            $uar->save();
        }
    }

    private function isReviewerDone(UAR $uar, $userId)
    {
        $users = $uar->users;

        if ($uar->primary_reviewer_id === $userId) {
            return $users->every(fn($user) => in_array($user->primary_review_status, ['Approved', 'Rejected']));
        }

        if ($uar->secondary_reviewer_id === $userId) {
            return $users->every(fn($user) => in_array($user->secondary_review_status, ['Approved', 'Rejected']));
        }

        return false;
    }
}
