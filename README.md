# EngPath — Website học tiếng Anh trực tuyến

> **Đồ án thực tập tốt nghiệp** — Trường Cao đẳng Công Thương TP.HCM  
> Sinh viên: **Phan Quang Thuật** — MSSV: 2120110351 — Lớp: CCQ2011E  
> Giảng viên hướng dẫn: **Vũ Thị Hường**

EngPath là nền tảng học tiếng Anh trực tuyến theo lộ trình CEFR (A1-C1), xây dựng bằng PHP MVC thuần, MySQL, Tailwind CSS và JavaScript. Hệ thống gồm 15 khóa học, bài giảng đa phương tiện, quiz tương tác, luyện nói AI, bài thi cuối khóa, gamification (XP/streak/level) và khu quản trị riêng.

---

## Tính năng chính

### Người học
- Đăng ký / đăng nhập (email + Google OAuth)
- Kiểm tra đầu vào IRT (Placement Test) để xác định trình độ CEFR
- 15 khóa học từ A1 đến C1, mỗi cấp 3 khóa
- Học bài giảng đa phương tiện (text, audio, video)
- Làm quiz trắc nghiệm, chấm điểm tức thì
- Bài thi cuối khóa Reading + Listening
- Luyện nói với AI (Web Speech API + OpenAI GPT)
- Dashboard: XP, streak, level, daily goal, biểu đồ Chart.js
- Bảng xếp hạng (Leaderboard)
- AI Chatbot hỗ trợ học tiếng Anh
- Tìm kiếm AJAX toàn trang
- Nâng cấp Pro qua QR code + admin duyệt
- Gửi ticket hỗ trợ
- Nhận chứng chỉ hoàn thành cấp độ CEFR

### Quản trị viên (Admin)
- Dashboard thống kê (biểu đồ, stat cards)
- Quản lý người dùng (thêm/sửa/xóa, phân quyền)
- Quản lý khóa học & chủ đề
- Quản lý câu hỏi kiểm tra
- Duyệt/từ chối đơn nâng cấp Pro
- Quản lý ticket hỗ trợ
- Cấu hình OpenAI API key

---

## Công nghệ sử dụng

| Phần | Công nghệ |
|------|-----------|
| Backend | PHP 8.x, MVC thuần (custom framework) |
| Database | MySQL, PDO prepared statements, utf8mb4 |
| Frontend | HTML5, Tailwind CSS, CSS3, JavaScript, AJAX |
| Biểu đồ | Chart.js 4.4 |
| Icons | Font Awesome 6 |
| Fonts | Inter + Plus Jakarta Sans (Google Fonts) |
| Đăng nhập | Google OAuth 2.0, session PHP, CSRF protection |
| Luyện nói | Web Speech API + OpenAI GPT-3.5 Turbo |
| Chatbot AI | OpenAI GPT API |
| Thanh toán | QR Code ngân hàng + admin duyệt thủ công |
| Server | XAMPP (Apache + MySQL) |

---

## Cài đặt

### Yêu cầu
- **XAMPP** (PHP 8.0+, MySQL 5.7+)
- **Composer** (để cài dependencies PHP)

### Bước 1: Clone hoặc giải nén source code

Đặt thư mục dự án vào `C:\xampp\htdocs\DA_TTTN`

### Bước 2: Cài dependencies

```bash
cd C:\xampp\htdocs\DA_TTTN
composer install
```

### Bước 3: Tạo database

Mở phpMyAdmin (`http://localhost/phpmyadmin`), tạo database `english_master` với charset `utf8mb4_general_ci`.

Import schema + dữ liệu mẫu:

```bash
mysql -u root english_master < database/schema.sql
mysql -u root english_master < database/english_master_full.sql
```

### Bước 4: Cấu hình môi trường

```bash
copy .env.example .env
```

Mở `.env` và điền thông tin database (mặc định XAMPP: user `root`, password rỗng). Các API key (Google OAuth, OpenAI) là tùy chọn — hệ thống vẫn chạy được nếu không có.

### Bước 5: Chạy

Truy cập: **http://localhost/DA_TTTN/public/**

---

## Tài khoản mẫu

| Vai trò | Username | Password |
|---------|----------|----------|
| Admin | `admin` | `admin123` |
| Học viên | `student1` | `student123` |

---

## Cấu trúc thư mục

```
DA_TTTN/
├── app/
│   ├── config/          # Cấu hình app, database, env
│   ├── controllers/     # 13 controllers
│   ├── core/            # App, Router, Controller, Model, Middleware, CSRF...
│   ├── Helpers/         # Request, Validator
│   ├── models/          # 11 models
│   ├── Services/        # MembershipService, OpenAIService, StreakService
│   └── views/           # 30+ view files (layouts, pages, admin)
├── database/
│   ├── schema.sql               # Cấu trúc database
│   ├── english_master_full.sql  # Dữ liệu mẫu
│   └── migration_*.sql          # Lịch sử migration
├── docs/
│   ├── diagrams/        # Sơ đồ UML (.drawio + .png)
│   └── screenshots/     # Ảnh giao diện
├── public/
│   ├── index.php        # Entry point (Front Controller)
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript
│   └── images/          # Hình ảnh
├── .env.example         # File cấu hình mẫu
├── .htaccess            # Apache URL rewriting
├── composer.json        # PHP dependencies
└── README.md
```

---

## Các URL chính

| Trang | URL |
|-------|-----|
| Trang chủ | `/public/` |
| Đăng nhập | `/public/auth/login` |
| Đăng ký | `/public/auth/register` |
| Khóa học | `/public/course` |
| Dashboard | `/public/dashboard` |
| Bảng xếp hạng | `/public/leaderboard` |
| Nâng cấp Pro | `/public/membership` |
| Hỗ trợ | `/public/support` |
| Kiểm tra đầu vào | `/public/placement/intro` |
| Admin | `/public/admin` |

---

## Database

Database `english_master` gồm 25+ bảng, chia thành các nhóm:

| Nhóm | Bảng |
|------|------|
| Người dùng | `users`, `xp_history` |
| Khóa học | `courses`, `topics`, `course_progress`, `lesson_progress`, `user_progress` |
| Nội dung học | `vocabularies`, `lessons`, `lesson_contents`, `lesson_reviews`, `course_reviews` |
| Kiểm tra | `tests`, `questions`, `test_results`, `user_answers` |
| Placement | `placement_sessions`, `placement_questions`, `placement_responses` |
| Membership | `membership_plans`, `membership_orders`, `support_tickets` |

---

## Ghi chú

- **Speaking AI**: Cần OpenAI API key trong `.env`. Nếu không có, hệ thống dùng phương pháp chấm điểm cục bộ.
- **Google OAuth**: Cần cấu hình Google Cloud Console với Redirect URI trùng khớp.
- **Pro Payment**: Dùng QR code chuyển khoản + admin duyệt thủ công. Webhook Casso là tùy chọn.
- **Trình duyệt**: Khuyến nghị Chrome/Firefox/Edge. Luyện nói cần quyền microphone.

---

## Tác giả

**Phan Quang Thuật** — MSSV: 2120110351 — Lớp: CCQ2011E  
Trường Cao đẳng Công Thương Thành phố Hồ Chí Minh  
Đồ án thực tập tốt nghiệp, tháng 7/2026
