<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function userBased(int $userId)
    {
        // Copy toàn bộ nội dung hàm userCFRecommend vào đây
        $orderItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select('orders.customer_id', 'order_details.product_id')
            ->distinct()
            ->get();


        // dd($orderItems);


        $userProducts = $orderItems->groupBy('customer_id')->mapWithKeys(function ($items, $key) {
            return [(int)$key => $items->pluck('product_id')->unique()->toArray()];
        })->toArray();

        if (!isset($userProducts[$userId])) {
            return response()->json(['message' => 'User has no purchase data'], 404);
        }

        $jaccardSimilarity = function (array $setA, array $setB) {
            $intersection = count(array_intersect($setA, $setB));
            $union = count(array_unique(array_merge($setA, $setB)));
            return $union === 0 ? 0 : $intersection / $union;
        };

        $getNearestNeighbors = function (int $userId, array $userProducts, int $k = 3) use ($jaccardSimilarity) {
            $currentUserProducts = $userProducts[$userId] ?? [];
            $similarities = [];

            foreach ($userProducts as $otherUserId => $products) {
                if ($otherUserId == $userId) continue;
                $similarities[$otherUserId] = $jaccardSimilarity($currentUserProducts, $products);
            }
            arsort($similarities);
            // dd($similarities);
            return array_slice($similarities, 0, $k, true);
        };

        // dd($getNearestNeighbors);

        $recommendProducts = function (int $userId, array $userProducts, int $k = 3) use ($getNearestNeighbors) {
            $neighbors = $getNearestNeighbors($userId, $userProducts, $k);
            $currentUserProducts = $userProducts[$userId] ?? [];
            $recommendationScores = [];

            foreach ($neighbors as $neighborId => $similarity) {
                $neighborProducts = $userProducts[$neighborId];
                foreach ($neighborProducts as $productId) {
                    if (in_array($productId, $currentUserProducts)) continue;
                    if (!isset($recommendationScores[$productId])) {
                        $recommendationScores[$productId] = 0;
                    }
                    $recommendationScores[$productId] += $similarity;
                }
            }
            arsort($recommendationScores);
            // dd($recommendationScores);
            return array_keys($recommendationScores);
        };

        $recommendationProductIds = $recommendProducts($userId, $userProducts, 3);

        // dd($recommendationProductIds);
        $products = Product::whereIn('id', $recommendationProductIds)->select('id')->get();

        return response()->json($products);
    }

    public function itemCFRecommend(int $userId)
    {
        // Bước 1: Lấy dữ liệu user - sản phẩm đã mua
        $orderItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select('orders.customer_id', 'order_details.product_id')
            ->distinct()
            ->get();

        // dd($orderItems);


        // Gom nhóm theo user
        $userProducts = $orderItems->groupBy('customer_id')->map(function ($items) {
            return $items->pluck('product_id')->unique()->toArray();
        })->toArray();

        // Gom nhóm theo sản phẩm → các user đã mua sản phẩm đó
        $productUsers = $orderItems->groupBy('product_id')->map(function ($items) {
            return $items->pluck('customer_id')->unique()->toArray();
        })->toArray();

        // dd($userProducts,$productUsers);

        // Kiểm tra user hiện tại có dữ liệu không
        if (!isset($userProducts[$userId])) {
            return collect(); // user chưa mua gì
        }

        $currentUserProducts = $userProducts[$userId];

        // dd($currentUserProducts);

        // Hàm tính similarity giữa 2 sản phẩm dựa trên người mua
        $jaccardSimilarity = function (array $usersA, array $usersB) {
            $intersection = count(array_intersect($usersA, $usersB));
            $union = count(array_unique(array_merge($usersA, $usersB)));
            if ($union === 0) return 0;
            return $intersection / $union;
        };

        // Bước 2: Tính similarity giữa các sản phẩm user đã mua với các sản phẩm khác
        $similarityScores = [];

        foreach ($currentUserProducts as $productId) {
            foreach ($productUsers as $otherProductId => $usersWhoBought) {
                if (in_array($otherProductId, $currentUserProducts)) continue; // bỏ qua sản phẩm đã mua

                $sim = $jaccardSimilarity($productUsers[$productId], $usersWhoBought);

                if (!isset($similarityScores[$otherProductId])) {
                    $similarityScores[$otherProductId] = 0;
                }
                $similarityScores[$otherProductId] += $sim;
            }
        }

        // Bước 3: Sắp xếp sản phẩm theo điểm similarity giảm dần
        arsort($similarityScores);

        // dd($similarityScores);

        // Bước 4: Lấy danh sách gợi ý theo ID
        $recommendedProductIds = array_keys($similarityScores);
        return Product::whereIn('id', $recommendedProductIds)->select('id')->get();
    }

    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        $allKeys = array_unique(array_merge(array_keys($vecA), array_keys($vecB)));

        foreach ($allKeys as $key) {
            $a = $vecA[$key] ?? 0;
            $b = $vecB[$key] ?? 0;
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        if ($normA == 0 || $normB == 0) {
            return 0; // tránh chia cho 0
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    // User-based CF dùng cosine similarity
    public function userBasedCosine(int $userId)
    {
        // Lấy dữ liệu với số lượng mua (trọng số)
        $orderItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select('orders.customer_id', 'order_details.product_id', DB::raw('count(*) as quantity'))
            ->groupBy('orders.customer_id', 'order_details.product_id')
            ->get();

        // Gom nhóm user -> [product_id => quantity]
        $userProducts = [];
        foreach ($orderItems as $item) {
            $userProducts[(int)$item->customer_id][(int)$item->product_id] = (int)$item->quantity;
        }

        if (!isset($userProducts[$userId])) {
            return response()->json(['message' => 'User has no purchase data'], 404);
        }

        // Tìm neighbors dựa trên cosine similarity
        $getNearestNeighbors = function (int $userId, array $userProducts, int $k = 3) {
            $currentUserVector = $userProducts[$userId];
            $similarities = [];
            foreach ($userProducts as $otherUserId => $productsVector) {
                if ($otherUserId == $userId) continue;
                $similarities[$otherUserId] = $this->cosineSimilarity($currentUserVector, $productsVector);
            }
            arsort($similarities);
            return array_slice($similarities, 0, $k, true);
        };

        $neighbors = $getNearestNeighbors($userId, $userProducts, 3);
        $currentUserProducts = $userProducts[$userId];
        $recommendationScores = [];

        foreach ($neighbors as $neighborId => $similarity) {
            $neighborProducts = $userProducts[$neighborId];
            foreach ($neighborProducts as $productId => $quantity) {
                if (isset($currentUserProducts[$productId])) continue; // đã mua rồi
                if (!isset($recommendationScores[$productId])) {
                    $recommendationScores[$productId] = 0;
                }
                $recommendationScores[$productId] += $similarity * $quantity;
            }
        }

        arsort($recommendationScores);

        $recommendationProductIds = array_keys($recommendationScores);

        $products = Product::whereIn('id', $recommendationProductIds)->select('id')->get();

        return response()->json($products);
    }

    // Item-based CF dùng cosine similarity
    public function itemBasedCosine(int $userId)
    {
        // Lấy dữ liệu với số lượng mua (trọng số)
        $orderItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select('orders.customer_id', 'order_details.product_id', DB::raw('count(*) as quantity'))
            ->groupBy('orders.customer_id', 'order_details.product_id')
            ->get();

        // Gom nhóm user -> [product_id => quantity]
        $userProducts = [];
        foreach ($orderItems as $item) {
            $userProducts[(int)$item->customer_id][(int)$item->product_id] = (int)$item->quantity;
        }

        // Gom nhóm product -> [user_id => quantity]
        $productUsers = [];
        foreach ($orderItems as $item) {
            $productUsers[(int)$item->product_id][(int)$item->customer_id] = (int)$item->quantity;
        }

        if (!isset($userProducts[$userId])) {
            return response()->json(['message' => 'User has no purchase data'], 404);
        }

        $currentUserProducts = $userProducts[$userId];

        $similarityScores = [];

        foreach ($currentUserProducts as $productId => $qty) {
            foreach ($productUsers as $otherProductId => $userQtys) {
                if ($otherProductId == $productId) continue;

                // Tính cosine similarity giữa 2 sản phẩm dựa trên vector người dùng
                $sim = $this->cosineSimilarity(
                    $productUsers[$productId] ?? [],
                    $userQtys
                );

                if (!isset($similarityScores[$otherProductId])) {
                    $similarityScores[$otherProductId] = 0;
                }

                // Tính điểm dựa trên similarity * số lượng user mua sản phẩm hiện tại
                $similarityScores[$otherProductId] += $sim * $qty;
            }
        }

        arsort($similarityScores);

        $recommendedProductIds = array_keys($similarityScores);

        $products = Product::whereIn('id', $recommendedProductIds)->select('id')->get();

        return response()->json($products);
    }
}
