# BlogCMS – Admin Dashboard & Backend

## Project Overview
BlogCMS is a simple Content Management System that allows users to manage a blog on a daily basis.  
This project focuses on building the **backend** and an **admin dashboard** where content can be created, read, updated, and deleted securely.

The application is developed using **PHP 8 (procedural)** and a **relational database**.

---

## Features

### For All Users
- Secure login page
- Role-based access system:
  - Admin
  - Author
  - User

---

### For Administrators
- Admin dashboard with basic statistics
- Create, read, update, and delete categories
- Moderate comments
- Manage users (view, edit, delete)

---

### For Authors
- View published articles
- Create new articles
- Edit their own articles
- Delete their own articles
- Post comments

---

### For Visitors
- View published articles
- Post comments

---

### Bonus Features
- Image upload
- Article search
- Pagination for lists

---

## Technologies Used

### Backend
- PHP 8 (procedural programming)
- MySQL or PostgreSQL
- PDO with prepared statements

### Frontend
- HTML5 / CSS3
- Tailwind CSS or Bootstrap
- Basic JavaScript

### Security
- Secure PHP sessions
- Password hashing using bcrypt
- XSS protection with `htmlspecialchars`
- Server-side form validation

---

## Project Structure
- Procedural PHP files
- Separate files for:
  - Database connection
  - Authentication
  - CRUD operations
- Clean and readable code structure

---
