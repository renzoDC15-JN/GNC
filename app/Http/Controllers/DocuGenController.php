<?php

namespace App\Http\Controllers;

use App\Models\ClientInformations;
use App\Models\Documents;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Homeful\Contacts\Data\FlatData;
use Homeful\Contacts\Models\Contact;

class DocuGenController extends Controller
{
    /**
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function download_document($id, $document,$isView){
        if (!File::exists(storage_path('app/public/converted_documents/'))) {
            File::makeDirectory(storage_path('app/public/converted_documents/'), 0755, true);
        }
        if (!File::exists(storage_path('app/public/converted_pdf/'))) {
            File::makeDirectory(storage_path('app/public/converted_pdf/'), 0755, true);
        }
        $client_information = new ClientInformations();
        $information = $client_information->find($id);

        $document_template = Documents::find($document);
        $filePath = storage_path('app/public/' . $document_template->file_attachment);

        $templateProcessor = new TemplateProcessor($filePath);

        $ci = $information->toArray();
        //set values
        foreach ($ci as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }
        //set image
//        $imagePath = storage_path('app/public/test_image.png');

//        $templateProcessor->setImageValue('image', array('path' => $imagePath, 'width' => 100, 'height' => 100, 'ratio' => false));

        $docx_file =storage_path('app/public/converted_documents/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.docx');
        $templateProcessor->saveAs($docx_file);

        $outputFile = storage_path('app/public/converted_pdf/');
        $command = env('LIBREOFFICE_PATH')." --headless --convert-to pdf:writer_pdf_Export --outdir '".storage_path('app/public/converted_pdf/'). "' " . escapeshellarg($docx_file);
        exec($command, $output, $return_var);
        $pdfFile = storage_path('app/public/converted_pdf/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.pdf');

        if (file_exists($pdfFile)) {
//            if($isView){
//               return response()->file($pdfFile, [
//                    'Content-Type' => 'application/pdf',
//                    'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
//                ]);
//            }else{
//               return response()->download($pdfFile);
//            }
            return $isView? response()->file($pdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
            ]):response()->download($pdfFile);
        } else {
            return response()->json(['error' => 'An error occurred during the file conversion'], 500);
        }
    }

    /**
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function contacts_download_document($id, $document,$isView,$name){
        if (!File::exists(storage_path('app/public/converted_documents/'))) {
            File::makeDirectory(storage_path('app/public/converted_documents/'), 0755, true);
        }
        if (!File::exists(storage_path('app/public/converted_pdf/'))) {
            File::makeDirectory(storage_path('app/public/converted_pdf/'), 0755, true);
        }
        $contacts = new Contact();
        $information = $contacts->find($id);

        $document_template = Documents::find($document);
        $filePath = storage_path('app/public/' . $document_template->file_attachment);

        $templateProcessor = new TemplateProcessor($filePath);

        $data = $information->toArray();
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                // Recursively process arrays
                foreach ($value as $subKey => &$subValue) {
                    if (is_null($subValue)) {
                        $subValue = ''; // Set null values to empty strings in nested arrays
                    }
                }
            } elseif (is_null($value)) {
                $value = ''; // Set null values to empty strings
            }
        }
        $ci = FlatData::fromModel(new Contact($data));
        // dd($ci);
        //set values
        foreach ($ci as $key => $value) {

            $templateProcessor->setValue($key, htmlspecialchars($value??''));
//            if($key=='deputy_chief_seller_officer' || $key=='chief_seller_officer'){
//                dd(htmlspecialchars($value??''),$value, $templateProcessor);
//            }
        }

        //set image
//        $imagePath = storage_path('app/public/test_image.png');
//        $templateProcessor->setImageValue('image', array('path' => $imagePath, 'width' => 100, 'height' => 100, 'ratio' => false));

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '',now()->format('Ymd_His')."_{$document_template->name}_{$information->last_name}");
        $docx_file =storage_path("app/public/converted_documents/{$filename}_templated.docx");

        $templateProcessor->saveAs($docx_file);

        $outputFile = storage_path('app/public/converted_pdf/');
        $command = env('LIBREOFFICE_PATH')." --headless --convert-to pdf:writer_pdf_Export --outdir '".storage_path('app/public/converted_pdf/'). "' " . escapeshellarg($docx_file);
        exec($command, $outputFile, $return_var);
        // dd($command);
        $pdfFile = storage_path("app/public/converted_pdf/{$filename}_templated.pdf");
        // dd($pdfFile);
        if (file_exists($pdfFile)) {
//            if($isView){
//               return response()->file($pdfFile, [
//                    'Content-Type' => 'application/pdf',
//                    'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
//                ]);
//            }else{
//               return response()->download($pdfFile);
//            }
            if ($document_template->type=='WORD'){
                return $isView? response()->file($docx_file, [
                    'Content-Type' => 'application/msword',
                    'Content-Disposition' => 'inline; filename="' . basename($docx_file) . '"'
                ]):response()->download($docx_file);
            }else{
                return $isView? response()->file($pdfFile, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
                ]):response()->download($pdfFile);
            }
        } else {
            return response()->json(['error' => 'An error occurred during the file conversion'], 500);
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function es_file($id){
        if (!File::exists(storage_path('app/public/converted_es/'))) {
            File::makeDirectory(storage_path('app/public/converted_es/'), 0755, true);
        }

        $contacts = new Contact();
        $information = $contacts->find($id);
        if (File::copy(base_path().'/resources/documents/es_sheets/es-pasinaya.xlsx', storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_copied.xlsx'))) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_copied.xlsx'));
            $worksheet = $spreadsheet->getActiveSheet();
            $spreadsheet->getSecurity()->setLockStructure(false);
            if($spreadsheet->getActiveSheet()->getTitle()){
//                $worksheet->setCellValue('L18','');
//                $worksheet->getCell('L18')->setCalculatedValue('');
//                dd($worksheet->getCell('L18'));
//                $worksheet->unmergeCells('B6:D6');
//                $worksheet->getCell('B6')->setValue($information->last_name.', '.$information->first_name.' '.$information->middle_name);
//                dd($worksheet->getCell('G8'));
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
//                dd($writer->save(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.xls')));
                $writer->save(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.xls'));
                dd(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.xls'));
            }
            return "File copied successfully to temp directory.";
        } else {
            return "Failed to copy file.";
        }
    }

    public function download_bc_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.borrowers_conformity.bc-pdf', ['data' => $information]);
        // $lastname = explode(',', $information->buyer_name);
        // DownloadLog::create([
        //     'user_id' => Auth()->user()->id,
        //     'pa_table_id' => $id,
        //     'type' => 'PA New Format',
        //     'file' => 'PA-'.$information->account_number.'-'.$lastname.'_NEW_FORMAT.pdf',
        // ]);
        return $pdf->download('Borrower Conformity '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function view_bc_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        // dd($information);
        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('pdf.borrowers_conformity.bc-pdf', ['data' => $information ]);
        return $pdf->stream('Borrower Conformity '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function download_li_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.letter_of_intent.li-pdf', ['data' => $information]);
        return $pdf->download('Letter of Intent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function view_li_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        // dd($information);
        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('pdf.letter_of_intent.li-pdf', ['data' => $information ]);
        return $pdf->stream('Letter of Intent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function download_sbc_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.solaris_buyer_conformity.sbc-pdf', ['data' => $information]);
        return $pdf->download('Letter of Intent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function view_sbc_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        // dd($information);
        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('pdf.solaris_buyer_conformity.sbc-pdf', ['data' => $information ]);
        return $pdf->stream('Letter of Intent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function download_sua_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.solaris_usufruct_agreement.sua-pdf', ['data' => $information]);
        return $pdf->download('Solaris Usufruct Agreement '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function view_sua_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('pdf.solaris_usufruct_agreement.sua-pdf', ['data' => $information ]);
        return $pdf->stream('Solaris Usufruct Agreement '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function download_soac_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.solaris_affidavit_of_consent.saoc-pdf', ['data' => $information]);
        return $pdf->download('Solaris Affidavit of Consent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function view_soac_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('pdf.solaris_affidavit_of_consent.saoc-pdf', ['data' => $information ]);
        return $pdf->stream('Solaris Affidavit of Consent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function download_svsaw_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.solaris_voluntary_surrender_and_waiver.svsaw-pdf', ['data' => $information]);
        return $pdf->download('Solaris Affidavit of Consent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

    public function view_svsaw_pdf($id){
        $ci = new ClientInformations();
        $information = $ci->find($id);
        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('pdf.solaris_voluntary_surrender_and_waiver.svsaw-pdf', ['data' => $information ]);
        return $pdf->stream('Solaris Affidavit of Consent '.$information->property_name.'-'.$information->buyer_name.'.pdf');
    }

}
