<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\ProductRecent;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class HomeController extends Controller
{
    public function home()
    {
        if (Session::has('success_payment')) {
            Session::forget('success_payment');
        }

        // xử lý product recent lưu vào 1 mảng và truyền vào view
        $productRecentInfo = [];
        if (Session::has('product_recent') && count(Session::get('product_recent')) > 0) {
            foreach (Session::get('product_recent') as $item) {
                $product = Product::with('ProductVariants', 'Discount', 'comments')->find($item->id_recent);
                if ($product) {
                    $productRecentInfo[] = $product;
                }
            }
        }
        $data = Blog::with('staff')->paginate(5);


        $highestDiscountProduct = Product::whereHas('discount', function ($query) {
            $query->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        })
            ->with('discount')
            ->get()
            ->sortByDesc(function ($product) {
                return $product->discount->percent_discount;
            })
            ->first();

        // Format lại ngày nếu cần
        if ($highestDiscountProduct && $highestDiscountProduct->discount) {
            $highestDiscountProduct->discount->formatted_end_date = $highestDiscountProduct->discount->end_date->format('Y-m-d H:i:s');
        }
        return view('sites.home.index', compact('data', 'productRecentInfo', 'highestDiscountProduct'));
    }

    // Lọc theo màu, khuyến mãi
    public function shop(Request $request)
    {
        // Khởi tạo query
        $query = Product::with('category', 'Discount', 'ProductVariants', 'comments')->where('status', 1);

        // Tìm kiếm theo tên sản phẩm
        if ($request->has('q')) {
            $search = $request->q;
            $query->where('product_name', 'LIKE', "%$search%");
        }

        // Lọc theo danh mục
        if ($request->has('category')) {
            $categoryName = $request->category;
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('category_name', $categoryName);
            });
        }

        // Lọc theo thương hiệu
        if ($request->has('brand')) {
            $brandName = $request->brand;
            $query->where('brand', $brandName);
        }

        // Lọc theo khoảng giá
        if ($request->has('price')) {
            $price = $request->price;
            if (strpos($price, '-') !== false) {
                [$min, $max] = explode('-', $price);
                $minPrice = str_replace('.', '', $min);
                $maxPrice = str_replace('.', '', $max);
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            } else {
                $minPrice = str_replace('.', '', $price);
                $query->where('price', '>=', $minPrice);
            }
        }

        // Lọc theo tag
        if ($request->has('tag')) {
            $tag = str_replace('-', ' ', $request->tag);
            $query->where('tags', 'like', "%$tag%");
        }

        // Lọc theo màu sắc (variant)
        if ($request->has('color')) {
            $color = $request->color;
            $query->whereHas('ProductVariants', function ($q) use ($color) {
                $q->where('color', $color);
            });
        }

        // Lọc theo sản phẩm đang có khuyến mãi còn hạn
        if ($request->has('promotion')) {
            $currentDate = now()->format('Y-m-d');
            $query->whereHas('Discount', function ($q) use ($currentDate) {
                $q->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate);
            });
        }

        // Sắp xếp
        $sortBy = $request->input('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Phân trang và giữ lại các tham số lọc
        $products = $query->paginate(12)->appends($request->query());

        // Lấy dữ liệu cho các bộ lọc
        $categories = Category::all();
        $brands = Product::where('status', 1)->distinct()->pluck('brand');
        $priceRanges = [
            '0-1000000' => 'Dưới 1 triệu',
            '1000000-5000000' => '1 - 5 triệu',
            '5000000-10000000' => '5 - 10 triệu',
            '10000000' => 'Trên 10 triệu'
        ];

        // Lấy danh sách các màu sắc có sẵn
        $colors = ProductVariant::select('color')
            ->distinct()
            ->pluck('color');

        return view("sites.shop.shop", compact('products', 'categories', 'brands', 'priceRanges', 'sortBy', 'colors'));
    }


    // lọc nâng cao theo size, theo bán chạy
    // public function shop(Request $request)
    // {
    //     // Khởi tạo query
    //     $query = Product::with('category', 'Discount', 'ProductVariants', 'comments')->where('status', 1);

    //     // Tìm kiếm theo tên sản phẩm
    //     if ($request->has('q')) {
    //         $search = $request->q;
    //         $query->where('product_name', 'LIKE', "%$search%");
    //     }

    //     // Lọc theo danh mục
    //     if ($request->has('category')) {
    //         $categoryName = $request->category;
    //         $query->whereHas('category', function ($q) use ($categoryName) {
    //             $q->where('category_name', $categoryName);
    //         });
    //     }

    //     // Lọc theo thương hiệu
    //     if ($request->has('brand')) {
    //         $brandName = $request->brand;
    //         $query->where('brand', $brandName);
    //     }

    //     // Lọc theo khoảng giá
    //     if ($request->has('price')) {
    //         $price = $request->price;
    //         if (strpos($price, '-') !== false) {
    //             [$min, $max] = explode('-', $price);
    //             $minPrice = str_replace('.', '', $min);
    //             $maxPrice = str_replace('.', '', $max);
    //             $query->whereBetween('price', [$minPrice, $maxPrice]);
    //         } else {
    //             $minPrice = str_replace('.', '', $price);
    //             $query->where('price', '>=', $minPrice);
    //         }
    //     }

    //     // Lọc theo tag
    //     if ($request->has('tag')) {
    //         $tag = str_replace('-', ' ', $request->tag);
    //         $query->where('tags', 'like', "%$tag%");
    //     }

    //     // Lọc theo màu sắc (variant)
    //     if ($request->has('color')) {
    //         $color = $request->color;
    //         $query->whereHas('ProductVariants', function ($q) use ($color) {
    //             $q->where('color', $color);
    //         });
    //     }

    //     // Lọc theo size (variant)
    //     if ($request->has('size')) {
    //         $size = $request->size;
    //         $query->whereHas('ProductVariants', function ($q) use ($size) {
    //             $q->where('size', $size);
    //         });
    //     }

    //     // Lọc theo sản phẩm đang có khuyến mãi còn hạn
    //     if ($request->has('promotion')) {
    //         $currentDate = now()->format('Y-m-d');
    //         $query->whereHas('Discount', function ($q) use ($currentDate) {
    //             $q->where('start_date', '<=', $currentDate)
    //                 ->where('end_date', '>=', $currentDate);
    //         });
    //     }

    //     // Sắp xếp
    //     $sortBy = $request->input('sort_by', 'newest');
    //     switch ($sortBy) {
    //         case 'price_asc':
    //             $query->orderBy('price', 'asc');
    //             break;
    //         case 'price_desc':
    //             $query->orderBy('price', 'desc');
    //             break;
    //         case 'best_selling':
    //             // Join với bảng order items để lấy sản phẩm bán chạy
    //             $query->leftJoin('order_details as order_d', 'products.id', '=', 'order_details as order_d.product_id')
    //                 ->select('products.*', DB::raw('SUM(order_details as order_d.quantity) as total_sold'))
    //                 ->groupBy('products.id')
    //                 ->orderBy('total_sold', 'desc');
    //             break;
    //         case 'newest':
    //         default:
    //             $query->orderBy('created_at', 'desc');
    //             break;
    //     }

    //     // Phân trang và giữ lại các tham số lọc
    //     $products = $query->paginate(12)->appends($request->query());

    //     // Lấy dữ liệu cho các bộ lọc
    //     $categories = Category::all();
    //     $brands = Product::where('status', 1)->distinct()->pluck('brand');
    //     $priceRanges = [
    //         '0-1000000' => 'Dưới 1 triệu',
    //         '1000000-5000000' => '1 - 5 triệu',
    //         '5000000-10000000' => '5 - 10 triệu',
    //         '10000000' => 'Trên 10 triệu'
    //     ];

    //     // Lấy danh sách các màu sắc có sẵn
    //     $colors = ProductVariant::select('color')
    //         ->distinct()
    //         ->pluck('color');

    //     // Lấy danh sách các size có sẵn
    //     $sizes = ProductVariant::select('size')
    //         ->distinct()
    //         ->pluck('size')
    //         ->filter()
    //         ->values();

    //     return view("sites.shop.shop", compact('products', 'categories', 'brands', 'priceRanges', 'sortBy', 'colors', 'sizes'));
    // }

    public function cart()
    {
        return view('sites.cart.index');
    }

    public function contact()
    {
        return view('sites.contact.contact');
    }

    public function blog()
    {
        $data = Blog::with('staff')->paginate(5);
        return view('sites.blog.blog', compact('data'));
    }



    public function aboutUs()
    {
        return view('sites.pages.aboutUs');
    }

    public function blogDetail($slug)
    {
        $blogDetail = Blog::where('slug', $slug)->with('staff')->firstOrFail();
        $previousBlog = Blog::where('id', '<', $blogDetail->id)->orderBy('id', 'desc')->first();
        $nextBlog = Blog::where('id', '>', $blogDetail->id)->orderBy('id', 'asc')->first();

        return view('sites.pages.blogDetail', compact('blogDetail', 'previousBlog', 'nextBlog'));
    }


    public function shoppingCart()
    {
        return view('sites.pages.shoppingCart');
    }

    public function checkout()
    {

        return view('sites.pages.checkout');
    }

    public function productDetail(ProductRecent $productRecent, Product $productDetail, $slug)
    {
        $productDetail = Product::where('slug', $slug)
            ->with([
                'Category',
                'Discount',
                'ProductVariants' => function ($query) {
                    $query->where('active', true);
                },
                'ProductVariants.ImageVariants'
            ])
            ->firstOrFail();
        $prices = $productDetail->ProductVariants->pluck('price');
        // Lấy danh sách size của sản phẩm
        $sizes = $productDetail->ProductVariants->pluck('size')->unique();
        // Lấy danh sách màu của sản phẩm

        $colors = $productDetail->ProductVariants->pluck('color')->unique();

        $totalSale = $productDetail->orderDetails()->distinct('order_id')->count();


        $productRecentInfo = [];
        if (Session::has('product_recent') && count(Session::get('product_recent')) > 0) {
            foreach (Session::get('product_recent') as $item) {
                $product = Product::with('ProductVariants', 'Discount')->find($item->id_recent);
                if ($product) {
                    $productRecentInfo[] = $product;
                }
            }
        }


        // Lấy danh sách bình luận của khách hàng
        $commentCustomers = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->join('order_details as od', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('comments as r', function ($join) {
                $join->on('r.product_id', '=', 'p.id')
                    ->on('r.customer_id', '=', 'c.id')
                    ->on('r.order_id', '=', 'o.id');
            })
            ->where('p.slug', $slug)
            ->where('r.status', 1)
            ->orderBy('r.created_at', 'desc')
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

        // Nếu không có bình luận thì trả về mảng rỗng
        if ($commentCustomers->isEmpty()) {
            $commentCustomers = [];
        }

        // Lấy sao trung bình của sản phẩm
        $starAvg = DB::table('products as p')
            ->join('comments as r', 'r.product_id', '=', 'p.id')
            ->where('p.slug', $slug)
            ->select(
                'p.id as product_id',
                DB::raw('AVG(r.star) as star_avg')
            )
            ->groupBy('p.id')
            ->distinct()
            ->first();

        // Nếu không có sao trung bình thì mặc định là 0
        $starAvg = $starAvg ? $starAvg->star_avg : 0;

        // Thêm sản phẩm vào mảng session để hiển thị ra sản phẩm đã xem
        $productRecent->addToProductRecent($productDetail);

        return view('sites.product.product_detail', compact('productDetail', 'sizes', 'colors', 'commentCustomers', 'starAvg', 'totalSale', 'productRecentInfo'));
    }

    public function successPayment()
    {
        return view('sites.success.payment');
    }
}
