<!doctype html>
<html lang="en">

<?php
$base = "http://localhost/inspeksimonitoring/";
$conn = mysqli_connect('localhost','root','');

mysqli_select_db($conn, 'network_project'); 
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
<meta>
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
                        <a class="navbar-brand" href="#"> Monitoring Dashboard </a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <button class="btn btn-sm btn-info" type="button" id="download-pdf">
                            GET PDF
                        </button>
                        <canvas id="tekanan" width="600" height="290"></canvas>
                        <p id="output"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<!--   Core JS Files   -->
<script src="<?php echo $base; ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/jspdf.min.js" type="text/javascript"></script>
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
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script> -->

<?php

if (isset($_POST['time']) && isset($_POST['time1'])) {
	$time = $_POST['time'];
	$time1 = $_POST['time1'];

	$sqlMinute = mysqli_fetch_row(mysqli_query($conn, "SELECT MINUTE(Time) FROM data1 WHERE SUBSTRING(Time,1,10)='$time1' LIMIT 1"));
	$sqlSecond = mysqli_fetch_row(mysqli_query($conn, "SELECT SECOND(Time) FROM data1 WHERE SUBSTRING(Time,1,10)='$time1' LIMIT 1"));

	$data1 = ($sqlMinute[0] + 15) % 60; // data kedua
	$data2 = ($data1 + 15) % 60; // data ketiga
	$data3 = ($data2 + 15) % 60; // data keempat
	$data4 = ($data3 + 15) % 60; // data pertama

	$sql = "SELECT Tekanan, SUBSTRING(Time,12,16) as time 
		FROM 
	(
		SELECT Tekanan, Time, ID 
		FROM data1 
		WHERE SUBSTRING(Time,1,10)='$time1' 
		AND 
		(
		(MINUTE(Time)='$data4' AND SECOND(Time)='$sqlSecond[0]') OR 
		(MINUTE(Time)='$data1' AND SECOND(Time)='$sqlSecond[0]') OR 
		(MINUTE(Time)='$data2' AND SECOND(Time)='$sqlSecond[0]') OR 
		(MINUTE(Time)='$data3' AND SECOND(Time)='$sqlSecond[0]')
		) 
		ORDER BY ID DESC
		) 
		sub ORDER BY ID ASC";

		$query = mysqli_query($conn,$sql);

        $arr = array();
		$arr1 = array();

		while ($array = mysqli_fetch_array($query)) {
            $arr[] = $array['Tekanan'];
			$arr1[] = $array['time'];
		}

        $arrLength = count($arr);
}

?>

<script type="text/javascript">

	var result = [];
    var label = [];

    var arrLength = "<?php echo $arrLength; ?>";
    
    "<?php foreach($arr1 as $val){ ?>"

        label.push("<?php echo $val; ?>");

    "<?php } ?>"
    

    "<?php foreach($arr as $val){ ?>"

        result.push("<?php echo $val; ?>");

    "<?php } ?>"
 
	$('#output').html("<b>tekanan: </b>"+result);

	var pressureCanvas = document.getElementById("tekanan");

	Chart.defaults.global.defaultFontFamily = "Lato";
	Chart.defaults.global.defaultFontSize = 14;

	var chartOptions = {
		legend: {
			display: true,
			position: 'top',
			labels: {
				boxWidth: 80,
				fontColor: 'black'
			}
		},
		// animation: {
		// 	duration: 1
		// },
		showTooltips: true
	};
	var dataTekanan = {
		labels: label,
		datasets: [{
			label: "Tekanan",
			data: result,
			backgroundColor: '#663096',
			fill: false,
			radius:3,
			borderColor: '#663096'
		}]
	};

	var lineChart = new Chart(pressureCanvas, {
		type: 'line',
		data: dataTekanan,
		options: chartOptions,
		animation: false
	});
        

document.getElementById('download-pdf').addEventListener("click", downloadPDF);

function downloadPDF() {
    var newCanvas = document.querySelector('#tekanan');

    //create image from dummy canvas
    var newCanvasImg = newCanvas.toDataURL("image/png", 1.0);
  
    //creates PDF from img
    var doc = new jsPDF('landscape');
    doc.setFontSize(13);
    doc.addImage(newCanvasImg, 'JPEG', 10, 10, 278, 150);
    // doc.save('new-canvas.pdf');
    doc.text(10, 170, "Tekanan : "+result);
    doc.output('dataurlnewwindow');
}

</script>

</html>