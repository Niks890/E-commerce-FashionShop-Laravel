<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryExtraResource;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
 // Lấy giá nhập gần nhất của các variant thuộc product
    public function lastPrices($productId)
    {
        // Lấy danh sách id các variant của sản phẩm này
        $variantIds = ProductVariant::where('product_id', $productId)->where('active', 1)->pluck('id');

        // Lấy các dòng nhập kho liên quan tới các variant này, mới nhất trước
        $details = InventoryDetail::whereIn('product_variant_id', $variantIds)
            ->orderByDesc('created_at')
            ->get();

        // Duyệt để lấy giá nhập gần nhất cho từng variant (theo product_variant_id)
        $variantPrices = [];
        foreach ($details as $detail) {
            $variantId = $detail->product_variant_id;
            if (!isset($variantPrices[$variantId])) {
                $variantPrices[$variantId] = [
                    'product_variant_id' => $variantId,
                    'color' => $detail->ProductVariant->color ?? null, // nếu có quan hệ variant
                    'size'  => $detail->ProductVariant->size ?? null,
                    'last_price' => $detail->price,
                ];
            }
        }

        return response()->json([
            'status_code' => 200,
            'variant_prices' => array_values($variantPrices),
        ]);
    }

    public function staff($id)
    {
        $staff = Staff::find($id);
        if ($staff) {
            return $this->apiStatus($staff, 200, 1, 'ok');
        }
        return $this->apiStatus(null, 404, 0, 'Data not found.');
    }

    public function discounts()
    {
        $discounts = Discount::orderBy('id', 'ASC')->get();
        return $this->apiStatus($discounts, 200, $discounts->count(), 'ok');
    }
    public function discount($id)
    {
        $discount = Discount::with(['products' => function ($query) {
            $query->select('id', 'product_name', 'price', 'image', 'discount_id');
        }])->find($id);

        if ($discount) {
            return $this->apiStatus($discount, 200, 1, 'ok');
        }
        return $this->apiStatus(null, 404, 0, 'Data not found.');
    }


    public function getDiscountByCode($code)
    {
        $discount = Discount::where('code', $code)->first();

        if (!empty($discount)) {
            return $this->apiStatus($discount, 200, 1, 'ok');
        }

        return $this->apiStatus(null, 404, 0, 'Data not found.');
    }


    public function checkVoucher($code)
    {
        $customerId = request()->input('customer_id');
        $voucher = Voucher::where('vouchers_code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Voucher not found'
            ], 404);
        }

        // Check dates
        $now = now();
        if ($now < $voucher->vouchers_start_date || $now > $voucher->vouchers_end_date) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Voucher not valid at this time'
            ], 400);
        }

        // Check total usage
        $usageCount = VoucherUsage::where('voucher_id', $voucher->id)
            ->whereNotNull('order_id') // Only count actually used vouchers
            ->count();

        if ($usageCount >= $voucher->vouchers_usage_limit) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Voucher usage limit reached'
            ], 400);
        }

        // Check customer usage if logged in
        $alreadyUsed = false;
        if ($customerId) {
            $alreadyUsed = VoucherUsage::where('voucher_id', $voucher->id)
                ->where('customer_id', $customerId)
                ->whereNotNull('order_id') // Only mark as used if actually used (has order_id)
                ->exists();
        }

        return response()->json([
            'status_code' => 200,
            'data' => [
                ...$voucher->toArray(),
                'usage_count' => $usageCount,
                'already_used' => $alreadyUsed
            ]
        ]);
    }

    public function categories()
    {
        $categories = Category::where('status', 1)
            ->withCount(['products' => function($query) {
                $query->where('status', 1);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('id', 'ASC')
            ->get();

        return $this->apiStatus($categories, 200, $categories->count(), 'ok');
    }



    public function inventories(Request $request)
    {
        $query = Inventory::with([
            'Staff',
            'Provider',
            'InventoryDetails.Product.Category',
            'InventoryDetails.ProductVariant'
        ])
            ->orderBy('id', 'DESC');

        // Lọc theo từ khóa tìm kiếm (ID phiếu nhập, tên nhân viên, tên sản phẩm)
        if ($request->has('query') && !empty($request->input('query'))) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', $searchTerm)
                    ->orWhereHas('Staff', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('InventoryDetails.Product', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('product_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Lọc theo trạng thái
        if ($request->has('status') && !empty($request->input('status'))) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // Lọc theo ngày tạo (created_at)
        if ($request->has('start_date') && !empty($request->input('start_date'))) {
            $startDate = $request->input('start_date');
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($request->has('end_date') && !empty($request->input('end_date'))) {
            $endDate = $request->input('end_date');
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Lọc theo nhà cung cấp
        if ($request->has('provider_id') && !empty($request->input('provider_id'))) {
            $providerId = $request->input('provider_id');
            $query->where('provider_id', $providerId);
        }

        $inventories = $query->paginate(10);

        return response()->json([
            'status_code' => 200,
            'data' => InventoryResource::collection($inventories),
            'pagination' => [
                'current_page' => $inventories->currentPage(),
                'last_page' => $inventories->lastPage(),
                'total' => $inventories->total(),
                'per_page' => $inventories->perPage(),
                'next_page_url' => $inventories->nextPageUrl(),
                'prev_page_url' => $inventories->previousPageUrl(),
            ],
        ]);
    }



    public function inventory($id)
    {
            $inventory = Inventory::with([
                'Staff',
                'Provider',
                'InventoryDetails.Product.Category',
                'InventoryDetails.Product.activeVariants'

            ])->find($id);

        if ($inventory) {
            $inventoryResource = new InventoryExtraResource($inventory);
            return $this->apiStatus($inventoryResource, 200, 1, 'ok');
        } else {
            return $this->apiStatus(null, 404, 0, 'Data not found.');
        }
    }



    public function inventoryDetail($id)
    {
        $inventories = Inventory::with([
            'Staff',
            'Provider',
            'InventoryDetails.Product.Category',
            'InventoryDetails.Product.ProductVariants',
        ])->find($id);
        $inventoriesResource = new InventoryResource($inventories);
        if ($inventories) {
            return $this->apiStatus($inventoriesResource, 200, 1, 'ok');
        } else {
            return $this->apiStatus(null, 404, 0, 'Data not found.');
        }
    }

    public function products()
    {
        $products = Product::orderBy('id', 'ASC')->paginate(5);
        return $this->apiStatus($products, 200, $products->count(), 'ok');
    }

    //http://127.0.0.1:8000/product-variant/{color}/{product-id}

    public function productVariantSizes(Request $request)
    {
        $productVariants = ProductVariant::where('color', $request->color)
            ->where('product_id', $request->product_id)->get();
        return $this->apiStatus($productVariants, 200, $productVariants->count(), 'ok');
    }

    public function getSeletedProductVariant(Request $request)
    {
        $productVariants = ProductVariant::where('color', $request->color)
            ->where('product_id', $request->product_id)
            ->where('size', $request->size)->first();
        return $this->apiStatus($productVariants, 200, 1, 'ok');
    }



    public function getProductsClient(Request $request)
    {
        $perPage = $request->input('per_page', 8); // Default to 8 items per page
        $products = Product::with(['Discount', 'ProductVariants'])
            ->withCount(['comments as comments_count'])
            ->withAvg('comments as star', 'star')
            ->withCount('comments as comments_count')
            ->orderBy('id', 'ASC')
            ->where('status', 1)
            ->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }




    public function product($id)
    {
        // $products = Product::with('Category', 'ProductVariants', 'Discount')->find($id);
        $products = Product::with([
            'Category',
            'Discount',
            'ProductVariants' => function ($query) {
                $query->where('active', true);
            }
        ])->find($id);
        $productResource = new ProductResource($products);
        if ($productResource) {
            return $this->apiStatus($productResource, 200, 1, 'ok');
        } else {
            return $this->apiStatus(null, 404, 0, 'Data not found.');
        }
    }

    public function brands()
    {
        $brands = DB::select('SELECT DISTINCT brand FROM products');
        return $this->apiStatus($brands, 200, 0, 'ok');
    }

    public function productVariants()
    {
        $productVariants = ProductVariant::orderBy('id', 'ASC')->paginate(2);
        return $this->apiStatus($productVariants, 200, $productVariants->count(), 'ok');
    }




    public function getProductDiscount()
    {
        $now = Carbon::now();
        $products = Product::with('Discount', 'ProductVariants', 'comments')
            ->whereHas('Discount', function ($query) use ($now) {
                $query->where('status', 'active')
                    ->whereDate('start_date', '<=', $now)
                    ->whereDate('end_date', '>=', $now);
            })
            ->where('status', 1)
            ->paginate(5);
        $productResource = ProductResource::collection($products);
        return $this->apiStatus($productResource, 200, 1, 'ok');
    }



    public function blogDetail($id)
    {
        $blog = Blog::with('staff')->find($id);
        if ($blog) {
            return $this->apiStatus($blog, 200, 1, 'ok');
        }
        return $this->apiStatus(null, 404, 0, 'Data not found.');
    }

    public function rateOrder($id)
    {
        $orderDetail = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->join('order_details as od', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->leftJoin('comments as r', function ($join) {
                $join->on('r.product_id', '=', 'p.id')
                    ->on('r.customer_id', '=', 'c.id')
                    ->on('r.order_id', '=', 'o.id'); // Thêm điều kiện order_id vào on
            })
            ->where('o.id', $id)
            ->select(
                'o.id as order_id',
                'r.*',
                'c.name as customer_name',
                'p.product_name as product_name',
                'p.id as product_id',
                'p.image',
                'pv.size',
                'pv.color'
            )
            ->distinct()
            ->get();
        if ($orderDetail) {
            return $this->apiStatus($orderDetail, 200, 1, 'ok');
        }
        return $this->apiStatus(null, 404, 0, 'Data not found.');
    }
}
