@include('sites.components.css.search-and-chatbot-css')
<div class="bg-light d-flex align-items-center justify-content-center">
    <!-- Chatbox Icon -->
    <div class="position-fixed bottom-0 end-0 m-4" id="chatbox-icon" style="z-index: 1050;">
        <button class="btn rounded-circle shadow-lg">
            <i class="fas fa-robot"></i>
        </button>
    </div>

    <!-- Chatbox Modal -->
    <div class="modal fade" id="chatbox-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-robot"></i>
                        Trợ Lý Thông Minh
                    </h5>
                    <button type="button" class="btn-close-chatbot" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" id="chatbox-messages">
                    <!-- Welcome Message -->
                    <div class="welcome-message fade-in">
                        <div class="bot-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4>Chào mừng bạn!</h4>
                        <p>Tôi là trợ lý AI của bạn. Hãy hỏi tôi bất cứ điều gì bạn muốn biết!</p>
                    </div>


                    <!-- Topic Guide -->
                    <div class="topic-guide">
                        <h5>
                            <i class="fas fa-compass"></i>
                            Hãy chọn một chủ đề
                        </h5>
                        <p>Chọn một trong các chủ đề dưới đây để bắt đầu cuộc trò chuyện</p>
                    </div>


                    <div class="quick-replies">
                        <button class="quick-reply-btn" data-message="Tôi muốn xem áo thun nam">
                            <i class="fas fa-tshirt"></i>
                            Áo thun nam
                        </button>
                        <button class="quick-reply-btn" data-message="Có áo sơ mi nữ nào đẹp không?">
                            <i class="fas fa-user-tie"></i>
                            Áo sơ mi nữ
                        </button>
                        <button class="quick-reply-btn" data-message="Quần jean nam size nào có sẵn?">
                            <i class="fas fa-user"></i>
                            Quần jean nam
                        </button>
                        <button class="quick-reply-btn" data-message="Váy đầm cho nữ có những mẫu nào?">
                            <i class="fas fa-female"></i>
                            Váy đầm nữ
                        </button>
                        <button class="quick-reply-btn" data-message="Sản phẩm nào đang sale?">
                            <i class="fas fa-tags"></i>
                            Sản phẩm khuyến mãi
                        </button>
                        <button class="quick-reply-btn" data-message="Bảng size quần áo như thế nào?">
                            <i class="fas fa-ruler"></i>
                            Hướng dẫn chọn size
                        </button>
                    </div>
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




            document.addEventListener("click", function(event) {
                if (event.target.closest(".quick-reply-btn")) {
                    const button = event.target.closest(".quick-reply-btn");
                    const message = button.getAttribute("data-message");

                    if (message) {
                        // Thêm hiệu ứng click
                        button.style.transform = "scale(0.95)";
                        setTimeout(() => {
                            button.style.transform = "";
                        }, 150);

                        // Gọi hàm sendMessage với nội dung từ button
                        // (Giả sử bạn đã có hàm sendMessage() và chatboxInput)
                        if (typeof sendMessage === 'function' && typeof chatboxInput !== 'undefined') {
                            chatboxInput.value = message;
                            sendMessage();
                        } else {
                            // Demo: hiển thị message được chọn
                            console.log("Message selected:", message);
                            alert("Đã chọn: " + message);
                        }
                    }
                }
            });

            chatboxIcon.addEventListener("click", () => {
                modal.show();
                // Focus on input when modal opens
                setTimeout(() => {
                    chatboxInput.focus();
                }, 300);
            });

            chatboxSend.addEventListener("click", sendMessage);

            chatboxInput.addEventListener("keypress", function(event) {
                if (event.key === "Enter" && !event.shiftKey) {
                    event.preventDefault();
                    sendMessage();
                }
            });

            if (closeChatbot) {
                closeChatbot.addEventListener("click", () => {
                    modal.hide();
                });
            }

            // Auto-scroll to bottom
            function scrollToBottom() {
                setTimeout(() => {
                    chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
                }, 100);
            }


            function sendMessage() {
                let message = chatboxInput.value.trim();
                if (message === "") return;

                // Remove welcome message if it exists
                const welcomeMessage = chatboxMessages.querySelector('.welcome-message');
                if (welcomeMessage) {
                    welcomeMessage.remove();
                }

                // Add user message
                const userMessageContainer = document.createElement("div");
                userMessageContainer.className = "message-container d-flex justify-content-end";
                userMessageContainer.innerHTML = `
                    <div class="user-message">
                        ${escapeHtml(message)}
                    </div>
                `;
                chatboxMessages.appendChild(userMessageContainer);

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
                            // Đảm bảo meta tag CSRF token đã được render trong HTML của bạn
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        },
                        body: JSON.stringify({
                            message: message
                        })
                    })
                    .then(async res => { // Sử dụng async để có thể dùng await
                        if (!res.ok) {
                            // Nếu phản hồi không thành công, cố gắng đọc nó như văn bản để debug
                            const errorText = await res.text();
                            console.error('Server responded with an error:', res.status, res.statusText,
                                errorText);
                            throw new Error(
                                `HTTP error! Status: ${res.status} - ${errorText.substring(0, 200)}...`
                            ); // Giới hạn độ dài lỗi
                        }
                        // Nếu thành công, mới parse JSON
                        return res.json();
                    })
                    .then(response => {
                        const typingIndicator = document.getElementById("typing-indicator");
                        if (typingIndicator) typingIndicator.remove();

                        // Kiểm tra xem phản hồi có phải là dữ liệu cấu trúc (như danh sách sản phẩm) hay là tin nhắn văn bản thông thường
                        if (response.reply_data) {
                            appendBotMessage(response.reply_data);
                        } else if (response.reply) {
                            appendBotMessage(response.reply);
                        } else {
                            appendBotMessage(
                                "Xin lỗi, tôi không thể trả lời câu hỏi này lúc này. Vui lòng thử lại sau.");
                        }

                        scrollToBottom();
                    })
                    .catch(error => {
                        console.error('Chat API Error:', error);
                        const typingIndicator = document.getElementById("typing-indicator");
                        if (typingIndicator) typingIndicator.remove();

                        appendBotMessage(
                            "Đã xảy ra lỗi khi kết nối. Vui lòng kiểm tra kết nối internet và thử lại. (Lỗi: " +
                            error.message + ")");
                        scrollToBottom();
                    });
            }



            function appendBotMessage(data) {
                // Debug: hiển thị toàn bộ dữ liệu nhận được
                // console.log('Raw data received:', data);

                const botMessageContainer = document.createElement("div");
                botMessageContainer.className = "message-container bot-message-container slide-up";

                const avatar = document.createElement("div");
                avatar.className = "bot-avatar";
                avatar.innerHTML = '<i class="fas fa-robot"></i>';

                const messageWrapper = document.createElement("div");
                messageWrapper.className = "bot-message";

                const messageContent = document.createElement("div");

                // Xử lý mọi trường hợp
                if (typeof data === 'string') {
                    messageContent.innerHTML = data.replace(/\n/g, '<br>');
                } else if (typeof data === 'object') {
                    // Trường hợp response từ handleSpecialCases
                    if (data.content) {
                        messageContent.innerHTML = data.content.replace(/\n/g, '<br>');
                    }
                    // Trường hợp product list
                    else if (data.type === 'product_list') {
                        let html = `<strong>${data.intro_message}</strong><br><br>`;
                        data.products.forEach(product => {
                            html += `
                    <div class="product-card mb-2 d-flex align-items-center">
                        <a href="${product.link}" target="_blank" class="text-decoration-none text-dark">
                            <img src="${product.image_url}" alt="${escapeHtml(product.name)}" class="product-image rounded me-3">
                        </a>
                        <div class="product-info flex-grow-1">
                            <a href="${product.link}" target="_blank" class="product-name text-decoration-none text-primary fw-bold">${escapeHtml(product.name)}</a><br>
                            <span class="product-price text-danger">${product.price}</span>
                        </div>
                    </div>`;
                        });
                        html += `<br>${data.outro_message}`;
                        messageContent.innerHTML = html;
                    }
                    // Fallback cho các trường hợp khác
                    else {
                        // console.error('Unsupported data format:', data);
                        messageContent.innerHTML = "Xin lỗi, có lỗi xảy ra khi xử lý tin nhắn.";
                    }
                }

                messageWrapper.appendChild(messageContent);
                botMessageContainer.appendChild(avatar);
                botMessageContainer.appendChild(messageWrapper);
                chatboxMessages.appendChild(botMessageContainer);
                scrollToBottom();
            }


            // Typing effect
            function typeText(element, text, index = 0) {
                if (index < text.length) {
                    element.textContent += text.charAt(index);
                    setTimeout(() => typeText(element, text, index + 1), 30);
                }
            }

            // Check if URL is image
            function isImageUrl(url) {
                return /^https?:\/\/.+\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(url) ||
                    /cloudinary\.com\/.+/i.test(url);
            }

            // Escape HTML
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

            setTimeout(() => {
                if (chatboxMessages.querySelector('.welcome-message')) {}
            }, 3000);
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
        $("#search-box").on("input", function(e) {
            let query = $("#search-box").val();
            console.log(query);
            if (query.length > 1) {
                $.ajax({
                    url: "http://127.0.0.1:8000/api/search",
                    type: "GET",
                    data: {
                        q: query
                    },
                    success: function(data) {
                        let results = $("#search-results");
                        console.log(results);
                        results.empty();

                        if (data.results.length > 0) {
                            data.results.forEach(function(item) {
                                console.log(item);
                                let price = Intl.NumberFormat('vi-VN').format(item.price);
                                if (item.discount_id != null) {
                                    price = Intl.NumberFormat('vi-VN').format(item.price - (item
                                        .price * item.discount.percent_discount));
                                }
                                results.append(`
                                        <li class="list-group-item d-flex align-items-center p-3 border-bottom"
                                                style="cursor: pointer;"
                                                onmouseover="this.style.backgroundColor='#ccc'; this.style.textDecoration='underline';"
                                                onmouseout="this.style.backgroundColor='#fff'; this.style.textDecoration='none';">
                                            <a class="fw-medium text-decoration-none text-dark" href="{{ url('product') }}/${item.slug}">
                                            <img src="${item.image}" width="50" height="50" alt="">
                                            ${item.product_name} | <p class="d-inline">Giá:</p> ${price} đ
                                            </a>
                                        </li>

                                `);
                            });
                        } else {
                            results.append("<li>Không tìm thấy kết quả</li>");
                        }
                    }
                });
            }
        });



        // phải search thử ở http://127.0.0.1:8000/api/search?q="....." => get ở http://127.0.0.1:8000/api/suggest-content-based thì mới thấy
        // Lấy gợi ý sản phẩm
        $.get("http://127.0.0.1:8000/api/suggest-content-based", function(data) {
            let suggestions = $("#suggestion-list");
            suggestions.empty();

            if (data.length > 0) {
                data.forEach(function(item) {
                    let price = Intl.NumberFormat('vi-VN').format(item.price);
                    if (item.discount_id != null) {
                        price = item.price - (item.price * item.discount.percent_discount);
                    }
                    let listItem = `
                        <li class="list-group-item d-flex align-items-center p-3 border-bottom">
                            <a href="/product/${item.slug}" class="fw-medium text-decoration-none text-dark">
                                <img src="${item.image}" width="50" height="50" alt="">
                                ${item.product_name} | <p class="d-inline">Giá:</p> ${Intl.NumberFormat('vi-VN').format(price)} đ
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
            event.stopPropagation(); // Ngăn chặn sự kiện lan ra ngoài
            $(".dropdown-content").toggle();
        });

        $(document).click(function() {
            $(".dropdown-content").hide();
        });
    </script>

    <script src="{{ asset('client/js/cart-add.js') }}"></script>
@endsection
