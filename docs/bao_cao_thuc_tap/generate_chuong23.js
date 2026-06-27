const fs = require('fs');
const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
        Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
        ShadingType, PageNumber, PageBreak, ImageRun } = require('docx');

// ── Helpers ──
const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const cellMargins = { top: 60, bottom: 60, left: 100, right: 100 };
const DIAGRAMS = 'D:/xampp/htdocs/DA_TTTN/docs/diagrams';

function p(text, opts = {}) {
    return new Paragraph({
        spacing: { after: 160, line: 360 },
        ...opts,
        children: [new TextRun({ text, size: 26, font: "Times New Roman", ...opts.run })]
    });
}

function heading(text, level) {
    return new Paragraph({
        heading: level,
        spacing: { before: 240, after: 160 },
        children: [new TextRun({ text, bold: true, font: "Times New Roman", size: level === HeadingLevel.HEADING_1 ? 32 : 28 })]
    });
}

function bullet(text) {
    return new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        spacing: { after: 80, line: 340 },
        children: [new TextRun({ text, size: 26, font: "Times New Roman" })]
    });
}

function diagram(filename, caption, width = 460) {
    const data = fs.readFileSync(`${DIAGRAMS}/${filename}.drawio.png`);
    return [
        new Paragraph({
            spacing: { before: 200, after: 100 },
            children: [new ImageRun({
                type: "png",
                data: data,
                transformation: { width: width, height: Math.round(width * 0.65) },
                altText: { title: caption, description: caption, name: filename }
            })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { after: 200 },
            children: [new TextRun({ text: caption, size: 22, font: "Times New Roman", italics: true, color: "333333" })]
        })
    ];
}

function tableRow(cells, isHeader = false) {
    return new TableRow({
        children: cells.map(c => new TableCell({
            borders,
            width: { size: 9026 / cells.length, type: WidthType.DXA },
            shading: isHeader ? { fill: "1F4E79", type: ShadingType.CLEAR } : undefined,
            margins: cellMargins,
            children: [new Paragraph({
                spacing: { after: 0 },
                children: [new TextRun({ text: c, size: 22, font: "Times New Roman", bold: isHeader, color: isHeader ? "FFFFFF" : "000000" })]
            })]
        }))
    });
}

// ── Build Document ──
const doc = new Document({
    styles: {
        default: { document: { run: { font: "Times New Roman", size: 26 } } },
        paragraphStyles: [
            { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
              run: { size: 32, bold: true, font: "Times New Roman" },
              paragraph: { spacing: { before: 240, after: 240 }, outlineLevel: 0 } },
            { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
              run: { size: 28, bold: true, font: "Times New Roman" },
              paragraph: { spacing: { before: 200, after: 200 }, outlineLevel: 1 } },
            { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
              run: { size: 26, bold: true, font: "Times New Roman" },
              paragraph: { spacing: { before: 160, after: 160 }, outlineLevel: 2 } },
        ]
    },
    numbering: {
        config: [{
            reference: "bullets",
            levels: [{ level: 0, format: "bullet", text: "•", alignment: AlignmentType.LEFT,
              style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
        }]
    },
    sections: [
        // ── CHƯƠNG 2: CƠ SỞ LÝ THUYẾT ──
        {
            properties: {
                page: {
                    size: { width: 11906, height: 16838 },
                    margin: { top: 1440, right: 1440, bottom: 1440, left: 1800 }
                }
            },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.CENTER,
                        border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: "2E75B6", space: 4 } },
                        children: [new TextRun({ text: "Báo cáo Thực tập Tốt nghiệp — EngPath", size: 18, font: "Times New Roman", italics: true, color: "666666" })]
                    })]
                })
            },
            footers: {
                default: new Footer({
                    children: [new Paragraph({
                        alignment: AlignmentType.CENTER,
                        children: [new TextRun({ text: "Trang ", size: 18, font: "Times New Roman" }), new TextRun({ children: [PageNumber.CURRENT], size: 18, font: "Times New Roman" })]
                    })]
                })
            },
            children: [
                heading("Chương 2. CƠ SỞ LÝ THUYẾT", HeadingLevel.HEADING_1),

                heading("2.1. Mô hình MVC (Model-View-Controller)", HeadingLevel.HEADING_2),
                p("EngPath được xây dựng theo mô hình MVC thuần (không sử dụng framework). Đây là mô hình kiến trúc phần mềm phổ biến nhất trong phát triển web, chia ứng dụng thành 3 thành phần riêng biệt:"),
                bullet("Model (app/models/): Chịu trách nhiệm thao tác với cơ sở dữ liệu. Base Model cung cấp các phương thức CRUD chung (all, find, findBy, where, create, update, delete, count, raw). Mỗi model kế thừa từ Base Model, tương ứng với một bảng trong database."),
                bullet("View (app/views/): Chịu trách nhiệm hiển thị giao diện. Sử dụng PHP template thuần, tổ chức theo thư mục controller/action. Layout được tách riêng trong views/layouts/header.php và footer.php."),
                bullet("Controller (app/controllers/): Điều phối giữa Model và View. Nhận request từ người dùng, gọi Model để lấy dữ liệu, truyền dữ liệu vào View để render. Base Controller cung cấp các helper: model(), view(), json(), redirect(), setFlash(), isMethod(), input(), query()."),

                p("Luồng xử lý request trong EngPath:"),
                p("URL request → .htaccess (RewriteRule) → public/index.php (Front Controller) → App.php (Dispatcher) → Router.php (URL → Controller/Method) → Controller → Model (PDO → MySQL) → View (render HTML) → Response"),

                ...diagram("architecture", "Hình 2.1. Kiến trúc hệ thống EngPath — MVC pattern với Front Controller, Router, Middleware, Services và External APIs"),

                heading("2.2. Công nghệ sử dụng", HeadingLevel.HEADING_2),

                heading("2.2.1. PHP 8.0 và PDO", HeadingLevel.HEADING_3),
                p("Ngôn ngữ PHP 8.0 được chọn vì hỗ trợ lập trình hướng đối tượng đầy đủ (class, inheritance, type hints, named arguments), PDO (PHP Data Objects) cung cấp API thống nhất để làm việc với MySQL. Prepared Statements mặc định chống SQL Injection."),
                p("Cấu hình PDO trong EngPath:"),
                bullet("PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION — ném exception khi có lỗi SQL"),
                bullet("PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC — trả về mảng kết hợp"),
                bullet("PDO::ATTR_EMULATE_PREPARES => false — sử dụng native prepared statements của MySQL"),

                heading("2.2.2. Hệ quản trị CSDL MySQL", HeadingLevel.HEADING_3),
                p("Database 'english_master' gồm 19 bảng, thiết kế theo chuẩn 3NF, sử dụng InnoDB engine với foreign key constraints và utf8mb4 charset để hỗ trợ tiếng Việt và emoji."),

                new Table({
                    width: { size: 9026, type: WidthType.DXA },
                    columnWidths: [2800, 3526, 2700],
                    rows: [
                        tableRow(["Bảng", "Mô tả", "Số trường"], true),
                        tableRow(["users", "Người dùng (student/admin) — trung tâm hệ thống", "18"]),
                        tableRow(["topics", "Chủ đề học tập (beginner/intermediate/advanced)", "8"]),
                        tableRow(["vocabularies", "Từ vựng theo chủ đề (word, meaning_vi, pronunciation)", "8"]),
                        tableRow(["lessons", "Bài học (title, content, sort_order)", "8"]),
                        tableRow(["tests", "Bài kiểm tra (quiz/listening/reading)", "8"]),
                        tableRow(["questions", "Câu hỏi (4 đáp án A/B/C/D, passage)", "8"]),
                        tableRow(["test_results", "Kết quả làm bài (score, total_points)", "8"]),
                        tableRow(["user_answers", "Câu trả lời của user (selected_answer, is_correct)", "7"]),
                        tableRow(["speaking_prompts", "Câu hỏi luyện nói (prompt_text, sample_answer)", "7"]),
                        tableRow(["speaking_attempts", "Lượt luyện nói (transcript, scores, feedback)", "9"]),
                        tableRow(["grammar_lessons", "Bài học ngữ pháp (title, content, category)", "7"]),
                        tableRow(["membership_plans", "Gói hội viên (name, duration_months, price)", "5"]),
                        tableRow(["membership_orders", "Đơn nâng cấp Pro (pending/completed/cancelled)", "11"]),
                        tableRow(["activation_codes", "Mã kích hoạt Pro (code, is_used, used_by)", "8"]),
                        tableRow(["wallet_transactions", "Giao dịch ví (deposit/withdraw/purchase/refund)", "9"]),
                        tableRow(["support_tickets", "Ticket hỗ trợ (open/in_progress/resolved/closed)", "8"]),
                        tableRow(["user_progress", "Tiến độ học tập (vocab_learned, is_completed)", "7"]),
                        tableRow(["xp_history", "Lịch sử XP (xp_amount, source, description)", "6"]),
                    ]
                }),

                ...diagram("erd", "Hình 2.2. Sơ đồ cơ sở dữ liệu EngPath — 19 bảng với quan hệ khóa ngoại"),

                heading("2.2.3. HTML5, CSS3 và JavaScript", HeadingLevel.HEADING_3),
                p("Giao diện EngPath sử dụng HTML5 cho cấu trúc trang, CSS3 (custom properties, flexbox, grid, animation) cho thiết kế responsive, và JavaScript thuần (Vanilla JS) cho tương tác người dùng. Các thư viện bổ trợ: Font Awesome 6.5 (icons), Chart.js 4.4 (biểu đồ dashboard), Google Fonts (Be Vietnam Pro, Inter)."),

                heading("2.2.4. OpenAI API — AI Speaking & Chatbot", HeadingLevel.HEADING_3),
                p("EngPath tích hợp OpenAI GPT-3.5 Turbo thông qua REST API cho 2 tính năng chính:"),
                bullet("AI Speaking Evaluation: Gửi transcript từ Web Speech API và sample answer lên OpenAI với system prompt yêu cầu trả về JSON chứa accuracy_score, fluency_score, pronunciation_score, overall_score và feedback bằng tiếng Việt. Fallback: nếu không có API key hoặc API lỗi, hệ thống dùng thuật toán so khớp từ cục bộ (word matching + similar_text)."),
                bullet("AI Chatbot: Trợ lý học tiếng Anh với system prompt 'English Learning AI Assistant'. Trả lời câu hỏi về ngữ pháp, từ vựng, dịch thuật. Hỗ trợ lịch sử chat (6 tin nhắn gần nhất). Giao diện floating widget."),

                ...diagram("sequence_speaking", "Hình 2.3. Sequence Diagram — Luồng luyện nói AI (Web Speech API → OpenAI → Feedback)"),

                heading("2.2.5. Google OAuth 2.0", HeadingLevel.HEADING_3),
                p("Đăng nhập bằng Google sử dụng OAuth 2.0 Authorization Code flow: redirect user đến Google consent screen → nhận authorization code → đổi lấy access token → gọi Google UserInfo API lấy email, name, picture → tìm user hiện có hoặc tạo mới → đăng nhập."),

                heading("2.2.6. Casso Webhook — Tự động xác nhận thanh toán", HeadingLevel.HEADING_3),
                p("Casso là dịch vụ cung cấp webhook khi có giao dịch vào tài khoản ngân hàng. EngPath nhận callback từ Casso, parse nội dung chuyển khoản theo format 'EMPRO {userId} GOI{planId}', tự động kiểm tra và kích hoạt gói Pro cho user."),

                heading("2.2.7. Bảo mật Web", HeadingLevel.HEADING_3),
                bullet("Prepared Statements — 100% truy vấn SQL sử dụng PDO prepare/execute"),
                bullet("Session Security — HttpOnly cookie, SameSite=Lax, Secure flag (HTTPS), session_regenerate_id() sau login"),
                bullet("CSRF Protection — Token xác thực cho form POST (class CSRF)"),
                bullet("Rate Limiting — Giới hạn 5 lần đăng nhập/60 giây (class RateLimiter, session-based)"),
                bullet("Input Validation — Validate tập trung qua Validator class (required, email, min, max, alphanumeric, in)"),
                bullet("Path Traversal Protection — Router chỉ cho phép ký tự a-zA-Z trong controller name"),
                bullet("Method Blocking — Các method nội bộ (model, view, json, redirect...) bị chặn không cho gọi từ URL"),
                bullet("SSL Verification — CURLOPT_SSL_VERIFYPEER = true khi gọi API bên ngoài"),

                new Paragraph({ children: [new PageBreak()] }),

                // ═══ CHƯƠNG 3: NỘI DUNG THỰC TẬP ═══
                heading("Chương 3. NỘI DUNG THỰC TẬP", HeadingLevel.HEADING_1),

                heading("3.1. Phân tích và thiết kế hệ thống", HeadingLevel.HEADING_2),

                heading("3.1.1. Sơ đồ Use Case", HeadingLevel.HEADING_3),
                p("Hệ thống EngPath có 2 actor chính: Learner (Người học) và Admin (Quản trị viên). Ngoài ra còn 3 external actor: Ngân hàng (Casso Webhook), Google (OAuth), OpenAI (API)."),
                p("Learner có 9 use case chính: Đăng ký/Đăng nhập, Học từ vựng/Bài học, Làm bài kiểm tra, Luyện nói AI + Chatbot, Dashboard + Bảng xếp hạng, Nâng cấp Pro (ví/CK/mã), Nạp/Rút tiền ví, Gửi ticket hỗ trợ/Hủy đơn, Quản lý hồ sơ cá nhân."),
                p("Admin có 4 use case chính: Quản lý Users, Quản lý Topics/Questions, Duyệt Đơn/Giao dịch Ví, Phản hồi Tickets."),

                ...diagram("usecase", "Hình 3.1. Sơ đồ Use Case — Learner (9 UC) + Admin (4 UC) + External Actors"),

                heading("3.1.2. Sơ đồ Sequence", HeadingLevel.HEADING_3),
                p("Các sequence diagram mô tả luồng xử lý chi tiết cho các chức năng chính của hệ thống."),

                ...diagram("sequence_login", "Hình 3.2. Sequence Diagram — Đăng nhập (AuthController → User Model → Database)"),
                ...diagram("sequence_purchase", "Hình 3.3. Sequence Diagram — Mua gói Pro qua ví điện tử (MembershipController → DB Transaction → MembershipService)"),

                heading("3.1.3. Sơ đồ State (Trạng thái)", HeadingLevel.HEADING_3),
                p("Các state diagram mô tả vòng đời của các đối tượng chính trong hệ thống:"),
                bullet("Membership: Free ↔ Pro (mua gói/kích hoạt code → Pro; hết hạn/admin hạ cấp → Free)"),
                bullet("Membership Order: Pending → Completed (admin duyệt/webhook) hoặc Cancelled (từ chối/hủy)"),
                bullet("Wallet Transaction: Pending → Completed (admin duyệt) hoặc Rejected (từ chối)"),
                bullet("Support Ticket: Open → In Progress → Resolved → Closed"),

                ...diagram("state_diagrams", "Hình 3.4. Sơ đồ Trạng thái — Membership, Order, Wallet Transaction, Support Ticket"),

                heading("3.1.4. Thiết kế cơ sở dữ liệu", HeadingLevel.HEADING_3),
                p("Database english_master gồm 19 bảng, tổ chức xoay quanh 3 thực thể trung tâm:"),
                bullet("users — trung tâm của mọi quan hệ: liên kết với test_results, speaking_attempts, user_progress, membership_orders, wallet_transactions, support_tickets, bookmarks, xp_history"),
                bullet("topics — đơn vị học tập: liên kết với vocabularies, lessons, tests, speaking_prompts, user_progress"),
                bullet("tests — bài kiểm tra: liên kết với questions, test_results, user_answers"),

                heading("3.2. Xây dựng ứng dụng", HeadingLevel.HEADING_2),

                heading("3.2.1. Core MVC Framework", HeadingLevel.HEADING_3),
                p("Xây dựng bộ khung MVC từ đầu, không sử dụng framework có sẵn:"),
                bullet("Router (App.php + Router.php): Phân tích URL dạng /controller/method/params. Case-insensitive method matching dùng Reflection API. Chặn 27 method nội bộ. Admin restriction (chỉ cho phép AdminController và AuthController)."),
                bullet("Base Controller: model() — load model và cache instance; view() — render với header/footer layout; viewPartial() — render không layout cho AJAX; json() — JSON response với HTTP status code; redirect(), setFlash(), isMethod(), input(), query()."),
                bullet("Base Model: CRUD đầy đủ với PDO prepared statements — all(), find(id), findBy(col, val), where(), create(data), update(id, data), delete(id), count(), raw(sql, params)."),
                bullet("Middleware: requireLogin(), requireAdmin(), requireStudent(), requirePro(), guest(), user(), isLoggedIn(), isAdmin(), isPro(). Hoạt động theo pattern: kiểm tra điều kiện → set flash message → redirect nếu không thỏa."),

                heading("3.2.2. Chức năng Người học (Learner)", HeadingLevel.HEADING_3),
                p("Đăng ký & Đăng nhập (AuthController): Validate input (username ≥3 ký tự, email hợp lệ, password ≥6 ký tự, password_confirm khớp). password_hash() với PASSWORD_DEFAULT. Google OAuth 2.0 Authorization Code flow. Auto-downgrade membership Pro hết hạn khi login. Session security: regenerate_id() + HttpOnly + SameSite."),
                p("Học từ vựng (TopicController + Vocabulary Model): Danh sách chủ đề kèm thống kê (số vocab, lessons, tests, speaking prompts). Lọc theo level (beginner/intermediate/advanced). Chi tiết chủ đề với tab navigation. Flashcard lật thẻ. Đánh dấu từ đã học (AJAX POST → increment vocab_learned → award XP). Tìm kiếm toàn văn trong topics, vocab, lessons, tests, grammar."),
                p("Bài kiểm tra (TestController): 3 loại test (quiz/listening/reading). Giao diện làm bài từng câu với timer đếm ngược. Radio button cho đáp án A/B/C/D. Tự động chấm điểm khi submit. Lưu test_result + user_answers. Hiển thị kết quả chi tiết: điểm số, câu đúng/sai, đáp án đúng."),
                p("Luyện nói AI (SpeakingController): Danh sách prompt theo chủ đề và độ khó. Web Speech API nhận diện giọng nói (Chrome). Gửi transcript lên OpenAI GPT-3.5 Turbo để chấm điểm (accuracy, fluency, pronunciation). Fallback: thuật toán so khớp từ + similar_text() khi không có API key. Lịch sử luyện nói."),
                p("Dashboard + Gamification: XP, streak, level, daily goal. Biểu đồ Chart.js. Bảng xếp hạng (top 10). Badge system. XpHistory log."),
                p("Membership Pro (MembershipController): Hiển thị gói (1/3/6 tháng) với giá. Thanh toán qua ví (BEGIN TRAN → SELECT balance FOR UPDATE → trừ tiền → INSERT order completed → COMMIT). Kích hoạt mã code. Chuyển khoản ngân hàng với QR code."),
                p("Ví điện tử (WalletController): Số dư, lịch sử giao dịch. Nạp tiền: tạo wallet_transaction (type=deposit, status=pending) → admin duyệt. Rút tiền: tạo wallet_transaction (type=withdraw) → admin duyệt."),
                p("Ticket hỗ trợ (SupportController): Tạo ticket (general/cancel_order/bug_report/feedback). Chính sách hủy đơn: 24h đầu hoàn 100%, gói ≥3 tháng trong 7 ngày hoàn 50%, quá hạn không hủy được. MembershipService.checkCancelEligibility()."),

                heading("3.2.3. Admin Panel", HeadingLevel.HEADING_3),
                p("Dashboard: Thống kê tổng quan (total users, Pro users, topics, tests, questions, attempts, unused codes, pending orders, pending tickets). Biểu đồ: tăng trưởng user 7 ngày, phân bố điểm test, đơn hàng 6 tháng, tỷ lệ Free/Pro."),
                p("Quản lý Users: Danh sách + tìm kiếm (escape LIKE wildcards). Sửa (full_name, email, role, membership) qua AJAX. Xóa user + cascade delete tất cả dữ liệu liên quan (test_results, user_answers, speaking_attempts, membership_orders, user_progress, bookmarks, xp_history)."),
                p("Quản lý Topics: Danh sách kèm subquery thống kê. Thêm/sửa qua AJAX modal."),
                p("Quản lý Questions: Chọn test → hiển thị câu hỏi. Thêm/sửa với 4 đáp án A/B/C/D + passage. Xóa → cascade delete user_answers."),
                p("Quản lý Orders: Duyệt đơn (BEGIN TRAN → tính expired_at → UPDATE order completed → UPDATE user Pro → COMMIT). Từ chối đơn."),
                p("Quản lý Tickets: Phản hồi (UPDATE admin_reply, status=resolved). Đổi trạng thái. Duyệt hủy đơn (tính refund → hoàn tiền vào ví → INSERT wallet_transaction refund)."),
                p("Quản lý Wallet: Duyệt giao dịch nạp/rút (SELECT balance FOR UPDATE → cộng/trừ → UPDATE balance + transaction completed). Từ chối."),

                heading("3.2.4. Webhook Ngân hàng (Casso)", HeadingLevel.HEADING_3),
                p("WebhookController::casso() nhận POST từ Casso: verify webhook secret → parse nội dung CK (regex /EMPRO\s+(\d+)\s+GOI(\d+)/i) → validate plan tồn tại + số tiền khớp (±1000đ) + user tồn tại → BEGIN TRAN → tìm/cập nhật order pending hoặc tạo mới → UPDATE user Pro → COMMIT. Log webhook để debug. Endpoint test và simulate cho admin."),

                heading("3.3. Kiểm thử", HeadingLevel.HEADING_2),
                p("Unit Testing (PHPUnit 9.6): 19 test cases cho MembershipService (calculateExpiryDate), Request (json, post, get, isMethod, isAjax), Validator (required, email, min, max, combined rules)."),
                p("Static Analysis (PHPStan level 5): 0 errors trên 90 PHP files. Phát hiện sớm type errors, undefined methods, unused code."),
                p("Code Style (PHP-CS-Fixer PSR-12): Toàn bộ codebase được format tự động. Pre-commit hook kiểm tra syntax + code style + PHPStan + PHPUnit."),

                new Paragraph({ children: [new PageBreak()] }),

                // ── CHƯƠNG 4: KẾT LUẬN ──
                heading("Chương 4. KẾT LUẬN VÀ KIẾN NGHỊ", HeadingLevel.HEADING_1),

                heading("4.1. Kết quả đạt được", HeadingLevel.HEADING_2),
                bullet("Hệ thống MVC hoàn chỉnh với 16 controllers, 9 models, 36 views, 90 PHP files"),
                bullet("Đầy đủ chức năng learner: đăng ký/đăng nhập (email + Google OAuth), học từ vựng, đọc bài học, làm test, luyện nói AI, chatbot, dashboard, bảng xếp hạng"),
                bullet("Hệ thống membership: gói Free/Pro, thanh toán qua ví, chuyển khoản ngân hàng + webhook tự động, mã kích hoạt"),
                bullet("Ví điện tử: nạp/rút tiền, lịch sử giao dịch, admin duyệt"),
                bullet("Ticket hỗ trợ + chính sách hủy đơn (hoàn tiền theo thời gian)"),
                bullet("Admin panel với 9 phân hệ quản lý"),
                bullet("Tích hợp OpenAI API (Speaking AI + Chatbot), Google OAuth 2.0, Casso Webhook"),
                bullet("Gamification: XP, streak, level, badge, bảng xếp hạng"),
                bullet("Bảo mật: Prepared Statements, CSRF, Rate Limiting, Session Security"),
                bullet("Chất lượng code: PHPStan level 5 (0 errors), PHPUnit 19 tests, PSR-12 formatting"),

                heading("4.2. Hạn chế", HeadingLevel.HEADING_2),
                bullet("Chưa có chức năng quên mật khẩu / reset password qua email"),
                bullet("Web Speech API chỉ hoạt động tốt trên Chrome"),
                bullet("Chưa có real-time notification, upload ảnh/audio cho topic"),
                bullet("Chưa hỗ trợ đa ngôn ngữ (chỉ tiếng Việt)"),
                bullet("Chưa có CI/CD pipeline tự động"),

                heading("4.3. Hướng phát triển", HeadingLevel.HEADING_2),
                bullet("Quên mật khẩu qua email, real-time notification"),
                bullet("Mobile App sử dụng REST API"),
                bullet("Đa ngôn ngữ (i18n), Docker containerization"),
                bullet("Tăng test coverage lên 70%+, CI/CD GitHub Actions"),
                bullet("Nâng cấp AI: GPT-4 hoặc model mới hơn"),
            ]
        }
    ]
});

// ── Generate ──
const outPath = 'D:/xampp/htdocs/DA_TTTN/docs/bao_cao_thuc_tap/BAO_CAO_CHUONG_2_3.docx';
Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(outPath, buffer);
    console.log('Generated: ' + outPath);
    console.log('Size: ' + (buffer.length / 1024).toFixed(0) + ' KB');
}).catch(err => {
    console.error('Error:', err.message);
    process.exit(1);
});
