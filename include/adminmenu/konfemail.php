<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!current_user_can('manage_options')) { die; exit(); }
?>
<div class="wrap">
	<h1 class="wp-heading-inline">Pengaturan Email</h1>
	<a class="page-title-action" data-bs-toggle="collapse" href="#advanced" role="button" aria-expanded="false">Bantuan ⮟</a>	
	<div class="collapse" id="advanced">	
	<p>Gunakan kode ini untuk menampilkan data member/sponsor:<br/>
	<code>[member_FIELD]</code>: untuk data member<br/>
	<code>[sponsor_FIELD]</code>: untuk data sponsor</p>

	<p>Ganti tulisan <strong>FIELD</strong> dengan nama field WP Affiliasi. Daftar lengkap nama field dapat dilihat di file <a href="https://cafebisnis.com/readme.txt">readme.txt</a></p>

	<p><strong>Contoh:</strong><br/>
	<code>[member_nama]</code>: untuk menampilkan nama member<br/>
	<code>[sponsor_telp]</code> : untuk menampilkan nomor telpon sponsor</p>

	<p><strong>Kode khusus:</strong><br/>
	<code>[produk_orderid]</code> : ID Order<br/>
	<code>[produk_nama]</code> : Nama Produk<br/>
	<code>[produk_diskripsi]</code> : Diskripsi Produk<br/>
	<code>[produk_harga]</code> : Harga Produk<br/>
	<code>[produk_hargaunik]</code> : Hrg Produk + angka unik</p>
	<p><a href="https://cafebisnis.com/pwa.php?id=17" target="_blank" class="btn btn-success">
			<span class="dashicons dashicons-controls-play"></span> Video Panduan Pengaturan Email</a></p>
	</div>	
	<hr class="wp-header-end">
	<?php
	$konfemail = get_option('konfemail');
	$listform = array(
		'registrasi_admin' => 'Email Registrasi utk Admin',
		'registrasi_member' => 'Email Registrasi utk Member',
		'registrasi_sponsor' => 'Email Registrasi utk Sponsor',
		'upgrade_member' => 'Email Upgrade utk Member',
		'upgrade_sponsor' => 'Email Upgrade utk Sponsor',
		'beli_member' => 'Email Beli Produk Lain utk Member',
		'beli_sponsor' => 'Email Beli Produk Lain utk Sponsor',
		'prosesbeli_member' => 'Email Aktifasi Produk Lain utk Member',
		'prosesbeli_sponsor' => 'Email Aktifasi Produk Lain utk Sponsor',
		'woo_member' => 'Email Complete Order Woocommerce utk Member',
		'woo_sponsor' => 'Email Complete Order Woocommerce utk Sponsor',
		'komisi_member' => 'Email Pencairan Komisi untuk Member'
	);

	if (isset($_POST['nama_email']) && $_POST['nama_email'] != '' && isset($_POST['alamat_email']) && $_POST['alamat_email'] != '') {
		$konfemail['nama_email'] = sanitize_text_field($_POST['nama_email']);
		$konfemail['alamat_email'] = sanitize_email($_POST['alamat_email']);

		foreach ($listform as $keyform => $valform) {
			$konfemail['judul_'.$keyform] = sanitize_text_field($_POST['judul_'.$keyform]);
			$konfemail['isi_'.$keyform] = sanitize_textarea_field($_POST['isi_'.$keyform]);
		}

		update_option('konfemail', $konfemail);

		echo '<div class="notice notice-success is-dismissible"><p>Pengaturan Email Telah Diperbarui</p></div>';
	}
	?>

<form action="" method="post">
<div class="accordion" id="accordion">
  <div class="accordion-item">    
    <div class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" style="border: solid 1px #cccccc; margin-bottom:5px; padding:5px; width:100%; text-align:left" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        <strong>DATA UMUM</strong>
      </button>
    </div>
    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordion">
      <div class="accordion-body">
        <div class="mb-3 row">
          <label class="col-sm-3 col-form-label">Nama Anda</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="nama_email" value="<?php echo stripslashes($konfemail['nama_email']); ?>">
          </div>
        </div>
        <div class="mb-3 row">
          <label class="col-sm-3 col-form-label">Alamat Email Anda</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="alamat_email" value="<?php echo stripslashes($konfemail['alamat_email']); ?>">
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
  foreach ($listform as $keyform => $valform) {
    echo '
    <div class="accordion-item">
      <div class="accordion-header" id="head'.$keyform.'">
        <button class="accordion-button collapsed" style="border: solid 1px #cccccc; margin-bottom:5px; padding:5px; width:100%; text-align:left" type="button" data-bs-toggle="collapse" data-bs-target="#col'.$keyform.'" aria-expanded="false" aria-controls="col'.$keyform.'">
          <strong>'.$valform.'</strong>
        </button>
      </div>
      <div id="col'.$keyform.'" class="accordion-collapse collapse" aria-labelledby="head'.$keyform.'" data-bs-parent="#accordion">
        <div class="accordion-body">
          <div class="mb-3">
            <input type="text" class="form-control" name="judul_'.$keyform.'" value="'.stripslashes($konfemail['judul_'.$keyform] ??= '').'">';
            
            $editorarr = array(
              'textarea_name' => 'isi_'.$keyform
            );
            
            $mailkonten = stripslashes($konfemail['isi_'.$keyform] ??= '');
            $mailkonten = html_entity_decode($mailkonten);
            wp_editor($mailkonten, 'isi_'.$keyform, $editorarr);

            echo '
          </div>
        </div>
      </div>
    </div>';
  }
  ?>
</div>


	 	<div class="form-group row">
	 		<label class="col-sm-3 col-form-label"></label>
	 		<div class="col-sm-9"><input type="submit" name="submit" class="button button-primary" value="Update Konfigurasi"></div>
	 	</div>
	</form>
</div>