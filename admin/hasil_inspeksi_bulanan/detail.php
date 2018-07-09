<?php

$conn = mysqli_connect('localhost','root','');

mysqli_select_db($conn, 'monitoring_inspeksi'); 

$noForm = $_POST['noform'];

// require('mem_image.php');
require_once('fpdf/fpdf.php');

class VariableStream
{
    private $varname;
    private $position;

    function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        if(!isset($GLOBALS[$this->varname]))
        {
            trigger_error('Global variable '.$this->varname.' does not exist', E_USER_WARNING);
            return false;
        }
        $this->position = 0;
        return true;
    }

    function stream_read($count)
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    function stream_eof()
    {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_seek($offset, $whence)
    {
        if($whence==SEEK_SET)
        {
            $this->position = $offset;
            return true;
        }
        return false;
    }
    
    function stream_stat()
    {
        return array();
    }
}

class myPDF extends FPDF { 

	function __construct($orientation='P', $unit='mm', $format='A4')
    {
        parent::__construct($orientation, $unit, $format);
        // Register var stream protocol
        stream_wrapper_register('var', 'VariableStream');
    }

    function MemImage($data, $x=null, $y=null, $w=0, $h=0, $link='')
    {
        // Display the image contained in $data
        $v = 'img'.md5($data);
        $GLOBALS[$v] = $data;
        $a = getimagesize('var://'.$v);
        if(!$a)
            $this->Error('Invalid image data');
        $type = substr(strstr($a['mime'],'/'),1);
        $this->Image('var://'.$v, $x, $y, $w, $h, $type, $link);
        unset($GLOBALS[$v]);
    }

    function GDImage($im, $x=null, $y=null, $w=0, $h=0, $link='')
    {
        // Display the GD image associated with $im
        ob_start();
        imagepng($im);
        $data = ob_get_clean();
        $this->MemImage($data, $x, $y, $w, $h, $link);
    }

	function headerDocument()
	{
		$this->SetFont('Times','B',13);
		$this->Cell(10);
		$this->Cell(260,7,'FORM INSPEKSI 2 MINGGU',0,0,'C');
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
		// $test = mysqli_query($conn, "SELECT * FROM tangki");
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
		$this->Cell(30,8,'Picture',1,0,'C'); 
		$this->Cell(45,8,'Permasalahan/Pertanyaan',1,0,'C'); 
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

$data_inspeksi = mysqli_query($conn, "SELECT * FROM hasil_form_teknisi LEFT JOIN gambar_teknisi ON hasil_form_teknisi.kd_gambar = gambar_teknisi.kd_gmbar WHERE no_form = '$noForm'");
while ($data = mysqli_fetch_array($data_inspeksi)) {
	$noPertanyaan[] = $data['no_pertanyaan'];
	$picture[] = $data['gambar'];
	$jawaban[] = $data['jawaban'];
	$kondisi[] = $data['kondisi'];
	$keterangan[] = $data['keterangan'];
	$rekomendasi[] = $data['rekomendasi'];
}
$no = 1;
$pdf->SetFont('Times','',12);
$wp = 45;
$wk = 50;
$h = 7;
$wpict = 30;

// 5 pertanyaan pertama
for ($i=1; $i <= 5; $i++) {

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
	if ($noPertanyaan[$i-1] == 5) {
		$pertanyaan = "Telah dilakukan pembersihan tangki bejana uap";
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
		$ls1 = $line - $line1;
		$ls2 = $line - $line2;
		$ls3 = $line - $line3;
		$newLine = $line;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls1 == 0) {
			$GLOBALS['spacek'] = "    ";
		}
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	// $pdf->Image(base64_encode($picture[$i-1]), 30, 30);
	
	// $pdf->Cell(20,($newLine * $h),$pdf->MemImage($picture[0],NULL,NULL,10,10),1,0,'C');
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

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

// pembatas data bejana uap
$pdf->Cell(10);
$pdf->Cell(265,7,'',1,0,'C');
$pdf->Ln();

$pdf->Cell(10);
$pdf->Cell(10,7,'',1,0,'L');
$pdf->Cell(255,7,'DATA BEJANA UAP',1,0,'L');
$pdf->Ln();

// pertanyaan nomer 1 data bejana uap
for ($i=6; $i <= 6; $i++) {
	if ($noPertanyaan[$i-1] == 6) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

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
for ($i=7; $i <= 20; $i++) {
	if ($noPertanyaan[$i-1] == 7) {
		$pertanyaan = "a. Nomer Serie";
	}
	if ($noPertanyaan[$i-1] == 8) {
		$pertanyaan = "b. Tempat pembuatan";
	}
	if ($noPertanyaan[$i-1] == 9) {
		$pertanyaan = "c. Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 10) {
		$pertanyaan = "d. Tekanan kerja max";
	}
	if ($noPertanyaan[$i-1] == 11) {
		$pertanyaan = "e. Tinggi badan";
	}
	if ($noPertanyaan[$i-1] == 12) {
		$pertanyaan = "f. Diameter badan";
	}
	if ($noPertanyaan[$i-1] == 13) {
		$pertanyaan = "g. Luas pemanas";
	}
	if ($noPertanyaan[$i-1] == 14) {
		$pertanyaan = "h. Diameter pipa pemanas           ";
	}
	if ($noPertanyaan[$i-1] == 15) {
		$pertanyaan = "i. Panjang pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 16) {
		$pertanyaan = "j. Jumlah pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 17) {
		$pertanyaan = "k. Diameter pipa jiwa";
	}
	if ($noPertanyaan[$i-1] == 18) {
		$pertanyaan = "l. Jumlah pipa amoniak";
	}
	if ($noPertanyaan[$i-1] == 19) {
		$pertanyaan = "m. Isi";
	}
	if ($noPertanyaan[$i-1] == 20) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$newLine = $linex;

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
$pdf->Cell(10,($jmlLine4 * 7),'2.',1,0,'C');
$pdf->Cell(30,(1 * $h),'',1,0,'C');
$pdf->Cell(225,7,'Data teknik bejana uap',1,0,'L');
$pdf->Ln();

for ($i=7; $i <= 20; $i++) {
	if ($noPertanyaan[$i-1] == 7) {
		$pertanyaan = "a. Nomer Serie";
	}
	if ($noPertanyaan[$i-1] == 8) {
		$pertanyaan = "b. Tempat pembuatan";
	}
	if ($noPertanyaan[$i-1] == 9) {
		$pertanyaan = "c. Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 10) {
		$pertanyaan = "d. Tekanan kerja max";
	}
	if ($noPertanyaan[$i-1] == 11) {
		$pertanyaan = "e. Tinggi badan";
	}
	if ($noPertanyaan[$i-1] == 12) {
		$pertanyaan = "f. Diameter badan";
	}
	if ($noPertanyaan[$i-1] == 13) {
		$pertanyaan = "g. Luas pemanas";
	}
	if ($noPertanyaan[$i-1] == 14) {
		$pertanyaan = "h. Diameter pipa pemanas       ";
	}
	if ($noPertanyaan[$i-1] == 15) {
		$pertanyaan = "i. Panjang pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 16) {
		$pertanyaan = "j. Jumlah pipa pemanas";
	}
	if ($noPertanyaan[$i-1] == 17) {
		$pertanyaan = "k. Diameter pipa jiwa";
	}
	if ($noPertanyaan[$i-1] == 18) {
		$pertanyaan = "l. Jumlah pipa amoniak";
	}
	if ($noPertanyaan[$i-1] == 19) {
		$pertanyaan = "m. Isi";
	}
	if ($noPertanyaan[$i-1] == 20) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	// $pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

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
for ($i=21; $i <= 29; $i++) {
	if ($noPertanyaan[$i-1] == 21) {
		$pertanyaan = "a. Pondasi";
	}
	if ($noPertanyaan[$i-1] == 22) {
		$pertanyaan = "b. Support/penompang";
	}
	if ($noPertanyaan[$i-1] == 23) {
		$pertanyaan = "c. Anchort Bolt";
	}
	if ($noPertanyaan[$i-1] == 24) {
		$pertanyaan = "d. Penutup Isolasi";
	}
	if ($noPertanyaan[$i-1] == 25) {
		$pertanyaan = "e. Safety Valve";
	}
	if ($noPertanyaan[$i-1] == 26) {
		$pertanyaan = "f. Pressure gauge";
	}
	if ($noPertanyaan[$i-1] == 27) {
		$pertanyaan = "g. Thermometer clock";
	}
	if ($noPertanyaan[$i-1] == 28) {
		$pertanyaan = "h. Sight glass";
	}
	if ($noPertanyaan[$i-1] == 29) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$newLine = $linex;

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
$pdf->Cell(30,(1 * $h),'',1,0,'C');
$pdf->Cell(225,7,'Apakah bejana uap dilengkapi dengan:',1,0,'L');
$pdf->Ln();

for ($i=21; $i <= 29; $i++) {
	if ($noPertanyaan[$i-1] == 21) {
		$pertanyaan = "a. Pondasi";
	}
	if ($noPertanyaan[$i-1] == 22) {
		$pertanyaan = "b. Support/penompang";
	}
	if ($noPertanyaan[$i-1] == 23) {
		$pertanyaan = "c. Anchort Bolt";
	}
	if ($noPertanyaan[$i-1] == 24) {
		$pertanyaan = "d. Penutup Isolasi";
	}
	if ($noPertanyaan[$i-1] == 25) {
		$pertanyaan = "e. Safety Valve";
	}
	if ($noPertanyaan[$i-1] == 26) {
		$pertanyaan = "f. Pressure gauge";
	}
	if ($noPertanyaan[$i-1] == 27) {
		$pertanyaan = "g. Thermometer clock";
	}
	if ($noPertanyaan[$i-1] == 28) {
		$pertanyaan = "h. Sight glass";
	}
	if ($noPertanyaan[$i-1] == 29) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	// $pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

	if ($pertanyaan == "e. Safety Valve" || $pertanyaan == "f. Pressure gauge" || $pertanyaan == "g. Thermometer clock") {
		$pdf->SetFont('Times','I',12);
	}

	$xPos = $pdf->GetX();
	$yPos = $pdf->GetY();
	$pdf->MultiCell($wp,$h,$pertanyaan.$spacep,1,'L');
	$pdf->SetXY($xPos + $wp , $yPos);

	// $pdf->Cell(30,($newLine * $h),$jawaban[$i-1],1,0,'C');
	$pdf->SetFont('Times','',12);
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
for ($i=30; $i <= 32; $i++) {
	if ($noPertanyaan[$i-1] == 30) {
		$pertanyaan = "a. Dipasang pada bagian bejana yang mudah dilihat oleh operator";
	}
	if ($noPertanyaan[$i-1] == 31) {
		$pertanyaan = "b. Apakah dapat menunjukkan tekanan kerja yang diperbolehkan               ";
	}
	if ($noPertanyaan[$i-1] == 32) {
		$pertanyaan = "c. Terdapat tanda pada tekanan kerja maximum       ";
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$newLine = $linex;

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
$pdf->Cell(30,(1 * $h),'',1,0,'C');
$pdf->SetFont('Times','I',12);
$pdf->Cell(225,7,'Pressure gauge',1,0,'L');
$pdf->Ln();

for ($i=30; $i <= 32; $i++) {
	if ($noPertanyaan[$i-1] == 30) {
		$pertanyaan = "a. Dipasang pada bagian bejana yang mudah dilihat oleh operator";
	}
	if ($noPertanyaan[$i-1] == 31) {
		$pertanyaan = "b. Apakah dapat menunjukkan tekanan kerja yang diperbolehkan             ";
	}
	if ($noPertanyaan[$i-1] == 32) {
		$pertanyaan = "c. Terdapat tanda pada tekanan kerja maximum       ";
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	// $pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

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
for ($i=33; $i <= 33; $i++) {
	if ($noPertanyaan[$i-1] == 33) {
		$pertanyaan = "a. Dapat bekerja apabila tekanan pada bejana uap melebihi tekanan maximum";
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$newLine = $linex;

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
$pdf->Cell(30,(1 * $h),'',1,0,'C');
$pdf->SetFont('Times','I',12);
$pdf->Cell(225,7,'Savety valve',1,0,'L');
$pdf->Ln();

for ($i=33; $i <= 33; $i++) {
	if ($noPertanyaan[$i-1] == 33) {
		$pertanyaan = "a. Dapat bekerja apabila tekanan pada bejana uap melebihi tekanan maximum";
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	// $pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

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

// ini untuk nomer 4 alat bantu operasi
for ($i=34; $i <= 39; $i++) {
	if ($noPertanyaan[$i-1] == 34) {
		$pertanyaan = "a. Asli/telah diganti";
	}
	if ($noPertanyaan[$i-1] == 35) {
		$pertanyaan = "b. Ukuran";
	}
	if ($noPertanyaan[$i-1] == 36) {
		$pertanyaan = "c. Memuat identitas bejana uap"."\n"."  - Nama dan tempat pembuatan         ";
	}
	if ($noPertanyaan[$i-1] == 37) {
		$pertanyaan = "  - Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 38) {
		$pertanyaan = "  - Nomor serie";
	}
	if ($noPertanyaan[$i-1] == 39) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$newLine = $linex;

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
		$newLine = $line;

	// jika kolom kodisi lebih besar daripada kolom lain
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
		$newLine = $line1;

	// jika kolom keterangan lebih besar daripada kolom lain
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
		$newLine = $line2;

	// jika kolom rekomendasi lebih besar daripada kolom lain
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
$pdf->Cell(30,(1 * $h),'',1,0,'C');
$pdf->Cell(225,7,'Pelat nama',1,0,'L');
$pdf->Ln();

for ($i=34; $i <= 39; $i++) {
	if ($noPertanyaan[$i-1] == 34) {
		$pertanyaan = "a. Asli/telah diganti";
	}
	if ($noPertanyaan[$i-1] == 35) {
		$pertanyaan = "b. Ukuran";
	}
	if ($noPertanyaan[$i-1] == 36) {
		$pertanyaan = "c. Memuat identitas bejana uap"."\n"."  - Nama dan tempat pembuatan         ";
	}
	if ($noPertanyaan[$i-1] == 37) {
		$pertanyaan = "  - Tahun pembuatan";
	}
	if ($noPertanyaan[$i-1] == 38) {
		$pertanyaan = "  - Nomor serie";
	}
	if ($noPertanyaan[$i-1] == 39) {
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

	if ($picture[$i-1] != NULL) {
		$linex = 3;
	} else {
		$linex = 1;
	}
	if(($linex >= $line) && ($linex >= $line1) && ($linex >= $line2) && ($linex >= $line3)){
		$ls0 = $linex - $line;
		$ls1 = $linex - $line1;
		$ls2 = $linex - $line2;
		$ls3 = $linex - $line3;
		$newLine = $linex;
		$spacek = " ";
		$spacep = " ";
		$spacet = " ";
		$spacer = " ";

		if ($ls0 == 1) {
			$GLOBALS['spacep'] = "\n ";
		}
		if ($ls0 == 2) {
			$GLOBALS['spacep'] = "\n\n ";
		}
		if ($ls0 == 3) {
			$GLOBALS['spacep'] = "\n\n\n ";
		}
		if ($ls0 == 4) {
			$GLOBALS['spacep'] = "\n\n\n\n ";
		}
		if ($ls0 == 5) {
			$GLOBALS['spacep'] = "\n\n\n\n\n ";
		}
		if ($ls0 == 6) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n ";
		}
		if ($ls0 == 7) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 8) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 9) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n ";
		}
		if ($ls0 == 10) {
			$GLOBALS['spacep'] = "\n\n\n\n\n\n\n\n\n\n ";
		}

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

	// jika kolom permasalahan lebih besar daripada kolom lain
	} elseif (($line >= $line1) && ($line >= $line2) && ($line >= $line3)) {
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
	} elseif (($line1 >= $line) && ($line1 >= $line2) && ($line1 >= $line3)) {
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
	} elseif (($line2 >= $line) && ($line2 >= $line1) && ($line2 >= $line3)) {
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
	} elseif (($line3 >= $line) && ($line3 >= $line1) && ($line3 >= $line2)) {
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
	// $pdf->Cell(20,($newLine * $h),'Picture',1,0,'C'); 
	if ($picture[$i-1] == NULL) {
		$pdf->Cell(30,($newLine * $h),'-',1,0,'C');
	} else {
		$xPos = $pdf->GetX();
		$yPos = $pdf->GetY();
		$pdf->MultiCell($wpict,$h,$pdf->MemImage($picture[$i-1],$xPos+1,$yPos+1,$wpict-2,($h * $newLine)-2),0,'C');
		$pdf->SetXY($xPos + $wpict , $yPos);
	}

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