<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\UARFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UAR;
use App\Models\UARUser;
use PhpOffice\PhpSpreadsheet\IOFactory;



class UARFileController extends Controller
{

    public function upload(Request $request, $id)
    {
        \Log::info('Starting upload process for UAR ID: ' . $id);

        $request->validate([
            'user_list' => 'required|file|mimes:csv,xlsx',
            'screenshot' => 'required|file|mimes:pdf,jpg,jpeg,png,docx'
        ]);
        \Log::info('Validation passed for uploaded files.');

        $userList = $request->file('user_list')->store('uploads/user_lists', 'public');
        \Log::info('User list file stored at: ' . $userList);

        $screenshot = $request->file('screenshot')->store('uploads/screenshots', 'public');
        \Log::info('Screenshot file stored at: ' . $screenshot);

        UARFile::create([
            'uar_id' => $id,
            'user_list' => $userList,
            'screenshot' => $screenshot,
        ]);
        \Log::info('UARFile record created in the database.');

        $filePath = storage_path('app/public/uploads/user_lists/' . basename($userList));
        \Log::info('Processing user list file at: ' . $filePath);

        try {
            $spreadsheet = IOFactory::load($filePath);
            \Log::info('Spreadsheet loaded successfully.');

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];
            \Log::info('Reading rows from the spreadsheet.');

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex === 1) {
                    \Log::info('Skipping header row.');
                    continue;
                }

                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = $cell->getValue();
                }

                $rows[] = [
                    'uar_id' => $id,
                    'user_data' => json_encode($cells),
                    'primary_review_status' => 'pending',
                    'secondary_review_status' => 'pending',
                    'primary_reviewer_id' => NULL,
                    'secondary_reviewer_id' => NULL,
                ];
            }

            UARUser::insert($rows);
            \Log::info('User data inserted into UARUser table.');

        } catch (\Exception $e) {
            \Log::error('Error processing file: ' . $e->getMessage());
            return back()->with('error', 'Error processing file: ' . $e->getMessage());
        }

        \Log::info('Upload process completed successfully for UAR ID: ' . $id);
        return redirect()->route('dashboard')->with('success', 'Files uploaded and users stored successfully!');
    }
/*    {
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

        // Parse the uploaded Excel/CSV file and store user data
        $filePath = storage_path('app/public/uploads/user_lists/' . basename($userList));

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
                    'uar_id' => $id,
                    'user_data' => json_encode($cells),
                    'primary_review_status' => 'pending',
                    'secondary_review_status' => 'pending',
                ];
            }

            UARUser::insert($rows);

        } catch (\Exception $e) {
            \Log::error('Error processing file: ' . $e->getMessage());
            return back()->with('error', 'Error processing file: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Files uploaded and users stored successfully!');
    } */

    public function showUploadForm($id)
    {
        $uar = UARFile::findOrFail($id); // Fetch the UARFile record by ID
        return view('uar.upload', compact('uar')); // Pass the UARFile object to the view
    }


}
