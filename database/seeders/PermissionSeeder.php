<?php

namespace Database\Seeders;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public $index = 0;
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ModulePermission::truncate();
        Permission::truncate();
        Schema::enableForeignKeyConstraints();

        $view = "View";
        $create = "Create";
        $edit = "Edit";
        $delete = "Delete";
        $trash = "Trash";
        $destroy = "Destroy";
        $detail = "Detail";
        $report = "Report";
        $excel_export = "Export to excel";
        $void = "Void";
        $copy = "Copy";
        $print = "Print";
        $change_password = "Change password";
        $permission = "Permission";
        $create_invoice = "Create invoice";
        $create_receipt = "Create receipt";
        $upload_file = "Upload file";
        $dmc_submit = "DMC submit";

        $stDashboard = $this->increaseIndex();
        $stType = $this->increaseIndex();
        $stService = $this->increaseIndex();
        $stProject = $this->increaseIndex();
        $stCustomer = $this->increaseIndex();
        $stPo = $this->increaseIndex();
        $stPurchase = $this->increaseIndex();
        $stInvoice = $this->increaseIndex();
        $stSummaryInvoice = $this->increaseIndex();
        $stCreditNote = $this->increaseIndex();
        $stReceipt = $this->increaseIndex();
        $stFtthService = $this->increaseIndex();
        $stWorkOrder = $this->increaseIndex();
        $stdDmcfileManager = $this->increaseIndex();
        $stCFOCNReport = $this->increaseIndex();
        $stTaxReport = $this->increaseIndex();
        $stMPTCReport = $this->increaseIndex();
        $stSummaryAnnualReport = $this->increaseIndex();
        $stDocument = $this->increaseIndex();
        $stUser = $this->increaseIndex();
        $stSetting = $this->increaseIndex();
        $stFttx = $this->increaseIndex();
        $stFttxCustomerType = $this->increaseIndex();
        $stFttxPosSpeed = $this->increaseIndex();
        $stFttxSettingPrice = $this->increaseIndex();
        $stFttxAnnualReport = $this->increaseIndex();
        $stFttxExpirationReport = $this->increaseIndex();
     

        //dashboard
        $dashboard = ModulePermission::create([
            'name' => 'Dashboard',
            'parent_id' => $stDashboard,
            'sort_no' => $stDashboard,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'dashboard-view'),
            ],
            $dashboard
        );

        //service type
        $type = ModulePermission::create([
            'name' => 'Service Type',
            'parent_id' => $stType,
            'sort_no' => $stType,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'service-type-view'),
                $this->objectData($create, 'service-type-create'),
                $this->objectData($edit, 'service-type-update'),
            ],
            $type
        );

        //service
        $service = ModulePermission::create([
            'name' => 'Service',
            'parent_id' => $stService,
            'sort_no' => $stService,

        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'service-view'),
                $this->objectData($create, 'service-create'),
                $this->objectData($edit, 'service-update'),
            ],
            $service
        );

        //Project 
        $project = ModulePermission::create([
            'name' => 'Project',
            'parent_id' => $stProject,
            'sort_no' => $stProject,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'project-view'),
                $this->objectData($create, 'project-create'),
                $this->objectData($edit, 'project-update')
            ],
            $project
        );

        //Customer
        $customer = ModulePermission::create([
            'name' => 'Customer',
            'parent_id' => $stCustomer,
            'sort_no' => $stCustomer,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'customer-view'),
                $this->objectData($create, 'customer-create'),
                $this->objectData($edit, 'customer-update'),
                $this->objectData($excel_export, 'customer-excel-export'),
                $this->objectData($upload_file, 'customer-upload-file')
            ],
            $customer
        );

         //po
         $po = ModulePermission::create([
            'name' => 'Po',
            'parent_id' => $stPo,
            'sort_no' => $stPo,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'purchase-order-view'),
                $this->objectData($create, 'purchase-order-create'),
                $this->objectData($edit, 'purchase-order-update'),
                $this->objectData($delete, 'purchase-order-delete'),
                $this->objectData($upload_file, 'purchase-order-upload-file'),
                $this->objectData('Create PAC', 'purchase-order-create-pac')
            ],
            $po
        );
        //PAC
        $purchase = ModulePermission::create([
            'name' => 'PAC',
            'parent_id' => $stPurchase,
            'sort_no' => $stPurchase,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'purchase-view'),
                $this->objectData($create, 'purchase-create'),
                $this->objectData($edit, 'purchase-update'),
                $this->objectData($create_invoice, 'purchase-create-invoice'),
                $this->objectData($upload_file, 'purchase-upload-file')
            ],
            $purchase
        );

        //invoice 
        $invoice = ModulePermission::create([
            'name' => 'Invoice',
            'parent_id' => $stInvoice,
            'sort_no' => $stInvoice,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'invoice-view'),
                $this->objectData($create, 'invoice-create'),
                $this->objectData($edit, 'invoice-update'),
                $this->objectData($void, 'invoice-void'),
                $this->objectData($detail, 'invoice-detail'),
                $this->objectData($create_receipt, 'invoice-create-receipt'),
                $this->objectData($copy, 'invoice-copy'),
                $this->objectData($dmc_submit, 'invoice-dmc-submit')
            ],
            $invoice
        );

        //Summary Invoice
        $summaryInvoice = ModulePermission::create([
            'name' => 'Summary Invoice',
            'parent_id' => $stSummaryInvoice,
            'sort_no' => $stSummaryInvoice,
        ]);
        $this->createPermission(
            [
                $this->objectData('Infra Invoice', 'infra-view'),
                $this->objectData('Submarine Invoice', 'submarine-view'),
            ],
            $summaryInvoice
        );

        //credit note
        $creditNote = ModulePermission::create([
            'name' => 'Credit Note',
            'parent_id' =>  $stCreditNote,
            'sort_no' =>  $stCreditNote,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'credit-note-view'),
                $this->objectData($create, 'credit-note-create'),
                $this->objectData($edit, 'credit-note-update'),
                $this->objectData($detail, 'credit-note-detail'),
                $this->objectData($dmc_submit, 'credit-note-dmc-submit')
            ],
            $creditNote
        );

        //Receipt
        $receipt = ModulePermission::create([
            'name' => 'Receipt',
            'parent_id' => $stReceipt,
            'sort_no' => $stReceipt,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'receipt-view'),
                $this->objectData($create, 'receipt-create'),
                $this->objectData($edit, 'receipt-update'),
                $this->objectData($detail, 'receipt-detail'),
                $this->objectData($delete, 'receipt-delete'),
            ],
            $receipt
        );

        //FTTH Service
        $ftthService  = ModulePermission::create([
            'name' => 'FTTH Service',
            'parent_id' => $stFtthService,
            'sort_no' => $stFtthService,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'ftth-service-view'),
                $this->objectData($create, 'ftth-service-create'),
                $this->objectData($edit, 'ftth-service-update'),
                $this->objectData($delete, 'ftth-service-delete'),
            ],
            $ftthService
        );

        //Work Order
        $workOrder = ModulePermission::create([
            'name' => 'Work Order',
            'parent_id' => $stWorkOrder,
            'sort_no' => $stWorkOrder,
        ]);
        $this->createPermission(
            [
                $this->objectData('Order', 'work-order-order'),
                $this->objectData('Invoice', 'work-order-invoice'),
                $this->objectData('Credit Note', 'work-order-credit-note'),
                $this->objectData('Receipt', 'work-order-receipt'),
            ],
            $workOrder
        );

        //DMC FileManager
        $dmcFileManager = ModulePermission::create([
            'name' => 'DMC File Manager',
            'parent_id' => $stdDmcfileManager,
            'sort_no' => $stdDmcfileManager,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'dmc-file-manager-view'),
            ],
            $dmcFileManager
        );


        //CFOCN Report
        $reportCFOCN = ModulePermission::create([
            'name' => 'CFOCN Report',
            'parent_id' => $stCFOCNReport,
            'sort_no' => $stCFOCNReport,
        ]);
        $this->createPermission(
            [
                $this->objectData('Invoice', 'report-invoice-view'),
                $this->objectData('Receive Payment', 'report-receive-payment-view'),
                $this->objectData('A/R By Acging', 'report-ar-acging-view'),
                $this->objectData('A/R By Project', 'report-ar-project-view'),
                $this->objectData('Customer Info', 'report-cfocn-customer-view'),
                $this->objectData('Revenue', 'report-revenue-view'),
                $this->objectData('Income', 'report-income-view'),
            ],
            $reportCFOCN
        );

        //Tax Report
        $reportTax = ModulePermission::create([
            'name' => 'Tax Report',
            'parent_id' => $stTaxReport,
            'sort_no' => $stTaxReport,
        ]);
        $this->createPermission(
            [
                $this->objectData('Sale Journal', 'report-sale-journal-view'),
            ],
            $reportTax
        );

        //MPTC Report
        $reportMPTC = ModulePermission::create([
            'name' => 'MPTC Report',
            'parent_id' => $stMPTCReport,
            'sort_no' => $stMPTCReport,
        ]);
        $this->createPermission(
            [
                $this->objectData('Licence Fee', 'report-licence-fee-view'),
                $this->objectData('Customer Info', 'report-customer-view'),
            ],
            $reportMPTC
        );

        //Summary Annual Report
        $summaryAnnualReport = ModulePermission::create([
            'name' => 'Summary Annual Report',
            'parent_id' =>  $stSummaryAnnualReport,
            'sort_no' =>  $stSummaryAnnualReport,
        ]);
        $this->createPermission(
            [
                $this->objectData('Invoice & Receipt Summary', 'invoice-receipt-report'),
                $this->objectData('Invoice Detail', 'invoice-detail-report'),
            ],
            $summaryAnnualReport
        );

        //document
        $document = ModulePermission::create([
            'name' => 'Document',
            'parent_id' =>   $stDocument,
            'sort_no' =>   $stDocument,
        ]);
        $this->createPermission(
            [
                $this->objectData('PAC', 'document-pac-view'),
                $this->objectData('Invoice', 'document-invoice-view'),
                $this->objectData('Receipt', 'document-receipt-view'),
                $this->objectData('Contract', 'document-contract-view')
            ],
            $document
        );

        //User Management
        $user = ModulePermission::create([
            'name' => 'User Management',
            'parent_id' => $stUser,
            'sort_no' => $stUser,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'user-view'),
                $this->objectData($create, 'user-create'),
                $this->objectData($edit, 'user-update'),
                $this->objectData($change_password, 'user-change-password'),
                $this->objectData($permission, 'user-permission')
            ],
            $user
        );

        //Setting
        $setting = ModulePermission::create([
            'name' => 'Setting',
            'parent_id' => $stSetting,
            'sort_no' => $stSetting,
        ]);
        $this->createPermission(
            [
                $this->objectData('Close Date', 'close-date'),
                $this->objectData('Exchange rate', 'exchange-rate'),
                $this->objectData('License fee', 'license-fee'),
                $this->objectData('Bank Account', 'bank-account'),
                $this->objectData('Lock Logo', 'logo-control'),
            ],
            $setting
        );

        //fttx 
        $fttx = ModulePermission::create([
            'name' => 'Fttx',
            'parent_id' =>  $stFttx,
            'sort_no' =>  $stFttx,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-view'),
                $this->objectData($create, 'fttx-create'),
                $this->objectData($edit, 'fttx-update'),
                $this->objectData($delete, 'fttx-delete'),
                $this->objectData('Renewal', 'fttx-renewal'),
                $this->objectData('Report detail', 'fttx-report-detail')
            ],
            $fttx
        );
        //fttx customer type
        $fttxCustomerType = ModulePermission::create([
            'name' => 'Fttx Customer Type',
            'parent_id' =>  $stFttxCustomerType,
            'sort_no' =>  $stFttxCustomerType,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-customer-type-view'),
                $this->objectData($create, 'fttx-customer-type-create'),
                $this->objectData($edit, 'fttx-customer-type-update'),
            ],
            $fttxCustomerType
        );

        //fttx pos speed
        $fttxPosSpeed = ModulePermission::create([
            'name' => 'Fttx Pos Speed',
            'parent_id' =>  $stFttxPosSpeed,
            'sort_no' =>  $stFttxPosSpeed,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-pos-speed-view'),
                $this->objectData($create, 'fttx-pos-speed-create'),
                $this->objectData($edit, 'fttx-pos-speed-update'),
            ],
            $fttxPosSpeed
        );

        //fttx setting price
        $fttxSettingPrice = ModulePermission::create([
            'name' => 'Fttx Setting Price',
            'parent_id' =>  $stFttxSettingPrice,
            'sort_no' =>  $stFttxSettingPrice,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-setting-price-view'),
                $this->objectData($create, 'fttx-setting-price-create'),
                $this->objectData($edit, 'fttx-setting-price-update'),
            ],
            $fttxSettingPrice
        );

          //fttx customer price
          $fttxSettingPrice = ModulePermission::create([
            'name' => 'Fttx Customer Price',
            'parent_id' =>  $stFttxSettingPrice,
            'sort_no' =>  $stFttxSettingPrice,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-customer-price-view'),
                $this->objectData($create, 'fttx-customer-price-create'),
                $this->objectData($edit, 'fttx-customer-price-update'),
            ],
            $fttxSettingPrice
        );


        //fttx anual report
        $fttxAnnualReport = ModulePermission::create([
            'name' => 'Fttx Annual Report',
            'parent_id' =>  $stFttxAnnualReport,
            'sort_no' =>  $stFttxAnnualReport,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-annual-report-view'),
            ],
            $fttxAnnualReport
        );

            //fttx expiration report
        $fttxAnnualReport = ModulePermission::create([
            'name' => 'Fttx Expiration Report',
            'parent_id' =>  $stFttxExpirationReport,
            'sort_no' =>  $stFttxExpirationReport,
        ]);
        $this->createPermission(
            [
                $this->objectData($view, 'fttx-expiration-report-view'),
            ],
            $fttxAnnualReport
        );
    }

    public function objectData($display_name, $name_key)
    {
        return (object) [
            'display_name' => $display_name,
            'name'  => $name_key
        ];
    }

    public function createPermission($arrayObject, $data)
    {
        $item = [];
        foreach ($arrayObject as $val) {
            $item[] = [
                'display_name' => $val->display_name,
                'name' => $val->name,
                'guard_name' => 'web',
                'module_id' => $data->id,
            ];
        }
        Permission::insert($item);
    }

    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
