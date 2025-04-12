<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UAR;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();

        // Fetch all users in the same organization
        $users = User::where('organization_code', $currentUser->organization_code)->get();
       
        // Attach UAR roles to each user
        $users = $users->map(function ($user) {
            $uarRoles = [];

            $uarOwner = UAR::where('app_owner_id', $user->id)->get();
            foreach ($uarOwner as $uar) {
                $uarRoles[] = "Owner - {$uar->application}";
            }

            $uarPrimary = UAR::where('primary_reviewer_id', $user->id)->get();
            foreach ($uarPrimary as $uar) {
                $uarRoles[] = "Primary - {$uar->application}";
            }

            $uarSecondary = UAR::where('secondary_reviewer_id', $user->id)->get();
            foreach ($uarSecondary as $uar) {
                $uarRoles[] = "Secondary - {$uar->application}";
            }

            $user->uarRoles = $uarRoles;
            return $user;
        });

      

        return view('users.manage', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('success', 'User admin status updated!');
    }
}
