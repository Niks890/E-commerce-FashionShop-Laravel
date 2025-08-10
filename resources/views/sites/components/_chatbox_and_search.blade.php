@include('sites.components.css.search-and-chatbot-css')
<div class="bg-light d-flex align-items-center justify-content-center">
    <!-- Chatbox Icon -->
    <div class="position-fixed bottom-0 end-0 m-4" id="chatbox-icon" style="z-index: 1050;">
        <button class="btn rounded-circle shadow-lg">
            <i class="fas fa-robot"></i>
        </button>
    </div>

    <!-- Chatbox Modal -->
    <div class="modal fade " id="chatbox-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end">
            <div class="modal-content rounded-2 shadow">

                <div class="modal-header">
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-robot"></i>
                        Trợ Lý Tư Vấn TFashionShop
                    </h5>
                    <div class="header-actions">
                        <button type="button" class="btn-clear-history" title="Xóa lịch sử chat">
                            <i class="fas fa-broom" style="font-size: 18px;"></i>
                        </button>
                        <button type="button" class="btn-close-chatbot" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body" id="chatbox-messages">
                    <!-- Welcome Message -->
                </div>
                <div class="modal-footer">
                    <div class="input-container w-100">
                        <input id="chatbox-input" class="form-control" placeholder="Nhập tin nhắn của bạn..."
                            type="text" />
                        <button id="chatbox-send" class="btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Search Modal -->
<form id="modal-search" class="modal-search js-modal" method="post" action="{{ route('sites.shopSearch') }}">
    @csrf
    <div class="modal-container-search js-modal-container p-4 shadow-lg rounded-lg bg-white position-relative">
        <!-- Nút đóng -->
        <div class="modal-close js-modal-close">
            <i class="fas fa-times"></i>
        </div>

        <div class="modal-body" style="overflow-y: hidden;">
            <div class="form-group position-relative d-flex align-items-center">
                <input type="text" name="q" id="search-box" class="form-control rounded-pill px-4 py-2"
                    placeholder="Nhập từ khóa bạn muốn tìm...">
                <button type="button" class="position-absolute btn btn-success rounded-pill ms-2 px-3"
                    onclick="submitSearch()" style="top: 0; right: 0;">
                    <i class="fa fa-fw fa-search text-white"></i>
                </button>
            </div>

            <ul id="search-results" class="list-unstyled mt-3"></ul>

            <h5 class="mt-4 text-muted">Có thể bạn sẽ thích</h5>
            <ul id="suggestion-list" class="list-group small"></ul>
        </div>
    </div>
</form>
<!-- End Modal Search -->
@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatboxIcon = document.getElementById("chatbox-icon");
            const chatboxMessages = document.getElementById("chatbox-messages");
            const chatboxInput = document.getElementById("chatbox-input");
            const chatboxSend = document.getElementById("chatbox-send");
            const closeChatbot = document.querySelector(".btn-close-chatbot");
            const modalElement = document.getElementById("chatbox-modal");
            const modal = new bootstrap.Modal(modalElement);
            const clearHistoryBtn = document.querySelector('.btn-clear-history');

            // --- LocalStorage Chat History functions ---
            function getChatHistory() {
                const history = localStorage.getItem('chat_history');
                return history ? JSON.parse(history) : [];
            }

            function saveMessageToHistory(role, message) {
                const history = getChatHistory();
                history.push({
                    role,
                    message,
                    time: Date.now()
                });
                localStorage.setItem('chat_history', JSON.stringify(history));
            }

            function clearChatHistory() {
                localStorage.removeItem('chat_history');
            }

            function renderChatHistory() {
                chatboxMessages.innerHTML = '';
                const history = getChatHistory();
                if (history.length === 0) {
                    // Welcome message + quick replies như mặc định
                    chatboxMessages.innerHTML = `
                    <div class="welcome-message fade-in">
                        <div class="bot-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4>Chào mừng bạn đến với TFashionShop!</h4>
                        <p>Tôi là trợ lý AI của bạn. Hãy hỏi tôi bất cứ điều gì bạn muốn biết (tư vấn phối đồ, gợi ý sản phẩm...)!</p>
                        <strong style="color: red; font-weight: bold; font-size: 14px;">(*Chatbot có thể phản hồi thông tin chưa chính xác! Mọi thông tin chỉ mang tính chất tham khảo!*)</strong>
                    </div>
                    <div class="topic-guide">
                        <h5><i class="fas fa-compass"></i> Hãy chọn một chủ đề</h5>
                        <p>Chọn một trong các chủ đề dưới đây để bắt đầu cuộc trò chuyện</p>
                    </div>
                    <div class="quick-replies">
                        <button class="quick-reply-btn" data-message="Tôi muốn xem áo thun nam">
                            <i class="fas fa-tshirt"></i> Áo thun nam
                        </button>
                        <button class="quick-reply-btn" data-message="Có áo sơ mi có mẫu nào đẹp không?">
                            <i class="fas fa-user-tie"></i> Áo sơ mi
                        </button>
                        <button class="quick-reply-btn" data-message="Sản phẩm nào rẻ nhất?">
                            <i class="fas fa-tags"></i> Sản phẩm giá mềm
                        </button>
                        <button class="quick-reply-btn" data-message="Sản phẩm nào đang sale?">
                            <i class="fas fa-tags"></i> Sản phẩm khuyến mãi
                        </button>
                        <button class="quick-reply-btn" data-message="Hướng dẫn chọn size?">
                            <i class="fas fa-ruler"></i> Hướng dẫn chọn size
                        </button>
                    </div>
                     `;
                    initQuickReplyButtons();
                    return;
                }
                history.forEach(item => {
                    if (item.role === 'user') {
                        const userMessageContainer = document.createElement("div");
                        userMessageContainer.className = "message-container d-flex justify-content-end";
                        userMessageContainer.innerHTML = `
                    <div class="user-message">
                        ${escapeHtml(item.message)}
                    </div>
                `;
                        chatboxMessages.appendChild(userMessageContainer);
                    } else {
                        const botMessageContainer = document.createElement("div");
                        botMessageContainer.className = "message-container bot-message-container slide-up";
                        botMessageContainer.innerHTML = `
                    <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                    <div class="bot-message"><div>${item.message}</div></div>
                `;
                        chatboxMessages.appendChild(botMessageContainer);
                    }
                });
            }

            // --- Sự kiện mở chatbox: load lại lịch sử chat ---
            chatboxIcon.addEventListener("click", (e) => {
                modal.show();
                // console.log('Chatbox opened', e.target);
                setTimeout(() => {
                    chatboxInput.focus();
                    renderChatHistory();
                    scrollToBottom();
                }, 300);
            });

            // --- Gửi tin nhắn ---
            chatboxSend.addEventListener("click", sendMessage);
            chatboxInput.addEventListener("keypress", function(event) {
                if (event.key === "Enter" && !event.shiftKey) {
                    event.preventDefault();
                    sendMessage();
                }
            });

            // Thêm biến flag ở đầu script
            let isWaitingForResponse = false;

            function sendMessage() {

                if (isWaitingForResponse) {
                    showToast('Vui lòng đợi phản hồi trước khi gửi tin nhắn mới', 'warning');
                    return;
                }
                let message = chatboxInput.value.trim();
                if (message === "") return;

                // Đặt cờ đang chờ phản hồi
                isWaitingForResponse = true;

                // Disable nút gửi và input
                chatboxInput.disabled = true;
                chatboxSend.disabled = true;

                // Remove welcome message if it exists
                const welcomeMessage = chatboxMessages.querySelector('.welcome-message');
                if (welcomeMessage) welcomeMessage.remove();

                // Add user message
                const userMessageContainer = document.createElement("div");
                userMessageContainer.className = "message-container d-flex justify-content-end";
                userMessageContainer.innerHTML = `
            <div class="user-message">
                ${escapeHtml(message)}
            </div>
        `;
                chatboxMessages.appendChild(userMessageContainer);
                saveMessageToHistory('user', message);

                chatboxInput.value = "";
                scrollToBottom();

                // Show typing indicator
                const typingContainer = document.createElement("div");
                typingContainer.className = "message-container bot-message-container";
                typingContainer.id = "typing-indicator";
                typingContainer.innerHTML = `
            <div class="bot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="typing-indicator">
                <span style="color: #6b7280; font-size: 0.9rem;">Đang phản hồi</span>
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
                chatboxMessages.appendChild(typingContainer);
                scrollToBottom();

                fetch("/chat/send", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        },
                        body: JSON.stringify({
                            message: message
                        })
                    })
                    .then(async res => {
                        if (!res.ok) {
                            if (res.status >= 500) {
                                const errorData = await res.json();
                                throw new Error(errorData.reply ||
                                    'Server đang gặp sự cố. Vui lòng thử lại sau.');
                            }
                            const errorText = await res.text();
                            throw new Error(errorText);
                        }
                        return res.json();
                    })
                    // In the sendMessage function, modify the response handling:
                    .then(response => {

                        isWaitingForResponse = false;
                        chatboxInput.disabled = false;
                        chatboxSend.disabled = false;
                        chatboxInput.focus();
                        const typingIndicator = document.getElementById("typing-indicator");
                        if (typingIndicator) typingIndicator.remove();

                        let replyMsg = '';
                        let replyData = null;

                        // Prioritize reply_data if available
                        if (response.reply_data) {
                            replyData = response.reply_data;
                            replyMsg = typeof response.reply_data === "string" ?
                                response.reply_data :
                                (response.reply_data.content || '');
                        } else if (response.reply) {
                            replyData = response.reply;
                            replyMsg = response.reply;
                        } else {
                            replyData =
                                "Xin lỗi, tôi không thể trả lời câu hỏi này lúc này. Vui lòng thử lại sau.";
                            replyMsg = replyData;
                        }

                        appendBotMessage(replyData);
                        saveMessageToHistory('assistant', replyData);
                        scrollToBottom();
                    })
                    .catch(error => {

                        isWaitingForResponse = false;
                        chatboxInput.disabled = false;
                        chatboxSend.disabled = false;
                        chatboxInput.focus();
                        console.error('Chat API Error:', error);
                        const typingIndicator = document.getElementById("typing-indicator");
                        if (typingIndicator) typingIndicator.remove();

                        appendBotMessage(
                            "Đã xảy ra lỗi khi kết nối. Vui lòng kiểm tra kết nối internet và thử lại sau.");
                        saveMessageToHistory('assistant',
                            "Đã xảy ra lỗi khi kết nối. Vui lòng kiểm tra kết nối internet và thử lại sau.");
                        scrollToBottom();
                    });

            }

            function appendBotMessage(data) {
                const botMessageContainer = document.createElement("div");
                botMessageContainer.className = "message-container bot-message-container slide-up";
                const avatar = document.createElement("div");
                avatar.className = "bot-avatar";
                avatar.innerHTML = '<i class="fas fa-robot"></i>';
                const messageWrapper = document.createElement("div");
                messageWrapper.className = "bot-message";
                const messageContent = document.createElement("div");
                if (typeof data === 'string') {
                    messageContent.innerHTML = data.replace(/\n/g, '<br>');
                } else if (typeof data === 'object') {
                    if (data.type === 'text_with_image' && data.image_url) {
                        messageContent.innerHTML = `
                <div>${data.content.replace(/\n/g, '<br>')}</div>
                <div class="mt-2 text-center">
                    <img src="${data.image_url}" alt="Bảng size áo"
                        class="img-fluid rounded border shadow-sm"
                        style="max-width: 100%; max-height: 300px; object-fit: contain;">
                </div>
                <div class="mt-2">
                    <a href="http://127.0.0.1:8000/size-guide" target="_blank"
                    class="text-primary text-decoration-none">
                    <i class="fas fa-external-link-alt"></i> Xem hướng dẫn chọn size chi tiết
                    </a>
                </div>
                `;
                    } else if (data.content) {
                        messageContent.innerHTML = data.content.replace(/\n/g, '<br>');
                    } else if (data.type === 'product_list') {
                        let html = `<strong>${data.intro_message}</strong><br><br>`;
                        data.products.forEach(product => {
                            let priceHtml = `<span class="product-price">${product.price}</span>`;
                            let discountBadge = '';
                            if (product.has_discount) {
                                priceHtml = `
                        <span class="product-price text-danger fw-bold">${product.price}</span>
                        <span class=" text-muted ms-2" style="text-decoration:  line-through;">${product.original_price}</span>
                        `;
                                discountBadge = `
                        <div class="mt-1">
                            <span class="badge bg-danger">Giảm ${product.discount_percent * 100}%</span>
                        </div>
                        `;
                            }
                            html += `
                        <div class="product-card mb-3 p-2 border rounded">
                            <div class="d-flex align-items-center">
                                <a href="${product.link}" target="_blank" class="text-decoration-none text-dark">
                                    <img src="${product.image_url}" alt="${escapeHtml(product.name)}"
                                        class="product-image rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                </a>
                                <div class="product-info flex-grow-1">
                                    <a href="${product.link}" target="_blank"
                                    class="product-name text-decoration-none text-primary fw-bold d-block mb-1">
                                    ${escapeHtml(product.name)}
                                    </a>
                                    <div class="price-container">
                                        ${priceHtml}
                                        ${discountBadge}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        });
                        html += `<div class="mt-2">${data.outro_message}</div>`;
                        messageContent.innerHTML = html;
                    } else {
                        messageContent.innerHTML = "Xin lỗi, có lỗi xảy ra khi xử lý tin nhắn.";
                    }
                }
                messageWrapper.appendChild(messageContent);
                botMessageContainer.appendChild(avatar);
                botMessageContainer.appendChild(messageWrapper);
                chatboxMessages.appendChild(botMessageContainer);
                scrollToBottom();
            }

            function scrollToBottom() {
                setTimeout(() => {
                    chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
                }, 100);
            }

            function escapeHtml(text) {
                return text.replace(/[&<>"']/g, function(m) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    } [m];
                });
            }

            // --- Xử lý quick reply ---
            function initQuickReplyButtons() {
                document.querySelectorAll('.quick-reply-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const message = this.getAttribute('data-message');
                        chatboxInput.value = message;
                        sendMessage();
                    });
                });
            }


            // --- Đóng chatbox ---
            if (closeChatbot) {
                closeChatbot.addEventListener("click", () => {
                    modal.hide();
                });
            }

            // --- Xóa lịch sử ---
            clearHistoryBtn.addEventListener('click', function() {
                if (confirm('Bạn có chắc chắn muốn xóa toàn bộ lịch sử chat?')) {
                    fetch('/chat/clear-history', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                clearChatHistory();
                                renderChatHistory();
                                showToast('Lịch sử chat đã được xóa thành công');
                                initQuickReplyButtons();
                            } else {
                                showToast('Có lỗi xảy ra khi xóa lịch sử', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Có lỗi xảy ra khi xóa lịch sử', 'error');
                        });
                }
            });

            // --- Toast notification ---
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('show');
                }, 10);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }

            // Tự động render welcome khi load lần đầu
            renderChatHistory();
        });
    </script>

    {{-- Xử lý tìm kiếm --}}
    <script>
        $('.search-btn').click(function() {
            $('.js-modal').addClass("open");
        });

        $('.js-modal-close').click(function() {
            $('.js-modal').removeClass("open");
        });

        function submitSearch() {
            $('#modal-search').submit();
        }

        // Tìm kiếm sản phẩm bằng AJAX
        // $("#search-box").on("input", function(e) {
        //     let query = $("#search-box").val();
        //     if (query.length > 1) {
        //         $.ajax({
        //             url: "http://127.0.0.1:8000/api/search",
        //             type: "GET",
        //             data: {
        //                 q: query
        //             },
        //             success: function(data) {
        //                 let results = $("#search-results");
        //                 results.empty();
        //                 if (data.results.length > 0) {
        //                     data.results.forEach(function(item) {
        //                         let price = Intl.NumberFormat('vi-VN').format(item.price);
        //                         if (item.discount_id != null) {
        //                             price = Intl.NumberFormat('vi-VN').format(item.price - (item
        //                                 .price * item.discount.percent_discount));
        //                         }
        //                         results.append(`
        //                             <li class="list-group-item d-flex align-items-center p-3 border-bottom"
        //                                     style="cursor: pointer;"
        //                                     onmouseover="this.style.backgroundColor='#ccc'; this.style.textDecoration='underline';"
        //                                     onmouseout="this.style.backgroundColor='#fff'; this.style.textDecoration='none';">
        //                                 <a class="fw-medium text-decoration-none text-dark" href="{{ url('product') }}/${item.slug}">
        //                                 <img src="${item.image}" width="50" height="50" alt="">
        //                                 ${item.product_name} | <p class="d-inline">Giá:</p> ${price} đ
        //                                 </a>
        //                             </li>
        //                     `);
        //                     });
        //                 } else {
        //                     results.append("<li>Không tìm thấy kết quả</li>");
        //                 }
        //             }
        //         });
        //     }
        // });

        let searchTimeout = null;
        let lastQuery = '';

        $("#search-box").on("input", function() {
            let query = $("#search-box").val().trim();

            // Không gọi API nếu query quá ngắn hoặc trùng lần trước
            if (query.length < 2 || query === lastQuery) {
                if (query.length < 2) $("#search-results").empty();
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                lastQuery = query;
                $.ajax({
                    url: "/api/search",
                    type: "GET",
                    data: {
                        q: query
                    },
                    success: function(data) {
                        let results = $("#search-results");
                        results.empty();
                        if (data.results.length > 0) {
                            data.results.forEach(function(item) {
                                let price = Intl.NumberFormat('vi-VN').format(item
                                    .price);
                                if (item.discount_id != null && item.discount) {
                                    price = Intl.NumberFormat('vi-VN').format(item
                                        .price - (item.price * item.discount
                                            .percent_discount));
                                }
                                results.append(`
                            <li class="list-group-item d-flex align-items-center p-3 border-bottom"
                                    style="cursor: pointer;"
                                    onmouseover="this.style.backgroundColor='#ccc'; this.style.textDecoration='underline';"
                                    onmouseout="this.style.backgroundColor='#fff'; this.style.textDecoration='none';">
                                <a class="fw-medium text-decoration-none text-dark" href="/product/${item.slug}">
                                <img src="${item.image}" width="50" height="50" alt="">
                                ${item.product_name} | <p class="d-inline">Giá:</p> ${price} đ
                                </a>
                            </li>
                        `);
                            });
                        } else {
                            results.append("<li>Không tìm thấy kết quả</li>");
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 429) {
                            alert("Bạn thao tác quá nhanh, vui lòng chờ một chút rồi thử lại!");
                        }
                    }
                });
            }, 400); // 400ms debounce
        });

        // Gợi ý sản phẩm
        // $.get("http://127.0.0.1:8000/api/suggest-content-based", function(data) {
        //     let suggestions = $("#suggestion-list");
        //     suggestions.empty();
        //     if (data.length > 0) {
        //         data.forEach(function(item) {
        //             let price = Intl.NumberFormat('vi-VN').format(item.price);
        //             if (item.discount_id != null) {
        //                 price = item.price - (item.price * item.discount.percent_discount);
        //             }
        //             let listItem = `
        //             <li class="list-group-item d-flex align-items-center p-3 border-bottom">
        //                 <a href="/product/${item.slug}" class="fw-medium text-decoration-none text-dark">
        //                     <img src="${item.image}" width="50" height="50" alt="">
        //                     ${item.product_name} | <p class="d-inline">Giá:</p> ${Intl.NumberFormat('vi-VN').format(price)} đ
        //                 </a>
        //             </li>
        //         `;
        //             suggestions.append(listItem);
        //         });
        //     } else {
        //         suggestions.append('<li class="list-group-item text-muted p-3">Không có gợi ý nào</li>');
        //     }
        // });

        $.get("/api/suggest-content-based", function(data) {
            let suggestions = $("#suggestion-list");
            suggestions.empty();
            if (data.length > 0) {
                data.forEach(function(item) {
                    let price = item.price;
                    // Kiểm tra discount tồn tại và hợp lệ
                    if (item.discount_id != null && item.discount && typeof item.discount
                        .percent_discount === "number") {
                        price = item.price - (item.price * item.discount.percent_discount);
                    }
                    // Chuyển về số nguyên nếu cần
                    price = Math.round(price);
                    let formattedPrice = Intl.NumberFormat('vi-VN').format(price);

                    let listItem = `
                    <li class="list-group-item d-flex align-items-center p-3 border-bottom">
                        <a href="/product/${item.slug}" class="fw-medium text-decoration-none text-dark">
                            <img src="${item.image}" width="50" height="50" alt="">
                            ${item.product_name} | <p class="d-inline">Giá:</p> ${formattedPrice} đ
                        </a>
                    </li>
                `;
                    suggestions.append(listItem);
                });
            } else {
                suggestions.append('<li class="list-group-item text-muted p-3">Không có gợi ý nào</li>');
            }
        });

        $(".dropdown-btn").click(function(event) {
            event.stopPropagation();
            $(".dropdown-content").toggle();
        });

        $(document).click(function() {
            $(".dropdown-content").hide();
        });
    </script>

    <script src="{{ asset('client/js/cart-add.js') }}"></script>
    <script src="{{ asset('client/js/chatbot-localstorage.js') }}"></script>
@endsection
