<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!isset($user_ID) || $user_ID == 0) { 
	$refer = $_SERVER['REQUEST_URI'];
	header("Location: ".wp_login_url($refer));
}
if (isset($_GET['page']) && $_GET['page'] == 'laporan') {
	$showtxt .= '<h2>Banner Promosi</h2>';
}

$member = $wpdb->get_row("SELECT `subdomain`,`membership` FROM `wp_member` WHERE `idwp` = '$user_ID'");
$urlaff = urlaff($member->subdomain);
$blogurl = site_url();
$databanner = get_option('banner');
$title = get_bloginfo('name');

$showtxt .= '
<p style="text-align:center">Link Affiliasi Anda:<br/>
	<b><a href="'.$urlaff.'">'.$urlaff.'</a></b>';

	if (substr(get_urlpendek($urlaff), 0, 4) == 'http') {
		$showtxt .= '<br/>(<a href="'.get_urlpendek($urlaff).'">'.get_urlpendek($urlaff).'</a>)';
	} 

	$showtxt .= '</p>
<p>Arahkan mouse ke salah satu thumbnail untuk melihat preview banner dan kode HTML-nya. <br/>
Klik kanan untuk menahan preview sehingga anda bisa mencopy kode HTML-nya.</p>

<p style="line-height:30px;">';
$i = 0;
$tooltip = '';
if (is_array($databanner)) {
	foreach ($databanner as $banner) {
		$i++;
		$showtxt .= '<span data-tooltip="sticky'.$i.'"><img src="'.$banner['url'].'" alt="'.$title.'" style="width:150px; height:150px; margin-left:5px"/></span>';
		$tooltip .= '
		<div id="sticky'.$i.'" class="atip">
		<img src="'.$banner['url'].'" /><br />
		<b>Kode utk Web:</b><br/>
		<textarea style="width:100%; height:50px;"><a href="'.$urlaff.'"><img src="'.$banner['url'].'" alt="'.$title.'"/></a></textarea><br/>
		</div>';
	}
}
$showtxt .= '
</p>
<div id="mystickytooltip" class="stickytooltip">
'.$tooltip.'
<div class="stickystatus"></div>
</div>';
