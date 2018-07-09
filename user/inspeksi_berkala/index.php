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
                    array('no' => 4, 'nama_inspeksi' => 'Instalasi listrik pada kontrol panel baik dan sesuai standar'));

$dataUmum5 = array(array('no' => 5, 'nama_inspeksi' => 'Pipa uap pemanas'), 
                    array('no' => 6, 'nama_inspeksi' => 'Pipa air masak soda'),
                    array('no' => 7, 'nama_inspeksi' => 'Pipa masukan nira'),
                    array('no' => 8, 'nama_inspeksi' => 'Pipa keluaran nira'),
                    array('no' => 9, 'nama_inspeksi' => 'Pipa pemanas'),
                    array('no' => 10, 'nama_inspeksi' => 'Pipa amonia'),
                    array('no' => 11, 'nama_inspeksi' => 'Pipa pancingan vacuum'),
                    array('no' => 12, 'nama_inspeksi' => 'Pipa uap nira'),
                    array('no' => 13, 'nama_inspeksi' => 'Pipa tap nira'),
                    array('no' => 14, 'nama_inspeksi' => 'Pipa tap soda'),
                    array('no' => 15, 'nama_inspeksi' => 'Pipa jiwa'));

$dataUmum6 = array(array('no' => 16, 'nama_inspeksi' => 'Gambar konstruksi lengkap'), 
                    array('no' => 17, 'nama_inspeksi' => 'Disahkan oleh bengkel konstruksi yang disahkan PJK3 fabrikasi'),
                    array('no' => 18, 'nama_inspeksi' => 'Sertifikat bahan'),
                    array('no' => 19, 'nama_inspeksi' => 'Tanda hasil NDT'),
                    array('no' => 20, 'nama_inspeksi' => 'Kalibrasi alat-alat pengaman dan pelengkap'));

$dataUmum7 = array(array('no' => 21, 'nama_inspeksi' => 'Gambar rencana'), 
                   array('no' => 22, 'nama_inspeksi' => 'Pemakaian'));

$dataBejana2 = array(array('no' => 24, 'nama_inspeksi' => 'Pengesahan gambar rencana reparasi', 'placeholder' => 'no pengesahan'), 
                     array('no' => 25, 'nama_inspeksi' => 'Dikerjakan bengkel konstruksi yang telas disahkan Depnaker', 'placeholder' => 'keterangan'));

$dataBejana3 = array(array('no' => 26, 'nama_inspeksi' => 'Nomer Serie'), 
                    array('no' => 27, 'nama_inspeksi' => 'Tempat pembuatan'),
                    array('no' => 28, 'nama_inspeksi' => 'Tahun pembuatan'),
                    array('no' => 29, 'nama_inspeksi' => 'Tekanan kerja max'),
                    array('no' => 30, 'nama_inspeksi' => 'Tinggi badan'),
                    array('no' => 31, 'nama_inspeksi' => 'Diameter badan'),
                    array('no' => 32, 'nama_inspeksi' => 'Luas pemanas'),
                    array('no' => 33, 'nama_inspeksi' => 'Diameter pipa pemanas'),
                    array('no' => 34, 'nama_inspeksi' => 'Panjang pipa pemanas'),
                    array('no' => 35, 'nama_inspeksi' => 'Jumlah pipa pemanas'),
                    array('no' => 36, 'nama_inspeksi' => 'Diameter pipa jiwa'),
                    array('no' => 37, 'nama_inspeksi' => 'Jumlah pipa amoniak'),
                    array('no' => 38, 'nama_inspeksi' => 'Isi'),
                    array('no' => 39, 'nama_inspeksi' => 'Bahan'));

$dataKelengkapan1 = array(array('no' => 40, 'nama_inspeksi' => 'Pondasi', 'placeholder' => 'keterangan'), 
                    array('no' => 41, 'nama_inspeksi' => 'Support/penompang', 'placeholder' => 'keterangan'),
                    array('no' => 42, 'nama_inspeksi' => 'Anchort Bolt', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 43, 'nama_inspeksi' => 'Penutup Isolasi', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 44, 'nama_inspeksi' => 'Safety Valve', 'placeholder' => 'keterangan'),
                    array('no' => 45, 'nama_inspeksi' => 'Pressure gauge', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 46, 'nama_inspeksi' => 'Thermometer clock', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 47, 'nama_inspeksi' => 'Sight glass', 'placeholder' => 'jumlah dalam buah'),
                    array('no' => 48, 'nama_inspeksi' => 'Pelat nama', 'placeholder' => 'keterangan'));

$dataKelengkapan2 = array(array('no' => 49, 'nama_inspeksi' => 'Dipasang pada bagian bejana yang mudah dilihat oleh operator'), 
                    array('no' => 50, 'nama_inspeksi' => 'Apakah dapat menunjukkan tekanan kerja yang diperbolehkan'),
                    array('no' => 51, 'nama_inspeksi' => 'Terdapat tanda pada tekanan kerja maximum'),
                    array('no' => 52, 'nama_inspeksi' => 'Telah dilakukan kalibrasi'));

$dataKelengkapan3 = array(array('no' => 53, 'nama_inspeksi' => 'Dapat bekerja apabila tekanan pada bejana uap melebihi tekanan maksimum'), 
                    array('no' => 54, 'nama_inspeksi' => 'Diset maksimum pada tekanan kerja yang diijinkan'),
                    array('no' => 55, 'nama_inspeksi' => 'Telah diuji kemampuannya pada tekanan kerja yang diijinkan selama 10 menit dan tidak terjadi kenaikan pada tekanan bejana uap'));

$dataKelengkapan41 = array(array('no' => 56, 'nama_inspeksi' => 'Asli/telah diganti', 'placeholder' => 'keterangan'), 
                     array('no' => 57, 'nama_inspeksi' => 'Ukuran', 'placeholder' => '..mm x ..mm'));

$dataKelengkapan42 = array(array('no' => 58, 'nama_inspeksi' => 'Nama dan tempat pembuatan', 'placeholder' => 'keterangan'), 
                     array('no' => 59, 'nama_inspeksi' => 'Tahun pembuatan', 'placeholder' => 'tahun'),
                     array('no' => 60, 'nama_inspeksi' => 'Nomor serie', 'placeholder' => 'nomor'),
                     array('no' => 61, 'nama_inspeksi' => 'Tekanan kerja max yang diijinkan', 'placeholder' => 'kg/cm2'));

$dataKelengkapan5 = array(array('no' => 62, 'nama_inspeksi' => 'Nama dan tempat pembuatan', 'placeholder' => 'memanjang/melingkar kedalaman..mm'), 
                     array('no' => 63, 'nama_inspeksi' => 'Tekanan kerja max yang diijinkan', 'placeholder' => 'memanjang/melingkar kedalaman..mm'));

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
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <a href="<?php echo $base; ?>user">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="<?php echo $base; ?>user/inspeksi_berkala">
                            <i class="material-icons">content_paste</i>
                            <p>Inspeksi Berkala</p>
                        </a>
                    </li>
                    <li>
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
                        <a class="navbar-brand" href="#"> User Dashboard Inspeksi Berkala</a>
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
                                    <h5 class="title">Inspeksi Berkala</h5>
                                    <p class="category">Sebelum Masa Giling</p>
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
                                                <!-- <div class="col-md-4">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">NIP</label>
                                                        <input type="text" class="form-control" name="nip" readonly="" value="<?php echo $_SESSION['nip']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">TANGGAL</label>
                                                        <input type="date" class="form-control" name="tanggal" readonly="" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div> -->
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group label-floating">
                                                        <label style="color: black;" class="">NO FORM</label>

                                                        <?php
                                                            $no_form = mysqli_query($conn, "SELECT no_form FROM form_teknisi WHERE jenis = 'Berkala' ORDER BY ABS(SUBSTRING(no_form,4,LENGTH(no_form))) DESC LIMIT 1");
                                                            $noForm = mysqli_fetch_assoc($no_form);

                                                            if (isset($noForm)) {
                                                                $nomor = 'BR-'.(substr($noForm['no_form'], -(strlen($noForm['no_form'])-3)) + 1);
                                                            } else {
                                                                $nomor = 'BR-1';
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
                                                    <!-- Pertanyaan 1 sampai 4 -->
                                                    <?php $no = 1; foreach ($dataUmum as $value) { ?>                                  
                                                    <tr>
                                                        <td width="10px">
                                                            <?php echo $no; ?>
                                                            <input type="hidden" <?php echo "name=no".$value['no']; ?> value="<?php echo $value['no']; ?>">
                                                        </td>
                                                        <td><input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$value['no']; ?> style="width: 180px;"></td>
                                                        <td width="150px"><strong><?php echo $value['nama_inspeksi']; ?></strong></td>
                                                        <td width="120px">
                                                            <div class="form_group">
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio".''.$value['no']; ?> <?php echo "id=ya".''.$value['no']; ?> value="Ya" checked >Ya
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
                                                        <td>5</td>
                                                        <td colspan="6">Pipa masih memiliki ketebalan yang cukup</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 5 sampai 15 -->
                                                    <?php foreach ($dataUmum5 as $dtUmum5) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtUmum5['no']; ?> value="<?php echo $dtUmum5['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtUmum5['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtUmum5['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtUmum5['no']; ?> <?php echo "id=ya".''.$dtUmum5['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtUmum5['no']; ?> <?php echo "id=tidak".''.$dtUmum5['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtUmum5['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtUmum5['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtUmum5['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td>6</td>
                                                        <td colspan="6">Dokumen bejana uap</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 16 sampai 20 -->
                                                    <?php foreach ($dataUmum6 as $dtUmum6) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtUmum6['no']; ?> value="<?php echo $dtUmum6['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtUmum6['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtUmum6['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="130px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtUmum6['no']; ?> <?php echo "id=ada".''.$dtUmum6['no']; ?> value="Ada" checked >Ada
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtUmum6['no']; ?> <?php echo "id=tidak".''.$dtUmum6['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtUmum6['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtUmum6['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtUmum6['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td>7</td>
                                                        <td colspan="6">Pengesahan</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 21 sampai 22 -->
                                                    <?php foreach ($dataUmum7 as $dtUmum7) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtUmum7['no']; ?> value="<?php echo $dtUmum7['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtUmum7['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtUmum7['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtUmum7['no']; ?> <?php echo "id=ya".''.$dtUmum7['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtUmum7['no']; ?> <?php echo "id=tidak".''.$dtUmum7['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtUmum7['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtUmum7['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtUmum7['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="6"><strong>DATA BEJANA UAP</strong></td>
                                                    </tr>
                                                    <!-- Pertanyaan 23 -->
                                                    <tr>
                                                        <td width="10px">1
                                                            <input type="hidden" name="no23" value="23">
                                                        </td>
                                                        <td><input onchange="ValidateSize(this)" type="file" name="picture23" style="width: 180px;"></td>
                                                        <td width="150px"><strong>Bejana uap memiliki ijin sesuai peraturan</strong></td>
                                                        <td width="120px">
                                                            <div class="form_group">
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio23"; ?> <?php echo "id=ya23"; ?> value="Ya" checked >Ya
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" <?php echo "name=radio23"; ?> <?php echo "id=tidak23"; ?> value="Tidak">Tidak
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">kondisi</label>
                                                                <input type="text" class="form-control" name="kondisi23">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">keterangan</label>
                                                                <input type="text" class="form-control" name="keterangan23">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group label-floating">
                                                                <label class="control-label">rekomendasi</label>
                                                                <input type="text" class="form-control" name="rekomendasi23">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td colspan="6">Bejana uap dilakukan reparasi karena cacad/kerusakan</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 24 sampai 25 -->
                                                    <?php foreach ($dataBejana2 as $dtBjn2) { ?> 
                                                        <tr>
                                                            <td width="10px"></td>
                                                            <td>
                                                                <input type="hidden" <?php echo "name=no".$dtBjn2['no']; ?> value="<?php echo $dtBjn2['no']; ?>">
                                                                <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtBjn2['no']; ?> style="width: 180px;">
                                                            </td>
                                                            <td>
                                                                <strong><?php echo $dtBjn2['nama_inspeksi']; ?></strong>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">jawaban</label>
                                                                    <input type="text" class="form-control" <?php echo "name=radio".$dtBjn2['no']; ?>>
                                                                </div>
                                                            </td>
                                                            
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtBjn2['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label"><?php echo $dtBjn2['placeholder']; ?></label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtBjn2['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtBjn2['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td>3</td>
                                                        <td colspan="6">Data teknik bejana uap</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 26 sampai 39 -->
                                                    <?php foreach ($dataBejana3 as $dtBjn3) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtBjn3['no']; ?> value="<?php echo $dtBjn3['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtBjn3['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtBjn3['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td colspan="4">
                                                                    <div class="form-group label-floating">
                                                                        <label class="control-label">jawaban</label>
                                                                        <input type="text" class="form-control" <?php echo "name=radio".$dtBjn3['no']; ?>>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <!-- <label class="control-label">kondisi</label> -->
                                                                    <input type="hidden" class="form-control" <?php echo "name=kondisi".$dtBjn3['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <!-- <label class="control-label"><?php echo $dtBjn2['placeholder']; ?></label> -->
                                                                    <input type="hidden" class="form-control" <?php echo "name=keterangan".$dtBjn3['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <!-- <label class="control-label">rekomendasi</label> -->
                                                                    <input type="hidden" class="form-control" <?php echo "name=rekomendasi".$dtBjn3['no']; ?>>
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
                                                    <!-- Pertanyaan baris 40 sampai 48 -->
                                                    <?php foreach ($dataKelengkapan1 as $dtKel1) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel1['no']; ?> value="<?php echo $dtKel1['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtKel1['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel1['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel1['no']; ?> <?php echo "id=ya".''.$dtKel1['no']; ?> value="Ya" checked >Ya
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
                                                    <!-- Pertanyaan baris 49 sampai 52 -->
                                                    <?php foreach ($dataKelengkapan2 as $dtKel2) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel2['no']; ?> value="<?php echo $dtKel2['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtKel2['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel2['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel2['no']; ?> <?php echo "id=ya".''.$dtKel2['no']; ?> value="Ya" checked >Ya
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
                                                    <!-- Pertanyaan baris 53 sampai 55 -->
                                                    <?php foreach ($dataKelengkapan3 as $dtKel3) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel3['no']; ?> value="<?php echo $dtKel3['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtKel3['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel3['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel3['no']; ?> <?php echo "id=ya".''.$dtKel3['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel3['no']; ?> <?php echo "id=tidak".''.$dtKel3['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtKel3['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">keterangan</label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtKel3['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtKel3['no']; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td>4</td>
                                                        <td colspan="6">Pelat nama</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 56 sampai 57 -->
                                                    <?php foreach ($dataKelengkapan41 as $dtKel41) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel41['no']; ?> value="<?php echo $dtKel41['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtKel41['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel41['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel41['no']; ?> <?php echo "id=ya".''.$dtKel41['no']; ?> value="Ya" checked >Ya
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
                                                                    <label class="control-label"><?php echo $dtKel41['placeholder']; ?></label>
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
                                                    <!-- Pertanyaan baris 58 sampai 61 -->
                                                    <?php foreach ($dataKelengkapan42 as $dtKel42) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel42['no']; ?> value="<?php echo $dtKel42['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtKel42['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel42['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel42['no']; ?> <?php echo "id=ya".''.$dtKel42['no']; ?> value="Ya" checked >Ya
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
                                                                    <label class="control-label"><?php echo $dtKel42['placeholder']; ?></label>
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
                                                    <tr>
                                                        <td>5</td>
                                                        <td colspan="6">Korosi yang terjadi pada bagian dalam bejana uap</td>
                                                    </tr>
                                                    <!-- Pertanyaan baris 58 sampai 61 -->
                                                    <?php foreach ($dataKelengkapan5 as $dtKel5) { ?> 
                                                        <tr>
                                                            <td width="10px">
                                                                <td>
                                                                    <input type="hidden" <?php echo "name=no".$dtKel5['no']; ?> value="<?php echo $dtKel5['no']; ?>">
                                                                    <input onchange="ValidateSize(this)" type="file" <?php echo "name=picture".$dtKel5['no']; ?> style="width: 180px;">
                                                                </td>
                                                                <td>
                                                                    <strong><?php echo $dtKel5['nama_inspeksi']; ?></strong>
                                                                </td>
                                                                <td width="120px">
                                                                    <div class="form_group">
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel5['no']; ?> <?php echo "id=ya".''.$dtKel5['no']; ?> value="Ya" checked >Ya
                                                                        </label>
                                                                        <label class="radio-inline">
                                                                            <input type="radio" <?php echo "name=radio".''.$dtKel5['no']; ?> <?php echo "id=tidak".''.$dtKel5['no']; ?> value="Tidak">Tidak
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">kondisi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=kondisi".$dtKel5['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label"><?php echo $dtKel5['placeholder']; ?></label>
                                                                    <input type="text" class="form-control" <?php echo "name=keterangan".$dtKel5['no']; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group label-floating">
                                                                    <label class="control-label">rekomendasi</label>
                                                                    <input type="text" class="form-control" <?php echo "name=rekomendasi".$dtKel5['no']; ?>>
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

</html>