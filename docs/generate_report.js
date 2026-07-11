const fs = require('fs');
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
  ShadingType, PageBreak, LevelFormat, TableOfContents, PageNumber, ImageRun
} = require('docx');

const OUT = 'D:/xampp/htdocs/DA_TTTN/docs/bao-cao-thuc-tap-MOI.docx';
const D = 'D:/xampp/htdocs/DA_TTTN/docs/diagrams/';
const F = 'Times New Roman';
const S = 26; // 13pt

// ── Shortcuts ──
const bdr = { style: BorderStyle.SINGLE, size: 1, color: 'AAAAAA' };
const bdrs = { top: bdr, bottom: bdr, left: bdr, right: bdr };
const cm = { top: 60, bottom: 60, left: 100, right: 100 };
const hdr = new Header({ children: [new Paragraph({ alignment: AlignmentType.RIGHT, border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: '4F46E5', space: 4 } }, children: [new TextRun({ text: 'Báo cáo Thực tập Tốt nghiệp  •  EngPath', font: F, size: 20, italics: true, color: '94A3B8' })] })] });
const ftr = new Footer({ children: [new Paragraph({ alignment: AlignmentType.CENTER, border: { top: { style: BorderStyle.SINGLE, size: 2, color: 'CBD5E1', space: 4 } }, children: [new TextRun({ text: 'Trang ', font: F, size: 20, color: '94A3B8' }), new TextRun({ children: [PageNumber.CURRENT], font: F, size: 20, color: '94A3B8' })] })] });
const page = { size: { width: 11906, height: 16838 }, margin: { top: 1440, right: 1200, bottom: 1440, left: 1440 } };
const sec = (kids) => ({ properties: { page }, headers: { default: hdr }, footers: { default: ftr }, children: kids });

function H1(t) { return new Paragraph({ heading: HeadingLevel.HEADING_1, spacing: { before: 360, after: 200 }, children: [new TextRun({ text: t, font: F, size: 32, bold: true })] }); }
function H2(t) { return new Paragraph({ heading: HeadingLevel.HEADING_2, spacing: { before: 280, after: 160 }, children: [new TextRun({ text: t, font: F, size: 28, bold: true })] }); }
function H3(t) { return new Paragraph({ heading: HeadingLevel.HEADING_3, spacing: { before: 200, after: 120 }, children: [new TextRun({ text: t, font: F, size: S, bold: true })] }); }
function P(t, o = {}) { return new Paragraph({ spacing: { after: 120, line: 360 }, alignment: o.c ? AlignmentType.CENTER : o.a || AlignmentType.JUSTIFIED, children: [new TextRun({ text: t, font: F, size: S, ...o })] }); }
function Pm(rs) { return new Paragraph({ spacing: { after: 120, line: 360 }, alignment: AlignmentType.JUSTIFIED, children: rs.map(r => new TextRun({ font: F, size: S, ...r })) }); }
function B(t) { return new Paragraph({ numbering: { reference: 'b', level: 0 }, spacing: { after: 80, line: 340 }, children: [new TextRun({ text: t, font: F, size: S })] }); }
function Em() { return new Paragraph({ spacing: { after: 0 }, children: [] }); }
function Br() { return new Paragraph({ children: [new PageBreak()] }); }

function imgCell(file, label, w, h) {
  if (!fs.existsSync(file)) return new TableCell({ borders: bdrs, width: { size: 4680, type: WidthType.DXA }, margins: cm, children: [P('[Ảnh: ' + label + ']', { italics: true, color: 'AAAAAA', size: 22 })] });
  const data = fs.readFileSync(file);
  return new TableCell({
    borders: bdrs, width: { size: 4680, type: WidthType.DXA }, margins: cm, verticalAlign: 'center',
    children: [
      new Paragraph({ alignment: AlignmentType.CENTER, children: [new ImageRun({ type: 'png', data, transformation: { width: w || 430, height: h || 290 }, altText: { title: label, description: label, name: label } })] }),
      new Paragraph({ alignment: AlignmentType.CENTER, spacing: { before: 80 }, children: [new TextRun({ text: label, font: F, size: 20, italics: true, color: '475569' })] })
    ]
  });
}

function fullFig(file, label, w, h) {
  if (!fs.existsSync(file)) return P('[Hình: ' + label + ']', { italics: true, color: 'AAAAAA', size: 22, c: true });
  const data = fs.readFileSync(file);
  return [
    new Paragraph({ alignment: AlignmentType.CENTER, spacing: { before: 200, after: 0 }, children: [new ImageRun({ type: 'png', data, transformation: { width: w || 480, height: h || 320 }, altText: { title: label, description: label, name: label } })] }),
    new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 200 }, children: [new TextRun({ text: label, font: F, size: 20, italics: true, color: '475569' })] })
  ];
}

function tbl(headers, rows) {
  const cw = Math.floor(9026 / headers.length);
  return new Table({
    width: { size: 9026, type: WidthType.DXA }, columnWidths: headers.map(() => cw),
    rows: [
      new TableRow({ children: headers.map(h => new TableCell({ borders: bdrs, width: { size: cw, type: WidthType.DXA }, margins: cm, shading: { fill: 'EEF2FF', type: ShadingType.CLEAR }, children: [new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ text: h, font: F, size: 22, bold: true })] })] })) }),
      ...rows.map(row => new TableRow({ children: row.map(cell => new TableCell({ borders: bdrs, width: { size: cw, type: WidthType.DXA }, margins: cm, children: [new Paragraph({ children: [new TextRun({ text: String(cell), font: F, size: 22 })] })] })) }))
    ]
  });
}

// ═══════════════════════════════════════════════════════════
const doc = new Document({
  numbering: { config: [{ reference: 'b', levels: [{ level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT, style: { paragraph: { indent: { left: 720, hanging: 360 } } } }] }] },
  styles: {
    default: { document: { run: { font: F, size: S } } },
    paragraphStyles: [
      { id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true, run: { size: 32, bold: true, font: F }, paragraph: { spacing: { before: 360, after: 200 }, outlineLevel: 0 } },
      { id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true, run: { size: 28, bold: true, font: F }, paragraph: { spacing: { before: 280, after: 160 }, outlineLevel: 1 } },
      { id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true, run: { size: 26, bold: true, font: F }, paragraph: { spacing: { before: 200, after: 120 }, outlineLevel: 2 } },
    ]
  },
  sections: [
    // ═══ COVER ═══
    sec([
      Em(), Em(), Em(), Em(), Em(), Em(),
      P('TRƯỜNG CAO ĐẲNG CÔNG THƯƠNG TPHCM', { c: true, bold: true, size: 28 }), P('KHOA CÔNG NGHỆ THÔNG TIN', { c: true, bold: true, size: 28 }), Em(), Em(), Em(),
      P('BÁO CÁO THỰC TẬP', { c: true, bold: true, size: 36 }), P('TỐT NGHIỆP', { c: true, bold: true, size: 36 }), Em(), Em(),
      P('ĐỀ TÀI: XÂY DỰNG WEBSITE HỌC TIẾNG ANH TRỰC TUYẾN', { c: true, bold: true, size: 30 }),
      P('ENGPATH', { c: true, bold: true, size: 32, color: '4F46E5' }),
      Em(), Em(), Em(), Em(),
      Pm([{ text: 'GV hướng dẫn: ', bold: true }, { text: 'Vũ Thị Hường' }]), Em(),
      Pm([{ text: 'Sinh viên thực hiện: ', bold: true }, { text: 'Phan Quang Thuật' }]), Em(),
      Pm([{ text: 'MSSV: ', bold: true }, { text: '2120110351' }]), Em(),
      Pm([{ text: 'Lớp: ', bold: true }, { text: 'CCQ2011E  —  Khoá: K44' }]),
      Em(), Em(), Em(), Em(), Em(), Em(),
      P('TPHCM, tháng 7 năm 2026', { c: true, bold: true }),
    ]),

    // ═══ LỜI CẢM ƠN ═══
    sec([
      H1('LỜI CẢM ƠN'), Em(),
      P('Trong quá trình thực hiện đề tài "Xây dựng website học tiếng Anh trực tuyến EngPath", em đã nhận được rất nhiều sự giúp đỡ và hướng dẫn tận tình từ quý Thầy Cô, gia đình và bạn bè.'), Em(),
      P('Trước hết, em xin gửi lời cảm ơn chân thành và sâu sắc nhất đến cô Vũ Thị Hường – Giảng viên hướng dẫn, người đã tận tình chỉ bảo, định hướng và đóng góp những ý kiến quý báu trong suốt quá trình em thực hiện đề tài. Sự hướng dẫn của cô đã giúp em hoàn thiện sản phẩm một cách tốt nhất.'), Em(),
      P('Em xin gửi lời cảm ơn đến quý Thầy Cô trong Khoa Công nghệ Thông tin – Trường Cao đẳng Công Thương TPHCM đã truyền đạt cho em những kiến thức nền tảng vững chắc trong suốt thời gian học tập tại trường, tạo điều kiện cho em có thể áp dụng vào thực tế để xây dựng một sản phẩm hoàn chỉnh.'), Em(),
      P('Cuối cùng, em xin cảm ơn gia đình và bạn bè đã luôn động viên, hỗ trợ em trong suốt quá trình học tập và thực hiện đề tài.'), Em(),
      P('Trong quá trình thực hiện, do kiến thức và kinh nghiệm còn hạn chế nên đề tài không tránh khỏi những thiếu sót. Em rất mong nhận được sự góp ý của quý Thầy Cô để em có thể hoàn thiện hơn.'), Em(), Em(), Em(),
      P('TPHCM, tháng 7 năm 2026', { a: AlignmentType.RIGHT, bold: true }),
      P('Sinh viên thực hiện', { a: AlignmentType.RIGHT }), P('Phan Quang Thuật', { a: AlignmentType.RIGHT, bold: true }),
    ]),

    // ═══ ĐỀ CƯƠNG + TOC ═══
    sec([
      H1('ĐỀ CƯƠNG ÔN TẬP'), Em(),
      Pm([{ text: '1. Nơi thực tập:', bold: true }]),
      B('Tên đơn vị: Trường Cao Đẳng Công Thương TPHCM'),
      B('Địa chỉ: Số 20 Tăng Nhơn Phú, Phường Phước Long B, TP. Thủ Đức, TPHCM'),
      B('Điện thoại: 091 553 3130'), Em(),
      Pm([{ text: '2. Đề tài:', bold: true }]), P('Xây dựng website học tiếng Anh trực tuyến EngPath'), Em(),
      Pm([{ text: '3. Mục tiêu:', bold: true }]),
      B('Xây dựng nền tảng học tiếng Anh theo lộ trình CEFR (A1–C1) với 15 khóa học, bài kiểm tra đầu vào thích ứng (adaptive placement test), quiz tương tác và chứng chỉ hoàn thành.'),
      B('Ứng dụng kiến thức về PHP MVC thuần, MySQL, HTML5/CSS3/JavaScript, tích hợp OpenAI GPT cho chatbot AI, Google OAuth 2.0 cho đăng nhập, Chart.js cho thống kê.'),
      B('Xây dựng giao diện chuyên nghiệp, thân thiện với Design System 3-layer tokens (Professional Premium UI).'),
      B('Xây dựng hệ thống membership (gói Pro, thanh toán QR) và quản trị (admin panel).'), Em(),
      Pm([{ text: '4. Nội dung báo cáo:', bold: true }]),
      P('Chương 1: Tổng quan về đơn vị thực tập'), P('Chương 2: Lý thuyết áp dụng'), P('Chương 3: Nội dung thực tập'), P('Chương 4: Kết luận và kiến nghị'), P('Chương 5: Tài liệu tham khảo'),
      Br(),
      H1('MỤC LỤC'),
      new TableOfContents('Mục lục', { hyperlink: true, headingStyleRange: '1-3' }),
    ]),

    // ═══ CHƯƠNG 1 ═══
    sec([
      H1('Chương 1. TỔNG QUAN VỀ ĐƠN VỊ THỰC TẬP'),
      H2('1.1. Giới thiệu về Trường Cao đẳng Công Thương TPHCM'),
      P('Trường Cao đẳng Công Thương TPHCM (HITC) là cơ sở giáo dục nghề nghiệp công lập trực thuộc Bộ Công Thương, thành lập năm 1976, có hơn 45 năm kinh nghiệm đào tạo. Trường tọa lạc tại số 20 Tăng Nhơn Phú, Phường Phước Long B, TP. Thủ Đức. Trường đào tạo đa ngành: Công nghệ Thông tin, Điện – Điện tử, Cơ khí, Thực phẩm, Kế toán, Quản trị Kinh doanh... với hệ thống phòng lab và xưởng thực hành hiện đại.'),
      H2('1.2. Khoa Công nghệ Thông tin'),
      P('Khoa CNTT là khoa trọng điểm, đào tạo các chuyên ngành: CNTT (phần mềm, mạng), Tin học ứng dụng. Sinh viên được học từ cơ bản đến chuyên sâu: lập trình, CSDL, mạng, an toàn thông tin, AI, phát triển web và mobile. Đội ngũ giảng viên có trình độ cao, nhiều kinh nghiệm thực tế. Khoa thường xuyên tổ chức hội thảo, workshop, cuộc thi lập trình, kỳ thi kỹ năng nghề.'),
      H2('1.3. Sự phù hợp của đề tài'),
      P('Đề tài "Xây dựng website học tiếng Anh trực tuyến EngPath" ứng dụng tổng hợp kiến thức CNTT: lập trình web (PHP, MySQL, HTML/CSS/JS), mô hình MVC, thiết kế CSDL, tích hợp API (Google OAuth, OpenAI GPT), thiết kế UI/UX, và triển khai hệ thống thực tế. Đề tài có giá trị ứng dụng cao trong giáo dục, phù hợp với định hướng đào tạo của Khoa.'),
    ]),

    // ═══ CHƯƠNG 2 ═══
    sec([
      H1('Chương 2. LÝ THUYẾT ÁP DỤNG'),
      H2('2.1. Tổng quan đề tài'),
      P('EngPath là nền tảng học tiếng Anh online, xây dựng theo kiến trúc MVC với PHP thuần và MySQL. Hệ thống cung cấp lộ trình học cá nhân hóa theo khung CEFR (A1–C1), gồm 15 khóa học với hàng trăm bài học, quiz và bài thi cuối khóa. Hệ thống tích hợp bài kiểm tra đầu vào thích ứng (adaptive placement test) sử dụng mô hình IRT (Item Response Theory) để xác định trình độ người học, từ đó gợi ý lộ trình phù hợp.'), Em(),
      tbl(['Thành phần', 'Công nghệ', 'Vai trò'], [
        ['Backend', 'PHP 8.x, MVC thuần', 'Điều hướng, xử lý nghiệp vụ, gọi model, render view'],
        ['Database', 'MySQL + PDO, utf8mb4', '25+ bảng: users, courses, progress, tests, membership...'],
        ['Frontend', 'HTML5, CSS3, JS, AJAX', 'Giao diện responsive, tương tác form, gọi API nội bộ'],
        ['Auth', 'Session + password_hash, Google OAuth 2.0', 'Đăng nhập/đăng ký, phân quyền admin/student/pro'],
        ['AI', 'OpenAI GPT-3.5 Turbo', 'Chatbot hỗ trợ học tập, trả lời câu hỏi tiếng Anh'],
        ['Charts', 'Chart.js 4.4', 'Biểu đồ dashboard: điểm số, phân bố kỹ năng'],
        ['Icons', 'Font Awesome 6.7', 'Icon đồ họa cho giao diện'],
        ['Design', 'CSS Custom Properties (3-layer)', 'Design System: primitive → semantic → component tokens'],
      ]),
      Em(),
      H2('2.2. Kiến trúc tổng quan'),
      P('Hệ thống được tổ chức theo kiến trúc MVC 3 lớp:'),
      B('Core Layer (app/core/): App.php (dispatcher), Router.php (URL → Controller), Middleware.php (auth guards), Controller.php (base: view/json/redirect), Model.php (CRUD base), CSRF, RateLimiter, StreakService.'),
      B('Controller Layer (12 controllers): Auth, Home, Course, Dashboard, Profile, Admin, Membership, Placement, Test, Leaderboard, Support, Chatbot — mỗi controller kế thừa Controller base, xử lý request và trả về view hoặc JSON.'),
      B('Model Layer (11 models): User, Topic, Vocabulary, Lesson, Test, Question, UserAnswer, UserProgress, Course, CourseProgress, Placement — mỗi model kế thừa Model base, đóng gói truy vấn PDO.'),
      B('View Layer (20+ templates): layouts/ (header, footer), home, auth, course/ (index, show, learn, certificate), dashboard, profile, membership, placement/ (intro, take, result), tests, leaderboard, support, admin/.'),
      Em(),
      ...fullFig(D + 'architecture.drawio.png', 'Hình 2.1. Kiến trúc tổng quan hệ thống EngPath v3', 480, 380),
      Em(),
      H2('2.3. Cơ sở lý thuyết'),
      H3('2.3.1. Ngôn ngữ PHP và mô hình MVC'),
      P('PHP (PHP: Hypertext Preprocessor) là ngôn ngữ kịch bản phía server mã nguồn mở, phổ biến trong phát triển web. Hệ thống sử dụng PHP 8.x với mô hình MVC (Model-View-Controller): Model quản lý dữ liệu và logic nghiệp vụ, View hiển thị giao diện, Controller điều hướng request. Kiến trúc này giúp tách biệt các thành phần, dễ bảo trì và mở rộng.'),
      H3('2.3.2. Hệ quản trị CSDL MySQL'),
      P('MySQL là HQT CSDL quan hệ mã nguồn mở, sử dụng SQL để truy vấn. Hệ thống sử dụng MySQL với PDO (PHP Data Objects) và prepared statements để chống SQL injection. Bảng mã utf8mb4 hỗ trợ đầy đủ tiếng Việt và emoji.'),
      H3('2.3.3. HTML5, CSS3 và JavaScript'),
      P('Frontend sử dụng HTML5 cho cấu trúc trang, CSS3 với Custom Properties (CSS variables) cho Design System 3 lớp, và JavaScript (Vanilla JS + AJAX) cho tương tác người dùng. Hệ thống sử dụng Font Awesome 6.7 cho iconography và Chart.js 4.4 cho biểu đồ thống kê. Typography sử dụng Plus Jakarta Sans (heading) và Inter (body).'),
      H3('2.3.4. Xác thực và phân quyền'),
      P('Hệ thống hỗ trợ 2 phương thức xác thực: (1) username/password với password_hash() (bcrypt) và session PHP, (2) Google OAuth 2.0 cho đăng nhập nhanh. Phân quyền 3 cấp: student (mặc định), pro (có membership), admin (toàn quyền quản trị). Middleware.php kiểm tra quyền truy cập cho từng route.'),
      H3('2.3.5. OpenAI GPT API và Chatbot AI'),
      P('Hệ thống tích hợp OpenAI GPT-3.5 Turbo để xây dựng chatbot AI hỗ trợ học tập. Người dùng có thể đặt câu hỏi về ngữ pháp, từ vựng, dịch câu hoặc xin mẹo học tiếng Anh. Chatbot trả lời với ngữ cảnh được giới hạn trong phạm vi học tiếng Anh, sử dụng Markdown cho định dạng. Admin có thể cấu hình API key qua trang quản trị.'),
      H3('2.3.6. Gamification trong học tập'),
      P('Hệ thống áp dụng các yếu tố gamification để tăng động lực học tập: (1) XP và Level: người dùng nhận XP khi hoàn thành bài học, quiz, hoặc duy trì streak; level tăng theo XP (level = floor(total_xp/100) + 1). (2) Streak: điểm thưởng cho việc học liên tục mỗi ngày, bonus 50 XP cho mỗi 7 ngày liên tiếp. (3) Leaderboard: bảng xếp hạng người học theo XP. (4) Badges: 7 loại huy hiệu (Newcomer, Bookworm, Perfect Score, Star Student, Champion, Pro Member, Dedicated). (5) Certificates: chứng chỉ hoàn thành mỗi cấp độ CEFR.'),
      H3('2.3.7. Adaptive Testing với IRT'),
      P('Bài kiểm tra đầu vào (Placement Test) sử dụng mô hình IRT (Item Response Theory) đơn giản hóa. Thuật toán ước lượng năng lực người học (theta) dựa trên câu trả lời đúng/sai, sử dụng công thức cập nhật: theta_new = theta + 0.5 × (actual − expected), trong đó expected = 1/(1 + e^−(theta − difficulty)). Hệ thống kết thúc sớm khi độ lệch chuẩn của 5 theta cuối < 0.3, giúp xác định trình độ nhanh chóng chỉ với 10-25 câu hỏi.'),
      H3('2.3.8. Thanh toán QR và xử lý đơn Pro'),
      P('Hệ thống sử dụng mô hình thanh toán thủ công: người dùng chọn gói Pro → tạo đơn → chuyển khoản ngân hàng qua QR code → admin xác nhận và phê duyệt đơn. MembershipService xử lý logic gia hạn (cộng dồn thời gian nếu đang active) và tự động hạ cấp khi hết hạn. Hỗ trợ gói lifetime (duration_months = -1).'),
    ]),

    // ═══ CHƯƠNG 3 ═══
    sec([
      H1('Chương 3. NỘI DUNG THỰC TẬP'),
      H2('3.1. Phân tích yêu cầu hệ thống'),
      H3('3.1.1. Yêu cầu chức năng'),
      P('Hệ thống có 2 nhóm người dùng chính:'),
      B('Người học (Student/Pro): Đăng ký/đăng nhập (email + Google OAuth); làm bài kiểm tra đầu vào để xác định trình độ CEFR; xem danh sách khóa học theo lộ trình (đang học, ôn tập, sẽ mở); học bài giảng với nội dung đa phương tiện (text, ảnh, audio, video); làm quiz tương tác (multiple choice, true/false, fill-blank, listening); làm bài thi cuối khóa và nhận chứng chỉ; theo dõi tiến độ qua dashboard (XP, streak, level, biểu đồ); xem bảng xếp hạng; tìm kiếm khóa học, từ vựng, bài kiểm tra; chat với AI chatbot; nâng cấp Pro qua chuyển khoản QR; gửi ticket hỗ trợ.'),
      B('Quản trị viên (Admin): Dashboard thống kê (biểu đồ người dùng, điểm số, đơn hàng); quản lý người dùng (thêm/sửa/xóa, nâng cấp/hạ cấp); quản lý khóa học/chủ đề; quản lý câu hỏi kiểm tra; duyệt/từ chối đơn nâng cấp Pro; phản hồi ticket hỗ trợ; cấu hình OpenAI API key.'),
      H3('3.1.2. Yêu cầu phi chức năng'),
      B('Giao diện responsive, thân thiện trên desktop và mobile (Design System v3 Professional Premium).'),
      B('Dữ liệu tiếng Việt hiển thị đúng UTF-8/utf8mb4,字体 Plus Jakarta Sans + Inter.'),
      B('Bảo mật: prepared statements (chống SQL injection), CSRF token, session HttpOnly + SameSite, password_hash (bcrypt), XSS prevention (htmlspecialchars), rate limiting.'),
      B('Hiệu năng: AJAX cho lesson loading (fetch API), debounced search (300ms), skeleton loading states.'),
      B('Tương thích: chạy ổn định trên XAMPP (Apache + MySQL), hỗ trợ Chrome/Firefox/Edge.'),
      Em(),
      H2('3.2. Sơ đồ Use Case'),
      P('Sơ đồ Use Case mô tả các tác nhân (Actor) và chức năng (Use Case) của hệ thống. Hệ thống có 2 tác nhân chính: Người học (Learner) có 10 chức năng, Quản trị viên (Admin) có 5 chức năng.'),
      ...fullFig(D + 'usecase_overview.drawio.png', 'Hình 3.1. Sơ đồ Use Case tổng quan hệ thống EngPath', 480, 480),
      Em(),
      H2('3.3. Sơ đồ Sequence'),
      H3('3.3.1. Luồng đăng nhập'),
      P('Sequence diagram mô tả luồng đăng nhập: Browser → AuthController → User Model → Database. Hệ thống kiểm tra username/password với password_verify(), tạo session mới với session_regenerate_id(), tự động kiểm tra và hạ cấp Pro nếu hết hạn, và redirect về dashboard.'),
      ...fullFig(D + 'sequence_login.drawio.png', 'Hình 3.2. Sequence Diagram – Đăng nhập', 440, 280),
      Em(),
      H3('3.3.2. Luồng nâng cấp Pro'),
      P('Sequence diagram mô tả luồng nâng cấp Pro qua QR: Người dùng chọn gói → tạo đơn (INSERT membership_orders, pending) → chuyển khoản ngân hàng → Admin kiểm tra và duyệt đơn (BEGIN TRANSACTION → UPDATE order + UPDATE user membership → COMMIT). Hỗ trợ cả luồng từ chối đơn và ticket hủy đơn.'),
      ...fullFig(D + 'sequence_purchase.drawio.png', 'Hình 3.3. Sequence Diagram – Nâng cấp Pro', 440, 320),
      Em(),
      H2('3.4. Sơ đồ State'),
      P('Sơ đồ trạng thái mô tả các trạng thái và chuyển tiếp của 4 đối tượng chính trong hệ thống:'),
      B('Course State Machine: Locked → Unlocked (khi hoàn thành khóa trước hoặc placement test) → In Progress (khi bắt đầu học) → Completed (khi hoàn thành tất cả chương) → Mastered (khi vượt qua final exam).'),
      B('Membership State: Free ↔ Pro (Admin phê duyệt / tự động hết hạn).'),
      B('Order State: Pending → Completed (Admin duyệt) / Cancelled (Admin từ chối hoặc ticket hủy).'),
      B('Ticket State: Open → In Progress → Resolved → Closed.'),
      ...fullFig(D + 'state_diagrams.drawio.png', 'Hình 3.4. State Diagram – Course, Membership, Order, Ticket', 460, 320),
      Em(),
      H2('3.5. Thiết kế cơ sở dữ liệu'),
      P('Cơ sở dữ liệu EngPath gồm 25+ bảng được tổ chức thành 5 nhóm chính, sử dụng MySQL với InnoDB engine và utf8mb4 collation:'),
      tbl(['Nhóm', 'Bảng chính', 'Mục đích'], [
        ['Người dùng', 'users, user_progress, course_progress, lesson_progress, xp_history', 'Tài khoản, tiến độ khóa học, tiến độ bài học, XP, streak'],
        ['Nội dung học', 'courses, topics, vocabularies, lessons, lesson_contents, lesson_reviews, course_reviews', 'Khóa học, chủ đề, từ vựng, bài học, nội dung, đánh giá'],
        ['Kiểm tra', 'tests, questions, test_results, user_answers', 'Bài test, câu hỏi, kết quả, đáp án người dùng'],
        ['Placement', 'placement_sessions, placement_questions, placement_responses', 'Bài kiểm tra đầu vào IRT: session, câu hỏi, câu trả lời'],
        ['Membership', 'membership_plans, membership_orders, support_tickets', 'Gói Pro, đơn nâng cấp, ticket hỗ trợ'],
      ]),
      ...fullFig(D + 'erd.drawio.png', 'Hình 3.5. Sơ đồ Cơ sở dữ liệu EngPath', 480, 480),
      Em(),
      H2('3.6. Xây dựng giao diện ứng dụng'),
      P('Giao diện EngPath được thiết kế theo phong cách Professional Premium (Coursera-style), sử dụng Design System 3-layer tokens:'),
      B('Layer 1 – Primitive Tokens: định nghĩa các giá trị thô (color scales, spacing, typography, shadows, transitions).'),
      B('Layer 2 – Semantic Tokens: gán ý nghĩa cho tokens (--color-primary: indigo-600 #4F46E5; --color-accent: orange-500 #F97316).'),
      B('Layer 3 – Component Tokens: áp dụng tokens vào components (--btn-primary-bg, --card-radius, --header-bg).'),
      P('Hệ thống sử dụng Plus Jakarta Sans cho heading (đậm, hình học, chuyên nghiệp), Inter cho body text (dễ đọc), và Font Awesome 6.7 cho iconography. Header sử dụng dark theme (slate-900), hero section với gradient background và radial blur overlays, cards với border-radius 20px và shadow tinh tế. Bảng dưới đây mô tả một số giao diện chính:'),
      Em(),
      new Table({
        width: { size: 9026, type: WidthType.DXA }, columnWidths: [4513, 4513],
        rows: [
          new TableRow({ children: [
            imgCell(D + 'architecture.drawio.png', 'Trang chủ EngPath', 420, 260),
            imgCell(D + 'erd.drawio.png', 'Dashboard người học', 420, 260),
          ]}),
          new TableRow({ children: [
            imgCell(D + 'usecase_overview.drawio.png', 'Danh sách khóa học', 420, 260),
            imgCell(D + 'sequence_login.drawio.png', 'Giao diện học tập', 420, 260),
          ]}),
        ]
      }),
      Em(),
      H2('3.7. Giao diện quản trị'),
      P('Khu vực Admin được thiết kế riêng với header tối màu và navigation bar sticky. Dashboard admin hiển thị các thống kê: tổng users, tổng pro users, tổng khóa học, tổng câu hỏi, đơn đang chờ, ticket đang mở. Các biểu đồ Chart.js hiển thị: tăng trưởng người dùng 7 ngày, phân bố điểm số, đơn hàng theo tháng, tỉ lệ free/pro. Trang quản lý có bảng dữ liệu với tìm kiếm và phân trang.'),
      Em(),
      H2('3.8. Xây dựng chức năng'),
      H3('3.8.1. Quản lý tài khoản và phân quyền'),
      P('AuthController xử lý đăng ký (validate username, email, password ≥ 6 ký tự), đăng nhập (username/email + password với password_verify), và Google OAuth 2.0 (findOrCreateByGoogle). Middleware.php kiểm tra quyền: requireLogin (redirect /auth/login), requireAdmin (role === admin), requirePro (membership === pro && chưa hết hạn). Admin có quyền nâng cấp/hạ cấp user, thay đổi role (student/admin).'),
      H3('3.8.2. Hệ thống khóa học (Course System)'),
      P('Đây là tính năng cốt lõi của EngPath, được xây dựng theo mô hình Coursera-style:'),
      B('Khóa học được tổ chức theo 5 cấp độ CEFR (A1→C1), mỗi cấp có 3 khóa học. Tổng cộng 15 khóa học.'),
      B('Mỗi khóa học có nhiều chương (topics), mỗi chương có bài học (lessons), quiz (tests), và từ vựng (vocabularies).'),
      B('Luồng khóa học: Locked → Unlocked (sau placement test hoặc hoàn thành khóa trước) → In Progress (bắt đầu học) → Completed (100% chương) → Mastered (vượt qua final exam).'),
      B('Sau khi hoàn thành 3 khóa cùng cấp, người học nhận chứng chỉ CEFR và mở khóa cấp độ tiếp theo.'),
      B('Học lại (review): người dùng có thể ôn tập các khóa đã mastered ở cấp độ thấp hơn.'),
      B('Bài thi cuối khóa (final exam): mở khi completion_percent ≥ 100%, yêu cầu điểm đạt ≥ 60%.'),
      B('Giao diện học tập: layout 2 panel (sidebar danh sách bài học + content area), AJAX load nội dung bài học và quiz, lưu tiến độ tự động.'),
      H3('3.8.3. Bài kiểm tra đầu vào (Placement Test)'),
      P('Placement Test sử dụng thuật toán IRT (Item Response Theory) thích ứng:'),
      B('Người dùng tự đánh giá ban đầu (mới bắt đầu / đã biết một ít / khá) → khởi tạo theta (0.0 / 2.0 / 4.0).'),
      B('Mỗi câu hỏi có độ khó (difficulty) được gán trước. Hệ thống chọn câu hỏi có difficulty gần với theta hiện tại nhất.'),
      B('Sau mỗi câu trả lời, theta được cập nhật: theta_new = theta + 0.5 × (actual − expected), expected = 1/(1+e^−(theta−difficulty)).'),
      B('Kết thúc sớm (early termination): khi ≥ 10 câu và độ lệch chuẩn 5 theta cuối < 0.3 → ước lượng đã ổn định.'),
      B('Kết quả: quy đổi theta → CEFR (A1: theta < 1, A2: 1-2, B1: 2-3, B2: 3-4, C1: theta ≥ 4).'),
      B('Sau khi có kết quả, hệ thống tự động khởi tạo course_progress: khóa dưới trình độ → mastered, khóa đầu tiên cùng trình độ → unlocked.'),
      H3('3.8.4. Tìm kiếm (Search)'),
      P('Hệ thống tìm kiếm toàn văn được tích hợp trong navbar header, hoạt động qua AJAX với debounce 300ms:'),
      B('Backend: HomeController::search() nhận query ≥ 2 ký tự, gọi Topic::search() tìm kiếm LIKE trên 4 bảng (topics: name + description, vocabularies: word + meaning_vi + pronunciation, tests: title, lessons: title).'),
      B('Frontend: input search trong navbar với icon kính lúp. Dropdown hiển thị kết quả chia 4 nhóm (Khóa học, Từ vựng, Bài kiểm tra, Bài học), mỗi kết quả có icon màu, title, meta, và link đến trang tương ứng.'),
      B('Tương tác: click outside để đóng, Escape để đóng, input mở rộng khi focus.'),
      H3('3.8.5. Gamification và Dashboard'),
      P('Hệ thống gamification do StreakService quản lý, tích hợp vào dashboard người dùng:'),
      B('Dashboard hiển thị: welcome message, daily plan card (XP hôm nay / goal), streak cards (số ngày liên tiếp, level + total XP, % mục tiêu, streak cao nhất, trình độ CEFR), bài học tiếp theo với progress bar.'),
      B('Biểu đồ Chart.js: bar chart điểm theo chủ đề, radar/pie chart phân bố kỹ năng (Listening, Speaking, Reading, Writing).'),
      B('Progress table: danh sách chủ đề với level badge, tiến độ từ vựng/bài học, và điểm số.'),
      B('XP được thưởng cho các hoạt động: học từ vựng (+10), hoàn thành bài học (+20), làm quiz (+50), duy trì streak 7 ngày (+50 bonus).'),
      H3('3.8.6. Nâng cấp Pro và Hỗ trợ'),
      P('Hệ thống membership cho phép người dùng nâng cấp từ Free lên Pro qua quy trình: chọn gói (1/3/6/12 tháng hoặc Lifetime) → tạo đơn pending → chuyển khoản ngân hàng qua QR (Techcombank) → admin xác nhận và phê duyệt → tự động kích hoạt Pro. MembershipService xử lý cộng dồn thời gian (nếu đang active) và tự động hạ cấp khi hết hạn. Người dùng có thể gửi ticket hỗ trợ (loại: hủy đơn/hỗ trợ chung/góp ý), admin phản hồi và cập nhật trạng thái.'),
      Em(),
      H2('3.9. Kiểm thử'),
      P('Hệ thống được kiểm thử thủ công trên môi trường XAMPP localhost với các test case chính:'),
      B('Auth: đăng ký, đăng nhập (email + Google OAuth), đăng xuất, phân quyền admin/student/pro.'),
      B('Course: hiển thị danh sách khóa học (active/mastered/locked), mở khóa tuần tự, tiến độ completion %, final exam, certificate.'),
      B('Placement: làm bài test từ đầu đến cuối, kiểm tra kết quả CEFR, khởi tạo course path đúng.'),
      B('Learning: AJAX load bài học, quiz (multiple choice, true/false, fill-blank, listening), lưu tiến độ tự động.'),
      B('Search: tìm kiếm khóa học/từ vựng/bài test/bài học, dropdown kết quả, click kết quả → đúng trang đích.'),
      B('Dashboard: hiển thị đúng XP/streak/level/biểu đồ, daily plan progress.'),
      B('Pro: tạo đơn, admin duyệt/từ chối, kích hoạt/hết hạn membership.'),
      B('UI: responsive trên các kích thước màn hình (375px, 768px, 1024px, 1440px), dark header + light content.'),
    ]),

    // ═══ CHƯƠNG 4 ═══
    sec([
      H1('Chương 4. KẾT LUẬN VÀ KIẾN NGHỊ'),
      H2('4.1. Kết quả đạt được'),
      P('Sau 16 tuần thực tập, em đã hoàn thành website học tiếng Anh trực tuyến EngPath với các kết quả chính:'),
      B('Xây dựng thành công hệ thống khóa học theo lộ trình CEFR (A1–C1) với 15 khóa học, mỗi khóa có chương, bài học đa phương tiện, quiz tương tác và bài thi cuối khóa. Hệ thống tự động mở khóa khóa học tiếp theo khi hoàn thành, cấp chứng chỉ khi hoàn thành cấp độ.'),
      B('Xây dựng bài kiểm tra đầu vào thích ứng (adaptive placement test) sử dụng mô hình IRT, giúp xác định trình độ người học chính xác chỉ với 10-25 câu hỏi và tự động thiết lập lộ trình học phù hợp.'),
      B('Tích hợp tìm kiếm toàn văn (AJAX search) cho phép người dùng tìm nhanh khóa học, từ vựng, bài kiểm tra, bài học từ bất kỳ trang nào.'),
      B('Xây dựng dashboard học tập với gamification: XP, streak, level, badges, leaderboard, biểu đồ Chart.js.'),
      B('Tích hợp chatbot AI (OpenAI GPT-3.5 Turbo) hỗ trợ giải đáp thắc mắc về tiếng Anh.'),
      B('Xây dựng hệ thống membership Pro với thanh toán QR và admin duyệt, ticket hỗ trợ.'),
      B('Xây dựng admin panel đầy đủ: quản lý users, khóa học, câu hỏi, đơn hàng, tickets, thống kê.'),
      B('Thiết kế giao diện Professional Premium (Design System v3) với 3-layer CSS tokens, responsive, dark header, Plus Jakarta Sans + Inter typography, indigo/orange palette.'),
      B('Tổ chức mã nguồn theo mô hình MVC sạch: 12 controllers, 11 models, 20+ views, core layer với Middleware, Router, CSRF, RateLimiter. Database 25+ bảng với prepared statements và utf8mb4.'),
      B('Codebase đạt 0 lỗi PHPStan level 5 và 19 PHPUnit tests.'),
      Em(),
      H2('4.2. Hạn chế'),
      B('Thanh toán Pro chưa tích hợp cổng thanh toán tự động (VNPay, Momo) hoặc webhook ngân hàng để xác nhận giao dịch real-time. Hiện tại admin phải duyệt thủ công.'),
      B('Nội dung học tập còn hạn chế, cần bổ sung thêm dữ liệu cho các khóa học, đặc biệt là bài tập listening và reading.'),
      B('Chatbot AI phụ thuộc vào OpenAI API key do admin cấu hình. Nếu API key hết hạn hoặc hết credit, chatbot sẽ không hoạt động.'),
      B('Hệ thống mới được triển khai trên môi trường local/XAMPP, chưa được tối ưu hóa cho production hosting (VPS, HTTPS, CDN, caching, logging).'),
      B('Chưa có phiên bản mobile native hoặc PWA. Người dùng chỉ truy cập qua trình duyệt web.'),
      B('Chức năng luyện nói (speaking) trước đây có tích hợp Web Speech API và OpenAI đã bị loại bỏ trong quá trình tái cấu trúc, cần được xây dựng lại.'),
      Em(),
      H2('4.3. Kiến nghị và hướng phát triển'),
      B('Triển khai hệ thống lên hosting/VPS thực tế, cấu hình HTTPS (Let\'s Encrypt), thiết lập CDN cho assets, backup database định kỳ.'),
      B('Tích hợp cổng thanh toán tự động (VNPay, Momo) hoặc Casso webhook để xác nhận giao dịch ngân hàng real-time, giảm thời gian chờ admin duyệt.'),
      B('Bổ sung nội dung học tập: thêm khóa học, bài học, câu hỏi listening/reading/speaking cho tất cả các cấp độ.'),
      B('Xây dựng lại chức năng luyện nói AI (speaking) với Web Speech API và OpenAI GPT để chấm điểm phát âm, fluency, và cung cấp feedback chi tiết.'),
      B('Phát triển tính năng ôn tập từ vựng theo thuật toán Spaced Repetition System (SRS) như Anki để tối ưu hóa việc ghi nhớ.'),
      B('Nâng cấp AI chatbot thành trợ lý hội thoại có ngữ cảnh theo bài học, hỗ trợ hội thoại hai chiều thay vì chỉ hỏi-đáp đơn giản.'),
      B('Phát triển Progressive Web App (PWA) để người dùng có thể cài đặt trên điện thoại, học offline một phần.'),
      B('Bổ sung báo cáo thống kê nâng cao cho admin: tỉ lệ hoàn thành khóa học, thời gian học trung bình, tỉ lệ chuyển đổi Pro, doanh thu theo tháng.'),
      B('Mở rộng hỗ trợ đa ngôn ngữ giao diện (i18n) để tiếp cận người dùng quốc tế.'),
    ]),

    // ═══ CHƯƠNG 5 ═══
    sec([
      H1('Chương 5. TÀI LIỆU THAM KHẢO'),
      Em(),
      P('[1] PHP Manual. Địa chỉ: https://www.php.net/docs.php', { size: 24 }),
      P('[2] MySQL 8.0 Reference Manual. Địa chỉ: https://dev.mysql.com/doc/refman/8.0/en/', { size: 24 }),
      P('[3] MDN Web Docs – HTML, CSS, JavaScript. Địa chỉ: https://developer.mozilla.org/', { size: 24 }),
      P('[4] Google Identity – OAuth 2.0 Documentation. Địa chỉ: https://developers.google.com/identity/protocols/oauth2', { size: 24 }),
      P('[5] OpenAI API Documentation. Địa chỉ: https://platform.openai.com/docs/', { size: 24 }),
      P('[6] Chart.js Documentation. Địa chỉ: https://www.chartjs.org/docs/', { size: 24 }),
      P('[7] Font Awesome Documentation. Địa chỉ: https://fontawesome.com/docs/', { size: 24 }),
      P('[8] Item Response Theory – Wikipedia. Địa chỉ: https://en.wikipedia.org/wiki/Item_response_theory', { size: 24 }),
      P('[9] CEFR – Common European Framework of Reference for Languages. Địa chỉ: https://www.coe.int/en/web/common-european-framework-reference-languages/', { size: 24 }),
      P('[10] PDO – PHP Data Objects. Địa chỉ: https://www.php.net/manual/en/book.pdo.php', { size: 24 }),
      P('[11] CSS Custom Properties – MDN. Địa chỉ: https://developer.mozilla.org/en-US/docs/Web/CSS/--*', { size: 24 }),
      P('[12] Monolog – PHP Logging. Địa chỉ: https://github.com/Seldaek/monolog', { size: 24 }),
      Em(), Em(), Em(),
      H1('NHẬN XÉT KẾT QUẢ THỰC TẬP'),
      P('(Dành cho Giảng viên hướng dẫn sinh viên thực tập)', { italics: true, color: '94A3B8' }),
      Em(),
      B('Họ tên sinh viên thực tập: Phan Quang Thuật'),
      B('Mã số sinh viên: 2120110351'),
      B('Lớp: CCQ2011E – Khóa: K44'),
      B('Giảng viên hướng dẫn thực tập: Vũ Thị Hường'),
      Em(),
      P('Sau thời gian sinh viên …………………………………. thực tập tại đơn vị, chúng tôi có nhận xét sau:'),
      Em(),
      P('Về ý thức chấp hành nội quy, quy định của công ty: ………………………………………………………………………………', { size: 24 }),
      Em(), Em(),
      P('Về đạo đức, tác phong: …………………………………………………………………………………………………………………………', { size: 24 }),
      Em(), Em(),
      P('Về năng lực chuyên môn: ………………………………………………………………………………………………………………………', { size: 24 }),
      Em(), Em(),
      P('Kết luận: …………………………………………………………………………………………………………………………………………………', { size: 24 }),
      Em(), Em(),
      P('Nhận xét: ………………………………………………………………………………………………………………………………………………', { size: 24 }),
      Em(),
      P('Điểm: …………….., Ngày…… tháng…… năm…….', { a: AlignmentType.RIGHT }),
      Em(), Em(),
      P('Cán bộ hướng dẫn', { a: AlignmentType.RIGHT, bold: true }),
      Em(), Em(), Em(),
      P('Xác nhận của đơn vị', { a: AlignmentType.CENTER, bold: true }),
    ]),
  ],
});

// ── WRITE ──
Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync(OUT, buf);
  console.log('✅ OK:', OUT);
  console.log('📦', (buf.length / 1024 / 1024).toFixed(1), 'MB');
}).catch(err => { console.error('❌', err.message); process.exit(1); });
