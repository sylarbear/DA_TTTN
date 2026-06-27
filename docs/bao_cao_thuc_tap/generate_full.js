const fs = require('fs');
const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
        Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
        ShadingType, PageNumber, PageBreak, ImageRun } = require('docx');

const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const cm = { top: 60, bottom: 60, left: 100, right: 100 };
const DIA = 'D:/xampp/htdocs/DA_TTTN/docs/diagrams';
const OLD = 'D:/xampp/htdocs/DA_TTTN/docs/tuan3_unpacked/word/media';

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
function diagram(filename, caption, w = 460, h = 310) {
    return [
        new Paragraph({ spacing: { before: 200, after: 80 }, children: [new ImageRun({
            type: "png", data: fs.readFileSync(`${DIA}/${filename}`),
            transformation: { width: w, height: h },
            altText: { title: caption, description: caption, name: filename }
        })] }),
        new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 160 },
            children: [new TextRun({ text: caption, size: 22, font: "Times New Roman", italics: true, color: "333333" })] })
    ];
}
function screenshot(oldName, caption, w = 440, h = 280) {
    return [
        new Paragraph({ spacing: { before: 160, after: 60 }, children: [new ImageRun({
            type: "png", data: fs.readFileSync(`${OLD}/${oldName}`),
            transformation: { width: w, height: h },
            altText: { title: caption, description: caption, name: oldName }
        })] }),
        new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 140 },
            children: [new TextRun({ text: caption, size: 22, font: "Times New Roman", italics: true, color: "333333" })] })
    ];
}

// UI screenshots from old report (image files)
const SS = {
    login: 'image16.png',
    homepage: 'image18.png',
    topics: 'image19.png',
    topic_detail: 'image20.png',
    flashcard: 'image21.png',
    test: 'image22.png',
    speaking: 'image23.png',
    dashboard: 'image24.png',
    membership: 'image26.png',
    admin_dashboard: 'image28.png',
    admin_orders: 'image29.png',
    admin_tickets: 'image30.png',
};

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
            // ═══ CHƯƠNG 2 ═══
            h("Chương 2. CƠ SỞ LÝ THUYẾT", HeadingLevel.HEADING_1),
            h("2.1. Mô hình MVC", HeadingLevel.HEADING_2),
            p("EngPath được xây dựng theo mô hình MVC thuần (không sử dụng framework). Model (9 file) thao tác CSDL qua PDO. View (34 template) hiển thị giao diện với PHP template thuần. Controller (15 file) điều phối giữa Model và View."),
            p("Luồng xử lý: URL → .htaccess → public/index.php (Front Controller) → Router → Controller → Model (PDO → MySQL) → View (render HTML) → Response."),
            ...diagram("architecture.drawio.png", "Hình 2.1. Kiến trúc hệ thống EngPath"),

            h("2.2. Công nghệ sử dụng", HeadingLevel.HEADING_2),
            h("2.2.1. PHP 8.0 và PDO", HeadingLevel.HEADING_3),
            p("PHP 8.0 với PDO: ERRMODE_EXCEPTION, FETCH_ASSOC, native prepared statements (EMULATE_PREPARES=false)."),
            h("2.2.2. MySQL", HeadingLevel.HEADING_3),
            p("Database english_master: 23 bảng, InnoDB, utf8mb4, chuẩn 3NF. Tổ chức 4 nhóm: Người dùng, Học tập, Kiểm tra & Luyện nói, Membership & Hỗ trợ."),
            ...diagram("erd.drawio.png", "Hình 2.2. Sơ đồ CSDL — 23 bảng"),
            h("2.2.3. HTML5, CSS3, JavaScript", HeadingLevel.HEADING_3),
            p("Giao diện responsive với CSS custom properties, flexbox, grid. Thư viện: Font Awesome 6.5, Chart.js 4.4, Google Fonts (Be Vietnam Pro, Inter)."),
            h("2.2.4. OpenAI API", HeadingLevel.HEADING_3),
            p("GPT-3.5 Turbo cho Speaking AI (chấm điểm + feedback tiếng Việt) và Chatbot (trợ lý học tiếng Anh). Fallback local scoring khi API không khả dụng."),
            h("2.2.5. Google OAuth 2.0", HeadingLevel.HEADING_3),
            p("Authorization Code flow: redirect → consent → code → access_token → UserInfo → tìm/tạo user → login."),
            h("2.2.6. Bảo mật Web", HeadingLevel.HEADING_3),
            bullet("Prepared Statements 100% — chống SQL Injection."),
            bullet("Session Security: HttpOnly, SameSite=Lax, Secure flag (HTTPS), session_regenerate_id()."),
            bullet("CSRF Protection (class CSRF), Rate Limiting (5 lần/60s cho login)."),
            bullet("Input Validation qua Validator class. Path Traversal Protection trong Router."),
            bullet("SSL Verification: CURLOPT_SSL_VERIFYPEER=true."),

            new Paragraph({ children: [new PageBreak()] }),

            // ═══ CHƯƠNG 3 ═══
            h("Chương 3. NỘI DUNG THỰC TẬP", HeadingLevel.HEADING_1),
            h("3.1. Phân tích và thiết kế hệ thống", HeadingLevel.HEADING_2),
            h("3.1.1. Sơ đồ Use Case", HeadingLevel.HEADING_3),
            p("Hệ thống có 2 actor: Người học (9 UC) và Quản trị viên (6 UC). External actors: Google OAuth (xác thực) và OpenAI API (Speaking + Chatbot)."),
            ...diagram("usecase_overview.drawio.png", "Hình 3.1. Sơ đồ Use Case tổng quan"),

            h("3.1.2. Sơ đồ Sequence", HeadingLevel.HEADING_3),
            p("a) Đăng nhập: POST /auth/login → authenticate() → password_verify() → session_regenerate_id() → redirect."),
            ...diagram("sequence_login.drawio.png", "Hình 3.2. Sequence — Đăng nhập"),
            p("b) Luyện nói AI: Web Speech API → transcript → POST /speaking/score {prompt_id, transcript, confidence} → OpenAI hoặc local scoring → saveAttempt → update user_progress → award XP."),
            ...diagram("sequence_speaking.drawio.png", "Hình 3.3. Sequence — Luyện nói AI"),
            p("c) Nâng cấp Pro (QR): User chọn gói → POST /membership/createOrder → INSERT order pending → chuyển khoản → Admin approveOrder → BEGIN TRAN → UPDATE order + user → COMMIT."),
            ...diagram("sequence_purchase.drawio.png", "Hình 3.4. Sequence — Nâng cấp Pro (QR)"),

            h("3.1.3. Sơ đồ trạng thái", HeadingLevel.HEADING_3),
            p("Membership: Free ↔ Pro. Membership Order: Pending → Completed/Cancelled. Support Ticket: Open → InProgress → Resolved → Closed."),
            ...diagram("state_diagrams.drawio.png", "Hình 3.5. State Diagram"),

            new Paragraph({ children: [new PageBreak()] }),

            h("3.2. Xây dựng giao diện ứng dụng", HeadingLevel.HEADING_2),
            p("Giao diện EngPath được thiết kế theo phong cách hiện đại, lấy cảm hứng từ các nền tảng học ngoại ngữ như Busuu và Duolingo. Bố cục rõ ràng, màu sắc tươi sáng, tập trung vào trải nghiệm người học. Dưới đây là các màn hình chính của hệ thống."),

            h("3.2.1. Trang đăng nhập", HeadingLevel.HEADING_3),
            p("Giao diện đăng nhập gồm form nhập username/password và nút đăng nhập bằng Google OAuth. Form được thiết kế đơn giản, tập trung, có validation hiển thị lỗi rõ ràng. Người dùng chưa có tài khoản có thể chuyển sang trang đăng ký."),
            ...screenshot(SS.login, "Hình 3.6. Giao diện trang đăng nhập"),

            h("3.2.2. Trang chủ", HeadingLevel.HEADING_3),
            p("Trang chủ được thiết kế dạng landing page với hero section lớn, phone mockup hiển thị preview giao diện học, các số liệu thống kê (topics, lessons, vocabulary, tests), goal panel chọn mục tiêu học tập, feature cards giới thiệu lộ trình học và luyện nói AI, course grid hiển thị danh sách chủ đề, và final CTA kêu gọi đăng ký."),
            ...screenshot(SS.homepage, "Hình 3.7. Giao diện trang chủ"),

            h("3.2.3. Danh sách chủ đề", HeadingLevel.HEADING_3),
            p("Trang danh sách chủ đề hiển thị các khóa học dưới dạng grid card. Mỗi card hiển thị tên chủ đề, mô tả, level badge (beginner/intermediate/advanced), và số lượng từ vựng, bài học, bài test. Có filter bar để lọc theo cấp độ."),
            ...screenshot(SS.topics, "Hình 3.8. Giao diện danh sách chủ đề"),

            h("3.2.4. Chi tiết chủ đề", HeadingLevel.HEADING_3),
            p("Trang chi tiết chủ đề sử dụng tab navigation với 4 tab: Từ vựng, Bài học, Bài test, và Luyện nói. Mỗi tab hiển thị nội dung tương ứng. Phần progress bar hiển thị tiến độ học tập của người dùng đối với chủ đề này."),
            ...screenshot(SS.topic_detail, "Hình 3.9. Giao diện chi tiết chủ đề"),

            h("3.2.5. Flashcard", HeadingLevel.HEADING_3),
            p("Tính năng flashcard cho phép người dùng học từ vựng bằng thẻ lật. Mặt trước hiển thị từ tiếng Anh và phiên âm, mặt sau hiển thị nghĩa tiếng Việt và câu ví dụ. Người dùng có thể đánh dấu 'Đã biết' hoặc 'Chưa biết' để hệ thống điều chỉnh tiến độ ôn tập. Có progress bar hiển thị tiến độ học."),
            ...screenshot(SS.flashcard, "Hình 3.10. Giao diện Flashcard"),

            h("3.2.6. Bài kiểm tra", HeadingLevel.HEADING_3),
            p("Trang bài kiểm tra hiển thị danh sách các bài test được phân loại theo chủ đề và loại (Quiz, Listening, Reading). Khi làm bài, giao diện hiển thị từng câu hỏi một với 4 đáp án A/B/C/D, timer đếm ngược, và navigation dots để di chuyển giữa các câu. Sau khi nộp bài, kết quả được hiển thị chi tiết với điểm số, câu đúng/sai, và đáp án đúng."),
            ...screenshot(SS.test, "Hình 3.11. Giao diện bài kiểm tra"),

            h("3.2.7. Luyện nói", HeadingLevel.HEADING_3),
            p("Trang luyện nói cho phép người dùng chọn prompt theo chủ đề và độ khó. Giao diện luyện nói gồm hai cột: bên trái hiển thị sample text cần đọc, bên phải là khu vực ghi âm với nút record. Web Speech API nhận diện giọng nói và hiển thị transcript. Sau khi hoàn thành, hệ thống hiển thị điểm số (accuracy, fluency, pronunciation, overall) dưới dạng score rings và feedback từ AI."),
            ...screenshot(SS.speaking, "Hình 3.12. Giao diện luyện nói AI"),

            h("3.2.8. Dashboard người học", HeadingLevel.HEADING_3),
            p("Dashboard hiển thị tổng quan tiến độ học tập: XP, streak, level, số từ đã học, số test đã hoàn thành. Có biểu đồ Chart.js hiển thị tiến độ theo thời gian, lịch sử hoạt động gần đây, streak bar và XP progress bar. Bảng xếp hạng hiển thị top 10 người học có điểm XP cao nhất."),
            ...screenshot(SS.dashboard, "Hình 3.13. Giao diện Dashboard người học"),

            h("3.2.9. Nâng cấp Pro", HeadingLevel.HEADING_3),
            p("Trang Membership hiển thị bảng so sánh Free vs Pro, các gói Pro (1/3/6 tháng) với giá và quyền lợi. Khi người dùng chọn gói, modal hiển thị QR code ngân hàng, thông tin tài khoản (ngân hàng, số TK, chủ TK), và ô nhập nội dung chuyển khoản. Người dùng chuyển khoản xong bấm xác nhận, hệ thống tạo đơn pending chờ admin duyệt."),
            ...screenshot(SS.membership, "Hình 3.14. Giao diện nâng cấp Pro"),

            new Paragraph({ children: [new PageBreak()] }),

            h("3.3. Giao diện quản trị (Admin)", HeadingLevel.HEADING_2),
            p("Khu quản trị có giao diện riêng với header tối màu, navigation bar sticky, và layout tối ưu cho việc quản lý dữ liệu."),

            h("3.3.1. Dashboard Admin", HeadingLevel.HEADING_3),
            p("Dashboard admin hiển thị các số liệu thống kê tổng quan: tổng số học viên, số Pro members, số chủ đề, bài test, câu hỏi, lượt làm bài, đơn chờ duyệt, tickets đang mở. Có các biểu đồ Chart.js: tăng trưởng người dùng 7 ngày, phân bố điểm test, đơn hàng theo tháng, tỷ lệ Free/Pro. Phần recent activity hiển thị người dùng mới nhất và lượt làm bài gần đây."),
            ...screenshot(SS.admin_dashboard, "Hình 3.15. Giao diện Dashboard Admin"),

            h("3.3.2. Quản lý đơn nâng cấp", HeadingLevel.HEADING_3),
            p("Trang quản lý đơn hiển thị danh sách tất cả đơn nâng cấp Pro với thông tin: username, tên gói, số tiền, nội dung chuyển khoản, trạng thái (pending/completed/cancelled). Admin có thể duyệt đơn (approve) hoặc từ chối (reject). Khi duyệt, hệ thống tự động tính ngày hết hạn (cộng dồn nếu đang là Pro) và cập nhật membership cho user."),
            ...screenshot(SS.admin_orders, "Hình 3.16. Giao diện quản lý đơn nâng cấp"),

            h("3.3.3. Quản lý Tickets", HeadingLevel.HEADING_3),
            p("Trang quản lý tickets hiển thị danh sách yêu cầu hỗ trợ từ người dùng, bao gồm loại ticket (general, cancel_order, bug_report, feedback), tiêu đề, nội dung, và trạng thái (open/in_progress/resolved/closed). Admin có thể phản hồi ticket, đổi trạng thái, và xử lý yêu cầu hủy đơn."),
            ...screenshot(SS.admin_tickets, "Hình 3.17. Giao diện quản lý Tickets"),

            new Paragraph({ children: [new PageBreak()] }),

            h("3.4. Xây dựng chức năng", HeadingLevel.HEADING_2),
            h("3.4.1. Core MVC Framework", HeadingLevel.HEADING_3),
            p("Router (App.php + Router.php): Phân tích URL, case-insensitive method matching (Reflection API), chặn 25 method nội bộ, admin restriction. Base Controller: model(), view(), viewPartial(), json(), redirect(), setFlash(), isMethod(), input(), query(). Base Model: CRUD qua PDO prepared statements. Middleware: requireLogin(), requireAdmin(), requireStudent(), requirePro(), guest()."),

            h("3.4.2. Chức năng Người học", HeadingLevel.HEADING_3),
            p("AuthController: Đăng ký (validate + password_hash) và đăng nhập (authenticate + password_verify + session_regenerate_id). Google OAuth 2.0 Authorization Code flow. Auto-downgrade Pro hết hạn. Rate limiting 5 lần/60s."),
            p("TopicController + Vocabulary Model: Danh sách chủ đề kèm thống kê, lọc level, tab navigation, flashcard, đánh dấu đã học (AJAX + XP), tìm kiếm toàn văn."),
            p("TestController: Quiz/Listening/Reading, giao diện từng câu + timer, tự động chấm điểm, lưu kết quả + câu trả lời."),
            p("SpeakingController + OpenAIService: Web Speech API → transcript → POST /speaking/score → OpenAI GPT-3.5 Turbo (hoặc local fallback) → lưu attempt + update progress + award XP."),
            p("MembershipController: QR + bank info → tạo đơn pending → admin duyệt → kích hoạt Pro."),
            p("SupportController: Tạo ticket, chính sách hủy đơn (24h hoàn 100%, 7 ngày hoàn 50%)."),

            h("3.4.3. Admin Panel", HeadingLevel.HEADING_3),
            p("Dashboard: thống kê + biểu đồ Chart.js. Quản lý Users: danh sách, tìm kiếm, sửa, xóa (cascade). Quản lý Topics/Questions: thêm/sửa topic, quản lý câu hỏi theo test (4 đáp án A/B/C/D). Duyệt đơn Pro: BEGIN TRAN → UPDATE order + user → COMMIT. Tickets: phản hồi, đổi trạng thái, xử lý hủy đơn."),

            h("3.5. Kiểm thử", HeadingLevel.HEADING_2),
            p("PHPUnit 9.6: 19 test cases (MembershipService, Request, Validator). PHPStan level 5: 0 errors. PHP-CS-Fixer PSR-12: toàn bộ codebase được format tự động. Pre-commit hook: syntax + code style + PHPStan + PHPUnit."),

            new Paragraph({ children: [new PageBreak()] }),

            // ═══ CHƯƠNG 4 ═══
            h("Chương 4. KẾT LUẬN VÀ KIẾN NGHỊ", HeadingLevel.HEADING_1),
            h("4.1. Kết quả đạt được", HeadingLevel.HEADING_2),
            bullet("Hệ thống MVC hoàn chỉnh: 15 controllers, 9 models, 34 views, 23 bảng database."),
            bullet("Đầy đủ chức năng Learner: đăng ký/đăng nhập, học từ vựng/bài học, test, speaking AI, chatbot, dashboard, QR Pro, ticket."),
            bullet("Admin panel 6 phân hệ: Dashboard, Users, Topics/Questions, Orders, Tickets, Settings."),
            bullet("Tích hợp OpenAI API, Google OAuth 2.0. Gamification: XP, streak, level, badge, bảng xếp hạng."),
            bullet("Bảo mật: Prepared Statements, CSRF, Rate Limiting, Session Security, Input Validation."),
            bullet("Chất lượng code: PHPStan level 5 (0 errors), PHPUnit 19 tests, PSR-12, Composer, Monolog."),

            h("4.2. Hạn chế", HeadingLevel.HEADING_2),
            bullet("Chưa có quên mật khẩu qua email. Web Speech API chỉ hoạt động tốt trên Chrome."),
            bullet("Chưa có real-time notification, upload ảnh/audio cho topic. Chưa hỗ trợ đa ngôn ngữ."),
            bullet("Chưa có CI/CD pipeline tự động."),

            h("4.3. Hướng phát triển", HeadingLevel.HEADING_2),
            bullet("Quên mật khẩu qua email, real-time notification, mobile app."),
            bullet("Đa ngôn ngữ (i18n), Docker containerization, CI/CD GitHub Actions."),
            bullet("Tăng test coverage 70%+, nâng cấp AI lên GPT-4."),
        ]
    }]
});

const outPath = 'D:/xampp/htdocs/DA_TTTN/docs/bao_cao_thuc_tap/BAO_CAO_FULL.docx';
Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(outPath, buffer);
    console.log('Generated: ' + outPath + ' (' + (buffer.length / 1024).toFixed(0) + ' KB)');
}).catch(err => { console.error('Error:', err.message); process.exit(1); });
