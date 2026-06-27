# BÁO CÁO THỰC TẬP TỐT NGHIỆP

**Đề tài:** XÂY DỰNG WEBSITE HỌC TIẾNG ANH TRỰC TUYẾN ENGPATH

**GV hướng dẫn:** Vũ Thị Hường  
**Sinh viên:** Phan Quang Thuật | **MSSV:** 2120110351  
**Trường:** Cao đẳng Công Thương TPHCM | **Khoa:** Công Nghệ Thông Tin  
**TPHCM, tháng 6 năm 2025**

---

## LỜI CẢM ƠN

Trong suốt quá trình thực tập và thực hiện đồ án tốt nghiệp, em đã nhận được sự hướng dẫn, giúp đỡ và động viên từ nhiều phía.

Trước hết, em xin gửi lời cảm ơn chân thành đến cô **Vũ Thị Hường** — giảng viên khoa Công Nghệ Thông Tin, Trường Cao Đẳng Công Thương TPHCM — người đã trực tiếp hướng dẫn, định hướng đề tài và đóng góp nhiều ý kiến quý báu trong suốt quá trình em thực hiện đồ án.

Em cũng xin cảm ơn quý thầy cô trong khoa Công Nghệ Thông Tin đã trang bị cho em những kiến thức nền tảng về lập trình, cơ sở dữ liệu, thiết kế web và phân tích hệ thống — những kiến thức không thể thiếu để em hoàn thành đồ án này.

Cuối cùng, em xin cảm ơn gia đình và bạn bè đã luôn ủng hộ, động viên em trong suốt thời gian học tập và thực hiện đề tài.

Mặc dù đã cố gắng hết sức, nhưng do thời gian và kinh nghiệm còn hạn chế, đồ án không tránh khỏi những thiếu sót. Em rất mong nhận được sự góp ý từ quý thầy cô để đồ án được hoàn thiện hơn.

Em xin chân thành cảm ơn!

---

## ĐỀ CƯƠNG THỰC TẬP

**1. Nơi thực tập:**  
Tự thực hiện đồ án tại nhà dưới sự hướng dẫn của giảng viên khoa Công Nghệ Thông Tin.

**2. Đề tài:**  
Xây dựng Website Học Tiếng Anh Trực Tuyến EngPath

**3. Mục tiêu đề tài:**  

Đề tài thuộc lĩnh vực phát triển ứng dụng web, giải quyết bài toán học tiếng Anh trực tuyến cho người Việt. Website cung cấp một lộ trình học tập rõ ràng: từ học từ vựng theo chủ đề, đọc bài học, làm bài kiểm tra, luyện nói với AI, đến theo dõi tiến độ học tập qua dashboard.

Ý nghĩa thực tiễn:
- Giúp người học tiếng Anh có một nền tảng học tập có cấu trúc, dễ tiếp cận
- Tích hợp AI để chấm điểm phát âm, giúp người học luyện nói mọi lúc
- Tiết kiệm chi phí so với việc học tại trung tâm
- Xây dựng thói quen học tập thông qua hệ thống streak, XP và bảng xếp hạng

**4. Công nghệ sử dụng:**

| Thành phần | Công nghệ |
|---|---|
| Ngôn ngữ chính | PHP 8.0 (MVC pattern thuần, không framework) |
| Cơ sở dữ liệu | MySQL (PDO, prepared statements) |
| Frontend | HTML5, CSS3, JavaScript (Vanilla JS) |
| Thư viện CSS | Font Awesome 6.5, Google Fonts (Be Vietnam Pro, Inter) |
| Biểu đồ | Chart.js 4.4 |
| AI/API | OpenAI API (GPT-3.5 Turbo cho Speaking + Chatbot) |
| OAuth | Google OAuth 2.0 (đăng nhập Google) |
| Webhook | Casso API (tự động xác nhận thanh toán ngân hàng) |
| Quản lý gói | Composer (PHP dependencies), PSR-4 autoload |
| Testing | PHPUnit 9.6, PHPStan level 5 |
| Code Style | PHP-CS-Fixer (PSR-12) |

**5. Nội dung chính của thực tập:**

- **Chương 1:** Tổng quan về đề tài học tiếng Anh trực tuyến
- **Chương 2:** Cơ sở lý thuyết (MVC pattern, PHP, MySQL, PDO, RESTful API, AI Speaking)
- **Chương 3:** Phân tích, thiết kế hệ thống và xây dựng ứng dụng
- **Chương 4:** Kết luận, đánh giá kết quả và hướng phát triển

**6. Tiến độ thực hiện:**

| TT | Thời gian | Nội dung công việc | Kết quả |
|---|---|---|---|
| 1 | Tháng 3/2025 | Khảo sát, chọn đề tài, viết đề cương | Đề cương chi tiết |
| 2 | Tháng 4/2025 | Phân tích thiết kế CSDL, xây dựng MVC core | Database schema, App core |
| 3 | Tháng 5/2025 | Xây dựng chức năng người dùng (topics, lessons, tests) | CRUD modules learner |
| 4 | Tháng 5/2025 | Tích hợp AI Speaking + Chatbot, Google OAuth | OpenAI + OAuth |
| 5 | Tháng 6/2025 | Admin panel, Membership Pro, Wallet, Webhook | Admin + Payment |
| 6 | Tháng 6/2025 | Kiểm thử, sửa lỗi, viết báo cáo | Hoàn thiện đồ án |

---

## Chương 1. TỔNG QUAN VỀ ĐỀ TÀI

### 1.1. Giới thiệu đề tài

EngPath là website học tiếng Anh trực tuyến được xây dựng nhằm cung cấp một môi trường học tập toàn diện cho người Việt. Website tổ chức nội dung theo lộ trình rõ ràng: chọn chủ đề → học từ vựng → đọc bài học → làm bài kiểm tra → luyện nói → theo dõi tiến độ.

Điểm khác biệt của EngPath so với các website học tiếng Anh khác:
- **Lộ trình học tập có cấu trúc:** Mỗi chủ đề (topic) là một đơn vị học tập hoàn chỉnh, bao gồm từ vựng, bài học, bài test và luyện nói
- **Tích hợp AI Speaking:** Sử dụng OpenAI API để chấm điểm phát âm, đưa ra phản hồi chi tiết bằng tiếng Việt
- **Gamification:** Hệ thống XP, streak, level, badge và bảng xếp hạng tạo động lực học tập
- **Membership Pro:** Mô hình freemium với gói Pro mở khóa tính năng nâng cao
- **Admin Dashboard:** Quản trị viên có thể quản lý toàn bộ hệ thống qua giao diện web

### 1.2. Mục tiêu đề tài

- Xây dựng website học tiếng Anh hoàn chỉnh với đầy đủ chức năng cho learner và admin
- Áp dụng mô hình MVC thuần để hiểu sâu kiến trúc web
- Tích hợp các API bên thứ ba (OpenAI, Google OAuth, Casso)
- Xây dựng giao diện hiện đại, thân thiện với người dùng
- Đảm bảo bảo mật cơ bản (prepared statements, session security, CSRF protection, rate limiting)

### 1.3. Đối tượng và phạm vi

**Đối tượng sử dụng:**
- Người học tiếng Anh ở mọi trình độ (beginner, intermediate, advanced)
- Quản trị viên hệ thống

**Phạm vi:**
- 6 chủ đề học tập với 70+ từ vựng, 18+ bài học, 12+ bài test
- Hệ thống thành viên (free/pro), ví điện tử, nạp/rút tiền
- Khu quản trị với 10+ chức năng quản lý

---

## Chương 2. CƠ SỞ LÝ THUYẾT

### 2.1. Mô hình MVC (Model-View-Controller)

EngPath được xây dựng theo mô hình MVC thuần (không sử dụng framework):

- **Model (`app/models/`):** Chịu trách nhiệm thao tác với cơ sở dữ liệu. Base Model cung cấp các phương thức CRUD chung (all, find, findBy, where, create, update, delete, count). Mỗi model kế thừa từ Base Model, tương ứng với một bảng trong database.
- **View (`app/views/`):** Chịu trách nhiệm hiển thị giao diện. Sử dụng PHP template thuần, tổ chức theo thư mục controller/action. Layout được tách riêng trong `views/layouts/`.
- **Controller (`app/controllers/`):** Điều phối giữa Model và View. Nhận request từ người dùng, gọi Model để lấy dữ liệu, truyền dữ liệu vào View để render.

**Luồng xử lý request:**
```
URL request → .htaccess → public/index.php → App (Router) → Controller → Model → Database
                                                              ↓
                                                            View → Response
```

### 2.2. PHP và PDO

Ngôn ngữ PHP 8.0 được chọn vì:
- Hỗ trợ lập trình hướng đối tượng đầy đủ
- PDO (PHP Data Objects) cung cấp API thống nhất để làm việc với MySQL
- Prepared Statements chống SQL Injection
- Cộng đồng lớn, tài liệu phong phú

PDO được cấu hình với:
- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` — ném exception khi có lỗi SQL
- `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` — trả về mảng kết hợp
- `PDO::ATTR_EMULATE_PREPARES => false` — sử dụng native prepared statements của MySQL

### 2.3. Cơ sở dữ liệu MySQL

Database `english_master` gồm 20+ bảng, thiết kế theo chuẩn 3NF:

**Các bảng chính:**

| Bảng | Mô tả | Số trường |
|---|---|---|
| `users` | Người dùng (student/admin) | 18 |
| `topics` | Chủ đề học tập | 8 |
| `vocabularies` | Từ vựng theo chủ đề | 8 |
| `lessons` | Bài học | 8 |
| `tests` | Bài kiểm tra | 8 |
| `questions` | Câu hỏi kiểm tra | 8 |
| `test_results` | Kết quả làm bài | 8 |
| `user_answers` | Câu trả lời của user | 7 |
| `speaking_prompts` | Câu hỏi luyện nói | 7 |
| `speaking_attempts` | Lượt luyện nói | 9 |
| `grammar_lessons` | Bài học ngữ pháp | 7 |
| `membership_plans` | Gói hội viên | 5 |
| `membership_orders` | Đơn nâng cấp Pro | 11 |
| `activation_codes` | Mã kích hoạt | 8 |
| `wallet_transactions` | Giao dịch ví | 9 |
| `support_tickets` | Ticket hỗ trợ | 8 |
| `user_progress` | Tiến độ học tập | 7 |
| `bookmarks` | Từ vựng đã lưu | 4 |
| `xp_history` | Lịch sử XP | 6 |

### 2.4. OpenAI API — AI Speaking & Chatbot

Tích hợp OpenAI GPT-3.5 Turbo cho 2 tính năng:
- **AI Speaking Evaluation:** Gửi transcript từ Web Speech API và sample answer lên OpenAI, nhận về điểm accuracy, fluency, pronunciation và feedback bằng tiếng Việt
- **AI Chatbot:** Trợ lý học tiếng Anh, trả lời câu hỏi về ngữ pháp, từ vựng, dịch thuật

### 2.5. RESTful API Design (JSON)

Các endpoint AJAX trong hệ thống tuân theo thiết kế RESTful:
- Sử dụng HTTP method đúng ngữ nghĩa (GET đọc, POST tạo/sửa/xóa)
- Trả về JSON với cấu trúc thống nhất `{success: bool, data/error: ...}`
- HTTP status code phù hợp (200, 400, 404, 405, 422, 429, 500)

### 2.6. Bảo mật Web

Các biện pháp bảo mật được áp dụng:
- **Prepared Statements** cho tất cả truy vấn SQL
- **Session Security:** HttpOnly cookie, SameSite=Lax, Secure flag (HTTPS), session_regenerate_id sau login
- **CSRF Protection:** Token cho form POST
- **Rate Limiting:** Giới hạn 5 lần đăng nhập/60 giây
- **Input Validation:** Validate dữ liệu đầu vào tập trung qua Validator class
- **Path Traversal Protection:** Router chỉ cho phép ký tự a-zA-Z trong controller name
- **Method Blocking:** Các method nội bộ bị chặn không cho gọi từ URL
- **Environment Separation:** APP_ENV (development/production) kiểm soát hiển thị lỗi
- **SSL Verification:** CURLOPT_SSL_VERIFYPEER = true khi gọi API bên ngoài

---

## Chương 3. NỘI DUNG THỰC TẬP

### 3.1. Phân tích hệ thống

#### 3.1.1. Yêu cầu chức năng

**Actor: Learner (Người học)**
- Đăng ký / Đăng nhập (email+password hoặc Google OAuth)
- Xem danh sách chủ đề học tập, lọc theo level
- Xem chi tiết chủ đề: từ vựng, bài học, bài test, luyện nói
- Học từ vựng với flashcard, đánh dấu đã học
- Đọc bài học theo chủ đề
- Làm bài test (Quiz, Listening, Reading), xem kết quả
- Luyện nói với AI, nhận chấm điểm và phản hồi
- Chat với AI Assistant về tiếng Anh
- Xem dashboard: XP, streak, level, tiến độ
- Xem bảng xếp hạng
- Quản lý hồ sơ cá nhân, đổi mật khẩu
- Nạp/rút tiền vào ví điện tử
- Mua gói Pro qua ví hoặc chuyển khoản ngân hàng
- Kích hoạt mã Pro
- Gửi ticket hỗ trợ, yêu cầu hủy đơn

**Actor: Admin (Quản trị viên)**
- Dashboard thống kê (users, topics, tests, orders, tickets)
- Quản lý users (xem, sửa, xóa)
- Quản lý topics (thêm, sửa)
- Quản lý câu hỏi test (thêm, sửa, xóa)
- Quản lý mã kích hoạt (tạo, xóa)
- Duyệt/từ chối đơn nâng cấp Pro
- Duyệt/từ chối giao dịch ví (nạp/rút)
- Phản hồi ticket hỗ trợ
- Cấu hình OpenAI API key

#### 3.1.2. Use-case Diagram

```
                    ┌──────────────────────────┐
                    │       EngPath System      │
                    │                          │
  Learner ─────────┤  • Đăng ký / Đăng nhập   │
                    │  • Học từ vựng / Bài học │
                    │  • Làm bài test          │
                    │  • Luyện nói AI          │
                    │  • Dashboard / Bảng xếp hạng│
                    │  • Nâng cấp Pro          │
                    │  • Gửi ticket hỗ trợ     │
                    │                          │
  Admin ───────────┤  • Quản lý users / topics │
                    │  • Quản lý câu hỏi       │
                    │  • Duyệt đơn / giao dịch │
                    │  • Phản hồi ticket       │
                    │  • Cấu hình hệ thống     │
                    └──────────────────────────┘
```

### 3.2. Thiết kế hệ thống

#### 3.2.1. Kiến trúc tổng thể

```
public/index.php          ← Front Controller (entry point)
    ↓
app/core/App.php          ← Dispatcher
app/core/Router.php       ← URL Routing & Controller Resolution
    ↓
app/controllers/*.php     ← Controllers (16 files)
    ↓           ↓
app/models/*.php    app/views/*.php
    ↓
database (MySQL)
```

**Cấu trúc thư mục:**
```
DA_TTTN/
├── public/               # Document root
│   ├── index.php         # Front controller
│   ├── css/              # style.css, components.css, pages.css
│   ├── js/               # app.js, dashboard.js, speaking.js
│   └── images/
├── app/
│   ├── config/           # app.php, database.php, aliases.php
│   ├── core/             # App, Router, Controller, Model, Middleware,
│   │                     # Logger, CSRF, Env, Exceptions, RateLimiter
│   ├── controllers/      # 16 controller classes
│   ├── models/           # 9 model classes
│   ├── Services/         # MembershipService (business logic)
│   ├── Helpers/          # Request, Validator (utility)
│   └── views/            # 36 template files (theo controller/action)
├── database/             # schema.sql, migrations
├── tests/                # Unit tests + Feature tests
├── vendor/               # Composer dependencies
└── storage/logs/         # Application logs
```

#### 3.2.2. Sơ đồ lớp (Class Diagram) — Core

```
┌─────────────────────┐
│      Model          │ (Base)
├─────────────────────┤
│ + all(orderBy)      │
│ + find(id)          │
│ + findBy(col, val)  │
│ + where(col, val)   │
│ + create(data)      │
│ + update(id, data)  │
│ + delete(id)        │
│ + count(col?, val?) │
│ + raw(sql, params)  │
└─────────┬───────────┘
          │ extends
    ┌─────┴─────┬──────────────┐
    │ User      │ Topic        │ ...
    ├───────────┼──────────────┤
    │ + register│ + getActive  │
    │ + auth    │ + getWithStats│
    │ + findBy  │ + search     │
    │ + update  │ + ...        │
    └───────────┴──────────────┘

┌─────────────────────┐
│   Controller        │ (Base)
├─────────────────────┤
│ + model(name)       │
│ + view(view, data)  │
│ + json(data, code)  │
│ + redirect(url)     │
│ + setFlash(t, msg)  │
│ + isMethod(method)  │
│ + input(key, def)   │
└──────────┬──────────┘
           │ extends
    ┌──────┴──────────────┐
    │ AuthController      │ ...
    ├─────────────────────┤
    │ + login()           │
    │ + register()        │
    │ + google()          │
    │ + googleCallback()  │
    │ + logout()          │
    └─────────────────────┘
```

#### 3.2.3. Thiết kế cơ sở dữ liệu (ERD)

Sơ đồ quan hệ giữa các bảng chính:

```
users ──┬── membership_orders ──── membership_plans
        │
        ├── test_results ──────── tests ──────── topics
        │       │                                     │
        │   user_answers ─── questions          ┌─────┴─────┐
        │                                    vocabularies  lessons
        ├── speaking_attempts ─── speaking_prompts ──┘
        │
        ├── user_progress ───── topics
        ├── bookmarks ───────── vocabularies
        ├── xp_history
        ├── wallet_transactions
        ├── support_tickets ──── membership_orders
        └── activation_codes ─── membership_plans
```

#### 3.2.4. Sơ đồ Sequence — Đăng nhập

```
Browser          AuthController      User Model       Database
  │                    │                  │               │
  │ POST /auth/login   │                  │               │
  │───────────────────>│                  │               │
  │                    │ authenticate()   │               │
  │                    │─────────────────>│ SELECT * FROM │
  │                    │                  │ users WHERE   │
  │                    │                  │ username = ?  │
  │                    │                  │──────────────>│
  │                    │                  │ <─ user data ─│
  │                    │ <── user|false ──│               │
  │                    │                  │               │
  │                    │ password_verify()│               │
  │                    │ session_regenerate_id()          │
  │                    │ set session data │               │
  │                    │                  │               │
  │ <── redirect home ─│                  │               │
```

#### 3.2.5. Sơ đồ Sequence — Luyện nói AI

```
Browser          SpeakingController   OpenAIService    OpenAI API
  │                    │                   │               │
  │ POST /speaking/    │                   │               │
  │ speak (audio blob) │                   │               │
  │───────────────────>│                   │               │
  │                    │ Web Speech API    │               │
  │                    │ (recognize audio) │               │
  │                    │                   │               │
  │                    │ scoreSpeaking()   │               │
  │                    │──────────────────>│               │
  │                    │                   │ POST /v1/chat │
  │                    │                   │ /completions  │
  │                    │                   │──────────────>│
  │                    │                   │ <─ AI response│
  │                    │ <── scores ───────│               │
  │                    │                   │               │
  │                    │ save to DB        │               │
  │                    │                   │               │
  │ <── JSON scores ───│                   │               │
```

### 3.3. Xây dựng ứng dụng

#### 3.3.1. Xây dựng Core MVC

Xây dựng bộ khung MVC từ đầu với các thành phần:

**Router (`app/core/App.php` + `app/core/Router.php`):**
- Phân tích URL dạng `/controller/method/param1/param2`
- Case-insensitive method matching sử dụng Reflection API
- Chặn các method nội bộ (model, view, json, redirect, etc.)
- Admin restriction: admin chỉ truy cập được AdminController

**Base Controller (`app/core/Controller.php`):**
- `model($name)` — Load model và trả về instance
- `view($view, $data)` — Render view với layout header/footer
- `viewPartial($view, $data)` — Render view không layout (AJAX)
- `json($data, $statusCode)` — JSON response
- `redirect($url)` — HTTP redirect
- `setFlash($type, $message)` — Flash message
- `isMethod($method)`, `input($key)`, `query($key)` — HTTP helpers

**Base Model (`app/core/Model.php`):**
- CRUD: `all()`, `find($id)`, `findBy($col, $val)`, `where()`, `create()`, `update()`, `delete()`
- `count()` — Đếm bản ghi
- `raw($sql, $params)` — Query tùy chỉnh

**Middleware (`app/core/Middleware.php`):**
- `requireLogin()` — Yêu cầu đăng nhập
- `requireAdmin()` — Yêu cầu quyền admin
- `requireStudent()` — Chặn admin dùng learner flow
- `requirePro()` — Yêu cầu tài khoản Pro
- `guest()` — Chỉ cho phép khi chưa đăng nhập
- `user()` — Lấy thông tin user từ session

#### 3.3.2. Xây dựng chức năng Learner

**Đăng ký & Đăng nhập (`AuthController.php`):**
- Đăng ký: validate username, email, password, password_confirm
- Đăng nhập: tìm user bằng username hoặc email, password_verify
- Google OAuth 2.0: redirect → callback → lấy user info → tìm hoặc tạo user
- Session security: `session_regenerate_id(true)` sau login, HttpOnly + SameSite cookie
- Auto-downgrade membership Pro hết hạn khi login

**Học từ vựng (`TopicController.php` + `Vocabulary.php`):**
- Danh sách chủ đề kèm thống kê (số từ vựng, bài học, test)
- Chi tiết chủ đề: tab từ vựng, bài học, test, luyện nói
- Flashcard: thẻ lật từ vựng, đánh dấu đã biết/chưa biết
- Đánh dấu từ đã học (AJAX), nhận XP
- Tìm kiếm: tìm trong topics, vocab, lessons, tests, grammar

**Làm bài test (`TestController.php`):**
- 3 loại test: Quiz (trắc nghiệm), Listening (nghe + chọn), Reading (đọc hiểu)
- Giao diện làm bài: hiển thị từng câu, timer đếm ngược
- Tự động chấm điểm, hiển thị kết quả chi tiết
- Lưu kết quả + câu trả lời vào database

**Luyện nói AI (`SpeakingController.php`):**
- Danh sách câu hỏi theo chủ đề và độ khó
- Giao diện luyện nói: hiển thị sample text, nút record
- Web Speech API để nhận diện giọng nói (trên Chrome)
- Gửi transcript lên OpenAI API để chấm điểm
- Fallback scoring: nếu không có API key, dùng thuật toán so khớp từ
- Lịch sử luyện nói của user

**Chatbot (`ChatbotController.php`):**
- Giao diện chat widget (floating button, cửa sổ chat)
- Gọi OpenAI API với system prompt "English Learning AI Assistant"
- Lưu lịch sử chat (6 tin nhắn gần nhất)
- Gợi ý câu hỏi mẫu

**Dashboard (`DashboardController.php`):**
- Thống kê: XP, streak, level, số từ đã học, số test đã làm
- Biểu đồ tiến độ (Chart.js)
- Lịch sử hoạt động gần đây
- Streak bar, XP progress bar

**Membership Pro (`MembershipController.php`):**
- Hiển thị các gói (1 tháng, 3 tháng, 6 tháng) với giá và quyền lợi
- Thanh toán qua ví điện tử (trừ balance → tạo order → kích hoạt Pro)
- Kích hoạt mã code (nhập mã → validate → kích hoạt Pro)
- Chuyển khoản ngân hàng (hiển thị QR + thông tin TK + nội dung CK)

**Ví điện tử (`WalletController.php`):**
- Xem số dư, lịch sử giao dịch
- Nạp tiền: tạo giao dịch deposit → admin duyệt
- Rút tiền: tạo giao dịch withdraw → admin duyệt

**Hỗ trợ (`SupportController.php`):**
- Tạo ticket (general, cancel_order, bug_report, feedback)
- Hủy đơn: kiểm tra điều kiện (24h đầu hoàn 100%, 7 ngày hoàn 50%)
- Xem danh sách ticket đã gửi

#### 3.3.3. Xây dựng Admin Panel

**Admin Dashboard:**
- Thống kê tổng quan: users, Pro users, topics, tests, questions, attempts
- Biểu đồ: tăng trưởng user 7 ngày, phân bố điểm test, đơn hàng theo tháng, tỷ lệ Free/Pro

**Quản lý Users:**
- Danh sách users, tìm kiếm
- Sửa thông tin (full_name, email, role, membership)
- Xóa user + tất cả dữ liệu liên quan (test_results, answers, progress, orders, etc.)

**Quản lý Topics:**
- Danh sách topics kèm thống kê (số lessons, vocab, tests)
- Thêm/sửa topic (AJAX modal)

**Quản lý Questions:**
- Chọn test → xem danh sách câu hỏi
- Thêm/sửa câu hỏi (4 đáp án A/B/C/D, đáp án đúng, passage)
- Xóa câu hỏi

**Quản lý Activation Codes:**
- Danh sách mã kích hoạt kèm trạng thái (đã dùng/chưa dùng)
- Tạo mã mới (validate format, chống trùng)
- Xóa mã chưa sử dụng

**Quản lý Orders:**
- Danh sách đơn nâng cấp Pro
- Duyệt đơn: tính ngày hết hạn, cập nhật user → Pro, cập nhật order → completed
- Từ chối đơn

**Quản lý Tickets:**
- Danh sách ticket theo trạng thái
- Phản hồi ticket (admin_reply → status resolved)
- Đổi trạng thái ticket
- Duyệt hủy đơn từ ticket: tính refund, hoàn tiền vào ví

**Quản lý Wallet:**
- Danh sách giao dịch (nạp/rút)
- Duyệt giao dịch: cập nhật balance, có FOR UPDATE lock chống race condition
- Từ chối giao dịch

**Cấu hình hệ thống:**
- Lưu/xóa OpenAI API key
- Validate format API key (phải bắt đầu bằng "sk-")

#### 3.3.4. Tích hợp Webhook Ngân hàng

WebhookController nhận callback từ Casso khi có giao dịch chuyển khoản:
- Verify webhook secret (chống unauthorized access)
- Parse nội dung chuyển khoản (format: `EMPRO {userId} GOI{planId}`)
- Validate: kiểm tra plan tồn tại, số tiền khớp (±1000đ), user tồn tại
- Tự động kích hoạt Pro: cập nhật order → completed, cập nhật user → Pro
- Ghi log webhook để debug

### 3.4. Giao diện người dùng

Giao diện EngPath được thiết kế theo phong cách hiện đại, lấy cảm hứng từ Busuu/Duolingo:

**Trang chủ:**
- Hero section với headline lớn, CTA "Learn for free"
- Phone mockup hiển thị preview giao diện học
- Stats: số topics, lessons, vocabulary, tests
- Goal panel: chọn mục tiêu học (Communication, Vocabulary, Grammar, Speaking)
- Feature cards: lộ trình học, luyện nói AI
- Course grid: danh sách chủ đề với level, icon, stats
- Final CTA: "Build your English habit with EngPath"

**Trang khóa học:**
- Filter bar: All / Beginner / Intermediate / Advanced
- Grid card: mỗi topic có tên, mô tả, level badge, số lượng vocab/lessons/tests

**Trang chi tiết khóa học:**
- Tab navigation: Từ vựng / Bài học / Bài test / Luyện nói
- Progress bar hiển thị tiến độ học

**Trang luyện nói:**
- Voice selector (giọng đọc)
- Text display panel + Recording panel
- Score rings (accuracy, fluency, pronunciation)
- AI feedback

**Admin Panel:**
- Dark header với navigation
- Stats grid, charts, data tables
- Modal form cho thêm/sửa

### 3.5. Kiểm thử

**Unit Testing (PHPUnit):**
- 19 test cases cho MembershipService, Request, Validator
- Test coverage: business logic, validation rules, HTTP helpers

**Static Analysis (PHPStan level 5):**
- 0 errors, 90 PHP files analyzed
- Phát hiện sớm type errors, undefined methods, unused code

**Code Style (PHP-CS-Fixer PSR-12):**
- Toàn bộ codebase được format tự động
- Pre-commit hook kiểm tra syntax + code style + PHPStan + PHPUnit

---

## Chương 4. KẾT LUẬN VÀ KIẾN NGHỊ

### 4.1. Kết quả đạt được

**Tính năng đã hoàn thành:**
- Hệ thống MVC hoàn chỉnh với 16 controllers, 9 models, 36 views
- Đầy đủ chức năng cho learner: đăng ký/đăng nhập (email + Google OAuth), học từ vựng, đọc bài học, làm test, luyện nói AI, chatbot, dashboard, bảng xếp hạng, quản lý hồ sơ
- Hệ thống membership: gói Free/Pro, thanh toán qua ví, chuyển khoản ngân hàng, mã kích hoạt
- Ví điện tử: nạp/rút tiền, lịch sử giao dịch
- Hệ thống ticket hỗ trợ + chính sách hủy đơn
- Admin panel với 9 phân hệ quản lý
- Webhook tự động xác nhận thanh toán từ ngân hàng
- Tích hợp OpenAI API cho Speaking + Chatbot
- Gamification: XP, streak, level, badge, bảng xếp hạng

**Chất lượng code:**
- 90 PHP files, ~5,000 dòng code PHP
- PHPStan level 5: 0 errors
- PHPUnit: 19 tests / 24 assertions
- PSR-12 code style
- Composer autoload (classmap + PSR-4)
- Monolog logging, CSRF protection, Rate limiting

**Bảo mật:**
- Prepared Statements chống SQL Injection
- Session security (HttpOnly, SameSite, Secure)
- CSRF token cho form POST
- Rate limiting cho login
- Input validation tập trung
- SSL verification cho API calls
- Method blocking trong router

### 4.2. Hạn chế

- Chưa có hệ thống quên mật khẩu / reset password qua email
- Web Speech API chỉ hoạt động tốt trên Chrome
- Chưa có real-time notification
- Chưa có upload ảnh/audio cho topic và bài học
- Chưa hỗ trợ đa ngôn ngữ (chỉ tiếng Việt)
- Chưa có bộ test đầy đủ cho tất cả controller
- Chưa có CI/CD pipeline tự động

### 4.3. Hướng phát triển

- **Quên mật khẩu:** Gửi email reset password
- **Real-time Notification:** Thông báo khi admin duyệt đơn, phản hồi ticket
- **Mobile App:** Phát triển ứng dụng di động sử dụng API
- **Đa ngôn ngữ:** Hỗ trợ giao diện tiếng Anh
- **Test Coverage:** Tăng coverage lên 70%+
- **CI/CD:** GitHub Actions cho test tự động
- **Docker:** Containerize ứng dụng để dễ deploy
- **Admin Logging:** Ghi log mọi hành động của admin
- **Nâng cấp AI:** Sử dụng GPT-4 hoặc model mới hơn
- **Hỗ trợ nhiều ngân hàng:** Mở rộng webhook cho nhiều ngân hàng

---

## TÀI LIỆU THAM KHẢO

1. PHP Manual — https://www.php.net/manual/
2. PDO Documentation — https://www.php.net/manual/en/book.pdo.php
3. MySQL Reference Manual — https://dev.mysql.com/doc/refman/8.0/
4. OpenAI API Documentation — https://platform.openai.com/docs/
5. Google OAuth 2.0 — https://developers.google.com/identity/protocols/oauth2
6. Chart.js Documentation — https://www.chartjs.org/docs/
7. MDN Web Docs (HTML/CSS/JavaScript) — https://developer.mozilla.org/
8. PHPUnit Manual — https://docs.phpunit.de/
9. PHPStan Documentation — https://phpstan.org/
10. PHP-FIG PSR-12 — https://www.php-fig.org/psr/psr-12/
11. Monolog Documentation — https://github.com/Seldaek/monolog
12. Font Awesome Icons — https://fontawesome.com/
13. Web Speech API — https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API
14. Casso API — https://casso.vn/developers
