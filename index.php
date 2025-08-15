<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardo - Business Card Maker</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #f4f4f9;
        }
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .header .auth-buttons button {
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            border: 1px solid #007bff;
            border-radius: 4px;
            cursor: pointer;
        }
        .header .auth-buttons .login {
            background-color: #007bff;
            color: white;
        }
        .header .auth-buttons .signup {
            background-color: white;
            color: #007bff;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
        }

        /* Banner Section */
        .banner-section {
            height: 300px;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            border-radius: 8px;
        }

        /* Templates Section */
        .templates-section h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .template-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
            padding: 1rem;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-around;
            padding: 1rem 0;
            background-color: #fff;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }
        .bottom-nav a {
            color: #333;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo">Cardo</div>
        <!-- Hamburger Menu Placeholder -->
        <div>&#9776;</div>
        <div class="auth-buttons">
            <button class="login">Login</button>
            <button class="signup">Sign Up</button>
        </div>
        <!-- Profile/Logout (for logged-in state) would go here -->
    </header>

    <main class="main-content">
        <section class="banner-section">
            <div>Sliding Banners Placeholder</div>
        </section>

        <section class="templates-section">
            <h2>Our Templates</h2>
            <div class="template-grid">
                <div class="template-card">Template 1</div>
                <div class="template-card">Template 2</div>
                <div class="template-card">Template 3</div>
                <div class="template-card">Template 4</div>
            </div>
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="#">Templates</a>
        <a href="#">My Orders</a>
        <a href="#">Payment Status</a>
        <a href="#">Support</a>
    </nav>

</body>
</html>
