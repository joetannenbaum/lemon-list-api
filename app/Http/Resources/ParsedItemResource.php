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
            'aisle'        => $this->aisle ?? null,
            'name'         => $this->name,
            'original'     => $this->original,
            'originalName' => $this->originalName,
            'quantity'     => $this->getQuantity(),
            'amount'       => $this->amount,
            'unit'         => $this->unit ?? '',
            'meta'         => $this->meta,
        ];
    }

    protected function getQuantity()
    {
        $unit = $this->unit ?? null;

        if (!$unit) {
            // If there's no unit (e.g. 4 chicken breasts) just return the amount
            return $this->amount;
        }

        // For now doing this in case we need to expand on this later
        if (in_array($unit, ['grams', 'teaspoons', 'tablespoons', 'cups'])) {
            return 1;
        }

        return 1;
    }
}
