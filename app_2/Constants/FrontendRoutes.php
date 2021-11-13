<?php

namespace App\Constants;

final class FrontendRoutes
{
    const RESET_PASSWORD = 'auth/reset-password/{token}';

    public static function buildUrl($route, $params = [])
    {
        foreach ($params as $paramName => $value) {
            $route = str_replace('{' . $paramName . '}', $value, $route);
        }

        return config('app.frontend_url') . '/' . $route;
    }
}
