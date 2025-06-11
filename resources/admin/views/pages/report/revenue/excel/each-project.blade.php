<table>
    @php
        $arrySaleLease = array_merge($data['lease'], $data['sale']);
        $saleLeaseInProject = [];
        $otherInProject = [];
        $creditNoteInProject=[];
        $jan = null;  $janOther = null; $janCredit=null;
        $feb = null;  $febOther= null; $febCredit=null;
        $mar = null;  $marOther = null;$marCredit=null;
        $apr = null;  $aprOther = null; $aprCredit=null;
        $may = null;  $mayOther = null; $mayCredit=null;
        $jun = null;  $junOther = null; $junCredit=null;
        $jul = null;  $julOther = null; $julCredit=null;
        $aug = null;  $augOther = null; $augCredit=null;
        $sep = null;  $sepOther = null; $sepCredit=null;
        $oct = null;  $octOther = null; $octCredit=null;
        $nov = null;  $novOther = null; $novCredit=null;
        $dec = null;  $decOther = null; $decCredit=null;
        foreach ($arrySaleLease as $invoice) {
            if ($invoice?->purchase?->project_id == $data['project']->id) {
                array_push($saleLeaseInProject, $invoice);
            }
        }
        foreach ($data['service'] as $invoice) {
            if ($invoice?->purchase?->project_id == $data['project']->id) {
                array_push( $otherInProject, $invoice);
            }
        }
        foreach ($data['creditNote'] as $invoice) {
            if ($invoice?->purchase?->project_id == $data['project']->id) {
                array_push($creditNoteInProject, $invoice);
            }
        }
        foreach ($saleLeaseInProject as $totalMonthYear) {
            if (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-01') {
                $jan += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-02') {
                $feb += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-03') {
                $mar += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-04') {
                $apr += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-05') {
                $may += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-06') {
                $jun += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-07') {
                $jul += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-08') {
                $aug += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-09') {
                $sep += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-10') {
                $oct += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-11') {
                $nov += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-12') {
                $dec += $totalMonthYear->total_grand;
            }
        }

        foreach ($otherInProject as $totalMonthYear) {
            if (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-01') {
                $janOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-02') {
                $febOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-03') {
                $marOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-04') {
                $aprOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-05') {
                $mayOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-06') {
                $junOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-07') {
                $julOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-08') {
                $augOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-09') {
                $sepOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-10') {
                $octOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-11') {
                $novOther += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-12') {
                $decOther += $totalMonthYear->total_grand;
            }
        }
        foreach ($creditNoteInProject as $totalMonthYear) {
            if (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-01') {
                $janCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-02') {
                $febCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-03') {
                $marCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-04') {
                $aprCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-05') {
                $mayCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-06') {
                $junCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-07') {
                $julCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-08') {
                $augCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-09') {
                $sepCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-10') {
                $octCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-11') {
                $novCredit += $totalMonthYear->total_grand;
            } elseif (date_format(date_create($totalMonthYear->issue_date), 'Y-m') == $data['year']?$data['year']:now()->year . '-12') {
                $decCredit += $totalMonthYear->total_grand;
            }
        }
    @endphp
    <thead>
        <tr>
            <th style="vertical-align:middle; text-align:center;" colspan="16">(Cambodia) Fiber Optic Communication
                Network Co.,Ltd.</th>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center;" colspan="16">CFOCN-{{ $data['project']->name }} Cable
                Network Revenue Y2022</th>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:center;"></th>
            <th style="vertical-align:middle; text-align:right;" colspan="3">VAT Tin :
                {{ $data['project']->vat_tin }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="vertical-align:middle; text-align:center;" rowspan="2">#</td>
            <td style="vertical-align:middle; text-align:center;" rowspan="2">Type</td>
            <td style="vertical-align:middle; text-align:center;" rowspan="2">Category</td>
            <td style="vertical-align:middle; text-align:center;" colspan="12">(Self Declaration Report) Gross Revenue
                (USD)</td>
            <td style="vertical-align:middle; text-align:center;" rowspan="2">YTD {{ $data['year']?$data['year']:now()->year }}</td>
        </tr>
        <tr>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Jan-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Feb-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Mar-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Apr-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-May-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Jun-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Jul-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Aug-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Sep-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Oct-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Nov-01</td>
            <td style="vertical-align:middle; text-align:center;">{{ $data['year']?$data['year']:now()->year }}-Dec-01</td>
        </tr>
        <tr>

            <td style="vertical-align:middle; text-align:center;">1</td>
            <td style="vertical-align:middle; text-align:center;" rowspan="4">
                @if ($data['project']->id == 2)
                    Submarine Cable
                @else
                    Optical Cable Networks
                @endif
            </td>
            <td style="vertical-align:middle;">
                @if ($data['project']->id == 2)
                    Submarine Cable Charge
                @else
                    Optical Cable Networks Charge
                @endif
            </td>
            <td style="vertical-align:middle; text-align:right;">{{ $jan }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $feb }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $mar }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $apr }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $may }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $jun }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $jul }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $aug }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $sep }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $oct }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $nov }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $dec }}</td>
            <td style="vertical-align:middle; text-align:right;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle; text-align:center;">2</td>
            <td style="vertical-align:middle;">Other Charges </td>
            <td style="vertical-align:middle; text-align:right;">{{ $janOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $febOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $marOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $aprOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $mayOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $junOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $julOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $augOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $sepOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $octOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $novOther }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $decOther }}</td>
            <td style="vertical-align:middle; text-align:right;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle; text-align:center;">3</td>
            <td style="vertical-align:middle;">Credit Notes</td>
            <td style="vertical-align:middle; text-align:right;">{{ $janCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $febCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $marCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $aprCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $mayCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $junCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $julCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $augCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $sepCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $octCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $novCredit }}</td>
            <td style="vertical-align:middle; text-align:right;">{{ $decCredit }}</td>
            <td style="vertical-align:middle; text-align:right;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle; text-align:center;">4</td>
            <td style="vertical-align:middle;"> Debit Notes</td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle; text-align:right;" colspan="3"><b>Total Gross Revenue (USD) : </b> </td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle; text-align:right;"></td>
        </tr>
    </tbody>
    <tfoot>
        <tr></tr>
        <tr></tr>
        <tr>
            <td></td>
            <td><b>Noted</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>- Please provide gross revenue <span style="background-color:blue !important;">figures including
                    VAT,</span> SPT and without any cost adjustments</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>- Please list down details service/item which CFOCN included into each revenue category (e.g. duct
                leasing, Installation fee…..etc.) in {{ $data['project']->name }}_Items List worksheet.</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
