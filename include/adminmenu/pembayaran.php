<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (is_admin()) :
?>
<div class="wrap">
<h1 class="wp-heading-inline">Metode Pembayaran</h1>
<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=7" target="_blank" role="button" aria-expanded="false">Bantuan</a>
<hr class="wp-header-end">
<?php 
$options = get_option('cb_pengaturan');
$error = '';
if (isset($_POST) && count($_POST) > 0) {
	unset($options['duitku']['payment']);
	foreach ($_POST as $key => $value) {
		if ($key == 'banklain')	{
			$options['banklain'] = strip_tags($value,'<a><img><code><strong><ol><ul><li><b><i><em><u>');
		} else {
			if (is_array($value)) {
				foreach ($value as $item => $keyvalue) {					
					if (is_array($keyvalue)) {
						foreach ($keyvalue as $keyitem => $keyvalueitem) {
							$options[$key][$item][$keyitem] = sanitize_text_field($keyvalueitem);
						}
					} else {
						$options[$key][$item] = sanitize_text_field($keyvalue);
					}
				}
			} else {
				$options[$key] = sanitize_text_field($value);
			}
		}

		if ($key == 'pp_email' && $value != '') {		
			if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
		       $error .= 'Email PayPal Tidak Valid<br/>'; 		       
		    }
		}

		if (($key == 'limit' || $key == 'harga' || $key == 'pp_price') && $value != '') {
			if (!is_numeric($value)) {
				switch ($key) {
					case 'limit' : $error .= 'Limit Pembayaran Komisi '; break;
					case 'harga': $error .= 'Harga Produk '; break;
					case 'pp_price': $error .= 'Kurs per Dollar '; break;
				}

				$error .= 'harus berupa ANGKA. Untuk angka desimal, gunakan titik sebagai tanda koma<br/>';
			}
		}
	}

	if (!isset($_POST['pp_sand'])) { unset($options['pp_sand']); }
	if (!isset($_POST['duitkusand'])) {	unset($options['duitkusand']); }
	
	if ($error == '') {
		update_option('cb_pengaturan', $options); 
		echo '<div class="notice notice-success is-dismissible">
		<p>Update berhasil</p>
		</div>';
	} else {
		echo '<div class="notice notice-error is-dismissible"><p>'.$error.'</p></div>';
	}
}
$args = ''; ?>
<style type="text/css">
	.judul {text-align:right; vertical-align:top; width:200px; padding-right:5px;}
	.isian {width:500px;}
	#_tip {color:#ff0000;}
</style>
<form action="" method="post">
	<strong>Pembayaran Otomatis</strong>
	<p>Agar member bisa melakukan upgrade keanggotaan secara otomatis, silahkan masukkan data-data bank dan PayPal anda disini:</p>

	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Key Transaksi</label>
	    <div class="col-sm-6"><input type="text" name="key_trx" class="form-control" value="<?php if (isset($options['key_trx'])) { echo $options['key_trx']; }?>"/>
		<small class="form-text text-muted">Kode yg harus dimasukkan di kolom berita saat transfer ke BCA dan Mandiri. Misalnya: ORDER-1234, maka masukkan: ORDER-</small></div>
	</div>
	
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Biaya Upgrade Premium</label>
	    <div class="col-sm-6"><input type="text" name="harga" class="form-control" value="<?php if (isset($options['harga'])) { echo $options['harga']; }?>"/>
		<small class="form-text text-muted">Masukkan biaya untuk upgrade ke premium di sini</small></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Mata Uang</label>
	    <div class="col-sm-3"><input type="text" name="matauang" class="form-control" value="<?php if (isset($options['matauang'])) { echo $options['matauang']; } else { echo 'Rp.'; } ?>"/></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Angka Unik</label>
	    <div class="col-sm-6">
	    	<select name="angkaunik">
	    		<?php
	    		$unik = array('','','');
	    		if (isset($options['angkaunik'])) {
	    			$unik[$options['angkaunik']] = ' selected';
	    		}

	    		if (isset($options['harga']) && is_numeric($options['harga'])) {
	    			$harga = $options['harga'];
	    		} else {
	    			$harga = 0;
	    		}

	    		?>
	    		<option value="2"<?php echo $unik[2];?>>Tambah (<?php echo number_format($harga+123);?>)</option>
	    		<option value="1"<?php echo $unik[1];?>>Kurang (<?php echo number_format(($harga-1000)+123); ?>)</option>
	    		<option value="0"<?php echo $unik[0];?>>Tetap (<?php echo number_format($harga);?>)</option>
	    	</select>
	    </div>
	</div>
	<h4>TRANSFER BANK MANUAL</h4>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Prosedur Transaksi</label>
	    <div class="col-sm-6">
	    	<?php
	    	$editorarr = array(
 						'textarea_name' => 'banklain'
 				);
 					
 					#$mailkonten = str_replace('\r\n', '<br/>', ($konfemail['isi_'.$keyform] ??= '')); 					
 					$banklain = stripslashes($options['banklain'] ??= '');
 					$banklain = html_entity_decode($banklain);
 					wp_editor($banklain,'banklain',$editorarr);
 				?>
	    	<small class="form-text text-muted">Petunjuk pembayaran transfer manual. Gunakan kode <code>[hargaunik]</code> untuk menampilkan jumlah transfer yang telah disertai kode ini.</small></div>
	</div>

	<?php if (function_exists('curl_init')): ?>
	<h4>Duitku</h4>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Callback URL</label>
	    <div class="col-sm-6"><input type="text" name="callback" class="form-control" value="<?php echo plugin_dir_url( __DIR__ ).'duitkucall.php'; ?>" disabled/>
	    	<small class="form-text text-muted">Masukkan URL ini di web duitku.</small></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Merchant Code</label>
	    <div class="col-sm-6"><input type="text" name="duitku[merchant]" class="form-control" value="<?php if (isset($options['duitku']['merchant'])) { echo stripslashes($options['duitku']['merchant']); }?>"/></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Project API Key</label>
	    <div class="col-sm-6"><input type="text" name="duitku[api]" class="form-control" value="<?php if (isset($options['duitku']['api'])) { echo stripslashes($options['duitku']['api']);}?>"/></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Duitku Sandbox</label>
	    <div class="col-sm-6"><input type="checkbox" name="duitkusand" value="1" <?php if (isset($options['duitkusand'])) echo 'CHECKED';?>/><br/>
		<small class="form-text text-muted">Beri centang opsi ini jika anda ingin menggunakan Duitku Sandbox terlebih dahulu untuk mengujicoba plugin.</small></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Pembayaran yg Diterima</label>
	    <div class="col-sm-6">
	    	<input type="checkbox" name="duitku[payment][VC]" value="1" <?php if (isset($options['duitku']['payment']['VC'])) echo 'CHECKED';?>/> Credit Card (Visa / Master)<br/>
			<input type="checkbox" name="duitku[payment][BK]" value="1" <?php if (isset($options['duitku']['payment']['BK'])) echo 'CHECKED';?>/> BCA KlikPay<br/>
			<input type="checkbox" name="duitku[payment][M1]" value="1" <?php if (isset($options['duitku']['payment']['M1'])) echo 'CHECKED';?>/> Mandiri Virtual Account<br/>
			<input type="checkbox" name="duitku[payment][BT]" value="1" <?php if (isset($options['duitku']['payment']['BT'])) echo 'CHECKED';?>/> Permata Bank Virtual Account<br/>
			<input type="checkbox" name="duitku[payment][A1]" value="1" <?php if (isset($options['duitku']['payment']['A1'])) echo 'CHECKED';?>/> ATM Bersama<br/>
			<input type="checkbox" name="duitku[payment][B1]" value="1" <?php if (isset($options['duitku']['payment']['B1'])) echo 'CHECKED';?>/> CIMB Niaga Virtual Account<br/>
			<input type="checkbox" name="duitku[payment][I1]" value="1" <?php if (isset($options['duitku']['payment']['I1'])) echo 'CHECKED';?>/> BNI Virtual Account<br/>
			<input type="checkbox" name="duitku[payment][VA]" value="1" <?php if (isset($options['duitku']['payment']['VA'])) echo 'CHECKED';?>/> Maybank Virtual Account<br/>
			<input type="checkbox" name="duitku[payment][FT]" value="1" <?php if (isset($options['duitku']['payment']['FT'])) echo 'CHECKED';?>/> Ritel<br/>
			<input type="checkbox" name="duitku[payment][OV]" value="1" <?php if (isset($options['duitku']['payment']['OV'])) echo 'CHECKED';?>/> OVO<br/>
			<input type="checkbox" name="duitku[payment][DN]" value="1" <?php if (isset($options['duitku']['payment']['DN'])) echo 'CHECKED';?>/> Indodana Paylater<br/>
			<input type="checkbox" name="duitku[payment][SP]" value="1" <?php if (isset($options['duitku']['payment']['SP'])) echo 'CHECKED';?>/> Shopee Pay<br/>
			<input type="checkbox" name="duitku[payment][SA]" value="1" <?php if (isset($options['duitku']['payment']['SA'])) echo 'CHECKED';?>/> Shopee Pay Apps<br/>
			<input type="checkbox" name="duitku[payment][AG]" value="1" <?php if (isset($options['duitku']['payment']['AG'])) echo 'CHECKED';?>/> Bank Artha Graha<br/>
			<input type="checkbox" name="duitku[payment][S1]" value="1" <?php if (isset($options['duitku']['payment']['S1'])) echo 'CHECKED';?>/> Bank Sahabat Sampoerna<br/>
		<small class="form-text text-muted">Beri centang opsi pembayaran yang anda inginkan.</small></div>
	</div>

	<?php endif; ?>
	<h4>Data PayPal</h4>

	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Email PayPal</label>
	    <div class="col-sm-6"><input type="text" name="pp_email" class="form-control" value="<?php if (isset($options['pp_email'])) { echo $options['pp_email'];}?>"/>
		<small class="form-text text-muted">Masukkan email PayPal anda disini. Pastikan akun tersebut menggunakan type Premier atau Business. Jika anda memasukkan akun personal, maka script autoupgrade tidak bekerja bahkan dana yang masuk terpotong untuk administrasi.</small></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">Kurs per Dollar</label>
	    <div class="col-sm-6"><input type="text" name="pp_price" class="form-control" value="<?php if (isset($options['pp_price'])) { echo $options['pp_price']; }?>"/>
		<small class="form-text text-muted">Masukkan nilai kurs rupiah terhadap dollar di sini. Harga paypal akan dibagi dengan nilai yg tercantum di sini.</small></div>
	</div>
	<div class="form-group row">
		 <label class="col-sm-3 col-form-label">PayPal Sandbox</label>
	    <div class="col-sm-6"><input type="checkbox" name="pp_sand" value="1" <?php if (isset($options['pp_sand'])) echo 'CHECKED';?>/><br/>
		<small class="form-text text-muted">Beri centang opsi ini jika anda ingin menggunakan PayPal Sandbox terlebih dahulu untuk mengujicoba plugin.</small></div>
	</div>
	
	
	<input type="submit" class="button button-primary" value="Update"/>
</form>
<?php endif; ?>