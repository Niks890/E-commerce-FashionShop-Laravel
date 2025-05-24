<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{


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
        $discount = Discount::find($id);
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


    public function categories()
    {
        // $categories = Category::orderBy('id', 'ASC')->get();
        $categories = Category::withCount('products')->get();
        return $this->apiStatus($categories, 200, $categories->count(), 'ok');
    }



// public function inventories(Request $request)
// {
//     $query = Inventory::with([
//         'Staff',
//         'Provider',
//         'InventoryDetails.Product.Category',
//         'InventoryDetails.Product.ProductVariants'
//     ])->orderBy('id', 'DESC');

//     // Thêm điều kiện tìm kiếm nếu có tham số 'query' trong request
//     if ($request->has('query') && !empty($request->input('query'))) {
//         $searchTerm = $request->input('query');
//         $query->where(function ($q) use ($searchTerm) {
//             // Tìm kiếm theo ID phiếu nhập (chính xác)
//             $q->where('id', $searchTerm)
//               // Tìm kiếm theo tên nhân viên (gần đúng)
//               ->orWhereHas('Staff', function ($subQuery) use ($searchTerm) {
//                   $subQuery->where('name', 'like', '%' . $searchTerm . '%');
//               })
//               // THÊM ĐIỀU KIỆN NÀY ĐỂ TÌM KIẾM THEO TÊN SẢN PHẨM
//               ->orWhereHas('InventoryDetails.Product', function ($subQuery) use ($searchTerm) {
//                   $subQuery->where('product_name', 'like', '%' . $searchTerm . '%');
//               });
//         });
//     }

//     $inventories = $query->paginate(10);

//     return response()->json([
//         'status_code' => 200,
//         'data' => InventoryResource::collection($inventories),
//         'pagination' => [
//             'current_page' => $inventories->currentPage(),
//             'last_page' => $inventories->lastPage(),
//             'total' => $inventories->total(),
//             'per_page' => $inventories->perPage(),
//             'next_page_url' => $inventories->nextPageUrl(),
//             'prev_page_url' => $inventories->previousPageUrl(),
//         ],
//     ]);
// }

 public function inventories(Request $request)
    {
        $query = Inventory::with([
            'Staff',
            'Provider',
            'InventoryDetails.Product.Category',
            'InventoryDetails.Product.ProductVariants'
        ])->orderBy('id', 'DESC');

        // Lọc theo từ khóa tìm kiếm (ID phiếu nhập, tên nhân viên, tên sản phẩm)
        if ($request->has('query') && !empty($request->input('query'))) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                // Tìm kiếm theo ID phiếu nhập (chính xác)
                $q->where('id', $searchTerm)
                  // Tìm kiếm theo tên nhân viên (gần đúng)
                  ->orWhereHas('Staff', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  // Tìm kiếm theo tên sản phẩm (gần đúng)
                  ->orWhereHas('InventoryDetails.Product', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('product_name', 'like', '%' . $searchTerm . '%');
                  });
            });
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
        $inventories = Inventory::with([
            'Staff',
            'Provider',
            'InventoryDetails.Product.Category',
            'InventoryDetails.Product.ProductVariants'
        ])->find($id);
        $inventoriesResource = new InventoryResource($inventories);
        if ($inventories) {
            return $this->apiStatus($inventoriesResource, 200, 1, 'ok');
        } else {
            return $this->apiStatus(null, 404, 0, 'Data not found.');
        }
    }

    // public function inventoriesSearch(Request $request)
    // {
    //     $query = Inventory::with([
    //         'Staff',
    //         'Provider',
    //         'InventoryDetails.Product.Category',
    //         'InventoryDetails.Product.ProductVariants'
    //     ]);

    //     if ($request->has('id')) {
    //         $query->where('id', $request->id);
    //     }

    //     if ($request->has('staff_name')) {
    //         $query->whereHas('Staff', function ($q) use ($request) {
    //             $q->where('name', 'like', '%' . $request->staff_name . '%');
    //         });
    //     }

    //     $inventories = $query->paginate(10);

    //     return response()->json([
    //         'status_code' => 200,
    //         'message' => 'Thành công',
    //         'data' => InventoryResource::collection($inventories),
    //         'pagination' => [
    //             'current_page' => $inventories->currentPage(),
    //             'last_page' => $inventories->lastPage(),
    //             'total' => $inventories->total(),
    //             'per_page' => $inventories->perPage(),
    //             'next_page_url' => $inventories->nextPageUrl(),
    //             'prev_page_url' => $inventories->previousPageUrl(),
    //         ]
    //     ]);
    // }


    public function inventoryDetail($id)
    {
        $inventories = Inventory::with([
            'Staff',
            'Provider',
            'InventoryDetails.Product.Category',
            'InventoryDetails.Product.ProductVariants'
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


    public function getProductsClient()
    {
        $products = Product::with('Discount', 'ProductVariants')->orderBy('id', 'ASC')->get();
        return $this->apiStatus($products, 200, $products->count(), 'ok');
    }

    // public function getProductsClient()
    // {
    //     $products = Product::with('Discount', 'ProductVariants')
    //         ->orderBy('id', 'ASC')
    //         ->paginate(10); // mỗi trang 10 sản phẩm, bạn tùy chỉnh số này

    //     // Bạn có thể trả về cả dữ liệu phân trang đầy đủ để frontend biết tổng số trang, trang hiện tại, ...
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $products->items(),
    //         'current_page' => $products->currentPage(),
    //         'last_page' => $products->lastPage(),
    //         'per_page' => $products->perPage(),
    //         'total' => $products->total(),
    //     ], 200);
    // }


    public function product($id)
    {
        $products = Product::with('Category', 'ProductVariants')->find($id);
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

    // public function test($id){
    //     $data = DB::table('orders as o')
    //     ->join('customers as c', 'o.customer_id', '=', 'c.id')
    //     ->join('order_details as od', 'o.id', '=', 'od.order_id')
    //     ->join('products as p', 'p.id', '=', 'od.product_id')
    //     ->join('product_variants as pv', 'pv.product_id', '=', 'p.id')
    //     ->where('o.id', $id)
    //     ->select('o.*', 'c.name as customer_name', 'p.product_name as product_name', 'pv.size', 'pv.color', 'od.quantity', 'od.price')
    //     ->get();
    //     return $this->apiStatus($data, 200, 1, 'ok');
    // }


    public function getProductDiscount()
    {
        $products = Product::with('Discount', 'ProductVariants')->whereNotNull('discount_id')->paginate(5);
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
