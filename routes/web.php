<?php

use App\Http\Controllers\DocuGenController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
})->name('login');

//Route::get('/', function () {
//    return view('welcome');
//});
// PDF
Route::middleware([Authenticate::class])->group(function () {
    // Download
    Route::get('pdf-bc-dl/{id}', [DocuGenController::class, 'download_bc_pdf'])->name('download.bc'); // working
    Route::get('pdf-li-dl/{id}', [DocuGenController::class, 'download_li_pdf'])->name('download.li'); //letter of intent
    Route::get('pdf-soac-dl/{id}', [DocuGenController::class, 'download_soac_pdf'])->name('download.soac');//solaris affidavit of consent
    Route::get('pdf-sbc-dl/{id}', [DocuGenController::class, 'download_sbc_pdf'])->name('download.sbc');//solaris buyers confirmity
    Route::get('pdf-sua-dl/{id}', [DocuGenController::class, 'download_sua_pdf'])->name('download.sua');//solaris usufruct agreement
    Route::get('pdf-svsaw-dl/{id}', [DocuGenController::class, 'download_svsaw_pdf'])->name('download.svsaw');//solaris voluntary surrender and waiver
    // View
    Route::get('pdf-bc-view/{id}', [DocuGenController::class, 'view_bc_pdf'])->name('view.bc');
    Route::get('pdf-li-view/{id}', [DocuGenController::class, 'view_li_pdf'])->name('view.li'); //letter of intent
    Route::get('pdf-soac-view/{id}', [DocuGenController::class, 'view_soac_pdf'])->name('view.soac');//solaris affidavit of consent
    Route::get('pdf-sbc-view/{id}', [DocuGenController::class, 'view_sbc_pdf'])->name('view.sbc');//solaris buyers confirmity
    Route::get('pdf-sua-view/{id}', [DocuGenController::class, 'view_sua_pdf'])->name('view.sua');//solaris usufruct agreement
    Route::get('pdf-svsaw-view/{id}', [DocuGenController::class, 'view_svsaw_pdf'])->name('view.svsaw');//solaris voluntary surrender and waiver

    Route::get('docx-pdf-download/{id}/{document}/{is_view}', [DocuGenController::class, 'download_document'])->name('docx_to_pdf');
    Route::get('contacts-docx-pdf-download/{id}/{document}/{is_view}', [DocuGenController::class, 'contacts_download_document'])->name('contacts_docx_to_pdf');

    Route::get('es-sheet/{id}', [DocuGenController::class, 'es_file'])->name('es_file');//solaris voluntary surrender and waiver

    Route::get('/document-stream/{id}', function ($id) {
        $record = \App\Models\Documents::findOrFail($id);
        $component = new \App\Livewire\DocumentPreviewComponent();
        $component->record = $record;
        return $component->streamPdf();
    })->name('document.stream');
});
