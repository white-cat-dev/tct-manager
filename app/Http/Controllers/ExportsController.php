<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Exports\MaterialsExport;
use App\Order;
use Excel;
use Storage;
use PDF;


class ExportsController extends Controller
{
    public function index(Request $request) 
    {
        $fileName = $request->route('file', '');
        $type = $request->route('type', '');

        if ($type == 'order')
        {
            return file(storage_path('app/exports/' . $type . '/' . $fileName));
        }
        else
        {
            return Storage::download('exports/' . $type . '/' . $fileName);
        }
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


    public function order(Request $request)
    {
        $orderId = $request->get('id', 0);
        $order = Order::find($orderId);
        
        $pdf = PDF::loadView('exports/order');

        $content = $pdf->download()->getOriginalContent();

        $fileName = $order->number . '.pdf';

        Storage::put('exports/order/' . $fileName, $content);


        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'order'])
        ];
    }
}