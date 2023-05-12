<?php

namespace App\Interfaces;

interface HttpCodeInterface
{
    /* 100 - 199*/
    const CONTINUE = 100;
    const SWITCHING_PROTOCOL = 101;
    const PROCESSING = 102;

    /* 200 - 299 */
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NOM_AUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    const MULTI_STATUS = 207;
    const IM_USED = 226;

    /* 300 - 399 */
    const MULTIPLE_CHOICE = 300;
    const MOVE_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;

    /* 400 - 499 */
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const CONFLICT = 409;

    const UNPROCESSABLE_ENTITY = 422;

    /* 500 - 599 */
    const INTERNAL_SERVER_ERROR = 500;
}
