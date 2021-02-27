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
            'is_owner'       => $this->owner->id === $request->user()->id,
            'active_version' => new ShoppingListVersionResource($this->whenLoaded('activeVersion')),
            'total_items'    => $this->activeVersion->items()->count(),
        ];
    }
}
