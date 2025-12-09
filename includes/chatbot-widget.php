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
<div id="contact-buttons" class="fixed bottom-6 left-6 z-50 flex flex-col-reverse gap-5 transition-all duration-300">
    
    <!-- Phone Button -->
    <div class="relative group">
        <!-- Pulse ring effect -->
        <span class="absolute inset-0 flex items-center justify-center">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
        </span>
        
        <a href="tel:0787972075" 
           class="relative flex items-center justify-center w-16 h-16 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <i class="fas fa-phone-alt text-2xl animate-pulse"></i>
        </a>
        
        <!-- Tooltip -->
        <div class="absolute left-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 shadow-xl pointer-events-none">
            Gọi: 078.797.2075
            <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>
    
    <!-- Zalo Button -->
    <div class="relative group">
        <!-- Pulse ring effect -->
        <span class="absolute inset-0 flex items-center justify-center">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
        </span>
        
        <a href="https://zalo.me/0787972075" target="_blank" 
           class="relative flex flex-col items-center justify-center w-16 h-16 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <span class="text-white text-xs font-bold tracking-wide animate-pulse">Zalo</span>
        </a>
        
        <!-- Tooltip -->
        <div class="absolute left-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 shadow-xl pointer-events-none">
            Chat qua Zalo
            <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <div class="relative group">
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
           class="flex items-center justify-center w-16 h-16 bg-cyan-400 hover:bg-cyan-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <i class="fas fa-arrow-up text-2xl"></i>
        </button>
        
        <!-- Tooltip -->
        <div class="absolute left-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 shadow-xl pointer-events-none">
            Lên đầu trang
            <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>
</div>

<!-- Chatbot Button (Right Side) -->
<div id="chatbot-button" class="fixed bottom-6 right-6 z-50 transition-all duration-300">
    <div class="relative group">
        <button id="chatbot-toggle" 
                class="flex items-center justify-center w-16 h-16 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 overflow-hidden border-2 border-pink-300 hover:border-pink-500 bg-white p-0">
            <img src="images/chatbot.webp" alt="Chatbot" class="w-full h-full object-cover rounded-full">
            
            <!-- Notification badge -->
            <span class="absolute -top-1 -right-1 flex h-5 w-5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center text-xs font-bold border-2 border-white text-white">3</span>
            </span>
        </button>
        
        <!-- Tooltip -->
        <div class="absolute right-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 shadow-xl pointer-events-none">
            Chat với Trà My 💬
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
                <div class="w-11 h-11 rounded-full border-2 border-white overflow-hidden shadow-lg">
                    <img src="images/chatbot.webp" alt="Chatbot" class="w-full h-full object-cover">
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full animate-pulse"></span>
            </div>
            <div>
                <h3 class="text-white font-bold text-base">Trà My</h3>
                <p class="text-pink-100 text-xs flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-300 rounded-full animate-pulse"></span>
                    Tư vấn viên online
                </p>
            </div>
        </div>
        <div class="flex items-center gap-1 relative z-10">
            <!-- Nút xem lịch sử chat -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <button id="chatbot-history" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-300 hover:scale-110" title="Xem lịch sử chat">
                <i class="fas fa-history text-sm"></i>
            </button>
            <?php endif; ?>
            <!-- Nút làm mới chat -->
            <button id="chatbot-refresh" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-300 hover:scale-110" title="Làm mới đoạn chat">
                <i class="fas fa-sync-alt text-sm"></i>
            </button>
            <button id="chatbot-close" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-300 hover:scale-110 hover:rotate-90 relative z-10">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Chat Messages -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gradient-to-b from-gray-50 to-white">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-xl p-3 border border-pink-100 shadow-sm chat-message">
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 shadow-md border border-pink-200">
                    <img src="images/chatbot.webp" alt="Bot" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
                        <p class="text-gray-800 text-xs font-medium mb-1">👋 Chào <span class="font-bold text-pink-600"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>! Em là <span class="font-bold text-pink-600">Trà My</span> - tư vấn viên của <span class="font-bold text-pink-600">Váy Cưới Thiên Thần</span></p>
                        <p class="text-gray-600 text-xs">Rất vui được gặp lại chị! Em sẵn sàng hỗ trợ chị bất cứ lúc nào! 💕</p>
                    <?php else: ?>
                        <p class="text-gray-800 text-xs font-medium mb-1">👋 Xin chào! Em là <span class="font-bold text-pink-600">Trà My</span> - tư vấn viên của <span class="font-bold text-pink-600">Váy Cưới Thiên Thần</span></p>
                        <p class="text-gray-600 text-xs">Em sẵn sàng tư vấn giúp chị tìm được chiếc váy cưới hoàn hảo nhất! 💕</p>
                    <?php endif; ?>
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

<!-- History Sessions Panel (for logged in users) -->
<?php if (isset($_SESSION['user_id'])): ?>
<div id="chatbot-history-panel" class="fixed bottom-6 right-6 w-[380px] h-[550px] bg-white rounded-3xl shadow-2xl z-40 hidden flex-col overflow-hidden border border-gray-200 fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 p-4 flex items-center justify-between animate-gradient relative overflow-hidden">
        <div class="flex items-center gap-2 relative z-10">
            <button id="history-back" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-300">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div>
                <h3 class="text-white font-bold text-base">Lịch sử trò chuyện</h3>
                <p class="text-indigo-100 text-xs">Xem lại các cuộc hội thoại trước</p>
            </div>
        </div>
    </div>
    
    <!-- Sessions List -->
    <div id="history-sessions-list" class="flex-1 overflow-y-auto p-3 space-y-2 bg-gradient-to-b from-gray-50 to-white">
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
            <p class="text-sm">Đang tải lịch sử...</p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Chatbot JavaScript - Powered by Groq AI -->
<script>
    // Thông tin người dùng đã đăng nhập
    const chatbotUser = {
        isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
        userId: <?php echo isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'null'; ?>,
        userName: <?php echo isset($_SESSION['user_name']) ? json_encode($_SESSION['user_name']) : 'null'; ?>,
        userEmail: <?php echo isset($_SESSION['user_email']) ? json_encode($_SESSION['user_email']) : 'null'; ?>
    };

    // Toggle chatbot window
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotRefresh = document.getElementById('chatbot-refresh');
    const chatbotHistoryBtn = document.getElementById('chatbot-history');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendMessage = document.getElementById('send-message');
    const chatbotButton = document.getElementById('chatbot-button');
    
    // History panel elements (only for logged in users)
    const historyPanel = document.getElementById('chatbot-history-panel');
    const historyBack = document.getElementById('history-back');
    const historySessionsList = document.getElementById('history-sessions-list');

    // Lưu lịch sử hội thoại để AI hiểu ngữ cảnh
    let conversationHistory = [];
    let currentSessionId = null;
    let historyLoaded = false;

    // Load lịch sử chat khi mở chatbot
    async function loadChatHistory() {
        if (historyLoaded) return;
        
        try {
            const response = await fetch('api/chatbot-history.php?action=get_history');
            const data = await response.json();
            
            if (data.success && data.messages && data.messages.length > 0) {
                currentSessionId = data.session_id;
                
                // Xóa nội dung mặc định và hiển thị lịch sử
                const welcomeCard = chatMessages.querySelector('.chat-message');
                const quickActions = chatMessages.querySelector('.space-y-2');
                
                // Hiển thị tin nhắn từ lịch sử
                data.messages.forEach(msg => {
                    if (msg.from === 'user') {
                        addMessage(msg.message, true, false);
                        conversationHistory.push({ role: 'user', content: msg.message });
                    } else {
                        addMessage(msg.message, false, false);
                        conversationHistory.push({ role: 'assistant', content: msg.message });
                    }
                });
                
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            historyLoaded = true;
        } catch (error) {
            console.error('Error loading chat history:', error);
            historyLoaded = true;
        }
    }

    // Làm mới đoạn chat
    async function refreshChat() {
        if (!confirm('Bạn có muốn làm mới đoạn chat? Lịch sử chat hiện tại sẽ được lưu lại.')) {
            return;
        }
        
        try {
            const response = await fetch('api/chatbot-history.php?action=clear_history', {
                method: 'POST'
            });
            const data = await response.json();
            
            if (data.success) {
                // Reset conversation
                conversationHistory = [];
                currentSessionId = data.new_session_id;
                historyLoaded = false;
                
                // Reset UI
                chatMessages.innerHTML = `
                    <!-- Welcome Card -->
                    <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-xl p-3 border border-pink-100 shadow-sm chat-message">
                        <div class="flex items-start gap-2">
                            <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 shadow-md border border-pink-200">
                                <img src="images/chatbot.webp" alt="Bot" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                ${chatbotUser.isLoggedIn ? 
                                    `<p class="text-gray-800 text-xs font-medium mb-1">👋 Chào <span class="font-bold text-pink-600">${chatbotUser.userName}</span>! Em là <span class="font-bold text-pink-600">Trà My</span> - tư vấn viên của <span class="font-bold text-pink-600">Váy Cưới Thiên Thần</span></p>
                                    <p class="text-gray-600 text-xs">Cuộc trò chuyện mới đã được bắt đầu! Em sẵn sàng hỗ trợ chị! 💕</p>` :
                                    `<p class="text-gray-800 text-xs font-medium mb-1">👋 Xin chào! Em là <span class="font-bold text-pink-600">Trà My</span> - tư vấn viên của <span class="font-bold text-pink-600">Váy Cưới Thiên Thần</span></p>
                                    <p class="text-gray-600 text-xs">Cuộc trò chuyện mới đã được bắt đầu! Em sẵn sàng tư vấn giúp chị! 💕</p>`
                                }
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
                `;
                
                // Re-bind quick action buttons
                bindQuickActions();
                
                historyLoaded = true;
            }
        } catch (error) {
            console.error('Error refreshing chat:', error);
            alert('Có lỗi xảy ra khi làm mới đoạn chat!');
        }
    }

    // Load danh sách phiên chat (cho user đã đăng nhập)
    async function loadChatSessions() {
        if (!chatbotUser.isLoggedIn || !historySessionsList) return;
        
        try {
            historySessionsList.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-sm">Đang tải lịch sử...</p>
                </div>
            `;
            
            const response = await fetch('api/chatbot-history.php?action=get_sessions');
            const data = await response.json();
            
            if (data.success && data.sessions && data.sessions.length > 0) {
                historySessionsList.innerHTML = data.sessions.map(session => `
                    <div class="session-item bg-white hover:bg-gray-50 border border-gray-200 rounded-xl p-3 cursor-pointer transition-all duration-300 hover:shadow-md" data-session-id="${session.session_id}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-medium truncate">${escapeHtml(session.preview)}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        ${formatDate(session.last_message_at)}
                                    </span>
                                    <span class="text-xs text-pink-500">
                                        <i class="fas fa-comments mr-1"></i>
                                        ${session.message_count} tin nhắn
                                    </span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 mt-1"></i>
                        </div>
                    </div>
                `).join('');
                
                // Bind click events
                document.querySelectorAll('.session-item').forEach(item => {
                    item.addEventListener('click', () => {
                        loadSessionMessages(item.dataset.sessionId);
                    });
                });
            } else {
                historySessionsList.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-comments text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">Chưa có lịch sử trò chuyện</p>
                        <p class="text-xs text-gray-400 mt-1">Bắt đầu chat để tạo lịch sử!</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
            historySessionsList.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p class="text-sm">Có lỗi xảy ra!</p>
                </div>
            `;
        }
    }

    // Load tin nhắn của một phiên chat cụ thể
    async function loadSessionMessages(sessionId) {
        try {
            const response = await fetch(`api/chatbot-history.php?action=get_history&session_id=${sessionId}`);
            const data = await response.json();
            
            if (data.success && data.messages) {
                // Đóng history panel và mở chat window
                historyPanel.classList.add('hidden');
                historyPanel.classList.remove('flex');
                chatbotWindow.classList.remove('hidden');
                chatbotWindow.classList.add('flex');
                
                // Reset và load messages
                conversationHistory = [];
                chatMessages.innerHTML = `
                    <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100 shadow-sm mb-3">
                        <p class="text-xs text-indigo-600 text-center">
                            <i class="fas fa-history mr-1"></i>
                            Đang xem lại lịch sử chat từ ${formatDate(data.messages[0]?.created_at || new Date())}
                        </p>
                    </div>
                `;
                
                data.messages.forEach(msg => {
                    if (msg.from === 'user') {
                        addMessage(msg.message, true, false);
                        conversationHistory.push({ role: 'user', content: msg.message });
                    } else {
                        addMessage(msg.message, false, false);
                        conversationHistory.push({ role: 'assistant', content: msg.message });
                    }
                });
                
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        } catch (error) {
            console.error('Error loading session messages:', error);
        }
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Vừa xong';
        if (diff < 3600000) return Math.floor(diff / 60000) + ' phút trước';
        if (diff < 86400000) return Math.floor(diff / 3600000) + ' giờ trước';
        if (diff < 604800000) return Math.floor(diff / 86400000) + ' ngày trước';
        
        return date.toLocaleDateString('vi-VN');
    }

    chatbotToggle.addEventListener('click', () => {
        chatbotWindow.classList.toggle('hidden');
        chatbotWindow.classList.toggle('flex');
        
        if (!chatbotWindow.classList.contains('hidden')) {
            chatbotButton.classList.add('opacity-0', 'pointer-events-none');
            chatInput.focus();
            
            // Load lịch sử chat khi mở
            loadChatHistory();
        } else {
            chatbotButton.classList.remove('opacity-0', 'pointer-events-none');
        }
    });

    chatbotClose.addEventListener('click', () => {
        chatbotWindow.classList.add('hidden');
        chatbotWindow.classList.remove('flex');
        chatbotButton.classList.remove('opacity-0', 'pointer-events-none');
    });

    // Refresh button event
    if (chatbotRefresh) {
        chatbotRefresh.addEventListener('click', refreshChat);
    }

    // History button event (only for logged in users)
    if (chatbotHistoryBtn && historyPanel) {
        chatbotHistoryBtn.addEventListener('click', () => {
            chatbotWindow.classList.add('hidden');
            chatbotWindow.classList.remove('flex');
            historyPanel.classList.remove('hidden');
            historyPanel.classList.add('flex');
            loadChatSessions();
        });
        
        historyBack.addEventListener('click', () => {
            historyPanel.classList.add('hidden');
            historyPanel.classList.remove('flex');
            chatbotWindow.classList.remove('hidden');
            chatbotWindow.classList.add('flex');
        });
    }

    // Typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'flex gap-2 items-end chat-message';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 shadow-md border border-pink-200">
                <img src="images/chatbot.webp" alt="Bot" class="w-full h-full object-cover">
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
    function addMessage(message, isUser = false, animate = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex gap-2 items-end ${animate ? 'chat-message' : ''} ${isUser ? 'justify-end' : ''}`;
        
        if (isUser) {
            messageDiv.innerHTML = `
                <div class="bg-gradient-to-br from-pink-500 to-purple-600 text-white rounded-xl rounded-br-none px-3 py-2 shadow-md max-w-[75%]">
                    <p class="text-xs leading-relaxed">${escapeHtml(message)}</p>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 shadow-md border border-pink-200">
                    <img src="images/chatbot.webp" alt="Bot" class="w-full h-full object-cover">
                </div>
                <div class="bg-white rounded-xl rounded-bl-none px-3 py-2 shadow-md border border-gray-100 max-w-[75%]">
                    <p class="text-gray-800 text-xs leading-relaxed">${message}</p>
                </div>
            `;
        }
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Fallback responses khi API lỗi
    function getFallbackResponse(userMessage) {
        const message = userMessage.toLowerCase();
        const greeting = chatbotUser.isLoggedIn ? `Dạ ${chatbotUser.userName} ơi` : 'Dạ chị ơi';
        
        if (message.includes('váy cưới') || message.includes('xem')) {
            return `${greeting}, bên em có rất nhiều mẫu váy cưới đẹp từ cổ điển đến hiện đại ạ! 👰 Chị có thể xem tại trang <a href="products.php" class="text-pink-500 underline font-medium">Bộ sưu tập</a> của shop nha!`;
        } else if (message.includes('giá') || message.includes('bảng giá')) {
            return `${greeting}, giá thuê váy cưới bên em từ 2.000.000đ - 15.000.000đ tùy mẫu ạ! 💰 Cho em biết ngân sách để em tư vấn mẫu phù hợp nhất nha!`;
        } else if (message.includes('tư vấn') || message.includes('chọn')) {
            return `${greeting}, để em tư vấn chính xác nhất, cho em biết: chiều cao, số đo 3 vòng và phong cách thích nha! 📝`;
        } else if (message.includes('đặt lịch') || message.includes('hẹn')) {
            return `${greeting}, có thể <a href="booking.php" class="text-pink-500 underline font-medium">đặt lịch hẹn tại đây</a> hoặc gọi hotline: 078.797.2075 ạ! 📅💕`;
        } else if (message.includes('địa chỉ') || message.includes('ở đâu')) {
            return `${greeting}, shop em ở: 123 Đường ABC, Quận XYZ, TP.HCM ạ! 📍 Xem <a href="contact.php" class="text-pink-500 underline font-medium">bản đồ tại đây</a> nha!`;
        } else {
            if (chatbotUser.isLoggedIn) {
                return `Dạ cảm ơn ${chatbotUser.userName} đã nhắn tin ạ! 💕 Em là Trà My, rất vui được hỗ trợ về váy cưới, giá thuê, tư vấn, đặt lịch hẹn nha!`;
            }
            return 'Dạ cảm ơn chị đã nhắn tin ạ! 💕 Em là Trà My, rất vui được hỗ trợ chị về váy cưới, giá thuê, tư vấn, đặt lịch hẹn nha!';
        }
    }

    // Gọi API Groq AI để lấy phản hồi thông minh
    async function getAIResponse(userMessage) {
        try {
            const response = await fetch('api/chatbot-ai.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: userMessage,
                    history: conversationHistory,
                    user: chatbotUser
                })
            });

            const data = await response.json();

            if (data.success && data.message) {
                // Lưu vào lịch sử hội thoại
                conversationHistory.push({ role: 'user', content: userMessage });
                conversationHistory.push({ role: 'assistant', content: data.message });
                
                // Giới hạn lịch sử 20 tin nhắn
                if (conversationHistory.length > 20) {
                    conversationHistory = conversationHistory.slice(-20);
                }
                
                return data.message;
            } else {
                // Fallback nếu API lỗi
                console.warn('AI API fallback:', data.error);
                return getFallbackResponse(userMessage);
            }
        } catch (error) {
            console.error('Chatbot AI error:', error);
            return getFallbackResponse(userMessage);
        }
    }

    // Send message handler
    async function handleSendMessage(message) {
        if (!message) return;
        
        addMessage(message, true);
        chatInput.value = '';
        chatInput.disabled = true;
        sendMessage.disabled = true;
        
        showTypingIndicator();
        
        try {
            const response = await getAIResponse(message);
            removeTypingIndicator();
            addMessage(response, false);
        } catch (error) {
            removeTypingIndicator();
            addMessage(getFallbackResponse(message), false);
        }
        
        chatInput.disabled = false;
        sendMessage.disabled = false;
        chatInput.focus();
    }

    // Send message on button click
    sendMessage.addEventListener('click', () => {
        const message = chatInput.value.trim();
        handleSendMessage(message);
    });

    // Send message on Enter key
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const message = chatInput.value.trim();
            handleSendMessage(message);
        }
    });

    // Quick action buttons - bind function
    function bindQuickActions() {
        document.querySelectorAll('.quick-action').forEach(button => {
            button.addEventListener('click', () => {
                const message = button.getAttribute('data-message');
                handleSendMessage(message);
            });
        });
    }
    
    // Initial bind
    bindQuickActions();

    // Auto focus input when window opens
    chatbotToggle.addEventListener('click', () => {
        setTimeout(() => {
            if (!chatbotWindow.classList.contains('hidden')) {
                chatInput.focus();
            }
        }, 300);
    });
</script>


<!-- Responsive CSS for Mobile -->
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        #floatingContact {
            left: 8px !important;
            bottom: 70px !important;
        }

        .floating-btn .w-16 {
            width: 3.5rem !important;
            height: 3.5rem !important;
        }

        .floating-btn svg {
            width: 1.5rem !important;
            height: 1.5rem !important;
        }

        .floating-btn .text-lg {
            font-size: 0.75rem !important;
        }

        .floating-btn .absolute.left-20 {
            left: 4rem;
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
    }

    @media (max-width: 640px) {
        #floatingContact {
            left: 6px !important;
            bottom: 60px !important;
        }

        .floating-btn .w-16 {
            width: 3rem !important;
            height: 3rem !important;
        }

        .floating-btn svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }

        .floating-btn .text-lg {
            font-size: 0.65rem !important;
        }
    }

    @media (max-width: 480px) {
        .floating-btn .absolute.left-20 {
            display: none !important;
        }

        #floatingContact {
            left: 4px !important;
            bottom: 50px !important;
        }

        .floating-btn .w-16 {
            width: 2.75rem !important;
            height: 2.75rem !important;
        }
    }
</style>
