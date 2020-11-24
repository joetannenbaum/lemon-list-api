<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListItemResource extends JsonResource
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
            'id'                       => $this->id,
            'shopping_list_version_id' => $this->shopping_list_version_id,
            'item_id'                  => $this->item_id,
            'order'                    => $this->order,
            'quantity'                 => $this->quantity,
            'checked_off'              => $this->checked_off,
            'note'                     => $this->note,
            'item'                     => new ItemResource($this->whenLoaded('item')),
        ];
    }
}
