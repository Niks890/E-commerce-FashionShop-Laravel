<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Provider;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.inventory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cats = Category::all();
        $providers = Provider::all();
        return view('admin.inventory.create', compact('cats', 'providers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'product_name' => 'required|min:3|max:150|unique:products,product_name',
            'brand_name' => 'required|max:100',
            'image' => 'required',
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:providers,id',
            'price' => 'required|numeric|min:1',
            'color' => 'required',
            'sizes' => 'required',
        ], [
            'product_name.required' => 'Tên sản phẩm không được để trống.',
            'product_name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
            'product_name.max' => 'Tên sản phẩm đã vượt quá 150 ký tự.',
            'product_name.unique' => 'Tên sản phẩm này đã tồn tại.',
            'image.required' => 'Vui lòng chọn hình ảnh.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Tên danh mục không hợp lệ.',
            'provider_id.required' => 'Vui lòng chọn tên nhà cung cấp.',
            'provider_id.exists' => 'Tên nhà cung cấp không hợp lệ.',
            'price.required' => 'Vui lòng điền giá nhập.',
            'price.min' => 'Giá tiền phải lớn hơn 0.',
            'color.required' => 'Vui lòng nhập màu sắc cho sản phẩm.',
            'sizes.required' => 'Vui lòng chọn kích cỡ cho sản phẩm.',
        ]);

        //Thêm vào Phiếu nhập hàng
        // dd($request->formatted_sizes);
        $inventory = new Inventory();
        $inventory->provider_id = $data['provider_id'];
        $inventory->staff_id = $data['id'];
        //Xử lý chuỗi {size}-{số lượng}
        $size_and_quantitys = explode(',', $request->formatted_sizes);
        // XL-10,L-20
        // dd($size_and_quantitys);
        $totalQuantity = 0;
        $sizes = "";
        $size_assoc = [];
        foreach ($size_and_quantitys as $size_and_quantity) {
            $item = explode('-', $size_and_quantity);
            list($key, $value) = $item;
            $size_assoc[$key] = (int)$value;
            //XL => 10, L => 20
            $sizes .= $item[0] . ",";
            $totalQuantity += $item[1];
        }
        $sizes = rtrim($sizes, ',');
        // XL,L
        // dd($sizes, $totalQuantity, $size_assoc);
        $inventory->total = $totalQuantity * $data['price'];
        $inventory->save();

        //Thêm vào Sản phẩm
        $product = new Product();
        $product->product_name = $data['product_name'];
        $product->brand = $data['brand_name'];
        $product->sku = strtoupper(Str::random(6));
        $product->category_id = $data['category_id'];
        //Xu ly anh
        $file_name = $request->image->hashName();
        $request->image->move(public_path('uploads'), $file_name);
        $product->image = $file_name;
        $product->slug = Str::slug($data['product_name']); //"Áo thun Polo XXL"->"ao-thun-polo-xxl"
        $product->save();

        // dd($query)
        //Thêm vào Mô tả sản phẩm

        foreach (explode(',', $sizes) as $size) {
            ProductVariant::firstOrCreate([
                'color' => $data['color'],
                'size' => $size,
                'stock' => $size_assoc[$size],
                'product_id' => $product->id,
            ]);
        };

        //Thêm vào Chi tiết Phiếu nhập
        $inventoryDetail = new InventoryDetail();
        $inventoryDetail->product_id = $product->id;
        $inventoryDetail->inventory_id = $inventory->id;
        $inventoryDetail->price = $data['price'];
        $inventoryDetail->quantity = $totalQuantity;
        //Xử lý chuỗi sizes
        $inventoryDetail->size = preg_replace('/([^,]+)/', '$1-' . $data['color'], join(',', $size_and_quantitys));
        $inventoryDetail->save();

        return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function add_extra()
    {
        $cats = Category::all();
        $providers = Provider::all();
        return view('admin.inventory.add-extra', compact('cats', 'providers'));
    }

    public function post_add_extra(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'product_name' => 'min:3|max:150',
            'image' => 'mimes:jpg,jpeg,gif,png,webp',
            'category_id' => 'string',
            'provider_id' => 'required|exists:providers,id',
            'price' => 'required|numeric|min:1',
            'color' => 'required',
            'sizes' => 'required',
        ], [
            'product_name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
            'product_name.max' => 'Tên sản phẩm đã vượt quá 150 ký tự.',
            'category_id.exists' => 'Tên danh mục không hợp lệ.',
            'provider_id.required' => 'Vui lòng chọn tên nhà cung cấp.',
            'provider_id.exists' => 'Tên nhà cung cấp không hợp lệ.',
            'price.required' => 'Vui lòng điền giá nhập.',
            'price.min' => 'Giá tiền phải lớn hơn 0.',
            'color.required' => 'Vui lòng nhập màu sắc cho sản phẩm.',
            'sizes.required' => 'Vui lòng chọn kích cỡ cho sản phẩm.',
        ]);

        $inventory = new Inventory();
        $inventory->provider_id = $data['provider_id'];
        $inventory->staff_id = $data['id'];

        // Xử lý chuỗi formatted_sizes -> Tạo mảng size và số lượng
        // formatted_size = XL-1,L-2
        $size_and_quantitys = explode(',', $request->formatted_sizes);
        $totalQuantity = 0;
        $size_assoc = [];

        foreach ($size_and_quantitys as $size_and_quantity) {
            $item = explode('-', $size_and_quantity);
            list($size, $quantity) = $item;
            // tạo mảng assoc XL->1, L->2
            $size_assoc[$size] = (int)$quantity;
            $totalQuantity += (int)$quantity;
        }

        // Tạo danh sách kích thước hiện có (từ request->variant) + size mới từ formatted_sizes
        $allSizes = array_unique(array_merge(array_keys($request->variant ?? []), array_keys($size_assoc)));
        $updatedStocks = [];

        foreach ($allSizes as $size) {
            $existingStock = $request->variant[$size] ?? 0;
            $newStock = $size_assoc[$size] ?? 0;
            $updatedStocks[$size] = $existingStock + $newStock;
        }
        // dd($request->variant, $size_assoc, $allSizes, $updatedStocks);

        // Xử lý cập nhật hoặc thêm mới vào bảng product_variants
        $color = $data['color'];

        foreach ($size_assoc as $size => $stock) {
            $existingVariant = ProductVariant::where([
                'color' => $color,
                'size' => $size,
                'product_id' => $request->product_id
            ])->first();

            if ($existingVariant) {
                // Nếu đã tồn tại -> Cộng dồn stock
                $existingVariant->stock += $stock;
                $existingVariant->save();
            } else {
                // Nếu chưa tồn tại -> Thêm mới
                ProductVariant::create([
                    'color' => $color,
                    'size' => $size,
                    'stock' => $stock,
                    'product_id' => $request->product_id
                ]);
            }
        }

        $inventory->total = $totalQuantity * $data['price'];
        $inventory->save();

        // Thêm vào bảng inventory_details
        $inventoryDetail = new InventoryDetail();
        $inventoryDetail->product_id = $request->product_id;
        $inventoryDetail->inventory_id = $inventory->id;
        $inventoryDetail->price = $data['price'];
        $inventoryDetail->quantity = $totalQuantity;
        // Thêm thông tin size kèm màu
        $inventoryDetail->size = preg_replace('/([^,]+)/', '$1-' . $color, $request->formatted_sizes);
        //VD: Có 2 chuỗi "XS-3" và "Vàng" -> "XS-3-Vàng"
        $inventoryDetail->save();

        return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công!");
    }


    public function search(Request $request) {}
}
