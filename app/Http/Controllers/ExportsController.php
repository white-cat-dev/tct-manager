<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Exports\ProductsExport;
use App\Exports\MaterialsExport;
use App\Exports\ProductionsExport;
use Carbon\Carbon;
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
            $file = Storage::get('exports/' . $type . '/' . $fileName);
            $response = Response::make($file, 200);
            $response->header('Content-Type', 'application/pdf');
            return $response;
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

        $fileName = 'products_(' . date('d_m_Y') . ').xlsx';

        Excel::store(new ProductsExport($category, $onlyInStock, $withMaterials), 'exports/products/' . $fileName);

        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'products'])
        ];
    }


    public function materials(Request $request)
    {
        $fileName = 'materials_(' . date('d_m_Y') . ').xlsx';

        Excel::store(new MaterialsExport(), 'exports/materials/' . $fileName);

        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'materials'])
        ];
    }


    public function productions(Request $request)
    {
        $today = Carbon::today();

        $year = (int)$request->get('year', $today->year);
        $month = (int)$request->get('month', $today->month);

        $date = Carbon::create($year, $month);

        $fileName = 'productions_' . $date->format('m_Y') . '_(' . date('d_m_Y') . ').xlsx';

        Excel::store(new ProductionsExport($year, $month), 'exports/productions/' . $fileName);

        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'productions'])
        ];
    }


    public function order(Request $request)
    {
        $orderId = $request->get('id', 0);
        $order = Order::find($orderId);

        $html = view('exports/order', compact('order'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        
        $pdf = PDF::loadHtml($html, 'UTF-8');

        $content = $pdf->output();

        $fileName = $order->number . '.pdf';

        Storage::put('exports/order/' . $fileName, $content);


        return [
            'file' => route('export', ['file' => $fileName, 'type' => 'order'])
        ];
    }
}