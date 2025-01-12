<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333333;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            font-size: 16px;
            color: #555555;
            margin: 10px 0;
        }
        .content a {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #aaaaaa;
            margin-top: 20px;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>Password Recovery</h1>
    </div>
    <div class="content">
        <p>Hi {{ $mailData['name'] }},</p>
        <p>You requested to reset your password. Click the button below to proceed:</p>
        <a href="{{ $mailData['link'] }}">Reset Password</a>
        <p>If you did not request this, please ignore this email.</p>
    </div>
    <div class="footer">
        <p>© 2024 NewsHub. All rights reserved.</p>
    </div>
</div>
</body>
</html>
