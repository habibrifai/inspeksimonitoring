<?php

$conn = mysqli_connect('localhost','root','');

mysqli_select_db($conn, 'monitoring_inspeksi'); 

$noForm = $_POST['noform'];

require_once('fpdf/fpdf.php');

class myPDF extends FPDF{ 

	function headerDocument()
	{
		$this->SetFont('Times','B',13);
		$this->Cell(10);
		$this->Cell(260,7,'FORM INSPEKSI BERKALA (SEBELUM MASA GILING)',0,0,'C');
		$this->Ln();
		$this->Cell(10);
		$this->Cell(260,7,'TANGKI EVAPORATOR',0,0,'C');
		$this->Ln(20);
	}

	function headerContent()
	{
		$conn = mysqli_connect('localhost','root','');
		mysqli_select_db($conn, 'monitoring_inspeksi');

		$noTangki = $_POST['notangki'];

		$tangki = mysqli_query($conn,"SELECT uk_tangki, jenis_tangki FROM tangki WHERE no_tangki = '$noTangki'");
		$test = mysqli_query($conn, "SELECT * FROM tangki");
		while($row = mysqli_fetch_array($tangki)){
			$ukuran = $row['uk_tangki'];
			$jenis = $row['jenis_tangki'];
		}

		$this->SetFont('Times','',12);
		$this->Cell(10);
		$this->Cell(170,7,'NIP                            : '.$_POST['nip'],0,0,'L');
		$this->Cell(90,7,'TANGGAL : '.$_POST['tanggal'],0,0,'L');
		$this->Ln();
		$this->Cell(10);
		$this->Cell(170,7,'NO TANGKI            : '.$_POST['notangki'],0,0,'L');
		$this->Cell(90,7,'NO FORM  : '.$_POST['noform'],0,0,'L');
		$this->Ln();
		$this->Cell(10);
		$this->Cell(170,7,'JENIS                        : '.$jenis,0,0,'L');
		$this->Ln();
		$this->Cell(10);
		$this->Cell(170,7,'UKURAN TANGKI : '.$ukuran,0,0,'L');
		$this->Ln();
	}

	function headerTable()
	{
		$this->SetFont('Times','',12);
		$this->Cell(10);
		$this->Cell(10,8,'No',1,0,'C');
		$this->Cell(20,8,'Picture',1,0,'C'); 
		$this->Cell(55,8,'Permasalahan/Pertanyaan',1,0,'C'); 
		$this->Cell(30,8,'Hasil/Jawaban',1,0,'C');
		$this->Cell(50,8,'Kondisi',1,0,'C'); 
		$this->Cell(50,8,'Keterangan',1,0,'C'); 
		$this->Cell(50,8,'Rekomendasi',1,0,'C');  
		$this->Ln();
	}
}

$pdf = new myPDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');

$pdf->headerDocument();
$pdf->headerContent();
$pdf->headerTable();

$pdf->Cell(10);
$pdf->Cell(10,8,'',1,0,'C');
$pdf->Cell(255,8,'DATA UMUM BEJANA UAP',1,0,'L');
$pdf->Ln();

$data_inspeksi = mysqli_query($conn, "SELECT * FROM hasil_form_teknisi WHERE no_form = '$noForm'");
// var_dump($data);

while ($data = mysqli_fetch_array($data_inspeksi)) {
	$noPertanyaan[] = $data['no_pertanyaan'];
	// $picture[] = $data['']
	$jawaban[] = $data['jawaban'];
	$kondisi[] = $data['kondisi'];
	$keterangan[] = $data['keterangan'];
	$rekomendasi[] = $data['rekomendasi'];
}
$no = 1;
$pdf->SetFont('Times','',12);
$wp = 55;
$wk = 50;
$h = 7;
for ($i=1; $i <= 4; $i++) {

	if ($noPertanyaan[$i-1] == 1) {
		$pertanyaan = "Isolasi/Selubung badan bejana uap tidak terkelupas";
	}
	if ($noPertanyaan[$i-1] == 2) {
		$pertanyaan = "Tidak terdapat korosi/kerak pada dinding pipa";
	}
	if ($noPertanyaan[$i-1] == 3) {
		$pertanyaan = "Bejana uap digunakan pada tekanan yang diijinkan";
	}
	if ($noPertanyaan[$i-1] == 4) {
		$pertanyaan = "Instalasi listrik pada kontrol panel baik dan sesuai standar          ";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10,($newLine * $h),$no.'.',1,0,'C');
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();

	$no++;
}

// ini untuk nomer 5 pertama
for ($i=5; $i <= 15; $i++) {
	if ($noPertanyaan[$i-1] == 5) {
		$pertanyaan = "a. Pipa uap pemanas";
	}
	if ($noPertanyaan[$i-1] == 6) {
		$pertanyaan = "b. Pipa air masak soda";
	}
	if ($noPertanyaan[$i-1] == 7) {
		$pertanyaan = "c. Pipa masukan nira";
	}
	if ($noPertanyaan[$i-1] == 8) {
		$pertanyaan = "d. Pipa keluaran nira";
	}
	if ($noPertanyaan[$i-1] == 9) {
		$pertanyaan = "e. Pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 10) {
		$pertanyaan = "f. Pipa amonia";
	}
	if ($noPertanyaan[$i-1] == 11) {
		$pertanyaan = "g. Pipa pancingan vacuum";
	}
	if ($noPertanyaan[$i-1] == 12) {
		$pertanyaan = "h. Pipa uap nira";
	}
	if ($noPertanyaan[$i-1] == 13) {
		$pertanyaan = "i. Pipa tap nira";
	}
	if ($noPertanyaan[$i-1] == 14) {
		$pertanyaan = "j. Pipa tap soda";
	}
	if ($noPertanyaan[$i-1] == 15) {
		$pertanyaan = "k. Pipa jiwa";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width[] = $newLine;
	// echo $newLine;
}

$jmlLine = array_sum($width)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine * $h),'5.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Pipa masih memiliki ketebalan yang cukup',1,0,'L');
$pdf->Ln();

for ($i=5; $i <= 15; $i++) {
	if ($noPertanyaan[$i-1] == 5) {
		$pertanyaan = "a. Pipa uap pemanas";
	}
	if ($noPertanyaan[$i-1] == 6) {
		$pertanyaan = "b. Pipa air masak soda";
	}
	if ($noPertanyaan[$i-1] == 7) {
		$pertanyaan = "c. Pipa masukan nira";
	}
	if ($noPertanyaan[$i-1] == 8) {
		$pertanyaan = "d. Pipa keluaran nira";
	}
	if ($noPertanyaan[$i-1] == 9) {
		$pertanyaan = "e. Pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 10) {
		$pertanyaan = "f. Pipa amonia";
	}
	if ($noPertanyaan[$i-1] == 11) {
		$pertanyaan = "g. Pipa pancingan vacuum";
	}
	if ($noPertanyaan[$i-1] == 12) {
		$pertanyaan = "h. Pipa uap nira";
	}
	if ($noPertanyaan[$i-1] == 13) {
		$pertanyaan = "i. Pipa tap nira";
	}
	if ($noPertanyaan[$i-1] == 14) {
		$pertanyaan = "j. Pipa tap soda";
	}
	if ($noPertanyaan[$i-1] == 15) {
		$pertanyaan = "k. Pipa jiwa";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	// $pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	$pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// ini untuk nomer 6 pertama
for ($i=16; $i <= 20; $i++) {
	if ($noPertanyaan[$i-1] == 16) {
		$pertanyaan = "a. Gambar konstruksi lengkap";
	}
	if ($noPertanyaan[$i-1] == 17) {
		$pertanyaan = "b. Disahkan oleh bengkel konstruksi yang disahkan PJK3 fabrikasi";
	}
	if ($noPertanyaan[$i-1] == 18) {
		$pertanyaan = "c. Sertifikat bahan";
	}
	if ($noPertanyaan[$i-1] == 19) {
		$pertanyaan = "d. Tanda hasil NDT";
	}
	if ($noPertanyaan[$i-1] == 20) {
		$pertanyaan = "e. Kalibrasi alat-alat pengaman dan pelengkap";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width1[] = $newLine;
	// echo $newLine;
}

$jmlLine1 = array_sum($width1)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine1 * 7),'6.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Dokumen bejana uap',1,0,'L');
$pdf->Ln();

for ($i=16; $i <= 20; $i++) {
	if ($noPertanyaan[$i-1] == 16) {
		$pertanyaan = "a. Gambar konstruksi lengkap";
	}
	if ($noPertanyaan[$i-1] == 17) {
		$pertanyaan = "b. Disahkan oleh bengkel konstruksi yang disahkan PJK3 fabrikasi";
	}
	if ($noPertanyaan[$i-1] == 18) {
		$pertanyaan = "c. Sertifikat bahan";
	}
	if ($noPertanyaan[$i-1] == 19) {
		$pertanyaan = "d. Tanda hasil NDT";
	}
	if ($noPertanyaan[$i-1] == 20) {
		$pertanyaan = "e. Kalibrasi alat-alat pengaman dan pelengkap";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	// $xPos = $pdf->GetX();
	// $yPos = $pdf->GetY();
	// $pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	// $pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// ini untuk nomer 7 pertama
for ($i=21; $i <= 22; $i++) {
	if ($noPertanyaan[$i-1] == 21) {
		$pertanyaan = "a. Gambar rencana";
	}
	if ($noPertanyaan[$i-1] == 22) {
		$pertanyaan = "b. Pemakaian";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width2[] = $newLine;
	// echo $newLine;
}

$jmlLine2 = array_sum($width2)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine2 * 7),'7.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Pengesahan',1,0,'L');
$pdf->Ln();

for ($i=21; $i <= 22; $i++) {
	if ($noPertanyaan[$i-1] == 21) {
		$pertanyaan = "a. Gambar rencana";
	}
	if ($noPertanyaan[$i-1] == 22) {
		$pertanyaan = "b. Pemakaian";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	// $xPos = $pdf->GetX();
	// $yPos = $pdf->GetY();
	// $pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	// $pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// pembatas data bejana uap
$pdf->Cell(10);
$pdf->Cell(265,7,'',1,0,'C');
$pdf->Ln();

$pdf->Cell(10);
$pdf->Cell(10,7,'',1,0,'L');
$pdf->Cell(255,7,'DATA BEJANA UAP',1,0,'L');
$pdf->Ln();

// pertanyaan nomer 1 data bejana uap
for ($i=23; $i <= 23; $i++) {
	if ($noPertanyaan[$i-1] == 23) {
		$pertanyaan = "Bejana uap memiliki ijin sesuai peraturan";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10,($newLine * $h),'1.',1,0,'C'); 
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// pertanyaan nomer 2 data bejana uap
for ($i=24; $i <= 25; $i++) {
	if ($noPertanyaan[$i-1] == 24) {
		$pertanyaan = "a. Pengesahan gambar rencana reparasi";
	}
	if ($noPertanyaan[$i-1] == 25) {
		$pertanyaan = "b. Dikerjakan bengkel konstruksi yang telas disahkan Depnaker";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width3[] = $newLine;
	// echo $newLine;
}

$jmlLine3 = array_sum($width3)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine3 * 7),'2.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Bejana uap dilakukan reparasi karena cacad/kerusakan',1,0,'L');
$pdf->Ln();

for ($i=24; $i <= 25; $i++) {
	if ($noPertanyaan[$i-1] == 24) {
		$pertanyaan = "a. Pengesahan gambar rencana reparasi";
	}
	if ($noPertanyaan[$i-1] == 25) {
		$pertanyaan = "b. Dikerjakan bengkel konstruksi yang telas disahkan Depnaker ";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// pertanyaan nomer 3 data bejana uap
for ($i=26; $i <= 39; $i++) {
	if ($noPertanyaan[$i-1] == 26) {
		$pertanyaan = "a. Nomer Serie";
	}
	if ($noPertanyaan[$i-1] == 27) {
		$pertanyaan = "b. Tempat pembuatan";
	}
	if ($noPertanyaan[$i-1] == 28) {
		$pertanyaan = "c. Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 29) {
		$pertanyaan = "d. Tekanan kerja max";
	}
	if ($noPertanyaan[$i-1] == 30) {
		$pertanyaan = "e. Tinggi badan";
	}
	if ($noPertanyaan[$i-1] == 31) {
		$pertanyaan = "f. Diameter badan";
	}
	if ($noPertanyaan[$i-1] == 32) {
		$pertanyaan = "g. Luas pemanas";
	}
	if ($noPertanyaan[$i-1] == 33) {
		$pertanyaan = "h. Diameter pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 34) {
		$pertanyaan = "i. Panjang pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 35) {
		$pertanyaan = "j. Jumlah pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 36) {
		$pertanyaan = "k. Diameter pipa jiwa";
	}
	if ($noPertanyaan[$i-1] == 37) {
		$pertanyaan = "l. Jumlah pipa amoniak";
	}
	if ($noPertanyaan[$i-1] == 38) {
		$pertanyaan = "m. Isi";
	}
	if ($noPertanyaan[$i-1] == 39) {
		$pertanyaan = "n. Bahan";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($jawaban[$i-1]) < 180) {
		$line1=1;
	} else {
		$textLength1 = strlen($jawaban[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < (180 - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($jawaban[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width4[] = $newLine;
	// echo $newLine;
}

$jmlLine4 = array_sum($width4)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine4 * 7),'3.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Data teknik bejana uap',1,0,'L');
$pdf->Ln();

for ($i=26; $i <= 39; $i++) {
	if ($noPertanyaan[$i-1] == 26) {
		$pertanyaan = "a. Nomer Serie";
	}
	if ($noPertanyaan[$i-1] == 27) {
		$pertanyaan = "b. Tempat pembuatan";
	}
	if ($noPertanyaan[$i-1] == 28) {
		$pertanyaan = "c. Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 29) {
		$pertanyaan = "d. Tekanan kerja max";
	}
	if ($noPertanyaan[$i-1] == 30) {
		$pertanyaan = "e. Tinggi badan";
	}
	if ($noPertanyaan[$i-1] == 31) {
		$pertanyaan = "f. Diameter badan";
	}
	if ($noPertanyaan[$i-1] == 32) {
		$pertanyaan = "g. Luas pemanas";
	}
	if ($noPertanyaan[$i-1] == 33) {
		$pertanyaan = "h. Diameter pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 34) {
		$pertanyaan = "i. Panjang pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 35) {
		$pertanyaan = "j. Jumlah pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 36) {
		$pertanyaan = "k. Diameter pipa jiwa";
	}
	if ($noPertanyaan[$i-1] == 37) {
		$pertanyaan = "l. Jumlah pipa amoniak";
	}
	if ($noPertanyaan[$i-1] == 38) {
		$pertanyaan = "m. Isi";
	}
	if ($noPertanyaan[$i-1] == 39) {
		$pertanyaan = "n. Bahan";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($jawaban[$i-1]) < 180) {
		$line1=1;
	} else {
		$textLength1 = strlen($jawaban[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < (180 - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($jawaban[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	// $pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell(180,$h,$jawaban[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + 180 , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// pembatas kelengkapan/alat bantu operasi
$pdf->Cell(10);
$pdf->Cell(265,7,'',1,0,'C');
$pdf->Ln();

$pdf->Cell(10);
$pdf->Cell(10,7,'',1,0,'L');
$pdf->Cell(255,7,'KELENGKAPAN/ALAT BANTU OPERASI',1,0,'L');
$pdf->Ln();

// ini untuk nomer 1 alat bantu operasi
for ($i=40; $i <= 48; $i++) {
	if ($noPertanyaan[$i-1] == 40) {
		$pertanyaan = "a. Pondasi";
	}
	if ($noPertanyaan[$i-1] == 41) {
		$pertanyaan = "b. Support/penompang";
	}
	if ($noPertanyaan[$i-1] == 42) {
		$pertanyaan = "c. Anchort Bolt";
	}
	if ($noPertanyaan[$i-1] == 43) {
		$pertanyaan = "d. Penutup Isolasi";
	}
	if ($noPertanyaan[$i-1] == 44) {
		$pertanyaan = "e. Safety Valve";
	}
	if ($noPertanyaan[$i-1] == 45) {
		$pertanyaan = "f. Pressure gauge";
	}
	if ($noPertanyaan[$i-1] == 46) {
		$pertanyaan = "g. Thermometer clock";
	}
	if ($noPertanyaan[$i-1] == 47) {
		$pertanyaan = "h. Sight glass";
	}
	if ($noPertanyaan[$i-1] == 48) {
		$pertanyaan = "i. Pelat nama";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width5[] = $newLine;
	// echo $newLine;
}

$jmlLine5 = array_sum($width5)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine5 * $h),'1.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Apakah bejana uap dilengkapi dengan:',1,0,'L');
$pdf->Ln();

for ($i=40; $i <= 48; $i++) {
	if ($noPertanyaan[$i-1] == 40) {
		$pertanyaan = "a. Pondasi";
	}
	if ($noPertanyaan[$i-1] == 41) {
		$pertanyaan = "b. Support/penompang";
	}
	if ($noPertanyaan[$i-1] == 42) {
		$pertanyaan = "c. Anchort Bolt";
	}
	if ($noPertanyaan[$i-1] == 43) {
		$pertanyaan = "d. Penutup Isolasi";
	}
	if ($noPertanyaan[$i-1] == 44) {
		$pertanyaan = "e. Safety Valve";
	}
	if ($noPertanyaan[$i-1] == 45) {
		$pertanyaan = "f. Pressure gauge";
	}
	if ($noPertanyaan[$i-1] == 46) {
		$pertanyaan = "g. Thermometer clock";
	}
	if ($noPertanyaan[$i-1] == 47) {
		$pertanyaan = "h. Sight glass";
	}
	if ($noPertanyaan[$i-1] == 48) {
		$pertanyaan = "i. Pelat nama";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	if ($pertanyaan == "e. Safety Valve" || $pertanyaan == "f. Pressure gauge" || $pertanyaan == "g. Thermometer clock") {
		$pdf->SetFont('Times','I',12);
	}

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	// $pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	$pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// ini untuk nomer 2 alat bantu operasi
for ($i=49; $i <= 52; $i++) {
	if ($noPertanyaan[$i-1] == 49) {
		$pertanyaan = "a. Dipasang pada bagian bejana yang mudah dilihat oleh operator";
	}
	if ($noPertanyaan[$i-1] == 50) {
		$pertanyaan = "b. Apakah dapat menunjukkan tekanan kerja yang diperbolehkan";
	}
	if ($noPertanyaan[$i-1] == 51) {
		$pertanyaan = "c. Terdapat tanda pada tekanan kerja maximum";
	}
	if ($noPertanyaan[$i-1] == 52) {
		$pertanyaan = "d. Telah dilakukan kalibrasi";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width6[] = $newLine;
	// echo $newLine;
}

$jmlLine6 = array_sum($width6)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine6 * $h),'2.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->SetFont('Times','I',12);
$pdf->Cell(235,7,'Pressure gauge',1,0,'L');
$pdf->Ln();

for ($i=49; $i <= 52; $i++) {
	if ($noPertanyaan[$i-1] == 49) {
		$pertanyaan = "a. Dipasang pada bagian bejana yang mudah dilihat oleh operator";
	}
	if ($noPertanyaan[$i-1] == 50) {
		$pertanyaan = "b. Apakah dapat menunjukkan tekanan kerja yang diperbolehkan";
	}
	if ($noPertanyaan[$i-1] == 51) {
		$pertanyaan = "c. Terdapat tanda pada tekanan kerja maximum";
	}
	if ($noPertanyaan[$i-1] == 52) {
		$pertanyaan = "d. Telah dilakukan kalibrasi";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	// $xPos = $pdf->GetX();
	// $yPos = $pdf->GetY();
	// $pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	// $pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// ini untuk nomer 3 alat bantu operasi
for ($i=53; $i <= 55; $i++) {
	if ($noPertanyaan[$i-1] == 53) {
		$pertanyaan = "a. Dapat bekerja apabila tekanan pada bejana uap melebihi tekanan maksimum";
	}
	if ($noPertanyaan[$i-1] == 54) {
		$pertanyaan = "b. Diset maksimum pada tekanan kerja yang diijinkan";
	}
	if ($noPertanyaan[$i-1] == 55) {
		$pertanyaan = "c. Telah diuji kemampuannya pada tekanan kerja yang diijinkan selama 10 menit dan tidak terjadi kenaikan pada tekanan bejana uap";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width7[] = $newLine;
	// echo $newLine;
}

$jmlLine7 = array_sum($width7)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine7 * $h),'3.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->SetFont('Times','I',12);
$pdf->Cell(235,7,'Savety valve',1,0,'L');
$pdf->Ln();

for ($i=53; $i <= 55; $i++) {
	if ($noPertanyaan[$i-1] == 53) {
		$pertanyaan = "a. Dapat bekerja apabila tekanan pada bejana uap melebihi tekanan maksimum";
	}
	if ($noPertanyaan[$i-1] == 54) {
		$pertanyaan = "b. Diset maksimum pada tekanan kerja yang diijinkan";
	}
	if ($noPertanyaan[$i-1] == 55) {
		$pertanyaan = "c. Telah diuji kemampuannya pada tekanan kerja yang diijinkan selama 10 menit dan tidak terjadi kenaikan pada tekanan bejana uap";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	// $xPos = $pdf->GetX();
	// $yPos = $pdf->GetY();
	// $pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	// $pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// ini untuk nomer 4 alat bantu operasi
for ($i=56; $i <= 61; $i++) {
	if ($noPertanyaan[$i-1] == 56) {
		$pertanyaan = "a. Asli/telah diganti";
	}
	if ($noPertanyaan[$i-1] == 57) {
		$pertanyaan = "b. Ukuran";
	}
	if ($noPertanyaan[$i-1] == 58) {
		$pertanyaan = "c. Memuat identitas bejana uap"."\n"."  - Nama dan tempat pembuatan";
	}
	if ($noPertanyaan[$i-1] == 59) {
		$pertanyaan = "  - Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 60) {
		$pertanyaan = "  - Nomor serie";
	}
	if ($noPertanyaan[$i-1] == 61) {
		$pertanyaan = "  - Tekanan kerja max yang diijinkan";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width8[] = $newLine;
	// echo $newLine;
}

$jmlLine8 = array_sum($width8)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine8 * $h),'4.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
// $pdf->SetFont('Times','I',12);
$pdf->Cell(235,7,'Pelat nama',1,0,'L');
$pdf->Ln();

for ($i=56; $i <= 61; $i++) {
	if ($noPertanyaan[$i-1] == 56) {
		$pertanyaan = "a. Asli/telah diganti";
	}
	if ($noPertanyaan[$i-1] == 57) {
		$pertanyaan = "b. Ukuran";
	}
	if ($noPertanyaan[$i-1] == 58) {
		$pertanyaan = "c. Memuat identitas bejana uap"."\n"."  - Nama dan tempat pembuatan";
	}
	if ($noPertanyaan[$i-1] == 59) {
		$pertanyaan = "  - Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 60) {
		$pertanyaan = "  - Nomor serie";
	}
	if ($noPertanyaan[$i-1] == 61) {
		$pertanyaan = "  - Tekanan kerja max yang diijinkan";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	// $xPos = $pdf->GetX();
	// $yPos = $pdf->GetY();
	// $pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	// $pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

// ini untuk nomer 5 alat bantu operasi
for ($i=62; $i <= 63; $i++) {
	if ($noPertanyaan[$i-1] == 62) {
		$pertanyaan = "a. Pada Badan";
	}
	if ($noPertanyaan[$i-1] == 63) {
		$pertanyaan = "b. Pada Front";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$newLine = $line3;

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
	}

	$width9[] = $newLine;
	// echo $newLine;
}

$jmlLine9 = array_sum($width9)+1;

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(10,($jmlLine9 * $h),'5.',1,0,'C');
$pdf->Cell(20,(1 * $h),'',1,0,'C');
$pdf->Cell(235,7,'Korosi yang terjadi pada bagian dalam bejana uap',1,0,'L');
$pdf->Ln();

for ($i=62; $i <= 63; $i++) {
	if ($noPertanyaan[$i-1] == 62) {
		$pertanyaan = "a. Pada Badan";
	}
	if ($noPertanyaan[$i-1] == 63) {
		$pertanyaan = "b. Pada Front";
	}

	if ($pdf->GetStringWidth($pertanyaan) < $wp) {
		$line=1;
	} else {
		$textLength = strlen($pertanyaan);
		$errMargin = 8;
		$startChar = 0;
		$maxChar = 0;
		$textArray = Array();
		$tmpString = "";

		while ($startChar < $textLength) {
			while ($pdf->GetStringWidth($tmpString) < ($wp - $errMargin) && ($startChar+$maxChar) < $textLength) {
				$maxChar++;
				$tmpString = substr($pertanyaan,$startChar,$maxChar);
			}
			$startChar = $startChar+$maxChar;
			array_push($textArray, $tmpString);

			$maxChar = 0;
			$tmpString = '';
		}
		$line = count($textArray);
	}

	if ($pdf->GetStringWidth($kondisi[$i-1]) < $wk) {
		$line1=1;
	} else {
		$textLength1 = strlen($kondisi[$i-1]);
		$errMargin1 = 8;
		$startChar1 = 0;
		$maxChar1 = 0;
		$textArray1 = Array();
		$tmpString1 = "";

		while ($startChar1 < $textLength1) {
			while ($pdf->GetStringWidth($tmpString1) < ($wk - $errMargin1) && ($startChar1+$maxChar1) < $textLength1) {
				$maxChar1++;
				$tmpString1 = substr($kondisi[$i-1],$startChar1,$maxChar1);
			}
			$startChar1 = $startChar1+$maxChar1;
			array_push($textArray1, $tmpString1);

			$maxChar1 = 0;
			$tmpString1 = '';
		}
		$line1 = count($textArray1);
	}

	if ($pdf->GetStringWidth($keterangan[$i-1]) < $wk) {
		$line2=1;
	} else {
		$textLength2 = strlen($keterangan[$i-1]);
		$errMargin2 = 8;
		$startChar2 = 0;
		$maxChar2 = 0;
		$textArray2 = Array();
		$tmpString2 = "";

		while ($startChar2 < $textLength2) {
			while ($pdf->GetStringWidth($tmpString2) < ($wk - $errMargin2) && ($startChar2+$maxChar2) < $textLength2) {
				$maxChar2++;
				$tmpString2 = substr($keterangan[$i-1],$startChar2,$maxChar2);
			}
			$startChar2 = $startChar2+$maxChar2;
			array_push($textArray2, $tmpString2);

			$maxChar2 = 0;
			$tmpString2 = '';
		}
		$line2 = count($textArray2);
	}

	if ($pdf->GetStringWidth($rekomendasi[$i-1]) < $wk) {
		$line3=1;
	} else {
		$textLength3 = strlen($rekomendasi[$i-1]);
		$errMargin3 = 8;
		$startChar3 = 0;
		$maxChar3 = 0;
		$textArray3 = Array();
		$tmpString3 = "";

		while ($startChar3 < $textLength3) {
			while ($pdf->GetStringWidth($tmpString3) < ($wk - $errMargin3) && ($startChar3+$maxChar3) < $textLength3) {
				$maxChar3++;
				$tmpString3 = substr($rekomendasi[$i-1],$startChar3,$maxChar3);
			}
			$startChar3 = $startChar3+$maxChar3;
			array_push($textArray3, $tmpString3);

			$maxChar3 = 0;
			$tmpString3 = '';
		}
		$line3 = count($textArray3);
	}

	// jika kolom permasalahan lebih besar daripada kolom lain
	if (($line > $line1) && ($line > $line2) && ($line > $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 > $line) && ($line1 > $line2) && ($line1 > $line3)) {
		$ls1 = $line1 - $line;
		$ls2 = $line1 - $line2;
		$ls3 = $line1 - $line3;
		$newLine = $line1;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 > $line) && ($line2 > $line1) && ($line2 > $line3)) {
		$ls1 = $line2 - $line;
		$ls2 = $line2 - $line1;
		$ls3 = $line2 - $line3;
		$newLine = $line2;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacer'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacer'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacer'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacer'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacer'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacer'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 > $line) && ($line3 > $line1) && ($line3 > $line2)) {
		$ls1 = $line3 - $line;
		$ls2 = $line3 - $line1;
		$ls3 = $line3 - $line2;
		$newLine = $line3;
		$spacep = " ";
		$spacet = " ";
		$spacek = " ";
		$spacer = " ";

		if ($ls1 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls1 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls1 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls1 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls1 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls1 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls1 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls1 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls2 == 1) {
			$GLOBALS['spacek'] = "\n ";
		}
		if ($ls2 == 2) {
			$GLOBALS['spacek'] = "\n\n ";
		}
		if ($ls2 == 3) {
			$GLOBALS['spacek'] = "\n\n\n ";
		}
		if ($ls2 == 4) {
			$GLOBALS['spacek'] = "\n\n\n\n ";
		}
		if ($ls2 == 5) {
			$GLOBALS['spacek'] = "\n\n\n\n\n ";
		}
		if ($ls2 == 6) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n ";
		}
		if ($ls2 == 7) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 8) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 9) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls2 == 10) {
			$GLOBALS['spacek'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

		if ($ls3 == 1) {
			$GLOBALS['spacet'] = "\n ";
		}
		if ($ls3 == 2) {
			$GLOBALS['spacet'] = "\n\n ";
		}
		if ($ls3 == 3) {
			$GLOBALS['spacet'] = "\n\n\n ";
		}
		if ($ls3 == 4) {
			$GLOBALS['spacet'] = "\n\n\n\n ";
		}
		if ($ls3 == 5) {
			$GLOBALS['spacet'] = "\n\n\n\n\n ";
		}
		if ($ls3 == 6) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n ";
		}
		if ($ls3 == 7) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 8) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 9) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls3 == 10) {
			$GLOBALS['spacet'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

	// jika semua kolom memiliki besar yang sama
	} elseif ($line == $line1 && $line2 && $line3) {
		$newLine = $line1;
		$spacep = " ";
		$spacek = " ";
		$spacet = " ";
		$spacer = " ";
	}

	$pdf->SetFont('Times','',12);
	$pdf->Cell(10);
	$pdf->Cell(10);
	$pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	$pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');

	// $xPos = $pdf->GetX();
	// $yPos = $pdf->GetY();
	// $pdf->MultiCell(30,$h,$jawaban[$i-1].$spacep,1,'C'); 
	// $pdf->SetXY($xPos + 30 , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$kondisi[$i-1].$spacek,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$keterangan[$i-1].$spacet,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wk,$h,$rekomendasi[$i-1].$spacer,1,'L'); 
	$pdf->SetXY($xPos + $wk , $yPos);

	$pdf->Cell(0,($newLine * $h), '',0,0);

	$pdf->Ln();
}

$pdf->Output('test.pdf','I');

?>