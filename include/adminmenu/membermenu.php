<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } ?>
<div class="wrap">
	<h1 class="wp-heading-inline">Pengaturan Menu Member Area</h1>
	<p>Ini adalah menu-menu yang akan muncul di bagian menu memberarea. Anda bisa mengganti teks-nya dengan tulisan anda sendiri</p>
<?php
if (is_admin()) :	
	$menuoption = get_option('menuoption');
	
	if (isset($_POST) && count($_POST) > 0) {
		
		$menuoption = array();
		foreach ($_POST as $key => $value) {			
			if ($key == 'infobaru') {
				foreach ($value as $infokey => $infoval) {
					$menuoption['infobaru'][$infokey] = sanitize_text_field($infoval);
				}
			} else {
				$menuoption[$key] = sanitize_text_field($value);
			}
		}

		update_option("menuoption",$menuoption);
		echo '<div class="notice notice-success is-dismissible"><p>Pengaturan Menu Telah Diperbarui</p></div>';
	}
?>
	<form action="" method="post">
	<p><input type="checkbox" name="home_cek" value="1" <?php if (isset($menuoption['home_cek']) && $menuoption['home_cek'] == 1) { echo 'checked="checked" ';}?>/>
		<input type="text" name="home_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['home_label'])) { echo $menuoption['home_label']; } else { echo 'Home';} ?>"/>
		<input type="text" name="home_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['home_desc'])) { echo $menuoption['home_desc']; } else { echo 'Adalah menu untuk kembali ke halaman depan memberarea';} ?>"/>
	</p>
	<p><input type="checkbox" name="profil_cek" value="1" <?php if (isset($menuoption['profil_cek']) && $menuoption['profil_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="profil_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['profil_label'])) { echo $menuoption['profil_label']; } else { echo 'Profil';} ?>"/> <input type="text" name="profil_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['profil_desc'])) { echo $menuoption['profil_desc']; } else { echo 'Adalah menu untuk mengganti data-data di profile anda';} ?>"/>
	</p>
	<p><input type="checkbox" name="laporan_cek" value="1" <?php if (isset($menuoption['laporan_cek']) && $menuoption['laporan_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="laporan_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['laporan_label'])) { echo $menuoption['laporan_label']; } else { echo 'Laporan';} ?>"/> <input type="text" name="laporan_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['laporan_desc'])) { echo $menuoption['laporan_desc']; } else { echo 'Adalah menu untuk melihat laporan keuangan anda';} ?>"/>
	</p>
	<p><input type="checkbox" name="banner_cek" value="1" <?php if (isset($menuoption['banner_cek']) && $menuoption['banner_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="banner_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['banner_label'])) { echo $menuoption['banner_label']; } else { echo 'Banner';} ?>"/> <input type="text" name="banner_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['banner_desc'])) { echo $menuoption['banner_desc']; } else { echo 'Adalah menu untuk mendapatkan banner-banner promosi affiliasi anda';} ?>"/>
	</p>
	<p><input type="checkbox" name="klien_cek" value="1" <?php if (isset($menuoption['klien_cek']) && $menuoption['klien_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="klien_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['klien_label'])) { echo $menuoption['klien_label']; } else { echo 'Klien';} ?>"/> <input type="text" name="klien_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['klien_desc'])) { echo $menuoption['klien_desc']; } else { echo 'Adalah menu untuk melihat daftar orang-orang yang berhasil anda rekrut';} ?>"/>
	</p>
	<p><input type="checkbox" name="jaringan_cek" value="1" <?php if (isset($menuoption['jaringan_cek']) && $menuoption['jaringan_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="jaringan_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['jaringan_label'])) { echo $menuoption['jaringan_label']; } else { echo 'Jaringan';} ?>"/> <input type="text" name="jaringan_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['jaringan_desc'])) { echo $menuoption['jaringan_desc']; } else { echo 'Adalah menu untuk melihat seluruh jaringan yang ada di bawah anda';} ?>"/>
	</p>
	<p><input type="checkbox" name="download_cek" value="1" <?php if (isset($menuoption['download_cek']) && $menuoption['download_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="download_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['download_label'])) { echo $menuoption['download_label']; } else { echo 'Download';} ?>"/> <input type="text" name="download_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['download_desc'])) { echo $menuoption['download_desc']; } else { echo 'Adalah menu untuk mendownload produk-produk sudah anda beli';} ?>"/>
	</p>
	<p><input type="checkbox" name="upgrade_cek" value="1" <?php if (isset($menuoption['upgrade_cek']) && $menuoption['upgrade_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="upgrade_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['upgrade_label'])) { echo $menuoption['upgrade_label']; } else { echo 'Upgrade';} ?>"/> <input type="text" name="upgrade_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['upgrade_desc'])) { echo $menuoption['upgrade_desc']; } else { echo 'Adalah menu untuk mengupgrade keanggotaan anda menjadi premium member';} ?>"/>
	</p>
	<p><input type="checkbox" name="logout_cek" value="1" <?php if (isset($menuoption['logout_cek']) && $menuoption['logout_cek'] == 1) { echo 'checked="checked" ';}?>/>
	<input type="text" name="logout_label" style="width:100%; max-width: 150px" value="<?php if (isset($menuoption['logout_label'])) { echo $menuoption['logout_label']; } else { echo 'Logout';} ?>"/> <input type="text" name="logout_desc" style="width:100%; max-width: 600px" value="<?php if (isset($menuoption['logout_desc'])) { echo $menuoption['logout_desc']; } else { echo 'Adalah menu untuk menutup dan mengakhiri sesi login anda';} ?>"/>
	</p>
	<p><input type="checkbox" name="keterangan_cek" value="1" <?php if (isset($menuoption['keterangan_cek']) && $menuoption['keterangan_cek'] == 1) { echo 'checked="checked" ';}?>/> Tampilkan keterangan masing-masing menu</p>
	<h4>Pengaturan Informasi Terbaru</h4>
	<p>Pilih Kategori yang akan dimunculkan di bagian depan memberarea. Untuk memilih semua kategori, pilih <code>Tampilkan Semua</code>. Untuk menghilangkan fasilitas ini, pilih <code>Jangan Munculkan Apapun</code>. Untuk memilih lebih dari 1 kategori, tekan tombol Ctrl dan klik kategori-kategori yg anda inginkan</p>
	<div class="form-row">
	<?php 
	$args = array(
		'show_option_none'  => 'Jangan Munculkan Apapun',
		'option_none_value'  => "0",
		'class' 	=> 'form-control col-5 mb-3',
		'taxonomy'	=> 'category',
		'echo'		=> 0,
		'name'		=> 'infobaru[]'
	);

	$select = wp_dropdown_categories( $args );
	$select = str_replace('<select  ', '<select multiple ', $select);
	$select = str_replace("<option value='0'", '<option value="-1">Tampilkan Semua</option><option value="0"', $select);
	$select = str_replace(" selected='selected'", "", $select);
	if (isset($menuoption['infobaru']) && is_array($menuoption['infobaru'])) {
		foreach ($menuoption['infobaru'] as $key => $value) {
			$select = str_replace('value="'.$value.'"', 'value="'.$value.'" selected', $select);
		}
	}
	echo $select;
	?>
	</div>
	<p><input type="submit" class="button button-primary" value="Update"></p>
	</form>
</div>
<?php
endif;
?>