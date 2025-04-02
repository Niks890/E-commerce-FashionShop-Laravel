<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class SearchController extends Controller
{
    // API tìm kiếm sản phẩm và lưu lịch sử vào session
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['message' => 'Vui lòng nhập từ khóa!'], 400);
        }

        // Tìm kiếm sản phẩm
        $results = Product::with('Discount')
            ->where('product_name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('tags', 'LIKE', "%{$query}%")
            ->get()
            ->take(10);

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm phù hợp!', 'results' => []]);
        }

        // Lưu lịch sử tìm kiếm vào session (giới hạn 10 mục)
        $history = Session::get('search_history', []);
        if (!in_array($query, $history)) {
            array_push($history, $query); // Thêm vào cuối danh sách
            $history = array_slice($history, 0, 100); // Giới hạn 50 mục
            Session::put('search_history', $history);
        }

        return response()->json([
            'results' => $results,
            'history' => session()->get('search_history')
        ]);
    }

    // API lấy lịch sử tìm kiếm từ session
    public function getSearchHistory()
    {
        return response()->json(Session::get('search_history', []));
    }

    // API gợi ý sản phẩm dựa trên lịch sử tìm kiếm
    public function suggestContentBased()
    {
        $history = Session::get('search_history', []);

        if (empty($history)) {
            return response()->json([]);
        }

        // Tìm tất cả sản phẩm liên quan đến bất kỳ từ khóa nào trong lịch sử tìm kiếm
        $suggestedProducts = Product::with('Discount', 'ProductVariants')->where(function ($query) use ($history) {
            foreach ($history as $term) {
                $query->orWhere('product_name', 'LIKE', "%{$term}%")
                    ->orWhere('tags', 'LIKE', "%{$term}%");
            }
        })
            ->orderByRaw("
                        CASE
                            WHEN product_name LIKE ? THEN 1
                            WHEN tags LIKE ? THEN 2
                            ELSE 3
                        END", ["%{$history[0]}%", "%{$history[0]}%"])
            ->limit(10)
            ->get();

        return response()->json($suggestedProducts);
    }

    // API xóa lịch sử tìm kiếm trong session
    public function clearSearchHistory()
    {
        session()->forget('search_history');
        return response()->json(['message' => 'Lịch sử tìm kiếm đã được xóa']);
    }
}
