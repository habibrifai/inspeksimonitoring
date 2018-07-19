<!doctype html>
<html lang="en">

<?php
$base = "http://localhost/inspeksimonitoring/";
include '../../config.php';
session_start();

if($_SESSION['status'] != "login admin"){
    header("location:". $base."login");
}

?>

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $base; ?>assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="<?php echo $base; ?>assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Dashboard - Admin</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!-- Bootstrap core CSS     -->
    <link href="<?php echo $base; ?>assets/css/bootstrap.min.css" rel="stylesheet" />
    <!--  Material Dashboard CSS    -->
    <link href="<?php echo $base; ?>assets/css/material-dashboard.css?v=1.2.0" rel="stylesheet" />
    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="<?php echo $base; ?>assets/css/demo.css" rel="stylesheet" />
    <!--     Fonts and icons     -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons" rel='stylesheet'>
</head>

<body onload="UploadTtd();">
    <div class="wrapper">
        <div class="sidebar" data-color="purple" data-image="<?php echo $base; ?>assets/img/sidebar-1.jpg">
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <a href="<?php echo $base; ?>admin">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="<?php echo $base; ?>admin/tambah_user">
                            <i class="material-icons">person_add</i>
                            <p>Tambah User</p>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>admin/list_user">
                            <i class="material-icons">person</i>
                            <p>List User</p>
                        </a>
                    </li>
                    <li>
                    	<a href="<?php echo $base; ?>admin/hasil_inspeksi_berkala">
                    		<i class="material-icons">content_paste</i>
                    		<p>Inspeksi Berkala</p>
                    	</a>
                    </li>
                    <li>
                    	<a href="<?php echo $base; ?>admin/hasil_inspeksi_bulanan">
                    		<i class="material-icons">content_paste</i>
                    		<p>Inspeksi Bulanan</p>
                    	</a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>monitoring">
                            <i class="material-icons">graphic_eq</i>
                            <p>Hasil Monitor</p>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>logout">
                            <i class="material-icons">exit_to_app</i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <nav class="navbar navbar-transparent navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#"> Tambah User </a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">                     
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header" data-background-color="orange">
                                    <h4 class="title"> Form Tambah User</h4>
                                </div>
                                <div class="card-content">
                                	<form method="POST" action="index.php" enctype="multipart/form-data">
                                		<div class="col-md-12">
                                			<div class="form-group label-floating">
                                				<label class="control-label">Nama Pekerja</label>
                                				<input type="text" class="form-control" name="nama" required="">
                                			</div>
                                			<div class="form-group label-floating">
                                				<label class="control-label">Divisi Pekerja</label>
                                				<input type="text" class="form-control" name="divisi" required="">
                                			</div>
                                			<div class="form-group label-floating" onclick="UploadTtd()">
                                				<label class="control-label">Jabatan Pekerja</label>
                                				<select class="form-control" name="jabatan" id="jabatan">
                                					<option value="Inspektor">Petugas Inspeksi</option>
                                					<option value="Monitoring">Petugas Monitoring</option>
                                				</select>
                                			</div>
                                			<div style="display: block;" id="ttd">
                                				<label class="control-label">Scan Tanda Tangan Pekerja</label>
                                				<input type="file" name="ttd" onchange="ValidateSize(this)" accept="image/*">
                                			</div>
                                			<script type="text/javascript">
                                				function UploadTtd(){
                                					var ttd = document.getElementById('ttd');
                                					// var ttdd = document.getElementByName('ttd');
                                					var jabatan = document.getElementById('jabatan').value;
                                					if (jabatan == 'Inspektor') {
                                						ttd.style.display = "block";
                                					} else {
                                						ttd.style.display = "none";
                                					}
                                				}

                                				function ValidateSize(file){
                                					var FileSize = file.files[0].size / 1024 / 1024;
                                					var FileType = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                                					if (FileSize > 2) {
                                						alert('Setiap gambar harus kurang dari 2MB');
                                						location.reload();  
                                					}
                                					if (!FileType.exec(file.value)) {
                                						alert('File yang anda masukkan harus format gambar');
                                						location.reload();  
                                					}
                                				}
                                			</script>
                                		</div>
                                		<div>
                                			<input style="float: right;" type="submit" name="submit" class="btn btn-md btn-success" value="SIMPAN">
                                		</div>
                                	</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--   Core JS Files   -->
<script src="<?php echo $base; ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/material.min.js" type="text/javascript"></script>
<!--  Dynamic Elements plugin -->
<script src="<?php echo $base; ?>assets/js/arrive.min.js"></script>
<!--  PerfectScrollbar Library -->
<script src="<?php echo $base; ?>assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--  Notifications Plugin    -->
<script src="<?php echo $base; ?>assets/js/bootstrap-notify.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="<?php echo $base; ?>assets/js/material-dashboard.js?v=1.2.0"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="<?php echo $base; ?>assets/js/demo.js"></script>

<div class="modal fade" tabindex="-1" role="dialog" id="failed">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title alert alert-danger">GAGAL!</h4>
			</div>
			<div class="modal-body">
				<p>Data karyawan gagal ditambahkan, silakan ulangi lagi..</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="failedttd">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title alert alert-danger">GAGAL!</h4>
			</div>
			<div class="modal-body">
				<p>Data karyawan gagal ditambahkan, silakan ulangi lagi..</p>
				<p>Pastikan anda memasukkan hasil scan tanda tangan petugas inspeksi</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php 
if (isset($_POST['submit'])) {
	$nama = $_POST['nama'];
	$divisi = $_POST['divisi'];
	$jabatan = $_POST['jabatan'];

	if ($jabatan == "Inspektor") {

		$read_nip = mysqli_query($conn, "SELECT MAX(nip) FROM user WHERE jabatan = 'Inspektor'");

		while ($data = mysqli_fetch_array($read_nip)) {

			if (isset($data['MAX(nip)'])) {
				$nip_password = $data['MAX(nip)'] + 1;
			} else {
				$nip_password = '21001';
			}
			
			$target = "../../assets/gambar/";
			// $gambar = pathinfo($_FILES['ttd']['name'], PATHINFO_FILENAME);
			$extension = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);



			$basename = $nip_password . '.' . $extension;	

			if (move_uploaded_file($_FILES['ttd']['tmp_name'], $target.$basename)) {

				mysqli_query($conn, "INSERT INTO user (nip, nama, password, divisi, jabatan)
				VALUES ('$nip_password', '$nama', '$nip_password', '$divisi', '$jabatan')");

				echo "<script type='text/javascript'>
				$(window).on('load',function(){
					$('#success').modal('show');
					});
					</script>";
			} else {
				echo "<script type='text/javascript'>
				$(window).on('load',function(){
					$('#failedttd').modal('show');
					});
					</script>";
			}
		}
	} elseif($jabatan == "Monitoring"){

		$read_nip2 = mysqli_query($conn, "SELECT MAX(nip) FROM user WHERE jabatan = 'Monitoring'");

		while ($data = mysqli_fetch_array($read_nip2)) {

			if (isset($data['MAX(nip)'])) {
				$nip_password = $data['MAX(nip)'] + 1;
			} else {
				$nip_password = '31001';
			}
			

			if (mysqli_query($conn, "INSERT INTO user (nip, nama, password, divisi, jabatan)
				VALUES ('$nip_password', '$nama', '$nip_password', '$divisi', '$jabatan')"))  
			{

				echo "<script type='text/javascript'>
				$(window).on('load',function(){
					$('#success').modal('show');
					});
					</script>";
			} else {
				echo "<script type='text/javascript'>
				$(window).on('load',function(){
					$('#failed').modal('show');
					});
					</script>";
			}

		}
	}

}
?>

<div class="modal fade" tabindex="-1" role="dialog" id="success">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title alert alert-success">BERHASIL!</h4>
			</div>
			<div class="modal-body">
				<p>Data karyawan berhasil disimpan..</p>
				<p>NIP User : <?php echo $nip_password; ?></p>
				<p>Password : <?php echo $nip_password; ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>