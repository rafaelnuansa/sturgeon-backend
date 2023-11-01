<!DOCTYPE html>
<html>
<head>
    <title>Registration Confirmation</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>

    <p>Thank you for registering on our website. Your registration has been successfully confirmed.</p>

    <p>Your account details:</p>
    <ul>
        <li>Name: {{ $user->name }}</li>
        <li>Username: {{ $user->username }}</li>
        <li>Email: {{ $user->email }}</li>
    </ul>

    <p>Feel free to log in and explore our website!</p>

    <p>Best regards,<br>Your Website Team</p>
</body>
</html>
