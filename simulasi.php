<?php

$mySQLserver = "localhost";
$mySQLuser = "root"; // isi dengan mysql user anda
$mySQLpassword = ""; // isi dengan password anda
$mySQLdefaultdb = "simulasi_crypto";  // jalankan file logsimulasi.sql pada database anda
$host = "localhost/";
$link = mysqli_connect($mySQLserver, $mySQLuser, $mySQLpassword,$mySQLdefaultdb) or die ("Could not connect to MySQL");

$key_botmantul="xxxxxxx"; // isi dengan data key botmantul.id anda
$secretKey_botmantul="yyyyyyyyyyyyyyyyyyyy"; // isi dengan data secretkey botmantul anda
// daftar disini bila belum punya https://botmantul.id/crypto/register.php

$API_HOST_1="https://botmantul.id/crypto/API_getCrypto.php";
$API_HOST_2="https://botmantul.id/crypto/API_getAllCrypto.php";

date_default_timezone_set("Asia/Jakarta");
$tanggalhariini = date("Y-m-d");
$jamhariini = date("H:i:sa");
$saatini = $tanggalhariini. " ".$jamhariini;
$saatini_tanpa_ampm = str_replace("am", "", $saatini);
$saatini_tanpa_ampm = str_replace("pm", "", $saatini);

$gethari = gethari_tahunduluan($saatini);
//$responsetouser = "<br>Saat ini : ".$saatini_tanpa_ampm;
$responsetouser = "<br>Saat ini : ".$gethari;
echo "<br>".$responsetouser;

//isi data

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';

if ($mode=="jual") {
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$profit_loss = isset($_POST['profit_loss']) ? $_POST['profit_loss'] : '';
	//profit_loss

	$sql = "update `logsimulasi` 
	set `isactive`='0',
	`isjual`='1',
	`return` = '$profit_loss' ,
	`datejual`='$saatini_tanpa_ampm' 
	where `id`='$id' 
	";
	$query = mysqli_query($link, $sql) or die('gagal insert data'.mysqli_error($link));

}
if ($mode=="tambah_investasi") {
	$invest = isset($_POST['invest']) ? $_POST['invest'] : '';
	$nominal_invetasi = isset($_POST['nominal_invetasi']) ? $_POST['nominal_invetasi'] : '';
	list($symbol,$hargasaatini) = explode("~",$invest);

	//echo "<br>symbol =".$symbol;
	//echo "<br>hargasaatini =".$hargasaatini;
	$crypto_amount = $nominal_invetasi / $hargasaatini;

	if ($invest!="0" && $nominal_invetasi>=1) {
		$sql = " insert into `logsimulasi` 
		(`symbol`,`crypto_amount`,`nominal_amount`,`datebeli`) 

		values 
		('$symbol','$crypto_amount','$nominal_invetasi','$saatini_tanpa_ampm') ";
		//echo "<br>sql = ".$sql;
		$query = mysqli_query($link, $sql) or die('gagal insert data'.mysqli_error($link));
	}
}

?>
<br>Ini adalah script simulasi investasi cryptocurrency
<br>Bila anda ingin mempelajari seberapa cepat pergerakan coin crypto,
<Br>Anda dapat melakukan simulasi investasi, melihat return yang dihasilkan
<br>Data harga crypto akan terupdate setiap 15 menit.
<Br>
<h1>Form Tambah Investasi</h1>
<br>Bila data tidak keluar, pastikan anda sudah memiliki akses API,
<br>Pastikan Data public key dan secret key sudah diisi dengan benar
<br>bila belum punya, bisa daftar disini https://botmantul.id/crypto/register.php
<form method="POST">
<br>Symbol :
<select name="invest">
<option value="0">Pilih Crypto</option>
<?php
	$option = getallCrypto($key_botmantul,$secretKey_botmantul,$API_HOST_2);
	echo "<Br>option = ". $option;
?>
</select>
<br>Jumlah investasi dalam rupiah
<br><input type="text" name="nominal_invetasi" required>
<input type="hidden" name="mode" value="tambah_investasi">
<input type="submit" name="submit" value="Tambah">

</form>

<form method="POST">
<input type="submit" name="submit" value="Refresh">

</form>

<?php



$responsetouser = connect_crypto($key_botmantul,$secretKey_botmantul,$API_HOST_1,$saatini,$link,$mySQLserver,$mySQLuser,$mySQLpassword,$mySQLdefaultdb) ;

echo "<br>".$responsetouser;


function connect_crypto($key_botmantul,$secretKey_botmantul,$API_HOST_1,$saatini,$link,$mySQLserver,$mySQLuser,$mySQLpassword,$mySQLdefaultdb) {

$sql = " select * from `logsimulasi` where `isactive`='1' "; 
//echo $sql;
$options = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			);
			$conn = new PDO("mysql:host=$mySQLserver;dbname=$mySQLdefaultdb", $mySQLuser, $mySQLpassword);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$no=0;
			$fieldvalue="";
			$gainloss=0;
			$totalgainloss=0;
			$totalinvest=0;

$html ="<table border='1'>";
$html .="<tr>";
$html .="<td>No</td>";
$html .="<td>Tgl Pembelian</td>";
$html .="<td>Symbol Crypto</td>";
$html .="<td>harga beli  / jual per 1 unit</td>";
$html .="<td>Nilai beli dalam rupiah</td>";
$html .="<td>Nilai saldo crypto</td>";
$html .="<td>Nilai jual sekarang saat ini</td>";
$html .="<td>Profit / loss dalam rupiah</td>";
$html .="<td>Percentage Profit / loss</td>";
$html .="<td>Jual</td>";

$html .="</tr>";


			foreach($conn->query($sql) as $row) {
					$no=$no+1;
					$id=$row['id'];
					$symbol=$row['symbol'];
					$datebeli=$row['datebeli'];
					$getharibeli = gethari_tahunduluan($datebeli);
					$crypto_amount=$row['crypto_amount'];
					$nominal_amount=$row['nominal_amount'];

					$harga_Saat_beli = $nominal_amount / $crypto_amount;
					$saldoCRYPTO = $crypto_amount;
					$nilaibeliCRYPTO = $nominal_amount;
					
					$totalinvest = $totalinvest + $nilaibeliCRYPTO;
					$PRICE_CRYPTO = chekharga($symbol,$key_botmantul,$secretKey_botmantul,$API_HOST_1);
				
					$dompetCRYPTO = $PRICE_CRYPTO * $saldoCRYPTO;

					$dompetCRYPTO_format = number_format($dompetCRYPTO);
					$nilaijualCRYPTO_saatini = $dompetCRYPTO;
					$selisih_jual_beli_CRYPTO = $nilaijualCRYPTO_saatini  -  $nilaibeliCRYPTO;
					$totalgainloss = $totalgainloss + $selisih_jual_beli_CRYPTO;
					$persentase_rugi_untung_CRYPTO = ($selisih_jual_beli_CRYPTO / $nilaibeliCRYPTO) * 100;
/*
					
*/


					if ($selisih_jual_beli_CRYPTO<=0) {
						$font_gainloss="Red";
					}
					else {
						$font_gainloss="Green";	
					}

$ketjual ="JUAL saldo ".$crypto_amount." ".$symbol." semuanya !.\n\n Potensi profit/loss =  ".number_format($selisih_jual_beli_CRYPTO). "";


$formjual = "<form method='post'>";
$formjual .= "<input type='hidden' name='id' value='".$id."'>";
$formjual .= "<input type='hidden' name='profit_loss' value='".$selisih_jual_beli_CRYPTO."'>";
$formjual .= "<input type='hidden' name='mode' value='jual'>";
$formjual .= "<input type='submit' name='jual' value='".$ketjual."'> ";
$formjual .= "</form>";


$html .="<tr>";
$html .="<td>".$no."</td>";
$html .="<td>".$getharibeli."</td>";
$html .="<td>".$symbol."</td>";
$html .="<td> saat beli 1 ".$symbol. "  = Rp ".number_format($harga_Saat_beli)."<br>";
$html .="saat jual sekarang 1 ".$symbol. "  = Rp ".number_format($PRICE_CRYPTO)."</td>";
$html .="<td> Rp ".number_format($nilaibeliCRYPTO)."</td>";
$html .="<td>Saldo ".$symbol." = ".$saldoCRYPTO."</td>";
$html .="<td>Rp ".number_format($nilaijualCRYPTO_saatini)."</td>";
$html .="<td><b><font color='".$font_gainloss."'>Rp ".number_format($selisih_jual_beli_CRYPTO)."</font></b></td>";
$html .="<td><b><font color='".$font_gainloss."'>".number_format($persentase_rugi_untung_CRYPTO,4) . " %"."</font></b></td>";
$html .="<td>" .$formjual. "</td>";

$html .="</tr>";


			}

$html .="</table>";
echo "<br>".$html;

$sql = "select * from `logsimulasi` where `isjual`='1' and `isactive`='0' ";
$options = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$html ="<h1>Coin terjual</h1>";
			$html .="<table border='1'>";
			$html .="<tr>";
			$html .="<td>No</td>";
			$html .="<td>Symbol Crypto</td>";
			$html .="<td>Total Investasi</td>";
			$html .="<td>Total Saldo Crypto</td>";
			$html .="<td>Tanggal beli</td>";
			$html .="<td>Return on Investment</td>";
			$html .="<td>Tanggal dijual</td>";
			$html .="</tr>";

		$rt=0;
		$return_total=0;
		foreach($conn->query($sql) as $row) {
			$rt=$rt+1;
			$symbol=$row['symbol'];
			$crypto_amount=$row['crypto_amount'];
			$datebeli=$row['datebeli'];
			$getharibeli = gethari_tahunduluan($datebeli);
			$return=$row['return'];
			$return_total = $return_total + $return;
			$return_f = number_format($return);
			$datejual=$row['datejual'];
			$getharijual = gethari_tahunduluan($datejual);
			$nominal_amount = $row["nominal_amount"];
			$nominal_amount_f = number_format($nominal_amount);

			$html .="<tr>";
			$html .="<td>".$rt."</td>";
			$html .="<td>".$symbol."</td>";
			$html .="<td> Rp ".$nominal_amount_f."</td>";
			$html .="<td>".$crypto_amount."</td>";
			$html .="<td>".$getharibeli."</td>";
			$html .="<td>".$return_f."</td>";
			$html .="<td>".$getharijual."</td>";
			$html .="</tr>";
		}

$html .="</table>";

$html .="Total Return = Rp ".number_format($return_total);
echo $html;



$sql = "SELECT symbol,sum(crypto_amount) as `t1`, sum(nominal_amount) as `t2` 
FROM `logsimulasi`  where `isactive`='1'  group by symbol ORDER
 BY `t2` DESC ";
$options = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$html ="<h1>Rekap semua coin</h1>";
			$html .="<table border='1'>";
			$html .="<tr>";
			$html .="<td>No</td>";
			$html .="<td>Symbol Crypto</td>";
			$html .="<td>Total Saldo Crypto</td>";
			$html .="<td>Total Investasi</td>";
			$html .="<td>Total Return</td>";
			$html .="<td>Selisih Profit/Loss dalam rupiah</td>";
			$html .="<td>Percentage profit / loss</td>";
			$html .="</tr>";
			
				$rt=0;
						foreach($conn->query($sql) as $row) {
								$rt=$rt+1;
								$symbol=$row['symbol'];
								$t1=$row['t1'];
								$t1_format = number_format($t1);
								$t2=$row['t2'];
								$t2_format = number_format($t2);

								
								$PRICE_CRYPTO = chekharga($symbol,$key_botmantul,$secretKey_botmantul,$API_HOST_1);

								//echo "<br>PRICE_CRYPTO = ".$PRICE_CRYPTO;

							
							$total_return = $PRICE_CRYPTO * $t1;
							$total_return_format= number_format($total_return);
							$selisih_jual_beli_CRYPTO = $total_return -  $t2;
							$selisih_jual_beli_CRYPTO_format = number_format($selisih_jual_beli_CRYPTO);
							$persentase_rugi_untung_CRYPTO = ($selisih_jual_beli_CRYPTO / $t2) * 100;
							$persentase_rugi_untung_CRYPTO_format = number_format(	$persentase_rugi_untung_CRYPTO,2);
								
							if ($selisih_jual_beli_CRYPTO<=0) {
								$font_gainloss="Red";
							}
							else {
								$font_gainloss="Green";	
							}

							
							$html .="<tr>";
								$html .="<td>".$rt."</td>";
								$html .="<td>".$symbol."</td>";
								$html .="<td>".$t1."</td>";
								$html .="<td>Rp ".$t2_format."</td>";
								$html .="<td>Rp ".$total_return_format."</td>";
								$html .="<td><b><font color='".$font_gainloss."'>Rp ".$selisih_jual_beli_CRYPTO_format." </b></font></td>";
								$html .="<td><b><font color='".$font_gainloss."'>".$persentase_rugi_untung_CRYPTO_format." % </b></font></td>";
								$html .="</tr>";
						}						
	
	$html .="</table>";

	$totalAsset = $totalinvest + $totalgainloss;
	if ($totalinvest<=0) {
		$totalinvest=1;
	}
	$persentase_gainloss = ($totalgainloss / $totalinvest) * 100;
	$html .="\n Total investasi = Rp ". number_format($totalinvest);
	$html .="\n Total Asset = Rp ". number_format($totalAsset);
	$html .="\n Total Gain/Loss = Rp ". number_format($totalgainloss);
	$html .="\n Percentage Gain/Loss ". number_format($persentase_gainloss,3). " %";		
	$html = str_replace("\n","<br>",$html );
	echo	$html;
}


function gethari_tahunduluan($tanggalpublish) {
//14-10-2020 04:10:03
//2020-10-14 04:10:03 <-- yang ini	
$tanggalpublish = str_replace("am", "", $tanggalpublish);
$tanggalpublish = str_replace("pm", "", $tanggalpublish);


//echo "<br>tgl : ".$tanggalpublish;
$dt = strtotime($tanggalpublish);
$day = strtolower(date("D", $dt));
$tahun = substr($tanggalpublish,0,4);
$tanggal = substr($tanggalpublish,8,2);
$bulan = substr($tanggalpublish,5,2);
$jam =  substr($tanggalpublish,11,2);
$menit =  substr($tanggalpublish,14,2);
$detik =  substr($tanggalpublish,17,2);

$namahari="";
$namabulan="";

if ($bulan=="01") {
	$namabulan="January";
} else if ($bulan=="02") {
	$namabulan="February";
} else if ($bulan=="03") {
	$namabulan="Maret";
} else if ($bulan=="04") {
	$namabulan="April";
} else if ($bulan=="05") {
	$namabulan="May";
} else if ($bulan=="06") {
	$namabulan="Juni";
} else if ($bulan=="07") {
	$namabulan="July";
} else if ($bulan=="08") {
	$namabulan="Agustus";
} else if ($bulan=="09") {
	$namabulan="September";
} else if ($bulan=="10") {
	$namabulan="Oktober";
} else if ($bulan=="11") {
	$namabulan="November";
} else if ($bulan=="12") {
	$namabulan="Desember";
}

if ($day=="sun") {
	$namahari="Minggu";
} else if ($day=="mon") {
	$namahari="Senin";
} else if ($day=="tue") {
	$namahari="Selasa";
} else if ($day=="wed") {
	$namahari="Rabu";
} else if ($day=="thu") {
	$namahari="Kamis";
} else if ($day=="fri") {
	$namahari="Jumat";
} else if ($day=="sat") {
	$namahari="Sabtu";
}

$formatwaktu = $namahari . " ".$tanggal . " ".$namabulan. " " .$tahun. " pada jam ".$jam.":".$menit.":".$detik;

return $formatwaktu;
}


function getallCrypto($key_botmantul,$secretKey_botmantul,$API_HOST_2) {

$base64encdode = base64_encode($key_botmantul.$secretKey_botmantul);

   $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Accept: application/json';
    $headers[] = 'Client-ID: '.$key_botmantul;
    $headers[] = 'Pass-Key: '.$secretKey_botmantul;
   
 
    //echo "<br>headers = ".json_encode($headers);

	
    $ch = curl_init($API_HOST_2);

	//echo "<br>API_HOST_2 = ".json_encode($API_HOST_2);    
   // echo "<br>postData=". json_encode($postData,1);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    
    $content = curl_exec($ch);
    //$content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);
    curl_close($ch);
   
    $json = json_decode($content, true);

   // echo "<br>json: ".$json;
    //echo "<br>content results = ".$content;
    
    $status = $json["status"];
    //echo "<br>status line 343 = ".  $status;
     $hits = $json["hits"];
     $maxhits = $json["maxhits"];

    if ($status!="200") {
      $messages = $json["messages"];
      echo "<br>".$messages;
      exit;
    }

	 $jumlah_data = count($json["detail"]);
    // echo "<br>jumlah_data = ".$jumlah_data;
    $total_asset=0;
    $html_option="";
    for ($i=0;$i<$jumlah_data;$i++) {
      $urut = $i + 1;
      $symbol = $json["detail"][$i]["symbol"];
      $harga_updated = $json["detail"][$i]["harga_updated"];
      $harga_updated_f=number_format($harga_updated);
      $c=$symbol. " => Rp ".$harga_updated_f;
      $d=$symbol."~".$harga_updated;
      $html_option .="<option value='".$d."'>".$c."</option>";

    }

	 return $html_option;

}

function chekharga($symbol,$key_botmantul,$secretKey_botmantul,$API_HOST_1) {

$base64encdode = base64_encode($key_botmantul.$secretKey_botmantul);

   $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Accept: application/json';
    $headers[] = 'Client-ID: '.$key_botmantul;
    $headers[] = 'Pass-Key: '.$secretKey_botmantul;
   
 
    // echo "<br>headers = ".json_encode($headers);

		$postData = array(
	      'symbol' => $symbol,
		);

    $ch = curl_init($API_HOST_1);

	//echo "<br>API_HOST_1 = ".json_encode($API_HOST_1);    
   // echo "<br>postData=". json_encode($postData,1);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    
    $content = curl_exec($ch);
    //$content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);
    curl_close($ch);
   
    $json = json_decode($content, true);

   // echo "<br>json: ".$json;
   // echo "<br>content results = ".$content;
    
    $status = $json["status"];
    //echo "<br>status line 343 = ".  $status;
     $hits = $json["hits"];
     $maxhits = $json["maxhits"];

    if ($status!="200") {
      $messages = $json["messages"];
      echo "<br>".$messages;
      exit;
    }

	 $harga_updated = $json["detail"][0]["harga_updated"];
	 //echo "<br>harga_updated results = ".$harga_updated;
    
	 return $harga_updated;

}

?>
