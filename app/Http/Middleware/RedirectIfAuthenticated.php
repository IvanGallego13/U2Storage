<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;

class RedirectIfAuthenticated extends Middleware
{
    /**
     * The URIs that users should be redirected to when authenticated.
     *
     * @var array
     */
    protected $redirectTo = [
        'default' => '/',
    ];
}
