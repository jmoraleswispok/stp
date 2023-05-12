<?php

namespace App\Traits\Utilities;

use App\Interfaces\HttpCodeInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneralUtilities
{
    /**
     * @param Builder $model
     * @param $column
     * @return string
     * @throws Exception
     */
    public function getCode(Builder $model, $column): string
    {
        $exists = true;
        $activationCode = '';
        $counter = 1;
        while ($exists)
        {
            $activationCode = $this->randomNumber();
            $model::withTrashed()->where($column,$activationCode)->firstOr(function () use(&$exists) {
                $exists = false;
            });
            $counter++;
            if($counter == 10000) throw new Exception("", HttpCodeInterface::BAD_REQUEST);
        }
        return $activationCode;
    }

    /**
     * @param Model $model
     * @param $slug
     * @return string
     */
    public function getSlug(Model $model, $slug): string
    {
        $exists = true;
        $slug = Str::slug(mb_strtolower($slug),'-');
        $counter = 1;
        $random = 10;
        while ($exists)
        {
            $model::withTrashed()->where('slug', $slug)->firstOr(function () use(&$exists){
                $exists = false;
            });
            if ($exists)
            {
                $slug .= "-".mb_strtolower(Str::random($random));
                $slug = Str::slug(mb_strtolower($slug),'-');
                $counter++;
                if($counter == 10000) {
                    $random++;
                    $counter = 1;
                };
            }
        }
        return $slug;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->randomNumber(8).'_'.Carbon::now()->timestamp.'_'.$this->randomNumber(16).'_'.$this->randomNumber(32).'_'.Str::random(1);
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->generateRandomStringNumber();
    }

    /**
     * @param int $length
     * @return string
     */
    private function randomNumber(int $length = 6): string
    {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    /**
     * @param int $length
     * @return string
     */
    private function generateRandomStringNumber(int $length = 8): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateRandomStringUpper(int $length = 8): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
