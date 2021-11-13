<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\Text;

class ItemsCount extends Text
{
    /**
     * @param string $label
     * @param int $relationForCount
     * @return Text
     */
    public static function makeField(string $label, int $relationForCount): Text
    {
        return Text::make($label, function () use ($relationForCount) {
            $itemsCount = $relationForCount;

            return $itemsCount > 0 ? $itemsCount . ' items selected' : ' — ';
        })->onlyOnIndex()->asHtml();
    }
}
