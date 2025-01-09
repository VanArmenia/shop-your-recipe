<?php

namespace App\Models\Api;

class Recipe extends \App\Models\Recipe
{
    public function getRouteKeyName()
    {
        return 'id';
    }
}
