<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProductImage;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Provider;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;


class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.inventory.index');
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            // Nếu không có từ khóa tìm kiếm, chuyển hướng về trang index ban đầu
            if (empty($query)) {
                return redirect()->route('inventory.index');
            }

            // Tìm kiếm trong database
            $inventories = Inventory::with(['staff', 'provider', 'detail.product.category'])
                ->where(function ($q) use ($query) {
                    // Tìm kiếm theo ID phiếu nhập (chính xác hoặc một phần)
                    $q->where('id', 'LIKE', '%' . $query . '%')
                        // Tìm kiếm theo tên nhân viên
                        ->orWhereHas('staff', function ($staffQuery) use ($query) {
                            $staffQuery->where('name', 'LIKE', '%' . $query . '%');
                        })
                        // Tìm kiếm theo tên nhà cung cấp
                        ->orWhereHas('provider', function ($providerQuery) use ($query) {
                            $providerQuery->where('name', 'LIKE', '%' . $query . '%');
                        })
                        // Tìm kiếm theo tên sản phẩm
                        ->orWhereHas('detail.product', function ($productQuery) use ($query) {
                            $productQuery->where('name', 'LIKE', '%' . $query . '%');
                        });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Thêm query parameter vào pagination links để giữ từ khóa tìm kiếm khi chuyển trang
            $inventories->appends(['query' => $query]);

            // Trả về view 'admin.inventory.index' với dữ liệu đã tìm kiếm
            return view('admin.inventory.index', compact('inventories', 'query'));
        } catch (\Exception $e) {
            return redirect()->route('inventory.index')
                ->with('error', 'Có lỗi xảy ra khi tìm kiếm: ' . $e->getMessage());
        }
    }




    public function generatePDF($id)
    {
        // Get inventory data
        $inventory = Inventory::with(['staff', 'provider', 'InventoryDetails.product.category', 'InventoryDetails.ProductVariant'])
            ->findOrFail($id);

        // Prepare data for PDF
        $data = [
            'inventory' => $inventory,
            'createdDate' => $inventory->created_at->format('d/m/Y'),
            'updatedDate' => $inventory->updated_at->format('d/m/Y'),
        ];
        // dd($data);

        // Generate PDF
        $pdf = PDF::loadView('admin.inventory.pdf', $data);

        // Set paper options
        $pdf->setPaper('A4', 'portrait');

        // Download PDF
        return $pdf->download('PhieuNhap_' . $id . '.pdf');
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


    public function store(Request $request, CloudinaryService $cloudinaryService)
    {
        DB::beginTransaction();
        try {
            Log::info('Data received:', $request->all());

            $data = $request->validate([
                // 'staff_id' => 'required|exists:users,id',
                'id' => 'required',
                'provider_id' => 'required|exists:providers,id',
                'products' => 'required|array|min:1',
                'products.*.product_name' => 'required|min:3|max:150|unique:products,product_name',
                'products.*.brand_name' => 'required|max:100',
                'products.*.image' => 'required|image',
                'products.*.category_id' => 'required|exists:categories,id',
                'products.*.price' => 'required|numeric|min:1',
                'products.*.variants' => 'required|array|min:1',
                'products.*.variants.*.color' => 'required|string',
                'products.*.variants.*.size' => 'required|string',
                'products.*.variants.*.quantity' => 'required|integer|min:1',
            ], [
                'products.*.product_name.required' => 'Tên sản phẩm không được để trống.',
                'products.*.product_name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
                'products.*.product_name.max' => 'Tên sản phẩm đã vượt quá 150 ký tự.',
                'products.*.product_name.unique' => 'Tên sản phẩm này đã tồn tại.',
                'products.*.image.required' => 'Vui lòng chọn hình ảnh.',
                'products.*.category_id.required' => 'Vui lòng chọn danh mục.',
                'products.*.category_id.exists' => 'Tên danh mục không hợp lệ.',
                'products.*.price.required' => 'Vui lòng điền giá nhập.',
                'products.*.price.min' => 'Giá tiền phải lớn hơn 0.',
                'products.*.variants.required' => 'Vui lòng nhập ít nhất một biến thể cho sản phẩm.',
                'products.*.variants.*.color.required' => 'Vui lòng nhập màu sắc.',
                'products.*.variants.*.size.required' => 'Vui lòng chọn kích cỡ.',
                'products.*.variants.*.quantity.required' => 'Vui lòng nhập số lượng.',
                'products.*.variants.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
            ]);

            // Tạo inventory
            $inventory = Inventory::create([
                'provider_id' => $data['provider_id'],
                'staff_id' => $data['id'],
                'total' => 0,
                'vat' => 0
            ]);

            $totalInventoryValue = 0;
            $totalVat = 0;
            $productsToProcessImages = [];
            $inventoryDetailBatchInsert = [];
            $vatRate = 0.1;

            foreach ($request->products as $productData) {
                // Tạo product (chưa xử lý ảnh)
                $product = Product::create([
                    'product_name' => $productData['product_name'],
                    'price' => $productData['price'],
                    'brand' => $productData['brand_name'],
                    'status' => 0,
                    'sku' => strtoupper(Str::random(6)),
                    'category_id' => $productData['category_id'],
                    'image' => 'temp', // Tạm thời
                    'slug' => Str::slug($productData['product_name'])
                ]);

                $productTotalQuantity = 0;
                $productTotalValue = 0;

                // Chuẩn bị data cho variants
                foreach ($productData['variants'] as $variant) {
                    $productTotalQuantity += $variant['quantity'];

                    $productVariant = ProductVariant::create([
                        'color' => $variant['color'],
                        'size' => $variant['size'],
                        'price' => $productData['price'],
                        'stock' => $variant['quantity'],
                        'product_id' => $product->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $inventoryDetailBatchInsert[] = [
                        'product_variant_id' => $productVariant->id,
                        'product_id' => $product->id,
                        'inventory_id' => $inventory->id,
                        'price' => $productData['price'],
                        'quantity' => $variant['quantity'],
                        'size' => $variant['size'] . '-' . $variant['quantity'] . '-' . $variant['color'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                // Tính toán giá trị
                $productTotalValue = $productTotalQuantity * $productData['price'];
                $productVat = $productTotalValue * $vatRate;
                $totalInventoryValue += $productTotalValue + $productVat;
                $totalVat += $productVat;

                // Lưu thông tin để xử lý ảnh sau
                $productsToProcessImages[] = [
                    'product' => $product,
                    'image' => $productData['image']
                ];
            }

            // Batch insert inventory details
            if (!empty($inventoryDetailBatchInsert)) {
                InventoryDetail::insert($inventoryDetailBatchInsert);
            }

            // Cập nhật tổng inventory
            $inventory->update([
                'total' => $totalInventoryValue,
                'vat' => $totalVat
            ]);

            DB::commit();

            // Xử lý ảnh sau khi transaction thành công
            foreach ($productsToProcessImages as $item) {
                $this->processProductImage($item['product'], $item['image'], $cloudinaryService);
            }

            // foreach ($productsToProcessImages as $item) {
            //     // Lưu file vào storage thay vì dùng file tạm
            //     $tempPath = $item['image']->store('temp_uploads');

            //     ProcessProductImage::dispatch(
            //         $item['product']->id, // Chỉ truyền ID
            //         Storage::path($tempPath) // Đường dẫn đầy đủ
            //     );
            // }

            Log::info('Inventory created successfully:', [
                'inventory_id' => $inventory->id,
                'total_products' => count($request->products)
            ]);

            return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công với " . count($request->products) . " sản phẩm!");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating inventory:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    // Hàm riêng xử lý ảnh (có thể đưa vào queue)
    protected function processProductImage(Product $product, $imageFile, CloudinaryService $cloudinaryService)
    {
        try {
            $uploadResult = $cloudinaryService->uploadImage($imageFile->getPathname(), 'product_images');

            if (isset($uploadResult['error'])) {
                Log::error('Image upload failed for product: ' . $product->id, [
                    'error' => $uploadResult['error']
                ]);
                return;
            }

            $product->update(['image' => $uploadResult['url']]);
        } catch (Exception $e) {
            Log::error('Error processing product image:', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }


    // public function add_extra()
    // {
    //     $cats = Category::all();
    //     $providers = Provider::all();
    //     return view('admin.inventory.add-extra', compact('cats', 'providers'));
    // }

    // public function post_add_extra(Request $request)
    // {
    //     $data = $request->validate([
    //         'id' => 'required',
    //         'product_name' => 'min:3|max:150',
    //         'image' => 'mimes:jpg,jpeg,gif,png,webp',
    //         'category_id' => 'string',
    //         'provider_id' => 'required|exists:providers,id',
    //         'price' => 'required|numeric|min:1',
    //         'color' => 'required',
    //         'sizes' => 'required',
    //     ], [
    //         'product_name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
    //         'product_name.max' => 'Tên sản phẩm đã vượt quá 150 ký tự.',
    //         'category_id.exists' => 'Tên danh mục không hợp lệ.',
    //         'provider_id.required' => 'Vui lòng chọn tên nhà cung cấp.',
    //         'provider_id.exists' => 'Tên nhà cung cấp không hợp lệ.',
    //         'price.required' => 'Vui lòng điền giá nhập.',
    //         'price.min' => 'Giá tiền phải lớn hơn 0.',
    //         'color.required' => 'Vui lòng nhập màu sắc cho sản phẩm.',
    //         'sizes.required' => 'Vui lòng chọn kích cỡ cho sản phẩm.',
    //     ]);

    //     $inventory = new Inventory();
    //     $inventory->provider_id = $data['provider_id'];
    //     $inventory->staff_id = $data['id'];

    //     // Xử lý chuỗi formatted_sizes -> Tạo mảng size và số lượng
    //     // formatted_size = XL-1,L-2
    //     $size_and_quantitys = explode(',', $request->formatted_sizes);
    //     $totalQuantity = 0;
    //     $size_assoc = [];

    //     foreach ($size_and_quantitys as $size_and_quantity) {
    //         $item = explode('-', $size_and_quantity);
    //         list($size, $quantity) = $item;
    //         // tạo mảng assoc XL->1, L->2
    //         $size_assoc[$size] = (int)$quantity;
    //         $totalQuantity += (int)$quantity;
    //     }

    //     // Tạo danh sách kích thước hiện có (từ request->variant) + size mới từ formatted_sizes
    //     $allSizes = array_unique(array_merge(array_keys($request->variant ?? []), array_keys($size_assoc)));
    //     $updatedStocks = [];

    //     foreach ($allSizes as $size) {
    //         $existingStock = $request->variant[$size] ?? 0;
    //         $newStock = $size_assoc[$size] ?? 0;
    //         $updatedStocks[$size] = $existingStock + $newStock;
    //     }
    //     // dd($request->variant, $size_assoc, $allSizes, $updatedStocks);

    //     // Xử lý cập nhật hoặc thêm mới vào bảng product_variants
    //     $color = $data['color'];

    //     foreach ($size_assoc as $size => $stock) {
    //         $existingVariant = ProductVariant::where([
    //             'color' => $color,
    //             'size' => $size,
    //             'product_id' => $request->product_id
    //         ])->first();

    //         if ($existingVariant) {
    //             // Nếu đã tồn tại -> Cộng dồn stock
    //             $existingVariant->stock += $stock;
    //             $existingVariant->save();
    //         } else {
    //             // Nếu chưa tồn tại -> Thêm mới
    //             ProductVariant::create([
    //                 'color' => $color,
    //                 'size' => $size,
    //                 'stock' => $stock,
    //                 'product_id' => $request->product_id
    //             ]);
    //         }
    //     }

    //     $inventory->total = $totalQuantity * $data['price'];
    //     $inventory->save();

    //     // Thêm vào bảng inventory_details
    //     $inventoryDetail = new InventoryDetail();
    //     $inventoryDetail->product_id = $request->product_id;
    //     $inventoryDetail->inventory_id = $inventory->id;
    //     $inventoryDetail->price = $data['price'];
    //     $inventoryDetail->quantity = $totalQuantity;
    //     // Thêm thông tin size kèm màu
    //     $inventoryDetail->size = preg_replace('/([^,]+)/', '$1-' . $color, $request->formatted_sizes);
    //     //VD: Có 2 chuỗi "XS-3" và "Vàng" -> "XS-3-Vàng"
    //     $inventoryDetail->save();

    //     return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công!");
    // }


    public function add_extra(Request $request)
{
    $request->validate(['inventory_id' => 'required|exists:inventories,id']);

    $originalInventory = Inventory::with('provider')->findOrFail($request->inventory_id);
    $products = Product::with(['productVariants' => function($query) {
        $query->select('product_id', 'color', 'size') // Only select grouped columns
              ->groupBy('product_id', 'color', 'size');
    }])->get();

    return view('admin.inventory.add-extra', compact('originalInventory', 'products'));
}

    public function post_add_extra(Request $request)
    {
        $data = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'selected_products' => 'required|array',
            'selected_products.*.product_id' => 'required|exists:products,id',
            'selected_products.*.price' => 'required|numeric|min:1',
            'selected_products.*.variants' => 'required|array',
            'selected_products.*.variants.*.color' => 'required',
            'selected_products.*.variants.*.sizes' => 'required|array',
            'selected_products.*.variants.*.sizes.*.size' => 'required',
            'selected_products.*.variants.*.sizes.*.quantity' => 'required|integer|min:1',
        ]);

        $originalInventory = Inventory::findOrFail($data['inventory_id']);

        DB::beginTransaction();

        try {
            // Create new inventory record
            $newInventory = new Inventory();
            $newInventory->provider_id = $originalInventory->provider_id;
            $newInventory->staff_id = auth()->id();
            $newInventory->original_inventory_id = $originalInventory->id;
            $newInventory->is_additional = true;
            $newInventory->save();

            $totalValue = 0;

            foreach ($data['selected_products'] as $selectedProduct) {
                $productTotal = 0;
                $productQuantity = 0;
                $sizeDetails = [];

                foreach ($selectedProduct['variants'] as $variant) {
                    $color = $variant['color'];
                    $colorSizes = [];

                    foreach ($variant['sizes'] as $sizeData) {
                        $size = $sizeData['size'];
                        $quantity = (int)$sizeData['quantity'];

                        // Update stock quantity
                        ProductVariant::updateOrCreate(
                            [
                                'product_id' => $selectedProduct['product_id'],
                                'color' => $color,
                                'size' => $size
                            ],
                            ['stock' => DB::raw("stock + $quantity")]
                        );

                        $productQuantity += $quantity;
                        $colorSizes[] = "$size-$quantity";
                        $sizeDetails[] = "$size-$quantity-$color";
                    }

                    $productTotal += $productQuantity * $selectedProduct['price'];
                }

                if ($productQuantity > 0) {
                    // Add to inventory details
                    $detail = new InventoryDetail();
                    $detail->inventory_id = $newInventory->id;
                    $detail->product_id = $selectedProduct['product_id'];
                    $detail->price = $selectedProduct['price'];
                    $detail->quantity = $productQuantity;
                    $detail->size = implode(',', $sizeDetails);
                    $detail->save();

                    $totalValue += $productTotal;
                }
            }

            $newInventory->total = $totalValue;
            $newInventory->save();

            DB::commit();

            return redirect()->route('inventory.index')
                   ->with('success', 'Nhập thêm hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
