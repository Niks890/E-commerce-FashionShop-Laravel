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


    // Hàm nhập (form mới)
    public function store(Request $request, CloudinaryService $cloudinaryService)
    {
        DB::beginTransaction();
        try {
            Log::info('Data received for inventory creation:', $request->all());

            // 1. VALIDATION: Cập nhật quy tắc để khớp với cấu trúc mới
            $data = $request->validate([
                'id' => 'required|exists:staff,id',
                'provider_id' => 'required|exists:providers,id',
                'note_inventory' => 'required|string',
                'products' => 'required|array|min:1',
                'products.*.product_name' => 'required|min:3|max:150|distinct', // Sử dụng distinct để kiểm tra trong cùng request
                'products.*.brand_name' => 'required|max:100',
                'products.*.image' => 'required',
                'products.*.category_id' => 'required|exists:categories,id',
                'products.*.price' => 'required|numeric|min:1',
                'products.*.variants' => 'required|array|min:1',
                'products.*.variants.*.color' => 'required|string',
                'products.*.variants.*.details' => 'required|array|min:1', // Mỗi màu phải có ít nhất 1 size
                'products.*.variants.*.details.*.size' => 'required|string',
                'products.*.variants.*.details.*.quantity' => 'required|integer|min:1',
            ], [
                'products.*.product_name.required' => 'Tên sản phẩm không được để trống.',
                'products.*.product_name.distinct' => 'Tên sản phẩm không được lặp lại trong cùng một phiếu nhập.',
                'products.*.variants.min' => 'Mỗi sản phẩm phải có ít nhất một màu.',
                'products.*.variants.*.details.min' => 'Mỗi màu phải có ít nhất một cặp Size/Số lượng.',
                'products.*.variants.*.details.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
            ]);

            // Tạo phiếu nhập kho
            $inventory = Inventory::create([
                'provider_id' => $data['provider_id'],
                'staff_id' => $data['id'],
                'total' => 0, // Sẽ cập nhật sau
                'vat' => 0, // Sẽ cập nhật sau
                'status' => 'pending', // Trạng thái chờ duyệt
                'note' => $data['note_inventory'],
            ]);

            $totalInventoryValue = 0;
            $totalVat = 0;
            $productsToProcessImages = [];
            $inventoryDetailBatchInsert = [];
            $vatRate = 0.1; // 10% VAT

            // 2. VÒNG LẶP: Lặp qua từng sản phẩm
            foreach ($data['products'] as $index => $productData) {
                // Kiểm tra tên sản phẩm đã tồn tại trong DB chưa
                if (Product::where('product_name', $productData['product_name'])->exists()) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', "Tên sản phẩm '{$productData['product_name']}' đã tồn tại trong hệ thống.");
                }

                // Tạo sản phẩm
                $product = Product::create([
                    'product_name' => $productData['product_name'],
                    'price' => $productData['price'],
                    'brand' => $productData['brand_name'],
                    'status' => 2, // Chưa active
                    'sku' => $this->generateSku($productData['product_name'], $productData['category_id'], $productData['brand_name']),
                    'category_id' => $productData['category_id'],
                    'image' => 'temp_placeholder', // Tạm thời
                    'slug' => Str::slug($productData['product_name'])
                ]);

                $productTotalQuantity = 0;

                // Lặp qua từng màu sắc (variant)
                foreach ($productData['variants'] as $variantData) {
                    // Lặp qua từng cặp size/số lượng (details)
                    foreach ($variantData['details'] as $detail) {
                        $quantity = (int)$detail['quantity'];
                        $productTotalQuantity += $quantity;

                        // Tạo biến thể sản phẩm cho mỗi cặp màu-size
                        $productVariant = ProductVariant::create([
                            'color' => $variantData['color'],
                            'size' => $detail['size'],
                            'price' => $productData['price'], // Giả sử giá nhập là giá của sản phẩm
                            'stock' => 0, // Khởi tạo stock = 0, sẽ cập nhật khi duyệt
                            'product_id' => $product->id,
                        ]);

                        // Chuẩn bị dữ liệu cho batch insert chi tiết phiếu nhập
                        $inventoryDetailBatchInsert[] = [
                            'product_variant_id' => $productVariant->id,
                            'product_id' => $product->id,
                            'inventory_id' => $inventory->id,
                            'price' => $productData['price'],
                            'quantity' => $quantity,
                            'size' => $detail['size'] . '-' . $quantity . '-' . $variantData['color'], // Mô tả chi tiết
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

                // Tính toán tổng giá trị cho sản phẩm này
                $productTotalValue = $productTotalQuantity * $productData['price'];
                $productVat = $productTotalValue * $vatRate;
                $totalInventoryValue += $productTotalValue + $productVat;
                $totalVat += $productVat;

                // Lưu thông tin để xử lý ảnh sau (sử dụng index từ request gốc)
                if ($request->hasFile("products.{$index}.image")) {
                    $productsToProcessImages[] = [
                        'product' => $product,
                        'image' => $request->file("products.{$index}.image")
                    ];
                }
            }

            // Batch insert inventory details để tăng hiệu suất
            if (!empty($inventoryDetailBatchInsert)) {
                InventoryDetail::insert($inventoryDetailBatchInsert);
            }

            // Cập nhật tổng giá trị và VAT cho phiếu nhập
            $inventory->update([
                'total' => $totalInventoryValue,
                'vat' => $totalVat
            ]);

            DB::commit();

            // Xử lý upload ảnh sau khi transaction đã thành công
            foreach ($productsToProcessImages as $item) {
                $this->processProductImage($item['product'], $item['image'], $cloudinaryService);
            }

            Log::info('Inventory created successfully:', [
                'inventory_id' => $inventory->id,
                'total_products' => count($data['products'])
            ]);

            return redirect()->route('inventory.index')->with('success', "Thêm phiếu nhập mới thành công với " . count($data['products']) . " sản phẩm! Phiếu nhập đang chờ duyệt.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error creating inventory:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            // Quay lại với lỗi và dữ liệu đã nhập
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating inventory:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage());
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



    public function add_extra()
    {
        $categories = Category::all();
        $providers = Provider::all();
        $products = Product::with(['category', 'productVariants'])->get();
        return view('admin.inventory.add-extra', compact('providers', 'categories', 'products'));
    }
    // Nhập đc nhiều sp, nhiều size, nhiều màu, nhiều kích thước
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



    public function getProductWithVariants($id)
    {

        $product = Product::with([
        'category',
        'productVariants' => function ($query) {
            $query->where('active', 1);
        }
        ])->find($id);


        if (!$product) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        // Transform the data to match frontend expectations
        $response = [
            'id' => $product->id,
            'name' => $product->product_name,
            'image' => $product->image, // Make sure this field exists
            'category' => [
                'name' => $product->category->category_name ?? 'N/A'
            ],
            'brand' => $product->brand,
            'product-variant' => $product->productVariants->map(function ($variant) {
                return [
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'stock' => $variant->available_stock
                ];
            })
        ];

        return response()->json([
            'status_code' => 200,
            'data' => $response
        ]);
    }

    /**
     * Tìm kiếm sản phẩm (dùng cho Select2)
     */
    public function searchProducts(Request $request)
    {
        $query = Product::query()->whereIn('status', [0, 1]);

        if ($request->has('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        $products = $query->select('id', 'product_name as text')->limit(10)->get();

        return response()->json([
            'status_code' => 200,
            'results' => $products // Select2 expects 'results' key
        ]);
    }


    // Thêm vào InventoryController
    public function getAllProductsWithVariants()
    {
         $products = Product::with([
        'category',
        'productVariants' => function ($query) {
            $query->where('active', 1);
        }
        ])->get();

        $transformedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'image' => $product->image,
                'category' => [
                    'name' => $product->category->category_name ?? null
                ],
                'brand' => $product->brand,
                'product-variant' => $product->productVariants->map(function ($variant) {
                    return [
                        'color' => $variant->color,
                        'size' => $variant->size,
                        'stock' => $variant->stock
                    ];
                })
            ];
        });

        return response()->json([
            'status_code' => 200,
            'data' => $transformedProducts
        ]);
    }
}
