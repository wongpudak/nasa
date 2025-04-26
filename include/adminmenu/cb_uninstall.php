<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; }?>
<div class="wrap">
	<h1 class="wp-heading-inline">Uninstall WP Affiliasi</h1>
	<?php
	if (isset($_POST['siap'])) {
		if ($_POST['siap']=='reset') {
			$wpdb->query("DELETE FROM `wp_member` WHERE `idwp` != ".$user_ID);
			$wpdb->query("UPDATE `wp_member` SET `downline_lngsg`=0, `jml_downline`=0, `jml_voucher`=0, `sisa_voucher`=0 WHERE `idwp`=".$user_ID);
			$wpdb->query("DELETE FROM `{$wpdb->prefix}users` WHERE `ID` != ".$user_ID);
			$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `user_id` != ".$user_ID);
			$wpdb->query("TRUNCATE TABLE  `cb_laporan`");
			$wpdb->query("TRUNCATE TABLE  `cb_produklain`");

			echo '<div class="updated"><p>Data user, data order dan data keuangan telah dikosongkan</div>';
		} elseif ($_POST['siap']=='uninstall') {
			
			$options = array('nama_email','alamat_email','notifadmin','notifsponsor','judul_email_daftar','isi_email_daftar','judul_email_aktif','isi_email_aktif','judul_email_sale','isi_email_sale','judul_email_bayar','isi_email_bayar','aturform','cb_pengaturan','wp_affiliasi_version','banner','smsoption','konfemail','konfautoresponder','lisensi');
			foreach ($options as $option_name) {
			 	delete_option( $option_name );
			}

			global $wpdb;
			$wpdb->query("DROP TABLE IF EXISTS `cb_download`, `cb_laporan`, `cb_produk`, `cb_produklain`, `cb_produk_cat`, `wp_member`");

			$tablename = $wpdb->prefix.'users';
			$wpdb->query("DELETE FROM `$tablename` WHERE `ID` != ".$user_ID);
			$tablename = $wpdb->prefix.'usermeta';
			$wpdb->query("DELETE FROM `$tablename` WHERE `user_id` != ".$user_ID);

			echo '<div class="updated"><p>WP Affiliasi telah dibersihkan. Silahkan menuju ke <a href="plugins.php"><strong>menu plugin</strong></a> untuk menghapusnya</div>';
		}
	}
	?>
	<p>Menu ini dapat dipergunakan untuk reset maupun menghapus secara keseluruhan plugin wp-affiliasi. Gunakan dengan bijak.<br/> 
	<strong style="color:#ff0000">Data yang sudah dihapus tidak dapat dikembalikan.</strong><br/>	
	Kami sarankan untuk <strong>membackup</strong> terlebih dahulu database anda sebelum melakukan uninstall</p>
	<p>Silahkan pilih metode yang ingin dilakukan:</p>
	<form action="" method="post">
	<p>		
		<input type="radio" name="siap" value="reset" /> <strong>Reset WP Affiliasi</strong> : Hanya menghapus data order, data laporan keuangan dan semua member kecuali data admin.</p>
	<p>
		<input type="radio" name="siap" value="uninstall" /> <strong>Uninstall WP Affiliasi</strong> : Semua opsi, data dan tabel akan dihapus secara keseluruhan		
	</p>
	<p><input type="submit" class="button button-primary" style="background:#ff0000;border:none" value="Saya Siap!" onclick="javascript:return confirm('Apakah anda yakin akan melakukannya?');"></p>
	</form>
</div>