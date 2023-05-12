<?php

namespace App\Http\Controllers;

use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\ApiResponse;
use App\Traits\Utilities\GeneralUtilities;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController implements HttpCodeInterface
{
    use AuthorizesRequests, ValidatesRequests, GeneralUtilities, ApiResponse;
}
