<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParsedUrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title'    => $this->title,
            'servings' => $this->servings,
            'url'      => $this->sourceUrl,
            'image'    => $this->image,
            'items'    => ParsedItemResource::collection($this->extendedIngredients),
        ];
    }
}
