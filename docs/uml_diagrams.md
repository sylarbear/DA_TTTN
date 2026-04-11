# Phân tích hệ thống EnglishMaster — UML Diagrams

---

## 1. Use Case Diagram

```mermaid
graph TB
    subgraph Hệ thống EnglishMaster
        UC1["Đăng ký / Đăng nhập"]
        UC2["Đăng nhập Google OAuth"]
        UC3["Xem chủ đề từ vựng"]
        UC4["Học bài (Lesson)"]
        UC5["Học Flashcard"]
        UC6["Làm bài test Quiz"]
        UC7["Làm bài Listening"]
        UC8["Làm bài Reading"]
        UC9["Luyện nói Speaking"]
        UC10["Học Ngữ pháp"]
        UC11["Đánh giá bài học"]
        UC12["Bookmark từ vựng"]
        UC13["Tìm kiếm từ vựng"]
        UC14["Xem Dashboard tiến độ"]
        UC15["Xem Leaderboard"]
        UC16["Nâng cấp Pro"]
        UC17["Nạp tiền vào ví"]
        UC18["Mua gói bằng ví"]
        UC19["Rút tiền từ ví"]
        UC20["Kích hoạt mã"]
        UC21["Gửi ticket hỗ trợ"]
        UC22["Yêu cầu hủy đơn"]
        UC23["Quản lý Users"]
        UC24["Quản lý Chủ đề"]
        UC25["Quản lý Câu hỏi"]
        UC26["Quản lý Mã kích hoạt"]
        UC27["Duyệt đơn nâng cấp"]
        UC28["Duyệt giao dịch ví"]
        UC29["Quản lý Tickets"]
        UC30["Duyệt hủy đơn + Hoàn tiền"]
        UC31["Cài đặt hệ thống"]
    end

    Guest(("👤 Khách"))
    User(("👨‍🎓 User"))
    Pro(("⭐ Pro User"))
    Admin(("🔧 Admin"))

    Guest --> UC1
    Guest --> UC2
    Guest --> UC3
    
    User --> UC3
    User --> UC4
    User --> UC5
    User --> UC6
    User --> UC10
    User --> UC11
    User --> UC12
    User --> UC13
    User --> UC14
    User --> UC15
    User --> UC16
    User --> UC17
    User --> UC18
    User --> UC19
    User --> UC20
    User --> UC21
    User --> UC22

    Pro --> UC7
    Pro --> UC8
    Pro --> UC9
    
    Admin --> UC23
    Admin --> UC24
    Admin --> UC25
    Admin --> UC26
    Admin --> UC27
    Admin --> UC28
    Admin --> UC29
    Admin --> UC30
    Admin --> UC31
```

> [!NOTE]
> **Actors:** Guest (chưa đăng nhập), User (đã đăng nhập - Free), Pro User (hội viên Pro), Admin (quản trị viên).
> Pro User kế thừa tất cả quyền của User. Admin kế thừa tất cả quyền của Pro User.

---

## 2. Class Diagram

```mermaid
classDiagram
    class User {
        +int id
        +string username
        +string email
        +string password_hash
        +string full_name
        +string avatar
        +enum role [student, admin]
        +enum membership [free, pro]
        +datetime membership_expired_at
        +decimal balance
        +int total_xp
        +int level
        +int current_streak
        +register()
        +authenticate()
        +updateProfile()
        +changePassword()
        +findOrCreateByGoogle()
    }

    class Topic {
        +int id
        +string name
        +string slug
        +string description
        +string thumbnail
        +enum level
        +int sort_order
        +getActive()
        +search()
        +getAllWithStats()
    }

    class Vocabulary {
        +int id
        +int topic_id
        +string word
        +string pronunciation
        +string meaning_vi
        +string example_sentence
        +string audio_url
        +getByTopic()
        +search()
    }

    class Lesson {
        +int id
        +int topic_id
        +string title
        +string description
        +int sort_order
        +getByTopic()
        +getWithContents()
    }

    class LessonContent {
        +int id
        +int lesson_id
        +enum content_type [text, image, audio, video]
        +text content
        +int sort_order
    }

    class LessonReview {
        +int id
        +int user_id
        +int lesson_id
        +int rating
        +text comment
    }

    class Test {
        +int id
        +int topic_id
        +string title
        +enum test_type [quiz, listening, reading]
        +int duration_minutes
        +int pass_score
        +getByTopic()
        +getWithQuestions()
    }

    class Question {
        +int id
        +int test_id
        +text question_text
        +enum question_type
        +json options_json
        +string correct_answer
        +string audio_url
        +text passage
    }

    class TestResult {
        +int id
        +int user_id
        +int test_id
        +int score
        +int total_points
        +int time_spent
    }

    class UserProgress {
        +int id
        +int user_id
        +int topic_id
        +int vocab_learned
        +int lessons_completed
        +int tests_passed
        +int speaking_practiced
        +getOrCreate()
        +increment()
    }

    class MembershipPlan {
        +int id
        +string name
        +int duration_months
        +decimal price
        +string features
        +bool is_popular
    }

    class MembershipOrder {
        +int id
        +int user_id
        +int plan_id
        +string activation_code
        +decimal amount
        +string payment_method
        +enum status [pending, completed, cancelled]
        +datetime expired_at
    }

    class WalletTransaction {
        +int id
        +int user_id
        +enum type [deposit, purchase, refund, withdraw]
        +decimal amount
        +decimal balance_after
        +enum status [pending, completed, rejected]
        +string bank_name
        +string bank_account
    }

    class SupportTicket {
        +int id
        +int user_id
        +enum type [general, cancel_order, bug_report, feedback]
        +int related_order_id
        +string subject
        +text message
        +enum status [open, in_progress, resolved, closed]
        +text admin_reply
    }

    class GrammarLesson {
        +int id
        +string title
        +enum category
        +enum level
        +text content_html
    }

    class GrammarQuestion {
        +int id
        +int grammar_lesson_id
        +string question_text
        +json options
        +char correct_answer
    }

    class SpeakingPrompt {
        +int id
        +int topic_id
        +text prompt_text
        +text sample_answer
        +enum difficulty
    }

    class SpeakingAttempt {
        +int id
        +int user_id
        +int prompt_id
        +text transcript
        +int overall_score
        +text feedback
        +scoreSpeaking()
    }

    class Bookmark {
        +int id
        +int user_id
        +int vocabulary_id
        +text note
    }

    Topic "1" --> "*" Vocabulary
    Topic "1" --> "*" Lesson
    Topic "1" --> "*" Test
    Topic "1" --> "*" SpeakingPrompt
    Lesson "1" --> "*" LessonContent
    Lesson "1" --> "*" LessonReview
    Test "1" --> "*" Question
    GrammarLesson "1" --> "*" GrammarQuestion
    User "1" --> "*" TestResult
    User "1" --> "*" UserProgress
    User "1" --> "*" MembershipOrder
    User "1" --> "*" WalletTransaction
    User "1" --> "*" SupportTicket
    User "1" --> "*" SpeakingAttempt
    User "1" --> "*" Bookmark
    User "1" --> "*" LessonReview
    MembershipPlan "1" --> "*" MembershipOrder
    SpeakingPrompt "1" --> "*" SpeakingAttempt
    Vocabulary "1" --> "*" Bookmark
    Test "1" --> "*" TestResult
    MembershipOrder "1" --> "0..1" SupportTicket
```

---

## 3. Sequence Diagrams

### 3.1. Đăng nhập

```mermaid
sequenceDiagram
    actor U as User
    participant V as Login Page
    participant C as AuthController
    participant M as User Model
    participant DB as Database
    participant S as Session

    U->>V: Nhập username + password
    V->>C: POST /auth/login
    C->>M: authenticate(username, password)
    M->>DB: SELECT * FROM users WHERE username = ?
    DB-->>M: User data
    M->>M: password_verify(password, hash)
    alt Đăng nhập thành công
        M-->>C: User object
        C->>S: Lưu session (user_id, role, membership)
        C-->>V: Redirect → Trang chủ
    else Sai mật khẩu
        M-->>C: null
        C-->>V: Flash error "Sai thông tin"
    end
```

### 3.2. Nạp tiền vào ví

```mermaid
sequenceDiagram
    actor U as User
    participant V as Deposit Page
    participant W as WalletController
    participant DB as Database
    actor A as Admin
    participant AV as Admin Wallet Page
    participant AC as AdminController

    U->>V: Nhập số tiền (50,000đ)
    V->>V: Hiển thị QR VietQR + thông tin CK
    U->>U: Chuyển khoản qua app ngân hàng
    U->>V: Click "Đã chuyển khoản xong"
    V->>W: POST /wallet/createDeposit {amount, transfer_note}
    W->>W: Validate (min 10K, max 10M, no pending)
    W->>DB: INSERT wallet_transactions (type=deposit, status=pending)
    W-->>V: "Yêu cầu đã gửi, chờ Admin duyệt"

    Note over A,AC: Admin xác nhận chuyển khoản
    A->>AV: Xem danh sách giao dịch pending
    AV->>AC: POST /admin/approveTransaction {id}
    AC->>DB: BEGIN TRANSACTION
    AC->>DB: SELECT balance FROM users FOR UPDATE
    AC->>DB: UPDATE users SET balance += amount
    AC->>DB: UPDATE wallet_transactions SET status=completed
    AC->>DB: COMMIT
    AC-->>AV: "Duyệt thành công"
```

### 3.3. Mua gói Pro bằng ví

```mermaid
sequenceDiagram
    actor U as User
    participant V as Membership Page
    participant MC as MembershipController
    participant DB as Database

    U->>V: Click "Chọn gói này" (Pro 1 Tháng - 50,000đ)
    V->>V: Hiện modal: balance, giá, nút thanh toán
    U->>V: Click "Thanh toán bằng ví"
    V->>MC: POST /membership/createOrder {plan_id}

    MC->>DB: SELECT plan info
    MC->>DB: BEGIN TRANSACTION
    MC->>DB: SELECT balance FROM users FOR UPDATE (lock row)
    
    alt Đủ tiền
        MC->>DB: UPDATE users SET balance -= price, membership = pro
        MC->>DB: INSERT membership_orders (status=completed)
        MC->>DB: INSERT wallet_transactions (type=purchase)
        MC->>DB: COMMIT
        MC-->>V: {success: true, message: "Mua thành công!"}
        V->>V: Hiện thông báo thành công + nút reload
    else Không đủ tiền
        MC->>DB: ROLLBACK
        MC-->>V: {error: "Số dư không đủ", need_deposit: true}
        V->>V: Hiện link "Nạp tiền ngay"
    end
```

### 3.4. Hủy đơn + Hoàn tiền

```mermaid
sequenceDiagram
    actor U as User
    participant V as Support Page
    participant SC as SupportController
    participant DB as Database
    actor A as Admin
    participant AC as AdminController

    U->>V: Chọn "Hủy đơn nâng cấp" + chọn đơn pending
    V->>SC: POST /support/store {type=cancel_order, order_id}
    SC->>SC: checkCancelEligibility(order, plan)
    
    alt Trong 24h → 100% refund
        SC->>DB: INSERT support_tickets (type=cancel_order)
        SC-->>V: "Đã gửi yêu cầu hủy"
    else Sau 7 ngày → 0%
        SC-->>V: "Đã quá thời hạn hủy đơn"
    end

    Note over A,AC: Admin duyệt hủy đơn
    A->>AC: POST /admin/approveCancelOrder {ticket_id, order_id}
    AC->>DB: BEGIN TRANSACTION
    AC->>AC: Tính refundAmount = amount × refund%
    AC->>DB: UPDATE membership_orders SET status=cancelled
    AC->>DB: SELECT balance FOR UPDATE
    AC->>DB: UPDATE users SET balance += refundAmount
    AC->>DB: INSERT wallet_transactions (type=refund)
    AC->>DB: UPDATE support_tickets SET status=resolved
    AC->>DB: COMMIT
    AC-->>A: "Đã hủy đơn, hoàn X đ vào ví"
```

---

## 4. State Diagrams

### 4.1. Trạng thái Đơn hàng (Membership Order)

```mermaid
stateDiagram-v2
    [*] --> Pending: User chuyển khoản
    [*] --> Completed: User mua bằng ví / mã kích hoạt

    Pending --> Completed: Admin duyệt
    Pending --> Cancelled: Admin từ chối
    Pending --> Cancelled: User hủy đơn (Admin duyệt)

    Completed --> [*]
    Cancelled --> [*]

    note right of Pending: Đơn chờ xác nhận
    note right of Completed: Đã kích hoạt Pro
    note right of Cancelled: Đã hủy + hoàn tiền (nếu có)
```

### 4.2. Trạng thái Giao dịch Ví (Wallet Transaction)

```mermaid
stateDiagram-v2
    [*] --> Pending: User tạo yêu cầu nạp/rút
    [*] --> Completed: Hệ thống tự động ghi (purchase/refund)

    Pending --> Completed: Admin duyệt
    Pending --> Rejected: Admin từ chối

    Completed --> [*]
    Rejected --> [*]

    note right of Pending: Chờ Admin xác nhận
    note right of Completed: Đã xử lý + cập nhật balance
    note right of Rejected: Từ chối + ghi lý do
```

### 4.3. Trạng thái Support Ticket

```mermaid
stateDiagram-v2
    [*] --> Open: User gửi ticket

    Open --> InProgress: Admin đang xử lý
    Open --> Resolved: Admin phản hồi
    
    InProgress --> Resolved: Admin phản hồi / duyệt hủy đơn
    
    Resolved --> Closed: Admin đóng
    Resolved --> Open: User phản hồi lại

    Closed --> [*]

    note right of Open: Mới gửi, chờ xử lý
    note right of Resolved: Đã giải quyết
```

### 4.4. Trạng thái User Membership

```mermaid
stateDiagram-v2
    [*] --> Free: Đăng ký tài khoản

    Free --> Pro: Mua gói / Kích hoạt mã
    
    Pro --> Free: Hết hạn membership
    Pro --> Pro: Gia hạn thêm gói

    note right of Free: Giới hạn tính năng
    note right of Pro: Mở khóa toàn bộ (Speaking, Listening, Reading)
```
