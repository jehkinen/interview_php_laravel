<?php

namespace App\Traits;

use ReflectionClass;
use Illuminate\Support\Str;

trait HasShortName
{
    /**
     * Get only class name without namespace.
     * @return bool|string
     */
    public static function shortClassName()
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    public static function snakeName()
    {
        return Str::snake(self::shortClassName());
    }

    /**
     * @param $attribute
     * @throws \ReflectionException
     * @return string
     */
    public function getMorphClassName($attribute)
    {
        return (new ReflectionClass(new $this->{$attribute}))->getShortName();
    }
}
