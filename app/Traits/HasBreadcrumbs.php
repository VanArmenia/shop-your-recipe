<?php

namespace App\Traits;

trait HasBreadcrumbs
{
    public function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $category = $this; // Start with the current category

        while ($category) {
            $breadcrumbs[] = [
                'name' => $category->name,
                'url' => $category->getCategoryRoute() // Use dynamic method
            ];
            $category = $category->parent; // Ensure parent relationship exists
        }

        return array_reverse($breadcrumbs);
    }
}
