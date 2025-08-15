# Cardo - Business Card Making App

Cardo is a web application that allows users to select a business card template, fill in their details, and order a custom business card. It features a full admin panel for managing users, templates, banners, and orders.

## Features Implemented
- **Admin Panel:** A secure, session-based admin panel for managing the application.
- **User Management:** Admins can view, add, search, and delete users.
- **Banner Management:** Admins can add and delete homepage banners.
- **Template Management:** Full CRUD (Create, Read, Update, Delete) functionality for both business card templates and their categories.
- **Order Management (In Progress):** Admins can view all orders and their details, and update the order status.

---

## 🚀 Setup and Installation

To run this application, you need a web server that supports **PHP** and a **MySQL** database. The easiest way to get this on a personal computer is by using a free program like **XAMPP** or **WAMP**.

Follow these 5 steps carefully to set up the project.

### Step 1: Place Project Files on the Server

- **If using XAMPP/WAMP:** Place the entire project folder (e.g., `CardoApp-main`) inside the `htdocs` folder (for XAMPP) or the `www` folder (for WAMP).
- **If using a live web host:** Upload all project files to your server's main directory (often called `public_html` or `www`).

### Step 2: Create a Database

You need to create a database where the application will store all its data.

1.  Open your web browser and go to `http://localhost/phpmyadmin/` (this is the default address for XAMPP).
2.  Click on the **"Databases"** tab at the top.
3.  In the "Create database" field, enter a name for your database. We recommend using `cardo_db`.
4.  Click the **"Create"** button.

### Step 3: Import the SQL Tables

Now you need to create all the necessary tables inside your new database.

1.  After creating the database in the previous step, click on its name in the left sidebar to select it.
2.  Click on the **"Import"** tab at the top.
3.  Click on the "Choose File" or "Browse..." button.
4.  Find and select the `database.sql` file from the project folder.
5.  Scroll to the bottom of the page and click the **"Go"** or **"Import"** button.

This will execute the SQL script and create all the tables (`users`, `admin`, `orders`, etc.).

### Step 4: Configure the Database Connection (Most Important Step!)

You must tell the PHP code how to connect to the database you just created.

1.  In the project folder, open the `includes/config.php` file with a text editor.
2.  You will see the following lines:
    ```php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'cardo_db');
    ```
3.  **You must edit these values** to match your database setup.
    -   **For a standard XAMPP setup:** Change `localhost` to `127.0.0.1`. The other values (`root`, `''` for password, `cardo_db`) are usually correct by default. The corrected block would look like this:
        ```php
        define('DB_SERVER', '127.0.0.1'); // Use this instead of 'localhost'
        define('DB_USERNAME', 'root');
        define('DB_PASSWORD', ''); // XAMPP password is empty by default
        define('DB_NAME', 'cardo_db'); // The name you chose in Step 2
        ```
    -   **For a live web host:** Your hosting provider will give you the correct database host (server), database name, username, and password when you create the database in their control panel. You must use those exact values here.

### Step 5: Run the Application

You are all set!

-   **Admin Panel:** To access the admin login page, go to: `http://localhost/YourProjectFolderName/admin/login.php`
    -   **Default Username:** `rushikeshmurhekar2@gmail.com`
    -   **Default Password:** `$$$Rushi@12#`

-   **User Application:** To access the main user-facing homepage, go to: `http://localhost/YourProjectFolderName/index.php`

---

Following these instructions will resolve the connection errors and make the application fully functional on your server.
