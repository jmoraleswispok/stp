<?php

namespace App\Traits\Utilities;

use App\Interfaces\ModelInterface;

trait ValidationTrait
{
    /**
     * @param string $table
     * @param string $column
     * @param string $extraColumn
     * @return string
     */
    private function existsActiveSoftDelete(string $table, string $column, string $extraColumn = ""): string
    {
        $active = ModelInterface::ACTIVE;
        return "exists:{$table},{$column},status,{$active},deleted_at,NULL{$extraColumn}";
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $extraColumn
     * @return string
     */
    private function existsActive(string $table, string $column, string $extraColumn = ""): string
    {
        $active = ModelInterface::ACTIVE;
        return "exists:{$table},{$column},status,{$active}{$extraColumn}";
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $extraColumn
     * @return string
     */
    private function existsSoftDelete(string $table, string $column, string $extraColumn = ""): string
    {
        return "exists:{$table},{$column},deleted_at,NULL{$extraColumn}";
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $extraColumn
     * @return string
     */
    private function existsRecord(string $table, string $column, string $extraColumn = ""): string
    {
        return "exists:{$table},{$column}{$extraColumn}";
    }

    /**
     * @param string $table
     * @param string $column
     * @param bool $except
     * @param string|null $modelId
     * @param string $extraColumn
     * @return string
     */
    private function uniqueSoftDelete(string $table, string $column, bool $except = false, string $modelId = null, string $extraColumn = ""): string
    {
        $id = ",NULL,uuid";
        if ($except)
        {
            $id = ",{$modelId},uuid";
        }
        return "unique:{$table},{$column}{$id},deleted_at,NULL{$extraColumn}";
    }


    /**
     * @param int $length
     * @param int $decimals
     * @return string
     */
    public function validateDecimals(int $length = 14, int $decimals = 4): string
    {
        return "regex:/^[\d]{0,{$length}}(\.[\d]{0,{$decimals}})?$/";
    }

    /**
     * @return string
     */
    public function validateCurp(): string
    {
        return 'regex:/^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$/';
    }

    /**
     * @return string
     */
    public function validateRfc(): string
    {
        return 'regex:/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/';
    }

}
