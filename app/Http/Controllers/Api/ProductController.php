<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateProductFormRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $product;
    private $totalPage = 10;

    public function __construct(Product $product)
    { 
        $this->product = $product;
    }

    public function index(Request $request)
    {
        $products = $this->product->getResults($request->all(), $this->totalPage)->all();
        
        return response()->json($products, 200);
    }

    public function store(StoreUpdateProductFormRequest $request)
    {
        $data = $request->all();

        if($request->hasFile('image') && $request->file('image')->isValid()) {
            $name = Str::kebab($request->name);
            $extension = $request->image->extension();
            $fileName = $name . '.' . $extension;
            $data['image'] = $fileName;

            $upload = $request->image->storeAs('products', $fileName);

            if(!$upload) {
                return response()->json(['error' => 'Fail_Upload'], 500);
            }
        }

        $product = $this->product->create($data);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = $this->product->findOrFail($id);

        return response()->json($product, 200);
    }

    public function update(StoreUpdateProductFormRequest $request, $id)
    {
        dd('aqui');
        $product = $this->product->findOrFail($id);

        $data = $request->all();

        if($request->hasFile('image') && $request->file('image')->isValid()) {
            if($product->image) {
                if(Storage::exists("products/$product->image")) {
                    Storage::delete("products/$product->image");
                }
            }

            $name = Str::kebab($request->name);
            $extension = $request->image->extension();
            $fileName = $name . '.' . $extension;
            $data['image'] = $fileName;

            $upload = $request->image->storeAs('products', $fileName);

            if(!$upload) {
                return response()->json(['error' => 'Fail_Upload'], 500);
            }
        }

        $product->update($request->all());

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = $this->product->findOrFail($id);

        $product->delete();

        return response()->json(['success' => true], 204);
    }
}
