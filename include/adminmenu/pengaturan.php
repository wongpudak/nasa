<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!current_user_can('manage_options')) { die; exit(); }
?>
<div class="wrap">
<h1 class="wp-heading-inline">Pengaturan Umum</h1>
<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=5" target="_blank" role="button" aria-expanded="false">Bantuan</a>
<hr class="wp-header-end">
<?php 
$options = get_option('cb_pengaturan');
$error = '';
if (isset($_POST) && count($_POST) > 0) {
	foreach ($_POST as $key => $value) {
		if ($key == 'banklain')	{
			$options['banklain'] = strip_tags($value,'<a><img><code><strong><ol><ul><li><b><i><em><u>');
		} else {
			if (is_array($value)) {
				foreach ($value as $item => $keyvalue) {
					$options[$key][$item] = sanitize_text_field($keyvalue);
				}
			} else {
				$options[$key] = sanitize_text_field($value);
			}
		}
		
		if ($key == 'default') {
			$default = '';
			if (!is_numeric($value)) {				
				if ($value != '') {				
					$cek = explode(',', $value);				
					foreach ($cek as $cekdefault) {
						$cekid = trim($cekdefault);
						if (is_numeric($cekid)) {
							$user = get_userdata( $cekid );
							if ( $user === false ) {
							    $error .= 'Tidak ditemukan member dengan ID '.$cekid.'<br/>';
							} else {
							    $default .= $cekid.',';
							}
						} else {
							$error .= 'Isian ID Sponsor Default harus berupa angka<br/>';
						}
					}

					$default = substr($default, 0,-1);
				}
			} else {
				$user = get_userdata( $value );
				if ( $user === false ) {
				    $error .= 'Tidak ditemukan member dengan ID '.$value.'<br/>';
				} else {
					$default = $value;
				}
			}

			$options['default'] = $default;
		}

	}

	if (!isset($_POST['khususpremium'])) {
		unset($options['khususpremium']);
	}

	if (!isset($_POST['autopremium'])) {
		unset($options['autopremium']);
	}
	
	if (!isset($_POST['autopremiumwoo'])) {
		unset($options['autopremiumwoo']);
	}


	if (!isset($_POST['cap_registrasi'])) {
		unset($options['cap_registrasi']);
	}

	if (!isset($_POST['cap_kontak'])) {
		unset($options['cap_kontak']);
	}

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
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">No. ID Sponsor Default</label>
    <div class="col-sm-6"><input type="text" name="default" class="form-control" value="<?php if (isset($options['default'])) { echo $options['default']; } ?>"/>
	<small class="form-text text-muted">Masukkan nomor ID member default (member yang akan jadi sponsor kalau ada pengunjung datang tanpa link affiliasi). Jika lebih dari satu, pisahkan dengan koma. Contoh: 1,2,3,4. Kosongkan jika anda ingin menggunakan random member<br/>
		<a href="https://cafebisnis.com/pwa.php?id=4" target="_blank">Panduan Sponsor Default bisa klik di sini</a></small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Pengunjung tanpa sponsor</label>
    <div class="col-sm-6">
	<?php 
	$args = array(
		'name' => 'carisponsor',
		'class' => 'form-control',
		'show_option_none' => 'Gunakan ID Sponsor Default (random)',
		'option_none_value'=> 0
	);

	if (isset($options['carisponsor'])) { 
		$args['selected'] =$options['carisponsor'];
		
	} 
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page yang akan tampil jika ada pengunjung <strong>tanpa link affiliasi</strong>.</small></div>
</div>
<div class="form-group row">
	<label class="col-sm-3 col-form-label">Pengunjung salah link</label>
    <div class="col-sm-6">
	<?php 
	$args = array(
		'name' => 'salahlink',
		'class' => 'form-control',
		'show_option_none' => 'Gunakan ID Sponsor Default (random)',
		'option_none_value'=> 0
	);
	 
	if (isset($options['salahlink'])) { 
		$args['selected'] =$options['salahlink'];
		
	} 
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page yang akan tampil jika ada pengunjung <strong>salah link affiliasi</strong>.</small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Affiliasi hanya utk Premium Member</label>
    <div class="col-sm-6"><input type="checkbox" name="khususpremium" value="1" <?php if (isset($options['khususpremium'])) echo 'CHECKED';?>/><br/>
	<small class="form-text text-muted">Beri centang opsi ini jika URL Affiliasi hanya bisa dipakai oleh Premium Member saja. Selain premium akan diarahkan berdasarkan setting sponsor default di atas</small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Auto Premium</label>
    <div class="col-sm-6"><input type="checkbox" name="autopremium" value="1" <?php if (isset($options['autopremium'])) echo 'CHECKED';?>/><br/>
	<small class="form-text text-muted">Beri centang opsi ini jika anda ingin agar semua pembeli produk lain otomatis menjadi premium member<br/>
		<a href="https://cafebisnis.com/pwa.php?id=14" target="_blank">Panduan Auto Premium bisa klik di sini</a></small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Auto Premium Woocommerce</label>
    <div class="col-sm-6"><input type="checkbox" name="autopremiumwoo" value="1" <?php if (isset($options['autopremiumwoo'])) echo 'CHECKED';?>/><br/>
	<small class="form-text text-muted">Beri centang opsi ini jika anda ingin agar semua pembeli woocommerce otomatis menjadi premium member<br/>
		<a href="https://cafebisnis.com/pwa.php?id=14" target="_blank">Panduan Auto Premium bisa klik di sini</a></small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Limit Pembayaran Komisi</label>
    <div class="col-sm-6"><input type="text" name="limit" class="form-control" value="<?php if (isset($options['limit'])) { echo $options['limit']; }?>"/>
	<small class="form-text text-muted">Masukkan minimum pembayaran kepada affiliasi disini. Nama member yang mendapat komisi dibawah
	limit ini, tidak akan muncul di menu <a href="?page=bayar">bayar</a></small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">reCaptcha Site Key</label>
    <div class="col-sm-6"><input type="text" name="cap_key" class="form-control" value="<?php if (isset($options['cap_key'])) { echo $options['cap_key']; }?>"/></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">reCaptcha Secret Key</label>
    <div class="col-sm-6"><input type="text" name="cap_secret" class="form-control" value="<?php if (isset($options['cap_secret'])) { echo $options['cap_secret']; }?>"/>
	<small class="form-text text-muted">Untuk mengaktifkan reCaptcha di form registrasi dan kontak, silahkan masukkan Site Key dan Secret Key. <a href="https://www.google.com/recaptcha/admin" target="_blank">Klik di sini untuk mendapatkan keduanya</a>. Gunakan pilihan reCaptcha v2<br/>
		<a href="https://cafebisnis.com/pwa.php?id=3" target="_blank">Panduan re-Captcha bisa klik di sini</a>
	</small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Tampilkan reCaptcha</label>
    <div class="col-sm-3"><input type="checkbox" name="cap_registrasi" value="1" <?php if (isset($options['cap_registrasi'])) echo 'CHECKED';?>/> di Form Registrasi
    </div>
    <div class="col-sm-3"><input type="checkbox" name="cap_kontak" value="1" <?php if (isset($options['cap_kontak'])) echo 'CHECKED';?>/> di Form Kontak
    </div>
    
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Halaman Registrasi</label>
    <div class="col-sm-6">
	<?php 
	if (isset($options['registrasi'])) { 
		$args = 'selected='.$options['registrasi'].'&name=registrasi&class=form-control'; 
	} else {
		$args = 'name=registrasi&class=form-control'; 
	}
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page yang anda pakai sebagai halaman registrasi pengguna baru.</small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Halaman Login</label>
    <div class="col-sm-6">
	<?php 
	if (isset($options['loginpage'])) { 
		$args = 'selected='.$options['loginpage'].'&name=loginpage&class=form-control&show_option_none=Pakai Default WordPress'; 
	} else {
		$args = 'name=loginpage&class=form-control&show_option_none=Pakai Default WordPress'; 
	}
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page yang anda pakai sebagai halaman login.</small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Halaman Memberarea</label>
    <div class="col-sm-6">
	<?php 
	if (isset($options['memberarea'])) { 
		$args = 'selected='.$options['memberarea'].'&name=memberarea&class=form-control&show_option_none=Pilih Page'; 
	} else {
		$args = 'name=memberarea&class=form-control&show_option_none=Pilih Page'; 
	}
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page memberarea.</small></div>
</div>

<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Success Page</label>
    <div class="col-sm-6">
	<?php 
	if (isset($options['successpage'])) { 
		$args = 'selected='.$options['successpage'].'&name=successpage&class=form-control'; 
	}else {
		$args = 'name=successpage&class=form-control'; 
	}
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page untuk memberitahu pendaftaran telah sukses.</small></div>
</div>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Halaman Order</label>
    <div class="col-sm-6">
	<?php 
	if (isset($options['order'])) { 
		$args = 'selected='.$options['order'].'&name=order&class=form-control'; 
	}else {
		$args = 'name=order&class=form-control&show_option_none=Pilih Page'; 
	}
	wp_dropdown_pages($args);?>
	<small class="form-text text-muted">Pilih Page yang anda pakai sebagai halaman order.</small></div>
</div>

<?php if (function_exists('curl_init')): ?>
<div class="form-group row">
	 <label class="col-sm-3 col-form-label">Layanan Penyingkat URL</label>
    <div class="col-sm-6">
	<input type="text" name="shorturl" class="form-control" value="<?php if (isset($options['shorturl'])) { echo $options['shorturl']; }?>"/>
	<small class="form-text text-muted"><p>Digunakan untuk menyingkat URL Affiliasi anda. Member bisa memilih apakah menggunakan URL Affiliasi anda atau URL dari penyingkat URL yang jauh lebih singkat. Silahkan pilih URL API dari layanan yang ingin anda gunakan.</p>
	<p>Contoh Format URL : <code>http://tinyurl.com/api-create.php?url=<span style="color:#ff0000;">[URL]</span></code></p>
	<p><a href="http://cafebisnis.com/url.php" target="_blank">Klik Disini</a> untuk mendapatkan format beberapa layanan penyingkat URL (copy dan paste ke kotak isian diatas)</p></small>
	</div>
</div>
<?php endif; ?>

<input type="submit" class="button button-primary" value="Ubah Pengaturan"/>
</form>

</div>