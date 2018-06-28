<!-- ini kode buat fungsi login user -->
<?php 
include '../config.php';

 // waktu input username tadi, inputannya ditangkep disini
$n = $_POST['nip'];
 // $password = md5($_POST['password']);

 //waktu input password adi, inputannya ditangkep disini
$pass = $_POST['password'];

$nip = mysqli_real_escape_string($conn, $n);
// $password = md5(mysqli_real_escape_string($conn, $pass));

// cek apakah inputan username password sama dengan yang di database
$login = mysqli_query($conn, "select * from user where nip='$nip' and password='$pass'");
$cek = mysqli_num_rows($login);

if ($cek > 0) {
	while($row = mysqli_fetch_assoc($login)){   

		if($cek > 0 && $row['jabatan']=='Admin'){
			session_start();
			$_SESSION['nip'] = $nip;
			$_SESSION['status'] = "login admin";
			header("location:../admin");	
		}elseif($cek > 0 && $row['jabatan']=='Inspektor') {
			session_start();
			$_SESSION['nip'] = $nip;
			$_SESSION['status'] = "login inspektor";
			header("location:../inspeksi");
		}elseif($cek > 0 && $row['jabatan']=='Monitoring'){
			session_start();
			$_SESSION['nip'] = $nip;
			$_SESSION['status'] = "login monitoring";
			header("location:../monitoring");
		}
	}
} else {	
	echo "<script>alert('Username atau password salah!!')</script>";
	echo "<script>location.href='../login';</script>";	
}

?>

