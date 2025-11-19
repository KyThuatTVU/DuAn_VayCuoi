<!-- Chatbot Widget CSS -->
<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    
    @keyframes pulse-ring {
        0% { 
            transform: scale(0.9); 
            opacity: 0.8; 
        }
        50% {
            transform: scale(1.2);
            opacity: 0.4;
        }
        100% { 
            transform: scale(1.5); 
            opacity: 0; 
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: rotate(0deg); }
        10%, 30%, 50%, 70%, 90% { transform: rotate(-10deg); }
        20%, 40%, 60%, 80% { transform: rotate(10deg); }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes typing {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; }
    }
    
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }
    
    .animate-pulse-ring {
        animation: pulse-ring 2.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .animate-shake {
        animation: shake 1s ease-in-out infinite;
    }
    
    .chat-message {
        animation: slideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    .typing-indicator span {
        animation: typing 1.4s infinite;
    }
    
    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    /* Custom scrollbar */
    #chat-messages::-webkit-scrollbar {
        width: 6px;
    }
    
    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #chat-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    
    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Gradient animation */
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradient 3s ease infinite;
    }
</style>

<!-- Contact Buttons (Left Side) -->
<div id="contact-buttons" class="fixed bottom-6 left-6 z-50 flex flex-col gap-3 transition-all duration-300">
    
    <!-- Zalo Button -->
    <div class="relative group">
        <div class="absolute inset-0 bg-blue-500 rounded-full animate-pulse-ring opacity-75"></div>
        <a href="https://zalo.me/0123456789" target="_blank" 
           class="relative flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full shadow-2xl transition-all duration-300 hover:scale-110 hover:shadow-blue-500/50 animate-bounce-slow">
            <svg class="w-9 h-9" viewBox="0 0 48 48" fill="currentColor">
                <path d="M24 4C13.5 4 5 11.9 5 21.7c0 5.7 3 10.8 7.6 14.3L11 44l7.8-2.6c1.7.5 3.5.8 5.2.8 10.5 0 19-7.9 19-17.7S34.5 4 24 4zm7 27h-14c-.6 0-1-.4-1-1s.4-1 1-1h14c.6 0 1 .4 1 1s-.4 1-1 1zm0-6h-14c-.6 0-1-.4-1-1s.4-1 1-1h14c.6 0 1 .4 1 1s-.4 1-1 1zm0-6h-14c-.6 0-1-.4-1-1s.4-1 1-1h14c.6 0 1 .4 1 1s-.4 1-1 1z"/>
            </svg>
        </a>
        <div class="absolute left-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl pointer-events-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-comment-dots"></i>
                Chat qua Zalo
            </span>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>

    <!-- Phone Button -->
    <div class="relative group">
        <div class="absolute inset-0 bg-green-500 rounded-full animate-pulse-ring opacity-75"></div>
        <a href="tel:+84123456789" 
           class="relative flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-full shadow-2xl transition-all duration-300 hover:scale-110 hover:shadow-green-500/50 animate-shake">
            <i class="fas fa-phone-alt text-2xl"></i>
        </a>
        <div class="absolute left-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl pointer-events-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-phone-volume"></i>
                Gọi ngay: 0123-456-789
            </span>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>
</div>

<!-- Chatbot Button (Right Side) -->
<div id="chatbot-button" class="fixed bottom-6 right-6 z-50 transition-all duration-300">
    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full animate-pulse-ring opacity-75"></div>
        <button id="chatbot-toggle" 
                class="relative flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-500 via-pink-600 to-purple-600 hover:from-pink-600 hover:via-pink-700 hover:to-purple-700 text-white rounded-full shadow-2xl transition-all duration-300 hover:scale-110 hover:shadow-pink-500/50 animate-gradient">
            <i class="fas fa-headset text-2xl"></i>
            <span class="absolute -top-1 -right-1 flex h-5 w-5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center text-xs font-bold">3</span>
            </span>
        </button>
        <div class="absolute right-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl pointer-events-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-comments"></i>
                Tư vấn online 24/7
            </span>
            <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>
</div>

<!-- Chatbot Window -->
<div id="chatbot-window" class="fixed bottom-6 right-6 w-[380px] h-[550px] bg-white rounded-3xl shadow-2xl z-40 hidden flex-col overflow-hidden border border-gray-200 fade-in">
    
    <!-- Header -->
    <div class="bg-gradient-to-br from-pink-500 via-pink-600 to-purple-600 p-4 flex items-center justify-between animate-gradient relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-12 translate-x-12"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/10 rounded-full translate-y-10 -translate-x-10"></div>
        
        <div class="flex items-center gap-2 relative z-10">
            <div class="relative">
                <div class="w-11 h-11 rounded-full border-2 border-white bg-gradient-to-br from-white to-pink-50 flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-pink-600 text-lg"></i>
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full animate-pulse"></span>
            </div>
            <div>
                <h3 class="text-white font-bold text-base">Tư Vấn Viên</h3>
                <p class="text-pink-100 text-xs flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-300 rounded-full animate-pulse"></span>
                    Đang online
                </p>
            </div>
        </div>
        <button id="chatbot-close" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-300 hover:scale-110 hover:rotate-90 relative z-10">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Chat Messages -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gradient-to-b from-gray-50 to-white">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-xl p-3 border border-pink-100 shadow-sm chat-message">
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-gray-800 text-xs font-medium mb-1">👋 Xin chào! Chào mừng bạn đến với <span class="font-bold text-pink-600">Váy Cưới Thiên Thần</span></p>
                    <p class="text-gray-600 text-xs">Tôi là trợ lý ảo, sẵn sàng tư vấn giúp bạn tìm được chiếc váy cưới hoàn hảo! 💕</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-2">
            <p class="text-xs text-gray-500 font-medium px-1">Bạn quan tâm đến:</p>
            <div class="grid grid-cols-2 gap-2">
                <button class="quick-action group bg-white hover:bg-gradient-to-br hover:from-pink-500 hover:to-pink-600 border-2 border-pink-200 hover:border-pink-500 text-pink-700 hover:text-white px-3 py-2 rounded-xl text-xs font-medium transition-all duration-300 hover:shadow-lg hover:scale-105" data-message="Xem bộ sưu tập váy cưới">
                    <i class="fas fa-dress mr-1 group-hover:scale-110 transition-transform"></i>
                    <span>Váy cưới</span>
                </button>
                <button class="quick-action group bg-white hover:bg-gradient-to-br hover:from-purple-500 hover:to-purple-600 border-2 border-purple-200 hover:border-purple-500 text-purple-700 hover:text-white px-3 py-2 rounded-xl text-xs font-medium transition-all duration-300 hover:shadow-lg hover:scale-105" data-message="Tư vấn chọn váy">
                    <i class="fas fa-user-tie mr-1 group-hover:scale-110 transition-transform"></i>
                    <span>Tư vấn</span>
                </button>
                <button class="quick-action group bg-white hover:bg-gradient-to-br hover:from-blue-500 hover:to-blue-600 border-2 border-blue-200 hover:border-blue-500 text-blue-700 hover:text-white px-3 py-2 rounded-xl text-xs font-medium transition-all duration-300 hover:shadow-lg hover:scale-105" data-message="Bảng giá dịch vụ">
                    <i class="fas fa-tags mr-1 group-hover:scale-110 transition-transform"></i>
                    <span>Bảng giá</span>
                </button>
                <button class="quick-action group bg-white hover:bg-gradient-to-br hover:from-green-500 hover:to-green-600 border-2 border-green-200 hover:border-green-500 text-green-700 hover:text-white px-3 py-2 rounded-xl text-xs font-medium transition-all duration-300 hover:shadow-lg hover:scale-105" data-message="Đặt lịch hẹn">
                    <i class="fas fa-calendar-check mr-1 group-hover:scale-110 transition-transform"></i>
                    <span>Đặt lịch</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="p-3 bg-white border-t border-gray-200 shadow-lg">
        <div class="flex gap-2 items-center">
            <input type="text" id="chat-input" placeholder="Nhập tin nhắn..." 
                   class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-full focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition-all text-sm bg-gray-50 focus:bg-white">
            <button id="send-message" class="bg-gradient-to-br from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white p-2.5 rounded-full transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-pink-500/50 flex-shrink-0">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- Chatbot JavaScript -->
<script>
    // Toggle chatbot window
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendMessage = document.getElementById('send-message');

    const chatbotButton = document.getElementById('chatbot-button');

    chatbotToggle.addEventListener('click', () => {
        chatbotWindow.classList.toggle('hidden');
        chatbotWindow.classList.toggle('flex');
        
        // Ẩn/hiện nút chatbot khi mở/đóng chat
        if (!chatbotWindow.classList.contains('hidden')) {
            chatbotButton.classList.add('opacity-0', 'pointer-events-none');
            chatInput.focus();
        } else {
            chatbotButton.classList.remove('opacity-0', 'pointer-events-none');
        }
    });

    chatbotClose.addEventListener('click', () => {
        chatbotWindow.classList.add('hidden');
        chatbotWindow.classList.remove('flex');
        chatbotButton.classList.remove('opacity-0', 'pointer-events-none');
    });

    // Typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'flex gap-2 items-end chat-message';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <i class="fas fa-robot text-white text-sm"></i>
            </div>
            <div class="bg-white rounded-xl rounded-bl-none px-3 py-2 shadow-md border border-gray-100">
                <div class="typing-indicator flex gap-1">
                    <span class="w-1.5 h-1.5 bg-pink-400 rounded-full"></span>
                    <span class="w-1.5 h-1.5 bg-pink-400 rounded-full"></span>
                    <span class="w-1.5 h-1.5 bg-pink-400 rounded-full"></span>
                </div>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Send message function
    function addMessage(message, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex gap-2 items-end chat-message ${isUser ? 'justify-end' : ''}`;
        
        if (isUser) {
            messageDiv.innerHTML = `
                <div class="bg-gradient-to-br from-pink-500 to-purple-600 text-white rounded-xl rounded-br-none px-3 py-2 shadow-md max-w-[75%]">
                    <p class="text-xs leading-relaxed">${message}</p>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="bg-white rounded-xl rounded-bl-none px-3 py-2 shadow-md border border-gray-100 max-w-[75%]">
                    <p class="text-gray-800 text-xs leading-relaxed">${message}</p>
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
            chatInput.focus();
            
            // Show typing indicator
            showTypingIndicator();
            
            // Simulate bot typing delay
            setTimeout(() => {
                removeTypingIndicator();
                const response = getBotResponse(message);
                addMessage(response, false);
            }, 1500);
        }
    });

    // Send message on Enter key
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage.click();
        }
    });

    // Quick action buttons
    document.querySelectorAll('.quick-action').forEach(button => {
        button.addEventListener('click', () => {
            const message = button.getAttribute('data-message');
            addMessage(message, true);
            
            // Show typing indicator
            showTypingIndicator();
            
            setTimeout(() => {
                removeTypingIndicator();
                const response = getBotResponse(message);
                addMessage(response, false);
            }, 1500);
        });
    });

    // Auto focus input when window opens
    chatbotToggle.addEventListener('click', () => {
        setTimeout(() => {
            if (!chatbotWindow.classList.contains('hidden')) {
                chatInput.focus();
            }
        }, 300);
    });
</script>
