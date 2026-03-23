# Hostel Management System

A fully functional web-based Hostel Management System built with PHP and MySQL. This project was developed as a personal portfolio project by a third year Computer Science student.

---

## Screenshots

**Home Page**
![Home Page](/1.png)

**Login Page**
![Login Page](/2.png)

**Admin Dashboard**
![Admin Dashboard](/4.png)

**Student Dashboard**
![Student Dashboard](/3.png)

---

## Tech Stack

**Frontend:**
- HTML5
- CSS3
- JavaScript
- Bootstrap

**Backend:**
- PHP
- MySQL

---

## Features

**Admin Panel:**
- Secure Admin Login
- Student Management (Add, Edit, Delete)
- Room Management (Add, Edit, Delete)
- Fee Management (Add, View, Track)
- Mess Payment Tracking
- Complaints Management
- Notifications System
- Reports and Dashboard
- Search Functionality

**Student Panel:**
- Secure Student Login
- View Room Details
- View Fee Status
- Upload Payment Receipt
- Submit Complaints
- View Notifications
- Update Profile
- Change Password

---

## Installation Guide

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP / WAMP / Any PHP Server

### Steps

1. Clone the repository
```bash
git clone https://github.com/VishwasSuthar04/hostel-management-system.git
```

2. Move project to server folder
```bash
# For XAMPP
mv hostel-management-system /xampp/htdocs/
```

3. Import the database
- Open phpMyAdmin
- Create a new database named `hostel_management`
- Import `database/hostel.sql` file

4. Configure database connection
- Open `includes/config.php`
- Update the database credentials
```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hostel_management');
```

5. Run the project
```
http://localhost/hostel-management-system/
```

---

## Login Credentials

**Admin Login:**
- Username: admin
- Password: admin123

**Student Login:**
- Registered student credentials

---

## Project Structure
```
hostel-management-system/
├── admin/
│   ├── index.php
│   ├── students.php
│   ├── rooms.php
│   ├── fees.php
│   ├── complaints.php
│   ├── reports.php
│   └── ...
├── student/
│   ├── index.php
│   ├── fees.php
│   ├── complaint.php
│   ├── profile.php
│   └── ...
├── includes/
│   ├── config.php
│   ├── auth.php
│   └── functions.php
├── database/
│   └── hostel.sql
├── screenshots/
│   ├── 1.png
│   ├── 2.png
│   ├── 3.png
│   └── 4.png
├── index.php
└── login.php
```

---

## What I Learned

- Backend development with PHP
- Database design and management with MySQL
- Frontend development with HTML, CSS, JavaScript and Bootstrap
- Secure login system with sessions
- Real world project development and deployment

---

## Author

**Vishwas Suthar**
- Third Year Computer Science Student
- GitHub: [VishwasSuthar04](https://github.com/VishwasSuthar04)
- LinkedIn: [vishwassutharuiux](https://www.linkedin.com/in/vishwassutharuiux)

---

## License

This project is open source and available under the [MIT License](LICENSE).
