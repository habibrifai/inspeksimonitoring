<!doctype html>
<html lang="en">

<?php
$base = "http://localhost/inspeksimonitoring/";

include "../config.php";

session_start();

if($_SESSION['status'] != ("login admin" || "login monitoring")){
    header("location:". $base."login");
}

?>

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $base; ?>assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="<?php echo $base; ?>assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Dashboard - Monitoring</title>
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

<body>
    <div class="wrapper">
        <div class="sidebar" data-color="purple" data-image="<?php echo $base; ?>assets/img/sidebar-1.jpg">
            <?php
            	if ($_SESSION['status'] == "login admin") { ?>
            		<div class="sidebar-wrapper">
            			<ul class="nav">
            				<li>
            					<a href="<?php echo $base; ?>admin">
            						<i class="material-icons">dashboard</i>
            						<p>Dashboard</p>
            					</a>
            				</li>
                            <li>
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
            					<?php
                                $notif_berkala = mysqli_query($conn, "SELECT COUNT(*) FROM form_teknisi WHERE status = 'Belum Disetujui' AND jenis = 'Berkala'") or die(mysqli_error());
                                $not_berkala = mysqli_fetch_assoc($notif_berkala);

                            // echo $not_berkala['COUNT(*)'];

                                if ($not_berkala['COUNT(*)'] > 0) { ?>
                                    <a href="<?php echo $base; ?>admin/hasil_inspeksi_berkala">
                                        <i class="material-icons">content_paste</i>
                                        <p>Inspeksi Berkala&nbsp;<span class="label label-danger">!</span></p>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo $base; ?>admin/hasil_inspeksi_berkala">
                                        <i class="material-icons">content_paste</i>
                                        <p>Inspeksi Berkala</p>
                                    </a>
                                <?php } ?>  
            				</li>
            				<li>
            					<?php
                                $notif_berkala = mysqli_query($conn, "SELECT COUNT(*) FROM form_teknisi WHERE status = 'Belum Disetujui' AND jenis = 'Bulanan'") or die(mysqli_error());
                                $not_berkala = mysqli_fetch_assoc($notif_berkala);

                            // echo $not_berkala['COUNT(*)'];

                                if ($not_berkala['COUNT(*)'] > 0) { ?>
                                    <a href="<?php echo $base; ?>admin/hasil_inspeksi_bulanan">
                                        <i class="material-icons">content_paste</i>
                                        <p>Inspeksi Bulanan&nbsp;<span class="label label-danger">!</span></p>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo $base; ?>admin/hasil_inspeksi_bulanan">
                                        <i class="material-icons">content_paste</i>
                                        <p>Inspeksi Bulanan</p>
                                    </a>
                                <?php } ?> 
            				</li>
            				<li>
            					<a href="<?php echo $base; ?>monitoring">
            						<i class="material-icons">graphic_eq</i>
            						<p>Hasil Monitor</p>
            					</a>
            				</li>
                            <li class="active">
                                <a href="<?php echo $base; ?>laporan_monitoring">
                                    <i class="material-icons">graphic_eq</i>
                                    <p>Laporan Monitoring</p>
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
            <?php } elseif ($_SESSION['status'] == "login monitoring") { ?>
            	<div class="sidebar-wrapper">
            		<ul class="nav">
            			<li>
            				<a href="<?php echo $base; ?>monitoring">
            					<i class="material-icons">graphic_eq</i>
            					<p>Hasil Monitor</p>
            				</a>
            			</li>
                        <li class="active">
                            <a href="<?php echo $base; ?>laporan_monitoring">
                                <i class="material-icons">graphic_eq</i>
                                <p>Laporan Monitoring</p>
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
            <?php } ?>
            
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
                        <a class="navbar-brand" href="#"> Laporan Monitoring </a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">                     
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header" data-background-color="orange">
                                    <h4 class="title"> List Laporan Monitoring</h4>
                                </div>
                                <div class="card-content table-responsive">
                                    <div class="col-md-12">
                                        <table class="table">
                                            <thead class="text-primary">
                                                <th>No</th>
                                                <th>Tanggal Monitoring</th>
                                                <th>Keterangan</th>
                                            </thead>
                                            <tbody>
                                                <?php

                                                function tanggal_indo($tanggal, $cetak_hari = false)
                                                {
                                                    $hari = array ( 1 =>    'Senin',
                                                        'Selasa',
                                                        'Rabu',
                                                        'Kamis',
                                                        'Jumat',
                                                        'Sabtu',
                                                        'Minggu'
                                                    );

                                                    $bulan = array (1 =>   'Januari',
                                                        'Februari',
                                                        'Maret',
                                                        'April',
                                                        'Mei',
                                                        'Juni',
                                                        'Juli',
                                                        'Agustus',
                                                        'September',
                                                        'Oktober',
                                                        'November',
                                                        'Desember'
                                                    );
                                                    $split    = explode('-', $tanggal);
                                                    $tgl_indo = $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];

                                                    if ($cetak_hari) {
                                                        $num = date('N', strtotime($tanggal));
                                                        return $hari[$num] . ', ' . $tgl_indo;
                                                    }
                                                    return $tgl_indo;
                                                }
                                                $no = 1;
                                                $data = mysqli_query($conn, "SELECT SUBSTRING(Time,1,10) as time FROM data1 GROUP BY SUBSTRING(Time,1,10) DESC HAVING COUNT(*) > 1");

                                                while ($dt = mysqli_fetch_array($data)) { ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $no; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo tanggal_indo($dt['time'], true); ?>
                                                        </td>
                                                        <td>
                                                            <form action="detail.php" method="POST">
                                                                <input type="hidden" name="time" value="<?php echo tanggal_indo($dt['time']); ?>">
                                                                <input type="hidden" name="time1" value="<?php echo $dt['time']; ?>">
                                                                <input class="btn btn-md btn-info" type="submit" name="submit" value="DETAIL">
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php $no++; } ?>
                                            </tbody>
                                        </table>
                                    </div>                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<!--   Core JS Files   -->
<script src="<?php echo $base; ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/material.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/Chart.min.js"></script>
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

</html>