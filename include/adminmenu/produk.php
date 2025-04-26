<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
$read = site_url().'/wp-admin/admin.php?page=cbaf_produk&';
$iconfolder = plugins_url('wp-affiliasi/img/folder.gif');
$pesan = $folder = $file = '';
?>
<div class="wrap">
	<h1 class="wp-heading-inline">Pengaturan Produk</h1>
	<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=15" 
	target="_blank" role="button" aria-expanded="false">Bantuan</a>
	<div class="row">
	<div class="col-md-8 order-2">
		<?php 
		if (isset($_GET['cat'])) {			
			if (is_numeric($_GET['cat'])) {
				# Tampilkan Form Edit Cat				
				echo '<h3>Edit Kategori</h3>';
				if (isset($_POST['nama']) && $_POST['nama'] != '') {
					$wpdb->query("UPDATE `cb_produk_cat` SET `id_parent`='".sanitize_text_field($_POST['id_parent'])."', 
						`name`='".sanitize_text_field($_POST['nama'])."' 
						WHERE `id_cat`=".$_GET['cat']);
					echo '<div class="notice notice-success is-dismissible">
					<p>Perubahan Kategori telah disimpan</p>
					</div>';
				}
				$kategori = $wpdb->get_row("SELECT * FROM `cb_produk_cat` WHERE `id_cat`=".$_GET['cat']);
				if (!isset($kategori->name)) { $error = 'Kategori tidak ditemukan'; }
			} elseif ($_GET['cat'] == 'new') {
				# Tampilkan Kategori Baru
				echo '<h3>Tambah Kategori</h3>';
				if (isset($_POST['nama']) && $_POST['nama'] != '') {
					$wpdb->query("INSERT INTO `cb_produk_cat` (`id_parent`,`name`) VALUES
						('".sanitize_text_field($_POST['id_parent'])."','".sanitize_text_field($_POST['nama'])."')");
					$id_cat = $wpdb->insert_id;
					echo '<div class="notice notice-success is-dismissible">
					<p>Kategori telah ditambahkan. Silahkan <a href="'.$read.'produk=new&idcat='.$id_cat.'">Tambah Produk</a></p>
					</div>';
				}
			}

			# Tampilkan Form Kategori
			echo '
			<form action="" method="post">
			<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Kategori Induk</label>
	 			<div class="col-md-9">
	 				<select name="id_parent">
	 					<option value="0">Kategori Teratas</option>';
	 					$allcat = $wpdb->get_results("SELECT * FROM `cb_produk_cat` ORDER BY `id_parent`");
	 					foreach ($allcat as $allcat) {	 						
	 						echo '<option value="'.$allcat->id_cat.'"';
	 						if (isset($kategori->id_parent) && $kategori->id_parent == $allcat->id_cat) { echo ' selected'; }
	 						echo '>'.$allcat->name.'</option>';
	 					}
	 		echo '
	 				</select>
	 			</div>
	 		</div>
			<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Nama</label>
	 			<div class="col-md-9">
	 			<input type="text" name="nama" class="form-control" value="'.($kategori->name ?? '').'" required/>
	 			</div>
	 		</div>
			';
			wp_nonce_field( 'acme-settings-save', 'acme-custom-message' );
			submit_button('Simpan');
			if (isset($kategori->id_cat)) {
				echo '<a href="'.$read.'delcat='.$kategori->id_cat.'" class="btn btn-sm btn-danger">Hapus Kategori</a>';
			}
			echo '
			</form>';
			
		} elseif (isset($_GET['produk'])) {
			if (isset($_POST['nama']) && isset($_POST['file'])) {
				$dbupdate = '';
				if (isset($_FILES['thumb_file']['tmp_name']) && is_uploaded_file($_FILES['thumb_file']['tmp_name'])) {
					$wordpress_upload_dir = wp_upload_dir();
					$thumb = $_FILES['thumb_file'];
					$new_file_path = $wordpress_upload_dir['path'] . '/' . $thumb['name'];
					$new_file_mime = mime_content_type( $thumb['tmp_name'] );
					$i = 1;
					if( empty( $thumb ) )
						die( 'File is not selected.' );

					if( $thumb['error'] )
						die( $thumb['error'] );
						
					if( $thumb['size'] > wp_max_upload_size() )
						die( 'File terlalu besar. Max: '. wp_max_upload_size());
						
					if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
						die( 'WordPress tidak mengijinkan file ini.' );
						
					while( file_exists( $new_file_path ) ) {
						$i++;
						$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $thumb['name'];
					}

					if( move_uploaded_file( $thumb['tmp_name'], $new_file_path ) ) {
						$upload_id = wp_insert_attachment( array(
							'guid'           => $new_file_path, 
							'post_mime_type' => $new_file_mime,
							'post_title'     => preg_replace( '/\.[^.]+$/', '', $thumb['name'] ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						), $new_file_path );

						// wp_generate_attachment_metadata() won't work if you do not include this file
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						// Generate and save the attachment metas into the database
						wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

						// Show the uploaded file in browser
						#wp_redirect( $wordpress_upload_dir['url'] . '/' . basename( $new_file_path ) );

						$urlgambar = $wordpress_upload_dir['url'] . '/' . basename( $new_file_path );
						$dbupdate = "`thumb_file`='".$urlgambar."',";
					}
				} 
			}

			if (is_numeric($_GET['produk'])) {
				# Tampilkan Form Edit Cat
				echo '<h3>Edit Produk</h3>';
				if (isset($_POST['nama'])) {
					$wpdb->query("UPDATE `cb_produk` SET 
						`id_cat`=".sanitize_text_field($_POST['id_cat']).",
						`nama`='".sanitize_text_field($_POST['nama'])."',
						`url_file` = '".sanitize_text_field($_POST['file'])."',".$dbupdate."						
						`diskripsi` = '".sanitize_text_field($_POST['diskripsi'])."',
						`count` = ".sanitize_text_field($_POST['salesletter']).",
						`membership` = ".sanitize_text_field($_POST['membership']).",
						`password` = '".sanitize_text_field($_POST['password'])."',
						`harga` = '".sanitize_text_field($_POST['harga'])."',
						`status` = '".sanitize_text_field($_POST['status'])."'
						WHERE `id`= ".$_GET['produk']);

					if ($wpdb->last_error != '') {
						echo '<div class="notice notice-error is-dismissible">
						<p>'.$wpdb->print_error().'</p>
						</div>';
					} else {
						echo '<div class="notice notice-success is-dismissible">
						<p>Perubahan Produk telah disimpan.</p>
						</div>';
					}
				}
				$prodetil = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id`=".$_GET['produk']);
			} elseif ($_GET['produk'] == 'new') {
				# Tampilkan Kategori Baru				
				echo '<h3>Tambah Produk</h3>';
				if (isset($_POST['nama'])) {
					if (!isset($urlgambar)) {
						$urlgambar = '';
					}

					if (isset($_GET['idcat']) && is_numeric($_GET['idcat'])) {
						$wpdb->query("INSERT INTO `cb_produk` 
							(`id_cat`,`nama`,`url_file`,`thumb_file`,`diskripsi`,`count`,`membership`,`password`,`harga`,`status`) VALUES 
							(".sanitize_text_field($_POST['id_cat']).",'".sanitize_text_field($_POST['nama'])."',
							'".sanitize_text_field($_POST['file'])."',
							'".$urlgambar."',
							'".sanitize_text_field($_POST['diskripsi'])."',
							".sanitize_text_field($_POST['salesletter']).",
							".sanitize_text_field($_POST['membership']).",
							'".sanitize_text_field($_POST['password'])."',
							'".sanitize_text_field($_POST['harga'])."',
							'".sanitize_text_field($_POST['status'])."')");
						if ($wpdb->last_error != '') {
							echo '<div class="notice notice-error is-dismissible">
							<p>'.$wpdb->print_error().'</p>
							</div>';
						} else {
							echo '<div class="notice notice-success is-dismissible">
							<p>Produk telah ditambahkan.</p>
							</div>';
						}
					} else {
						echo '<div class="notice notice-error is-dismissible">
						<p>Kategori belum dipilih.</p>
						</div>';
					}
				}
			}

			# Tampilkan Form Produk
			$jenis = array('','','',' selected');
			$status = array('',' selected');
			if (isset($prodetil->membership)) { $jenis[3] = ''; $jenis[$prodetil->membership] = ' selected'; }
			if (isset($prodetil->status)) { $status[1] = ''; $status[$prodetil->status] = ' selected'; }

			if (isset($prodetil->thumb_file) && $prodetil->thumb_file != '') {
				echo '<p class="text-center"><img src="'.$prodetil->thumb_file.'" class="img-thumbnail" style="max-height:200px;"/></p>';
			}
			echo '
			<form action="" method="post" enctype="multipart/form-data">			
			<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Kategori Induk</label>
	 			<div class="col-md-9">
	 				<select name="id_cat">';
	 					$allcat = $wpdb->get_results("SELECT * FROM `cb_produk_cat` ORDER BY `id_parent`");
	 					foreach ($allcat as $allcat) {	 						
	 						echo '<option value="'.$allcat->id_cat.'"';
	 						if (isset($prodetil->id_cat) && $prodetil->id_cat == $allcat->id_cat) { echo ' selected'; }
	 						echo '>'.$allcat->name.'</option>';
	 					}
	 		echo '
	 				</select>
	 			</div>
	 		</div>
			<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Nama</label>
	 			<div class="col-md-9">
	 			<input type="text" name="nama" class="form-control" value="'.($prodetil->nama ?? '').'" required/>
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Diskripsi</label>
	 			<div class="col-md-9">
	 			<input type="text" name="diskripsi" class="form-control" value="'.($prodetil->diskripsi ?? '').'" />
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Landing Page</label>
	 			<div class="col-md-9">';
	 			$args = array(
	 				'class' => 'form-control',
	 				'show_option_none' => 'Tanpa Landing Page',
	 				'name' => 'salesletter',
	 				'selected' => ($prodetil->count ?? ''),
	 				'option_none_value' => 0
	 			);
	 		wp_dropdown_pages($args);
	 		echo '
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Nama / URL File</label>
	 			<div class="col-md-9">
	 			<input type="text" name="file" class="form-control" value="'.($prodetil->url_file ?? '').'" />
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Foto Produk</label>
	 			<div class="col-md-9">
	 			<input type="file" name="thumb_file" class="form-control" value="'.($prodetil->thumb_file ?? '').'" />
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Password</label>
	 			<div class="col-md-9">
	 			<input type="text" name="password" class="form-control" value="'.($prodetil->password ?? '').'" />
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Harga (khusus produk terpisah)</label>
	 			<div class="col-md-9">
	 			<input type="text" name="harga" class="form-control" value="'.($prodetil->harga ?? '').'" />
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Jenis Produk</label>
	 			<div class="col-md-9">
	 			<select name="membership" class="form-control">
	 			<option value="1" '.$jenis[1].'>Bonus Free Member</option>
	 			<option value="2" '.$jenis[2].'>Bonus Premium Member</option>
	 			<option value="3" '.$jenis[3].'>Produk Terpisah</option>
	 			</select>
	 			</div>
	 		</div>
	 		<div class="form-group row">
	 			<label class="col-md-3 col-form-label">Status</label>
	 			<div class="col-md-9">
	 			<select name="status" class="form-control">
	 			<option value="1" '.$status[1].'>Aktif</option>
	 			<option value="0" '.$status[0].'>Non Aktif</option>
	 			</select>
	 			</div>
	 		</div>
	 		
			';
			wp_nonce_field( 'acme-settings-save', 'acme-custom-message' );
			submit_button('Simpan');
			if (isset($prodetil->id)) {
				echo '<a href="'.$read.'delpro='.$prodetil->id.'" class="btn btn-sm btn-danger">Hapus Produk</a>';
			}
			echo '
			</form>';
		} elseif (isset($_GET['delcat']) && is_numeric($_GET['delcat'])) {
			$wpdb->query("DELETE FROM `cb_produk_cat` WHERE `id_cat`=".$_GET['delcat']);
			$wpdb->query("DELETE FROM `cb_produk` WHERE `id_cat`=".$_GET['delcat']);
			if ($wpdb->last_error != '') {
				echo '<div class="notice notice-error is-dismissible">
				<p>'.$wpdb->print_error().'</p>
				</div>';
			} else {
				echo '<div class="notice notice-success is-dismissible">
				<p>Kategori dan Produk di dalamnya telah dihapus.</p>
				</div>';
			}
		} elseif (isset($_GET['delpro']) && is_numeric($_GET['delpro'])) {
			$wpdb->query("DELETE FROM `cb_produk` WHERE `id`=".$_GET['delpro']);
			if ($wpdb->last_error != '') {
				echo '<div class="notice notice-error is-dismissible">
				<p>'.$wpdb->print_error().'</p>
				</div>';
			} else {
				echo '<div class="notice notice-success is-dismissible">
				<p>Produk telah dihapus.</p>
				</div>';
			}
		} else {
			$options = get_option('cb_pengaturan');
			if (isset($_POST['downloadpath'])) {			
				$options['downloadpath'] = sanitize_text_field($_POST['downloadpath']);
				update_option('cb_pengaturan', $options);
				$pesan = 'Path Download sudah diperbarui';
			} else {
				if (isset($options['downloadpath'])) {
					$optionpath = stripslashes($options['downloadpath']);
				} else {
					$optionpath = '';
				}
				echo '		
				<form action="" method="post" name="cbpath">
				<h3>Path Folder Download</h3>
				<p>Tentukan lokasi folder penyimpanan file-file untuk didownload oleh member.</p> 
				<p>Contoh :<br/>
				 - path folder plugin ini: <code>'.str_replace('include/adminmenu/produk.php','',__FILE__).'</code><br/>
				 - document root web ini: <code>'.str_replace('wp-content/plugins/wp-affiliasi/include/adminmenu/produk.php','',__FILE__).'</code></p>
				<p>Silahkan sesuaikan dengan lokasi folder penyimpanan file anda.</p>
				<p><input type="text" name="downloadpath" size="40" value="'.$optionpath.'"/> <input type="button" value="Pilih Folder" class="button button-secondary" onclick="makeSelection(this.form, \'downloadpath\');"></p>
				<p><input type="submit" class="button button-primary" value="Update Path"/></p>
				</form>';
			}
		}
		?>
	</div>
	<div class="col-md-4 order-1 mb-3">
		<div class="dtree">
		<?php 
		if (isset($pesan) && $pesan != '') { 
			echo '<div id="message2" class="notice notice-success is-dismissible"><p>'.$pesan.'</p></div>'; 
		}

		$produks = $wpdb->get_results("SELECT * FROM `cb_produk_cat` ORDER BY `id_cat` ASC");
		if (count($produks) > 0) {
			foreach ($produks as $produk) {			
				$folder .= '
				d.add('.$produk->id_cat.','.$produk->id_parent.',\''.str_replace("'","",$produk->name).'\',\''.$read.
				'cat='.$produk->id_cat.'\',\'\',\'\',\''.$iconfolder.'\');
				d.add('.(999999+($produk->id_cat)).','.$produk->id_cat.',\'<i>Tambah Produk</i>\',\''.$read.
				'produk=new&idcat='.$produk->id_cat.'\',\'\',\'\');
				';		
			}
		}
		$produks = $wpdb->get_results("SELECT * FROM `cb_produk` ORDER BY `id` ASC");
		
		if (count($produks) > 0) {
			foreach ($produks as $produk) {			
				$file .= "\n".'d.add('.(1000+($produk->id)).','.$produk->id_cat.',\''.str_replace("'","",$produk->nama).
				'\',\''.$read.'produk='.$produk->id.'\',\'\',\'\');';	
			}
		}
		?>
		<p><a href="javascript: d.openAll();">Buka</a> | <a href="javascript: d.closeAll();">Tutup</a></p>

		<script type="text/javascript">
			<!--
			d = new dTree('d');
			d.add(0,-1,'Produk');
			<?php echo $folder;?>
			<?php echo $file;?>	
			d.add(10000,0,'<i><b>Tambah Kategori</b></i>','<?php echo $read;?>cat=new','','','<?php echo home_url(); ?>/wp-content/plugins/wp-affiliasi/folder_add.png');
			document.write(d);
			//-->
		</script>
		</div>
	</div>
</div>