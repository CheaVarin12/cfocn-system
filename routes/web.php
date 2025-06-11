<?php

use App\Events\BookingEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Admin\InvoiceController;
Route::get('/', function () {
    return redirect()->route('admin-login');
    // return view("admin::pusher");
    //return view("admin::welcome");
});

Route::get('/pusher', function () {
    return view("admin::pusher");
});

Route::get('export_pdf/{id}', [InvoiceController::class, 'Export_PDF'])->name('export_pdf');

Route::get('export_excel/{id}', [InvoiceController::class, 'Export_excel'])->name('export_excel');