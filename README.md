# SVM School Management System

A modular School Management System built with **PHP, MySQL/MariaDB, HTML, CSS, JavaScript, and Tailwind CSS**.

The project is designed around four primary roles:

- **Admin** — system and user management
- **Teacher** — attendance, academic records, marks, study materials, and notices
- **Student** — attendance, results, fees, and learning materials
- **Parent** — child progress, attendance, fees, and notifications

## Features

### Admin
- Admin authentication
- Dashboard
- Teacher registration
- Student registration
- Parent registration
- Attendance overview
- Fees management
- Results management
- Notifications
- Notification history

### Teacher
- Teacher dashboard
- Student list
- Attendance marking
- Attendance viewing
- Results / marks entry
- Study material management
- Notices

### Student
- Student dashboard
- Attendance
- Results
- Study materials

### Parent
- Parent dashboard
- Child attendance
- Child materials
- Fees
- Notifications

## Project Structure

```text
SMS/
├── admin/
├── assets/
├── auth/
├── includes/
├── parent/
├── student/
├── teacher/
├── archive/          # local archive; excluded from Git
├── index.php
└── .gitignore
```

## Tech Stack

- PHP
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- Tailwind CSS
- XAMPP / Apache

## Local Setup

1. Install **XAMPP**.
2. Start **Apache** and **MySQL**.
3. Copy the project into:

```text
C:\xampp\htdocs\SMS
```

4. Create the project database in phpMyAdmin.
5. Configure the local database connection in the project's local configuration.
6. Open:

```text
http://localhost/SMS/
```

> Never commit real database passwords, API keys, payment secrets, or other credentials to this public repository.

## Portfolio Demo

A polished screen-recording showcase of the SMS project is included in the repository:

**[🎥 Watch the SMS Portfolio Showcase](assets/video/SMS_Portfolio_Showcase.mp4)**

The video demonstrates the main UI and workflows across the Admin, Teacher, Student, and Parent modules.

## Documentation

Project documentation includes:

- ER Diagram
- Use Case Diagram
- Activity Diagram
- Data Flow Diagram
- Data Dictionary
- Database design

## Security

The application is intended to use:

- Password hashing with `password_hash()`
- Password verification with `password_verify()`
- Prepared SQL statements
- Role-based access control
- Session-based authentication
- CSRF protection for state-changing forms
- Server-side authorization for student/parent data

## Development

This project is currently developed and tested locally with XAMPP. Configuration and database credentials should remain local and must not be committed.

## Portfolio

This repository is part of my full-stack development portfolio and demonstrates PHP/MySQL application architecture, role-based authentication, CRUD workflows, attendance, fees, academic records, notifications, and responsive UI.
