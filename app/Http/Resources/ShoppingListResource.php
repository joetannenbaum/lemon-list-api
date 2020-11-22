<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListResource extends JsonResource
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
            'id'             => $this->id,
            'owner_id'       => $this->owner_id,
            'name'           => $this->name,
            'image'          => $this->image,
            'uuid'           => $this->uuid,
            'is_shared'      => $this->is_shared,
            'active_version' => new ShoppingListVersionResource($this->whenLoaded('activeVersion')),
        ];
    }
}
