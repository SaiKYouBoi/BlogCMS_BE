

# **BlogCMS – Backend & Admin Dashboard**

*A procedural PHP Content Management System for managing blog articles, users, comments, and categories.*

---

## **Overview**

**BlogCMS** is a lightweight & secure Content Management System built with **PHP procédural**, **MySQL** and **PDO**.
It includes a full **Admin Dashboard**, a **role-based system**, and complete **CRUD functionalities** for managing:

* Articles
* Categories
* Users
* Comments

The project is designed as part of a backend development brief and aims to demonstrate clean structure, security, and backend features.

---

## **Features**

### **Authentication & Security**

* Secure login system
* Password hashing with `password_hash()` (bcrypt)
* Session management
* Role system: `admin`, `editor`, `author`, `user`
* Protection against:

  * SQL Injection (PDO prepared statements)
  * XSS (htmlspecialchars)
  * Unauthorized access (role-based permissions)

---

### 🛠️ **Admin Dashboard**

* Overview statistics
* CRUD:

  * Categories
  * Articles
  * Users
  * Comments (moderation)
* Pagination on lists
* Article search engine
* Image upload for posts

---

### **Frontend (Public)**

* Display published articles
* Article detail page
* Leave comments (logged-in users only)
* Filter by category
* Search articles

---

## 🗂 **Project Structure**

```
/config
    config.php        → global configuration
    db.php            → PDO connection

/controllers
    articles/
    categories/
    users/
    comments/

/models
    Article.php
    Category.php
    User.php
    Comment.php

/views
    admin/
        dashboard.php
        articles/
        categories/
        users/
        comments/
    public/
        home.php
        article.php
        login.php

/public
    css/
    js/
    uploads/          → uploaded images

/functions
    auth.php          → login/logout, role checks
    helpers.php       → sanitization, redirects
```

---

## **Technologies Used**

### **Backend**

* PHP 8+
* PDO (prepared statements)
* MySQL

### **Frontend**

* HTML5, CSS3
* TailwindCSS

---

## **Main CRUD Functionalities**

### **Admin**

* Manage all content
* Moderate comments
* Manage users & roles

### **Editor**

* Manage all articles & categories
* No user management

### **Author**

* Create/Edit/Delete *own* articles
* Comment on posts

### **User**

* View published articles
* Post comments

---

## **Security Measures**

* Password hashing (bcrypt)
* Input sanitization
* Prepared SQL statements
* Limited file upload types
* Role-based route protection
* Session regeneration

---
