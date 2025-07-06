<style>
    .toast-notification {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 12px 24px;
        background-color: #28a745;
        color: white;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1100;
    }

    .toast-notification.show {
        opacity: 1;
    }

    .toast-notification.error {
        background-color: #dc3545;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-clear-history {
        background: none;
        border: none;
        color: #ffffff;
        font-size: 1rem;
        margin-right: 10px;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-clear-history:hover {
        color: #dc3545;
    }

    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --chat-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --user-msg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --bot-msg: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.08);
        --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.12);
        --shadow-heavy: 0 15px 50px rgba(0, 0, 0, 0.15);
    }

    /* Chatbox Icon Styles */
    #chatbox-icon {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    #chatbox-icon button {
        background: var(--primary-gradient);
        border: none;
        width: 70px;
        height: 70px;
        box-shadow: var(--shadow-medium);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    #chatbox-icon button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    #chatbox-icon button:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: var(--shadow-heavy);
    }

    #chatbox-icon button:hover::before {
        left: 100%;
    }

    #chatbox-icon .fa-comments {
        transition: all 0.3s ease;
    }

    #chatbox-icon button:hover .fa-comments {
        transform: scale(1.1);
    }

    /* Pulse animation for attention */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
        }
    }

    #chatbox-icon button {
        animation: pulse 2s infinite;
    }

    /* Modal Styles */
    .modal-dialog-end {
        position: fixed;
        margin: 0;
        width: 300px;
        max-width: 90vw;
        height: 80vh;
        right: 0;
        top: 145px;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal.show .modal-dialog-end {
        transform: translateX(0);
    }

    .modal-content {
        height: 80vh;
        border-radius: 0 !important;
        border: none;
        box-shadow: var(--shadow-heavy);
        overflow: hidden;
    }

    /* Header Styles */
    .modal-header {
        background: var(--chat-bg);
        color: white;
        padding: 1.5rem;
        border-bottom: none;
        position: relative;
    }

    .modal-header::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-close-chatbot {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 16px;
        font-weight: bold;
    }

    .btn-close-chatbot:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    /* Messages Area */
    .modal-body {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        padding: 1.5rem;
        max-height: calc(80vh - 150px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(102, 126, 234, 0.3) transparent;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: rgba(102, 126, 234, 0.3);
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: rgba(102, 126, 234, 0.5);
    }

    /* Message Styles */
    .message-container {
        margin-bottom: 1rem;
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* User Messages */
    .user-message {
        background: var(--user-msg);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 18px 18px 4px 18px;
        max-width: 80%;
        box-shadow: var(--shadow-light);
        position: relative;
        word-wrap: break-word;
    }

    .user-message::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: -8px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-left-color: #667eea;
        border-bottom: none;
        border-right: none;
    }

    /* Bot Messages */
    .bot-message-container {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .bot-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: var(--shadow-light);
        flex-shrink: 0;
    }

    .bot-message {
        background: var(--bot-msg);
        border: 1px solid rgba(102, 126, 234, 0.1);
        padding: 0.75rem 1rem;
        border-radius: 18px 18px 18px 4px;
        max-width: 80%;
        box-shadow: var(--shadow-light);
        position: relative;
        word-wrap: break-word;
    }

    .bot-message::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -8px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-right-color: #f8f9ff;
        border-bottom: none;
        border-left: none;
    }

    /* Typing Indicator */
    .typing-indicator {
        background: var(--bot-msg);
        border: 1px solid rgba(102, 126, 234, 0.1);
        padding: 1rem;
        border-radius: 18px 18px 18px 4px;
        box-shadow: var(--shadow-light);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary-gradient);
        animation: typingBounce 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) {
        animation-delay: -0.32s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes typingBounce {

        0%,
        80%,
        100% {
            transform: scale(0.8);
            opacity: 0.5;
        }

        40% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Footer Input Area */
    .modal-footer {
        background: white;
        border-top: 1px solid rgba(102, 126, 234, 0.1);
        padding: 1.5rem;
    }

    .input-container {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    #chatbox-input {
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 25px;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s ease;
        background: rgba(248, 249, 255, 0.5);
    }

    #chatbox-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    #chatbox-send {
        background: var(--primary-gradient);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-light);
    }

    #chatbox-send:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-medium);
    }

    #chatbox-send:active {
        transform: scale(0.95);
    }

    /* Welcome Message */
    .welcome-message {
        text-align: center;
        padding: 2rem 1rem;
        color: #6b7280;
    }

    .welcome-message .bot-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin: 0 auto 1rem;
        box-shadow: var(--shadow-medium);
    }

    .welcome-message h4 {
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .welcome-message p {
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-dialog-end {
            width: 100vw;
            max-width: none;
        }

        #chatbox-icon {
            margin: 1rem !important;
        }

        #chatbox-icon button {
            width: 60px;
            height: 60px;
        }
    }

    /* Additional Animations */
    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .slide-up {
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /* chatbot css */
    .typing {
        display: inline-block;
        font-style: italic;
        color: #888;
    }

    .typing::after {
        content: '...';
        animation: blink 1s steps(3, start) infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }
    }


    /* modal search - css */

    .modal-container-search {
        position: relative;
        max-height: 80vh;
        overflow-y: auto;
        padding-right: 15px;
    }

    .modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1100;
        background: white;
        padding: 5px;
        border-radius: 50%;
        cursor: pointer;
    }

    .modal-close i {
        font-size: 1.5rem;
        color: #6c757d;
    }

    .modal-close:hover i {
        color: #dc3545;
    }

    #search-history li {
        font-size: 18px;
        padding: 12px 16px;
        border-bottom: 1px solid #ddd;
        transition: background 0.2s;
    }

    #search-history li:hover {
        background: #f1f1f1;
    }

    #search-history li i {
        font-size: 20px;
    }

    #search-history li .text-primary {
        font-size: 16px;
        cursor: pointer;
    }

    #suggestion-list li {
        font-size: 18px;
        transition: background 0.2s;
    }

    #suggestion-list li:hover {
        background: #f9f9f9;
        cursor: pointer;
    }

    /* Thêm vào file CSS của bạn */

    .product-card {
        background-color: #f8f9fa;
        /* Light background for the card */
        border: 1px solid #dee2e6;
        /* Light border */
        border-radius: 8px;
        /* Rounded corners */
        padding: 10px;
        margin-bottom: 10px;
        /* Space between product cards */
        display: flex;
        align-items: center;
        /* Align items vertically */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        /* Subtle shadow */
        transition: transform 0.2s ease-in-out;
        /* Smooth hover effect */
    }

    .product-card:hover {
        transform: translateY(-2px);
        /* Slightly lift on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        width: 60px;
        /* Kích thước ảnh sản phẩm */
        height: 60px;
        object-fit: cover;
        /* Đảm bảo ảnh không bị méo */
        border-radius: 4px;
        /* Slightly rounded corners for images */
        flex-shrink: 0;
        /* Prevent image from shrinking */
        margin-right: 15px;
        /* Space between image and text */
    }

    .product-info {
        flex-grow: 1;
        /* Allow product info to take available space */
    }

    .product-name {
        font-size: 0.95rem;
        color: #007bff;
        /* Blue for links */
        text-decoration: none;
        display: block;
        /* Make the link a block to ensure full clickable area */
        margin-bottom: 2px;
    }

    .product-name:hover {
        text-decoration: underline;
    }

    .product-price {
        font-size: 0.9rem;
        font-weight: bold;
        color: #dc3545;
        /* Red for price */
    }

    /* Optional: Adjust bot message styles if needed */
    .bot-message {
        /* Đảm bảo đủ rộng để chứa card sản phẩm */
        max-width: 80%;
        /* Or adjust as needed */
    }


    /* Quick Reply Buttons CSS */
    .quick-replies {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }

    .quick-reply-btn {
        background: white;
        border: 2px solid #e9ecef;
        color: #495057;
        padding: 12px 16px;
        border-radius: 25px;
        text-align: left;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .quick-reply-btn:hover {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }

    .quick-reply-btn i {
        margin-right: 10px;
        width: 18px;
        font-size: 16px;
    }

    /* Animation for quick replies */
    .quick-reply-btn {
        animation: slideInUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    .quick-reply-btn:nth-child(1) {
        animation-delay: 0.1s;
    }

    .quick-reply-btn:nth-child(2) {
        animation-delay: 0.2s;
    }

    .quick-reply-btn:nth-child(3) {
        animation-delay: 0.3s;
    }

    .quick-reply-btn:nth-child(4) {
        animation-delay: 0.4s;
    }

    .quick-reply-btn:nth-child(5) {
        animation-delay: 0.5s;
    }

    .quick-reply-btn:nth-child(6) {
        animation-delay: 0.6s;
    }





    .topic-guide {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .topic-guide h5 {
        margin: 0 0 10px 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .topic-guide p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }
</style>
