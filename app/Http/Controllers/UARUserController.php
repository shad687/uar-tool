<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UARUser;
use App\Models\UAR;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class UARUserController extends Controller
{
    public function storeUsersFromExcel($uar_id)
    {
        $uar = UAR::findOrFail($uar_id);

        // Retrieve the latest uploaded Excel file for this UAR
        $uarFile = $uar->files()->latest()->first();

        if (!$uarFile) {
            return back()->with('error', 'No user list found for this UAR.');
        }

        $filePath = storage_path('uploads/user_lists' . $uarFile->user_list);

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex === 1)
                    continue; // Skip header row

                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = $cell->getValue();
                }

                $rows[] = [
                    'uar_id' => $uar_id,
                    'user_data' => json_encode($cells),
                    'status' => 'pending',
                ];
            }

            UARUser::insert($rows);
            return redirect()->route('dashboard')->with('success', 'Users stored successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }
    public function show($uar_id)
    {
        $uar = UAR::with('users')->findOrFail($uar_id);

        return view('uar.review', compact('uar'));
    }

    public function approve($id)
    {
        // Find the user by ID
        $uarUser = UARUser::findOrFail($id);

        // Determine if the logged-in user is the primary or secondary reviewer based on the UAR record
        $uar = UAR::findOrFail($uarUser->uar_id);

        if ($uar->primary_reviewer_id === auth()->id()) {
            // Update the primary reviewer fields
            $uarUser->update([
                'status' => 'Approved',
                'primary_reviewer_id' => auth()->id(), // Set the logged-in user's ID
                'primary_reviewed_at' => now(), // Set the current timestamp
            ]);
        } elseif ($uar->secondary_reviewer_id === auth()->id()) {
            // Update the secondary reviewer fields
            $uarUser->update([
                'status' => 'Approved',
                'secondary_reviewer_id' => auth()->id(), // Set the logged-in user's ID
                'secondary_reviewed_at' => now(), // Set the current timestamp
            ]);
        } else {
            // If the logged-in user is neither a primary nor a secondary reviewer, deny access
            return redirect()->back()->with('error', 'You are not authorized to approve this user.');
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'User access approved successfully!');
    }

    public function reject($id)
    {
        // Find the user by ID
        $uarUser = UARUser::findOrFail($id);

        // Determine if the logged-in user is the primary or secondary reviewer based on the UAR record
        $uar = UAR::findOrFail($uarUser->uar_id);

        if ($uar->primary_reviewer_id === auth()->id()) {
            // Update the primary reviewer fields
            $uarUser->update([
                'status' => 'Rejected',
                'primary_reviewer_id' => auth()->id(), // Set the logged-in user's ID
                'primary_reviewed_at' => now(), // Set the current timestamp
            ]);
        } elseif ($uar->secondary_reviewer_id === auth()->id()) {
            // Update the secondary reviewer fields
            $uarUser->update([
                'status' => 'Rejected',
                'secondary_reviewer_id' => auth()->id(), // Set the logged-in user's ID
                'secondary_reviewed_at' => now(), // Set the current timestamp
            ]);
        } else {
            // If the logged-in user is neither a primary nor a secondary reviewer, deny access
            return redirect()->back()->with('error', 'You are not authorized to reject this user.');
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'User access rejected successfully!');
    }

    public function approveAll($uar_id)
    {
        // Retrieve the UAR record
        $uar = UAR::findOrFail($uar_id);

        // Determine if the logged-in user is the primary or secondary reviewer
        if ($uar->primary_reviewer_id === auth()->id()) {
            // Update all pending users for primary reviewer
            UARUser::where('uar_id', $uar_id)
                ->update([
                    'status' => 'Approved',
                    'primary_reviewer_id' => auth()->id(),
                    'primary_reviewed_at' => now(),
                ]);
        } elseif ($uar->secondary_reviewer_id === auth()->id()) {
            // Update all pending users for secondary reviewer
            UARUser::where('uar_id', $uar_id)
                ->update([
                    'status' => 'Approved',
                    'secondary_reviewer_id' => auth()->id(),
                    'secondary_reviewed_at' => now(),
                ]);
        } else {
            // If the logged-in user is neither a primary nor a secondary reviewer, deny access
            return redirect()->back()->with('error', 'You are not authorized to approve all users.');
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'All users approved successfully!');
    }
}