<?php

namespace App\Utilities;

use App\Interfaces\HttpCodeInterface;
use App\Interfaces\ModelInterface;
use App\Traits\Utilities\GeneralUtilities;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ModelUtility
{
    use GeneralUtilities;

    /**
     * @param Builder $model
     * @param string $column
     * @param int $length
     * @return string
     * @throws Exception
     */
    public static function generateCode(Builder $model, string $column, int $length): string
    {
        return  (new ModelUtility)->getCode($model, $column, $length);
    }

    /**
     * @param Builder $model
     * @param string $prefix
     * @return string
     */
    public static function generateCodeWithPrefix(Builder $model, string $prefix): string
    {
        $result = $model::query()->whereNotNull('code')->get()->map(function ($item) {
            return preg_replace('/[^0-9]/', '', $item->code);
        })->last();
        if (empty($result)) {
            $code = 1;
        } else {
            $result = (int) $result;
            $code = $result + 1;
        }
        return $prefix.$code;
    }

    /**
     * @param $data
     * @return mixed|string
     */
    public static function nullSafeForString($data): mixed
    {
        return empty($data) ? "" : $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function nullSafeForNumeric($data): mixed
    {
        return empty($data) ? 0 : $data;
    }

    /**
     * @param Builder $model
     * @param Request $request
     * @return Builder
     */
    public static function filterStatus(Builder &$model, Request $request): Builder
    {
        switch ($request->input('status'))
        {
            case 2:
                $model->where('status', ModelInterface::ACTIVE);
                break;
            case 3:
                $model->where('status', ModelInterface::INACTIVE);
                break;
        }
        return $model;
    }

    /**
     * @param Builder $model
     * @param string|int $id
     * @param string $message
     * @return Builder|Model|mixed
     */
    public static function findRecord(Builder $model, string|int $id, string $message = 'Registro no encontrado.'): mixed
    {
        return $model->where(function ($query) use ($id){
            return is_numeric($id) ? $query->where('id', $id) : $query->where('uuid', $id);
        })->firstOr(function () use ($message) {
            throw new ModelNotFoundException($message, HttpCodeInterface::NOT_FOUND);
        });
    }

    /**
     * @param $number
     * @param int $decimal
     * @param string $symbol
     * @param bool $round
     * @return string
     */
    public static function numberFormat($number, int $decimal = 2, string $symbol = "", bool $round = false): string
    {
        $number = $round ? $number : ModelUtility::bcdiv($number, 1,$decimal);
        return $symbol.number_format($number, $decimal);
    }

    /**
     * @param $total
     * @param int $num2
     * @param int $scale
     * @return float
     */
    public static function bcdiv($total, int $num2 = 1, int $scale = 2): float
    {
        return floatval(bcdiv($total,$num2,$scale));
    }

    /**
     * @param $num1
     * @param $num2
     * @param int $scale
     * @return float
     */
    public static function bcsub($num1, $num2, int $scale = 20): float
    {
        return floatval(bcsub($num1,$num2,$scale));
    }

    /**
     * @param $amount
     * @return float
     */
    public static function removeCommas($amount): float
    {
        return floatval(str_replace(",", "", $amount));
    }

    /**
     * @param $year
     * @param $month
     * @param string $day
     * @return string
     */
    public static function getDateOfChange($year, $month, string $day = "01"): string
    {
        $now = Carbon::now();
        $consultationDate = Carbon::create($year, $month, $day);
        if ($now->year == $year && $now->month == $month) {
            $dateOfChange = $consultationDate->format('Y-m-d');
        } else {
            $dateOfChange = $consultationDate->lastOfMonth()->format('Y-m-d');
        }
        return $dateOfChange;
    }

    public static function formatMonth($month)
    {
        return ($month < 10 &&  strlen($month) < 2) ? "0{$month}" : $month;
    }

    public static function decrypt($value)
    {
        try
        {
            $valueDecrypt = decrypt($value);
        } catch(Exception $e) {
            $valueDecrypt = '';
        }
        return $valueDecrypt;
    }
}
