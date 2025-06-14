# Hệ thống Quản lý Sinh viên

## Giới thiệu

Hệ thống giúp quản lý toàn diện thông tin sinh viên, môn học, điểm số và điểm danh. Được xây dựng theo mô hình **MVC của Laravel**, dễ mở rộng và bảo trì.

---

## Các chức năng chính

| Module    | Chức năng                                                                 |
|-----------|-------------------------------------------------------------------------- |
| **Sinh viên**  | - Thêm, sửa, xóa sinh viên<br>- Tìm kiếm sinh viên theo tên, mã, email |
| **Môn học**    | - Thêm, sửa, xóa môn học                                              |
| **Điểm số**    | - Nhập, sửa điểm sinh viên theo từng môn<br>- Tính điểm tổng kết     |
| **Điểm danh**  | - Ghi nhận số buổi vắng chi tiết theo từng buổi học                  |

---

## Mô hình cơ sở dữ liệu

### `students`

| Cột     | Kiểu dữ liệu  | Ghi chú              |
|---------|---------------|----------------------|
| id      | BIGINT        | Khóa chính           |
| code    | VARCHAR(10)   | Mã sinh viên, duy nhất|
| name    | VARCHAR       | Họ tên sinh viên     |
| email   | VARCHAR       | Email, duy nhất      |
| gender  | VARCHAR       | Giới tính            |
| dob     | DATE          | Ngày sinh            |

### `subjects`

| Cột     | Kiểu dữ liệu | Ghi chú               |
|---------|--------------|-----------------------|
| id      | BIGINT       | Khóa chính            |
| code    | VARCHAR      | Mã môn học, duy nhất  |
| name    | VARCHAR      | Tên môn học           |
| credit  | INT          | Số tín chỉ (thêm mới) |
| total_sessions | INT   | Tổng số buổi học      |

### `scores`

| Cột       | Kiểu dữ liệu  | Ghi chú                                |
|-----------|----------------|----------------------------------------|
| id        | BIGINT         | Khóa chính                            |
| student_id| FOREIGN (BIGINT)| Liên kết đến `students`               |
| subject_id| FOREIGN (BIGINT)| Liên kết đến `subjects`               |
| cc1       | FLOAT          | Chuyên cần từ điểm danh               |
| cc2       | FLOAT          | Chuyên cần nhập tay                   |
| midterm   | FLOAT          | Giữa kỳ                               |
| final     | FLOAT          | Cuối kỳ                               |
| score     | FLOAT          | Điểm tổng kết (calculated)            |

### `attendances`

| Cột             | Kiểu dữ liệu     | Ghi chú                                  |
|-----------------|------------------|------------------------------------------|
| id              | BIGINT           | Khóa chính                               |
| student_id      | FOREIGN (BIGINT) | FK đến `students`                        |
| subject_id      | FOREIGN (BIGINT) | FK đến `subjects`                        |
| absent_sessions | INT              | Số buổi vắng (tính từ `session_details`)|
| session_details | JSON             | Lưu mảng trạng thái điểm danh (true/false)|

---

## Logic Tính Điểm & Học Lại

- **cc1 = max(0, 10 - số buổi vắng * 3)**
- Nếu `số buổi vắng > 3` → Học lại môn
- **Điểm tổng kết =** `cc1 * 0.05 + cc2 * 0.05 + midterm * 0.3 + final * 0.6`

---
### sơ đồ class diagram 
![Screenshot 2025-06-14 100605](https://github.com/user-attachments/assets/01d31ebc-c65a-4b4c-9608-af2bd98b7f33)
### sơ đồ activity diagram
![Screenshot 2025-06-14 102656](https://github.com/user-attachments/assets/9b6c5f32-6b2e-4b8f-ab1c-0098438645de)

### demo
### chức năng quản lý sinh viên 
### danh sách sinh viên
![Screenshot 2025-06-14 111444](https://github.com/user-attachments/assets/9d3fdf3a-df96-4643-b28d-67e850d4b1b0)
### thêm sinh viên 
![Screenshot 2025-06-14 111602](https://github.com/user-attachments/assets/44fa0355-e131-4f03-997e-30381bc6ef58)
### chức năng môn học 
### danh sách môn học 
![Screenshot 2025-06-14 111658](https://github.com/user-attachments/assets/97140359-4312-4413-9cb3-4dbbe0056698)
### danh sách diểm của sinh viên
![Screenshot 2025-06-14 111757](https://github.com/user-attachments/assets/3b1f30a0-a804-463e-af33-d06987335987)
### danh sách điểm danh của sinh viên
![Screenshot 2025-06-14 111835](https://github.com/user-attachments/assets/51d08011-c544-42c1-84f6-1576ccda5563)
### danh sách điểm của tất cả sinh viên 
![Screenshot 2025-06-14 111922](https://github.com/user-attachments/assets/e5e34968-ee93-4462-9084-4aff2b12561a)
### danh sách điểm danh của tất cả sinh viên 
![Screenshot 2025-06-14 111954](https://github.com/user-attachments/assets/c1d7269a-acc0-4d03-93e8-5056b32ec4e7)
### Code 
Model student 
![Screenshot 2025-06-14 112120](https://github.com/user-attachments/assets/2f005515-222c-495e-b9ac-df05f90f19b7)
Model score 
![Screenshot 2025-06-14 112156](https://github.com/user-attachments/assets/1c8852bb-26aa-464e-85e7-7132acfcafb1)
Model subject 
![Screenshot 2025-06-14 112239](https://github.com/user-attachments/assets/7b3841e4-8a38-41ed-890a-b7ed18483ec1)
Model attendance 
![Screenshot 2025-06-14 112317](https://github.com/user-attachments/assets/7bef0a47-f7f1-47ba-b825-e7514ed6a2d8)
### Controller
StudentController 
![Screenshot 2025-06-14 112427](https://github.com/user-attachments/assets/e930f3bc-5665-4e08-8842-fd5ba2bbbde7)
ScoreController 
![Screenshot 2025-06-14 112516](https://github.com/user-attachments/assets/315fcfa9-04e2-43a8-aef2-964a6975150b)
### View
student 
![Screenshot 2025-06-14 112614](https://github.com/user-attachments/assets/8707a1ae-1ff7-40b9-bd05-5afc428d9c22)
score 
![image](https://github.com/user-attachments/assets/c00eeec2-9789-4512-9379-0116e6795f05)

## 🔌 API Endpoints

Tất cả API trả về JSON.

### SubjectApiController

| Phương thức | Endpoint           | Mô tả                    |
|-------------|--------------------|---------------------------|
| GET         | /api/subjects      | Lấy danh sách môn học    |
| POST        | /api/subjects      | Tạo mới môn học          |
| PUT         | /api/subjects/{id} | Cập nhật môn học         |
| DELETE      | /api/subjects/{id} | Xóa môn học              |

---

### StudentApiController

| Phương thức | Endpoint           | Mô tả                                          |
|-------------|--------------------|------------------------------------------------|
| GET         | /api/students      | Danh sách sinh viên (search, sort)            |
| POST        | /api/students      | Thêm sinh viên mới                            |
| PUT         | /api/students/{id} | Cập nhật thông tin sinh viên                  |
| DELETE      | /api/students/{id} | Xóa sinh viên                                  |

---

### ScoreApiController

| Phương thức | Endpoint                            | Mô tả                      |
|-------------|--------------------------------------|-----------------------------|
| GET         | /students/{id}/scores               | Danh sách điểm theo sinh viên |
| POST        | /students/{id}/scores               | Gán điểm                    |
| PUT         | /scores/{id}                        | Cập nhật điểm              |
| DELETE      | /scores/{id}                        | Xóa điểm                   |

---

### AttendanceApiController

| Phương thức | Endpoint                               | Mô tả                            |
|-------------|-----------------------------------------|-----------------------------------|
| GET         | /students/{id}/attendances             | Xem điểm danh                    |
| POST        | /students/{id}/attendances             | Ghi nhận điểm danh               |
| PUT         | /attendances/{id}                      | Cập nhật số buổi vắng            |
| DELETE      | /attendances/{id}                      | Xóa ghi nhận                     |

---

## Xác thực & Phân quyền

- **Admin**: Có quyền truy cập và chỉnh sửa toàn bộ dữ liệu.
- **Người dùng thông thường**: Truy cập dashboard và profile cá nhân.

---

## Cấu trúc Route chính

### `web.php` (giao diện web)

- `/students`, `/subjects`, `/students/{id}/scores`, `/students/{id}/attendances`, ...
- `/dashboard`, `/profile`, ...

### `api.php` (RESTful API)

- `/api/students`, `/api/subjects`, `/api/scores`, `/api/attendances`, ...

---

## Ghi chú

- **Sử dụng CSDL `mysql_aiven`** cho toàn bộ các model.
- Dữ liệu điểm danh chi tiết lưu trong `JSON` để theo dõi từng buổi học.
- Hệ thống hỗ trợ cả **giao diện web** và **API** phục vụ frontend/mobile.
