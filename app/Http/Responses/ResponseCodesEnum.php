<?php

declare(strict_types=1);

namespace App\Http\Responses;

enum ResponseCodesEnum: string
{
    case INTERNAL_ERROR           = 'INTERNAL_ERROR';
    case REQUEST_VALIDATION_ERROR = 'REQUEST_VALIDATION_ERROR';
    case NOT_FOUND                = 'NOT_FOUND';
    case FORBIDDEN                = 'FORBIDDEN';
    case UNAUTHORIZED             = 'UNAUTHORIZED';
    case UNPROCESSABLE_ENTITY     = 'UNPROCESSABLE ENTITY';
}
