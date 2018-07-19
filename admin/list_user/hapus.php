<?php
include "../../config.php";

$nip = $_POST['nip'];

if ($query = mysqli_query($conn, "DELETE FROM user WHERE nip = '$nip'")){
	header("location:../list_user");

} else {

}

?>