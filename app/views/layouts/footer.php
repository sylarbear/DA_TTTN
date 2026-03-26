    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3><i class="fas fa-graduation-cap"></i> <?= APP_NAME ?></h3>
                    <p>Hệ thống học tiếng Anh trực tuyến theo chủ đề, tích hợp đánh giá kỹ năng nói bằng AI.</p>
                </div>
                <div class="footer-links">
                    <h4>Liên kết</h4>
                    <ul>
                        <li><a href="<?= BASE_URL ?>/topic">Chủ đề học</a></li>
                        <li><a href="<?= BASE_URL ?>/test">Bài kiểm tra</a></li>
                        <li><a href="<?= BASE_URL ?>/speaking">Luyện nói</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Hướng dẫn sử dụng</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Đồ án tốt nghiệp.</p>
            </div>
        </div>
    </footer>

    <?php if (Middleware::isLoggedIn()): ?>
    <!-- AI Chatbot Widget -->
    <div class="chatbot-fab" id="chatbotFab" onclick="toggleChatbot()">
        <i class="fas fa-robot"></i>
    </div>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <strong>AI Trợ lý</strong>
                    <small id="chatbotStatus">Sẵn sàng hỗ trợ</small>
                </div>
            </div>
            <button class="chatbot-close" onclick="toggleChatbot()"><i class="fas fa-times"></i></button>
        </div>

        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chat-msg bot">
                <div class="chat-bubble">
                    Xin chào! 👋 Mình là AI trợ lý của English Learning. Bạn có thể hỏi mình về:
                    <br>• Ngữ pháp tiếng Anh
                    <br>• Từ vựng & nghĩa
                    <br>• Dịch thuật
                    <br>• Mẹo học tiếng Anh
                </div>
            </div>
        </div>

        <div class="chatbot-suggestions" id="chatbotSuggestions">
            <button onclick="sendQuickQ('Giải thích cách dùng Present Perfect')">🕐 Present Perfect</button>
            <button onclick="sendQuickQ('Phân biệt since và for')">📝 Since vs For</button>
            <button onclick="sendQuickQ('Dịch: Tôi đang học tiếng Anh')">🔄 Dịch câu</button>
        </div>

        <div class="chatbot-input">
            <input type="text" id="chatbotInput" placeholder="Hỏi gì đó bằng tiếng Anh..." 
                   onkeydown="if(event.key==='Enter')sendChatMessage()">
            <button onclick="sendChatMessage()" id="chatSendBtn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
    const chatHistory = [];
    let chatbotAvailable = false;

    // Check chatbot status on load
    fetch('<?= BASE_URL ?>/chatbot/status', {credentials:'same-origin'})
    .then(r=>r.json()).then(d => {
        chatbotAvailable = d.available;
        if (!d.available) {
            document.getElementById('chatbotStatus').textContent = 'Chưa cấu hình AI';
        }
    }).catch(()=>{});

    function toggleChatbot() {
        const w = document.getElementById('chatbotWindow');
        const f = document.getElementById('chatbotFab');
        w.classList.toggle('open');
        f.classList.toggle('active');
        if (w.classList.contains('open')) {
            document.getElementById('chatbotInput').focus();
        }
    }

    function sendQuickQ(q) {
        document.getElementById('chatbotInput').value = q;
        sendChatMessage();
        document.getElementById('chatbotSuggestions').style.display = 'none';
    }

    function addMessage(text, isBot) {
        const msgs = document.getElementById('chatbotMessages');
        const div = document.createElement('div');
        div.className = 'chat-msg ' + (isBot ? 'bot' : 'user');
        div.innerHTML = '<div class="chat-bubble">' + formatMsg(text) + '</div>';
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function formatMsg(text) {
        // Basic markdown: **bold**, `code`, newlines
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/`(.*?)`/g, '<code>$1</code>')
            .replace(/\n/g, '<br>');
    }

    function sendChatMessage() {
        const input = document.getElementById('chatbotInput');
        const msg = input.value.trim();
        if (!msg) return;

        addMessage(msg, false);
        chatHistory.push({role:'user', content: msg});
        input.value = '';

        // Show typing
        const typing = document.createElement('div');
        typing.className = 'chat-msg bot typing-indicator';
        typing.innerHTML = '<div class="chat-bubble"><i class="fas fa-circle-notch fa-spin"></i> Đang suy nghĩ...</div>';
        document.getElementById('chatbotMessages').appendChild(typing);
        document.getElementById('chatbotMessages').scrollTop = 999999;

        fetch('<?= BASE_URL ?>/chatbot/send', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            credentials:'same-origin',
            body: JSON.stringify({ message: msg, history: chatHistory.slice(-6) })
        })
        .then(r=>r.json())
        .then(d => {
            typing.remove();
            if (d.success) {
                addMessage(d.message, true);
                chatHistory.push({role:'assistant', content: d.message});
            } else {
                addMessage('⚠️ ' + (d.error || 'Lỗi kết nối AI'), true);
            }
        })
        .catch(() => {
            typing.remove();
            addMessage('⚠️ Không thể kết nối. Vui lòng thử lại.', true);
        });
    }
    </script>
    <?php endif; ?>

    <!-- Global JS -->
    <script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>

