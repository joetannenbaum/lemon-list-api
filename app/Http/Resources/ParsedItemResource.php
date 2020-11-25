<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParsedItemResource extends JsonResource
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
            'id'           => $this->id ?? null,
            'aisle'        => $this->aisle,
            'name'         => $this->name,
            'original'     => $this->original,
            'originalName' => $this->originalName,
            'quantity'     => $this->unit === '' ? $this->amount : 1,
            'amount'       => $this->amount,
            'unit'         => $this->unit,
            'meta'         => $this->meta,
        ];
    }
}
