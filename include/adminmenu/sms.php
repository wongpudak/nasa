<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!current_user_can('manage_options')) { die; exit(); } ?>
<div class="wrap">
	<h1 class="wp-heading-inline">Pengaturan SMS</h1>
	<a class="page-title-action" data-bs-toggle="collapse" href="#advanced" role="button" aria-expanded="false">Bantuan ⮟</a>
	<hr class="wp-header-end">
	<div class="collapse" id="advanced">	
	<h3>Kode-kode SMS :</h3>
	<p><code>[member_FIELD]</code> : untuk data member<br/>
		<code>[sponsor_FIELD]</code> : untuk data sponsor</p>
	<p>Ganti tulisan FIELD dengan nama field WP Affiliasi. Daftar lengkap nama field dapat dilihat di file <a href="<?php echo site_url();?>/wp-content/plugins/wp-affiliasi/readme.txt">readme.txt</a></p>
	<p>Contoh : <code>[member_nama]</code> : untuk menampilkan nama member<br/>
		<code>[sponsor_telp]</code> : untuk menampilkan nomor telpon sponsor</p>
	<p><strong>Kode khusus pembelian produk:</strong><br/>
		<code>[produk_orderid]</code> : ID Order<br/>
		<code>[produk_nama]</code> : Nama Produk<br/>
		<code>[produk_diskripsi]</code> : Diskripsi Produk<br/>
		<code>[produk_harga]</code> : Harga Produk<br/>
		<code>[produk_hargaunik]</code> : Hrg Produk + angka unik</p>
	<p><strong>Kode Khusus pembayaran Komisi:</strong><br/>
	<code>[komisi]</code>: jumlah komisi</strong>
	<p><a href="https://cafebisnis.com/pwa.php?id=34" target="_blank" class="btn btn-success">
			<span class="dashicons dashicons-controls-play"></span> Video Panduan Pengaturan SMS</a></p>	
	</div>
<?php	
	$smsoption = get_option('smsoption');
	
	if (isset($_POST['sms_urlapi'])) {
		$smsoption = array();
		foreach ($_POST as $key => $value) {
			$smsoption[$key] = sanitize_text_field($value);
		}
		
		update_option("smsoption",$smsoption);
		echo '<div class="notice notice-success is-dismissible"><p>Pengaturan SMS Telah Diperbarui</p></div>';
	}
?>
<form action="" method="post">
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">SMS API Key</label>
	    <div class="col-sm-6"><input type="text" name="sms_urlapi" class="form-control" value="<?php if (isset($smsoption['sms_urlapi'])) { echo $smsoption['sms_urlapi']; } ?>"/>
		<small class="form-text text-muted">Isi dengan alamat URL API kirim SMS</small></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">POST Konten</label>
	    <div class="col-sm-6">
	    	<?php for ($i=1; $i <= 5; $i++) : ?>
	    	<div class="row">
	    		<div class="col-sm-6">
		    		<input type="text" name="sms_field<?php echo $i;?>" placeholder="Field" class="form-control" value="<?php if (isset($smsoption['sms_field'.$i])) { echo $smsoption['sms_field'.$i]; } ?>"/>
		    	</div>
		    	<div class="col-sm-6">
		    		<input type="text" name="sms_value<?php echo $i;?>" placeholder="Value" class="form-control" value="<?php if (isset($smsoption['sms_value'.$i])) { echo $smsoption['sms_value'.$i]; } ?>"/>
		    	</div>
		    </div>
		<?php endfor; ?>
		<small class="form-text text-muted">Isi dengan nama field post dan value post API_nya</small>
		</div>
	</div>
	
	<h4>SMS untuk Member</h4>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Daftar</label>
	    <div class="col-sm-6"><input type="text" name="sms_registrasi_member" class="form-control" value="<?php if (isset($smsoption['sms_registrasi_member'])) { echo $smsoption['sms_registrasi_member']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Upgrade</label>
	    <div class="col-sm-6"><input type="text" name="sms_upgrade_member" class="form-control" value="<?php if (isset($smsoption['sms_upgrade_member'])) { echo $smsoption['sms_upgrade_member']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Beli Produk</label>
	    <div class="col-sm-6"><input type="text" name="sms_beli_member" class="form-control" value="<?php if (isset($smsoption['sms_beli_member'])) { echo $smsoption['sms_beli_member']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Order Diproses</label>
	    <div class="col-sm-6"><input type="text" name="sms_prosesbeli_member" class="form-control" value="<?php if (isset($smsoption['sms_prosesbeli_member'])) { echo $smsoption['sms_prosesbeli_member']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Komisi Dibayar</label>
	    <div class="col-sm-6"><input type="text" name="sms_komisi_member" class="form-control" value="<?php if (isset($smsoption['sms_komisi_member'])) { echo $smsoption['sms_komisi_member']; } ?>"/></div>
	</div>
	<h4>SMS untuk Sponsor</h4>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Member Daftar</label>
	    <div class="col-sm-6"><input type="text" name="sms_registrasi_sponsor" class="form-control" value="<?php if (isset($smsoption['sms_registrasi_sponsor'])) { echo $smsoption['sms_registrasi_sponsor']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Member Upgrade</label>
	    <div class="col-sm-6"><input type="text" name="sms_upgrade_sponsor" class="form-control" value="<?php if (isset($smsoption['sms_upgrade_sponsor'])) { echo $smsoption['sms_upgrade_sponsor']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Member Beli Produk</label>
	    <div class="col-sm-6"><input type="text" name="sms_beli_sponsor" class="form-control" value="<?php if (isset($smsoption['sms_beli_sponsor'])) { echo $smsoption['sms_beli_sponsor']; } ?>"/></div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">Saat Order Member Diproses</label>
	    <div class="col-sm-6"><input type="text" name="sms_prosesbeli_sponsor" class="form-control" value="<?php if (isset($smsoption['sms_prosesbeli_sponsor'])) { echo $smsoption['sms_prosesbeli_sponsor']; } ?>"/></div>
	</div>

	<div class="form-group row">
		<input type="submit" class="button button-primary" value="Ubah Pengaturan"/>
	</div>
</form>

</div>