<?php

namespace App\Exports;
use App\Exports\revenueEachProjectReportExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class revenueReportExport implements WithMultipleSheets
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $data=$this->data;
        $allSheet=[new SubtotalLicienceReportExport($this->data)];
        
        foreach($this->data['data'] as $item){
        $data['project']=$item;
        array_push($allSheet,new revenueEachProjectReportExport($data));
        array_push($allSheet,new revenueItemListReportExport($data));
        }
        return $allSheet;
    }
}
