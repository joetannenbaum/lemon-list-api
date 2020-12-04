<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'id'         => $this->id,
            'owner_id'   => $this->owner_id,
            'name'       => $this->name,
            'store_tags' => StoreTagResource::collection($this->whenLoaded('storeTags')),
        ];
    }
}
