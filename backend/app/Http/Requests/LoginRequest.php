<?php

namespace App\Http\Requests;

/**
 * Backwards-compatible alias for API v1 login validation.
 */
class LoginRequest extends \App\Http\Requests\Api\V1\Auth\LoginRequest
{
}
