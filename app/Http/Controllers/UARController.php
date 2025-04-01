<?php

namespace App\Http\Controllers;

use App\Models\UARFile;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UAR;

class UARController extends Controller
{
    public function create()
    {
        // Fetch users from the same organization
        $users = User::where('organization_code', auth()->user()->organization_code)->get();

        return view('uar.create', compact('users'));
    }

    public function show($id)
    {
        $uar = UAR::with(['appOwner', 'primaryReviewer', 'secondaryReviewer'])->findOrFail($id);
        return view('uar.show', compact('uar'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        // Find UAR and check organization
        $uar = UAR::where('id', $id)
            ->where('organization_code', $user->organization_code) // Restrict access
            ->first();

        // If UAR does not exist for this org, abort with 403
        if (!$uar) {
            abort(403, 'Unauthorized Access');
        }
        $users = User::where('organization_code', $uar->organization_code)->get();

        return view('uar.edit', compact('uar', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'application' => 'required|string|max:255',
            'app_owner' => 'required|exists:users,id',
            'primary_reviewer' => 'required|exists:users,id',
            'secondary_reviewer' => 'required|exists:users,id',
            'frequency' => 'required|in:monthly,quarterly,semiannual,annual',
            'start_at' => 'required|date',
            'next_due' => 'required|date',
        ]);
        $user = auth()->user();

        UAR::create([
            'application' => $request->application,
            'app_owner_id' => $request->app_owner,
            'primary_reviewer_id' => $request->primary_reviewer,
            'secondary_reviewer_id' => $request->secondary_reviewer,
            'frequency' => $request->frequency,
            'start_at' => $request->start_at,
            'next_due' => $request->next_due,
            'organization_code' => $user->organization_code,
            'created_by' => auth()->id(),
        ]);

        return redirect('/dashboard')->with('success', 'UAR created successfully!');
    }

    public function update(Request $request, $id)
    {
        $uar = UAR::findOrFail($id);

        $request->validate([
            'application' => 'required|string|max:255',
            'application_owner' => 'required|exists:users,id',
            'primary_reviewer' => 'required|exists:users,id',
            'secondary_reviewer' => 'required|exists:users,id',
            'frequency' => 'required|in:monthly,quarterly,semiannual,annual',
            'start_at' => 'required|date',
        ]);

        // Ensure correct column names in the database
        $uar->update([
            'application' => $request->application,
            'app_owner_id' => $request->application_owner,
            'primary_reviewer_id' => $request->primary_reviewer,
            'secondary_reviewer_id' => $request->secondary_reviewer,
            'frequency' => $request->frequency,
            'start_at' => $request->start_at,
            'next_due' => $this->calculateNextDueDate($request->start_at, $request->frequency),
        ]);

        return redirect()->route('dashboard')->with('success', 'UAR updated successfully!');
    }

    public function dashboard()
    {
        $userId = auth()->id();
        $organizationCode = auth()->user()->organization_code;

        // Fetch all UARs for the organization (Active UARs)
        $uars = UAR::where('organization_code', $organizationCode)->get();

        //dd($upcomingUARs);
        // Fetch only upcoming UARs where the user is involved
        $upcomingUARs = UAR::where('organization_code', $organizationCode)
            ->where(function ($query) use ($userId) {
                $query->where('app_owner_id', $userId)
                    ->orWhere('primary_reviewer_id', $userId)
                    ->orWhere('secondary_reviewer_id', $userId);
            })
            ->whereBetween('next_due', [now()->subDays(30), now()->addDays(30)])
            ->orderBy('next_due', 'asc')
            ->get();
        return view('dashboard', compact('uars', 'upcomingUARs'));
    }

    public function showUpcoming($id)
    {
        $uar = UAR::with(['files', 'users'])->findOrFail($id); // Load users here
        $userId = auth()->id();

        if ($uar->app_owner_id == $userId) {
            return view('uar.upload', compact('uar'));
        } elseif ($uar->primary_reviewer_id == $userId || $uar->secondary_reviewer_id == $userId) {
            if ($uar->files && $uar->files->isNotEmpty()) {
                return view('uar.review', compact('uar'));
            } else {
                return view('uar.awaiting', compact('uar'));
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function destroy($id)
    {
        $uar = UAR::findOrFail($id);

        // Ensure the user is authorized to delete this UAR (same organization)
        if ($uar->organization_code !== auth()->user()->organization_code) {
            abort(403, 'Unauthorized action.');
        }

        $uar->delete();

        return redirect()->route('dashboard')->with('success', 'UAR deleted successfully!');
    }


    // Function to calculate next_due
    private function calculateNextDueDate($startDate, $frequency)
    {
        switch ($frequency) {
            case 'monthly':
                return \Carbon\Carbon::parse($startDate)->addMonth();
            case 'quarterly':
                return \Carbon\Carbon::parse($startDate)->addMonths(3);
            case 'semiannual':
                return \Carbon\Carbon::parse($startDate)->addMonths(6);
            case 'annual':
                return \Carbon\Carbon::parse($startDate)->addYear();
            default:
                return $startDate;
        }
    }

    //not in use below function, we are using function in UARFIleController
    public function upload(Request $request, $id)
    {
        $uar = UAR::findOrFail($id);

        // Validate file uploads
        $request->validate([
            'user_list' => 'required|mimes:xls,xlsx,csv',
            'screenshot' => 'required|mimes:pdf,jpg,jpeg,png,docx',
        ]);

        // Store files
        $userListPath = $request->file('user_list')->store('uploads/user_lists');
        $screenshotPath = $request->file('screenshot')->store('uploads/screenshots');

        // Save file paths to the UARFiles table
        UARFile::create([
            'uar_id' => $uar->id,
            'user_list' => $userListPath,
            'screenshot' => $screenshotPath,
        ]);

        if ($userListPath && $screenshotPath) {
            return redirect()->route('dashboard')->with('success', 'Files uploaded successfully.');
        } else {
            return redirect()->back()->with('error', 'File upload failed. Please try again.');
        }

    }
}
