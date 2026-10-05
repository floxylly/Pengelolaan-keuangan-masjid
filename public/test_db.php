<?php

require 'vendor/autoload.php';

use Config\Database;

$db = Database::connect();

if ($db->connID) {
    echo "Koneksi berhasil!";
} else {
    echo "Koneksi gagal: " . $db->error();
}