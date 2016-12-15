<html>
<head>
<title>ThaiCreate.Com PHP PDF</title>
</head>
<body>
<?php
	require('fpdf.php');

	define('FPDF_FONTPATH','font/');

	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->AddFont('angsa','','angsa.php');
	$pdf->SetFont('angsa','',36);
	$pdf->Cell(0,20,iconv( 'UTF-8','TIS-620','สวัสดี ชาวไทยครีเอท'),0,1,"C");
	unlink( '/tmp/test.pdf' );
	$pdf->Output("/tmp/test.pdf","F");
?>
	PDF Created Click <a href="/tmp/test.pdf">here</a> to Download
</body>
</html>
