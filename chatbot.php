<?php
session_start();
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Tư Vấn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
        
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
        
        .animate-pulse-ring {
            animation: pulse-ring 2s ease-out infinite;
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out infinite;
        }
        
        .chat-message {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Floating Contact Buttons -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-4">
        
        <!-- Zalo Button -->
        <div class="relative group">
            <div class="absolute inset-0 bg-blue-500 rounded-full animate-pulse-ring"></div>
            <a href="https://zalo.me/YOUR_ZALO_NUMBER" target="_blank" 
               class="relative flex items-center justify-center w-14 h-14 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg transition-all duration-300 hover:scale-110 animate-bounce-slow">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12c0 2.85 1.2 5.41 3.12 7.23L4 22l3.18-1.05C8.75 21.63 10.33 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm3.5 13.5h-7c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h7c.28 0 .5.22.5.5s-.22.5-.5.5zm0-3h-7c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h7c.28 0 .5.22.5.5s-.22.5-.5.5zm0-3h-7c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h7c.28 0 .5.22.5.5s-.22.5-.5.5z"/>
                </svg>
            </a>
            <span class="absolute right-16 top-1/2 -translate-y-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                Chat qua Zalo
            </span>
        </div>

        <!-- Phone Button -->
        <div class="relative group">
            <div class="absolute inset-0 bg-green-500 rounded-full animate-pulse-ring"></div>
            <a href="tel:+84123456789" 
               class="relative flex items-center justify-center w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg transition-all duration-300 hover:scale-110 animate-shake">
                <i class="fas fa-phone-alt text-xl"></i>
            </a>
            <span class="absolute right-16 top-1/2 -translate-y-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                Gọi ngay
            </span>
        </div>

        <!-- Chatbot Toggle Button -->
        <div class="relative group">
            <div class="absolute inset-0 bg-pink-500 rounded-full animate-pulse-ring"></div>
            <button id="chatbot-toggle" 
                    class="relative flex items-center justify-center w-14 h-14 bg-pink-500 hover:bg-pink-600 text-white rounded-full shadow-lg transition-all duration-300 hover:scale-110">
                <i class="fas fa-comments text-xl"></i>
            </button>
            <span class="absolute right-16 top-1/2 -translate-y-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                Tư vấn online
            </span>
        </div>
    </div>

    <!-- Chatbot Window -->
    <div id="chatbot-window" class="fixed bottom-24 right-6 w-96 h-[600px] bg-white rounded-2xl shadow-2xl z-40 hidden flex-col overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="images/Green And White Illustrative Flower Shop Logo.png" alt="Avatar" class="w-12 h-12 rounded-full border-2 border-white">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Tư vấn viên</h3>
                    <p class="text-pink-100 text-xs">Online</p>
                </div>
            </div>
            <button id="chatbot-close" class="text-white hover:bg-white/20 rounded-full p-2 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
            <!-- Bot Welcome Message -->
            <div class="flex gap-2 chat-message">
                <img src="images/Green And White Illustrative Flower Shop Logo.png" alt="Bot" class="w-8 h-8 rounded-full">
                <div class="bg-white rounded-2xl rounded-tl-none p-3 shadow-sm max-w-[80%]">
                    <p class="text-gray-800 text-sm">Xin chào! Tôi là trợ lý ảo của cửa hàng váy cưới. Tôi có thể giúp gì cho bạn?</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-2 ml-10">
                <button class="quick-action bg-pink-100 hover:bg-pink-200 text-pink-700 px-4 py-2 rounded-full text-sm transition-colors" data-message="Xem bộ sưu tập váy cưới">
                    <i class="fas fa-dress mr-1"></i> Xem váy cưới
                </button>
                <button class="quick-action bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-2 rounded-full text-sm transition-colors" data-message="Tư vấn chọn váy">
                    <i class="fas fa-user-tie mr-1"></i> Tư vấn chọn váy
                </button>
                <button class="quick-action bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-full text-sm transition-colors" data-message="Bảng giá dịch vụ">
                    <i class="fas fa-tags mr-1"></i> Bảng giá
                </button>
                <button class="quick-action bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-full text-sm transition-colors" data-message="Đặt lịch hẹn">
                    <i class="fas fa-calendar-check mr-1"></i> Đặt lịch
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-200">
            <div class="flex gap-2">
                <input type="text" id="chat-input" placeholder="Nhập tin nhắn..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-200 transition-all">
                <button id="send-message" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-full transition-colors">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Toggle chatbot window
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatbotWindow = document.getElementById('chatbot-window');
        const chatbotClose = document.getElementById('chatbot-close');
        const chatMessages = document.getElementById('chat-messages');
        const chatInput = document.getElementById('chat-input');
        const sendMessage = document.getElementById('send-message');

        chatbotToggle.addEventListener('click', () => {
            chatbotWindow.classList.toggle('hidden');
            chatbotWindow.classList.toggle('flex');
            if (!chatbotWindow.classList.contains('hidden')) {
                chatInput.focus();
            }
        });

        chatbotClose.addEventListener('click', () => {
            chatbotWindow.classList.add('hidden');
            chatbotWindow.classList.remove('flex');
        });

        // Send message function
        function addMessage(message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex gap-2 chat-message ${isUser ? 'justify-end' : ''}`;
            
            if (isUser) {
                messageDiv.innerHTML = `
                    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-2xl rounded-tr-none p-3 shadow-sm max-w-[80%]">
                        <p class="text-sm">${message}</p>
                    </div>
                `;
            } else {
                messageDiv.innerHTML = `
                    <img src="images/Green And White Illustrative Flower Shop Logo.png" alt="Bot" class="w-8 h-8 rounded-full">
                    <div class="bg-white rounded-2xl rounded-tl-none p-3 shadow-sm max-w-[80%]">
                        <p class="text-gray-800 text-sm">${message}</p>
                    </div>
                `;
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Bot responses
        function getBotResponse(userMessage) {
            const message = userMessage.toLowerCase();
            
            if (message.includes('váy cưới') || message.includes('xem')) {
                return 'Chúng tôi có nhiều mẫu váy cưới đẹp từ cổ điển đến hiện đại. Bạn có thể xem tại trang <a href="products.php" class="text-pink-500 underline">Sản phẩm</a> của chúng tôi.';
            } else if (message.includes('giá') || message.includes('bảng giá')) {
                return 'Giá thuê váy cưới của chúng tôi từ 2.000.000đ - 10.000.000đ tùy theo mẫu. Bạn muốn tôi tư vấn chi tiết hơn không?';
            } else if (message.includes('tư vấn') || message.includes('chọn')) {
                return 'Để tư vấn chính xác, bạn vui lòng cho tôi biết: chiều cao, số đo 3 vòng và phong cách yêu thích của bạn nhé!';
            } else if (message.includes('đặt lịch') || message.includes('hẹn')) {
                return 'Bạn có thể đặt lịch hẹn tại <a href="booking.php" class="text-pink-500 underline">đây</a> hoặc gọi hotline: 0123-456-789 để được hỗ trợ nhanh nhất.';
            } else if (message.includes('địa chỉ') || message.includes('ở đâu')) {
                return 'Cửa hàng chúng tôi tại: 123 Đường ABC, Quận XYZ, TP.HCM. Bạn có thể xem bản đồ tại trang <a href="contact.php" class="text-pink-500 underline">Liên hệ</a>.';
            } else {
                return 'Cảm ơn bạn đã nhắn tin. Bạn có thể hỏi tôi về: váy cưới, giá thuê, tư vấn chọn váy, đặt lịch hẹn, địa chỉ cửa hàng.';
            }
        }

        // Send message on button click
        sendMessage.addEventListener('click', () => {
            const message = chatInput.value.trim();
            if (message) {
                addMessage(message, true);
                chatInput.value = '';
                
                // Simulate bot typing
                setTimeout(() => {
                    const response = getBotResponse(message);
                    addMessage(response, false);
                }, 1000);
            }
        });

        // Send message on Enter key
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage.click();
            }
        });

        // Quick action buttons
        document.querySelectorAll('.quick-action').forEach(button => {
            button.addEventListener('click', () => {
                const message = button.getAttribute('data-message');
                addMessage(message, true);
                
                setTimeout(() => {
                    const response = getBotResponse(message);
                    addMessage(response, false);
                }, 1000);
            });
        });
    </script>

</body>
</html>
