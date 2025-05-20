<div class="bg-light d-flex align-items-center justify-content-center">
    <!-- Chatbox Icon -->
    <div class="position-fixed bottom-0 end-0 m-3" id="chatbox-icon">
        <button class="btn btn-primary rounded-circle p-3">
            <i class="fas fa-comments fa-2x"></i>
        </button>
    </div>

    <!-- Chatbox Modal -->
    <div class="modal fade" id="chatbox-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trò chuyện với chúng tôi</h5>
                    <button type="button" class="btn-close border-0 btn-close-chatbot" style="background: none;"
                        data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>

                <div class="modal-body">
                    <div class="d-flex flex-column gap-3" id="chatbox-messages">
                        <!-- Tin nhắn sẽ hiển thị ở đây -->
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex w-100 gap-2">
                        <input id="chatbox-input" class="form-control" placeholder="Nhập tin nhắn..." type="text" />
                        <button id="chatbox-send" class="btn btn-primary">
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

            {{-- <h5 class="mt-4 text-muted">Lịch sử tìm kiếm</h5>
            <ul id="search-history" class="list-group small"></ul>
            <button id="clear-history" class="btn btn-outline-danger btn-sm mt-2">Xóa lịch sử tìm kiếm</button> --}}

            <h5 class="mt-4 text-muted">Có thể bạn sẽ thích</h5>
            <ul id="suggestion-list" class="list-group small"></ul>
        </div>
    </div>
</form>
<!-- End Modal Search -->

@include('sites.components.css.search-and-chatbot-css')
@section('js')
    {{-- <script>
        $(document).ready(function() {
            const chatboxIcon = $("#chatbox-icon");
            const chatboxModal = $("#chatbox-modal");
            const chatboxMessages = $("#chatbox-messages");
            const chatboxInput = $("#chatbox-input");
            const chatboxSend = $("#chatbox-send");

            // Mở modal khi click vào icon chatbot
            chatboxIcon.on("click", function() {
                chatboxModal.modal("show");
            });

            // Đóng modal khi click vào nút đóng
            $('.btn-close-chatbot').on("click", function() {
                chatboxModal.modal("hide");
            });

            // Gửi tin nhắn khi nhấn nút gửi
            chatboxSend.on("click", function() {
                sendMessage();
            });

            // Gửi tin nhắn khi nhấn Enter
            chatboxInput.on("keypress", function(event) {
                if (event.which === 13) {
                    sendMessage();
                }
            });

            // Hàm cuộn xuống cuối tin nhắn
            function scrollToBottom() {
                setTimeout(() => {
                    chatboxMessages.scrollTop(chatboxMessages[0].scrollHeight);
                }, 100); // Chờ 100ms để tin nhắn render xong
            }

            // Gửi tin nhắn của người dùng
            function sendMessage() {
                let message = chatboxInput.val().trim();
                if (message === "") return;

                chatboxMessages.append(`
            <div class="d-flex justify-content-end align-items-end gap-2 ms-auto">
                <div class="bg-primary text-white p-2 rounded">
                    <p class="mb-0">${message}</p>
                </div>
            </div>
        `);

                chatboxInput.val("");
                scrollToBottom();

                // Hiển thị "Đang nhập..."
                let typingIndicator = appendTypingIndicator();

                $.post("/chatbot", {
                    message: message,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    removeTypingIndicator(typingIndicator);
                    showTypingEffect(response.response);
                });
            }

            // Hiển thị hiệu ứng gõ chữ khi chatbot phản hồi
            function showTypingEffect(message) {
                let chatbotReply = $(`
            <div class="d-flex align-items-start gap-2 mb-3">
                <img class="rounded-circle mt-2" src="{{ asset('client/img/chatbot/bot.avif') }}" width="30">
                <div class="bg-light p-2 rounded typing-effect"><p class="mb-0"></p></div>
            </div>
        `);
                chatboxMessages.append(chatbotReply);

                let textElement = chatbotReply.find("p");
                let index = 0;

                function typeCharacter() {
                    if (index < message.length) {
                        textElement.append(message[index]);
                        index++;
                        setTimeout(typeCharacter, 50); // Tốc độ gõ chữ (50ms mỗi ký tự)
                    }
                }

                typeCharacter();
                scrollToBottom();
            }

            // Thêm hiệu ứng "Đang nhập..."
            function appendTypingIndicator() {
                let typingIndicator = $(`
            <div class="d-flex align-items-start gap-2 mb-3 typing-indicator">
                <img class="rounded-circle mt-2" src="{{ asset('client/img/chatbot/bot.avif') }}" width="30">
                <div class="bg-light p-2 rounded"><em>Đang nhập...</em></div>
            </div>
        `);
                chatboxMessages.append(typingIndicator);
                scrollToBottom();
                return typingIndicator;
            }

            // Xóa hiệu ứng "Đang nhập..."
            function removeTypingIndicator(typingIndicator) {
                typingIndicator.remove();
            }
        });
    </script> --}}
    <script>
        $(document).ready(function() {
            const chatboxIcon = $("#chatbox-icon");
            const chatboxMessages = $("#chatbox-messages");
            const chatboxInput = $("#chatbox-input");
            const chatboxSend = $("#chatbox-send");

            chatboxIcon.on("click", function(e) {
                $("#chatbox-modal").modal("show");
            });

            chatboxSend.on("click", function() {
                sendMessage();
            });

            chatboxInput.on("keypress", function(event) {
                if (event.which === 13) {
                    sendMessage();
                }
            });

            function scrollToBottom() {
                const chatboxMessages = $("#chatbox-messages");
                setTimeout(() => {
                    chatboxMessages.scrollTop(chatboxMessages[0].scrollHeight);
                }, 100); // Chờ 100ms để tin nhắn render xong
            }

            function sendMessage() {
                let message = chatboxInput.val().trim();
                if (message === "") return;

                chatboxMessages.append(`<div class="d-flex justify-content-end align-items-end gap-2 ms-auto">
                                            <div class="bg-primary text-white p-2 rounded">
                                                <p class="mb-0">${message}</p>
                                            </div>
                                        </div>`);

                chatboxInput.val("");
                scrollToBottom(); // Cuộn xuống sau khi gửi tin nhắn

                $.post("/chatbot", {
                    message: message,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    // console.log(response);
                    chatboxMessages.append(`
                                            <div class="d-flex align-items-start gap-2 mb-3">
                                                <img class="rounded-circle mt-2" src="{{ asset('client/img/chatbot/bot.avif') }}" width="30">
                                                <div class="bg-light p-2 rounded">
                                                    <p class="mb-0">${response.response}</p>
                                                </div>
                                            </div>
                                        `);
                    scrollToBottom();
                });
            }

            $('.btn-close-chatbot').on("click", function() {
                $("#chatbox-modal").modal("hide");
            });

        });

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
                        // console.log(results);
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
                                            <img src="{{ asset('uploads/${item.image}') }}" width="50" height="50" alt="">
                                            ${item.product_name} | <p class="d-inline">Giá:</p> ${price} đ
                                            </a>
                                        </li>

                                `);
                            });
                        } else {
                            results.append("<li>Không tìm thấy kết quả</li>");
                        }

                        // Cập nhật lịch sử tìm kiếm
                        // updateSearchHistory(data.history);
                    }
                });
            }
        });

        // Lấy lịch sử tìm kiếm
        // function updateSearchHistory(history) {
        //     let historyList = $("#search-history");
        //     historyList.empty();

        //     if (history.length > 0) {
        //         history.forEach(function(item, index) {
        //             let listItem = `
    //                 <li class="d-flex justify-content-between align-items-center">
    //                     <span>
    //                         <i class="fas fa-clock text-secondary me-2"></i> ${item}
    //                     </span>
    //                 </li>
    //             `;
        //             historyList.append(listItem);
        //         });
        //     } else {
        //         historyList.append('<li class="text-muted p-2">Chưa có lịch sử tìm kiếm</li>');
        //     }
        // }

        // $.get("http://127.0.0.1:8000/api/search-history", function(data) {
        //     updateSearchHistory(data);
        // });


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
                                <img src="{{ asset('uploads/${item.image}') }}" width="50" height="50" alt="">
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

        // Xóa lịch sử tìm kiếm
        // $("#clear-history").on("click", function() {
        //     $.ajax({
        //         url: "http://127.0.0.1:8000/api/search-history",
        //         type: "DELETE",
        //         success: function(response) {
        //             alert(response.message);
        //             updateSearchHistory([]);
        //         }
        //     });
        // });

        $(".dropdown-btn").click(function(event) {
            event.stopPropagation(); // Ngăn chặn sự kiện lan ra ngoài
            $(".dropdown-content").toggle();
        });

        $(document).click(function() {
            $(".dropdown-content").hide();
        });
    </script>

    {{-- danh sách sản phẩm liên quan --}}
    <script>
        $(document).ready(function() {
            $("#suggestion-list-product").empty(); // Xóa dữ liệu cũ trước khi cập nhật mới

            $.ajax({
                url: "http://127.0.0.1:8000/api/suggest-content-based", // API lấy danh sách sản phẩm
                method: "GET",
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        console.log(data);
                        data.forEach(function(item) {
                            let price = item.price;
                            let nameDiscount = "";
                            if (item.discount_id != null) {
                                price = item.price - (item.price * item.discount
                                    .percent_discount);
                                nameDiscount = item.discount.name;
                            } else {
                                nameDiscount = "New";
                            }

                            let totalStock = [];
                            let addCartOrNone = [];
                            item.product_variants.map((variant) => {
                                totalStock[variant.product_id] = 0;
                            })
                            item.product_variants.forEach((variant) => {
                                totalStock[variant.product_id] += variant.stock + 0;
                            });
                            item.product_variants.map((variant) => {
                                if (totalStock[variant.product_id] == 0) {
                                    addCartOrNone[variant.product_id] = false;
                                } else {
                                    addCartOrNone[variant.product_id] = true;
                                };
                            })

                            let productHTML = `
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="product__item" id="product-list-shop">
                                <div class="product__item__pic">
                                    <img class="set-bg" width="280" height="250"
                                    src="{{ asset('uploads/${item.image}') }}"
                                    alt="${item.product_name}">
                                    <span class="label name-discount-suggest" >${nameDiscount}</span></li>
                                    <ul class="product__hover">
                                        <li><a href="{{ url('add-to-wishlist') }}/${item.id}"><img src="{{ asset('client/img/icon/heart.png') }}"
                                            alt=""></a></li>
                                            <li><a href="javascript:void(0);"><img src="{{ asset('client/img/icon/compare.png') }}"
                                                alt=""><span>Compare</span></a></li>
                                                <li><a href="{{ url('product') }}/${item.slug}">
                                                    <img src="{{ asset('client/img/icon/search.png') }}"
                                                    alt=""></a>

                                                    </ul>
                                                    </div>

                                                    <div class="product__item__text">
                                                        <h6>${item.product_name}</h6>
                                                        ` +

                                (addCartOrNone[item.id] > 0 ?
                                    `<a href="javascript:void(0);" class="add-cart" data-id="${item.id}">+ Add To Cart</a>` :
                                    `<span class=" badge badge-warning">Hết hàng</span>`)

                                +
                                `
                                            <div class="rating">
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                </div>
                                                <h5>${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)}</h5>
                                                <div class="product__color__select">
                                                    <label for="pc-4">
                                                        <input type="radio" id="pc-4">
                                                        </label>
                                                <label class="active black" for="pc-5">
                                                    <input type="radio" id="pc-5">
                                                    </label>
                                                    <label class="grey" for="pc-6">
                                                        <input type="radio" id="pc-6">
                                                        </label>
                                                        </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $(document).ready(function() {
                                $('.name-discount-suggest').each(function() {
                                    if ($(this).text().trim() !== "New") {
                                        $(this).addClass(
                                            'bg-danger text-white');
                                    }
                                });
                            });
                            $("#suggestion-list-product").append(productHTML);
                        });
                    } else {
                        $("#suggestion-list-product").html("<p>Không có sản phẩm nào.</p>");
                    }
                },
                error: function() {
                    console.error("Lỗi API khi tải danh sách sản phẩm.");
                    $("#suggestion-list-product").html("<p>Lỗi khi tải sản phẩm.</p>");
                }
            });


        });
    </script>



    <script src="{{ asset('client/js/cart-add.js') }}"></script>
@endsection
{{--
<button onclick="window.open('https://console.dialogflow.com/api-client/demo/embedded/d091fe6d-c3c9-487c-9c4b-855241a4956d', '_blank', 'width=400,height=500')">
    Mở Chatbot
</button> --}}
