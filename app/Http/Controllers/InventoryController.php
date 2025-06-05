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
        $providers = Provider::all();
        return view('admin.inventory.add-extra', compact('providers'));
    }


    public function post_add_extra(Request $request)
    {
        Log::debug('Bắt đầu post_add_extra', ['request_data' => $request->all()]);

        DB::beginTransaction();

        try {
            // Bước 1: Giải mã JSON products_to_add
            $productsToAdd = json_decode($request->input('products_to_add'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Định dạng JSON của products_to_add không hợp lệ');
            }

            // Bước 2: Gộp dữ liệu đã giải mã vào mảng products
            $mergedProducts = [];
            foreach ($request->input('products') as $index => $product) {
                if (isset($productsToAdd[$index])) {
                    $mergedProduct = array_merge($product, $productsToAdd[$index]);
                    $mergedProducts[] = $mergedProduct;
                } else {
                    Log::warning('Không tìm thấy dữ liệu products_to_add tương ứng cho sản phẩm tại index: ' . $index);
                    $mergedProducts[] = $product;
                }
            }

            // Bước 3: Thay thế dữ liệu products bằng dữ liệu đã gộp
            $request->merge(['products' => $mergedProducts]);

            // Bước 4: Validate dữ liệu request
            $validated = $request->validate([
                'id' => 'required', // Giả định 'id' là staff_id
                'provider_id' => 'required|exists:providers,id',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.new_price' => 'required|numeric|min:1',
                'products.*.new_color' => 'required|string|min:2',
                'products.*.formatted_new_sizes' => 'required|string',
            ], [
                'provider_id.required' => 'Vui lòng chọn nhà cung cấp.',
                'provider_id.exists' => 'Nhà cung cấp không hợp lệ.',
                'products.required' => 'Vui lòng chọn ít nhất một sản phẩm để nhập thêm.',
                'products.*.new_price.required' => 'Vui lòng nhập giá nhập cho sản phẩm.',
                'products.*.new_price.min' => 'Giá nhập phải lớn hơn 0.',
                'products.*.new_color.required' => 'Vui lòng nhập màu sắc cho sản phẩm.',
                'products.*.formatted_new_sizes.required' => 'Vui lòng chọn kích cỡ và số lượng cho sản phẩm.',
            ]);

            Log::debug('Validation đã vượt qua', ['validated_data' => $validated]);

            // Bước 5: Tạo bản ghi nhập kho mới (Inventory)
            $inventory = new Inventory();
            $inventory->provider_id = $validated['provider_id'];
            $inventory->staff_id = $validated['id'];
            $inventory->status = 'pending'; // Đặt trạng thái là 'pending' (chờ duyệt)
            $inventory->note = 'yêu cầu nhập thêm sản phẩm'; // Cập nhật ghi chú cho rõ ràng
            $inventory->total = 0; // Sẽ cập nhật sau khi tính toán tổng
            $inventory->save();

            Log::debug('Đã tạo phiếu nhập kho với trạng thái chờ duyệt', ['inventory_id' => $inventory->id]);

            $totalInventoryValue = 0;

            // Bước 6: Xử lý từng sản phẩm đã được validate
            foreach ($validated['products'] as $productData) {
                Log::debug("Đang xử lý sản phẩm", ['product_data' => $productData]);

                $sizeEntries = explode(',', $productData['formatted_new_sizes']);
                Log::debug('Các mục kích cỡ', ['entries' => $sizeEntries]);

                foreach ($sizeEntries as $entry) {
                    if (empty($entry)) continue;

                    try {
                        list($size, $quantity) = explode('-', $entry);
                        $size = trim($size);
                        $quantity = (int)trim($quantity);

                        if ($quantity <= 0) {
                            throw new Exception("Số lượng cho kích cỡ {$size} của sản phẩm có ID {$productData['product_id']} phải lớn hơn 0.");
                        }

                        Log::debug('Đã xử lý mục kích cỡ', ['size' => $size, 'quantity' => $quantity]);

                        $variantTotal = $quantity * $productData['new_price'];
                        $totalInventoryValue += $variantTotal;

                        Log::debug('Tổng giá trị biến thể đã tính toán', ['variant_total' => $variantTotal, 'running_total' => $totalInventoryValue]);

                        // Tìm hoặc tạo ProductVariant
                        // Quan trọng: Chúng ta CHỈ firstOrCreate ProductVariant ở đây,
                        // KHÔNG CẬP NHẬT STOCK ngay lập tức.
                        $variant = ProductVariant::firstOrCreate([
                            'product_id' => $productData['product_id'],
                            'color' => $productData['new_color'],
                            'size' => $size,
                        ]);
                        // Log::debug('Đã tìm/tạo biến thể nhưng chưa cập nhật stock', ['variant_id' => $variant->id]);
                        // Gán stock ban đầu là 0 nếu mới tạo, hoặc giữ nguyên nếu đã tồn tại.
                        // Việc tăng stock sẽ được thực hiện khi phiếu nhập được duyệt.
                        if (!$variant->exists) {
                            $variant->stock = 0; // Đặt stock ban đầu là 0 nếu mới tạo
                            $variant->save(); // Lưu lại để đảm bảo variant có ID
                        }


                        // Tạo bản ghi InventoryDetail cho TỪNG BIẾN THỂ CỤ THỂ
                        $inventoryDetail = new InventoryDetail();
                        $inventoryDetail->product_id = $productData['product_id'];
                        $inventoryDetail->inventory_id = $inventory->id;
                        $inventoryDetail->product_variant_id = $variant->id; // Gán ID của ProductVariant
                        $inventoryDetail->price = $productData['new_price'];
                        $inventoryDetail->quantity = $quantity; // Số lượng cho biến thể này (đang chờ duyệt)
                        $inventoryDetail->size = $size . '-' . $quantity . '-' . $productData['new_color'];
                        $inventoryDetail->save();

                        Log::debug('Đã tạo chi tiết nhập kho cho biến thể', ['detail_id' => $inventoryDetail->id, 'product_variant_id' => $variant->id]);

                    } catch (Exception $e) {
                        throw $e; // Ném ngoại lệ để kích hoạt rollback
                    }
                }
            }

            // Bước 7: Cập nhật tổng giá trị cho bản ghi Inventory chính
            $inventory->total = $totalInventoryValue;
            $inventory->save();

            DB::commit();
            Log::debug('Đã commit transaction cho phiếu nhập mới', ['inventory_id' => $inventory->id]);

            return redirect()->route('inventory.index')
                ->with('success', 'Yêu cầu nhập kho đã được tạo thành công và đang chờ duyệt!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi trong post_add_extra, đã rollback transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }


    public function approve($id)
    {
        DB::beginTransaction();
        try {
            // Nạp phiếu nhập cùng với chi tiết và biến thể sản phẩm
            $inventory = Inventory::with('InventoryDetails.ProductVariant')->findOrFail($id);

            // Kiểm tra nếu phiếu nhập đã được duyệt rồi hoặc bị từ chối
            if ($inventory->status === 'approved') {
                DB::rollBack(); // Đảm bảo rollback nếu có beginTransaction trước đó
                return back()->with('warning', 'Phiếu nhập này đã được duyệt trước đó.');
            }
            if ($inventory->status === 'rejected') {
                DB::rollBack();
                return back()->with('error', 'Phiếu nhập này đã bị từ chối, không thể duyệt.');
            }

            // Cập nhật stock cho từng biến thể sản phẩm
            foreach ($inventory->InventoryDetails as $detail) {
                if ($detail->ProductVariant) {
                    // Cập nhật số lượng tồn kho bằng cách tăng lên
                    $detail->ProductVariant->increment('stock', $detail->quantity);
                    Log::debug('Đã cập nhật stock cho biến thể', [
                        'variant_id' => $detail->ProductVariant->id,
                        'added_quantity' => $detail->quantity,
                        'new_stock' => $detail->ProductVariant->stock
                    ]);
                } else {
                    // Xử lý trường hợp không tìm thấy ProductVariant (có thể do lỗi dữ liệu)
                    // Ném lỗi để rollback toàn bộ transaction
                    throw new Exception('Không tìm thấy biến thể sản phẩm cho chi tiết nhập kho ID: ' . $detail->id);
                }
            }

            // Cập nhật trạng thái phiếu nhập
            $inventory->update([
                'status' => 'approved',
                'updated_at' => now(),
                'staff_id' => auth()->user()->id // Sử dụng ID của người duyệt hiện tại
            ]);
            Log::debug('Đã cập nhật trạng thái phiếu nhập thành duyệt', ['inventory_id' => $inventory->id]);

            // Kích hoạt các sản phẩm liên quan (nếu trạng thái 0 = hiển thị, 3 = ẩn)
            // Dựa trên comment của bạn "0 = ẩn trong kho", có vẻ bạn muốn đặt nó thành hiển thị
            // Giả sử status = 0 là hiển thị (hoặc một giá trị khác tùy ý định của bạn)
            Product::whereIn('id', $inventory->InventoryDetails->pluck('product_id')->unique())
                ->update(['status' => 1]); // Ví dụ: 1 là trạng thái hiển thị
            Log::debug('Đã cập nhật trạng thái sản phẩm liên quan', ['product_ids' => $inventory->InventoryDetails->pluck('product_id')->unique()]);


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
            $inventory = Inventory::findOrFail($id);

            // Kiểm tra trạng thái hợp lệ để từ chối
            if ($inventory->status !== 'pending') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Chỉ có thể huỷ phiếu nhập ở trạng thái chờ duyệt');
            }

            $inventory->update([
                'status' => 'rejected',
                'updated_at' => now(),
                'staff_id' => auth()->user()->id, // Sử dụng ID của người từ chối hiện tại
                'note' => $request->note
            ]);
            Log::debug('Đã cập nhật trạng thái phiếu nhập thành từ chối', ['inventory_id' => $inventory->id]);

            // Trong trường hợp từ chối, bạn có thể không cần cập nhật trạng thái Product
            // vì chúng chưa được thêm vào kho. Tuy nhiên, nếu bạn muốn đặt chúng về một trạng thái ẩn
            // (ví dụ: 'unlisted' hoặc 'inactive'), bạn có thể giữ lại đoạn này.
            // Nếu sản phẩm này hoàn toàn mới và chưa từng tồn tại, việc reject không cần làm gì với Product.
            // Nếu sản phẩm có thể đã tồn tại và bạn muốn đảm bảo nó không được hiển thị, thì giữ lại.
            // Giả sử bạn muốn ẩn chúng (status 3 = ẩn) khi phiếu nhập bị từ chối
            $productIds = InventoryDetail::where('inventory_id', $id)
                ->pluck('product_id')
                ->unique();

            Product::whereIn('id', $productIds)
                ->update(['status' => 3]); // 3 = ẩn/không niêm yết
            Log::debug('Đã cập nhật trạng thái sản phẩm liên quan khi từ chối', ['product_ids' => $productIds]);

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Đã huỷ phiếu nhập thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra khi huỷ phiếu nhập', [
                'inventory_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
