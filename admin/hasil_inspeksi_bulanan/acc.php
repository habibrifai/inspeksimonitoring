<?php
include "../../config.php";

$noForm = $_POST['noform'];

if ($query = mysqli_query($conn, "UPDATE form_teknisi SET status='Disetujui' WHERE no_form='$noForm';")) {
	header("location:../hasil_inspeksi_bulanan");
} else {

}

?>