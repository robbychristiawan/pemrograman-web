<?php

//host
$databaseHost = 'localhost';
//Nama database
$databaseName = 'simrs';
//Username
$databseUsername = 'root';
//Password mysql
$databasePassword = '';
$mysqli = mysqli_connect($databaseHost, $databseUsername, $databasePassword, $databaseName);
if (!$mysqli) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
echo "Koneksi ke database simrs berhasil!";
?>