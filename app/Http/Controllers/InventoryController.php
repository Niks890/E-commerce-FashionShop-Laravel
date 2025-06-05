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
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.inventory.index');
    }



    // public function search(Request $request)
    // {
    //     try {
    //         $query = $request->input('query');
    //         $status = $request->input('status');
    //         $startDate = $request->input('start_date');
    //         $endDate = $request->input('end_date');

    //         $inventories = Inventory::with(['staff', 'provider', 'detail.product.category'])
    //             ->when($query, function ($q) use ($query) {
    //                 $q->where('id', 'LIKE', '%' . $query . '%')
    //                     ->orWhereHas('staff', function ($staffQuery) use ($query) {
    //                         $staffQuery->where('name', 'LIKE', '%' . $query . '%');
    //                     })
    //                     ->orWhereHas('provider', function ($providerQuery) use ($query) {
    //                         $providerQuery->where('name', 'LIKE', '%' . $query . '%');
    //                     })
    //                     ->orWhereHas('detail.product', function ($productQuery) use ($query) {
    //                         $productQuery->where('name', 'LIKE', '%' . $query . '%');
    //                     });
    //             })
    //             ->when($status, function ($q) use ($status) {
    //                 $q->where('status', $status);
    //             })
    //             ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
    //                 $q->whereBetween('created_at', [
    //                     Carbon::parse($startDate)->startOfDay(),
    //                     Carbon::parse($endDate)->endOfDay()
    //                 ]);
    //             })
    //             ->orderBy('created_at', 'desc')
    //             ->paginate(10);

    //         $inventories->appends([
    //             'query' => $query,
    //             'status' => $status,
    //             'start_date' => $startDate,
    //             'end_date' => $endDate
    //         ]);

    //         return view('admin.inventory.index', compact('inventories', 'query', 'status', 'startDate', 'endDate'));
    //     } catch (\Exception $e) {
    //         return redirect()->route('inventory.index')
    //             ->with('error', 'Có lỗi xảy ra khi tìm kiếm: ' . $e->getMessage());
    //     }
    // }



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


    // public function store(Request $request, CloudinaryService $cloudinaryService)
    // {
    //     DB::beginTransaction();
    //     try {
    //         Log::info('Data received:', $request->all());

    //         $data = $request->validate([
    //             // 'staff_id' => 'required|exists:users,id',
    //             'id' => 'required',
    //             'provider_id' => 'required|exists:providers,id',
    //             'products' => 'required|array|min:1',
    //             'products.*.product_name' => 'required|min:3|max:150|unique:products,product_name',
    //             'products.*.brand_name' => 'required|max:100',
    //             'products.*.image' => 'required|image',
    //             'products.*.category_id' => 'required|exists:categories,id',
    //             'products.*.price' => 'required|numeric|min:1',
    //             'products.*.variants' => 'required|array|min:1',
    //             'products.*.variants.*.color' => 'required|string',
    //             'products.*.variants.*.size' => 'required|string',
    //             'products.*.variants.*.quantity' => 'required|integer|min:1',
    //         ], [
    //             'products.*.product_name.required' => 'Tên sản phẩm không được để trống.',
    //             'products.*.product_name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
    //             'products.*.product_name.max' => 'Tên sản phẩm đã vượt quá 150 ký tự.',
    //             'products.*.product_name.unique' => 'Tên sản phẩm này đã tồn tại.',
    //             'products.*.image.required' => 'Vui lòng chọn hình ảnh.',
    //             'products.*.category_id.required' => 'Vui lòng chọn danh mục.',
    //             'products.*.category_id.exists' => 'Tên danh mục không hợp lệ.',
    //             'products.*.price.required' => 'Vui lòng điền giá nhập.',
    //             'products.*.price.min' => 'Giá tiền phải lớn hơn 0.',
    //             'products.*.variants.required' => 'Vui lòng nhập ít nhất một biến thể cho sản phẩm.',
    //             'products.*.variants.*.color.required' => 'Vui lòng nhập màu sắc.',
    //             'products.*.variants.*.size.required' => 'Vui lòng chọn kích cỡ.',
    //             'products.*.variants.*.quantity.required' => 'Vui lòng nhập số lượng.',
    //             'products.*.variants.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
    //         ]);

    //         // Tạo inventory
    //         $inventory = Inventory::create([
    //             'provider_id' => $data['provider_id'],
    //             'staff_id' => $data['id'],
    //             'total' => 0,
    //             'vat' => 0
    //         ]);

    //         $totalInventoryValue = 0;
    //         $totalVat = 0;
    //         $productsToProcessImages = [];
    //         $inventoryDetailBatchInsert = [];
    //         $vatRate = 0.1;

    //         foreach ($request->products as $productData) {
    //             // Tạo product (chưa xử lý ảnh)
    //             $product = Product::create([
    //                 'product_name' => $productData['product_name'],
    //                 'price' => $productData['price'],
    //                 'brand' => $productData['brand_name'],
    //                 'status' => 0,
    //                 'sku' => strtoupper(Str::random(6)),
    //                 'category_id' => $productData['category_id'],
    //                 'image' => 'temp', // Tạm thời
    //                 'slug' => Str::slug($productData['product_name'])
    //             ]);

    //             $productTotalQuantity = 0;
    //             $productTotalValue = 0;

    //             // Chuẩn bị data cho variants
    //             foreach ($productData['variants'] as $variant) {
    //                 $productTotalQuantity += $variant['quantity'];

    //                 $productVariant = ProductVariant::create([
    //                     'color' => $variant['color'],
    //                     'size' => $variant['size'],
    //                     'price' => $productData['price'],
    //                     'stock' => $variant['quantity'],
    //                     'product_id' => $product->id,
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]);

    //                 $inventoryDetailBatchInsert[] = [
    //                     'product_variant_id' => $productVariant->id,
    //                     'product_id' => $product->id,
    //                     'inventory_id' => $inventory->id,
    //                     'price' => $productData['price'],
    //                     'quantity' => $variant['quantity'],
    //                     'size' => $variant['size'] . '-' . $variant['quantity'] . '-' . $variant['color'],
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ];
    //             }

    //             // Tính toán giá trị
    //             $productTotalValue = $productTotalQuantity * $productData['price'];
    //             $productVat = $productTotalValue * $vatRate;
    //             $totalInventoryValue += $productTotalValue + $productVat;
    //             $totalVat += $productVat;

    //             // Lưu thông tin để xử lý ảnh sau
    //             $productsToProcessImages[] = [
    //                 'product' => $product,
    //                 'image' => $productData['image']
    //             ];
    //         }

    //         // Batch insert inventory details
    //         if (!empty($inventoryDetailBatchInsert)) {
    //             InventoryDetail::insert($inventoryDetailBatchInsert);
    //         }

    //         // Cập nhật tổng inventory
    //         $inventory->update([
    //             'total' => $totalInventoryValue,
    //             'vat' => $totalVat
    //         ]);

    //         DB::commit();

    //         // Xử lý ảnh sau khi transaction thành công
    //         foreach ($productsToProcessImages as $item) {
    //             $this->processProductImage($item['product'], $item['image'], $cloudinaryService);
    //         }

    //         // foreach ($productsToProcessImages as $item) {
    //         //     // Lưu file vào storage thay vì dùng file tạm
    //         //     $tempPath = $item['image']->store('temp_uploads');

    //         //     ProcessProductImage::dispatch(
    //         //         $item['product']->id, // Chỉ truyền ID
    //         //         Storage::path($tempPath) // Đường dẫn đầy đủ
    //         //     );
    //         // }

    //         Log::info('Inventory created successfully:', [
    //             'inventory_id' => $inventory->id,
    //             'total_products' => count($request->products)
    //         ]);

    //         return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công với " . count($request->products) . " sản phẩm!");
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error creating inventory:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    //     }
    // }


    public function store(Request $request, CloudinaryService $cloudinaryService)
    {
        DB::beginTransaction();
        try {
            Log::info('Data received:', $request->all());

            $data = $request->validate([
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
                // Các message validate giữ nguyên
            ]);

            // Tạo inventory với status mặc định là 'pending'
            $inventory = Inventory::create([
                'provider_id' => $data['provider_id'],
                'staff_id' => $data['id'],
                'total' => 0,
                'vat' => 0,
                'status' => 'pending' // Thêm trạng thái chờ duyệt
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
                    'status' => 2, // Sản phẩm mới tạo chưa active
                    'sku' => strtoupper(Str::random(6)),
                    'category_id' => $productData['category_id'],
                    'image' => 'temp', // Tạm thời
                    'slug' => Str::slug($productData['product_name'])
                ]);

                $productTotalQuantity = 0;
                $productTotalValue = 0;

                // Tạo các biến thể nhưng KHÔNG cập nhật stock ở đây
                foreach ($productData['variants'] as $variant) {
                    $productTotalQuantity += $variant['quantity'];

                    $productVariant = ProductVariant::create([
                        'color' => $variant['color'],
                        'size' => $variant['size'],
                        'price' => $productData['price'],
                        'stock' => 0, // Khởi tạo stock = 0, sẽ cập nhật khi duyệt
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

            Log::info('Inventory created successfully:', [
                'inventory_id' => $inventory->id,
                'total_products' => count($request->products)
            ]);

            return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công với " . count($request->products) . " sản phẩm! Phiếu nhập đang chờ duyệt.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating inventory:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }


    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $inventory = Inventory::with('InventoryDetails.ProductVariant')->findOrFail($id);

            // Kiểm tra nếu phiếu nhập đã được duyệt rồi
            if ($inventory->status === 'approved') {
                return back()->with('warning', 'Phiếu nhập này đã được duyệt trước đó.');
            }

            // Cập nhật stock cho từng biến thể sản phẩm
            foreach ($inventory->InventoryDetails as $detail) {
                if ($detail->ProductVariant) {
                    $detail->ProductVariant->increment('stock', $detail->quantity);
                }
            }

            // Cập nhật trạng thái phiếu nhập
            $inventory->update([
                'status' => 'approved',
                'updated_at' => now(),
                'staff_id' => auth()->user()->id - 1
            ]);

            // Kích hoạt các sản phẩm liên quan
            Product::whereIn('id', $inventory->InventoryDetails->pluck('product_id')->unique())
                ->update(['status' => 0]); // 0 = ẩn trong kho

            DB::commit();

            return back()->with('success', 'Đã duyệt phiếu nhập và cập nhật kho thành công!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi duyệt phiếu nhập: ' . $e->getMessage());
        }
    }




public function reject($id, Request $request)
{
    $request->validate([
        'note' => 'required|string|max:500'
    ]);

    DB::beginTransaction();
    try {
        $inventory = Inventory::findOrFail($id);

        if ($inventory->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể huỷ phiếu nhập ở trạng thái chờ duyệt');
        }

        $inventory->update([
            'status' => 'rejected',
            'updated_at' => now(),
            'staff_id' => auth()->user()->id - 1,
            'note' => $request->note
        ]);

        $productIds = InventoryDetail::where('inventory_id', $id)
            ->pluck('product_id')
            ->unique();

        Product::whereIn('id', $productIds)
            ->update(['status' => 3]);

        DB::commit();

        return redirect()->route('inventory.index')
            ->with('success', 'Đã huỷ phiếu nhập và ẩn các sản phẩm liên quan');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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


    // public function edit(Inventory $inventory)
    // {
    //     // Chỉ cho phép edit khi status = pending
    //     if ($inventory->status !== 'pending') {
    //         return redirect()->route('inventory.show', $inventory)
    //             ->with('error', 'Chỉ có thể chỉnh sửa phiếu nhập đang chờ duyệt!');
    //     }

    //     $cats = Category::all();
    //     $providers = Provider::all();
    //     $inventory->load(['inventoryDetails.product', 'inventoryDetails.productVariant']);

    //     return view('admin.inventory.edit', compact('inventory', 'cats', 'providers'));
    // }


    // public function update(Request $request, Inventory $inventory, CloudinaryService $cloudinaryService)
    // {
    //     // Chỉ cho phép update khi status = pending
    //     if ($inventory->status !== 'pending') {
    //         return redirect()->route('inventory.show', $inventory)
    //             ->with('error', 'Chỉ có thể chỉnh sửa phiếu nhập đang chờ duyệt!');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         Log::info('Updating inventory:', ['inventory_id' => $inventory->id, 'data' => $request->all()]);

    //         $data = $request->validate([
    //             'provider_id' => 'required|exists:providers,id',
    //             'products' => 'required|array|min:1',
    //             'products.*.id' => 'nullable|exists:products,id', // ID sản phẩm (nếu cập nhật sản phẩm cũ)
    //             'products.*.product_name' => 'required|min:3|max:150',
    //             'products.*.brand_name' => 'required|max:100',
    //             'products.*.image' => 'nullable|image', // Không bắt buộc nếu cập nhật
    //             'products.*.category_id' => 'required|exists:categories,id',
    //             'products.*.price' => 'required|numeric|min:1',
    //             'products.*.variants' => 'required|array|min:1',
    //             'products.*.variants.*.id' => 'nullable|exists:product_variants,id', // ID variant (nếu cập nhật variant cũ)
    //             'products.*.variants.*.color' => 'required|string',
    //             'products.*.variants.*.size' => 'required|string',
    //             'products.*.variants.*.quantity' => 'required|integer|min:1',
    //             'deleted_products' => 'nullable|array', // Danh sách ID sản phẩm bị xóa
    //             'deleted_variants' => 'nullable|array', // Danh sách ID variant bị xóa
    //         ], [
    //             'products.*.product_name.required' => 'Tên sản phẩm không được để trống.',
    //             'products.*.product_name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
    //             'products.*.product_name.max' => 'Tên sản phẩm đã vượt quá 150 ký tự.',
    //             'products.*.image.image' => 'File phải là hình ảnh.',
    //             'products.*.category_id.required' => 'Vui lòng chọn danh mục.',
    //             'products.*.price.required' => 'Vui lòng điền giá nhập.',
    //             'products.*.price.min' => 'Giá tiền phải lớn hơn 0.',
    //             'products.*.variants.required' => 'Vui lòng nhập ít nhất một biến thể cho sản phẩm.',
    //             'products.*.variants.*.color.required' => 'Vui lòng nhập màu sắc.',
    //             'products.*.variants.*.size.required' => 'Vui lòng chọn kích cỡ.',
    //             'products.*.variants.*.quantity.required' => 'Vui lòng nhập số lượng.',
    //             'products.*.variants.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
    //         ]);

    //         // Cập nhật thông tin inventory
    //         $inventory->update([
    //             'provider_id' => $data['provider_id']
    //         ]);

    //         // Xử lý xóa sản phẩm (nếu có)
    //         if (!empty($request->deleted_products)) {
    //             foreach ($request->deleted_products as $productId) {
    //                 $product = Product::find($productId);
    //                 if ($product) {
    //                     // Xóa inventory details liên quan
    //                     InventoryDetail::where('inventory_id', $inventory->id)
    //                         ->where('product_id', $productId)
    //                         ->delete();

    //                     // Xóa variants
    //                     ProductVariant::where('product_id', $productId)->delete();

    //                     // Xóa sản phẩm
    //                     $product->delete();
    //                 }
    //             }
    //         }

    //         // Xử lý xóa variants riêng lẻ (nếu có)
    //         if (!empty($request->deleted_variants)) {
    //             foreach ($request->deleted_variants as $variantId) {
    //                 $variant = ProductVariant::find($variantId);
    //                 if ($variant) {
    //                     // Xóa inventory detail liên quan
    //                     InventoryDetail::where('inventory_id', $inventory->id)
    //                         ->where('product_variant_id', $variantId)
    //                         ->delete();

    //                     // Xóa variant
    //                     $variant->delete();
    //                 }
    //             }
    //         }

    //         $totalInventoryValue = 0;
    //         $totalVat = 0;
    //         $vatRate = 0.1;
    //         $productsToProcessImages = [];

    //         foreach ($request->products as $productData) {
    //             $product = null;

    //             // Kiểm tra xem đây là sản phẩm mới hay cập nhật
    //             if (isset($productData['id']) && !empty($productData['id'])) {
    //                 // Cập nhật sản phẩm cũ
    //                 $product = Product::find($productData['id']);
    //                 if ($product) {
    //                     // Kiểm tra tên sản phẩm unique (trừ chính nó)
    //                     $existingProduct = Product::where('product_name', $productData['product_name'])
    //                         ->where('id', '!=', $product->id)
    //                         ->first();

    //                     if ($existingProduct) {
    //                         throw new Exception("Tên sản phẩm '{$productData['product_name']}' đã tồn tại.");
    //                     }

    //                     $product->update([
    //                         'product_name' => $productData['product_name'],
    //                         'price' => $productData['price'],
    //                         'brand' => $productData['brand_name'],
    //                         'category_id' => $productData['category_id'],
    //                         'slug' => Str::slug($productData['product_name'])
    //                     ]);
    //                 }
    //             } else {
    //                 // Tạo sản phẩm mới
    //                 $existingProduct = Product::where('product_name', $productData['product_name'])->first();
    //                 if ($existingProduct) {
    //                     throw new Exception("Tên sản phẩm '{$productData['product_name']}' đã tồn tại.");
    //                 }

    //                 $product = Product::create([
    //                     'product_name' => $productData['product_name'],
    //                     'price' => $productData['price'],
    //                     'brand' => $productData['brand_name'],
    //                     'status' => 0,
    //                     'sku' => strtoupper(Str::random(6)),
    //                     'category_id' => $productData['category_id'],
    //                     'image' => 'temp',
    //                     'slug' => Str::slug($productData['product_name'])
    //                 ]);
    //             }

    //             // Xử lý hình ảnh (nếu có upload mới)
    //             if (isset($productData['image']) && $productData['image']) {
    //                 $productsToProcessImages[] = [
    //                     'product' => $product,
    //                     'image' => $productData['image']
    //                 ];
    //             }

    //             $productTotalQuantity = 0;
    //             $productTotalValue = 0;

    //             // Xử lý variants
    //             foreach ($productData['variants'] as $variantData) {
    //                 $productTotalQuantity += $variantData['quantity'];

    //                 if (isset($variantData['id']) && !empty($variantData['id'])) {
    //                     // Cập nhật variant cũ
    //                     $variant = ProductVariant::find($variantData['id']);
    //                     if ($variant) {
    //                         $variant->update([
    //                             'color' => $variantData['color'],
    //                             'size' => $variantData['size'],
    //                             'price' => $productData['price'],
    //                         ]);

    //                         // Cập nhật inventory detail
    //                         $inventoryDetail = InventoryDetail::where('inventory_id', $inventory->id)
    //                             ->where('product_variant_id', $variant->id)
    //                             ->first();

    //                         if ($inventoryDetail) {
    //                             $inventoryDetail->update([
    //                                 'price' => $productData['price'],
    //                                 'quantity' => $variantData['quantity'],
    //                                 'size' => $variantData['size'] . '-' . $variantData['quantity'] . '-' . $variantData['color'],
    //                             ]);
    //                         }
    //                     }
    //                 } else {
    //                     // Tạo variant mới
    //                     $variant = ProductVariant::create([
    //                         'color' => $variantData['color'],
    //                         'size' => $variantData['size'],
    //                         'price' => $productData['price'],
    //                         'stock' => 0,
    //                         'product_id' => $product->id,
    //                     ]);

    //                     // Tạo inventory detail mới
    //                     InventoryDetail::create([
    //                         'product_variant_id' => $variant->id,
    //                         'product_id' => $product->id,
    //                         'inventory_id' => $inventory->id,
    //                         'price' => $productData['price'],
    //                         'quantity' => $variantData['quantity'],
    //                         'size' => $variantData['size'] . '-' . $variantData['quantity'] . '-' . $variantData['color'],
    //                     ]);
    //                 }
    //             }

    //             $productTotalValue = $productTotalQuantity * $productData['price'];
    //             $productVat = $productTotalValue * $vatRate;
    //             $totalInventoryValue += $productTotalValue + $productVat;
    //             $totalVat += $productVat;
    //         }

    //         // Cập nhật tổng tiền của inventory
    //         $inventory->update([
    //             'total' => $totalInventoryValue,
    //             'vat' => $totalVat
    //         ]);

    //         DB::commit();

    //         // Xử lý ảnh sau khi transaction thành công
    //         foreach ($productsToProcessImages as $item) {
    //             $this->processProductImage($item['product'], $item['image'], $cloudinaryService);
    //         }

    //         Log::info('Inventory updated successfully:', [
    //             'inventory_id' => $inventory->id,
    //             'total_products' => count($request->products)
    //         ]);

    //         return redirect()->route('inventory.show', $inventory)
    //             ->with('success', 'Cập nhật phiếu nhập thành công!');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error updating inventory:', [
    //             'inventory_id' => $inventory->id,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    //     }
    // }

    // public function show(Inventory $inventory)
    // {
    //     $inventory->load(['provider', 'staff', 'inventoryDetails.product', 'inventoryDetails.productVariant']);
    //     return view('admin.inventory.show', compact('inventory'));
    // }


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
        $products = Product::with(['productVariants' => function ($query) {
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
