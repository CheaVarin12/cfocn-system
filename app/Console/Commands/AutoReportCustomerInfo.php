<?php

namespace App\Console\Commands;

use App\Services\ReportCustomerInfoService;
use Illuminate\Console\Command;

class AutoReportCustomerInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reportCustomerInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public $reportCustomerInfoService = null;
    public function __construct(ReportCustomerInfoService $ser)
    {
        $this->reportCustomerInfoService = $ser;
        parent::__construct();
    }
    public function handle()
    {
        $data['data'] = $this->reportCustomerInfoService->fetchData();
        $data['projectInExport'] =  $this->reportCustomerInfoService->dataGroupBy($data['data']);
        $data['projects'] = [];
        $this->reportCustomerInfoService->submitFile($data);
        // return Command::SUCCESS;
    }
}
