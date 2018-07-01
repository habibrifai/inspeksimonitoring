<!doctype html>
<html lang="en">

<?php
$base = "http://localhost/inspeksimonitoring/";

include '../../config.php';

session_start();

if($_SESSION['status'] != "login inspektor"){
    header("location:". $base."login");
}


$dataUmum = array(array('no' => 1, 'nama_inspeksi' => 'Isolasi/Selubung badan bejana uap tidak terkelupas'), 
                    array('no' => 2, 'nama_inspeksi' => 'Tidak terdapat korosi dan kerak pada dinding pipa'),
                    array('no' => 3, 'nama_inspeksi' => 'Bejana uap digunakan pada tekanan yang diijinkan'),
                    array('no' => 4, 'nama_inspeksi' => 'Instalasi listrik pada kontrol panel baik dan sesuai standar '),
                    array('no' => 5, 'nama_inspeksi' => 'Telah dilakukan pembersihan tangki bejana uap '));

$dataBejana = array(array('no' => 7, 'nama_inspeksi' => 'Nomer Serie'), 
                    array('no' => 8, 'nama_inspeksi' => 'Tempat pembuatan'),
                    array('no' => 9, 'nama_inspeksi' => 'Tahun pembuatan'),
                    array('no' => 10, 'nama_inspeksi' => 'Tekanan kerja max'),
                    array('no' => 11, 'nama_inspeksi' => 'Tinggi badan'),
                    array('no' => 12, 'nama_inspeksi' => 'Diameter badan'),
                    array('no' => 13, 'nama_inspeksi' => 'Luas pemanas'),
                    array('no' => 14, 'nama_inspeksi' => 'Diameter pipa pemanas'),
                    array('no' => 15, 'nama_inspeksi' => 'Panjang pipa pemanas'),
                    array('no' => 16, 'nama_inspeksi' => 'Jumlah pipa pemanas'),
                    array('no' => 17, 'nama_inspeksi' => 'Diameter pipa jiwa'),
                    array('no' => 18, 'nama_inspeksi' => 'Jumlah pipa amoniak'),
                    array('no' => 19, 'nama_inspeksi' => 'Isi'),
                    array('no' => 20, 'nama_inspeksi' => 'Bahan'));

$dataKelengkapan1 = array(array('no' => 21, 'nama_inspeksi' => 'Pondasi', 'placeholder' => 'keterangan'), 
                    array('no' => 22, 'nama_inspeksi' => 'Support/penompang', 'placeholder' => 'keterangan'),
                    array('no' => 23, 'nama_inspeksi' => 'Anchort Bolt', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 24, 'nama_inspeksi' => 'Penutup Isolasi', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 25, 'nama_inspeksi' => 'Safety Valve', 'placeholder' => 'keterangan'),
                    array('no' => 26, 'nama_inspeksi' => 'Pressure gauge', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 27, 'nama_inspeksi' => 'Thermometer clock', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 28, 'nama_inspeksi' => 'Sight glass', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 29, 'nama_inspeksi' => 'Pelat nama', 'placeholder' => 'keterangan'));

$dataKelengkapan2 = array(array('no' => 30, 'nama_inspeksi' => 'Dipasang pada bagian bejana yang mudah dilihat oleh operator'), 
                    array('no' => 31, 'nama_inspeksi' => 'Apakah dapat menunjukkan tekanan kerja yang diperbolehkan'),
                    array('no' => 32, 'nama_inspeksi' => 'Terdapat tanda pada tekanan kerja maximum'));

$dataKelengkapan41 = array(array('no' => 34, 'nama_inspeksi' => 'Asli/telah diganti'), 
                     array('no' => 35, 'nama_inspeksi' => 'Ukuran'));

$dataKelengkapan42 = array(array('no' => 36, 'nama_inspeksi' => 'Nama dan tempat pembuatan'), 
                     array('no' => 37, 'nama_inspeksi' => 'Tahun pembuatan'),
                     array('no' => 38, 'nama_inspeksi' => 'Nomor serie'),
                     array('no' => 39, 'nama_inspeksi' => 'Tekanan kerja max yang diijinkan'));

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

<body onload="select();">
    <div class="wrapper">
        <div class="sidebar" data-color="purple" data-image="<?php echo $base; ?>assets/img/sidebar-1.jpg">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <!-- <div class="logo">
                <a href="http://www.creative-tim.com" class="simple-text">
                    Creative Tim
                </a>
            </div> -->
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <a href="<?php echo $base; ?>user">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>user/inspeksi_berkala">
                            <i class="material-icons">content_paste</i>
                            <p>Inspeksi Berkala</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="<?php echo $base; ?>user/inspeksi_bulanan">
                            <i class="material-icons">content_paste</i>
                            <p>Inspeksi Bulanan</p>
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
                        <a class="navbar-brand" href="#"> User Dashboard Inspeksi Bulanan</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (isset($_SESSION['failed_message'])) { ?>
                            <div class="alert alert-danger">
                                <span>
                                    <?php echo $_SESSION['failed_message'];unset($_SESSION['failed_message']); ?>
                                </span>
                            </div>
                            <?php } ?>
                            <?php if (isset($_SESSION['success_message'])) { ?>
                            <div class="alert alert-success">
                                <span>
                                    <?php echo $_SESSION['success_message'];unset($_SESSION['success_message']); ?>
                                </span>
                            </div>
                            <?php } ?>
                            <div class="card">
                                <div class="card-header" data-background-color="purple">
                                    <h5 class="title">Inspeksi Bulanan</h5>
                                    <!-- <p class="category">Pelanggan yang hari ini berulang tahun</p> -->
                                </div>
                                <div class="card-content">
                                    <form method="POST" action="simpan.php" enctype="multipart/form-data">
                                        <div class="card-content table-responsive">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">NO TANGKI</label>
                                                        <select class="form-control" id="no_tangki" onclick="select()" required="">
                                                            <?php $read_data = mysqli_query($conn, "SELECT no_tangki, uk_tangki, jenis_tangki FROM tangki") or die(mysqli_error());
                                                            while ($data = mysqli_fetch_array($read_data)) { ?>
                                                                <option value="<?php echo $data['jenis_tangki'].$data['uk_tangki']; ?>"><?php echo $data['no_tangki']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <input type="hidden" class="form-control" name="noTangki" id="noTangki" value="">
                                                        <input type="hidden" class="form-control" name="nip" value="<?php echo $_SESSION['nip']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">NO FORM</label>

                                                        <?php
                                                            $no_form = mysqli_query($conn, "SELECT no_form FROM form_teknisi WHERE jenis = 'Bulanan' ORDER BY ABS(SUBSTRING(no_form,4,LENGTH(no_form))) DESC LIMIT 1");
                                                            $noForm = mysqli_fetch_assoc($no_form);

                                                            if (isset($noForm)) {
                                                                $nomor = 'BL-'.(substr($noForm['no_form'], -(strlen($noForm['no_form'])-3)) + 1);
                                                            } else {
                                                                $nomor = 'BL-1';
                                                            }  
                                                        ?>

                                                        <input type="text" class="form-control" name="no_form" readonly="" value="<?php echo $nomor; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">JENIS</label>
                                                        <input id="jenis" type="text" class="form-control" name="jenis" disabled="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">UKURAN TANGKI</label>
                                                        <input id="ukuran" type="text" class="form-control" name="ukuran_tangki" disabled="">
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table">
                                                <thead class="text-primary">
                                                    <th>No</th>
                                                    <th>Picture</th>
                                                    <th>Pertanyaan</th>
                                                    <th>Jawaban</th>
                                                    <th>Kondisi</th>
                                                    <th>Keterangan</th>
                                                    <th>Rekomendasi</th>
                                                </thead>
                                                <tbody> 
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="3"><strong>DATA UMUM BEJANA UAP</strong></td>
                                                    </tr> 
                                                    <!-- Pertanyaan 1 sampai 5 -->
                                                    <?php $no = 1; foreach ($dataUmum as $value) { ?>                                  
                                                    <tr>
                                                        <td width="10px">
                                                            <?php echo $no; ?>
                                                            <input type="hidden" <?php echo "name=no".$value['no']; ?> value="<?php echo $value['no']; ?>">
                                                        </td>
                                                        <td><input type="file" <?php echo "name=picture".$value['no']; ?> style="width: 180px;"></td>
                                                        <td width="150px"><strong><?php echo $value['nama_inspeksi']; ?></strong></td>
                                                        <td width="120px">
                                                            <div class="form_group">
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio".''.$value['no']; ?> <?php echo "id=Ya".''.$value['no']; ?> value="Ya" checked >Ya
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio".''.$value['no']; ?> <?php echo "id=tidak".''.$value['no']; ?> value="Tidak">Tidak
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">kondisi</label>
                                                                <input type="text" class="form-control" <?php echo "name=kondisi".$value['no']; ?>>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">keterangan</label>
                                                                <input type="text" class="form-control" <?php echo "name=keterangan".$value['no']; ?>>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">rekomendasi</label>
                                                                <input type="text" class="form-control" <?php echo "name=rekomendasi".$value['no']; ?>>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php $no++; } ?>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="6"><strong>DATA BEJANA UAP</strong></td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 6 -->
                                                    <tr>
                                                        <td width="10px">1
                                                            <input type="hidden" name="no6" value="6">
                                                        </td>
                                                        <td><input type="file" name="picture6" style="width: 180px;"></td>
                                                        <td width="150px"><strong>Bejana uap memiliki ijin sesuai peraturan</strong></td>
                                                        <td width="120px">
                                                            <div class="form_group">
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio6"; ?> <?php echo "id=baik6"; ?> value="Ya" checked >Ya
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio6"; ?> <?php echo "id=tidak6"; ?> value="Tidak">Tidak
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">kondisi</label>
                                                                <input type="text" class="form-control" name="kondisi6">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">keterangan</label>
                                                                <input type="text" class="form-control" name="keterangan6">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">rekomendasi</label>
                                                                <input type="text" class="form-control" name="rekomendasi6">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td colspan="6">Data teknik bejana uap</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 7 sampai 21 -->
                                                    <?php foreach ($dataBejana as $dtBjn) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtBjn['no']; ?> value="<?php echo $dtBjn['no']; ?>">
                                                                    <input type="file" <?php echo "name=picture".$dtBjn['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtBjn['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td colspan="4">
                                                                    <div class="form-group label-floating">
                                                                        <label class="control-label">jawaban</label>
                                                                        <input type="text" class="form-control" <?php echo "name=radio".$dtBjn['no']; ?>>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <!-- <label class="control-label">kondisi</label> -->
                                                                    <input type="hidden" class="form-control" <?php echo "name=kondisi".$dtBjn['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <!-- <label class="control-label">keterangan</label> -->
                                                                    <input type="hidden" class="form-control" <?php echo "name=keterangan".$dtBjn['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <!-- <label class="control-label">rekomendasi</label> -->
                                                                    <input type="hidden" class="form-control" <?php echo "name=rekomendasi".$dtBjn['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="3"><strong>KELENGKAPAN/ALAT BANTU OPERASI</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td colspan="6">Apakah bejana uap dilengkapi dengan:</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 22 sampai 30 -->
                                                    <?php foreach ($dataKelengkapan1 as $dtKel1) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel1['no']; ?> value="<?php echo $dtKel1['no']; ?>">
                                                                    <input type="file" <?php echo "name=picture".$dtKel1['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel1['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel1['no']; ?> <?php echo "id=Ya".''.$dtKel1['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel1['no']; ?> <?php echo "id=tidak".''.$dtKel1['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtKel1['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label"><?php echo $dtKel1['placeholder']; ?></label>
                                                                    <input 

                                                                    <?php
                                                                        if ($dtKel1['placeholder'] == 'keterangan') {
                                                                            echo "type=text";
                                                                        } else {
                                                                            echo "type=number min=0 onkeypress='return isNumberKey(event)'";
                                                                        }
                                                                    ?>

                                                                    class="form-control" <?php echo "name=keterangan".$dtKel1['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtKel1['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td>2</td>
                                                        <td colspan="6">Pressure gauge</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 30 sampai 32 -->
                                                    <?php foreach ($dataKelengkapan2 as $dtKel2) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel2['no']; ?> value="<?php echo $dtKel2['no']; ?>">
                                                                    <input type="file" <?php echo "name=picture".$dtKel2['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel2['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel2['no']; ?> <?php echo "id=Ya".''.$dtKel2['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel2['no']; ?> <?php echo "id=tidak".''.$dtKel2['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtKel2['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtKel2['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtKel2['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td>3</td>
                                                        <td colspan="6">Safety valve</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="10px">
                                                            <input type="hidden" name="no33" value="33">
                                                        </td>
                                                        <td><input type="file" name="picture33" style="width: 180px;"></td>
                                                        <td width="150px"><strong>Dapat bekerja apabila tekanan pada bejana uap melebihi tekanan maximum /strong></td>
                                                        <td width="120px">
                                                            <div class="form_group">
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio33"; ?> <?php echo "id=baik33"; ?> value="Ya" checked >Ya
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio33"; ?> <?php echo "id=tidak33"; ?> value="Tidak">Tidak
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">kondisi</label>
                                                                <input type="text" class="form-control" name="kondisi33">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">keterangan</label>
                                                                <input type="text" class="form-control" name="keterangan33">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">rekomendasi</label>
                                                                <input type="text" class="form-control" name="rekomendasi33">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td colspan="6">Pelat nama</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 34 sampai 35 -->
                                                    <?php foreach ($dataKelengkapan41 as $dtKel41) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel41['no']; ?> value="<?php echo $dtKel41['no']; ?>">
                                                                    <input type="file" <?php echo "name=picture".$dtKel41['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel41['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel41['no']; ?> <?php echo "id=Ya".''.$dtKel41['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel41['no']; ?> <?php echo "id=tidak".''.$dtKel41['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtKel41['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtKel41['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtKel41['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="6">Memuat identitas bejana uap</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 36 sampai 39 -->
                                                    <?php foreach ($dataKelengkapan42 as $dtKel42) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel42['no']; ?> value="<?php echo $dtKel42['no']; ?>">
                                                                    <input type="file" <?php echo "name=picture".$dtKel42['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel42['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel42['no']; ?> <?php echo "id=Ya".''.$dtKel42['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel42['no']; ?> <?php echo "id=tidak".''.$dtKel42['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtKel42['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtKel42['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtKel42['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div style="text-align: center; vertical-align: middle; margin: 40px auto 30px;">
                                            <input class="btn btn-md btn-success" type="submit" name="submit" value="Simpan Hasil Inspeksi">
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
</body>
<!--   Core JS Files   -->
<script src="<?php echo $base; ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $base; ?>assets/js/material.min.js" type="text/javascript"></script>
<!--  Charts Plugin -->
<script src="<?php echo $base; ?>assets/js/chartist.min.js"></script>
<!--  Dynamic Elements plugin -->
<script src="<?php echo $base; ?>assets/js/arrive.min.js"></script>
<!--  PerfectScrollbar Library -->
<script src="<?php echo $base; ?>assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--  Notifications Plugin    -->
<script src="<?php echo $base; ?>assets/js/bootstrap-notify.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Material Dashboard javascript methods -->
<script src="<?php echo $base; ?>assets/js/material-dashboard.js?v=1.2.0"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="<?php echo $base; ?>assets/js/demo.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        // Javascript method's body can be found in assets/js/demos.js
        demo.initDashboardPageCharts();

    });
</script>
<script type="text/javascript">
    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if(charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    function select(){
        var e = document.getElementById("no_tangki");
        var value = e.options[e.selectedIndex].value;
        document.getElementById('jenis').value = value.substr(0,10);
        document.getElementById('ukuran').value = value.substr(10,value.length);
        document.getElementById('noTangki').value = e.options[e.selectedIndex].text;
    }

</script>

</html>