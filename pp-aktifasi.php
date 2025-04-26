<?php
include_once('../../../wp-config.php'); 
if(!class_exists(wpdb)) { include_once('../../../wp-includes/wp-db.php');}

function libCurlPost($url,$data)  {

foreach($data as $i=>$v) {
$postdata.= $i . "=" . urlencode($v) . "&";
}
$postdata.="cmd=_notify-validate";

$ch=curl_init();

curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);

//Start ob to prevent curl_exec from displaying stuff.
ob_start();
curl_exec($ch);

//Get contents of output buffer
$info=ob_get_contents();
curl_close($ch);

//End ob and erase contents.
ob_end_clean();

return $info;

}
$options = get_option('cb_pengaturan');
if ($options['pp_sand']) { 
	$pp = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; 
} else {
	$pp = 'https://www.paypal.com/cgi-bin/webscr';
}
$result=libCurlPost($pp,$_POST); 

if(eregi("VERIFIED",$result)) { 
	if (isset($_POST['item_number']) && is_numeric($_POST['item_number'])) {
		$idorder = $_POST['item_number'];
		$cekorder = $wpdb->get_var("SELECT `idproduk` FROM `cb_produklain` WHERE `id` = $idorder");
		if ($cekorder == 0) {
			$harga = $options['harga']/$options['pp_price'];
		} else {			
			$cekharga = $wpdb->get_var("SELECT `harga` FROM `cb_produk` WHERE `id` = $cekorder");
			$harga = $cekharga/$options['pp_price'];
		}
		$status = 'autoactivate';
		$bank = 'PayPal';
		if ($_POST['payment_gross'] >= $harga && $_POST['receiver_email'] == $options['pp_email']) {
			aktivasi($idorder,'PayPal');
		}		
	}
} else { 
	$header = 'From: '.get_option('nama_email').' <'.get_option('alamat_email').'>';
	$body = '
	Seseorang melakukan transaksi menggunakan PayPal tapi gagal, silahkan cek secara manual. Berikut data yang mungkin bisa membantu:
	
	'.serialize($_POST).'

	'.$result;

	wp_mail(get_option('alamat_email'), 'Transaksi Gagal', $body, $header);
} 
?>