<?php

if (! function_exists('dd_json'))
{
    /**
     * @return never
     */
    function dd_json($data)
    {
        header('Content-Type: application/json');
        exit(response()->json($data));
    }
}
