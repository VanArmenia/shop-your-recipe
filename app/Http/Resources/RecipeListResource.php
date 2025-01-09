<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class RecipeListResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var \Illuminate\Support\Collection $images */
        $images = $this->images;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'prep_time' => $this->prep_time,
            'category' => $this->category,
            'tags' => $this->tags,
            'image_url' => $this->image_url,
            'updated_at' => ( new \DateTime($this->updated_at) )->format('Y-m-d H:i:s'),
        ];
    }
}
