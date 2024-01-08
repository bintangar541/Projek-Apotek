<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Medicine;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->simplePaginate(10);
        return view('order.kasir.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = medicine::all();
        return view('order.kasir.create', compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
    $request->validate([
        'name_customer' => 'required',
        'medicines' => 'required',
    ], [
        'name_customer.required' => 'Nama Pembeli Harus Diisi',
        'medivcines.required' => 'Obat Harus Diisi'
    ]);

    // dd($request->medicines); // ambil semua value dari input name=medicines

    $arrayDistinct = array_count_values($request->medicines); // ambil value dan di hitung ada berapa
    // id 1 => 2, obat dengan id 1 di pilih 2 kali atau item => jumlah

    // dd{$arrayDistinct}

    $arrayAssocMedicines = [];

    foreach ($arrayDistinct as $id => $count) {
        $medicine = Medicine::where('id', $id)->first();
        $subPrice = $medicine->price * $count;

        $arrayItem = [
            'id' => $id,
            'name_medicine' => $medicine->name,
            'qty' => $count,
            'price' => $medicine->price,
            'sub_price' => $subPrice
        ];
        array_push($arrayAssocMedicines, $arrayItem);
    }

    $totalPrice = 0;

    foreach ($arrayAssocMedicines as $item) {
        $totalPrice +=(int)$item['sub_price'];
    }

    $priceWithPPN = $totalPrice + ($totalPrice * 0.01);

    $proses = Order::create([
        'user_id' => Auth::user()->id,
        'medicines' => $arrayAssocMedicines,
        'name_customer' => $request->name_customer,
        'total_price' => $priceWithPPN,
    ]);
    
    if ($proses) {
        $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

        return redirect()->route('kasir.order.print', $order->id);
    }else {
        return redirect()->back()->with('failed', 'Gagal membuat data pembelian. Silahkan coba kembali dengan data yang sesuai!');
    }
    
}

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function downloadPDF($id)
    {
     
    }
}