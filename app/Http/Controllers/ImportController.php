<?php

namespace App\Http\Controllers;

use App\Services\BankStatementImportService;
use App\Services\SmsParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function importSmsXml(Request $request)
    {
        $request->validate([
            'xml_file' => ['nullable', 'file'],
            'raw_xml' => ['nullable', 'string'],
        ]);

        $userId = Auth::id();
        $xmlContent = '';

        if ($request->hasFile('xml_file')) {
            $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());
        } elseif ($request->filled('raw_xml')) {
            $xmlContent = $request->raw_xml;
        } else {
            return redirect()->back()->with('error', 'Please upload an XML file or paste XML string.');
        }

        $smsParser = new SmsParserService();
        $res = $smsParser->parseSmsXml($userId, $xmlContent);

        if ($res['success']) {
            return redirect()->back()->with('success', "SMS Import Complete: Processed {$res['parsed_count']} messages, created {$res['created_count']} transactions.");
        }

        return redirect()->back()->with('error', $res['message'] ?? 'Import failed.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'statement_file' => ['required', 'file'],
        ]);

        $userId = Auth::id();
        $file = $request->file('statement_file');
        $rows = [];

        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        $service = new BankStatementImportService();
        $result = $service->importStatementRows($userId, $file->getClientOriginalName(), $rows);

        return redirect()->back()->with('success', "Bank Statement Processed: Imported {$result['processed']} transactions!");
    }
}
