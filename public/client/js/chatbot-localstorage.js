function getChatHistory() {
    const history = localStorage.getItem('chat_history');
    return history ? JSON.parse(history) : [];
}

function saveMessageToHistory(role, message) {
    const history = getChatHistory();

    // Prepare message data for storage
    let messageToSave;
    if (typeof message === 'object' && message !== null) {
        // For product lists, store essential data
        if (message.type === 'product_list') {
            messageToSave = {
                type: 'product_list',
                intro_message: message.intro_message,
                outro_message: message.outro_message,
                products: message.products.map(product => ({
                    name: product.name,
                    price: product.price,
                    original_price: product.original_price,
                    discount_percent: product.discount_percent,
                    link: product.link,
                    image_url: product.image_url,
                    has_discount: product.has_discount
                }))
            };
        }
        // For image responses
        else if (message.type === 'text_with_image') {
            messageToSave = {
                type: 'text_with_image',
                content: message.content,
                image_url: message.image_url
            };
        }
        // For other object types
        else {
            messageToSave = message;
        }
    } else {
        messageToSave = message;
    }

    history.push({
        role,
        message: messageToSave,
        time: Date.now()
    });

    localStorage.setItem('chat_history', JSON.stringify(history));
}
function clearChatHistory() {
    localStorage.removeItem('chat_history');
}
// Render lại lịch sử chat khi mở chatbox
function renderChatHistory() {
    const chatboxMessages = document.getElementById("chatbox-messages");
    chatboxMessages.innerHTML = '';
    const history = getChatHistory();
    if (history.length === 0) {
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
                <button class="quick-reply-btn" data-message="Tôi muốn xem áo thun">
                    <i class="fas fa-tshirt"></i> Áo thun
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
            // Xử lý cả trường hợp message là string hoặc object
            let messageData = item.message;
            try {
                // Nếu là string JSON thì parse thành object
                if (typeof messageData === 'string' && messageData.startsWith('{')) {
                    messageData = JSON.parse(messageData);
                }
            } catch (e) {
                console.error('Error parsing message data', e);
            }
            appendBotMessage(messageData);
        }
    });
}

function escapeHtml(text) {
    if (typeof text !== 'string') return '';
    return text.replace(/[&<>"']/g, function (m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}

// appendBotMessage nhận object hoặc string
// function appendBotMessage(data) {
//     const chatboxMessages = document.getElementById("chatbox-messages");
//     const botMessageContainer = document.createElement("div");
//     botMessageContainer.className = "message-container bot-message-container slide-up";
//     const avatar = document.createElement("div");
//     avatar.className = "bot-avatar";
//     avatar.innerHTML = '<i class="fas fa-robot"></i>';
//     const messageWrapper = document.createElement("div");
//     messageWrapper.className = "bot-message";
//     const messageContent = document.createElement("div");
//     if (typeof data === 'string') {
//         messageContent.innerHTML = data.replace(/\n/g, '<br>');
//     } else if (typeof data === 'object' && data !== null) {
//         if (data.type === 'text_with_image' && data.image_url) {
//             messageContent.innerHTML = `
//                 <div>${data.content.replace(/\n/g, '<br>')}</div>
//                 <div class="mt-2 text-center">
//                     <img src="${data.image_url}" alt="Bảng size áo"
//                         class="img-fluid rounded border shadow-sm"
//                         style="max-width: 100%; max-height: 300px; object-fit: contain;">
//                 </div>
//                 <div class="mt-2">
//                     <a href="http://127.0.0.1:8000/size-guide" target="_blank"
//                     class="text-primary text-decoration-none">
//                     <i class="fas fa-external-link-alt"></i> Xem hướng dẫn chọn size chi tiết
//                     </a>
//                 </div>
//             `;
//         } else if (data.content) {
//             messageContent.innerHTML = data.content.replace(/\n/g, '<br>');
//         } else if (data.type === 'product_list' && Array.isArray(data.products)) {
//             let html = `<strong>${data.intro_message}</strong><br><br>`;
//             data.products.forEach(product => {
//                 let priceHtml = `<span class="product-price">${product.price}</span>`;
//                 let discountBadge = '';
//                 if (product.has_discount) {
//                     priceHtml = `
//                         <span class="product-price text-danger fw-bold">${product.price}</span>
//                         <span class=" text-muted ms-2" style="text-decoration:  line-through;">${product.original_price}</span>
//                     `;
//                     discountBadge = `
//                         <div class="mt-1">
//                             <span class="badge bg-danger">Giảm ${product.discount_percent * 100}%</span>
//                         </div>
//                     `;
//                 }
//                 html += `
//                     <div class="product-card mb-3 p-2 border rounded">
//                         <div class="d-flex align-items-center">
//                             <a href="${product.link}" target="_blank" class="text-decoration-none text-dark">
//                                 <img src="${product.image_url}" alt="${escapeHtml(product.name)}"
//                                     class="product-image rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
//                             </a>
//                             <div class="product-info flex-grow-1">
//                                 <a href="${product.link}" target="_blank"
//                                 class="product-name text-decoration-none text-primary fw-bold d-block mb-1">
//                                 ${escapeHtml(product.name)}
//                                 </a>
//                                 <div class="price-container">
//                                     ${priceHtml}
//                                     ${discountBadge}
//                                 </div>
//                             </div>
//                         </div>
//                     </div>`;
//             });
//             html += `<div class="mt-2">${data.outro_message}</div>`;
//             messageContent.innerHTML = html;
//         } else {
//             messageContent.innerHTML = "Xin lỗi, có lỗi xảy ra khi xử lý tin nhắn.";
//         }
//     } else {
//         messageContent.innerHTML = "Xin lỗi, có lỗi xảy ra khi xử lý tin nhắn.";
//     }
//     messageWrapper.appendChild(messageContent);
//     botMessageContainer.appendChild(avatar);
//     botMessageContainer.appendChild(messageWrapper);
//     chatboxMessages.appendChild(botMessageContainer);
//     scrollToBottom();
// }

function appendBotMessage(data) {
    // If data is a string from localStorage (legacy format)
    if (typeof data === 'string' && data.startsWith('{')) {
        try {
            data = JSON.parse(data);
        } catch (e) {
            console.error('Error parsing message data', e);
            data = "Không thể hiển thị tin nhắn này";
        }
    }

    const chatboxMessages = document.getElementById("chatbox-messages");
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
    }
    else if (typeof data === 'object' && data !== null) {
        if (data.type === 'text_with_image' && data.image_url) {
            messageContent.innerHTML = `
                <div>${(data.content || '').replace(/\n/g, '<br>')}</div>
                <div class="mt-2 text-center">
                    <img src="${data.image_url}" alt="Hình ảnh sản phẩm"
                        class="img-fluid rounded border shadow-sm"
                        style="max-width: 100%; max-height: 300px; object-fit: contain;">
                </div>
            `;
        }
        else if (data.type === 'product_list' && Array.isArray(data.products)) {
            let html = `<strong>${data.intro_message || ''}</strong><br><br>`;

            data.products.forEach(product => {
                let priceHtml = `<span class="product-price">${product.price || ''}</span>`;
                let discountBadge = '';

                if (product.has_discount && product.original_price) {
                    priceHtml = `
                        <span class="product-price text-danger fw-bold">${product.price || ''}</span>
                        <span class="text-muted ms-2" style="text-decoration: line-through;">${product.original_price}</span>
                    `;
                    discountBadge = `
                        <div class="mt-1">
                            <span class="badge bg-danger">Giảm ${(product.discount_percent || 0) * 100}%</span>
                        </div>
                    `;
                }

                html += `
                    <div class="product-card mb-3 p-2 border rounded">
                        <div class="d-flex align-items-center">
                            <a href="${product.link || '#'}" target="_blank" class="text-decoration-none text-dark">
                                <img src="${product.image_url || ''}" alt="${escapeHtml(product.name || '')}"
                                    class="product-image rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            </a>
                            <div class="product-info flex-grow-1">
                                <a href="${product.link || '#'}" target="_blank"
                                class="product-name text-decoration-none text-primary fw-bold d-block mb-1">
                                ${escapeHtml(product.name || '')}
                                </a>
                                <div class="price-container">
                                    ${priceHtml}
                                    ${discountBadge}
                                </div>
                            </div>
                        </div>
                    </div>`;
            });

            html += `<div class="mt-2">${data.outro_message || ''}</div>`;
            messageContent.innerHTML = html;
        }
        else if (data.content) {
            messageContent.innerHTML = (data.content || '').replace(/\n/g, '<br>');
        }
        else {
            messageContent.innerHTML = "Không thể hiển thị tin nhắn này";
        }
    }
    else {
        messageContent.innerHTML = "Không thể hiển thị tin nhắn này";
    }

    messageWrapper.appendChild(messageContent);
    botMessageContainer.appendChild(avatar);
    botMessageContainer.appendChild(messageWrapper);
    chatboxMessages.appendChild(botMessageContainer);
    scrollToBottom();
}

function scrollToBottom() {
    const chatboxMessages = document.getElementById("chatbox-messages");
    setTimeout(() => {
        chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
    }, 100);
}

function initQuickReplyButtons() {
    // Xóa tất cả sự kiện click cũ trước khi đăng ký mới
    document.querySelectorAll('.quick-reply-btn').forEach(button => {
        button.removeEventListener('click', handleQuickReplyClick);
    });

    // Đăng ký sự kiện mới
    document.querySelectorAll('.quick-reply-btn').forEach(button => {
        button.addEventListener('click', handleQuickReplyClick);
    });
}

function handleQuickReplyClick(event) {
    event.stopPropagation(); // Ngăn sự kiện lan ra ngoài

    const chatboxInput = document.getElementById("chatbox-input");
    const message = this.getAttribute('data-message');
    if (message) {
        // Hiệu ứng nhấn nút
        this.style.transform = "scale(0.95)";
        setTimeout(() => { this.style.transform = ""; }, 150);

        // Gửi tin nhắn
        chatboxInput.value = message;
        sendMessage();
    }
}
document.addEventListener("click", function (event) {
    if (event.target.closest(".quick-reply-btn")) {
        const button = event.target.closest(".quick-reply-btn");
        const chatboxInput = document.getElementById("chatbox-input");
        const message = button.getAttribute("data-message");
        if (message) {
            button.style.transform = "scale(0.95)";
            setTimeout(() => { button.style.transform = ""; }, 150);
            chatboxInput.value = message;
            sendMessage();
        }
    }
});

// --- Sự kiện và hàm gửi tin nhắn ---
document.addEventListener("DOMContentLoaded", function () {
    const chatboxIcon = document.getElementById("chatbox-icon");
    const chatboxMessages = document.getElementById("chatbox-messages");
    const chatboxInput = document.getElementById("chatbox-input");
    const chatboxSend = document.getElementById("chatbox-send");
    const closeChatbot = document.querySelector(".btn-close-chatbot");
    const modalElement = document.getElementById("chatbox-modal");
    const modal = new bootstrap.Modal(modalElement);
    const clearHistoryBtn = document.querySelector('.btn-clear-history');

    chatboxIcon.addEventListener("click", () => {
        modal.show();
        setTimeout(() => {
            chatboxInput.focus();
            renderChatHistory();
            scrollToBottom();
        }, 300);
    });

    // chatboxSend.addEventListener("click", sendMessage);
    chatboxInput.addEventListener("keypress", function (event) {
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
    if (clearHistoryBtn) {
        clearHistoryBtn.addEventListener('click', function () {
            if (confirm('Bạn có chắc chắn muốn xóa toàn bộ lịch sử chat?')) {
                fetch('/chat/clear-history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
    }

    function initChatAutoCleanup() {
        // Kiểm tra mỗi khi mở chatbox
        const checkAndCleanHistory = () => {
            const history = getChatHistory();
            if (history.length === 0) return;

            const now = Date.now();
            const oneDayInMs = 24 * 60 * 60 * 1000;
            const oldestMessageTime = history[0].time;

            if (now - oldestMessageTime > oneDayInMs) {
                clearChatHistory();
                renderChatHistory();
            }
        };

        // Kiểm tra khi mở chatbox
        document.getElementById('chatbox-icon').addEventListener('click', checkAndCleanHistory);

        // Kiểm tra mỗi giờ (phòng trường hợp người dùng để mở lâu)
        setInterval(checkAndCleanHistory, 60 * 60 * 1000);
    }
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.add('show'); }, 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => { toast.remove(); }, 300);
        }, 3000);
    }
    // --- Hàm gửi đi, lưu cả object trả về ---
    window.sendMessage = function sendMessage() {
        let message = chatboxInput.value.trim();
        if (message === "") return;
        const welcomeMessage = chatboxMessages.querySelector('.welcome-message');
        if (welcomeMessage) welcomeMessage.remove();
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
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ message: message })
        })
            .then(async res => {
                if (!res.ok) {
                    if (res.status >= 500) {
                        const errorData = await res.json();
                        throw new Error(errorData.reply || 'Server đang gặp sự cố. Vui lòng thử lại sau.');
                    }
                    const errorText = await res.text();
                    throw new Error(errorText);
                }
                return res.json();
            })
            .then(response => {
                const typingIndicator = document.getElementById("typing-indicator");
                if (typingIndicator) typingIndicator.remove();
                // LƯU NGUYÊN OBJECT, ƯU TIÊN reply_data
                let replyData = response.reply_data !== undefined ? response.reply_data : response.reply;
                appendBotMessage(replyData);
                saveMessageToHistory('assistant', replyData); // Lưu full object
                scrollToBottom();
            })
            .catch(error => {
                console.error('Chat API Error:', error);
                const typingIndicator = document.getElementById("typing-indicator");
                if (typingIndicator) typingIndicator.remove();
                const errMsg = "Đã xảy ra lỗi khi kết nối. Vui lòng kiểm tra kết nối internet và thử lại sau.";
                appendBotMessage(errMsg);
                saveMessageToHistory('assistant', errMsg);
                scrollToBottom();
            });
    }
    // --- Load welcome nếu lần đầu ---
    renderChatHistory();
    initChatAutoCleanup();
});
