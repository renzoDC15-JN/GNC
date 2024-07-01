<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\TemplateProcessor;

use PhpOffice\PhpWord\Settings;
use setasign\Fpdi\TcpdfFpdi;

class DocumentPreviewComponent extends Component
{

    public ?Model $record = null;
    public ?string $html_code = null;

    public function oldRender()
    {
        if ($this->record) {
            $filePath = storage_path('app/public/' . $this->record->file_attachment);
            $templateProcessor = new TemplateProcessor($filePath);
            $templateProcessor->setValue('name', 'John Doe');
            $templateProcessor->saveAs('templated.docx');

            // Load the processed DOCX file
            $phpWord = IOFactory::load('templated.docx');

            // Convert DOCX to HTML
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            ob_start();
            $htmlWriter->save('php://output');
            $htmlContent = ob_get_clean();

            // Initialize Dompdf and write the HTML content to PDF
            $dompdf = new Dompdf();
            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdfFileName = 'templated.pdf';
            $pdfFilePath = storage_path('app/public/' . $pdfFileName);
            file_put_contents($pdfFilePath, $dompdf->output());

//            $phpWord = IOFactory::load($filePath);
//            // Convert DOCX to HTML
//            $htmlWriter = IOFactory::createWriter($phpWord, 'Word2007');
////            $htmlContent = $htmlWriter->getContent();
//            $htmlWriter->save($this->record->name.'.docx');
////            $this->html_code = $htmlContent;
        }
        return view('livewire.document-preview-component');
    }

    public function render()
    {
//        if ($this->record) {
//            $filePath = storage_path('app/public/' . $this->record->file_attachment);
//            $templateProcessor = new TemplateProcessor($filePath);
//            $templateProcessor->setValue('name', 'John Doe');
//            $templateProcessor->saveAs('template output.docx');
//
//            // Load the processed DOCX file
//            $phpWord = IOFactory::load('templated.docx');
//
//        }
        return view('livewire.document-preview-component');
    }

    public function streamPdf()
    {
//        if($this->record) {
//            $filePath = storage_path('app/public/' . $this->record->file_attachment);
//            $phpWord = IOFactory::load($filePath);
//
//            // Convert DOCX to HTML
//            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
//            $htmlContent = $htmlWriter->getContent();
//            $this->html_code = $htmlContent;
//
//            // Generate PDF
//            $options = new Options();
//            $options->set('isHtml5ParserEnabled', true);
//            $dompdf = new Dompdf($options);
//            $dompdf->loadHtml($this->html_code);
//            $dompdf->setPaper('A4', 'portrait');
//            $dompdf->render();
//
//            // Stream the PDF directly to the browser
//            $pdfContent = $dompdf->output();
//        }
//        return response($pdfContent)->header('Content-Type', 'application/pdf');
    }
}
