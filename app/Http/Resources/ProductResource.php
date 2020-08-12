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
            'category' => new CategoryResource($this->category),
            'category_id' => $this->category_id,
            'free_in_stock' => $this->free_in_stock,
            'id' => $this->id,
            'in_stock' => $this->in_stock,
            'main_variation' => $this->in_stock,
            'main_variation_text' => $this->in_stock,
            'planned' => $this->in_stock,
            'price' => $this->in_stock,
            'price_cashless' => $this->in_stock,
            'price_unit' => $this->in_stock,
            'price_unit_cashless' => $this->in_stock,
            'price_unit_vat' => $this->in_stock,
            'price_vat' => $this->in_stock,
            // 'product_group' => new ProductGroupResource($this->product_group),
            'product_group_id' => $this->product_group_id,
            'realize_in_stock' => $this->realize_in_stock,
            'units_text' => $this->units_text,
            'variation' => $this->variation,
            'variation_noun_text' => $this->variation_noun_text,
            'variation_text' => $this->variation_text
        ];
    }
}
