<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher ID Card</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* CARD */
        .card {
            width: 300px;
            height: 500px;
            background: #f5f5f5;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }

        /* TOP CURVE */
        .top-shape {
            position: absolute;
            width: 200%;
            height: 200px;
            background: #2d2b63;
            top: -120px;
            left: -50%;
            border-radius: 50%;
        }

        /* ORANGE STRIPE */
        .top-shape::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 180px;
            background: orange;
            top: 40px;
            left: -50%;
            border-radius: 50%;
        }

        /* BOTTOM CURVE */
        .bottom-shape {
            position: absolute;
            width: 200%;
            height: 200px;
            background: #2d2b63;
            bottom: -120px;
            left: -50%;
            border-radius: 50%;
        }

        .bottom-shape::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 180px;
            background: orange;
            bottom: 40px;
            left: -50%;
            border-radius: 50%;
        }

        /* CONTENT */
        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 20px;
        }

        /* LOGO */
        .logo {
            margin-top: 40px;
            color: #2d2b63;
        }

        .tagline {
            font-size: 12px;
            margin-bottom: 15px;
        }

        /* PROFILE IMAGE */
        .profile {
            width: 120px;
            height: 120px;
            margin: 10px auto;
            border-radius: 50%;
            border: 5px solid #2d2b63;
            overflow: hidden;
        }

        .profile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* NAME */
        .name {
            color: #2d2b63;
            margin-top: 10px;
            font-size: 20px;
            font-weight: bold;
        }

        .role {
            margin: 5px 0 15px;
            font-weight: bold;
        }

        /* INFO */
        .info {
            font-size: 14px;
            line-height: 25px;
        }

        /* DOT PATTERN */
        .dots {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 80px;
            height: 80px;
            background-image: radial-gradient(#000 1px, transparent 1px);
            background-size: 8px 8px;
            opacity: 0.3;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="top-shape"></div>
        <div class="bottom-shape"></div>

        <div class="content">
            <h2 class="logo">excellency</h2>
            <p class="tagline">Learn to Serve The Nation</p>

            <div class="profile">
                <img src="teacher.jpg" alt="Teacher">
            </div>

            <h1 class="name">NAIMA AKTER</h1>
            <p class="role">TEACHER</p>

            <div class="info">
                <p>🩸 O+</p>
                <p>📞 01329-335171</p>
            </div>
        </div>

        <div class="dots"></div>
    </div>

</body>

</html>

