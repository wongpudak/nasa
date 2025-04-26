<?php
// Load WordPress environment
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
  require_once $wp_load;
} else {
  die('Could not load WordPress');
}

$sponsor_data = $member_data = $mysponsor_data = array();

if (defined('CB_SPONSOR')) {
  $sponsor_data = unserialize(CB_SPONSOR);
  if (isset($sponsor_data['homepage']) && !empty($sponsor_data['homepage'])) {
    $customsponsor = unserialize($sponsor_data['homepage']);
    if (count($customsponsor) > 0) {
      foreach ($customsponsor as $key => $value) {        
        if ($key == 'whatsapp') {
          $sponsor_data[$key] = formatwa($value);
        } else {
          $sponsor_data[$key] = $value;
        }
      }
    }
  }
}

if (defined('CB_MEMBER')) {
  $member_data = unserialize(CB_MEMBER);
  $member_data = get_object_vars($member_data);
  $statusmember = array('Belum validasi','Free Member','Premium Member');
  if (isset($member_data['homepage']) && !empty($member_data['homepage'])) {
    $custommember = unserialize($member_data['homepage']);
    if (count($custommember) > 0) {
      foreach ($custommember as $key => $value) {
        if ($key == 'whatsapp') {
          $member_data[$key] = formatwa($value);
        } else {
          $member_data[$key] = $value;
        }
      }
    }
  }
  $member_data['urlaff'] = urlaff($member_data['subdomain']);
  $member_data['urlpendek'] = get_urlpendek($member_data['urlaff']);
  $member_data['status'] = $statusmember[$member_data['membership']];
}

if (defined('CB_MYSPONSOR')) {
  $mysponsor_data = unserialize(CB_MYSPONSOR);
  $mysponsor_data = get_object_vars($mysponsor_data);
  if (isset($mysponsor_data['homepage']) && !empty($mysponsor_data['homepage'])) {
    $custommysponsor = unserialize($mysponsor_data['homepage']);
    if (count($custommysponsor) > 0) {
      foreach ($custommysponsor as $key => $value) {
        if ($key == 'whatsapp') {
          $mysponsor_data[$key] = formatwa($value);
        } else {
          $mysponsor_data[$key] = $value;
        }
      }
    }
  }
}

$hapusdata = ['password','downline_lngsg','jml_downline','jml_voucher','sisa_voucher','username','homepage','read','jmlfree','jmlpremium','jmlinvalid','lastupdate','val','jmldownline','uplines'];
$sponsor_data = array_diff_key($sponsor_data, array_flip($hapusdata));
unset($member_data['password']);
unset($mysponsor_data['password']);

$response_data = array(
    'sponsor' => $sponsor_data,
    'member' => $member_data,
    'mysponsor' => $mysponsor_data
);

header('Content-Type: application/json');
echo json_encode($response_data);
exit;
?>