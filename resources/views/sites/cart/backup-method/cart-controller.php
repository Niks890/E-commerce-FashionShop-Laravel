********************Controller************************
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\OrderStatusHistory;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Rate limiting: 5 orders per minute per IP
        if (RateLimiter::tooManyAttempts('order-placement:'.$request->ip(), 5)) {
            return redirect()->back()->with('error', 'Bạn đã đặt hàng quá nhiều lần. Vui lòng chờ 1 phút.');
        }
        RateLimiter::hit('order-placement:'.$request->ip());

        // Validate input data strictly
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-() ]+$/',
            'shipping_fee' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0.1',
            'note' => 'nullable|string|max:500',
            'receiver_name' => 'required|string|max:100',
            'email' => 'required|email:rfc,dns|max:100',
            'VAT' => 'required|numeric|min:0',
            'customer_id' => 'required|integer|exists:customers,id',
            'payment' => 'required|string|in:cash,credit_card,paypal,bank_transfer',
            'cart_token' => 'required|string' // CSRF token for cart
        ], [
            'address.required' => 'Vui lòng nhập điểm giao hàng',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'note.required' => 'Vui lòng nhập ghi chú',
            'receiver_name.required' => 'Vui lòng nhập tên người nhận',
            'email.required' => 'Vui lòng nhập email hợp lệ',
            'email.email' => 'Email không hợp lệ',
            'total.min' => 'Tổng giá trị đơn hàng không hợp lệ',
            'cart_token.required' => 'Phiên giỏ hàng không hợp lệ'
        ]);

        // Verify cart token matches session
        if ($request->cart_token !== session('cart_token')) {
            return redirect()->route('sites.cart')->with('error', 'Phiên giỏ hàng không hợp lệ. Vui lòng thử lại.');
        }

        $errors = [];
        DB::beginTransaction();
        try {
            // Get cart from session with strict validation
            $cart = collect(session('cart', []))->map(function ($item) {
                return is_object($item) ? $item : (object)$item;
            });

            // Filter checked items with strict validation
            $selectedItems = $cart->filter(function ($item) {
                return isset($item->checked) && $item->checked === true
                    && isset($item->id) && isset($item->quantity)
                    && isset($item->size) && isset($item->color);
            });

            if ($selectedItems->isEmpty()) {
                return redirect()->route('sites.cart')->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
            }

            // Validate stock and prices
            $hasStockIssue = false;
            $calculatedTotal = 0;
            $validatedItems = collect();

            foreach ($selectedItems as $key => $item) {
                // Strict variant validation
                $variant = ProductVariant::where('id', $item->product_variant_id ?? 0)
                    ->where('product_id', $item->id)
                    ->where('size', trim($item->size))
                    ->where('color', trim($item->color))
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    $errors[] = 'Sản phẩm "'.($item->product_name ?? 'Unknown').'" không tồn tại và đã bị xóa khỏi giỏ hàng.';
                    $hasStockIssue = true;
                    continue;
                }

                // Validate price hasn't changed
                if (abs($variant->price - $item->price) > 0.01) {
                    $errors[] = 'Giá sản phẩm "'.$item->product_name.'" đã thay đổi. Vui lòng kiểm tra lại.';
                    $hasStockIssue = true;
                    continue;
                }

                // Validate stock
                if ($variant->available_stock < $item->quantity) {
                    if ($variant->available_stock > 0) {
                        $errors[] = 'Sản phẩm "'.$item->product_name.'" chỉ còn '.$variant->available_stock.' sản phẩm. Số lượng đã được điều chỉnh.';
                        $item->quantity = $variant->available_stock;
                    } else {
                        $errors[] = 'Sản phẩm "'.$item->product_name.'" đã hết hàng và đã bị xóa khỏi giỏ hàng.';
                        $hasStockIssue = true;
                        continue;
                    }
                }

                $calculatedTotal += $variant->price * $item->quantity;
                $validatedItems->push($item);
            }

            // Validate total amount matches calculation
            $tolerance = 0.01; // Allow small rounding differences
            if (abs($calculatedTotal + $validated['shipping_fee'] + $validated['VAT'] - $validated['total']) > $tolerance) {
                $errors[] = 'Tổng giá trị đơn hàng không khớp. Vui lòng kiểm tra lại.';
                $hasStockIssue = true;
            }

            if ($hasStockIssue) {
                DB::rollBack();

                // Update cart in session
                $updatedCart = $cart->reject(function ($item) use ($validatedItems) {
                    return !$validatedItems->contains('id', $item->id);
                })->values();

                session(['cart' => $updatedCart]);

                return redirect()->route('sites.cart')
                    ->with('error', implode('<br>', $errors))
                    ->withInput();
            }

            // Create order
            $order = Order::create([
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'shipping_fee' => $validated['shipping_fee'],
                'total' => $validated['total'],
                'note' => $validated['note'] ?? null,
                'receiver_name' => $validated['receiver_name'],
                'email' => $validated['email'],
                'VAT' => $validated['VAT'],
                'payment' => $validated['payment'],
                'status' => 'Chờ xử lý',
                'customer_id' => $validated['customer_id'],
                'ip_address' => $request->ip()
            ]);

            // Create order history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'Chờ xử lý',
                'notes' => 'Đơn hàng được tạo'
            ]);

            // Create order details and update stock atomically
            foreach ($validatedItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'size_and_color' => $item->size.'-'.$item->color,
                    'code' => session('percent_discount', 0),
                ]);

                // Atomic stock update with condition
                $updated = ProductVariant::where('id', $item->product_variant_id)
                    ->where('available_stock', '>=', $item->quantity)
                    ->decrement('available_stock', $item->quantity);

                if (!$updated) {
                    throw new \Exception("Không đủ tồn kho cho sản phẩm ID: {$item->id}");
                }
            }

            // Send confirmation email (queue)
            try {
                Mail::to($order->email)->queue(new OrderConfirmationMail($order));
                Log::info('Email xác nhận đã gửi cho đơn hàng #'.$order->id);
            } catch (\Exception $e) {
                Log::error('Lỗi gửi email cho đơn #'.$order->id.': '.$e->getMessage());
            }

            // Update cart session
            $remainingCart = $cart->reject(function ($item) use ($validatedItems) {
                return $validatedItems->contains('id', $item->id);
            })->values();

            session([
                'cart' => $remainingCart,
                'cart_token' => Str::random(40) // Refresh cart token
            ]);
            session()->forget('percent_discount');

            // Store success data
            Session::put('success_data', [
                'logo' => 'cod.png',
                'receiver_name' => $order->receiver_name,
                'order_id' => $order->id,
                'total' => $order->total,
                'expires_at' => now()->addMinutes(30)
            ]);

            DB::commit();

            return redirect()->route('sites.success.payment');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order failed: ".$e->getMessage()."\n".$e->getTraceAsString());

            return redirect()->route('sites.cart')
                ->with('error', 'Đặt hàng thất bại do lỗi hệ thống. Vui lòng thử lại!')
                ->withInput();
        }
    }

    public function checkStock(Request $request)
    {
        $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'integer|exists:product_variants,id',
            'cart_token' => 'required|string'
        ]);

        if ($request->cart_token !== session('cart_token')) {
            return response()->json(['error' => 'Invalid cart session'], 400);
        }

        $stocks = ProductVariant::whereIn('id', $request->variant_ids)
            ->pluck('available_stock', 'id');

        return response()->json($stocks);
    }
}








********************JS************************
class CartManager {
    constructor() {
        this.cartToken = $('meta[name="cart-token"]').attr('content');
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
        this.initEvents();
        this.startStockPolling();
    }

    initEvents() {
        $(document).on('submit', '#checkout-form', (e) => this.handleCheckout(e));
        $(document).on('click', '.update-cart', (e) => this.updateCartItem(e));
        $(document).on('click', '.remove-item', (e) => this.removeCartItem(e));
    }

    startStockPolling() {
        this.pollingInterval = setInterval(() => this.checkStock(), 15000);
        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === "visible") {
                this.checkStock();
            }
        });
    }

    stopStockPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }

    async checkStock() {
        if (this.isCheckingStock) return;
        this.isCheckingStock = true;

        const variantIds = [];
        $('.cart-item').each(function() {
            const variantId = $(this).data('variant-id');
            if (variantId) variantIds.push(variantId);
        });

        if (variantIds.length === 0) {
            this.isCheckingStock = false;
            return;
        }

        try {
            const response = await $.ajax({
                url: '/cart/check-stock',
                method: 'POST',
                data: {
                    variant_ids: variantIds,
                    cart_token: this.cartToken,
                    _token: this.csrfToken
                }
            });

            this.processStockResponse(response);
        } catch (error) {
            console.error('Stock check error:', error);
        } finally {
            this.isCheckingStock = false;
        }
    }

    processStockResponse(stocks) {
        let hasChanges = false;
        const updates = [];

        $('.cart-item').each(function() {
            const row = $(this);
            const variantId = row.data('variant-id');
            const availableStock = stocks[variantId];
            const input = row.find('.product-quantity');
            const currentQty = parseInt(input.val()) || 0;

            if (availableStock === undefined) return;

            input.attr('max', availableStock);

            if (currentQty > availableStock) {
                hasChanges = true;
                const newQty = Math.max(0, availableStock);
                input.val(newQty);

                const productName = row.find('.product-name').text() || 'Sản phẩm';
                const message = newQty > 0
                    ? `${productName} chỉ còn ${newQty} sản phẩm. Đã điều chỉnh số lượng.`
                    : `${productName} đã hết hàng và sẽ bị xóa khỏi giỏ hàng.`;

                updates.push({
                    variantId,
                    newQty,
                    remove: newQty === 0,
                    message
                });
            }
        });

        if (hasChanges) {
            this.showStockAlert(updates);
            this.updateCartTotal();
        }
    }

    showStockAlert(updates) {
        const messages = updates.map(u => u.message);
        const alertMessage = messages.join('\n');

        if (updates.some(u => u.remove)) {
            // Remove items with 0 stock
            updates.filter(u => u.remove).forEach(u => {
                $(`[data-variant-id="${u.variantId}"]`).remove();
            });

            // Update cart in backend
            this.syncCartWithServer();
        }

        alert(alertMessage);
    }

    async syncCartWithServer() {
        try {
            await $.ajax({
                url: '/cart/sync',
                method: 'POST',
                data: {
                    _token: this.csrfToken,
                    cart_token: this.cartToken
                }
            });
        } catch (error) {
            console.error('Cart sync failed:', error);
        }
    }

    async handleCheckout(e) {
        e.preventDefault();
        const form = $('#checkout-form');
        const submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true).text('Đang kiểm tra...');

        try {
            const stockCheck = await this.validateStockBeforeCheckout();

            if (stockCheck.valid) {
                form.off('submit').submit();
            } else {
                alert(stockCheck.message);
                location.reload();
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert('Lỗi khi kiểm tra đơn hàng. Vui lòng thử lại.');
        } finally {
            submitBtn.prop('disabled', false).text('Đặt hàng');
        }
    }

    async validateStockBeforeCheckout() {
        const selectedItems = [];
        const variantIds = [];

        $('.cart-item input[type="checkbox"]:checked').each(function() {
            const row = $(this).closest('.cart-item');
            const variantId = row.data('variant-id');
            const quantity = parseInt(row.find('.product-quantity').val()) || 0;

            if (variantId && quantity > 0) {
                selectedItems.push({
                    row,
                    variantId,
                    quantity,
                    productName: row.find('.product-name').text() || 'Sản phẩm'
                });
                variantIds.push(variantId);
            }
        });

        if (selectedItems.length === 0) {
            return {
                valid: false,
                message: 'Không có sản phẩm nào được chọn để thanh toán.'
            };
        }

        try {
            const stocks = await $.ajax({
                url: '/cart/check-stock',
                method: 'POST',
                data: {
                    variant_ids: variantIds,
                    cart_token: this.cartToken,
                    _token: this.csrfToken
                }
            });

            const errors = [];
            let isValid = true;

            for (const item of selectedItems) {
                const availableStock = stocks[item.variantId] || 0;

                if (availableStock < item.quantity) {
                    isValid = false;
                    const message = availableStock > 0
                        ? `${item.productName} chỉ còn ${availableStock} sản phẩm.`
                        : `${item.productName} đã hết hàng.`;
                    errors.push(message);
                }
            }

            return {
                valid: isValid,
                message: isValid ? '' : errors.join('\n') + '\nVui lòng cập nhật giỏ hàng.'
            };
        } catch (error) {
            console.error('Stock validation error:', error);
            return {
                valid: false,
                message: 'Lỗi khi kiểm tra tồn kho. Vui lòng thử lại.'
            };
        }
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new CartManager();
});
