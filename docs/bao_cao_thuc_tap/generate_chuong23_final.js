const fs = require('fs');
const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
        Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
        ShadingType, PageNumber, PageBreak, ImageRun } = require('docx');

const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const cm = { top: 60, bottom: 60, left: 100, right: 100 };
const DIAGRAMS = 'D:/xampp/htdocs/DA_TTTN/docs/diagrams';

function p(text, opts = {}) {
    return new Paragraph({ spacing: { after: 120, line: 360 }, ...opts,
        children: [new TextRun({ text, size: 26, font: "Times New Roman", ...opts.run })] });
}

function h(text, level) {
    return new Paragraph({ heading: level, spacing: { before: level === HeadingLevel.HEADING_1 ? 280 : 200, after: 140 },
        children: [new TextRun({ text, bold: true, font: "Times New Roman", size: level === HeadingLevel.HEADING_1 ? 30 : level === HeadingLevel.HEADING_2 ? 27 : 26 })] });
}

function bullet(text) {
    return new Paragraph({ numbering: { reference: "bullets", level: 0 }, spacing: { after: 60, line: 340 },
        children: [new TextRun({ text, size: 26, font: "Times New Roman" })] });
}

function img(filename, caption, w = 460, h = 310) {
    return [
        new Paragraph({ spacing: { before: 200, after: 80 }, children: [new ImageRun({
            type: "png", data: fs.readFileSync(`${DIAGRAMS}/${filename}.drawio.png`),
            transformation: { width: w, height: h },
            altText: { title: caption, description: caption, name: filename }
        })] }),
        new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 160 },
            children: [new TextRun({ text: caption, size: 22, font: "Times New Roman", italics: true, color: "333333" })] })
    ];
}

function tblRow(cells, isHeader = false) {
    return new TableRow({ children: cells.map(c => new TableCell({
        borders, width: { size: 9026 / cells.length, type: WidthType.DXA },
        shading: isHeader ? { fill: "1F4E79", type: ShadingType.CLEAR } : undefined,
        margins: cm, children: [new Paragraph({ spacing: { after: 0 },
            children: [new TextRun({ text: c, size: 22, font: "Times New Roman", bold: isHeader, color: isHeader ? "FFFFFF" : "000000" })] })]
    })) });
}

const doc = new Document({
    styles: {
        default: { document: { run: { font: "Times New Roman", size: 26 } } },
        paragraphStyles: [
            { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
              run: { size: 30, bold: true, font: "Times New Roman" }, paragraph: { spacing: { before: 240, after: 240 }, outlineLevel: 0 } },
            { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
              run: { size: 27, bold: true, font: "Times New Roman" }, paragraph: { spacing: { before: 200, after: 200 }, outlineLevel: 1 } },
            { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
              run: { size: 26, bold: true, font: "Times New Roman" }, paragraph: { spacing: { before: 160, after: 160 }, outlineLevel: 2 } },
        ]
    },
    numbering: { config: [{ reference: "bullets",
        levels: [{ level: 0, format: "bullet", text: "•", alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }] }] },
    sections: [{
        properties: { page: { size: { width: 11906, height: 16838 }, margin: { top: 1440, right: 1440, bottom: 1440, left: 1800 } } },
        headers: { default: new Header({ children: [new Paragraph({
            alignment: AlignmentType.CENTER, border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: "2E75B6", space: 4 } },
            children: [new TextRun({ text: "Báo cáo Thực tập Tốt nghiệp — EngPath", size: 18, font: "Times New Roman", italics: true, color: "666666" })]
        })] }) },
        footers: { default: new Footer({ children: [new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [new TextRun({ text: "Trang ", size: 18, font: "Times New Roman" }), new TextRun({ children: [PageNumber.CURRENT], size: 18, font: "Times New Roman" })]
        })] }) },
        children: [
            // ═══════════════ CHƯƠNG 2 ═══════════════
            h("Chương 2. CƠ SỞ LÝ THUYẾT", HeadingLevel.HEADING_1),

            h("2.1. Mô hình MVC (Model-View-Controller)", HeadingLevel.HEADING_2),
            p("EngPath được xây dựng theo mô hình MVC thuần (không sử dụng framework). Đây là mô hình kiến trúc phần mềm phổ biến nhất trong phát triển web, chia ứng dụng thành ba thành phần riêng biệt:"),
            bullet("Model (app/models/ — 9 file): Chịu trách nhiệm thao tác với cơ sở dữ liệu. Base Model cung cấp các phương thức CRUD chung. Mỗi model kế thừa từ Base Model, tương ứng với một bảng trong database."),
            bullet("View (app/views/ — 34 template): Chịu trách nhiệm hiển thị giao diện. Sử dụng PHP template thuần, tổ chức theo thư mục controller/action. Layout được tách riêng trong views/layouts/header.php và footer.php."),
            bullet("Controller (app/controllers/ — 15 file): Điều phối giữa Model và View. Nhận request từ người dùng, gọi Model để lấy dữ liệu, truyền dữ liệu vào View để render."),
            p("Luồng xử lý request: URL request → .htaccess (RewriteRule) → public/index.php (Front Controller) → App.php (Dispatcher) → Router.php (URL → Controller/Method) → Controller → Model (PDO → MySQL) → View (render HTML) → Response."),

            ...img("architecture", "Hình 2.1. Kiến trúc hệ thống EngPath — 15 Controllers, 9 Models, 34 Views, 23 bảng MySQL"),

            h("2.2. Công nghệ sử dụng", HeadingLevel.HEADING_2),
            h("2.2.1. PHP 8.0 và PDO", HeadingLevel.HEADING_3),
            p("PHP 8.0 được chọn với các đặc điểm: lập trình hướng đối tượng đầy đủ (class, inheritance, type hints), PDO (PHP Data Objects) cung cấp API thống nhất để làm việc với MySQL, Prepared Statements chống SQL Injection."),
            p("Cấu hình PDO trong EngPath: PDO::ERRMODE_EXCEPTION (ném exception khi lỗi SQL), PDO::FETCH_ASSOC (trả về mảng kết hợp), PDO::ATTR_EMULATE_PREPARES = false (native prepared statements)."),

            h("2.2.2. Hệ quản trị CSDL MySQL", HeadingLevel.HEADING_3),
            p("Database english_master gồm 23 bảng, thiết kế theo chuẩn 3NF, sử dụng InnoDB engine với foreign key constraints, utf8mb4 charset hỗ trợ tiếng Việt và emoji. Các bảng được tổ chức thành 4 nhóm chính:"),
            bullet("Nhóm Người dùng: users, user_progress, xp_history, bookmarks, vocab_reviews — quản lý tài khoản, tiến độ học tập, điểm kinh nghiệm, từ vựng đã lưu và lịch ôn tập."),
            bullet("Nhóm Học tập: topics, vocabularies, lessons, lesson_contents, lesson_reviews, grammar_lessons, grammar_questions — nội dung khóa học, từ vựng, bài học và ngữ pháp."),
            bullet("Nhóm Kiểm tra & Luyện nói: tests, questions, test_results, user_answers, speaking_prompts, speaking_attempts — bài kiểm tra, câu hỏi, kết quả và luyện nói AI."),
            bullet("Nhóm Membership & Hỗ trợ: membership_plans, membership_orders, activation_codes, wallet_transactions, support_tickets — gói hội viên, đơn nâng cấp, giao dịch ví và ticket hỗ trợ."),

            ...img("erd", "Hình 2.2. Sơ đồ Cơ sở dữ liệu EngPath — 23 bảng với quan hệ khóa ngoại"),

            h("2.2.3. HTML5, CSS3 và JavaScript", HeadingLevel.HEADING_3),
            p("Giao diện EngPath sử dụng HTML5 cho cấu trúc trang, CSS3 (custom properties, flexbox, grid, animation) cho thiết kế responsive, và JavaScript thuần (Vanilla JS) cho tương tác người dùng. Thư viện bổ trợ: Font Awesome 6.5 (icons), Chart.js 4.4 (biểu đồ dashboard), Google Fonts (Be Vietnam Pro, Inter)."),

            h("2.2.4. OpenAI API — AI Speaking & Chatbot", HeadingLevel.HEADING_3),
            p("EngPath tích hợp OpenAI GPT-3.5 Turbo cho hai tính năng chính: (1) AI Speaking Evaluation — gửi transcript từ Web Speech API và sample answer lên OpenAI, nhận điểm accuracy, fluency, pronunciation, overall và feedback tiếng Việt; nếu API không khả dụng, fallback về thuật toán so khớp từ cục bộ (word matching + similar_text). (2) AI Chatbot — trợ lý học tiếng Anh với system prompt 'English Learning AI Assistant', hỗ trợ lịch sử chat 6 tin nhắn gần nhất."),

            h("2.2.5. Google OAuth 2.0", HeadingLevel.HEADING_3),
            p("Đăng nhập bằng Google sử dụng OAuth 2.0 Authorization Code flow: redirect user đến Google consent screen → nhận authorization code → đổi lấy access token → gọi Google UserInfo API lấy thông tin (email, name, picture) → tìm user hiện có hoặc tạo mới → đăng nhập với session_regenerate_id()."),

            h("2.2.6. Bảo mật Web", HeadingLevel.HEADING_3),
            bullet("Prepared Statements: 100% truy vấn SQL sử dụng PDO prepare/execute — chống SQL Injection."),
            bullet("Session Security: HttpOnly cookie, SameSite=Lax, Secure flag (HTTPS), session_regenerate_id(true) sau login."),
            bullet("CSRF Protection: Token xác thực cho form POST (class CSRF), hash_equals() để so sánh an toàn."),
            bullet("Rate Limiting: Giới hạn 5 lần đăng nhập/60 giây (class RateLimiter, session-based)."),
            bullet("Input Validation: Validate tập trung qua Validator class (required, email, min, max, alphanumeric, in)."),
            bullet("Path Traversal Protection: Router chỉ cho phép ký tự a-zA-Z trong controller name."),
            bullet("Method Blocking: 25 method nội bộ (model, view, json, redirect...) bị chặn không cho gọi từ URL."),
            bullet("SSL Verification: CURLOPT_SSL_VERIFYPEER = true khi gọi OpenAI API."),

            new Paragraph({ children: [new PageBreak()] }),

            // ═══════════════ CHƯƠNG 3 ═══════════════
            h("Chương 3. NỘI DUNG THỰC TẬP", HeadingLevel.HEADING_1),

            h("3.1. Phân tích và thiết kế hệ thống", HeadingLevel.HEADING_2),

            h("3.1.1. Sơ đồ Use Case tổng quan", HeadingLevel.HEADING_3),
            p("Hệ thống EngPath có hai actor chính: Người học (Learner) và Quản trị viên (Admin). Ngoài ra còn có hai external actor: Google OAuth (xác thực đăng nhập) và OpenAI API (cung cấp AI Speaking và Chatbot)."),
            p("Người học có 9 use case: Đăng ký/Đăng nhập, Học từ vựng/Bài học, Làm bài kiểm tra, Luyện nói AI, Chatbot AI, Dashboard + Xếp hạng, Nâng cấp Pro (QR), Gửi Ticket hỗ trợ, Quản lý hồ sơ."),
            p("Quản trị viên có 6 use case: Dashboard/Thống kê, Quản lý Users, Quản lý Topics/Questions, Duyệt đơn Pro, Phản hồi Tickets, Cấu hình API Key."),

            ...img("usecase_overview", "Hình 3.1. Sơ đồ Use Case tổng quan — Người học (9 UC) + Quản trị viên (6 UC)"),

            h("3.1.2. Sơ đồ Sequence", HeadingLevel.HEADING_3),
            p("Các sơ đồ sequence mô tả luồng xử lý chi tiết cho các chức năng chính của hệ thống."),

            h("a) Đăng nhập", HeadingLevel.HEADING_3),
            p("Người dùng gửi username và password qua POST /auth/login. AuthController gọi User Model authenticate() để tìm user (theo username hoặc email) và kiểm tra mật khẩu bằng password_verify(). Nếu thành công, session được tạo với session_regenerate_id(true), dữ liệu người dùng được lưu vào SESSION, hệ thống tự động hạ cấp membership Pro nếu đã hết hạn, sau đó redirect về trang chủ (learner) hoặc admin."),

            ...img("sequence_login", "Hình 3.2. Sequence Diagram — Đăng nhập"),

            h("b) Luyện nói AI", HeadingLevel.HEADING_3),
            p("Browser sử dụng Web Speech API để nhận diện giọng nói, tạo ra transcript và confidence score. Dữ liệu được gửi lên server qua POST /speaking/score với JSON {prompt_id, transcript, confidence}. SpeakingController lấy prompt và sample_answer từ database, sau đó thử gọi OpenAIService::scoreSpeaking(). Nếu OpenAI API khả dụng, hệ thống gửi transcript và sample_answer lên GPT-3.5 Turbo, nhận về điểm accuracy, fluency, pronunciation, overall và feedback. Nếu API không khả dụng, hệ thống fallback về thuật toán local scoring (word matching + similar_text). Kết quả được lưu vào speaking_attempts, cập nhật user_progress, và thưởng XP nếu overall_score >= 30."),

            ...img("sequence_speaking", "Hình 3.3. Sequence Diagram — Luyện nói AI"),

            h("c) Nâng cấp Pro qua QR", HeadingLevel.HEADING_3),
            p("Quy trình nâng cấp Pro gồm ba pha. Pha 1 — User tạo đơn: User truy cập trang Membership, chọn gói Pro, nhập nội dung chuyển khoản, và gửi POST /membership/createOrder với JSON {plan_id, transfer_note}. MembershipController kiểm tra gói tồn tại, kiểm tra không có đơn pending trùng lặp, sau đó INSERT vào membership_orders với trạng thái pending và payment_method='bank_transfer'. Pha 2 — User chuyển khoản: User thực hiện chuyển khoản ngân hàng với nội dung khớp transfer_note đã khai báo, sau đó chờ admin xử lý. Pha 3 — Admin duyệt đơn: Admin kiểm tra app ngân hàng, nếu xác nhận đã nhận tiền thì gọi POST /admin/approveOrder. AdminController thực hiện giao dịch: BEGIN TRANSACTION → UPDATE membership_orders SET status='completed', expired_at=? → UPDATE users SET membership='pro', membership_expired_at=? → COMMIT. Nếu admin từ chối, hệ thống gọi POST /admin/rejectOrder để cập nhật status='cancelled'."),

            ...img("sequence_purchase", "Hình 3.4. Sequence Diagram — Nâng cấp Pro (QR + Admin duyệt)"),

            h("3.1.3. Sơ đồ trạng thái (State Diagram)", HeadingLevel.HEADING_3),
            p("Hệ thống có ba sơ đồ trạng thái chính:"),
            bullet("Membership: Trạng thái Free được chuyển thành Pro khi Admin duyệt đơn QR. Trạng thái Pro quay về Free khi hết hạn membership_expired_at hoặc admin chủ động hạ cấp."),
            bullet("Membership Order: Đơn được tạo với trạng thái Pending. Admin có thể duyệt (Pending → Completed) hoặc từ chối (Pending → Cancelled). Đơn Completed có thể bị hủy qua ticket (Completed → Cancelled) khi admin xử lý yêu cầu hủy đơn."),
            bullet("Support Ticket: Ticket được tạo với trạng thái Open. Admin xử lý chuyển sang In Progress. Sau khi phản hồi, ticket chuyển sang Resolved. Cuối cùng có thể đóng ticket (Resolved → Closed)."),

            ...img("state_diagrams", "Hình 3.5. Sơ đồ Trạng thái — Membership, Membership Order, Support Ticket"),

            h("3.2. Xây dựng ứng dụng", HeadingLevel.HEADING_2),

            h("3.2.1. Core MVC Framework", HeadingLevel.HEADING_3),
            p("Router (App.php + Router.php): Phân tích URL dạng /controller/method/params. Case-insensitive method matching dùng Reflection API. Chặn 25 method nội bộ (model, view, json, redirect, setFlash, isMethod, input, query, handleLogin, handleRegister, validateRegister...). Admin restriction: admin chỉ được phép truy cập AdminController và AuthController."),
            p("Base Controller: model() — load model và cache instance; view() — render với header/footer layout; viewPartial() — render không layout cho AJAX; json() — JSON response với HTTP status code; redirect(), setFlash(), isMethod(), input(), query()."),
            p("Base Model: CRUD đầy đủ với PDO prepared statements — all(), find(id), findBy(col, val), where(), create(data), update(id, data), delete(id), count(), raw(sql, params)."),
            p("Middleware: requireLogin(), requireAdmin(), requireStudent(), requirePro(), guest(), user(), isLoggedIn(), isAdmin(), isPro()."),

            h("3.2.2. Chức năng Người học", HeadingLevel.HEADING_3),
            p("Đăng ký & Đăng nhập (AuthController): Validate input (username >= 3 ký tự, email hợp lệ, password >= 6 ký tự). password_hash() với PASSWORD_DEFAULT. Google OAuth 2.0 Authorization Code flow. Auto-downgrade membership Pro hết hạn khi login. Rate limiting: 5 lần/60 giây."),
            p("Học từ vựng (TopicController + Vocabulary Model): Danh sách chủ đề kèm thống kê, lọc theo level. Chi tiết chủ đề với tab navigation (từ vựng, bài học, test, speaking). Flashcard lật thẻ. Đánh dấu từ đã học (AJAX → increment vocab_learned → award 10 XP). Tìm kiếm toàn văn trong topics, vocab, lessons, tests, grammar."),
            p("Bài kiểm tra (TestController): Ba loại test (quiz/listening/reading). Giao diện làm bài từng câu với timer. Tự động chấm điểm. Lưu test_result + user_answers. Hiển thị kết quả chi tiết."),
            p("Luyện nói AI (SpeakingController + OpenAIService): Web Speech API → transcript → POST /speaking/score → OpenAI GPT-3.5 Turbo chấm điểm → fallback local scoring → lưu attempt + update progress + award XP."),
            p("Dashboard + Gamification: XP, streak, level, daily goal. Biểu đồ Chart.js. Bảng xếp hạng top 10. Hệ thống badge."),
            p("Nâng cấp Pro (MembershipController): QR code + bank info. User tạo đơn pending → chuyển khoản → admin duyệt → kích hoạt Pro."),
            p("Ticket hỗ trợ (SupportController): Tạo ticket (general/cancel_order/bug_report/feedback). Chính sách hủy đơn: 24h đầu hoàn 100%, gói >= 3 tháng trong 7 ngày hoàn 50%."),

            h("3.2.3. Admin Panel", HeadingLevel.HEADING_3),
            p("Dashboard: Thống kê tổng quan (users, Pro users, topics, tests, questions, attempts, pending orders, pending tickets). Biểu đồ Chart.js: tăng trưởng user, phân bố điểm, đơn hàng theo tháng, tỷ lệ Free/Pro."),
            p("Quản lý Users: Danh sách + tìm kiếm (escape LIKE wildcards). Sửa thông tin qua AJAX. Xóa user + cascade delete dữ liệu liên quan."),
            p("Quản lý Topics/Questions: Thêm/sửa topic. Quản lý câu hỏi theo test (4 đáp án A/B/C/D + passage). Xóa question → cascade user_answers."),
            p("Duyệt đơn Pro: Danh sách đơn pending. Duyệt: BEGIN TRAN → UPDATE order completed → UPDATE user Pro → COMMIT. Từ chối: UPDATE order cancelled."),
            p("Phản hồi Tickets: Xem danh sách ticket theo trạng thái. Phản hồi (UPDATE admin_reply, status=resolved). Đổi trạng thái. Duyệt hủy đơn qua ticket."),

            h("3.3. Kiểm thử", HeadingLevel.HEADING_2),
            p("Unit Testing (PHPUnit 9.6): 19 test cases cho MembershipService (calculateExpiryDate), Request (json, post, get, isMethod, isAjax), Validator (required, email, min, max, combined rules)."),
            p("Static Analysis (PHPStan level 5): 0 errors trên toàn bộ codebase. Phát hiện sớm type errors, undefined methods, unused code."),
            p("Code Style (PHP-CS-Fixer PSR-12): Toàn bộ codebase được format tự động theo chuẩn PSR-12. Pre-commit hook kiểm tra syntax + code style + PHPStan + PHPUnit."),

            new Paragraph({ children: [new PageBreak()] }),

            // ═══ CHƯƠNG 4 ═══
            h("Chương 4. KẾT LUẬN VÀ KIẾN NGHỊ", HeadingLevel.HEADING_1),

            h("4.1. Kết quả đạt được", HeadingLevel.HEADING_2),
            bullet("Hệ thống MVC hoàn chỉnh: 15 controllers, 9 models, 34 views, 23 bảng database."),
            bullet("Đầy đủ chức năng Learner: đăng ký/đăng nhập (email + Google OAuth), học từ vựng, bài học, làm test, luyện nói AI, chatbot, dashboard, bảng xếp hạng."),
            bullet("Hệ thống nâng cấp Pro qua QR + admin duyệt, chính sách hủy đơn qua ticket."),
            bullet("Admin panel với 6 phân hệ: Dashboard, Users, Topics/Questions, Orders, Tickets, Settings."),
            bullet("Tích hợp OpenAI API (Speaking AI + Chatbot), Google OAuth 2.0."),
            bullet("Gamification: XP, streak, level, badge, bảng xếp hạng."),
            bullet("Bảo mật: Prepared Statements, CSRF, Rate Limiting, Session Security, Input Validation."),
            bullet("Chất lượng code: PHPStan level 5 (0 errors), PHPUnit 19 tests, PSR-12 formatting, Composer autoload, Monolog logging."),

            h("4.2. Hạn chế", HeadingLevel.HEADING_2),
            bullet("Chưa có chức năng quên mật khẩu / reset password qua email."),
            bullet("Web Speech API chỉ hoạt động tốt trên trình duyệt Chrome."),
            bullet("Chưa có real-time notification, upload ảnh/audio cho topic và bài học."),
            bullet("Chưa hỗ trợ đa ngôn ngữ (hiện chỉ có giao diện tiếng Việt)."),
            bullet("Chưa có CI/CD pipeline tự động."),

            h("4.3. Hướng phát triển", HeadingLevel.HEADING_2),
            bullet("Quên mật khẩu qua email, real-time notification khi admin duyệt đơn."),
            bullet("Phát triển mobile app sử dụng REST API."),
            bullet("Hỗ trợ đa ngôn ngữ (i18n), Docker containerization."),
            bullet("Tăng test coverage lên 70%+, CI/CD GitHub Actions."),
            bullet("Nâng cấp AI: GPT-4 hoặc model mới hơn, hỗ trợ nhiều giọng đọc."),
        ]
    }]
});

const outPath = 'D:/xampp/htdocs/DA_TTTN/docs/bao_cao_thuc_tap/BAO_CAO_CHUONG_2_3_FINAL.docx';
Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(outPath, buffer);
    console.log('Generated: ' + outPath);
    console.log('Size: ' + (buffer.length / 1024).toFixed(0) + ' KB');
}).catch(err => { console.error('Error:', err.message); process.exit(1); });
