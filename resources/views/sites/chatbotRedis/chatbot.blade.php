<!DOCTYPE html>
<html>

<head>
    <title>Chatbot Tư Vấn</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f9f9f9;
        }

        .chat-box {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            background: white;
            height: 500px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .message {
            margin: 8px 0;
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 80%;
            display: inline-block;
        }

        .user {
            text-align: right;
        }

        .user .message {
            background: #d0eaff;
            color: #000;
            margin-left: auto;
        }

        .bot {
            text-align: left;
        }

        .bot .message {
            background: #e8f5e9;
            color: #000;
            margin-right: auto;
        }

        .input-area {
            max-width: 600px;
            margin: 15px auto;
            display: flex;
        }

        input[type=text] {
            flex: 1;
            padding: 10px;
            border-radius: 6px 0 0 6px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 15px;
            border: 1px solid #ccc;
            background: #007bff;
            color: white;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        img.chat-image {
            max-width: 200px;
            border-radius: 8px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="chat-box" id="chat-box"></div>

    <div class="input-area">
        <input type="text" id="message" placeholder="Nhập tin nhắn..."
            onkeydown="if(event.key==='Enter'){ sendMessage(); }" />
        <button onclick="sendMessage()">Gửi</button>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        const msgInput = document.getElementById('message');

        function addMessage(text, sender, isTyping = false) {
            const container = document.createElement('div');
            container.className = sender;

            const message = document.createElement('div');
            message.className = 'message';

            if (isTyping) {
                message.textContent = '';
                typeText(message, text);
            } else {
                // Nếu là URL ảnh thì hiển thị ảnh
                if (isImageUrl(text)) {
                    const img = document.createElement('img');
                    img.src = text;
                    img.alt = 'Hình ảnh';
                    img.className = 'chat-image';
                    message.appendChild(img);
                } else {
                    message.textContent = text;
                }
            }

            container.appendChild(message);
            chatBox.appendChild(container);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function typeText(element, text, index = 0) {
            if (index < text.length) {
                element.textContent += text.charAt(index);
                setTimeout(() => typeText(element, text, index + 1), 20);
            }
        }

        function isImageUrl(url) {
            return /^https?:\/\/.+\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(url) ||
                /cloudinary\.com\/.+/i.test(url);
        }

        function sendMessage() {
            const message = msgInput.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            msgInput.value = '';

            fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.reply) {
                        addMessage(data.reply, 'bot', true);

                        // if (data.images && Array.isArray(data.images)) {
                        //     data.images.forEach(url => {
                        //         addMessage(url, 'bot');
                        //     });
                        // }
                    } else {
                        addMessage('[Lỗi: Không nhận được phản hồi]', 'bot');
                    }
                })
                .catch(err => {
                    console.error(err);
                    addMessage('[Lỗi kết nối tới server]', 'bot');
                });
        }
    </script>

</body>

</html>
