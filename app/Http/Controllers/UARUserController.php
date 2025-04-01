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

        $filePath = storage_path('app/' . $uarFile->user_list);

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex === 1) continue; // Skip header row

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
}
