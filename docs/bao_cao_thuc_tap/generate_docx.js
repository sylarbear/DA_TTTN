const fs = require('fs');
const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
        Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
        ShadingType, PageNumber, PageBreak, TableOfContents, LevelFormat } = require('docx');

// ── Common styles ──
const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const cellMargins = { top: 60, bottom: 60, left: 100, right: 100 };

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

function tableRow(cells, isHeader = false) {
    return new TableRow({
        children: cells.map(c => new TableCell({
            borders,
            width: { size: 9360 / cells.length, type: WidthType.DXA },
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
            levels: [{ level: 0, format: LevelFormat.BULLET, text: "•", alignment: AlignmentType.LEFT,
              style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
        }]
    },
    sections: [
        // ── BÌA ──
        {
            properties: {
                page: {
                    size: { width: 11906, height: 16838 },
                    margin: { top: 720, right: 1440, bottom: 720, left: 1440 }
                }
            },
            children: [
                new Paragraph({ spacing: { before: 3000 }, children: [] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 100 },
                    children: [new TextRun({ text: "TRƯỜNG CAO ĐẲNG CÔNG THƯƠNG TPHCM", size: 28, bold: true, font: "Times New Roman" })] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 100 },
                    children: [new TextRun({ text: "KHOA CÔNG NGHỆ THÔNG TIN", size: 28, bold: true, font: "Times New Roman" })] }),
                new Paragraph({ spacing: { before: 600 }, children: [] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 100 },
                    children: [new TextRun({ text: "BÁO CÁO THỰC TẬP TỐT NGHIỆP", size: 40, bold: true, font: "Times New Roman" })] }),
                new Paragraph({ spacing: { before: 400 }, children: [] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 100 },
                    children: [new TextRun({ text: "Đề tài:", size: 26, font: "Times New Roman" })] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 100 },
                    children: [new TextRun({ text: "XÂY DỰNG WEBSITE HỌC TIẾNG ANH TRỰC TUYẾN ENGPATH", size: 30, bold: true, font: "Times New Roman" })] }),
                new Paragraph({ spacing: { before: 600 }, children: [] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 80 },
                    children: [new TextRun({ text: "GV hướng dẫn: Vũ Thị Hường", size: 26, font: "Times New Roman" })] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 80 },
                    children: [new TextRun({ text: "Sinh viên thực hiện: Phan Quang Thuật", size: 26, font: "Times New Roman" })] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 80 },
                    children: [new TextRun({ text: "MSSV: 2120110351 — Lớp: CDTIN21A", size: 26, font: "Times New Roman" })] }),
                new Paragraph({ spacing: { before: 800 }, children: [] }),
                new Paragraph({ alignment: AlignmentType.CENTER,
                    children: [new TextRun({ text: "TPHCM, tháng 6 năm 2025", size: 26, font: "Times New Roman" })] }),
            ]
        },

        // ── LỜI CẢM ƠN + MỤC LỤC ──
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
                heading("LỜI CẢM ƠN", HeadingLevel.HEADING_1),
                p("Trong suốt quá trình thực tập và thực hiện đồ án tốt nghiệp, em đã nhận được sự hướng dẫn, giúp đỡ và động viên từ nhiều phía."),
                p("Trước hết, em xin gửi lời cảm ơn chân thành đến cô Vũ Thị Hường — giảng viên khoa Công Nghệ Thông Tin, Trường Cao Đẳng Công Thương TPHCM — người đã trực tiếp hướng dẫn, định hướng đề tài và đóng góp nhiều ý kiến quý báu trong suốt quá trình em thực hiện đồ án."),
                p("Em cũng xin cảm ơn quý thầy cô trong khoa Công Nghệ Thông Tin đã trang bị cho em những kiến thức nền tảng về lập trình, cơ sở dữ liệu, thiết kế web và phân tích hệ thống."),
                p("Cuối cùng, em xin cảm ơn gia đình và bạn bè đã luôn ủng hộ, động viên em trong suốt thời gian học tập và thực hiện đề tài."),
                p("Mặc dù đã cố gắng hết sức, nhưng do thời gian và kinh nghiệm còn hạn chế, đồ án không tránh khỏi những thiếu sót. Em rất mong nhận được sự góp ý từ quý thầy cô."),

                new Paragraph({ children: [new PageBreak()] }),

                // ── MỤC LỤC ──
                heading("MỤC LỤC", HeadingLevel.HEADING_1),
                new TableOfContents("Mục Lục", { hyperlink: true, headingStyleRange: "1-3" }),

                new Paragraph({ children: [new PageBreak()] }),

                // ── ĐỀ CƯƠNG ──
                heading("ĐỀ CƯƠNG THỰC TẬP", HeadingLevel.HEADING_1),

                heading("1. Nơi thực tập", HeadingLevel.HEADING_3),
                p("Tự thực hiện đồ án tại nhà dưới sự hướng dẫn của giảng viên khoa Công Nghệ Thông Tin, Trường Cao Đẳng Công Thương TPHCM."),

                heading("2. Đề tài", HeadingLevel.HEADING_3),
                p("Xây dựng Website Học Tiếng Anh Trực Tuyến EngPath."),

                heading("3. Mục tiêu đề tài", HeadingLevel.HEADING_3),
                p("Đề tài thuộc lĩnh vực phát triển ứng dụng web, giải quyết bài toán học tiếng Anh trực tuyến cho người Việt. Website cung cấp một lộ trình học tập rõ ràng: từ học từ vựng theo chủ đề, đọc bài học, làm bài kiểm tra, luyện nói với AI, đến theo dõi tiến độ học tập qua dashboard."),
                p("Ý nghĩa thực tiễn:"),
                bullet("Giúp người học tiếng Anh có một nền tảng học tập có cấu trúc, dễ tiếp cận"),
                bullet("Tích hợp AI để chấm điểm phát âm, giúp người học luyện nói mọi lúc"),
                bullet("Tiết kiệm chi phí so với việc học tại trung tâm"),
                bullet("Xây dựng thói quen học tập thông qua hệ thống streak, XP và bảng xếp hạng"),

                heading("4. Công nghệ sử dụng", HeadingLevel.HEADING_3),
                new Table({
                    width: { size: 9026, type: WidthType.DXA },
                    columnWidths: [3000, 6026],
                    rows: [
                        tableRow(["Công nghệ", "Chi tiết"], true),
                        tableRow(["Ngôn ngữ chính", "PHP 8.0 (MVC pattern thuần, không framework)"]),
                        tableRow(["Cơ sở dữ liệu", "MySQL (PDO, prepared statements)"]),
                        tableRow(["Frontend", "HTML5, CSS3, JavaScript (Vanilla JS)"]),
                        tableRow(["Thư viện CSS", "Font Awesome 6.5, Google Fonts (Be Vietnam Pro, Inter)"]),
                        tableRow(["Biểu đồ", "Chart.js 4.4"]),
                        tableRow(["AI/API", "OpenAI API (GPT-3.5 Turbo cho Speaking + Chatbot)"]),
                        tableRow(["OAuth", "Google OAuth 2.0 (đăng nhập Google)"]),
                        tableRow(["Webhook", "Casso API (tự động xác nhận thanh toán ngân hàng)"]),
                        tableRow(["Quản lý gói", "Composer (PHP dependencies), PSR-4 autoload"]),
                        tableRow(["Testing", "PHPUnit 9.6, PHPStan level 5"]),
                        tableRow(["Code Style", "PHP-CS-Fixer (PSR-12)"]),
                    ]
                }),

                heading("5. Nội dung chính của thực tập", HeadingLevel.HEADING_3),
                bullet("Chương 1: Tổng quan về đề tài học tiếng Anh trực tuyến"),
                bullet("Chương 2: Cơ sở lý thuyết (MVC pattern, PHP, MySQL, PDO, RESTful API, AI Speaking)"),
                bullet("Chương 3: Phân tích, thiết kế hệ thống và xây dựng ứng dụng"),
                bullet("Chương 4: Kết luận, đánh giá kết quả và hướng phát triển"),

                heading("6. Tiến độ thực hiện", HeadingLevel.HEADING_3),
                new Table({
                    width: { size: 9026, type: WidthType.DXA },
                    columnWidths: [800, 2000, 3500, 2726],
                    rows: [
                        tableRow(["TT", "Thời gian", "Nội dung công việc", "Kết quả"], true),
                        tableRow(["1", "Tháng 3/2025", "Khảo sát, chọn đề tài, viết đề cương", "Đề cương chi tiết"]),
                        tableRow(["2", "Tháng 4/2025", "Phân tích thiết kế CSDL, xây dựng MVC core", "Database schema, App core"]),
                        tableRow(["3", "Tháng 5/2025", "Xây dựng chức năng learner (topics, lessons, tests)", "CRUD modules learner"]),
                        tableRow(["4", "Tháng 5/2025", "Tích hợp AI Speaking + Chatbot, Google OAuth", "OpenAI + OAuth"]),
                        tableRow(["5", "Tháng 6/2025", "Admin panel, Membership Pro, Wallet, Webhook", "Admin + Payment"]),
                        tableRow(["6", "Tháng 6/2025", "Kiểm thử, sửa lỗi, viết báo cáo", "Hoàn thiện đồ án"]),
                    ]
                }),

                new Paragraph({ children: [new PageBreak()] }),

                // ═══ CHƯƠNG 1 ═══
                heading("Chương 1. TỔNG QUAN VỀ ĐỀ TÀI", HeadingLevel.HEADING_1),

                heading("1.1. Giới thiệu đề tài", HeadingLevel.HEADING_2),
                p("EngPath là website học tiếng Anh trực tuyến được xây dựng nhằm cung cấp một môi trường học tập toàn diện cho người Việt. Website tổ chức nội dung theo lộ trình rõ ràng: chọn chủ đề → học từ vựng → đọc bài học → làm bài kiểm tra → luyện nói → theo dõi tiến độ."),
                p("Điểm khác biệt của EngPath so với các website học tiếng Anh khác:"),
                bullet("Lộ trình học tập có cấu trúc: Mỗi chủ đề (topic) là một đơn vị học tập hoàn chỉnh, bao gồm từ vựng, bài học, bài test và luyện nói"),
                bullet("Tích hợp AI Speaking: Sử dụng OpenAI API để chấm điểm phát âm, đưa ra phản hồi chi tiết bằng tiếng Việt"),
                bullet("Gamification: Hệ thống XP, streak, level, badge và bảng xếp hạng tạo động lực học tập"),
                bullet("Membership Pro: Mô hình freemium với gói Pro mở khóa tính năng nâng cao"),
                bullet("Admin Dashboard: Quản trị viên có thể quản lý toàn bộ hệ thống qua giao diện web"),

                heading("1.2. Mục tiêu đề tài", HeadingLevel.HEADING_2),
                bullet("Xây dựng website học tiếng Anh hoàn chỉnh với đầy đủ chức năng cho learner và admin"),
                bullet("Áp dụng mô hình MVC thuần để hiểu sâu kiến trúc web"),
                bullet("Tích hợp các API bên thứ ba (OpenAI, Google OAuth, Casso)"),
                bullet("Xây dựng giao diện hiện đại, thân thiện với người dùng"),
                bullet("Đảm bảo bảo mật cơ bản (prepared statements, session security, CSRF protection, rate limiting)"),

                heading("1.3. Đối tượng và phạm vi", HeadingLevel.HEADING_2),
                p("Đối tượng sử dụng: Người học tiếng Anh ở mọi trình độ (beginner, intermediate, advanced); Quản trị viên hệ thống."),
                p("Phạm vi: 6 chủ đề học tập với 70+ từ vựng, 18+ bài học, 12+ bài test. Hệ thống thành viên (free/pro), ví điện tử, nạp/rút tiền. Khu quản trị với 10+ chức năng quản lý."),

                new Paragraph({ children: [new PageBreak()] }),

                // ═══ CHƯƠNG 2 ═══
                heading("Chương 2. CƠ SỞ LÝ THUYẾT", HeadingLevel.HEADING_1),

                heading("2.1. Mô hình MVC (Model-View-Controller)", HeadingLevel.HEADING_2),
                p("EngPath được xây dựng theo mô hình MVC thuần (không sử dụng framework):"),
                bullet("Model (app/models/): Chịu trách nhiệm thao tác với cơ sở dữ liệu. Base Model cung cấp các phương thức CRUD chung. Mỗi model kế thừa từ Base Model, tương ứng với một bảng trong database."),
                bullet("View (app/views/): Chịu trách nhiệm hiển thị giao diện. Sử dụng PHP template thuần, tổ chức theo thư mục controller/action. Layout được tách riêng trong views/layouts/."),
                bullet("Controller (app/controllers/): Điều phối giữa Model và View. Nhận request từ người dùng, gọi Model để lấy dữ liệu, truyền dữ liệu vào View để render."),
                p("Luồng xử lý request: URL request → .htaccess → public/index.php → App (Router) → Controller → Model → Database → View → Response"),

                heading("2.2. PHP và PDO", HeadingLevel.HEADING_2),
                p("Ngôn ngữ PHP 8.0 được chọn vì hỗ trợ lập trình hướng đối tượng đầy đủ, PDO cung cấp API thống nhất để làm việc với MySQL, Prepared Statements chống SQL Injection, cộng đồng lớn và tài liệu phong phú."),
                p("PDO được cấu hình với: PDO::ERRMODE_EXCEPTION, PDO::FETCH_ASSOC, và native prepared statements (PDO::ATTR_EMULATE_PREPARES = false)."),

                heading("2.3. Cơ sở dữ liệu MySQL", HeadingLevel.HEADING_2),
                p("Database english_master gồm 20+ bảng, thiết kế theo chuẩn 3NF:"),
                new Table({
                    width: { size: 9026, type: WidthType.DXA },
                    columnWidths: [3000, 3500, 2526],
                    rows: [
                        tableRow(["Bảng", "Mô tả", "Số trường"], true),
                        tableRow(["users", "Người dùng (student/admin)", "18"]),
                        tableRow(["topics", "Chủ đề học tập", "8"]),
                        tableRow(["vocabularies", "Từ vựng theo chủ đề", "8"]),
                        tableRow(["lessons", "Bài học", "8"]),
                        tableRow(["tests", "Bài kiểm tra", "8"]),
                        tableRow(["questions", "Câu hỏi kiểm tra", "8"]),
                        tableRow(["test_results", "Kết quả làm bài", "8"]),
                        tableRow(["user_answers", "Câu trả lời của user", "7"]),
                        tableRow(["speaking_prompts", "Câu hỏi luyện nói", "7"]),
                        tableRow(["speaking_attempts", "Lượt luyện nói", "9"]),
                        tableRow(["grammar_lessons", "Bài học ngữ pháp", "7"]),
                        tableRow(["membership_plans", "Gói hội viên", "5"]),
                        tableRow(["membership_orders", "Đơn nâng cấp Pro", "11"]),
                        tableRow(["activation_codes", "Mã kích hoạt", "8"]),
                        tableRow(["wallet_transactions", "Giao dịch ví", "9"]),
                        tableRow(["support_tickets", "Ticket hỗ trợ", "8"]),
                        tableRow(["user_progress", "Tiến độ học tập", "7"]),
                        tableRow(["bookmarks", "Từ vựng đã lưu", "4"]),
                        tableRow(["xp_history", "Lịch sử XP", "6"]),
                    ]
                }),

                heading("2.4. OpenAI API — AI Speaking & Chatbot", HeadingLevel.HEADING_2),
                p("Tích hợp OpenAI GPT-3.5 Turbo cho 2 tính năng: AI Speaking Evaluation (gửi transcript từ Web Speech API lên OpenAI, nhận điểm accuracy, fluency, pronunciation và feedback tiếng Việt) và AI Chatbot (trợ lý học tiếng Anh, trả lời câu hỏi về ngữ pháp, từ vựng, dịch thuật)."),

                heading("2.5. RESTful API Design", HeadingLevel.HEADING_2),
                p("Các endpoint AJAX trong hệ thống tuân theo thiết kế RESTful: HTTP method đúng ngữ nghĩa, JSON response với cấu trúc thống nhất {success: bool, data/error: ...}, HTTP status code phù hợp (200, 400, 404, 405, 422, 429, 500)."),

                heading("2.6. Bảo mật Web", HeadingLevel.HEADING_2),
                bullet("Prepared Statements cho tất cả truy vấn SQL"),
                bullet("Session Security: HttpOnly cookie, SameSite=Lax, Secure flag (HTTPS), session_regenerate_id sau login"),
                bullet("CSRF Protection: Token cho form POST"),
                bullet("Rate Limiting: Giới hạn 5 lần đăng nhập/60 giây"),
                bullet("Input Validation: Validate dữ liệu đầu vào tập trung qua Validator class"),
                bullet("Path Traversal Protection: Router chỉ cho phép ký tự a-zA-Z trong controller name"),
                bullet("Method Blocking: Các method nội bộ bị chặn không cho gọi từ URL"),
                bullet("Environment Separation: APP_ENV kiểm soát hiển thị lỗi"),
                bullet("SSL Verification: CURLOPT_SSL_VERIFYPEER = true khi gọi API bên ngoài"),

                new Paragraph({ children: [new PageBreak()] }),

                // ═══ CHƯƠNG 3 ═══
                heading("Chương 3. NỘI DUNG THỰC TẬP", HeadingLevel.HEADING_1),

                heading("3.1. Phân tích hệ thống", HeadingLevel.HEADING_2),

                heading("3.1.1. Yêu cầu chức năng", HeadingLevel.HEADING_3),
                p("Actor: Learner (Người học)", { run: { bold: true } }),
                bullet("Đăng ký / Đăng nhập (email+password hoặc Google OAuth)"),
                bullet("Xem danh sách chủ đề học tập, lọc theo level"),
                bullet("Xem chi tiết chủ đề: từ vựng, bài học, bài test, luyện nói"),
                bullet("Học từ vựng với flashcard, đánh dấu đã học"),
                bullet("Đọc bài học theo chủ đề"),
                bullet("Làm bài test (Quiz, Listening, Reading), xem kết quả"),
                bullet("Luyện nói với AI, nhận chấm điểm và phản hồi"),
                bullet("Chat với AI Assistant về tiếng Anh"),
                bullet("Xem dashboard: XP, streak, level, tiến độ"),
                bullet("Xem bảng xếp hạng, quản lý hồ sơ cá nhân"),
                bullet("Nạp/rút tiền vào ví điện tử, mua gói Pro"),
                bullet("Kích hoạt mã Pro, gửi ticket hỗ trợ"),

                p("Actor: Admin (Quản trị viên)", { run: { bold: true } }),
                bullet("Dashboard thống kê (users, topics, tests, orders, tickets)"),
                bullet("Quản lý users (xem, sửa, xóa)"),
                bullet("Quản lý topics, câu hỏi test, mã kích hoạt"),
                bullet("Duyệt/từ chối đơn nâng cấp Pro, giao dịch ví"),
                bullet("Phản hồi ticket hỗ trợ"),
                bullet("Cấu hình OpenAI API key"),

                heading("3.2. Thiết kế hệ thống", HeadingLevel.HEADING_2),

                heading("3.2.1. Kiến trúc tổng thể", HeadingLevel.HEADING_3),
                p("Hệ thống được tổ chức theo mô hình MVC với Front Controller pattern:"),
                p("public/index.php (Front Controller) → app/core/App.php (Dispatcher) → app/core/Router.php (URL Routing) → Controllers → Models/Views"),
                p("Cấu trúc thư mục chính: app/ (config, core, controllers, models, Services, Helpers, views), public/ (index.php, css, js, images), database/ (schema.sql, migrations), tests/ (Unit, Feature), vendor/ (Composer dependencies), storage/logs/."),

                heading("3.2.2. Thiết kế cơ sở dữ liệu", HeadingLevel.HEADING_3),
                p("Database gồm 19 bảng chính, tổ chức xoay quanh 3 thực thể trung tâm:"),
                bullet("users: trung tâm của mọi quan hệ — liên kết với test_results, speaking_attempts, user_progress, membership_orders, wallet_transactions, support_tickets, bookmarks, xp_history"),
                bullet("topics: đơn vị học tập — liên kết với vocabularies, lessons, tests, speaking_prompts, user_progress"),
                bullet("tests: bài kiểm tra — liên kết với questions, test_results, user_answers"),

                heading("3.3. Xây dựng ứng dụng", HeadingLevel.HEADING_2),

                heading("3.3.1. Xây dựng Core MVC", HeadingLevel.HEADING_3),
                p("Router (App.php + Router.php): Phân tích URL dạng /controller/method/param1/param2. Case-insensitive method matching sử dụng Reflection API. Chặn các method nội bộ. Admin restriction."),
                p("Base Controller: model(), view(), viewPartial(), json(), redirect(), setFlash(), isMethod(), input(), query()."),
                p("Base Model: CRUD đầy đủ — all(), find(), findBy(), where(), create(), update(), delete(), count(), raw()."),
                p("Middleware: requireLogin(), requireAdmin(), requireStudent(), requirePro(), guest(), user(), isLoggedIn(), isAdmin(), isPro()."),

                heading("3.3.2. Xây dựng chức năng Learner", HeadingLevel.HEADING_3),
                p("Đăng ký & Đăng nhập (AuthController): validate input, password_hash + password_verify, Google OAuth 2.0, session security, auto-downgrade membership hết hạn."),
                p("Học từ vựng (TopicController + Vocabulary): Danh sách chủ đề kèm thống kê, chi tiết chủ đề với tab navigation, flashcard, đánh dấu đã học (AJAX + XP), tìm kiếm toàn văn."),
                p("Làm bài test (TestController): 3 loại test (Quiz, Listening, Reading), giao diện làm bài với timer, tự động chấm điểm, lưu kết quả + câu trả lời."),
                p("Luyện nói AI (SpeakingController): Danh sách prompt theo chủ đề, Web Speech API nhận diện giọng nói, OpenAI chấm điểm, fallback scoring, lịch sử luyện nói."),
                p("Chatbot (ChatbotController): Chat widget với OpenAI GPT-3.5 Turbo, system prompt 'English Learning AI Assistant', lịch sử 6 tin nhắn gần nhất."),
                p("Dashboard (DashboardController): XP, streak, level, biểu đồ Chart.js, lịch sử hoạt động."),
                p("Membership Pro (MembershipController): Hiển thị gói (1/3/6 tháng), thanh toán ví, kích hoạt mã, chuyển khoản ngân hàng với QR code."),
                p("Ví điện tử (WalletController): Số dư, lịch sử giao dịch, nạp/rút tiền (pending → admin duyệt)."),
                p("Hỗ trợ (SupportController): Tạo ticket (general, cancel_order, bug_report, feedback), chính sách hủy đơn (24h hoàn 100%, 7 ngày hoàn 50%)."),

                heading("3.3.3. Xây dựng Admin Panel", HeadingLevel.HEADING_3),
                p("Admin Dashboard: Thống kê tổng quan (users, Pro users, topics, tests, questions, attempts), biểu đồ (tăng trưởng user, phân bố điểm, đơn hàng theo tháng, tỷ lệ Free/Pro)."),
                p("Quản lý: Users (danh sách, tìm kiếm, sửa, xóa + cascade), Topics (thêm/sửa), Questions (chọn test → thêm/sửa/xóa với 4 đáp án), Activation Codes (tạo format A-Z0-9-, chống trùng), Orders (duyệt → tính hạn + cập nhật Pro, từ chối), Tickets (phản hồi, đổi trạng thái, duyệt hủy đơn + hoàn tiền), Wallet (duyệt/từ chối nạp/rút, FOR UPDATE lock), Cấu hình (OpenAI API key)."),

                heading("3.3.4. Tích hợp Webhook Ngân hàng", HeadingLevel.HEADING_3),
                p("WebhookController nhận callback từ Casso: verify secret, parse nội dung CK (format EMPRO {userId} GOI{planId}), validate plan + số tiền + user, tự động kích hoạt Pro, ghi log."),

                heading("3.4. Giao diện người dùng", HeadingLevel.HEADING_2),
                p("Giao diện EngPath được thiết kế theo phong cách hiện đại, lấy cảm hứng từ Busuu/Duolingo. Trang chủ có hero section, phone mockup, stats, goal panel, feature cards, course grid. Admin panel có dark header, sticky navigation, stats grid, charts, data tables, modal forms."),

                heading("3.5. Kiểm thử", HeadingLevel.HEADING_2),
                bullet("PHPUnit: 19 test cases cho MembershipService, Request, Validator — business logic, validation rules, HTTP helpers"),
                bullet("PHPStan level 5: 0 errors, 90 PHP files phân tích"),
                bullet("PHP-CS-Fixer PSR-12: Toàn bộ codebase được format tự động"),
                bullet("Pre-commit hook: kiểm tra syntax + code style + PHPStan + PHPUnit"),

                new Paragraph({ children: [new PageBreak()] }),

                // ═══ CHƯƠNG 4 ═══
                heading("Chương 4. KẾT LUẬN VÀ KIẾN NGHỊ", HeadingLevel.HEADING_1),

                heading("4.1. Kết quả đạt được", HeadingLevel.HEADING_2),
                p("Tính năng đã hoàn thành:"),
                bullet("Hệ thống MVC hoàn chỉnh với 16 controllers, 9 models, 36 views"),
                bullet("Đầy đủ chức năng learner: đăng ký/đăng nhập (email + Google OAuth), học từ vựng, đọc bài học, làm test, luyện nói AI, chatbot, dashboard, bảng xếp hạng, quản lý hồ sơ"),
                bullet("Hệ thống membership: gói Free/Pro, thanh toán qua ví, chuyển khoản ngân hàng, mã kích hoạt"),
                bullet("Ví điện tử: nạp/rút tiền, lịch sử giao dịch"),
                bullet("Hệ thống ticket hỗ trợ + chính sách hủy đơn"),
                bullet("Admin panel với 9 phân hệ quản lý"),
                bullet("Webhook tự động xác nhận thanh toán từ ngân hàng"),
                bullet("Tích hợp OpenAI API cho Speaking + Chatbot"),
                bullet("Gamification: XP, streak, level, badge, bảng xếp hạng"),

                p("Chất lượng code:"),
                bullet("90 PHP files, ~5,000 dòng code PHP"),
                bullet("PHPStan level 5: 0 errors"),
                bullet("PHPUnit: 19 tests / 24 assertions"),
                bullet("PSR-12 code style, Composer autoload (classmap + PSR-4)"),
                bullet("Monolog logging, CSRF protection, Rate limiting"),

                heading("4.2. Hạn chế", HeadingLevel.HEADING_2),
                bullet("Chưa có hệ thống quên mật khẩu / reset password qua email"),
                bullet("Web Speech API chỉ hoạt động tốt trên Chrome"),
                bullet("Chưa có real-time notification, upload ảnh/audio cho topic"),
                bullet("Chưa hỗ trợ đa ngôn ngữ (chỉ tiếng Việt)"),
                bullet("Chưa có bộ test đầy đủ cho tất cả controller"),
                bullet("Chưa có CI/CD pipeline tự động"),

                heading("4.3. Hướng phát triển", HeadingLevel.HEADING_2),
                bullet("Quên mật khẩu: Gửi email reset password"),
                bullet("Real-time Notification: Thông báo khi admin duyệt đơn, phản hồi ticket"),
                bullet("Mobile App: Phát triển ứng dụng di động sử dụng API"),
                bullet("Đa ngôn ngữ: Hỗ trợ giao diện tiếng Anh"),
                bullet("Test Coverage: Tăng coverage lên 70%+"),
                bullet("CI/CD: GitHub Actions cho test tự động"),
                bullet("Docker: Containerize ứng dụng để dễ deploy"),
                bullet("Nâng cấp AI: Sử dụng GPT-4 hoặc model mới hơn"),
                bullet("Hỗ trợ nhiều ngân hàng: Mở rộng webhook"),

                new Paragraph({ children: [new PageBreak()] }),

                // ── TÀI LIỆU THAM KHẢO ──
                heading("TÀI LIỆU THAM KHẢO", HeadingLevel.HEADING_1),
                p("[1] PHP Manual — https://www.php.net/manual/"),
                p("[2] PDO Documentation — https://www.php.net/manual/en/book.pdo.php"),
                p("[3] MySQL Reference Manual — https://dev.mysql.com/doc/refman/8.0/"),
                p("[4] OpenAI API Documentation — https://platform.openai.com/docs/"),
                p("[5] Google OAuth 2.0 — https://developers.google.com/identity/protocols/oauth2"),
                p("[6] Chart.js Documentation — https://www.chartjs.org/docs/"),
                p("[7] MDN Web Docs — https://developer.mozilla.org/"),
                p("[8] PHPUnit Manual — https://docs.phpunit.de/"),
                p("[9] PHPStan Documentation — https://phpstan.org/"),
                p("[10] PHP-FIG PSR-12 — https://www.php-fig.org/psr/psr-12/"),
                p("[11] Monolog Documentation — https://github.com/Seldaek/monolog"),
                p("[12] Font Awesome Icons — https://fontawesome.com/"),
                p("[13] Web Speech API — https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API"),
                p("[14] Casso API — https://casso.vn/developers"),
            ]
        }
    ]
});

// ── Generate ──
const outPath = 'D:/xampp/htdocs/DA_TTTN/docs/bao_cao_thuc_tap/BAO_CAO_ENGPATH.docx';
Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(outPath, buffer);
    console.log('✅ Generated: ' + outPath);
}).catch(err => {
    console.error('Error:', err.message);
    process.exit(1);
});
