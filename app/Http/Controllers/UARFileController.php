<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\UARFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UAR;
use App\Models\UARUser;



class UARFileController extends Controller
{
    public function upload(Request $request, $id)
    {
        $request->validate([
            'user_list' => 'required|file|mimes:csv,xlsx',
            'screenshot' => 'required|file|mimes:pdf,jpg,jpeg,png,docx'
        ]);

        $userList = $request->file('user_list')->store('uploads/user_lists', 'public');
        $screenshot = $request->file('screenshot')->store('uploads/screenshots', 'public');

        UARFile::create([
            'uar_id' => $id,
            'user_list' => $userList,
            'screenshot' => $screenshot,
        ]);

        /*Upload functionality
        $path = $request->file('user_listing')->getRealPath();
        $data = Excel::toArray([], $path);
        // For CSV, you can use $data = array_map('str_getcsv', file($path));
    
        foreach ($data[0] as $row) {
            UarUser::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'role' => $row['role'],
                // Add other columns from the Excel/CSV file
            ]);
        }
*/

       
       return redirect()->route('dashboard')->with('success', 'Files uploaded successfully!');
    }

    public function showUploadForm($id)
    {
        $uar = UARFile::findOrFail($id); // Fetch the UARFile record by ID
        return view('uar.upload', compact('uar')); // Pass the UARFile object to the view
    }
}
