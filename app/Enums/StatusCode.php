<?php

namespace App\Enums;

class StatusCode
{
    public const OK = 200;
    public const CREATED = 201;
    public const NO_CONTENT = 204;

    public const BAD_REQUEST = 400;

    public const VALIDATION_ERROR = 422;

    public const EMAIL_ALREADY_EXISTS = 409;

    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const CONFLICT = 409;

    public const INTERNAL_SERVER_ERROR = 500;
    public const SERVICE_UNAVAILABLE = 503;

    public const USER_REGISTRATION_FAILED = 1001;
    public const USER_UPDATE_FAILED = 1002;

}