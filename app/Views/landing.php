<?php if (session()->get('logged_in')): ?>
    <script>
        window.location.href = "<?= site_url('dashboard') ?>";
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30">
    <title>Keuangan Masjid Al-Anshor</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: space-between;
            align-items: stretch; /* agar tinggi flex item sama */
            height: 100vh;
            margin: 0;
            background: #f6f6e9;
        }

        .kiri {
            flex: 1;
            padding: 5%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kiri h1 {
            font-size: 3em;
            margin-bottom: 1.5em;
        }

        .kiri a {
            display: inline-block; /* memastikan tombol tetap sesuai konten */
            padding: 0.8em 2.5em;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1em;
            width: auto; /* jangan ikut flex container */
            text-align: center;
        }

        .kanan {
            flex: 1;
            overflow: hidden;
        }

        .kanan img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* gambar memenuhi area tanpa merusak proporsi */
        }
    </style>
</head>

<body>
    <div class="kiri">
        <h1>Keuangan Masjid<br>Al-Anshor</h1>
        <a href="/login">Mulai</a>
    </div>
    <div class="kanan">
        <img src="<?= base_url('/assets/masjidd.jpeg') ?>" alt="masjid">
    </div>
</body>

</html>
