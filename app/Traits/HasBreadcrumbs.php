<?php

namespace App\Traits;

trait HasBreadcrumbs
{
    public function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $unit = $this; // Start with the current category

        while ($unit) {
            $breadcrumbs[] = [
                'name' => $unit->getUnitName(),
                'url' => $unit->getUnitRoute() // Use dynamic method
            ];
            $unit = $unit->parent; // Ensure parent relationship exists
        }

        return array_reverse($breadcrumbs);
    }
}
