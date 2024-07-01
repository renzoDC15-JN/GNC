<?php

use App\Http\Controllers\DocuGenController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// PDF
Route::middleware([Authenticate::class])->group(function () {
    // Download
    Route::get('pdf-bc-dl/{id}', [DocuGenController::class, 'download_bc_pdf'])->name('download.bc'); // working
    // View
    Route::get('pdf-bc-view/{id}', [DocuGenController::class, 'view_bc_pdf'])->name('view.bc');

    Route::get('/document-stream/{id}', function ($id) {
        $record = \App\Models\Documents::findOrFail($id);
        $component = new \App\Livewire\DocumentPreviewComponent();
        $component->record = $record;
        return $component->streamPdf();
    })->name('document.stream');
});
