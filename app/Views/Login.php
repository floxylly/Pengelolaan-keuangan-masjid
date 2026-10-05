<!DOCTYPE html>
<html>

<head>
    <title>Login Pengelolaan Masjid</title>
</head>

<body>
    <h2>Login</h2>
    <?php if (session()->getFlashdata('error')): ?>
    <p style="color:red"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <label>Username</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Masuk</button>
    </form>
</body>

</html>