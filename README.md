# Employee Attendance Management System

A web-based system for managing employee attendance with role-based access, check-in/out tracking, group scheduling, and document uploads.

## 🔧 Tech Stack

- **Frontend**: HTML, CSS, JavaScript, jQuery
- **Backend**: PHP
- **Database**: MySQL

## 📁 File Structure

```
project/
├── config/
│   └── db.php
├── controllers/
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── AttendanceController.php
│   ├── FieldWorkerController.php
│   └── DocumentController.php
├── middleware/
│   └── AuthMiddleware.php
├── models/
│   ├── User.php
│   ├── FieldWorker.php
│   ├── Attendance.php
│   ├── Announcement.php
│   ├── Department.php
│   └── Document.php
├── services/
│   ├──Groupservice.php
│   └── UploadService.php
├── index.php
├── login.php
├── logout.php
├── dashboard.php
└── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
│
└── README.md
```

## ✅ Features

- Admin and field worker login
- Check-in and check-out functionality
- Group attendance logic by scheduled days
- Document upload (certificates/registration)
- Department and group assignment
- IP/Location control (mandatory)

## 🚀 Setup Instructions

1. **Clone the repo**
   ```bash
   git clone https://github.com/your-username/attendance-system.git
   cd attendance-system
   ```

2. **Create a database**
   - Import the provided `attendance_db.sql` file into MySQL.
   - Update `config/db.php` with your DB credentials.

3. **Run locally**
   - Place the project in your web root (e.g., `htdocs` for XAMPP or `/var/www/html` for Apache).
   - Access via browser: `http://localhost/attendance-system/index.php`

## 🧠 Group Logic

- Each group has a start date and assigned days (e.g., Mon, Wed, Fri).
- The system checks the current day against group schedule.
- If today isn’t a valid group day, check-in is blocked.

## 📄 Document Upload Rules

- Allowed types: `certificate`, `registration`
- Status: `uploaded` / `missing`
- Extra fields: `purpose`, `comment`

## 🔒 IP/Location Tracking

- IP and timestamp are logged during each check-in/check-out.
- Location matching is enforced by comparing against assigned base station IP.

## 🙋 Common Issues

- **Login fails**: Check password is hashed in DB using `password_hash`.
- **Check-in blocked**: Group might not be scheduled today or not assigned.

## 📫 Contact

For issues, email `isaacshaban54@gmail.com` or open an issue in the repo.
