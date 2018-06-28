<?php

require_once('fpdf/fpdf.php');

class myPDF extends FPDF{

	function headerTable()
	{
		$this->SetFont('Times','',12);
		$this->Cell(10);
		$this->Cell(10,10,'No',1,0,'C');
		$this->Cell(20,10,'Picture',1,0,'C'); 
		$this->Cell(55,10,'Permasalahan/Pertanyaan',1,0,'C'); 
		$this->Cell(30,10,'Hasil/Jawaban',1,0,'C');
		$this->Cell(50,10,'Kondisi',1,0,'C'); 
		$this->Cell(50,10,'Keterangan',1,0,'C'); 
		$this->Cell(50,10,'Rekomendasi',1,0,'C');  
		$this->Ln();
	}
}

$pdf = new myPDF();
$pdf->AliasNbPages(); // fungsi untuk mengitung jumlah total halaman
$pdf->AddPage('L'); // membuat halaman landscape
$pdf->SetFont('Times','',13);

$pdf->Cell(10);
$pdf->Cell(260,7,'FORM INSPEKSI 2 MINGGU',0,0,'C');
$pdf->Ln();
$pdf->Cell(10);
$pdf->Cell(260,7,'TANGKI EVAPORATOR',0,0,'C');
$pdf->Ln(20);

$pdf->SetFont('Times','',12);
$pdf->Cell(10);
$pdf->Cell(170,7,'NIP                            : ',0,0,'L');
$pdf->Cell(90,7,'TANGGAL :',0,0,'L');
$pdf->Ln();
$pdf->Cell(10);
$pdf->Cell(170,7,'NO TANGKI            : ',0,0,'L');
$pdf->Cell(90,7,'NO FORM  : ' ,0,0,'L');
$pdf->Ln();
$pdf->Cell(10);
$pdf->Cell(170,7,'JENIS                        : ',0,0,'L');
$pdf->Ln();
$pdf->Cell(10);
$pdf->Cell(170,7,'UKURAN TANGKI : ',0,0,'L');
$pdf->Ln();

$pdf->headerTable();

$pdf->Output('test.pdf','I');

?>