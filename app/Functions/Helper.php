<?php

use App\Models\CloseDate;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Gate;

function customUrl($url, $queryParam)
{
    $pattern = "/\?/i";
    $query = "";
    $i = 0;
    $parseUrl = parse_url($url, PHP_URL_QUERY);
    parse_str($parseUrl, $params);
    foreach (collect($queryParam) as $key => $value) {
        if (!isset($params[$key])) {
            if ($i == 0) {
                $hasQuery = preg_match($pattern, $url);
                if ($hasQuery < 1) {
                    $query .= '?' . $key . '=' . $value;
                } else {
                    $query .= '&' . $key . '=' . $value;
                }
            } else {
                $query .= '&' . $key . '=' . $value;
            }
        }
        $i++;
    }
    return $url ? $url . $query : '';
}

function routeActive(string $route)
{
    $arr = explode(',', $route);
    foreach ($arr as $item) {
        if (request()->is($item)) {
            return true;
        }
    }
    return false;
}

function resData($data = null)
{
    return response()->json([
        'data' => $data,
        'message' => 'fetch_data_success',
        'error' => false,
    ], 200);
}

function resSuccess($message, $data = null, array $options = [])
{
    return response()->json([
        'message' => $message,
        'error' => false,
        'data' => $data,
        ...$options,
    ], 200);
}

function resFail($message, Exception $exception = null)
{
    $response = [
        'message' => $message,
        'error' => true,
    ];
    if ($exception) {
        $response['exception'] = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
    }
    return response()->json($response, 202);
}

function resValidate(MessageBag $message)
{
    $response = [
        'validate' => $message,
        'error' => true,
    ];
    return response()->json($response, 422);
}
function CheckRole($field)
{
    return Gate::check($field) ? true : false;
}
function createFormat($date, $format = null)
{
    if ($date == "currentDate") {
        return Carbon::now()->format($format);
    }
    return $date->format('Y-m-d');
}
function dateInActive($date = null)
{
    $dateFormat = Carbon::parse($date)->format('Y-m-d');
    $currentDate = Carbon::now()->format('Y-m-d');
    if ($dateFormat == $currentDate) {
        return true;
    }
    return false;
}

function checkValidate($date)
{
    $getDateFormat = (Carbon::parse($date)->format('Y-m')) . '-01';
    $dataValid = CloseDate::whereDate('date', $getDateFormat)->where('status', 1)->first();
    if (isset($dataValid) && $dataValid) {
        return false;
    }
    return true;
}
function formatDate($date, $format = null)
{
    if (!$format) {
        return [
            Carbon::parse($date)->isoFormat('DD MMM, Y'),
            Carbon::parse($date)->isoFormat('hh:mm a'),
        ];
    } else {
        return Carbon::parse($date ?? Carbon::now())->isoFormat($format);
    }
}

function getNumberOfMonth($startDate, $endDate)
{
    if ($startDate && $endDate && $endDate >= $startDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $diffInMonths = ($end->format('Y') - $start->format('Y')) * 12 + ($end->format('m') - $start->format('m'));
        $adjustedStart = (clone $start)->modify("+$diffInMonths months");
        $diffInDays = $end->diff($adjustedStart)->days;
        $total = $diffInMonths + ($diffInDays / 30);
        return number_format((round($total, 2)), 2);
    }

    return 0;
}

function getDaysBetweenDates($date1, $date2)
{
    if ($date1 && $date2 && $date2 >= $date1) {
        $startDate = new DateTime($date1);
        $endDate = new DateTime($date2);

        $timeDifference = $endDate->diff($startDate)->days;

        return $timeDifference;
    }

    return 0;
}

function calculateDaysBetween($date)
{
    if ($date) {
        $today = new DateTime();
        $inputDate = new DateTime($date);
        $differenceInTime = $inputDate->diff($today);
        $differenceInDays = $differenceInTime->invert ? $differenceInTime->days : -$differenceInTime->days;

        return $differenceInDays;
    }
}

function addMonth($date, $number) {
    if (!is_numeric($number)) {
        return "Invalid input. The number of months must be numeric.";
    }
    try {
        $dateObject = new DateTime($date);
        $dateObject->modify("+$number months");
        return $dateObject->format('Y-m-d'); 
    } catch (Exception $e) {
        return "Invalid date format: " . $e->getMessage();
    }
}

function isTodayBetweenDateAndNextMonth($date) {
    $startDate = strtotime($date);
    $endDate = strtotime("+1 month", $startDate);
    $today = strtotime(date('Y-m-d'));

    return ($today >= $startDate) && ($today <= $endDate);
}


