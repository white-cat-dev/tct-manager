<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Exports\MaterialsExport;
use Excel;
use Storage;


class ExportsController extends Controller
{
    public function index(Request $request) 
    {
        $fileName = $request->route('file', 0);
        $type = $request->get('type', 0);

        return Storage::download('exports/' . $type . '/' . $fileName);
    }


    public function products(Request $request)
    {
        $category = $request->get('category', 0);
        $onlyInStock = $request->get('stock', false);
        $withMaterials = $request->get('materials', false);

        $fileName = 'products_' . date('d_m_Y') . '.xlsx';

        Excel::store(new ProductsExport($category, $onlyInStock, $withMaterials), 'exports/products/' . $fileName);

        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'products'])
        ];
    }


    public function materials(Request $request)
    {
        $fileName = 'materials_' . date('d_m_Y') . '.xlsx';

        Excel::store(new MaterialsExport(), 'exports/materials/' . $fileName);

        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'materials'])
        ];
    }
}