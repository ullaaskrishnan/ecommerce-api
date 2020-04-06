<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'sku' => $this->sku,
            'Name' => $this->Name,
            'description'=> $this->description,
            'price' => $this->price,
            'InStock' => $this->UnitsInStock>0 ? $this->UnitsInStock:"Out of Stock",
        ];
    }
}
