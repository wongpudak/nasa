<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
$datasponsor = unserialize(CB_SPONSOR);
$options = get_option('cb_pengaturan');
if (isset($_POST['cbemail']) && isset($_POST['cbpesan'])) {

	if (isset($options['cap_secret']) && $options['cap_secret'] != '' && isset($options['cap_kontak']) && $options['cap_kontak'] != '') {
		if (isset($_POST["g-recaptcha-response"]) && $_POST['g-recaptcha-response'] != '')  {
			$val = 'secret='.$options['cap_secret'].'&response='.$_POST['g-recaptcha-response'];			
			$cek = postData('https://www.google.com/recaptcha/api/siteverify',$_SERVER['HTTP_USER_AGENT'], $val);
			$result = json_decode($cek, true);
			if ($result['success'] != 1) {
				switch ($result['error-codes'][0]) {
					case 'timeout-or-duplicate': $kontaktxt = 'Waktu Captcha Habis atau Terjadi Duplikat';	break;
					case 'missing-input-secret': $kontaktxt = 'Secret Key tidak ada, silahkan hubungi admin web ini';	break;
					case 'invalid-input-secret': $kontaktxt = 'Secret Key Salah, silahkan hubungi admin web ini';	break;
					case 'missing-input-response': $kontaktxt = 'Anda belum menyelesaikan reCaptcha';	break;
					case 'invalid-input-response': $kontaktxt = 'reCaptcha yang anda lakukan salah';	break;
					case 'bad-request': $kontaktxt = 'Rekues Tidak Valid';	break;
					default: $kontaktxt = 'Ada masalah dengan reCaptcha: '.$result['error-codes'][0];
				}
			} else {
				# Kirim email
				if (is_email($_POST['cbemail'])) {
					$nama = sanitize_text_field($_POST['cbnama']);
					$email = sanitize_email($_POST['cbemail']);
					$alamat = sanitize_text_field($_POST['alamat']);
					$tahudari = sanitize_text_field($_POST['tahudari']);
					$judul = sanitize_text_field($_POST['judul']);
					$pesan = special($_POST['cbpesan']);
					
					if ($_POST['kontak'] == 'admin') {
						$sendto = get_option('alamat_email');
					} else {
						$sendto = $datasponsor['email'];
					}
					
					$body = '
Kiriman Email dari :
Nama : '.$nama.'
E-mail : '.$email.'
Alamat : '.$alamat.'
Tahu dari : '.$tahudari.'
Isi Pesan :
'.$pesan.'

Dikirim dari '.$_SERVER['SERVER_NAME'];			
					$header = 'From: '.get_option('nama_email').' <'.get_option('alamat_email').'>
					Reply-To: '.$nama.' <'.$email.'>';
					wp_mail($sendto, $judul, $body, $header);
					
					$kontaktxt = 'Terima kasih, Email Sudah dikirim';
				} else {
					$kontaktxt = 'Email anda tidak valid, gunakan format email yang benar. Contoh: email@host.com';
				}
			}
		} else {
			$kontaktxt = 'Captcha harus diisi';
		}
	}

} else {
$kontaktxt = '
<form action="" method="post">
<table width="100%" cellpadding="2">
	<tr>
		<td width="30%" align="right">Hubungi :</td>
		<td width="70%" align="left"><input type="radio" name="kontak" value="admin" /> Admin 
		<input type="radio" name="kontak" value="sponsor" /> '.$datasponsor['nama'].'
		</td>
	</tr>
	<tr>
		<td align="right">Nama :</td>
		<td align="left"><input type="text" name="cbnama" size="35" required></td>
	</tr>
	<tr>
		<td align="right">E-mail :</td>
		<td  align="left"><input type="email" name="cbemail" size="35" required></td>
	</tr>
	<tr>
		<td align="right">Alamat :</td>
		<td align="left"><input type="text" name="alamat" size="35"></td>
	</tr>
	<tr>
		<td align="right">Tahu Dari :</td>
		<td align="left"><input type="text" name="tahudari" size="35"></td>
	</tr>
	<tr>
		<td align="right">Judul :</td>
		<td align="left"><input type="text" name="judul" size="35"></td>
	</tr>
	<tr>
		<td align="right" valign="top">Pesan :</td>
		<td align="left"><textarea name="cbpesan" cols="40" rows="7" required></textarea></td>
	</tr>';
if (isset($options['cap_key']) && $options['cap_key'] != '' && isset($options['cap_kontak']) && $options['cap_kontak'] != '') {
	$kontaktxt .= '<tr><td width="30%" align="right">&nbsp;</td>
	<td><div class="g-recaptcha" data-sitekey="'.$options['cap_key'].'"></div></td></tr>';
}

$kontaktxt .= '
	<tr>
		<td width="30%" align="right" valign="top">&nbsp;</td>
		<td><input type="submit" value="Kirim Email"></td>		
	</tr>
	
</table>
</form>';
} 