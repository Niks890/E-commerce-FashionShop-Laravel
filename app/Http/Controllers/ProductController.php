<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

use App\Models\Category;
use App\Models\Discount;
use App\Models\ImageVariant;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{

    public function index()
    {
        $data = Product::with('discount', 'ProductVariants')
            ->whereIn('status', [0, 1, 2])
            ->orderBy('id', 'DESC')
            ->paginate(5);

        $categories = Category::all();
        return view('admin.product.index', compact('data', 'categories'));
    }

    public function search(Request $request)
    {
        $query = Product::with('discount', 'ProductVariants');

        // Lọc theo tên sản phẩm
        if ($request->filled('query')) {
            $keyword = $request->input('query');
            $query->where('product_name', 'like', "%$keyword%");
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $categoryId = $request->input('category');
            $query->where('category_id', $categoryId);
        }

        // Lọc theo giá
        if ($request->filled('price_range')) {
            $priceRange = $request->input('price_range');
            switch ($priceRange) {
                case '0-100000':
                    $query->whereBetween('price', [0, 100000]);
                    break;
                case '100001-500000':
                    $query->whereBetween('price', [100001, 500000]);
                    break;
                case '500001-1000000':
                    $query->whereBetween('price', [500001, 1000000]);
                    break;
                case '1000001-max':
                    $query->where('price', '>', 1000000);
                    break;
            }
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // Lọc theo trạng thái khuyến mãi MỚI
        if ($request->filled('promotion_status')) {
            $promotionStatus = $request->input('promotion_status');
            if ($promotionStatus === 'has_promotion') {
                $query->whereNotNull('discount_id'); // Sản phẩm có discount_id (có khuyến mãi)
            } elseif ($promotionStatus === 'no_promotion') {
                $query->whereNull('discount_id'); // Sản phẩm không có discount_id (không có khuyến mãi)
            }
        }

        if ($request->filled('stock_range')) {
            $stockRange = $request->input('stock_range');

            // Sử dụng subquery để tính tổng stock cho mỗi sản phẩm
            $productIdsWithStock = Product::select('products.id')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->groupBy('products.id')
                ->havingRaw('SUM(product_variants.stock) ' . ($stockRange == 'out_of_stock' ? '= 0' : '> 0')); // Logic cơ bản

            if ($stockRange == 'low_stock') {
                $productIdsWithStock->havingRaw('SUM(product_variants.stock) > 0 AND SUM(product_variants.stock) <= 10');
            } elseif ($stockRange == 'in_stock') {
                $productIdsWithStock->havingRaw('SUM(product_variants.stock) > 10');
            }

            // Lọc các sản phẩm chính dựa trên ID từ subquery
            $query->whereIn('products.id', $productIdsWithStock->pluck('id'));
        }


        $data = $query->paginate(5)->appends($request->except('page'));
        $categories = Category::all();

        return view('admin.product.index', compact('data', 'categories'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CloudinaryService $cloudinaryService)
    {
        $data = $request->validate([
            'name' => 'required|unique:products,product_name|min:3|max:100',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:jpg,jpeg,gif,png,webp'
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'image.mimes' => 'Định dạng ảnh phải là *.jpg, *.jpeg, *.gif, *.png, *.webp.'
        ]);

        $product = new Product();
        $product->product_name = $data['name'];
        $product->description = $data['description'];
        $product->price = $data['price'];

        //Xu ly anh
        // $file_name = $request->image->hashName();
        // $request->image->move(public_path('uploads'), $file_name);
        // $product->image = $file_name;
        // Xử lý ảnh
        // Upload ảnh lên Cloudinary
        $uploadResult = $cloudinaryService->uploadImage($request->file('image')->getPathname(), 'product_images');
        if (isset($uploadResult['error'])) {
            return redirect()->back()->with('error', 'Upload ảnh thất bại: ' . $uploadResult['error']);
        }
        $product->image = $uploadResult['url'];

        $product->status = $data['status'];
        $product->category_id = $data['category_id'];
        $product->save();
        return redirect()->route('product.index')->with('success', 'Thêm sản phẩm thành công');
    }



    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Product $product)
    // {
    //     $cats = Category::all();
    //     $discounts = Discount::where('status', 'active')->get();
    //     $lastestInventoryPrice = InventoryDetail::where('product_id', $product->id)->orderBy('created_at', 'desc')->first();
    //     $productVariants = ProductVariant::with('ImageVariants')->where('product_id', $product->id)->get();
    //     return view('admin.product.edit', compact('product', 'cats', 'discounts', 'productVariants', 'lastestInventoryPrice'));
    // }


    public function edit(Product $product)
    {
        $cats = Category::all();
        $discounts = Discount::where('status', 'active')->get();
        $lastestInventoryPrice = InventoryDetail::where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $productVariants = ProductVariant::with('ImageVariants')
            ->where('product_id', $product->id)
            ->get();

        // Lấy lịch sử giá từ Redis
        $priceHistory = $this->getPriceHistoryFromRedis($product->id);
        $variantPriceHistories = [];

        foreach ($productVariants as $variant) {
            $variantPriceHistories[$variant->id] = $this->getPriceHistoryFromRedis($product->id, $variant->id);
        }

        return view('admin.product.edit', compact(
            'product',
            'cats',
            'discounts',
            'productVariants',
            'lastestInventoryPrice',
            'priceHistory',
            'variantPriceHistories'
        ));
    }

    private function getPriceHistoryFromRedis($productId, $variantId = null)
    {
        $redis = Redis::connection();
        $key = $variantId
            ? "product:{$productId}:variant:{$variantId}:prices"
            : "product:{$productId}:prices";

        // Lấy tối đa 5 bản ghi gần nhất
        $history = $redis->lrange($key, 0, 4);

        return collect($history)->map(function($item) {
            return json_decode($item, true);
        });
}


    public function update(Request $request, Product $product, CloudinaryService $cloudinaryService)
    {
        $rules = [
            'name' => 'required|min:3|max:100|unique:products,product_name,' . $product->id,
            'status' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|mimes:jpg,jpeg,gif,png,webp,avif',
            'image_variant.*' => 'sometimes|array',
            'image_variant.*.*' => 'sometimes|image|mimes:jpg,jpeg,png,gif,webp,avif|max:2048',
            'price' => 'required|string|regex:/^[0-9,.]+$/',
            'price_variant.*' => 'required|string|regex:/^[0-9,.]+$/',
        ];

        $messages = [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'image.mimes' => 'Định dạng ảnh chính phải là *.jpg, *.jpeg, *.gif, *.png, *.webp, *.avif.',
            'image_variant.*.*.image' => 'Tệp tải lên cho biến thể phải là hình ảnh.',
            'image_variant.*.*.mimes' => 'Định dạng ảnh biến thể phải là *.jpg, *.jpeg, *.gif, *.png, *.webp, *.avif.',
            'price_variant.*.required' => 'Giá biến thể không được để trống.',
        ];

        $data = $request->validate($rules, $messages);

        // Cập nhật thông tin sản phẩm chính
        $product->product_name = $data['name'];

        // $product->price = $data['price'];
        // Xử lý giá - bỏ qua định dạng hóa đơn

        $cleanPrice = preg_replace('/[^0-9]/', '', $request->price);
        if ($product->price != $cleanPrice) {
            $this->savePriceToRedis($product->id, null, $product->price, $cleanPrice);
        }
        $product->price = (int) $cleanPrice;

        $product->category_id = $data['category_id'];
        $product->discount_id = $request->discount_id;
        $product->tags = $request->product_tags;
        $product->material = $request->material;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $data['status'];

        // Xử lý ảnh chính
        if ($request->image != null) {
            $uploadResult = $cloudinaryService->uploadImage($request->file('image')->getPathname(), 'product_first_variant_images');
            if (isset($uploadResult['error'])) {
                return redirect()->back()->with('error', 'Upload ảnh thất bại: ' . $uploadResult['error']);
            }
            $product->image = $uploadResult['url'];
        } else {
            $product->image = $request->image_path;
        }
        $product->save();

        // Xử lý biến thể sản phẩm
        $productVariants = ProductVariant::where('product_id', $product->id)->get();
        foreach ($productVariants as $variant) {
            $variantId = $variant->id;

            // Cập nhật giá biến thể
            if (isset($request->price_variant[$variantId])) {
                // Lưu giá biến thể vào Redis trước khi cập nhật

                $cleanVariantPrice = preg_replace('/[^0-9]/', '', $request->price_variant[$variantId]);
                if ($variant->price != $cleanVariantPrice) {
                    $this->savePriceToRedis($product->id, $variant->id, $variant->price, $cleanVariantPrice);
                }
                // $price = str_replace('.', '', $request->price_variant[$variantId]);
                $variant->price = (int) $cleanVariantPrice;
                $variant->save();
            }

            // Xử lý ảnh hiện có - xóa những ảnh không còn trong danh sách
            $keptImageIds = $request->input("existing_images.$variantId", []);
            ImageVariant::where('product_variant_id', $variantId)
                ->whereNotIn('id', $keptImageIds)
                ->delete();

            // Xử lý ảnh mới
            if ($request->hasFile("image_variant.$variantId")) {
                $images = $request->file("image_variant.$variantId");

                foreach ($images as $index => $imageFile) {
                    $uploadResult = $cloudinaryService->uploadImage($imageFile->getPathname(), 'product_variant_images');

                    if (isset($uploadResult['error'])) continue;

                    ImageVariant::create([
                        'url' => $uploadResult['url'],
                        'product_variant_id' => $variantId,
                        'order' => $index
                    ]);
                }
            }
        }

        return redirect()->route('product.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    private function savePriceToRedis($productId, $variantId, $oldPrice, $newPrice)
    {
        if ($oldPrice == $newPrice) {
            return;
        }

        $redis = Redis::connection();
        $key = $variantId
            ? "product:{$productId}:variant:{$variantId}:prices"
            : "product:{$productId}:prices";

        // Kiểm tra số lượng bản ghi hiện tại
        $currentCount = $redis->llen($key);

        // Nếu đã có đủ 5 bản ghi, xóa bản ghi cũ nhất
        if ($currentCount >= 5) {
            $redis->rpop($key); // Xóa bản ghi cũ nhất (right pop)
        }

        $timestamp = now()->toDateTimeString();
        $priceData = json_encode([
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'changed_at' => $timestamp,
            'changed_by' => auth()->id() ? auth()->id() : "NaN",
        ]);

        // Thêm bản ghi mới vào đầu danh sách
        $redis->lpush($key, $priceData);
    }

    public function destroy(Product $product)
    {
        if ($product->InventoryDetails->count() == 0) {
            $product->delete();
            return redirect()->back()->with('success', 'Xoá sản phẩm bán hàng thành công!');
        }
        return redirect()->back()->with('error', 'Xoá sản phẩm thất bại!');
    }



    public function getVariants(Product $product)
    {
        return response()->json([
            'variants' => $product->productVariants()
                ->where('active', 1) // Chỉ lấy variant active
                ->get()
                ->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'size' => $variant->size,
                        'stock' => $variant->stock,
                        'available_stock' => $variant->available_stock
                    ];
                })
        ]);
    }

    public function updateStock(Request $request)
    {
        DB::beginTransaction();

        try {
            $changes = $request->input('changes');

            foreach ($changes as $change) {
                $variant = ProductVariant::find($change['variant_id']);

                if (!$variant) {
                    continue;
                }

                // Validate stock doesn't exceed available
                if ($change['available_stock'] > $variant->stock) {
                    throw new \Exception("Available stock cannot exceed total stock");
                }

                $variant->available_stock = $change['available_stock'];
                $variant->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
