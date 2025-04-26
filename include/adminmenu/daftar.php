<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (current_user_can( 'activate_plugins' )) : 
$profil = '';
?>
<div class="wrap">
	<?php

	if (isset($_POST['form'])) {		

		$i = 1;
		$ok = 0;
		foreach ($_POST['form'] as $dform) {
			if ($dform['field'] != '') {
			if (!isset($dform['register'])) { $register = 0; } else { $register = 1;}
			if (!isset($dform['profil'])) { $profil = 0; } else { $profil = 1;}
			if (!isset($dform['jaringan'])) { $jaringan = 0; } else { $jaringan = 1;}
			if (!isset($dform['required'])) { $required = 0; } else { $required = 1;}
			$field = sanitize_text_field($dform['field']);
			$label = sanitize_text_field($dform['label']);
			$option = sanitize_text_field($dform['option']);
			$info = sanitize_text_field($dform['info']);
			$getform[$i] = array('register'	=>$register, 
								'profil'	=>$profil, 
								'jaringan'	=>$jaringan, 
								'required'	=>$required, 
								'field'		=>$field, 
								'label'		=>$label, 
								'option'	=>$option,
								'info'		=>$info);
			if (($field == 'nama' && $register ==1) || ($field == 'email' && $register == 1)) {
				$ok++;
			}
			$i++;
			}
		}
		$aturform = serialize($getform);
		update_option("aturform", $aturform);
		if ($ok >= 2) {
		echo '<div class="notice notice-success is-dismissible"><p>Update berhasil</p></div>';
		} else {
		echo '<div class="notice notice-error is-dismissible"><p>Peringatan! Isian Nama dan Email tidak aktif di form registrasi</p></div>';
		}
	}

	?>
<h1 class="wp-heading-inline">Pengaturan Form</h1>
<a class="page-title-action" data-bs-toggle="collapse" href="#advanced" role="button" aria-expanded="false" aria-controls="info'.$grup->idgrup.'">Bantuan Form ⮟</a>
<hr class="wp-header-end">
<div class="collapse" id="advanced">
	<p>Silahkan klik Tambah Item untuk menambah kolom isian. Anda bisa menyeret posisi isian untuk mengubah urutannya.</p>
<ul>
	<li><i class="dashicons dashicons-welcome-write-blog"></i> = <strong>Registrasi</strong>. Beri centang jika anda ingin menampilkan data ini di halaman registrasi</li>
	<li><i class="dashicons dashicons-admin-users"></i> = <strong>Profil</strong>. Beri centang jika anda ingin menampilkan data ini di halaman profil</li>
	<li><i class="dashicons dashicons-share"></i> = <strong>jaringan</strong>. Beri centang jika anda ingin menampilkan data ini di menu jaringan</li>
	<li><i class="dashicons dashicons-lock"></i> = <strong>Required</strong>. Beri centang jika anda ingin agar isian ini wajib diisi</li>
	<li><strong>Field :</strong> Pilih tabel isian yang ingin ditampilkan</li>
	<li><strong>Label :</strong> Isilah dengan label anda sendiri. Jika dikosongi, maka label default yang akan dipakai</li>
	<li><strong>Option:</strong> Isi dg dropdown option dan pisahkan tiap option menggunakan koma. Contoh: <code>BCA,BNI,BRI,Mandiri</code></li>
	<li><strong>Keterangan:</strong> petunjuk yang akan ditampilkan di bawah formulir</li>
	<li><a href="https://cafebisnis.com/pwa.php?id=10" target="_blank" class="btn btn-success">
		<span class="dashicons dashicons-controls-play"></span> Video Panduan Pengaturan Form</a></li>
</ul>
</div>
<?php 
$aturform = get_option('aturform');
	$form = unserialize($aturform);
	if ($form) { $countstart=count($form);} else { $countstart = 2; }
?>
<script type="text/javascript">
	var counter = <?php echo $countstart+1;?>;
	var limit = 50;
	function addInput(divName){
		 if (counter > limit)  {
			  alert("Batas Kedalaman " + (counter-1) + " level");
		 }
		 else {
			  var newdiv = document.createElement('li');
			  newdiv.className = 'form-group row';
			  newdiv.innerHTML = '<div class="col-sm-12 col-lg-2"><div class="row"><div class="col-sm-12 col-lg-3"><input type="checkbox" class="form-control" name="form['+counter+'][register]" value="1"/></div><div class="col-sm-12 col-lg-3"><input type="checkbox" class="form-control" name="form['+counter+'][profil]" value="1"/></div><div class="col-sm-12 col-lg-3"><input type="checkbox" class="form-control" name="form['+counter+'][jaringan]" value="1"/></div><div class="col-sm-12 col-lg-3"><input type="checkbox" class="form-control" name="form['+counter+'][required]" value="1"/></div></div></div><div class="col-sm-12 col-lg-2"><select class="form-control" name="form['+counter+'][field]"><option value="">&nbsp;</option><option value="id_tianshi">ID MLM</option><option value="nama">Nama</option><option value="email">Email</option><option value="ktp">No. KTP</option><option value="tgl_lahir">Tanggal Lahir</option><option value="alamat">Alamat</option><option value="kota">Kota</option><option value="provinsi">Provinsi</option><option value="kodepos">Kodepos</option><option value="telp">No. Telp / HP</option><option value="ktp_istri">No. KTP Pasangan</option><option value="nama_istri">Nama Pasangan</option><option value="tgl_lahir_istri">Tgl Lahir Pasangan</option><option value="ac">Atas Nama</option><option value="bank">Nama Bank</option><option value="rekening">No. Rekening</option><option value="kelamin">Jenis Kelamin</option><option value="username">Username</option><option value="password">Password</option><option value="subdomain">URL Affiliasi</option><option value="ym">Yahoo Messenger</option><option value="keterangan">Keterangan</option><option value="customwhatsapp">WhatsApp</option><option value="kodesponsor">Kode Sponsor</option><option value="custom">Custom Field</option></select></div><div class="col-sm-12 col-lg-2"><input type="text" name="form['+counter+'][label]" placeholder="Label" class="form-control"/></div><div class="col-sm-12 col-lg-3"><input type="text" placeholder="Option" name="form['+counter+'][option]" class="form-control"/></div><div class="col-sm-12 col-lg-3"><input type="text" name="form['+counter+'][info]" class="form-control" placeholder="Keterangan" /></div>';
			  document.getElementById(divName).appendChild(newdiv);
			  counter++;
		 }
	}
</script>
<form action="" method="post">
<ul id="sortable3" class="droptrue">
	<li class="row form-group ui-state-disabled">
		<div class="col-sm-12 col-lg-2">
			<div class="row">
			<div class="col col-lg-3"><i class="dashicons dashicons-welcome-write-blog" title="Munculkan di Form Registrasi"></i></div>
			<div class="col col-lg-3"><i class="dashicons dashicons-admin-users" title="Munculkan di Form Profil"></i></div>
			<div class="col col-lg-3"><i class="dashicons dashicons-share" title="Munculkan di Jaringan"></i></div>
			<div class="col col-lg-3"><i class="dashicons dashicons-lock" title="Isian yang Wajib Diisi"></i></div>
			</div>
		</div>
		<div class="col-sm-12 col-lg-2">Field</div>
		<div class="col-sm-12 col-lg-2">Label</div>
		<div class="col-sm-12 col-lg-3">Option</div>
		<div class="col-sm-12 col-lg-3">Keterangan</div>
	</li>
	<?php
	$id_tianshi = $nama = $email = $ktp = $tgl_lahir = $alamat = $kota = $provinsi = $kodepos = $telp = $ktp_istri = $nama_istri = $tgl_lahir_istri = $ac = $bank = $rekening = $kelamin = $username = $password = $subdomain = $ym = $keterangan = $customwhatsapp = $custom = $kodesponsor = '';
	if (is_array($form)) {
		$i = 1;
		foreach ($form as $form) {
			if (isset($form['register']) && $form['register'] == 1) { $register = 'checked'; } else { $register = ''; }
			if (isset($form['profil']) && $form['profil'] == 1) { $profil = 'checked'; } else { $profil = ''; }
			if (isset($form['jaringan']) && $form['jaringan'] == 1) { $jaringan = 'checked'; } else { $jaringan = ''; }
			if (isset($form['required']) && $form['required'] == 1) { $required = 'checked'; } else { $required = ''; }
			if (isset($form['info'])) { $info = $form['info']; } else { $info = ''; }
			${$form['field']} = 'selected';
		echo '
		<li class="row form-group">
			<div class="col-sm-12 col-lg-2">
				<div class="row">
				<div class="col col-lg-3"><input type="checkbox" class="form-control" name="form['.$i.'][register]" value="1" '.$register.'/></div>
				<div class="col col-lg-3"><input type="checkbox" class="form-control" name="form['.$i.'][profil]" value="1" '.$profil.'/></div>
				<div class="col col-lg-3"><input type="checkbox" class="form-control" name="form['.$i.'][jaringan]" value="1" '.$jaringan.'/></div>
				<div class="col col-lg-3"><input type="checkbox" class="form-control" name="form['.$i.'][required]" value="1" '.$required.'/></div>
				</div>
			</div>
			<div class="col-sm-12 col-lg-2"><select class="form-control" name="form['.$i.'][field]">
				<option value="">&nbsp;</option>
				<option value="id_tianshi" '.$id_tianshi.'>ID MLM</option>
				<option value="nama" '.$nama.'>Nama</option>
				<option value="email" '.$email.'>Email</option>
				<option value="ktp" '.$ktp.'>No. KTP</option>
				<option value="tgl_lahir" '.$tgl_lahir.'>Tanggal Lahir</option>
				<option value="alamat" '.$alamat.'>Alamat</option>
				<option value="kota" '.$kota.'>Kota</option>
				<option value="provinsi" '.$provinsi.'>Provinsi</option>
				<option value="kodepos" '.$kodepos.'>Kodepos</option>
				<option value="telp" '.$telp.'>No. Telp / HP</option>
				<option value="ktp_istri" '.$ktp_istri.'>No. KTP Pasangan</option>
				<option value="nama_istri" '.$nama_istri.'>Nama Pasangan</option>
				<option value="tgl_lahir_istri" '.$tgl_lahir_istri.'>Tgl Lahir Pasangan</option>
				<option value="ac" '.$ac.'>Atas Nama</option>
				<option value="bank" '.$bank.'>Nama Bank</option>
				<option value="rekening" '.$rekening.'>No. Rekening</option>
				<option value="kelamin" '.$kelamin.'>Jenis Kelamin</option>
				<option value="username" '.$username.'>Username</option>
				<option value="password" '.$password.'>Password</option>
				<option value="subdomain" '.$subdomain.'>URL Affiliasi</option>
				<option value="ym" '.$ym.'>Yahoo Messenger</option>
				<option value="keterangan" '.$keterangan.'>Keterangan</option>
				<option value="customwhatsapp" '.$customwhatsapp.'>WhatsApp</option>
				<option value="kodesponsor" '.$kodesponsor.'>Kode Sponsor</option>
				<option value="custom" '.$custom.'>Custom Field</option>
			</select></div>
			<div class="col-sm-12 col-lg-2"><input type="text" name="form['.$i.'][label]" class="form-control" placeholder="Label" value="'.$form['label'].'"/></div>
			<div class="col-sm-12 col-lg-3"><input type="text" name="form['.$i.'][option]" class="form-control" placeholder="Option" value="'.$form['option'].'"/></div>
			<div class="col-sm-12 col-lg-3"><input type="text" name="form['.$i.'][info]" class="form-control" placeholder="Keterangan" value="'.$info.'"/></div>
		
		</li>';
		${$form['field']} = '';
		$i++;
		}
	} else {
		echo '
		<li class="row form-group">
			<div class="col-sm-12 col-lg-1">
				<div class="row">
				<div class="col col-lg-4"><input type="checkbox" class="form-control" name="form[1][register]" value="1"/></div>
				<div class="col col-lg-4"><input type="checkbox" class="form-control" name="form[1][profil]" value="1"/></div>
				<div class="col col-lg-4"><input type="checkbox" class="form-control" name="form[1][required]" value="1"/></div>
				</div>
			</div>
			<div class="col-sm-12 col-lg-2"><select class="form-control" name="form[1][field]">
				<option value="">&nbsp;</option>
				<option value="id_tianshi" '.$id_tianshi.'>ID MLM</option>
				<option value="nama" '.$nama.'>Nama</option>
				<option value="email" '.$email.'>Email</option>
				<option value="ktp" '.$ktp.'>No. KTP</option>
				<option value="tgl_lahir" '.$tgl_lahir.'>Tanggal Lahir</option>
				<option value="alamat" '.$alamat.'>Alamat</option>
				<option value="kota" '.$kota.'>Kota</option>
				<option value="provinsi" '.$provinsi.'>Provinsi</option>
				<option value="kodepos" '.$kodepos.'>Kodepos</option>
				<option value="telp" '.$telp.'>No. Telp / HP</option>
				<option value="ktp_istri" '.$ktp_istri.'>No. KTP Pasangan</option>
				<option value="nama_istri" '.$nama_istri.'>Nama Pasangan</option>
				<option value="tgl_lahir_istri" '.$tgl_lahir_istri.'>Tgl Lahir Pasangan</option>
				<option value="ac" '.$ac.'>Atas Nama</option>
				<option value="bank" '.$bank.'>Nama Bank</option>
				<option value="rekening" '.$rekening.'>No. Rekening</option>
				<option value="kelamin" '.$kelamin.'>Jenis Kelamin</option>
				<option value="username" '.$username.'>Username</option>
				<option value="password" '.$password.'>Password</option>
				<option value="subdomain" '.$subdomain.'>URL Affiliasi</option>
				<option value="ym" '.$ym.'>Yahoo Messenger</option>
				<option value="keterangan" '.$keterangan.'>Keterangan</option>
				<option value="customwhatsapp" '.$customwhatsapp.'>WhatsApp</option>
				<option value="kodesponsor" '.$kodesponsor.'>Kode Sponsor</option>
				<option value="custom" '.$custom.'>Custom Field</option>
			</select></div>
			<div class="col-sm-12 col-lg-2"><input type="text" name="form[1][label]" class="form-control" placeholder="Label" value=""/></div>
			<div class="col-sm-12 col-lg-3"><input type="text" name="form[1][option]" class="form-control" placeholder="Option" value=""/></div>
			<div class="col-sm-12 col-lg-4"><input type="text" name="form[1][info]" class="form-control" placeholder="Keterangan" value=""/></div>
		</li>';
	}

	?>
</ul>
<div style="clear:both;"></div>
<p><input type="button" value="Tambah Item" class="button button-secondary" onClick="addInput('sortable3');">
<input type="submit" class="button button-primary" value="Update"/></p>
</form>
<?php endif; ?>