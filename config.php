<!-- ini buat connect ke database nya -->

<?php
// ini nama username dan password untuk connect ke database mysql
$conn = mysqli_connect('localhost','root','');

// ini nama database yang di mysql
mysqli_select_db($conn, 'monitoring_inspeksi'); 
?>