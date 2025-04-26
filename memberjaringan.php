<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!isset($user_ID) || $user_ID == 0) { 
	$refer = $_SERVER['REQUEST_URI'];
	header("Location: ".wp_login_url($refer));
}
if (isset($_GET['page']) && $_GET['page'] == 'jaringan') {
	$showtxt .= '<h2>Jaringan Bisnis Anda</h2>';
}

$data = $wpdb->get_results("SELECT `nama`,`idwp`,`membership` FROM `wp_member` WHERE `id_referral`=".$user_ID);
$showtxt .= '<div id="downline1">';
$i = 0;
foreach ($data as $data) {
	$showtxt .= '
	<div class="listmember" id="member'.$data->idwp.'"><img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/join.gif" style="height:18px;width:18px"/>';
	if ($data->membership > 0) {
		$showtxt .= '<a class="folder" id="'.$data->idwp.'"><img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/folder.gif" id="down'.$data->idwp.'" style="display: inline; width:18px"/></a><a class="detil" id="detil'.$data->idwp.'">'.$data->nama.'</a></div>';
	} else {
		$showtxt .= ' <img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/folder.gif" style="display: inline; width:18px" id="down'.$data->idwp.'"/>
		'.$data->nama.' <em>(blm valid)</em></div>';
	}
	$i++;
}
if ($i == 0) { $showtxt .= '<div>Anda belum memiliki jaringan. Silahkan berpromosi dan rekrut member baru</div>';}
$showtxt .= '
</div>
<div id="detilprofil"></div>
<div style="clear:both"></div>
';