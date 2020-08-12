<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ProductGroupResource extends JsonResource
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
            'adjectives' => $this->adjectives,
            'category' => new CategoryResource($this->category),
            'category_id' => $this->category_id,
            'forms' => $this->forms,
            'height' => $this->height,
            'id' => $this->id,
            'length' => $this->length,
            'name' => $this->name,
            'performance' => $this->performance,
            'products' => ProductResource::collection($this->products),
            'recipe_id' => $this->recipe_id,
            'salary_units' => $this->salary_units,
            'set_pair_id' => $this->set_pair_id,
            'set_pair_ratio' => $this->set_pair_ratio,
            'set_pair_ratio_to' => $this->set_pair_ratio_to,
            'size' => $this->size,
            'size_params' => $this->size_params,
            'unit_in_pallete' => $this->unit_in_pallete,
            'unit_in_units' => $this->unit_in_units,
            'units_from_batch' => $this->units_from_batch,
            'units_in_pallete' => $this->units_in_pallete,
            'units_text' => $this->units_text,
            'url' => $this->url,
            'weight_pallete' => $this->weight_pallete,
            'weight_unit' => $this->weight_unit,
            'width' => $this->width,
            'wp_name' => $this->wp_name,
            'wp_slug' => $this->wp_slug
        ];
    }
}
