<?php 
include '../../config.php';

$no_form = $_POST['no_form'];
$jenis = 'Bulanan';
$no_tangki = $_POST['noTangki'];
$nip = $_POST['nip'];
$tanggal = date('Y-m-d');

if ($insert = mysqli_query($conn, "INSERT INTO form_teknisi(`no_form`, `jenis`, `no_tangki`, `tanggal`, `nip`, `status`) VALUES ('$no_form','$jenis','$no_tangki','$tanggal','$nip', 'Belum Disetujui');")) {
	for ($i=1; $i < 40; $i++) { 
		if($_FILES['picture'.$i]['size'] > 0 && $_FILES['picture'.$i]['error'] == 0){  
  
  			$tmpFile = fopen($_FILES['picture'.$i]['tmp_name'], 'rb');  
  			$fileData = fread($tmpFile, filesize($_FILES['picture'.$i]['tmp_name']));  
  			$fileData = addslashes($fileData);  

  			$kodeGambar = mysqli_query($conn, "SELECT kd_gmbar FROM gambar_teknisi ORDER BY ABS(kd_gmbar) DESC LIMIT 1");
            $kdGambar = mysqli_fetch_assoc($kodeGambar);

            if (isset($kdGambar)) {
            	$kode = (int)($kdGambar['kd_gmbar']) + 1;
            } else {
            	$kode = 1;
            }

            $noPertanyaan = $_POST['no'.$i];
            $jawaban = $_POST['radio'.$i];
            $kondisi = $_POST['kondisi'.$i];
            $keterangan = $_POST['keterangan'.$i];
            $rekomendasi = $_POST['rekomendasi'.$i];

            if(mysqli_query($conn,"INSERT INTO gambar_teknisi (kd_gmbar, gambar) VALUES ('$kode','$fileData');")){
            	mysqli_query($conn,"INSERT INTO hasil_form_teknisi (no_pertanyaan, no_form, kd_gambar, jawaban, kondisi, keterangan, rekomendasi) VALUES ('$noPertanyaan','$no_form','$kode','$jawaban','$kondisi','$keterangan','$rekomendasi');") or die(mysqli_error()); 
            }

  		} else {

  			$noPertanyaan = $_POST['no'.$i];
            $jawaban = $_POST['radio'.$i];
            $kondisi = $_POST['kondisi'.$i];
            $keterangan = $_POST['keterangan'.$i];
            $rekomendasi = $_POST['rekomendasi'.$i];

        	mysqli_query($conn,"INSERT INTO hasil_form_teknisi (no_pertanyaan, no_form, kd_gambar, jawaban, kondisi, keterangan, rekomendasi) VALUES ('$noPertanyaan','$no_form',NULL,'$jawaban','$kondisi','$keterangan','$rekomendasi');") or die(mysqli_error()); 
        }
  	}
  	session_start();
	$_SESSION['success_message'] = 'Data berhasil disimpan..';
	echo "<script>location.href='../inspeksi_bulanan';</script>";
} else {
	session_start();
	$_SESSION['failed_message'] = 'Data gagal disimpan..';
	echo "<script>location.href='../inspeksi_bulanan';</script>";
}

?>