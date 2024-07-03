<?php

namespace App\Http\Controllers;

use App\Models\ClientInformations;
use Illuminate\Http\Request;
use PDF;

class DocuGenController extends Controller
{
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

}
