<?php

namespace App\Console\Commands;
use App\Services\InvoiceScheduleService;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Invoice extends Command
{
    public $invoiceScheduleService = null;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add invoices';

    public function __construct(InvoiceScheduleService $ser)
    {
        $this->invoiceScheduleService = $ser;
        parent::__construct();
       
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->invoiceScheduleService->createInvoiceByPeriodEnd();
        Log::info("generate schedule invoice"); 
        
    }
}