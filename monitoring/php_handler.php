<?php
$con = mysqli_connect('localhost','root','');

// ini nama database yang di mysql
mysqli_select_db($con, 'network_project'); 

$d = date('Y-m-d');

$sqlMinute = mysqli_fetch_row(mysqli_query($con, "SELECT MINUTE(Time) FROM data1 WHERE SUBSTRING(Time,1,10)= '$d' LIMIT 1"));
$sqlSecond = mysqli_fetch_row(mysqli_query($con, "SELECT SECOND(Time) FROM data1 WHERE SUBSTRING(Time,1,10)= '$d' LIMIT 1"));

$data1 = ($sqlMinute[0] + 15) % 60; // data kedua
$data2 = ($data1 + 15) % 60; // data ketiga
$data3 = ($data2 + 15) % 60; // data keempat
$data4 = ($data3 + 15) % 60; // data pertama

$sql = "SELECT Tekanan, Time 
			FROM 
			(
				SELECT Tekanan, Time, ID 
					FROM data1 
					WHERE SUBSTRING(Time,1,10)='$d' 
					AND 
					(
						(MINUTE(Time)='$data4' AND SECOND(Time)='$sqlSecond[0]') OR 
						(MINUTE(Time)='$data1' AND SECOND(Time)='$sqlSecond[0]') OR 
						(MINUTE(Time)='$data2' AND SECOND(Time)='$sqlSecond[0]') OR 
						(MINUTE(Time)='$data3' AND SECOND(Time)='$sqlSecond[0]')
					) 
					ORDER BY ID DESC LIMIT 10
			) 
			sub ORDER BY ID ASC";

$query = mysqli_query($con,$sql);

$arr = array();

while ($array = mysqli_fetch_array($query)) {
	$arr[] = $array['Tekanan'];
}

echo json_encode($arr);

// $query->close();

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// $d1 = ($sqlMinute[0] + 10) % 60;
// $d2 = ($d1 + 10) % 60;
// $d3 = ($d2 + 10) % 60;
// $d4 = ($d3 + 10) % 60;
// $d5 = ($d4 + 10) % 60;
// $d6 = ($d5 + 10) % 60;

// $sqll = "SELECT * FROM data1 WHERE 
// 								(MINUTE(Time)='$d6' AND SECOND(Time)='$sqlSecond[0]') OR
// 								(MINUTE(Time)='$d1' AND SECOND(Time)='$sqlSecond[0]') OR
// 								(MINUTE(Time)='$d2' AND SECOND(Time)='$sqlSecond[0]') OR
// 								(MINUTE(Time)='$d3' AND SECOND(Time)='$sqlSecond[0]') OR
// 								(MINUTE(Time)='$d4' AND SECOND(Time)='$sqlSecond[0]') OR
// 								(MINUTE(Time)='$d5' AND SECOND(Time)='$sqlSecond[0]')";


// $queryy = mysqli_query($con,$sqll);

// $arrr = array();

// while ($arrayy = mysqli_fetch_array($queryy)) {
// 	$arrr[] = $arrayy['Tekanan'];
// }

// echo json_encode($arrr);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $conn = mysqli_connect('localhost','root','');
// mysqli_select_db($conn, 'monitoring_inspeksi'); 

// $q = mysqli_query($conn, "SELECT * FROM (SELECT * FROM tekanan ORDER BY id_tekanan DESC LIMIT 10) sub ORDER BY id_tekanan ASC");
// $a = array();

// while ($aa = mysqli_fetch_array($q)) {
// 	$a[] = $aa['tekanan'];
// 	// echo $aa['tekanan']."<br>";
// }
?>