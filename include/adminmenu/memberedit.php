<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!current_user_can('manage_options')) { die();  exit; }

$iduser = $_GET['profil'];

if (isset($_GET['val']) && $_GET['val'] == 0) {
	$getmember = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$iduser);
} else {
	$getmember = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$iduser);
}

if (is_numeric($getmember->id_user) && $getmember->id_user > 0) {
	// Mulai proses
	$showtxt = '<div class="wrap">
	<h1 class="wp-heading-inline">'.$getmember->nama.'</h1>';

	$userid = $getmember->id_user;
	if (isset($_POST['nama']) && isset($_POST['email'])) {
		// Update Profil
		$txterror = $updatedb = '';
		$lainlain = unserialize($getmember->homepage);
		
		if (isset($_POST['id_referral'])) { $updatedb .= "`id_referral`= '".sanitize_text_field($_POST['id_referral'])."',"; }
		if (isset($_POST['id_tianshi'])) { $updatedb .= "`id_tianshi`= '".sanitize_text_field($_POST['id_tianshi'])."',"; }
		if (isset($_POST['ktp'])) { $updatedb .= "`ktp`= '".sanitize_text_field($_POST['ktp'])."',"; }
		if (isset($_POST['nama'])) { $updatedb .= "`nama`= '".sanitize_text_field($_POST['nama'])."',"; }	
		if (isset($_POST['tgl_lahir'])) { $updatedb .= "`tgl_lahir`= '".sanitize_text_field($_POST['tgl_lahir'])."',"; }
		if (isset($_POST['alamat'])) { $updatedb .= "`alamat`= '".sanitize_text_field($_POST['alamat'])."',"; }
		if (isset($_POST['kota'])) { $updatedb .= "`kota`= '".sanitize_text_field($_POST['kota'])."',"; }
		if (isset($_POST['provinsi'])) { $updatedb .= "`provinsi`= '".sanitize_text_field($_POST['provinsi'])."',"; }
		if (isset($_POST['kodepos'])) { $updatedb .= "`kodepos`= '".sanitize_text_field($_POST['kodepos'])."',"; }
		if (isset($_POST['telp'])) { $updatedb .= "`telp`= '".sanitize_text_field($_POST['telp'])."',"; }
		if (isset($_POST['nama_istri'])) { $updatedb .= "`nama_istri`= '".sanitize_text_field($_POST['nama_istri'])."',"; }
		if (isset($_POST['ktp_istri'])) { $updatedb .= "`ktp_istri`= '".sanitize_text_field($_POST['ktp_istri'])."',"; }
		if (isset($_POST['tgl_lahir_istri'])) { $updatedb .= "`tgl_lahir_istri`= '".sanitize_text_field($_POST['tgl_lahir_istri'])."',"; }
		if (isset($_POST['kelamin'])) { $updatedb .= "`kelamin`= '".sanitize_text_field($_POST['kelamin'])."',"; }
		if (isset($_POST['email'])) { 
			$email = sanitize_email(strtolower($_POST['email']));
			$updatedb .= "`email`= '".$email."',";
			if (!is_email($_POST['email'])) {
			    $txterror .= 'Email anda salah. Format yang benar username@domain.com contoh: bejo@gmail.com<br/>';
			}
			
			$check_email = $wpdb->get_var("SELECT `id_user` FROM `wp_member` WHERE `email` = '".$email."' AND `id_user` != ".$getmember->id_user);
			if (is_numeric($check_email) && $check_email > 0) {
				$errmail = 1;
			}

			if ($getmember->idwp > 0) {
				$check_usermail = email_exists( $email );
				
				if (is_numeric($check_usermail) && $check_usermail != $getmember->idwp) {
					$errmail = 1;
				}
			}

			if (isset($errmail) && $errmail == 1) {
				$txterror .= 'Email sudah pernah dipakai, anda hanya boleh menggunakan 1 alamat email per akun<br/>';
			}
		}

		if (isset($_POST['id_tianshi'])) { $updatedb .= "`id_tianshi`= '".sanitize_text_field($_POST['id_tianshi'])."',"; }

		if (isset($_POST['password']) && $_POST['password'] != '') { 

			$password = sanitize_text_field($_POST['password']); 

			if ($password == $getmember->username) {
				$txterror .= 'Password tidak boleh sama dengan Username '.$getmember->username;
			} else {
				if ($getmember->idwp > 0) {
					wp_set_password($password,$getmember->idwp);
					$updatedb .= "`password`= '*******',";
				} else {
					$updatedb .= "`password`= '".$password ."',";
				}
			}
		}

		if (isset($_POST['subdomain'])) { 
			$subdomain = sanitize_text_field($_POST['subdomain']); 
			$subdomain	= strtolower($subdomain);
			$subdomain	= preg_replace('/[^\w@,.;]/', '', $subdomain);		
			
			$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain`='".$subdomain."' && `id_user` != '".$getmember->id_user."'");

			if (isset($subdomain_exist)) {
				$txterror = 'URL sudah ada yang memakai';
			}

			if (!empty($_POST['subdomain'])) { $updatedb .= "`subdomain` = '".$subdomain."',"; }
		}
		if (isset($_POST['bank'])) { $updatedb .= "`bank`= '".sanitize_text_field($_POST['bank'])."',"; }
		if (isset($_POST['rekening'])) { $updatedb .= "`rekening`= '".sanitize_text_field($_POST['rekening'])."',"; }
		if (isset($_POST['ac'])) { $updatedb .= "`ac`= '".sanitize_text_field($_POST['ac'])."',"; }
		//if (isset($_POST['pic_profil'])) { $lainlain['pic_profil'] = sanitize_text_field($_POST['pic_profil']); }
		if (isset($_POST['customwhatsapp'])) { $lainlain['whatsapp'] = sanitize_text_field($_POST['customwhatsapp']); }
		
		$c = 1;
		if (isset($_POST['ym'])) { $lainlain[0] = sanitize_text_field($_POST['ym']); }
		if (isset($_POST['custom']) && is_array($_POST['custom'])) {
			foreach ($_POST['custom'] as $key => $value) {
				$lainlain[$key] = sanitize_text_field($value);			
			}
		}

		if (isset($_FILES["gambar"]["name"]) && $_FILES["gambar"]["name"] != '' && $getmember->idwp > 0) { 
			//Set max file size in bytes
			$max_size = 5 * 102400;
			//Set default file extension whitelist
			$whitelist_ext = array('jpeg','jpg','png','gif');
			//Set default file type whitelist
			$whitelist_type = array('image/jpeg', 'image/jpg', 'image/png','image/gif');
			
			$upload_dir = wp_upload_dir();
			$pic_dir = $upload_dir['basedir'].'/pic';			
			if( ! file_exists( $pic_dir ) ) { wp_mkdir_p( $pic_dir ); }
			$filename = 'picuser_'.$getmember->idwp;
			$output_filename = $pic_dir.'/'.$filename;
			$target_file = $upload_dir['basedir'].'/pic/'.$filename;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($_FILES["gambar"]["name"],PATHINFO_EXTENSION));

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
              $txterror = "Maaf, hanya support JPG, JPEG, PNG & GIF saja.";
              $uploadOk = 0;
            }

            //Check that the file is of the right type
			if (!in_array($_FILES["gambar"]["type"], $whitelist_type)) {
			  $txterror = "Maaf, hanya support JPG, JPEG, PNG & GIF saja.";
			  $uploadOk = 0;
			}

      // Check file size
      if ($_FILES["gambar"]["size"] > $max_size) {
        $txterror = 'Maaf, gambar terlalu besar. Max. 500kb';
        $uploadOk = 0;
      }

      if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file.'.'.$imageFileType)) {
          $lainlain['pic_profil'] = $upload_dir['baseurl'].'/pic/'.$filename.'.'.$imageFileType;
        } else {
          $txterror = 'Maaf, gambar tidak dapat diupload';
        }
      }
  	}

    if (isset($lainlain) && is_array($lainlain)) { 
			$updatedb .= "`homepage`='".serialize($lainlain)."',";
			$custom = $lainlain;
		}

		if ($txterror == '') {		
			$updatedb .= "`lastupdate`=NOW()";
			$wpdb->query("UPDATE wp_member SET ".$updatedb." WHERE `id_user`=".$getmember->id_user);
	
			if (isset($email) && $getmember->idwp > 0) {			
				$user_data = wp_update_user( array ('ID' => $getmember->idwp, 'user_email' => $email)); 
 
				if ( is_wp_error( $user_data ) ) {
				    // There was an error; possibly this user doesn't exist.
				    $txterror .= 'Kesalahan dalam perubahan email';
				} 				
			}
			
			$sukses = 'ok';
		} 
	}

	if (isset($txterror) && $txterror != '') {
		$showtxt .= '<div id="message2" class="notice notice-warning is-dismissible"><p><strong>ERROR:</strong> '.$txterror.'</p></div>';
		
	} 

	if (isset($sukses) && $sukses = 'ok') {
		$showtxt .= '<div id="message2" class="notice notice-success is-dismissible"><p>Data telah disimpan</p></div>';
	}

	$aturform = get_option('aturform');
	$form = unserialize($aturform);
	if (isset($_GET['val']) && $_GET['val'] == 0) {
		$user = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$iduser);
	} else {
		$user = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$iduser);
	}

	$custom = unserialize($user->homepage);
	$cc = 1;

	if (is_array($form)) {
		if (isset($custom['pic_profil']) && $custom['pic_profil'] != '') { 
			$pic = $custom['pic_profil']; } else { $pic = plugin_dir_url('kosong.png'); 
		}
		$showtxt .= '<form action="" method="post" enctype="multipart/form-data">
		<div class="cbform-row">
			<img id="gambar" src="'.$pic.'?rand='.rand(0,999).'" alt="" style="width:100%;max-width:300px;margin:0 auto" />
		</div>
		<div class="cbform-row">			
			<label class="cbform-label">Foto Profil</label>
			<div class="cbform-field">				
				<input type="file" name="gambar" 
				title="Pilih Foto Profil" id="imgInp" accept=".jpg, .gif, .jpeg, .png" />
				<br/><small>Max. 100kb</small>
				
			</div>
		</div>
		<div class="cbform-row">
			<label class="cbform-label">ID Sponsor</label>
			<div class="cbform-field"><input type="text" class="cbform-input" name="id_referral" value="'.$user->id_referral.'" required/>
			<br/><small>Masukkan ID Sponsor</small></div>
		</div>';
		foreach ($form as $form) {
			if ($form['profil'] == 1) {
				// Build Required
				if (isset($form['required']) && $form['required'] == 1) {
					$required = ' required';
				} else {
					$required = '';
				}

				// Build Label
				$required = $label = $value = '';
				if (empty($form['label'])) {				
					switch ($form['field']) {				
						case 'nama' : $label = 'Nama Lengkap'; $required = 'required'; break;
						case 'email' : $label = 'Email'; $required = 'required'; break;
						case 'id_tianshi' : $label = 'ID MLM'; break;					
						case 'ktp' : $label = 'No. KTP'; break;
						case 'tgl_lahir' : $label = 'Tanggal Lahir'; break;
						case 'alamat' : $label = 'Alamat'; break;
						case 'kota' : $label = 'Kota'; break;
						case 'provinsi' : $label = 'Provinsi'; break;
						case 'kodepos' : $label = 'Kodepos'; break;
						case 'telp' : $label = 'No. Telp / HP'; break;
						case 'ktp_istri' : $label = 'No. KTP Pasangan'; break;
						case 'nama_istri' : $label = 'Nama Pasangan'; break;
						case 'tgl_lahir_istri' : $label = 'Tgl Lahir Pasangan'; break;
						case 'ac' : $label = 'Atas Nama'; break;
						case 'bank' : $label = 'Nama Bank'; break;
						case 'rekening' : $label = 'No. Rekening'; break;
						case 'kelamin' : $label = 'Jenis Kelamin'; break;
						case 'username' : $label = 'Username'; break;
						case 'password' : $label = 'Password'; break;
						case 'subdomain' : $label = 'URL Affiliasi'; break;
						case 'customwhatsapp' : $label = 'WhatsApp'; break;
						case 'ym' : $label = 'Yahoo Messenger'; break;
					}
				} else {
					$label = $form['label'];
				}

				// Build Form
				switch ($form['field']) {
					case 'keterangan':
						$model = 'keterangan';
						break;
					case 'username':
						$model = 'disable';
						$value = $user->{$form['field']};
						$name = 'username';
						break;
					case 'password':
						$model = 'password';				
						$name = 'password';
						break;
					case 'kelamin':
						$model = 'kelamin';
						$option = array('Wanita','Pria');
						$sel = array('','');
						$sel[$user->kelamin] = ' selected';
						$name = 'kelamin';
						break;
					case 'email':
						$model = 'email';
						$value = $user->email;
						$name = 'email';
						break;
					case 'ym':
						$model = 'text';
						$value = $custom[0];
						$name = 'ym';
						break;
					case 'subdomain':
						$model = 'subdomain';
						$value = $user->subdomain;
						$name = 'subdomain';
						break;
					case 'customwhatsapp':
						$model = 'text';
						if (isset($custom['whatsapp'])) {
							$value = $custom['whatsapp'];
						} else {
							$value = '';
						}
						$name = 'customwhatsapp';
						break;
					case 'custom':
						$valuec = '';				
						if (isset($custom[$cc])) { 
							$valuec = $custom[$cc]; 
						} else {
							$valuec = '';
						}
						$name = 'custom['.$cc.']';
						if (isset($form['option']) && $form['option'] != '') {
							$model = 'dropdown';
							$exp = explode(',',$form['option']);
							$option = array();
							foreach ($exp as $key => $value) {
								$value = trim($value);
								$option[$value] = $value;
								$sel[$value] = '';
							}						
							$sel[$valuec] = ' selected';
						} else {
							$model = 'text';
							$value = $valuec;					
						}	
						break;
					default:
						$model = 'text';
						$value = $user->{$form['field']};
						$name = $form['field'];
						break;
				}

				if (isset($form['info']) && $form['info'] != '') {
					$info = '<br/><small>'.$form['info'].'</small>';
				} else {
					$info = '';
				}

				// Build Form
				switch ($model) {
					case 'keterangan':
						$showtxt .= '<h4 class="cbform-info">'.$label.'</h4>';
						break;
					case 'email':
						$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field"><input type="email" class="cbform-input" name="'.$name.'" value="'.$value.'"'.$required.'/>'.$info.'</div>
						</div>';
						break;
					case 'password':
						$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field"><input type="password" class="cbform-input" name="'.$name.'" value="'.$value.'"'.$required.'/>'.$info.'</div>
						</div>';
						break;
					case 'kelamin':
						$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field">
								<select name="'.$name.'" class="cbform-input">
									<option value="0"'.$sel[0].'>Wanita</option>
									<option value="1"'.$sel[1].'>Pria</option>
								</select>'.$info.'
							</div>
						</div>';
						break;
					case 'dropdown':
						$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field">
								<select name="'.$name.'" class="cbform-input">';
						foreach ($option as $option) {
							$showtxt .= '<option value="'.$option.'"'.$sel[$option].'>'.$option.'</option>';
						}
						$showtxt .= '
								</select>'.$info.'
							</div>
						</div>';
						break;
					case 'subdomain':
						if (get_option('affsub') == 1) {
							$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field"><input type="text" class="cbform-input" style="width:40%; float:left" name="'.$name.'" value="'.$value.'"'.$required.'/><strong>.'.$_SERVER['SERVER_NAME'].'</strong>'.$info.'</div>
						</div>';
						} else {
							$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field"><strong>'.site_url().'/?reg=</strong><input type="text" class="cbform-input" style="width:40%" name="'.$name.'" value="'.$value.'"'.$required.'/>'.$info.'</div>
						</div>';
						}
						break;
					case 'disable':
						$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field"><input type="text" class="cbform-input" name="'.$name.'" value="'.$value.'" readonly/></div>
						</div>';
						break;				
					default:
						$showtxt .= '
						<div class="cbform-row">
							<label class="cbform-label">'.$label.'</label>
							<div class="cbform-field"><input type="text" class="cbform-input" name="'.$name.'" value="'.$value.'"'.$required.'/>'.$info.'</div>
						</div>';
						break;
				}
			}

			if ($form['field'] == 'custom') {
				$cc++;
			}
		}

		$showtxt .= '<div class="cbform-row"><input type="submit" class="cbform-button" value="Update"/></div>
		</form>
		';
	} else {
		$showtxt .= '<p>Form Pengaturan Profil blm diaktifkan. Silahkan hubungi admin web ini</p>';
	}
} else {
	// Buat user di wp_member
	$showtxt = 'Member tidak ditemukan';
}

echo $showtxt.'</div>';