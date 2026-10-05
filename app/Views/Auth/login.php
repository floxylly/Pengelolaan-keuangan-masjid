

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Keuangan Masjid</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
            background: #f6f6e9;
        }

        .kiri {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 5%;
            max-width: 400px;
        }

        .kiri h1 {
            margin-bottom: 2em;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 1em;
        }

        .form input {
            padding: 0.6em;
            border-radius: 20px;
            border: none;
            background: #ccc;
            font-size: 1em;
        }

        .form button {
            padding: 0.6em 2em;
            background: #333;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            width: fit-content;
        }

        .kanan {
            flex: 1;
            display: flex;
            justify-content: right;
            align-items: center;
            overflow: hidden;
        }

        .kanan img {
            width: 80%;
            height: auto;
            max-height: 100vh;
            object-fit: cover;
        }

        @media screen and (max-width: 768px) {
            body {
                flex-direction: column;
                padding: 2em;
            }

            .kanan img {
                width: 80%;
                height: auto;
                margin-top: 2em;
            }
        }
    </style>
</head>

<body>
    <div class="kiri">
        <h1>Keuangan Masjid<br>Al-Anshor</h1>
        <form class="form" method="post" action="<?= site_url('login') ?>">

            <?= csrf_field() ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>

        <?php if (session()->getFlashdata('error')): ?>
        <div style="color:red; margin-top: 1em;">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="kanan">
        <img src="<?= base_url('/assets/masjidd.jpeg') ?>" alt="masjid">
    </div>
</body>

</html>
