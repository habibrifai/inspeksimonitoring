<?php
$con = mysqli_connect('localhost','root','');

// ini nama database yang di mysql
mysqli_select_db($con, 'monitoring_inspeksi'); 

$query = mysqli_query($con, "SELECT * FROM (SELECT * FROM tekanan ORDER BY id_tekanan DESC LIMIT 10) sub ORDER BY id_tekanan ASC");

$arr = array();

while ($array = mysqli_fetch_assoc($query)) {
	$arr[] = $array['tekanan'];
}
// var_dump($arr);
echo json_encode($arr);
?>