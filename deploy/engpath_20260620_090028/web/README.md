# EngPath - Website học tiếng Anh trực tuyến

> Đồ án thực tập tốt nghiệp - Trường Cao đẳng Công Thương Thành phố Hồ Chí Minh  
> Sinh viên: Phan Quang Thuật - MSSV: 2120110351  
> Giảng viên hướng dẫn: Vũ Thị Hường

EngPath là website học tiếng Anh trực tuyến xây dựng bằng PHP MVC thuần, MySQL và JavaScript. Giao diện đã được làm mới theo hướng hiện đại, thân thiện hơn với người học: trang chủ kiểu landing học ngoại ngữ, danh sách khóa học rõ ràng, trang bài test dễ quét, trang Pro nổi bật và khu quản trị riêng cho admin.

## Tính năng chính

### Dành cho người học

- Đăng ký, đăng nhập tài khoản thường hoặc Google OAuth.
- Xem lộ trình học theo khóa học/chủ đề.
- Học từ vựng theo topic, đánh dấu đã học và lưu bookmark.
- Ôn tập bằng flashcard.
- Đọc bài học theo từng chủ đề.
- Làm bài test dạng Quiz, Listening và Reading.
- Học ngữ pháp kèm câu hỏi luyện tập.
- Luyện nói, ghi âm và nhận phản hồi chấm điểm.
- Theo dõi dashboard học tập: XP, level, streak, tiến độ topic.
- Xem bảng xếp hạng.
- Quản lý hồ sơ cá nhân.
- Quản lý ví điện tử, nạp/rút tiền và mua gói Pro.
- Gửi yêu cầu hỗ trợ/ticket.

### Dành cho admin

- Dashboard tổng quan hệ thống.
- Quản lý tài khoản người dùng.
- Quản lý khóa học/chủ đề.
- Quản lý câu hỏi bài test.
- Quản lý mã kích hoạt Pro.
- Quản lý đơn nâng cấp Pro.
- Quản lý ticket hỗ trợ.
- Quản lý giao dịch ví.
- Cấu hình API và trạng thái chatbot.

## Giao diện hiện tại

Các màn hình chính đã được làm mới theo phong cách sáng, rõ ràng, nhiều khoảng thở và dễ thao tác:

- Trang chủ EngPath với hero lớn, CTA học miễn phí và các khối giới thiệu lộ trình.
- Trang khóa học có bộ lọc cấp độ và card topic trực quan.
- Trang chi tiết khóa học gom từ vựng, bài học, test và speaking.
- Trang bài test chia nhóm Quiz, Listening, Reading.
- Trang Pro hiển thị các gói theo tháng, giá và quyền lợi.
- Header điều hướng riêng cho user và admin.
- Admin panel có sidebar, thống kê, bảng quản lý và thao tác nhanh.

## Công nghệ sử dụng

| Phần | Công nghệ |
|---|---|
| Backend | PHP 8.x, mô hình MVC thuần |
| Database | MySQL, PDO, charset utf8mb4 |
| Frontend | HTML5, CSS3, JavaScript, AJAX |
| UI | CSS custom, Font Awesome, responsive layout |
| Biểu đồ | Chart.js |
| Đăng nhập | Google OAuth 2.0, session PHP |
| Luyện nói | Web Speech API, microphone browser |
| Thanh toán | Ví điện tử nội bộ, VietQR/Casso webhook mô phỏng |
| Server local | XAMPP, Apache, MySQL |

## Cài đặt local

### 1. Clone dự án

```bash
cd C:\xampp\htdocs
git clone https://github.com/sylarbear/DA_TTTN.git
```

Sau khi clone, thư mục dự án nên nằm tại:

```text
C:\xampp\htdocs\DA_TTTN
```

### 2. Import database

1. Mở XAMPP Control Panel.
2. Start `Apache` và `MySQL`.
3. Truy cập `http://localhost/phpmyadmin`.
4. Tạo database mới tên `english_master`.
5. Chọn charset/collation dạng UTF-8, ưu tiên `utf8mb4_general_ci`.
6. Import file:

```text
database/english_master_full.sql
```

Có thể import bằng terminal:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS english_master CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root english_master < database/english_master_full.sql
```

### 3. Cấu hình môi trường

Copy file mẫu:

```bash
copy app\config\env.example.php app\config\env.php
```

Mở `app/config/env.php` và điền thông tin nếu muốn dùng Google OAuth hoặc Casso:

```php
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('CASSO_API_KEY', '');
define('CASSO_WEBHOOK_SECRET', '');
```

Nếu chạy local bằng XAMPP mặc định thì không cần đổi cấu hình database. File đang dùng:

```text
app/config/database.php
```

Thông tin mặc định:

| Mục | Giá trị |
|---|---|
| Database | `english_master` |
| User | `root` |
| Password | rỗng |
| Charset | `utf8mb4` |

### 4. Chạy website

Truy cập:

```text
http://localhost/DA_TTTN/public/
```

## Tài khoản mẫu

| Vai trò | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Học viên | `student1` | `student123` |

Nếu tài khoản mẫu khác với database bạn đang import, hãy kiểm tra lại bảng `users` trong phpMyAdmin.

## Các đường dẫn quan trọng

| Trang | URL local |
|---|---|
| Trang chủ | `http://localhost/DA_TTTN/public/` |
| Đăng nhập | `http://localhost/DA_TTTN/public/auth/login` |
| Đăng ký | `http://localhost/DA_TTTN/public/auth/register` |
| Khóa học | `http://localhost/DA_TTTN/public/topic` |
| Bài test | `http://localhost/DA_TTTN/public/test` |
| Luyện nói | `http://localhost/DA_TTTN/public/speaking` |
| Ngữ pháp | `http://localhost/DA_TTTN/public/grammar` |
| Pro | `http://localhost/DA_TTTN/public/membership` |
| Dashboard học viên | `http://localhost/DA_TTTN/public/dashboard` |
| Admin | `http://localhost/DA_TTTN/public/admin` |

## Cấu trúc thư mục

```text
DA_TTTN/
├── app/
│   ├── config/          # Cấu hình app, database, env
│   ├── controllers/     # Controller theo từng module
│   ├── core/            # App core, Controller base, Middleware
│   ├── models/          # Model làm việc với database
│   └── views/           # Giao diện người dùng và admin
├── database/
│   ├── schema.sql
│   ├── english_master_full.sql
│   └── migration_*.sql
├── docs/                # Tài liệu báo cáo, UML, wireframe, ảnh minh họa
├── public/
│   ├── index.php        # Entry point
│   ├── css/
│   ├── js/
│   └── uploads/
├── .htaccess
├── .gitignore
└── README.md
```

## Database

Database hiện có 23 bảng chính:

```text
activation_codes, bookmarks, grammar_lessons, grammar_questions,
lesson_contents, lesson_reviews, lessons, membership_orders,
membership_plans, questions, speaking_attempts, speaking_prompts,
support_tickets, test_results, tests, topics, user_answers,
user_progress, users, vocab_reviews, vocabularies,
wallet_transactions, xp_history
```

Nhóm chức năng:

| Nhóm | Bảng tiêu biểu |
|---|---|
| Người dùng | `users`, `user_progress`, `xp_history`, `bookmarks` |
| Nội dung học | `topics`, `vocabularies`, `lessons`, `lesson_contents` |
| Kiểm tra | `tests`, `questions`, `test_results`, `user_answers` |
| Ngữ pháp | `grammar_lessons`, `grammar_questions` |
| Luyện nói | `speaking_prompts`, `speaking_attempts` |
| Pro và ví | `membership_plans`, `membership_orders`, `wallet_transactions`, `activation_codes` |
| Hỗ trợ | `support_tickets` |

## Screenshots

Một số ảnh giao diện nằm trong thư mục `docs/screenshots/`:

| Trang chủ | Khóa học | Bài test |
|---|---|---|
| ![Trang chủ](docs/screenshots/02_homepage_top.png) | ![Khóa học](docs/screenshots/04_topics.png) | ![Bài test](docs/screenshots/09_test.png) |

| Pro | Dashboard | Admin |
|---|---|---|
| ![Pro](docs/screenshots/18_membership_pricing.png) | ![Dashboard](docs/screenshots/11_dashboard.png) | ![Admin](docs/screenshots/22_admin_dashboard.png) |

## Ghi chú khi chạy local

- Nếu phpMyAdmin load mãi, hãy kiểm tra MySQL trong XAMPP đã chạy chưa.
- Nếu MySQL báo lỗi port, kiểm tra cổng `3306` có bị phần mềm khác chiếm không.
- Nếu website hiện lỗi font tiếng Việt, hãy chắc chắn database dùng `utf8mb4` và file SQL được import đúng encoding.
- Tính năng luyện nói cần trình duyệt cho phép microphone.
- Google OAuth chỉ hoạt động khi `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` và redirect URI được cấu hình đúng.

## Tác giả

Phan Quang Thuật  
MSSV: 2120110351  
Trường Cao đẳng Công Thương Thành phố Hồ Chí Minh

## Mục đích

Dự án được phát triển phục vụ đồ án thực tập tốt nghiệp, tập trung vào xây dựng website học tiếng Anh trực tuyến có giao diện thân thiện, chức năng học tập đầy đủ và khu quản trị nội dung.
