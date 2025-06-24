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
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{


    /**
     * Generate SKU based on product information
     * Format: BRAND-CATEGORY-PRODUCT-TIMESTAMP
     */
    private function generateSku($productName, $categoryId, $brandName)
    {
        // Lấy 3 chữ cái đầu của brand (loại bỏ ký tự đặc biệt, viết hoa)
        $brandPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $brandName), 0, 3));
        if (empty($brandPart)) {
            $brandPart = 'BRN'; // Fallback nếu brand không có chữ cái
        }

        // Lấy thông tin category từ database
        $category = Category::find($categoryId);
        $categoryName = $category ? $category->name : 'unknown';
        $categoryPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $categoryName), 0, 3));
        if (empty($categoryPart)) {
            $categoryPart = 'CAT'; // Fallback
        }

        // Lấy 3 chữ cái đầu của product name
        $productPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $productName), 0, 3));
        if (empty($productPart)) {
            $productPart = 'PRD'; // Fallback
        }

        // Thêm timestamp để đảm bảo unique
        $timePart = substr(time(), -4);

        return $brandPart . '-' . $categoryPart . '-' . $productPart . '-' . $timePart;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Provider::all();
        return view('admin.inventory.index', compact('providers'));
    }


    public function generatePDF($id)
    {
        // Get inventory data
        $inventory = Inventory::with(['staff', 'approvedBy', 'provider', 'InventoryDetails.product.category', 'InventoryDetails.ProductVariant'])
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

    // Hàm gốc
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
                'note_inventory' => 'required',
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
                'status' => 'pending', // Thêm trạng thái chờ duyệt
                'note' => $data['note_inventory'],
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
                    // 'sku' => strtoupper(Str::random(6)),
                    'sku' => $this->generateSku($productData['product_name'], $productData['category_id'], $productData['brand_name']),
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


    // Hàm gốc
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

    public function add_extra()
    {
        $categories = Category::all();
        $providers = Provider::all();
        return view('admin.inventory.add-extra', compact('providers', 'categories'));
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

    // Nhập đc nhiều sp, nhiều size, nhiều màu, nhiều kích thước


    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $inventory = Inventory::with(['inventoryDetails.productVariant', 'inventoryDetails.product'])->findOrFail($id);

            if ($inventory->status === 'approved') {
                DB::rollBack();
                return back()->with('warning', 'Phiếu nhập này đã được duyệt trước đó.');
            }
            if ($inventory->status === 'rejected') {
                DB::rollBack();
                return back()->with('error', 'Phiếu nhập này đã bị từ chối, không thể duyệt.');
            }

            $affectedProducts = [];

            foreach ($inventory->inventoryDetails as $detail) {
                if (!$detail->productVariant) {
                    throw new Exception('Không tìm thấy biến thể sản phẩm cho chi tiết nhập kho ID: ' . $detail->id);
                }

                $product = $detail->productVariant->product;
                $affectedProducts[$product->id] = $product;

                // Cập nhật stock và kích hoạt variant nếu cần
                $wasZeroStock = $detail->productVariant->stock == 0;
                $detail->productVariant->increment('stock', $detail->quantity);
                $detail->productVariant->active = true; // Đảm bảo variant được kích hoạt

                // Cập nhật giá nếu khác
                if ($detail->productVariant->price != $detail->price) {
                    $detail->productVariant->price = $detail->price;
                }

                $detail->productVariant->save();

                Log::debug('Đã cập nhật stock và price cho biến thể', [
                    'variant_id' => $detail->productVariant->id,
                    'product_id' => $product->id,
                    'color' => $detail->productVariant->color,
                    'size' => $detail->productVariant->size,
                    'was_zero_stock' => $wasZeroStock,
                    'added_quantity' => $detail->quantity,
                    'new_stock' => $detail->productVariant->stock,
                    'new_price' => $detail->productVariant->price
                ]);
            }

            // Cập nhật trạng thái phiếu nhập
            $inventory->update([
                'status' => 'approved',
                'updated_at' => now(),
                'approved_by' => auth()->user()->id - 1
            ]);
            Log::debug('Đã cập nhật trạng thái phiếu nhập thành duyệt', ['inventory_id' => $inventory->id]);

            // Cập nhật trạng thái sản phẩm liên quan
            foreach ($affectedProducts as $product) {
                $hasActiveVariants = $product->productVariants()
                    ->where('active', true)
                    ->where('stock', '>', 0)
                    ->exists();

                if ($hasActiveVariants) {
                    $newStatus = 0; // Hiển thị cả client lẫn admin
                } else {
                    $newStatus = 2; // Chỉ hiển thị admin nếu không có variant nào có stock
                }

                $product->status = $newStatus;
                $product->save();

                Log::debug('Đã cập nhật trạng thái sản phẩm', [
                    'product_id' => $product->id,
                    'new_status' => $newStatus,
                    'reason' => $hasActiveVariants ? 'Có variant tồn kho' : 'Không có variant tồn kho'
                ]);
            }

            DB::commit();

            return back()->with('success', 'Đã duyệt phiếu nhập và cập nhật kho thành công!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra khi duyệt phiếu nhập', [
                'inventory_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
            $inventory = Inventory::with(['inventoryDetails.productVariant', 'inventoryDetails.product'])->findOrFail($id);

            if ($inventory->status !== 'pending') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Chỉ có thể huỷ phiếu nhập ở trạng thái chờ duyệt');
            }

            // Cập nhật trạng thái phiếu nhập
            $inventory->update([
                'status' => 'rejected',
                'updated_at' => now(),
                'staff_id' => auth()->user()->id - 1,
                'note' => $request->note
            ]);

            $affectedProducts = [];

            foreach ($inventory->inventoryDetails as $detail) {
                $product = $detail->product;
                $variant = $detail->productVariant;

                $affectedProducts[$product->id] = $product;

                // Đánh dấu variant trong phiếu nhập này là inactive
                if ($variant) {
                    $variant->active = false;
                    $variant->save();

                    Log::debug('Đã đánh dấu variant là inactive', [
                        'variant_id' => $variant->id,
                        'product_id' => $product->id,
                        'color' => $variant->color,
                        'size' => $variant->size
                    ]);
                }
            }

            // Xử lý trạng thái sản phẩm sau khi hủy
            foreach ($affectedProducts as $product) {
                // Kiểm tra xem sản phẩm có variant active và có stock > 0 không
                $hasActiveStock = $product->productVariants()
                    ->where('active', true)
                    ->where('stock', '>', 0)
                    ->exists();

                if ($hasActiveStock) {
                    $product->status = 0;
                    // Nếu còn variant có stock: giữ nguyên trạng thái (không ẩn)
                    Log::debug('Giữ nguyên trạng thái sản phẩm do vẫn còn variant tồn kho', [
                        'product_id' => $product->id,
                        'status' => $product->status
                    ]);
                } else {
                    // Nếu không còn variant nào có stock: ẩn sản phẩm
                    $product->status = 3; // Ẩn hoàn toàn
                    $product->save();

                    Log::debug('Đã ẩn sản phẩm do không còn variant tồn kho', [
                        'product_id' => $product->id,
                        'new_status' => 3
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Đã huỷ phiếu nhập thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra khi huỷ phiếu nhập', [
                'inventory_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    //Hàm gốc
    // public function post_add_extra(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Validate basic fields first
    //         $request->validate([
    //             'id' => 'required',
    //             'provider_id' => 'required|exists:providers,id',
    //         ], [
    //             'provider_id.required' => 'Vui lòng chọn nhà cung cấp.',
    //             'provider_id.exists' => 'Nhà cung cấp không hợp lệ.',
    //         ]);

    //         // Decode products_to_add
    //         $productsToAdd = json_decode($request->input('products_to_add'), true);
    //         $note = $request->input('note');
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             throw new Exception('Định dạng JSON của products_to_add không hợp lệ');
    //         }

    //         // Validate products structure
    //         $validatedProducts = [];
    //         foreach ($productsToAdd as $product) {
    //             $validator = Validator::make($product, [
    //                 'product_id' => 'required|exists:products,id',
    //                 'new_price' => 'required|numeric|min:1',
    //                 'new_colors' => 'required|array|min:1',
    //                 'new_colors.*' => 'string|min:2',
    //                 'new_sizes_quantities' => 'required|array|min:1',
    //             ], [
    //                 'product_id.required' => 'ID sản phẩm không hợp lệ.',
    //                 'new_price.required' => 'Vui lòng nhập giá nhập cho sản phẩm.',
    //                 'new_price.min' => 'Giá nhập phải lớn hơn 0.',
    //                 'new_colors.required' => 'Vui lòng chọn ít nhất một màu cho sản phẩm.',
    //                 'new_sizes_quantities.required' => 'Vui lòng chọn kích cỡ và số lượng cho sản phẩm.',
    //             ]);

    //             if ($validator->fails()) {
    //                 throw new Exception($validator->errors()->first());
    //             }

    //             $validatedProducts[] = $product;
    //         }

    //         if (empty($validatedProducts)) {
    //             throw new Exception('Vui lòng chọn ít nhất một sản phẩm để nhập thêm.');
    //         }

    //         // Create inventory
    //         $inventory = new Inventory();
    //         $inventory->provider_id = $request->provider_id;
    //         $inventory->staff_id = $request->id;
    //         $inventory->status = 'pending';
    //         $inventory->note = $note;
    //         $inventory->total = 0;
    //         $inventory->save();

    //         $totalInventoryValue = 0;
    //         $vatRate = 0.1;

    //         foreach ($validatedProducts as $productData) {
    //             $colors = $productData['new_colors'];
    //             $sizesQuantities = $productData['new_sizes_quantities'];

    //             foreach ($colors as $color) {
    //                 $normalizedColor = strtolower(trim($color));

    //                 foreach ($sizesQuantities as $size => $quantity) {
    //                     if ($quantity <= 0) {
    //                         throw new Exception("Số lượng cho kích cỡ {$size} của sản phẩm có ID {$productData['product_id']} phải lớn hơn 0.");
    //                     }

    //                     $variantTotal = $quantity * $productData['new_price'];
    //                     $totalInventoryValue += $variantTotal;

    //                     // Find or create variant
    //                     $variant = ProductVariant::firstOrCreate([
    //                         'product_id' => $productData['product_id'],
    //                         'color' => $normalizedColor,
    //                         'size' => $size,
    //                     ], [
    //                         'stock' => 0,
    //                         'price' => $productData['new_price']
    //                     ]);

    //                     // Update price if different
    //                     if (!$variant->wasRecentlyCreated && $variant->price != $productData['new_price']) {
    //                         $variant->price = $productData['new_price'];
    //                         $variant->save();
    //                     }

    //                     // Create InventoryDetail
    //                     $inventoryDetail = new InventoryDetail();
    //                     $inventoryDetail->product_id = $productData['product_id'];
    //                     $inventoryDetail->inventory_id = $inventory->id;
    //                     $inventoryDetail->product_variant_id = $variant->id;
    //                     $inventoryDetail->price = $productData['new_price'];
    //                     $inventoryDetail->quantity = $quantity;
    //                     $inventoryDetail->size = $size . '-' . $quantity . '-' . $color;
    //                     $inventoryDetail->save();
    //                 }
    //             }
    //         }

    //         $inventory->total = $totalInventoryValue;
    //         $inventory->vat = $totalInventoryValue * $vatRate;
    //         $inventory->save();

    //         DB::commit();

    //         return redirect()->route('inventory.index')
    //             ->with('success', 'Yêu cầu nhập kho đã được tạo thành công và đang chờ duyệt!');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error in post_add_extra', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'request' => $request->all()
    //         ]);
    //         return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    //     }
    // }


    // Hàm bổ sung kiểm tra size
    public function post_add_extra(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate basic fields first
            $request->validate([
                'id' => 'required',
                'provider_id' => 'required|exists:providers,id',
            ], [
                'provider_id.required' => 'Vui lòng chọn nhà cung cấp.',
                'provider_id.exists' => 'Nhà cung cấp không hợp lệ.',
            ]);

            // Decode products_to_add
            $productsToAdd = json_decode($request->input('products_to_add'), true);
            $note = $request->input('note');

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Định dạng JSON của products_to_add không hợp lệ');
            }

            // Validate products structure
            $validatedProducts = [];
            foreach ($productsToAdd as $product) {
                $validator = Validator::make($product, [
                    'product_id' => 'required|exists:products,id',
                    'new_price' => 'required|numeric|min:1',
                    'new_colors' => 'required|array|min:1',
                    'new_colors.*' => 'string|min:2',
                    'new_sizes_quantities' => 'required|array|min:1',
                ], [
                    'product_id.required' => 'ID sản phẩm không hợp lệ.',
                    'new_price.required' => 'Vui lòng nhập giá nhập cho sản phẩm.',
                    'new_price.min' => 'Giá nhập phải lớn hơn 0.',
                    'new_colors.required' => 'Vui lòng chọn ít nhất một màu cho sản phẩm.',
                    'new_sizes_quantities.required' => 'Vui lòng chọn kích cỡ và số lượng cho sản phẩm.',
                ]);

                if ($validator->fails()) {
                    throw new Exception($validator->errors()->first());
                }

                // Kiểm tra trùng size trong new_sizes_quantities
                $sizes = array_keys($product['new_sizes_quantities']);
                if (count($sizes) !== count(array_unique($sizes))) {
                    throw new Exception("Sản phẩm ID {$product['product_id']} có size bị trùng lặp");
                }

                // Kiểm tra số lượng hợp lệ
                foreach ($product['new_sizes_quantities'] as $size => $quantity) {
                    if (!is_numeric($quantity)) {
                        throw new Exception("Số lượng cho size {$size} phải là số");
                    }
                    if ($quantity <= 0) {
                        throw new Exception("Số lượng cho size {$size} phải lớn hơn 0");
                    }
                }

                $validatedProducts[] = $product;
            }

            if (empty($validatedProducts)) {
                throw new Exception('Vui lòng chọn ít nhất một sản phẩm để nhập thêm.');
            }

            // Create inventory
            $inventory = new Inventory();
            $inventory->provider_id = $request->provider_id;
            $inventory->staff_id = $request->id;
            $inventory->status = 'pending';
            $inventory->note = $note;
            $inventory->total = 0;
            $inventory->save();

            $totalInventoryValue = 0;
            $vatRate = 0.1;

            foreach ($validatedProducts as $productData) {
                $colors = $productData['new_colors'];
                $sizesQuantities = $productData['new_sizes_quantities'];

                // Log product data để debug nếu cần
                Log::debug('Processing product', [
                    'product_id' => $productData['product_id'],
                    'colors' => $colors,
                    'sizesQuantities' => $sizesQuantities
                ]);

                foreach ($colors as $color) {
                    $normalizedColor = strtolower(trim($color));

                    foreach ($sizesQuantities as $size => $quantity) {
                        // Kiểm tra lại lần nữa trước khi lưu vào DB
                        if (!is_numeric($quantity) || $quantity <= 0) {
                            throw new Exception("Số lượng không hợp lệ cho size {$size}");
                        }

                        $variantTotal = $quantity * $productData['new_price'];
                        $totalInventoryValue += $variantTotal;

                        // Find or create variant
                        $variant = ProductVariant::firstOrCreate([
                            'product_id' => $productData['product_id'],
                            'color' => $normalizedColor,
                            'size' => $size,
                        ], [
                            'stock' => 0,
                            'price' => $productData['new_price']
                        ]);

                        // Update price if different
                        if (!$variant->wasRecentlyCreated && $variant->price != $productData['new_price']) {
                            $variant->price = $productData['new_price'];
                            $variant->save();
                        }

                        // Create InventoryDetail
                        $inventoryDetail = new InventoryDetail();
                        $inventoryDetail->product_id = $productData['product_id'];
                        $inventoryDetail->inventory_id = $inventory->id;
                        $inventoryDetail->product_variant_id = $variant->id;
                        $inventoryDetail->price = $productData['new_price'];
                        $inventoryDetail->quantity = $quantity;
                        $inventoryDetail->size = $size . '-' . $quantity . '-' . $color;
                        $inventoryDetail->save();
                    }
                }
            }

            $inventory->total = $totalInventoryValue;
            $inventory->vat = $totalInventoryValue * $vatRate;
            $inventory->save();

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Yêu cầu nhập kho đã được tạo thành công và đang chờ duyệt!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in post_add_extra', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }


    // hàm test
    // public function post_add_extra(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Validate basic fields first
    //         $request->validate([
    //             'id' => 'required',
    //             'provider_id' => 'required|exists:providers,id',
    //         ], [
    //             'provider_id.required' => 'Vui lòng chọn nhà cung cấp.',
    //             'provider_id.exists' => 'Nhà cung cấp không hợp lệ.',
    //         ]);

    //         // Kiểm tra có ít nhất một loại sản phẩm nào được chọn
    //         if (!$request->has('products_to_add') && !$request->has('new_products')) {
    //             throw new Exception('Vui lòng chọn ít nhất một sản phẩm (có sẵn hoặc mới) để nhập kho');
    //         }

    //         // Tạo phiếu nhập
    //         $inventory = new Inventory();
    //         $inventory->provider_id = $request->provider_id;
    //         $inventory->staff_id = $request->id;
    //         $inventory->status = 'pending';
    //         $inventory->note = $request->input('note');
    //         $inventory->total = 0;
    //         $inventory->save();

    //         $totalInventoryValue = 0;
    //         $vatRate = 0.1;

    //         // Xử lý sản phẩm có sẵn
    //         if ($request->has('products_to_add')) {
    //             $productsToAdd = json_decode($request->input('products_to_add'), true);

    //             if (json_last_error() !== JSON_ERROR_NONE) {
    //                 throw new Exception('Định dạng JSON của products_to_add không hợp lệ');
    //             }

    //             foreach ($productsToAdd as $product) {
    //                 $validator = Validator::make($product, [
    //                     'product_id' => 'required|exists:products,id',
    //                     'new_price' => 'required|numeric|min:1',
    //                     'new_colors' => 'required|array|min:1',
    //                     'new_colors.*' => 'string|min:2',
    //                     'new_sizes_quantities' => 'required|array|min:1',
    //                 ], [
    //                     'product_id.required' => 'ID sản phẩm không hợp lệ.',
    //                     'new_price.required' => 'Vui lòng nhập giá nhập cho sản phẩm.',
    //                     'new_price.min' => 'Giá nhập phải lớn hơn 0.',
    //                     'new_colors.required' => 'Vui lòng chọn ít nhất một màu cho sản phẩm.',
    //                     'new_sizes_quantities.required' => 'Vui lòng chọn kích cỡ và số lượng cho sản phẩm.',
    //                 ]);

    //                 if ($validator->fails()) {
    //                     throw new Exception($validator->errors()->first());
    //                 }

    //                 // Kiểm tra trùng size
    //                 $sizes = array_keys($product['new_sizes_quantities']);
    //                 if (count($sizes) !== count(array_unique($sizes))) {
    //                     throw new Exception("Sản phẩm ID {$product['product_id']} có size bị trùng lặp");
    //                 }

    //                 // Xử lý từng biến thể
    //                 foreach ($product['new_colors'] as $color) {
    //                     $normalizedColor = strtolower(trim($color));

    //                     foreach ($product['new_sizes_quantities'] as $size => $quantity) {
    //                         if (!is_numeric($quantity) || $quantity <= 0) {
    //                             throw new Exception("Số lượng không hợp lệ cho size {$size}");
    //                         }

    //                         $variantTotal = $quantity * $product['new_price'];
    //                         $totalInventoryValue += $variantTotal;

    //                         // Tìm hoặc tạo biến thể
    //                         $variant = ProductVariant::firstOrCreate([
    //                             'product_id' => $product['product_id'],
    //                             'color' => $normalizedColor,
    //                             'size' => $size,
    //                         ], [
    //                             'stock' => 0,
    //                             'price' => $product['new_price']
    //                         ]);

    //                         // Cập nhật giá nếu khác
    //                         if (!$variant->wasRecentlyCreated && $variant->price != $product['new_price']) {
    //                             $variant->price = $product['new_price'];
    //                             $variant->save();
    //                         }

    //                         // Tạo chi tiết phiếu nhập
    //                         $this->createInventoryDetail(
    //                             $inventory->id,
    //                             $product['product_id'],
    //                             $variant->id,
    //                             $product['new_price'],
    //                             $quantity,
    //                             $size,
    //                             $color
    //                         );
    //                     }
    //                 }
    //             }
    //         }

    //         // Xử lý sản phẩm mới
    //         if ($request->has('new_products')) {
    //             $newProducts = json_decode($request->input('new_products'), true);

    //             if (json_last_error() !== JSON_ERROR_NONE) {
    //                 throw new Exception('Định dạng JSON của new_products không hợp lệ');
    //             }

    //             foreach ($newProducts as $newProduct) {
    //                 // Validate dữ liệu sản phẩm mới
    //                 if (empty($newProduct['name']) || empty($newProduct['variants']) || !is_numeric($newProduct['price']) || $newProduct['price'] <= 0) {
    //                     throw new Exception('Thông tin sản phẩm mới không hợp lệ');
    //                 }

    //                 // Tạo sản phẩm mới
    //                 $product = new Product();
    //                 $product->name = $newProduct['name'];
    //                 $product->category_id = $newProduct['category_id'];
    //                 $product->brand = $newProduct['brand'] ?? null;
    //                    // Process image if uploaded
    //                 // if (isset($newProductData['image'])) {
    //                 //     $this->processProductImage($product, $newProductData['image'], $cloudinaryService);
    //                 // }
    //                 $product->image = 'default-product-image.jpg'; // Xử lý upload ảnh sau
    //                 $product->save();

    //                 // Xử lý từng biến thể
    //                 foreach ($newProduct['variants'] as $variant) {
    //                     if (empty($variant['color']) || empty($variant['size']) || !is_numeric($variant['quantity']) || $variant['quantity'] <= 0) {
    //                         throw new Exception('Thông tin biến thể sản phẩm mới không hợp lệ');
    //                     }

    //                     $variantTotal = $variant['quantity'] * $newProduct['price'];
    //                     $totalInventoryValue += $variantTotal;

    //                     // Tạo biến thể mới
    //                     $productVariant = new ProductVariant();
    //                     $productVariant->product_id = $product->id;
    //                     $productVariant->color = strtolower(trim($variant['color']));
    //                     $productVariant->size = $variant['size'];
    //                     $productVariant->stock = 0; // Sẽ được cập nhật sau khi nhập kho
    //                     $productVariant->price = $newProduct['price'];
    //                     $productVariant->save();

    //                     // Tạo chi tiết phiếu nhập
    //                     $this->createInventoryDetail(
    //                         $inventory->id,
    //                         $product->id,
    //                         $productVariant->id,
    //                         $newProduct['price'],
    //                         $variant['quantity'],
    //                         $variant['size'],
    //                         $variant['color']
    //                     );
    //                 }
    //             }
    //         }

    //         // Cập nhật tổng giá trị phiếu nhập
    //         $inventory->total = $totalInventoryValue;
    //         $inventory->vat = $totalInventoryValue * $vatRate;
    //         $inventory->save();

    //         DB::commit();

    //         return redirect()->route('inventory.index')
    //             ->with('success', 'Yêu cầu nhập kho đã được tạo thành công và đang chờ duyệt!');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error in post_add_extra', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'request' => $request->all()
    //         ]);
    //         return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    //     }
    // }

    // // Hàm helper tạo chi tiết phiếu nhập
    // private function createInventoryDetail($inventoryId, $productId, $variantId, $price, $quantity, $size, $color)
    // {
    //     $inventoryDetail = new InventoryDetail();
    //     $inventoryDetail->product_id = $productId;
    //     $inventoryDetail->inventory_id = $inventoryId;
    //     $inventoryDetail->product_variant_id = $variantId;
    //     $inventoryDetail->price = $price;
    //     $inventoryDetail->quantity = $quantity;
    //     $inventoryDetail->size = $size . '-' . $quantity . '-' . $color;
    //     $inventoryDetail->save();

    //     return $inventoryDetail;
    // }


    // Hàm xử lý nâng cao nhập mới trong phiếu nhập th
    // public function post_add_extra(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $request->validate([
    //             'id' => 'required',
    //             'provider_id' => 'required|exists:providers,id',
    //         ]);

    //         $isAddingNewProducts = $request->has('new_products');
    //         $isAddingExistingProducts = $request->has('products_to_add');

    //         if (!$isAddingNewProducts && !$isAddingExistingProducts) {
    //             throw new Exception('Vui lòng chọn ít nhất một sản phẩm để nhập kho.');
    //         }

    //         // Create inventory
    //         $inventory = new Inventory();
    //         $inventory->provider_id = $request->provider_id;
    //         $inventory->staff_id = $request->id;
    //         $inventory->status = 'pending';
    //         $inventory->note = $request->note;
    //         $inventory->total = 0;
    //         $inventory->save();

    //         $totalInventoryValue = 0;
    //         $vatRate = 0.1;

    //         // Process existing products
    //         if ($isAddingExistingProducts) {
    //             $productsToAdd = json_decode($request->input('products_to_add'), true);

    //             if (json_last_error() !== JSON_ERROR_NONE) {
    //                 throw new Exception('Định dạng JSON của products_to_add không hợp lệ');
    //             }

    //             foreach ($productsToAdd as $productData) {
    //                 // Your existing product processing logic here
    //                 // (Keep your current code for processing existing products)
    //             }
    //         }

    //         // Process new products
    //         if ($isAddingNewProducts) {
    //             foreach ($request->new_products as $newProductData) {
    //                 // Validate new product data
    //                 $validator = Validator::make($newProductData, [
    //                     'name' => 'required|string|max:255',
    //                     'category_id' => 'required|exists:categories,id',
    //                     'brand' => 'required|string|max:100',
    //                     'price' => 'required|numeric|min:1',
    //                     'quantity' => 'required|integer|min:1',
    //                     'color' => 'required|string|max:50',
    //                     'size' => 'required|string|max:10',
    //                 ]);

    //                 if ($validator->fails()) {
    //                     throw new Exception($validator->errors()->first());
    //                 }

    //                 // Create new product
    //                 $product = new Product();
    //                 $product->name = $newProductData['name'];
    //                 $product->category_id = $newProductData['category_id'];
    //                 $product->brand = $newProductData['brand'];
    //                 $product->description = 'Nhập mới từ phiếu nhập kho';
    //                 $product->status = 0; // Active
    //                 $product->save();

    //                 // Process image if uploaded
    //                 if (isset($newProductData['image']) {
    //                     $this->processProductImage($product, $newProductData['image'], $cloudinaryService);
    //                 }

    //                 // Create product variant
    //                 $variant = new ProductVariant();
    //                 $variant->product_id = $product->id;
    //                 $variant->color = strtolower(trim($newProductData['color']));
    //                 $variant->size = $newProductData['size'];
    //                 $variant->price = $newProductData['price'];
    //                 $variant->stock = $newProductData['quantity'];
    //                 $variant->active = true;
    //                 $variant->save();

    //                 // Create inventory detail
    //                 $inventoryDetail = new InventoryDetail();
    //                 $inventoryDetail->product_id = $product->id;
    //                 $inventoryDetail->inventory_id = $inventory->id;
    //                 $inventoryDetail->product_variant_id = $variant->id;
    //                 $inventoryDetail->price = $newProductData['price'];
    //                 $inventoryDetail->quantity = $newProductData['quantity'];
    //                 $inventoryDetail->size = $newProductData['size'] . '-' . $newProductData['quantity'] . '-' . $newProductData['color'];
    //                 $inventoryDetail->save();

    //                 $totalInventoryValue += ($newProductData['price'] * $newProductData['quantity']);
    //             }
    //         }

    //         $inventory->total = $totalInventoryValue;
    //         $inventory->vat = $totalInventoryValue * $vatRate;
    //         $inventory->save();

    //         DB::commit();

    //         return redirect()->route('inventory.index')
    //             ->with('success', 'Yêu cầu nhập kho đã được tạo thành công!');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error in post_add_extra', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'request' => $request->all()
    //         ]);
    //         return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    //     }
    // }
}
