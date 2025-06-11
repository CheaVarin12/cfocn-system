<?php

use App\Services\FileManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\CreditNoteController;
use App\Http\Controllers\Admin\CloseDateController;
use App\Http\Controllers\Admin\DMCFileManagerController;
use App\Http\Controllers\Admin\FttxController;
use App\Http\Controllers\Admin\FttxCustomerPriceController;
use App\Http\Controllers\Admin\FttxCustomerTypeController;
use App\Http\Controllers\Admin\FttxExpirationReportController;
use App\Http\Controllers\Admin\FttxPosSpeedController;
use App\Http\Controllers\Admin\FttxReportController;
use App\Http\Controllers\Admin\FttxSettingPriceController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\Report\ARAcgingReportController;
use App\Http\Controllers\Admin\Report\CustomerInfoController;
use App\Http\Controllers\Admin\Report\CustomerReportController;
use App\Http\Controllers\Admin\Report\IncomeReportController;
use App\Http\Controllers\Admin\Report\ReceivePaymentController;
use App\Http\Controllers\Admin\Report\RevenueReportController;
use App\Http\Controllers\Admin\Report\SaleJournalReportController;
use App\Http\Controllers\Admin\Report\SummaryAnnualReportController;
use App\Http\Controllers\Admin\Report\SummaryInvoiceController;
use App\Http\Controllers\Admin\WorkOrderCreditNoteController;
use App\Http\Controllers\Admin\WorkOrderInvoiceController;
use App\Http\Controllers\Admin\WorkOrderReceiptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('optimize', function () {
    Artisan::call('optimize:clear');
    return "Cache is cleared";
});
Route::get('seed', function () {
    Artisan::call('db:seed --class=PermissionSeeder');
    return "Seed db success";
});

// Auth
Route::prefix('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin-login');
    });
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::get('/forgot', [UserController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/login/post', [AuthController::class, 'login'])->name('login-post');
    Route::get('/sign-out', [AuthController::class, 'signOut'])->name('sign-out');
});
Route::middleware(['AdminGuard'])
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin-dashboard');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/list/pac/{id}', [DashboardController::class, 'ListPacInproject'])->name('list-pac-project');

        // User
        Route::prefix('user')
            ->name('user-')
            ->group(function () {
                Route::get('list/{id?}', [UserController::class, 'index'])->name('list');
                Route::get('create/{id?}', [UserController::class, 'onCreate'])->name('create');
                Route::post('save/{id?}', [UserController::class, 'onSave'])->name('save');
                Route::match(['get', 'post'], 'status/{id}/{status}', [UserController::class, 'onUpdateStatus'])->name('status');
                Route::get('change-password/{id}', [UserController::class, 'onChangePassword'])->name('change-password');
                Route::post('save-password/{id}', [UserController::class, 'onSavePassword'])->name('save-password');
                Route::get('permission/{id}', [UserController::class, 'setPermission'])->name('permission');
                Route::post('save-permission/{id}', [UserController::class, 'savePermission'])->name('save-permission');
            });
        //Type
        Route::group([
            'prefix' => 'type',
            'as' => 'type-'
        ], function () {
            Route::get('list/{status?}', [TypeController::class, 'index'])->name('list');
            Route::get('create', [TypeController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [TypeController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [TypeController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [TypeController::class, 'onUpdateStatus'])->name('status');
        });

        //Service
        Route::group([
            'prefix' => 'service',
            'as' => 'service-'
        ], function () {
            Route::get('list/{status?}', [ServiceController::class, 'index'])->name('list');
            Route::get('create', [ServiceController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [ServiceController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [ServiceController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [ServiceController::class, 'onUpdateStatus'])->name('status');
        });

        //Project
        Route::group([
            'prefix' => 'project',
            'as' => 'project-'
        ], function () {
            Route::get('list/{status?}', [ProjectController::class, 'index'])->name('list');
            Route::get('create', [ProjectController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [ProjectController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [ProjectController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [ProjectController::class, 'onUpdateStatus'])->name('status');
        });

        //Customer
        Route::group([
            'prefix' => 'customer',
            'as' => 'customer-'
        ], function () {
            Route::get('list/{status?}', [CustomerController::class, 'index'])->name('list');
            Route::get('create', [CustomerController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [CustomerController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [CustomerController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [CustomerController::class, 'onUpdateStatus'])->name('status');
            Route::get('/export-customer-excel', [CustomerController::class, 'exportCustomerExcel'])->name('export-customer-excel');
            Route::get('customer-report', [CustomerController::class, 'report'])->name('report');
            Route::get('delete/{id?}', [CustomerController::class, 'delete'])->name('delete');
            Route::get('destroy/{id?}', [CustomerController::class, 'destroy'])->name('destroy');
            Route::get('restore/{id?}', [CustomerController::class, 'restore'])->name('restore');

            //import customer
            Route::post('import-excel', [CustomerController::class, 'importExcel'])->name('import-excel');

            Route::get('/document/{id}', [CustomerController::class, 'documentIndex'])->name('list-document');
            Route::get('/first/{id}', [CustomerController::class, 'first'])->name('first');
            Route::get('/files/{id}', [CustomerController::class, 'getFiles'])->name('files');
            Route::get('/folders/{id}', [CustomerController::class, 'getFolders'])->name('folders');
            Route::post('/upload', [CustomerController::class, 'uploadFile'])->name('upload');
            Route::delete('/delete-file', [CustomerController::class, 'deleteFile'])->name('delete-file');
            // folder-------
            Route::post('/create-folder', [CustomerController::class, 'createFolder'])->name('create-folder');
            Route::post('/rename-folder', [CustomerController::class, 'renameFolder'])->name('rename-folder');
            Route::delete('/delete-folder', [CustomerController::class, 'deleteFolder'])->name('delete-folder');

            //trash bin----
            Route::delete('/delete-all', [CustomerController::class, 'deleteAll'])->name('delete-all');
            Route::put('/restore-all', [CustomerController::class, 'restoreAll'])->name('restore-all');

            //getCustomerHistory
            Route::get('/history/{id}', [CustomerController::class, 'getCustomerHistory']);
        });
        //Slide
        Route::group([
            'prefix' => 'slide',
            'as' => 'slide-'
        ], function () {
            Route::get('list/{status?}', [SlideController::class, 'index'])->name('list');
            Route::get('create', [SlideController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [SlideController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [SlideController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [SlideController::class, 'onUpdateStatus'])->name('status');
            Route::get('delete/{id}', [SlideController::class, 'delete'])->name('delete');
            Route::get('restore/{id}', [SlideController::class, 'Restore'])->name('restore');
            Route::get('destroy/{id}', [SlideController::class, 'Destroy'])->name('destroy');
        });
        // credit note
        Route::group([
            'prefix' => 'credit-note',
            'as' => 'credit-note-'
        ], function () {
            Route::get('list/{status}', [CreditNoteController::class, 'index'])->name('list');
            Route::get('/create/{id?}', [CreditNoteController::class, 'create'])->name('create');
            Route::post('save/', [CreditNoteController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [CreditNoteController::class, 'onUpdateStatus'])->name('status');
            Route::get('/view-detail/{id}', [CreditNoteController::class, 'viewDetail'])->name('detail');
            Route::get('/export-excel/{id}', [CreditNoteController::class, 'exportCreditNoteExcel'])->name('export-credit-note-excel');

            //route
            Route::get('/view-detail-doc-submit/{id}', [CreditNoteController::class, 'viewDetailDocSubmit'])->name('viewDetailDocSubmit');
            Route::post('dmc-submit', [CreditNoteController::class, 'DMCSubmit'])->name('DMCSubmit');
            Route::get('/doc-edit/{id}', [CreditNoteController::class, 'DocEdit'])->name('DocEdit');

            //pickup
            Route::get('/pick-up-invoice/{invoice_number}', [CreditNoteController::class, 'pickUpInvoice'])->name('pick-up-invoice');
            Route::get('/pick-up-credit-note/{invoice_number}', [CreditNoteController::class, 'pickUpCreditNote'])->name('pick-up-credit-note');
        });
        //Invoices
        Route::group([
            'prefix' => 'invoice',
            'as'     => 'invoice-'
        ], function () {
            Route::get('/list/{status?}', [InvoiceController::class, 'index'])->name('list');
            Route::post('/save', [InvoiceController::class, 'onSave'])->name('save');
            Route::get('/view-receipt/{id}', [InvoiceController::class, 'viewReceipt'])->name('receipt');
            Route::get('/export-invoice-excel/{id}', [InvoiceController::class, 'exportInvoiceExcel'])->name('export-invoice-excel');
            Route::get('/view-detail/{id}', [InvoiceController::class, 'viewDetail'])->name('detail');
            Route::post('add-days', [InvoiceController::class, 'addDay'])->name('add-days');
            Route::get('delete/{id}', [InvoiceController::class, 'onDelete'])->name('delete');
            Route::get('edit/{id}', [InvoiceController::class, 'onEdit'])->name('edit');
            Route::post('/update/{id?}', [InvoiceController::class, 'onUpdate'])->name('update');

            Route::get('copy/{id}', [InvoiceController::class, 'copy'])->name('copy');
            Route::post('onCopy', [InvoiceController::class, 'onCopy'])->name('copy-invoice');
            Route::post('copy-invoice-multiple-pac', [InvoiceController::class, 'onCopyInvoiceMultiplePac'])->name('copy-invoice-multiple-pac');

            Route::post('/dmc-submit', [InvoiceController::class, 'DMCSubmit'])->name('DMCSubmit');
            Route::post('destroy', [InvoiceController::class, 'destroy'])->name('destroy');

            Route::post('/save-create', [InvoiceController::class, 'onSaveCreate'])->name('save-create');
        });

        // PO
        Route::prefix('po')->group(function () {
            Route::group([
                'prefix' => 'po-service',
                'as'     => 'po-service-',
            ], function () {
                Route::get('/list/{status}', [PurchaseOrderController::class, 'index'])->name('list');
                Route::post('/save', [PurchaseOrderController::class, 'onSave'])->name('save');
                Route::post('/update/{id}', [PurchaseOrderController::class, 'onUpdate'])->name('update');
                Route::match(['get', 'post'], 'status/{id}/{status}', [PurchaseOrderController::class, 'onUpdateStatus'])->name('status');
                // upload file
                Route::post('/upload', [PurchaseOrderController::class, 'uploadFile'])->name('upload');
                //selectByType
                Route::get('/type-service/{id?}', [PurchaseOrderController::class, 'selectTypeService'])->name("select-type-service");
            });

            // create pac
            Route::post('/save-pac', [PurchaseOrderController::class, 'onSavePac'])->name('save-pac');
            Route::post('/delete-file', [PurchaseOrderController::class, 'onDeleteFile'])->name('delete-file');
            Route::get('/get-file/{id?}', [PurchaseOrderController::class, 'onGetFile'])->name('get-file');
            Route::get('/delete-po/{id?}', [PurchaseOrderController::class, 'onDeletePo'])->name('delete-po');
        });

        //Purchase
        Route::group([
            'prefix' => 'purchase',
            'as'     => 'purchase-'
        ], function () {
            Route::get('/list/{status}', [PurchaseController::class, 'index'])->name('list');
            Route::get('/create', [PurchaseController::class, 'onCreate'])->name('create');
            Route::get('/edit/{id?}', [PurchaseController::class, 'onEdit'])->name('edit');
            Route::post('/save', [PurchaseController::class, 'onSave'])->name('save');
            Route::post('/update/{id}', [PurchaseController::class, 'onUpdate'])->name('update');

            Route::get('/export-excel/{id}', [PurchaseController::class, 'exportExcel'])->name('export-excel');
            Route::match(['get', 'post'], 'status/{id}/{status}', [PurchaseController::class, 'onUpdateStatus'])->name('status');

            //selectByType
            Route::get('/type-service/{id?}', [PurchaseController::class, 'selectTypeToSerive'])->name("select-type-service");

            //import PAC
            Route::post('import-excel', [PurchaseController::class, 'importExcel'])->name('import-excel');
            Route::post('import-excel-detail', [PurchaseController::class, 'importExcelDetail'])->name('import-excel-detail');

            //invoice
            Route::get('/create-invoice/{id}', [PurchaseController::class, 'createInvoice'])->name('createInvoice');
            Route::post('save/invoice', [PurchaseController::class, 'onSaveInvoice'])->name('save-invoice');

            Route::get('/document/{id}', [PurchaseController::class, 'documentIndex'])->name('list-document');
            Route::get('/first/{id}', [PurchaseController::class, 'first'])->name('first');
            Route::get('/files/{id}', [PurchaseController::class, 'getFiles'])->name('files');
            Route::get('/folders/{id}', [PurchaseController::class, 'getFolders'])->name('folders');
            Route::post('/upload', [PurchaseController::class, 'uploadFile'])->name('upload');
            Route::delete('/delete-file', [PurchaseController::class, 'deleteFile'])->name('delete-file');
            // folder-------
            Route::post('/create-folder', [PurchaseController::class, 'createFolder'])->name('create-folder');
            Route::post('/rename-folder', [PurchaseController::class, 'renameFolder'])->name('rename-folder');
            Route::delete('/delete-folder', [PurchaseController::class, 'deleteFolder'])->name('delete-folder');

            //trash bin----
            Route::delete('/delete-all', [PurchaseController::class, 'deleteAll'])->name('delete-all');
            Route::put('/restore-all', [PurchaseController::class, 'restoreAll'])->name('restore-all');
        });

        //Receipt 
        Route::group([
            'prefix' => 'receipt',
            'as' => 'receipt-'
        ], function () {
            Route::get('list/{status?}', [ReceiptController::class, 'index'])->name('list');
            Route::get('view-detail/{id?}', [ReceiptController::class, 'viewReceipt'])->name('detail');
            Route::post('save/', [ReceiptController::class, 'onSave'])->name('save');
            Route::post('save-new/', [ReceiptController::class, 'onSaveNew'])->name('save-new');
            Route::get('/edit/{id?}', [ReceiptController::class, 'onEdit'])->name('edit');
            Route::post('/update/{id}', [ReceiptController::class, 'onUpdate'])->name('update');
            // Route::match(['get', 'post'], 'status/{id}/{status}', [ReceiptController::class, 'onUpdateStatus'])->name('status');
            Route::get('/edit-status/{id}', [ReceiptController::class, 'onEditStatus'])->name('edit-status');
            Route::post('/update-status/{id}', [ReceiptController::class, 'onUpdateStatus'])->name('update-status');
            Route::get('/export-receipt-excel/{id}', [ReceiptController::class, 'exportReceiptExcel'])->name('export-receipt-excel');
            Route::get('delete/{id?}', [ReceiptController::class, 'onDelete'])->name('delete');
        });

        // Work Order
        Route::prefix('work-order')->group(function () {

            // Order
            Route::group([
                'prefix' => 'order',
                'as'     => 'order-'
            ], function () {
                Route::get('/list/{status}', [OrderController::class, 'index'])->name('list');
                Route::get('/create', [OrderController::class, 'onCreate'])->name('create');
                Route::get('/edit/{id?}', [OrderController::class, 'onEdit'])->name('edit');
                Route::post('/save', [OrderController::class, 'onSave'])->name('save');
                Route::post('/update/{id}', [OrderController::class, 'onUpdate'])->name('update');

                Route::get('/export-excel/{id}', [OrderController::class, 'exportExcel'])->name('export-excel');
                Route::match(['get', 'post'], 'status/{id}/{status}', [OrderController::class, 'onUpdateStatus'])->name('status');

                //selectByType
                Route::get('/type-service/{id?}', [OrderController::class, 'selectTypeToSerive'])->name("select-type-service");

                //invoice
                Route::get('/create-invoice/{id}', [OrderController::class, 'createInvoice'])->name('createInvoice');
                Route::post('save/invoice', [OrderController::class, 'onSaveInvoice'])->name('save-invoice');
            });


            // Invoices
            Route::group([
                'prefix' => 'invoice',
                'as'     => 'work-order-invoice-'
            ], function () {
                Route::get('/list/{status?}', [WorkOrderInvoiceController::class, 'index'])->name('list');
                Route::post('/save', [WorkOrderInvoiceController::class, 'onSave'])->name('save');
                Route::get('/view-receipt/{id}', [WorkOrderInvoiceController::class, 'viewReceipt'])->name('receipt');
                Route::get('/export-invoice-excel/{id}', [WorkOrderInvoiceController::class, 'exportInvoiceExcel'])->name('export-invoice-excel');
                Route::get('/view-detail/{id}', [WorkOrderInvoiceController::class, 'viewDetail'])->name('detail');
                Route::post('add-days', [WorkOrderInvoiceController::class, 'addDay'])->name('add-days');
                Route::get('delete/{id}', [WorkOrderInvoiceController::class, 'onDelete'])->name('delete');
                Route::get('edit/{id}', [WorkOrderInvoiceController::class, 'onEdit'])->name('edit');
                Route::post('/update/{id?}', [WorkOrderInvoiceController::class, 'onUpdate'])->name('update');

                Route::get('copy/{id}', [WorkOrderInvoiceController::class, 'copy'])->name('copy');
                Route::post('onCopy', [WorkOrderInvoiceController::class, 'onCopy'])->name('copy-invoice');

                Route::post('/dmc-submit', [WorkOrderInvoiceController::class, 'DMCSubmit'])->name('DMCSubmit');
                Route::post('destroy', [WorkOrderInvoiceController::class, 'destroy'])->name('destroy');

                //Fetch FTTH Service
                Route::get('fetch-ftth-service', [WorkOrderInvoiceController::class, 'fetchFTTHService'])->name('fetch-ftth-service');
            });

            //Receipt 
            Route::group([
                'prefix' => 'receipt',
                'as' => 'work-order-receipt-'
            ], function () {
                Route::get('list/{status?}', [WorkOrderReceiptController::class, 'index'])->name('list');
                Route::get('view-detail/{id?}', [WorkOrderReceiptController::class, 'viewReceipt'])->name('detail');
                Route::post('save/', [WorkOrderReceiptController::class, 'onSave'])->name('save');
                Route::post('save-new/', [WorkOrderReceiptController::class, 'onSaveNew'])->name('save-new');
                Route::get('/edit/{id?}', [WorkOrderReceiptController::class, 'onEdit'])->name('edit');
                Route::post('/update', [WorkOrderReceiptController::class, 'onUpdate'])->name('update');
                Route::get('/edit-status/{id}', [WorkOrderReceiptController::class, 'onEditStatus'])->name('edit-status');
                Route::post('/update-status', [WorkOrderReceiptController::class, 'onUpdateStatus'])->name('update-status');
                Route::get('/export-receipt-excel/{id}', [WorkOrderReceiptController::class, 'exportReceiptExcel'])->name('export-receipt-excel');
                Route::get('/pick-up-invoice/{invoice_number}', [WorkOrderReceiptController::class, 'pickUpWorkOrderInvoice'])->name('pick-up-invoice');
                Route::get('/pick-up-credit-note/{invoice_number}', [WorkOrderReceiptController::class, 'pickUpWorkOrderCreditNote'])->name('pick-up-credit-note');
                Route::get('delete/{id}', [WorkOrderReceiptController::class, 'onDelete'])->name('delete');
            });

            //Credit Note
            Route::group([
                'prefix' => 'credit-note',
                'as' => 'work-order-credit-note-'
            ], function () {
                Route::get('list/{status}', [WorkOrderCreditNoteController::class, 'index'])->name('list');
                Route::post('save/', [WorkOrderCreditNoteController::class, 'onSave'])->name('save');
                Route::match(['get', 'post'], 'status/{id}/{status}', [WorkOrderCreditNoteController::class, 'onUpdateStatus'])->name('status');
                Route::get('/view-detail/{id}', [WorkOrderCreditNoteController::class, 'viewDetail'])->name('detail');
                Route::get('/export-excel/{id}', [WorkOrderCreditNoteController::class, 'exportCreditNoteExcel'])->name('export-credit-note-excel');

                Route::post('dmc-submit', [WorkOrderCreditNoteController::class, 'DMCSubmit'])->name('DMCSubmit');
                Route::get('select-invoice', [WorkOrderCreditNoteController::class, 'SelectInvoice'])->name('select-invoice');
                Route::get('/pick-up-invoice/{invoice_number}', [WorkOrderCreditNoteController::class, 'pickUpInvoice'])->name('pick-up-invoice');
            });
        });


        // fttx
        Route::prefix('fttx')->group(function () {
            Route::group([
                'prefix' => 'fttx',
                'as' => 'fttx-'
            ], function () {
                Route::get('list/{status?}', [FttxController::class, 'index'])->name('list');
                Route::post('save/{id?}', [FttxController::class, 'onSave'])->name('save');
                Route::get('delete/{id?}', [FttxController::class, 'onDelete'])->name('delete');
                Route::post('import-excel', [FttxController::class, 'importExcel'])->name('import-excel');
                Route::get('get-pos-speed/{id?}', [FttxController::class, 'getPosSpeed'])->name('get-pos-speed');
                Route::get('get-fttx-detail', [FttxController::class, 'getFttxDetail'])->name('get-fttx-detail');
                Route::post('delete-fttx-detail', [FttxController::class, 'onDeleteFttxDetail'])->name('delete-fttx-detail');
                Route::post('store-fttx-detail', [FttxController::class, 'onStoreDetail'])->name('store-fttx-detail');
                Route::post('renewal', [FttxController::class, 'onRenewal'])->name('renewal');
                Route::post('renewal-all', [FttxController::class, 'onRenewalAll'])->name('renewal-all');
                Route::get('update-status', [FttxController::class, 'onUpdateStatus'])->name('update-status');
                Route::get('template-upload', [FttxController::class, 'downloadTemplateUpload'])->name('template-upload');
                Route::post('save-column', [FttxController::class, 'onSaveColumn'])->name('save-column');
            });

            Route::group([
                'prefix' => 'customer-type',
                'as' => 'customer-type-'
            ], function () {
                Route::get('list/{status?}', [FttxCustomerTypeController::class, 'index'])->name('list');
                Route::post('save/{id?}', [FttxCustomerTypeController::class, 'onSave'])->name('save');
                Route::match(['get', 'post'], 'status/{id}/{status}', [FttxCustomerTypeController::class, 'onUpdateStatus'])->name('status');
            });

            Route::group([
                'prefix' => 'pos-speed',
                'as' => 'pos-speed-'
            ], function () {
                Route::get('list/{status?}', [FttxPosSpeedController::class, 'index'])->name('list');
                Route::post('save/{id?}', [FttxPosSpeedController::class, 'onSave'])->name('save');
                Route::post('update-price-by-payment-period/{id?}', [FttxPosSpeedController::class, 'onUpdatePriceByPaymentPeriod'])->name('update-price-by-payment-period');
                Route::match(['get', 'post'], 'status/{id}/{status}', [FttxPosSpeedController::class, 'onUpdateStatus'])->name('status');
            });

            Route::group([
                'prefix' => 'setting-price',
                'as' => 'setting-price-'
            ], function () {
                Route::get('list/{status?}', [FttxSettingPriceController::class, 'index'])->name('list');
                Route::post('save/{id?}', [FttxSettingPriceController::class, 'onSave'])->name('save');
                Route::match(['get', 'post'], 'status/{id}/{status}', [FttxSettingPriceController::class, 'onUpdateStatus'])->name('status');
            });

            Route::group([
                'prefix' => 'customer-price',
                'as' => 'customer-price-'
            ], function () {
                Route::get('list/{status?}', [FttxCustomerPriceController::class, 'index'])->name('list');
                Route::post('save/{id?}', [FttxCustomerPriceController::class, 'onSave'])->name('save');
                Route::match(['get', 'post'], 'status/{id}/{status}', [FttxCustomerPriceController::class, 'onUpdateStatus'])->name('status');
            });

            Route::group([
                'prefix' => 'report',
                'as' => 'report-'
            ], function () {
                Route::get('list', [FttxReportController::class, 'index'])->name('list');
                Route::get('get-detail', [FttxReportController::class, 'getDetail'])->name('get-detail');
            });


            Route::group([
                'prefix' => 'expiration-report',
                'as' => 'expiration-report-'
            ], function () {
                Route::get('list', [FttxExpirationReportController::class, 'index'])->name('list');
                Route::get('get-detail', [FttxExpirationReportController::class, 'getDetail'])->name('get-detail');
            });
        });


        //report
        Route::group([
            'prefix' => 'report',
            'as' => 'report-'
        ], function () {

            //receivePayment
            Route::group([
                'prefix' => 'receive-payment',
                'as' => 'receive-payment-'
            ], function () {
                Route::get('list', [ReceivePaymentController::class, 'index'])->name('list');
            });

            //customer
            Route::group([
                'prefix' => 'customer',
                'as' => 'customer-'
            ], function () {
                Route::get('list', [CustomerReportController::class, 'index'])->name('list');
            });

            //customerInfo
            Route::group([
                'prefix' => 'customer-info',
                'as' => 'customer-info-'
            ], function () {
                Route::get('list', [CustomerInfoController::class, 'index'])->name('list');
            });

            //Old Customer
            Route::group([
                'prefix' => 'old-customer',
                'as' => 'old-customer-'
            ], function () {
                Route::get('list', [CustomerReportController::class, 'oldCustomerIndex'])->name('list');
                Route::post('import-excel', [CustomerReportController::class, 'oldCustomerImportExcel'])->name('import-excel');
            });

            //Customer DMC
            Route::group([
                'prefix' => 'customer-dmc',
                'as' => 'customer-dmc-'
            ], function () {
                Route::get('list', [CustomerReportController::class, 'customerDMC'])->name('list');
                Route::post('import-excel', [CustomerReportController::class, 'customerDMCImportExcel'])->name('import-excel');
                Route::post('edit', [CustomerReportController::class, 'editCustomer'])->name('edit');
                Route::delete('delete', [CustomerReportController::class, 'onDelete'])->name('delete');
                Route::get('get-by-id', [CustomerReportController::class, 'getCustomerDmcById'])->name('get-by-id');
            });

            //ARAcging
            Route::group([
                'prefix' => 'ar-acging',
                'as' => 'ar-acging-'
            ], function () {
                Route::get('list', [ARAcgingReportController::class, 'index'])->name('list');
            });

            //SaleJournal
            Route::group([
                'prefix' => 'sale-journal',
                'as' => 'sale-journal-'
            ], function () {
                Route::get('list', [SaleJournalReportController::class, 'index'])->name('list');
            });

            //Income
            Route::group([
                'prefix' => 'income',
                'as' => 'income-'
            ], function () {
                Route::get('list', [IncomeReportController::class, 'index'])->name('list');
            });

            //Revenue
            Route::group([
                'prefix' => 'revenue',
                'as' => 'revenue-'
            ], function () {
                Route::get('list', [RevenueReportController::class, 'index'])->name('list');
                Route::get('fetchData', [RevenueReportController::class, 'fetchData'])->name('fetchData');
                Route::get('excel', [RevenueReportController::class, 'excel'])->name('excel');
            });

            //summary invoice
            Route::group([
                'prefix' => 'summary-invoice',
                'as' => 'summary-invoice-'
            ], function () {
                Route::get('{type}/list', [SummaryInvoiceController::class, 'index'])->name('list');
            });

            //Summary Annual Report
            Route::group([
                'prefix' => 'annual-report',
                'as' => 'annual-report-'
            ], function () {
                Route::get('list/invoice-receipt', [SummaryAnnualReportController::class, 'invoiceReceiptIndex'])->name('invoice-receipt-list');
                Route::get('list/invoice-detail', [SummaryAnnualReportController::class, 'invoiceDetailIndex'])->name('invoice-detail-list');
            });
        });
        //document management
        Route::group([
            'prefix' => 'document',
            'as' => 'document-'
        ], function () {
            Route::get('list-pac-document', [DocumentController::class, 'indexPac'])->name('list');
        });

        //File Manager
        Route::prefix('file-manager')
            ->name('file-manager-')
            ->group(function () {
                // Route::get('/index', [FileManager::class, 'index'])->name('index');
                Route::get('/first', [FileManager::class, 'first'])->name('first');
                Route::get('/files', [FileManager::class, 'getFiles'])->name('files');
                Route::get('/folders', [FileManager::class, 'getFolders'])->name('folders');
                Route::post('/upload', [FileManager::class, 'uploadFile'])->name('upload');
                Route::post('/rename-file', [FileManager::class, 'renameFile'])->name('rename-file');
                Route::delete('/delete-file', [FileManager::class, 'deleteFile'])->name('delete-file');

                //folder
                Route::post('/create-folder', [FileManager::class, 'createFolder'])->name('create-folder');
                Route::post('/rename-folder', [FileManager::class, 'renameFolder'])->name('rename-folder');
                Route::delete('/delete-folder', [FileManager::class, 'deleteFolder'])->name('delete-folder');

                //trash bin
                Route::delete('/delete-all', [FileManager::class, 'deleteAll'])->name('delete-all');
                Route::put('/restore-all', [FileManager::class, 'restoreAll'])->name('restore-all');
            });

        //document management
        Route::group([
            'prefix' => 'document',
            'as' => 'document-'
        ], function () {

            // pac document
            Route::get('/document-pac', [DocumentController::class, 'indexPac'])->name('list-pac');
            Route::get('/first-pac', [DocumentController::class, 'firstPac'])->name('first-pac');
            Route::get('/files-pac', [DocumentController::class, 'getFilesPac'])->name('files-pac');
            Route::get('/folders-pac', [DocumentController::class, 'getFoldersPac'])->name('folders-pac');
            Route::post('/upload-pac', [DocumentController::class, 'uploadFilePac'])->name('upload-pac');
            Route::delete('/delete-file-pac', [DocumentController::class, 'deleteFilePac'])->name('delete-file-pac');
            // folder-------
            Route::post('/create-folder-pac', [DocumentController::class, 'createFolderPac'])->name('create-folder-pac');
            Route::post('/rename-folder-pac', [DocumentController::class, 'renameFolderPac'])->name('rename-folder-pac');
            Route::delete('/delete-folder-pac', [DocumentController::class, 'deleteFolderPac'])->name('delete-folder-pac');

            //trash bin----
            Route::delete('/delete-all-pac', [DocumentController::class, 'deleteAllPac'])->name('delete-all-pac');
            Route::put('/restore-all-pac', [DocumentController::class, 'restoreAllPac'])->name('restore-all-pac');

            // Invoice document

            Route::get('/document-invoice', [DocumentController::class, 'indexInvoice'])->name('list-invoice');
            Route::get('/first-invoice', [DocumentController::class, 'firstInvoice'])->name('first-invoice');
            Route::get('/files-invoice', [DocumentController::class, 'getFilesInvoice'])->name('files-invoice');
            Route::get('/folders-invoice', [DocumentController::class, 'getFoldersInvoice'])->name('folders-invoice');
            Route::post('/upload-invoice', [DocumentController::class, 'uploadFileInvoice'])->name('upload-invoice');
            Route::delete('/delete-file-invoice', [DocumentController::class, 'deleteFileInvoice'])->name('delete-file-invoice');
            // folder-------
            Route::post('/create-folder-invoice', [DocumentController::class, 'createFolderInvoice'])->name('create-folder-invoice');
            Route::post('/rename-folder-invoice', [DocumentController::class, 'renameFolderInvoice'])->name('rename-folder-invoice');
            Route::delete('/delete-folder-invoice', [DocumentController::class, 'deleteFolderInvoice'])->name('delete-folder-invoice');

            //trash bin----
            Route::delete('/delete-all-invoice', [DocumentController::class, 'deleteAllInvoice'])->name('delete-all-invoice');
            Route::put('/restore-all-invoice', [DocumentController::class, 'restoreAllInvoice'])->name('restore-all-invoice');

            // receipt document

            Route::get('/document-receipt', [DocumentController::class, 'indexReceipt'])->name('list-receipt');
            Route::get('/first-receipt', [DocumentController::class, 'firstReceipt'])->name('first-receipt');
            Route::get('/files-receipt', [DocumentController::class, 'getFilesReceipt'])->name('files-receipt');
            Route::get('/folders-receipt', [DocumentController::class, 'getFoldersReceipt'])->name('folders-receipt');
            Route::post('/upload-receipt', [DocumentController::class, 'uploadFileReceipt'])->name('upload-receipt');
            Route::delete('/delete-file-receipt', [DocumentController::class, 'deleteFileReceipt'])->name('delete-file-receipt');
            // folder-------
            Route::post('/create-folder-receipt', [DocumentController::class, 'createFolderReceipt'])->name('create-folder-receipt');
            Route::post('/rename-folder-receipt', [DocumentController::class, 'renameFolderReceipt'])->name('rename-folder-receipt');
            Route::delete('/delete-folder-receipt', [DocumentController::class, 'deleteFolderReceipt'])->name('delete-folder-receipt');

            //trash bin----
            Route::delete('/delete-all-receipt', [DocumentController::class, 'deleteAllReceipt'])->name('delete-all-receipt');
            Route::put('/restore-all-receipt', [DocumentController::class, 'restoreAllReceipt'])->name('restore-all-receipt');


            // Contract document 

            Route::get('/document-contract', [DocumentController::class, 'indexContract'])->name('list-contract');
            Route::get('/first-contract', [DocumentController::class, 'firstContract'])->name('first-contract');
            Route::get('/files-contract', [DocumentController::class, 'getFilesContract'])->name('files-contract');
            Route::get('/folders-contract', [DocumentController::class, 'getFoldersContract'])->name('folders-contract');
            Route::post('/upload-contract', [DocumentController::class, 'uploadFileContract'])->name('upload-contract');
            Route::delete('/delete-file-contract', [DocumentController::class, 'deleteFileContract'])->name('delete-file-contract');
            // folder-------
            Route::post('/create-folder-contract', [DocumentController::class, 'createFolderContract'])->name('create-folder-contract');
            Route::post('/rename-folder-contract', [DocumentController::class, 'renameFolderContract'])->name('rename-folder-contract');
            Route::delete('/delete-folder-contract', [DocumentController::class, 'deleteFolderContract'])->name('delete-folder-contract');

            //trash bin----
            Route::delete('/delete-all-contract', [DocumentController::class, 'deleteAllContract'])->name('delete-all-contract');
            Route::put('/restore-all-contract', [DocumentController::class, 'restoreAllContract'])->name('restore-all-contract');
        });
        // Route::group(['prefix' => 'orders', 'as' => 'order-'], function () {
        //     Route::get('/', [Admin\OrderController::class, 'index'])->name('list');
        //     Route::get('/detail/{id}', [Admin\OrderController::class, 'detail'])->name('detail');
        //     Route::match(['get', 'post'], '/update-status', [Admin\OrderController::class, 'onUpdateStatus'])->name('update-status');
        // });

        //Select
        Route::group([
            'prefix' => 'select',
            'as'     => 'select-'
        ], function () {
            Route::get('customer', [Admin\SelectController::class, 'selectCustomer'])->name('customer');
            Route::get('project', [Admin\SelectController::class, 'selectProject'])->name('project');
            Route::get('type', [Admin\SelectController::class, 'selectType'])->name('type');
            Route::get('invoice', [Admin\SelectController::class, 'selectInvoice'])->name('invoice');
            Route::get('credit-note', [Admin\SelectController::class, 'selectCreditNote'])->name('credit-note');
            Route::get('work-order-invoice', [Admin\SelectController::class, 'selectWorkOrderInvoice'])->name('work-order-invoice');
            Route::get('work-order-credit-note', [Admin\SelectController::class, 'selectWorkOrderCreditNote'])->name('work-order-credit-note');
            Route::get('pac', [Admin\SelectController::class, 'selectPAC'])->name('pac');
            Route::get('get-fttx/{id?}', [Admin\SelectController::class, 'selectFttx'])->name('fttx');
            Route::get('get-standard-price-fttx', [Admin\SelectController::class, 'selectStandardPriceFttx'])->name('get-standard-price-fttx');
        });

        // Page
        Route::group([
            'prefix' => 'page',
            'as' => 'page-',
        ], function () {
            Route::get('/{type?}', [Admin\PageController::class, 'page'])->name('page');
            Route::post('save/{id?}', [Admin\PageController::class, 'onSave'])->name('save');
        });

        /////Contact
        Route::group([
            'prefix' => 'contact',
            'as' => 'contact-',
        ], function () {
            Route::get('/{type?}', [Admin\ContactController::class, 'index'])->name('contact');
            Route::post('save/{id?}', [Admin\ContactController::class, 'store'])->name('save');
        });

        //Rate setting
        Route::group([
            'prefix' => 'rate',
            'as' => 'rate-',
        ], function () {
            Route::get('/', [Admin\RateController::class, 'index'])->name('rate');
            Route::post('save/{id?}', [Admin\RateController::class, 'store'])->name('save');
        });

        //Lock Logo setting
        Route::group([
            'prefix' => 'logo-control',
            'as' => 'logo-control-',
        ], function () {
            Route::get('/', [Admin\LogoControlController::class, 'index'])->name('rate');
            Route::post('save/{id?}', [Admin\LogoControlController::class, 'store'])->name('save');
        });

        //License fee setting
        Route::group([
            'prefix' => 'license-fee',
            'as' => 'license-fee-',
        ], function () {
            Route::get('/list/{status}', [Admin\LicenseFeeController::class, 'index'])->name('list');
            Route::get('/create', [Admin\LicenseFeeController::class, 'onCreate'])->name('create');
            Route::post('/save', [Admin\LicenseFeeController::class, 'onSave'])->name('save');
            Route::get('/edit/{id}', [Admin\LicenseFeeController::class, 'onEdit'])->name('edit');
            Route::post('/update/{id}', [Admin\LicenseFeeController::class, 'onUpdate'])->name('update');
            Route::match(['get', 'post'], 'status/{id}/{status}', [Admin\LicenseFeeController::class, 'onUpdateStatus'])->name('status');
        });
        //CloseDate
        Route::group([
            'prefix' => 'close-date',
            'as' => 'close-date-'
        ], function () {
            Route::get('list/{status?}', [CloseDateController::class, 'index'])->name('list');
            Route::get('create', [CloseDateController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [CloseDateController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [CloseDateController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [CloseDateController::class, 'onUpdateStatus'])->name('status');
        });

        //DMC_file_manager
        Route::group([
            'prefix' => 'dmc-file-manager',
            'as'     => 'dmc-file-manager-'
        ], function () {
            Route::get('list/{id?}', [DMCFileManagerController::class, 'index'])->name('page');
            Route::get('fetchData', [DMCFileManagerController::class, 'fetchData'])->name('fetchData-page');
            Route::get('year', [DMCFileManagerController::class, 'fetchDataYear'])->name('year');
            Route::get('year-of-month', [DMCFileManagerController::class, 'fetchDataYearOfMonth'])->name('year-of-month');

            Route::get('download', [DMCFileManagerController::class, 'downloadFile'])->name('download-file');
        });

        //Upload
        Route::group([
            'prefix' => 'upload',
            'as' => 'upload-'
        ], function () {
            Route::get('/view', [UploadController::class, 'index'])->name('view');
            Route::post('/save', [UploadController::class, 'save'])->name('save');
            Route::get('/view-detail/{id}', [UploadController::class, 'viewDetail'])->name('detail');
        });

        //Auth
        Route::group([
            'prefix' => 'auth',
            'as' => 'auth-'
        ], function () {
            Route::post('/save', [AuthController::class, 'save'])->name('save');
            Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
        });

        //FTTH Service
        Route::prefix('ftth-service')->name('ftth-service-')->controller(Admin\FTTHServiceController::class)->group(function () {
            Route::get('list/{status?}', 'index')->name('list');
            Route::get('create/{id?}', 'create')->name('create');
            Route::post('store/{id?}', 'store')->name('store');
            Route::match(['get', 'post'], 'status/{id}/{status}', 'updateStatus')->name('status');
            Route::get('delete/{id?}', 'delete')->name('delete');
            Route::get('destroy/{id?}', 'destroy')->name('destroy');
            Route::get('restore/{id?}', 'restore')->name('restore');
        });

        //bank account
        Route::group([
            'prefix' => 'bank-account',
            'as' => 'bank-account-'
        ], function () {
            Route::get('list/{status?}', [BankAccountController::class, 'index'])->name('list');
            Route::get('create', [BankAccountController::class, 'onCreate'])->name('create');
            Route::get('edit/{id?}', [BankAccountController::class, 'onEdit'])->name('edit');
            Route::post('save/{id?}', [BankAccountController::class, 'onSave'])->name('save');
            Route::match(['get', 'post'], 'status/{id}/{status}', [BankAccountController::class, 'onUpdateStatus'])->name('status');
        });
    });
Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return "Cache is cleared";
});

Route::get('optimize', function () {
    Artisan::call('optimize:clear');
    return "Cache is cleared optimize";
});

Route::get('clear-optimize', function () {
    Artisan::call('optimize:clear');
    return redirect()->back();
});
