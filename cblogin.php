<?php 
$options = get_option('cb_pengaturan');
$showtxt = '';
if (isset($_GET['redirect_to'])) {
	$redirectlogin = $redirect = '?redirect_to='.$_GET['redirect_to'];
	$redirectlogin = str_replace(site_url(),'',$_GET['redirect_to']);
} elseif (isset($_GET['orderproduk'])) {
	$redirectlogin = '?p='.$options['order'].'&orderproduk='.$_GET['orderproduk'];
	$redirect = 'orderproduk='.$_GET['orderproduk'];
} elseif (isset($orderproduk) && is_numeric($orderproduk)) {
	$redirectlogin = '?p='.$options['order'].'&orderproduk='.$orderproduk;
	$redirect = 'orderproduk='.$orderproduk;
} else {
	$redirectlogin = '?p='.$options['memberarea'];
	$redirect = '';
}

if (isset($_GET['act']) && $_GET['act'] == 'register') {
	$showtxt .= cb_registrasi($orderproduk); 
	$showtxt .= '<p>Sudah punya akun? <a href="?'.$redirect.'">Klik di sini untuk login</a><br/>
	Lupa Password? <a href="?act=forgot&'.$redirect.'">Klik di sini untuk reset password</a></p>';
} elseif (isset($_GET['act']) && $_GET['act'] == 'forgot') {
	if (isset($_POST['lupa'])) {
		# Cek apakah ini username atau email
		if (is_email($_POST['lupa'])) {
			$field = 'email';
		} else {
			$field = 'username';
		}

		# Cek apakah ada data itu
		$member = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `".$field."`='".sanitize_text_field($_POST['lupa'])."'");
		if (isset($member->id_user)) {
			$val = rand(10000,99999);
			$wpdb->query("UPDATE `wp_member` SET `val`='".$val."' WHERE `id_user`=".$member->id_user);
			$subject = 'Konfirmasi Lupa Password';
			$content = 'Untuk konfirmasi reset password, silahkan klik link berikut:
'.site_url().'/?page_id='.get_the_ID().'&act=forgot&val='.$val;
			$kontenemail = get_option('konfemail');
			$header = 'From: '.$kontenemail['nama_email'].' <'.$kontenemail['alamat_email'].'>';

			if (function_exists('wp_mail')) {
				wp_mail($member->email, $subject, $content, $header);
			}

			$showtxt = 'Untuk memastikan anda yang melakukan reset password, silahkan klik link konfirmasi yang kami kirimkan melalui email';
		} else {
			$showtxt = ucwords($field).' tidak ditemukan';
		}
	} elseif (isset($_GET['val']) && is_numeric($_GET['val'])) {
		$member = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `val`='".$_GET['val']."'");
		if (isset($member->id_user)) {
			$password = gettimeofday(true);
			$password = substr(MD5($password),0,10);
			if (isset($member->idwp) && $member->idwp > 0) {
				wp_set_password($password,$member->idwp);
				$dbpass = md5($password);				
			} else {
				$dbpass = $password;
			}

			# Update database
			$wpdb->query("UPDATE `wp_member` SET `password`='".$dbpass."',`val`='' WHERE `id_user`=".$member->id_user);

			# Kirim Password Baru
			$subject = 'Password Baru Anda';
			$content = 'Berikut Data Login Anda:
	Username : '.$member->username.'
	Password : '.$password.'

	Silahkan login kembali';
			$kontenemail = get_option('konfemail');
			$header = 'From: '.$kontenemail['nama_email'].' <'.$kontenemail['alamat_email'].'>';

			if (function_exists('wp_mail')) {
				wp_mail($member->email, $subject, $content, $header);
			}

			$showtxt = 'Data Login baru telah kami kirimkan ke email anda';
		} else {
			$showtxt = 'Kode validasi tidak ditemukan atau sudah kadaluarsa';
		}
	} else {
		$showtxt = '<p>Silahkan masukkan Username/Email anda</p>
		<form action="" method="post">
		<div class="formprofil">
			<div class="cbform-row">
				<div class="cbform-label">Username/Email</div>
				<div class="cbform-field"><input type="text" class="cbform-input" name="lupa" required/></div>
			</div>			
			<input type="hidden" name="redirect_to" value="'.site_url($redirectlogin).'" />
			<input type="submit" id="cbform-submit" value="Konfirmasi"/>
		</div>			
		</form>
		<p>Sudah punya akun? <a href="?'.$redirect.'">Klik di sini untuk login</a><br/>
		Belum punya akun? <a href="?act=register&'.$redirect.'">Klik di sini untuk mendaftar</a></p>';
	}
} else {		
    if (isset($_POST['log2']) && $_POST['pwd2']) {
	    if (isset($_POST['rememberme'])) {
	    	$rememberme = true;
	    } else {
	    	$rememberme = false;
	    }
	    $creds = array(
	        'user_login'    => $_POST['log2'],
	        'user_password' => $_POST['pwd2'],
	        'remember'      => $rememberme
	    );
	 
	    $user = wp_signon( $creds, true );
	 
	    if ( is_wp_error( $user ) ) {
	        $showtxt .= $user->get_error_message();
	    } else {
	    	if (isset($_POST['redirect_to']) && $_POST['redirect_to'] != '') {
	    		header("Location:".$_POST['redirect_to']);
	    		exit;
	    	} else {
	    		$urlmember = site_url()."?page_id=".$options['memberarea'];
	    		header("Location:".$urlmember);
	    		exit;
	    	}
	    }

    }

	$showtxt .= '<p>Silahkan login dulu untuk melanjutkan proses</p>
	<form action="" method="post">
	<div class="formprofil">
		<div class="cbform-row">
			<div class="cbform-label">Username/Email</div>
			<div class="cbform-field"><input type="text" class="cbform-input" name="log2" required/></div>
		</div>
		<div class="cbform-row">
			<div class="cbform-label">Password</div>
			<div class="cbform-field"><input type="password" class="cbform-input" name="pwd2" required/></div>
		</div>
		<div class="cbform-row">
			<div class="cbform-label">Ingat Saya</div>
			<div class="cbform-field"><input name="rememberme" type="checkbox" id="rememberme" value="forever" /></div>
		</div>
		<input type="submit" name="wp-submit" id="cbform-submit" value="Login"/>
		<input type="hidden" name="redirect_to" value="'.site_url($redirectlogin).'" />
	</div>
	</form>
	<p>Belum punya akun? <a href="?act=register&'.$redirect.'">Klik di sini untuk mendaftar</a><br/>
	Lupa Password? <a href="?act=forgot&'.$redirect.'">Klik di sini untuk reset password</a></p>
	<p style="visibility:hidden;"><small>Powered by : Cafe <a href="https://cafebisnis.com/">Bisnis Online</a></small></p>';
}
?>