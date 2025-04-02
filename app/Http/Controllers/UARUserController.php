<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UARUser;
use App\Models\UAR;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

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

        if ($uar->primary_reviewer_id === auth()->id()) {
            $uarUser->update([
                
                'primary_reviewer_id' => auth()->id(),
                'primary_reviewed_at' => now(),
                'primary_review_status' => 'Approved',
            ]);
        } elseif ($uar->secondary_reviewer_id === auth()->id()) {
            $uarUser->update([
                
                'secondary_reviewer_id' => auth()->id(),
                'secondary_reviewed_at' => now(),
                'secondary_review_status' => 'Approved',
            ]);
        } else {
            return redirect()->back()->with('error', 'You are not authorized to approve this user.');
        }

        return redirect()->back()->with('success', 'User access approved successfully!');
    }

    public function reject($id)
    {
        $uarUser = UARUser::findOrFail($id);
        $uar = UAR::findOrFail($uarUser->uar_id);

        if ($uar->primary_reviewer_id === auth()->id()) {
            $uarUser->update([
                
                'primary_reviewer_id' => auth()->id(),
                'primary_reviewed_at' => now(),
                'primary_review_status' => 'Rejected',
            ]);
        } elseif ($uar->secondary_reviewer_id === auth()->id()) {
            $uarUser->update([
                
                'secondary_reviewer_id' => auth()->id(),
                'secondary_reviewed_at' => now(),
                'secondary_review_status' => 'Rejected',
            ]);
        } else {
            return redirect()->back()->with('error', 'You are not authorized to reject this user.');
        }

        return redirect()->back()->with('success', 'User access rejected successfully!');
    }

    public function approveAll($uar_id)
    {
        $uar = UAR::findOrFail($uar_id);

        if ($uar->primary_reviewer_id === auth()->id()) {
            UARUser::where('uar_id', $uar_id)
                
                ->update([
                  
                    'primary_reviewer_id' => auth()->id(),
                    'primary_reviewed_at' => now(),
                    'primary_review_status' => 'Approved',
                ]);
               

        } elseif ($uar->secondary_reviewer_id === auth()->id()) {
            UARUser::where('uar_id', $uar_id)
                
                ->update([
                    
                    'secondary_reviewer_id' => auth()->id(),
                    'secondary_reviewed_at' => now(),
                    'secondary_review_status' => 'Approved',
                ]);
        } else {
            return redirect()->back()->with('error', 'You are not authorized to approve all users.');
        }

        return redirect()->back()->with('success', 'All users approved successfully!');
    }
}
