<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!current_user_can('manage_options')) { die; exit(); }
$jumlah_field = 15;
if (isset($_POST['free']) && is_array($_POST['free'])) {
	update_option('konfautoresponder', $_POST); 
	echo '<div class="notice notice-success is-dismissible"><p>Pengaturan Autoresponder Berhasil! </p></div>';
}

$options = get_option('konfautoresponder');
?>
<div class="wrap">
<h1 class="wp-heading-inline">Integrasi dg Autoresponder</h1>
<a class="page-title-action" data-bs-toggle="collapse" href="#advanced" role="button" aria-expanded="false">Bantuan ⮟</a>
<hr class="wp-header-end">
<div class="collapse" id="advanced">	
<ul>
	<li>Copy kode HTML form dari autoresponder favorit anda</li>
	<li>Gunakan <a href="https://cafebisnis.com/tool/extractor" target="_blank">Form Extractor</a> untuk memisahkan field name dan value dari kode HTML</li>
	<li>Isi kolom name sesuai dg hasil extractor</li>
	<li>Untuk isian value yg berisi data member dan sponsor ganti dengan kode <code>[member_<strong>FIELD</strong>]</code> dan <code>[sponsor_<strong>FIELD</strong>]</code></li>
	<li>Ganti kode <code><strong>FIELD</strong></code> dengan kode data yg anda inginkan</li>
	<li>Contoh:<br/>
		- Untuk menampilkan nama, gunakan <code>[member_nama]</code><br/>
		- Untuk menampilkan username, gunakan <code>[member_username]</code><br/>
		- Untuk menampilkan nama sponsor, gunakan <code>[sponsor_nama]</code><br/>
		- Untuk menampilkan email sponsor, gunakan <code>[sponsor_email]</code><br/>
	Kode-kode field yang lain dapat dilihat di file <a href="<?php echo site_url();?>/wp-content/plugins/wp-affiliasi/readme.txt" target="_blank">readme.txt</a></li>
<li><a href="https://cafebisnis.com/pwa.php?id=32" target="_blank" class="btn btn-success">
		<span class="dashicons dashicons-controls-play"></span> Video Panduan Pengaturan Komisi</a></li>
</ul>
</div>

<form action="" method="post">
<div class="row">
    <div class="col-sm-6">
	<fieldset class="the-fieldset">
	<legend class="the-legend">Autoresponder Free Member</legend>
	<div class="form-group row">
	    <label class="col-sm-3 col-form-label">Form Action</label>
	    <div class="col-sm-9"><input type="text" name="free[action]" value="<?php if (isset($options['free']['action'])) { echo $options['free']['action'];}?>" class="form-control"/></div>
	</div>
	<?php
	for ($i=0; $i < $jumlah_field; $i++) { 
	echo '
	<div class="form-row">
	    <div class="col-6">
	      <div class="input-group mb-2">
	        <div class="input-group-prepend">
	          <div class="input-group-text">Name</div>
	        </div>
	        <input type="text" class="form-control" name="free['.$i.'][field]" value="';
	        if (isset($options['free'][$i]['field'])) { 
	        	echo $options['free'][$i]['field'];
	        } else { 
	        	if ($i == 0) {
		        	echo 'nama'; 
		        } elseif ($i == 1) {
		        	echo 'email';
	        	}
	        }
	        echo '" />
	      </div>
	    </div>
	    <div class="col-6">
	      <div class="input-group mb-2">
	        <div class="input-group-prepend">
	          <div class="input-group-text">Value</div>
	        </div>
	        <input type="text" class="form-control" name="free['.$i.'][value]" value="';
	        if (isset($options['free'][$i]['value'])) { 
	        	echo $options['free'][$i]['value'];
	        } else { 
	        	if ($i == 0) {
		        	echo '[member_nama]'; 
		        } elseif ($i == 1) {
		        	echo '[member_email]';
	        	}
	        }
	        echo '" />
	      </div>
	    </div>
	</div>';
	}	
	?>
	</fieldset>
	</div>
	<div class="col-sm-6">
	<fieldset class="the-fieldset">
	<legend class="the-legend">Autoresponder Premium Member</legend>
	<div class="form-group row">
	    <label class="col-sm-3 col-form-label">Form Action</label>
	    <div class="col-sm-9"><input type="text" name="premium[action]" value="<?php if (isset($options['premium']['action'])) { echo $options['premium']['action'];}?>" class="form-control"/></div>
	</div>
	<?php
	for ($i=0; $i < $jumlah_field; $i++) { 
	echo '
	<div class="form-row">
	    <div class="col-6">
	      <div class="input-group mb-2">
	        <div class="input-group-prepend">
	          <div class="input-group-text">Name</div>
	        </div>
	        <input type="text" class="form-control" name="premium['.$i.'][field]" value="';
	        if (isset($options['premium'][$i]['field'])) { 
	        	echo $options['premium'][$i]['field'];
	        } else { 
	        	if ($i == 0) {
		        	echo 'nama'; 
		        } elseif ($i == 1) {
		        	echo 'email';
	        	}
	        }
	        echo '" />
	      </div>
	    </div>
	    <div class="col-6">
	      <div class="input-group mb-2">
	        <div class="input-group-prepend">
	          <div class="input-group-text">Value</div>
	        </div>
	        <input type="text" class="form-control" name="premium['.$i.'][value]" value="';
	        if (isset($options['premium'][$i]['value'])) { 
	        	echo $options['premium'][$i]['value'];
	        } else { 
	        	if ($i == 0) {
		        	echo '[member_nama]'; 
		        } elseif ($i == 1) {
		        	echo '[member_email]';
	        	}
	        }
	        echo '" />
	      </div>
	    </div>
	</div>';
	}	
	?>
	</fieldset>
	</div>
</div>
	<input type="submit" class="button button-primary" value="Ubah Pengaturan"/>
</form>
</div>