<?php
function cb_head() {
	if (isset($_GET['page']) && $_GET['page'] =='cbaf_produk') {
		echo '
		<link rel="StyleSheet" href="'.site_url().'/wp-content/plugins/wp-affiliasi/dtree.css" type="text/css" />
		<script type="text/javascript" src="'.site_url().'/wp-content/plugins/wp-affiliasi/dtree.js"></script>
		<script type="text/javascript">
			targetElement = null;
			function makeSelection(cbpath, id) {
			if(!cbpath || !id)
			return;
			targetElement = cbpath.elements[id];
			var handle = window.open(\''.site_url().'/wp-content/plugins/wp-affiliasi/folder.php\',\'\',\'scrollbars=yes, width=525, height=275, top=100, left=100\');
			}
		</script>';	
	} elseif (isset($_GET['page']) && $_GET['page'] == 'cbaf_daftar') {
		echo '
		<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  		<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		 <style>
		  #sortable3 { margin: 0; background: #eee; padding: 5px 0; }
		  #sortable3 li { border: 1px solid #cccccc; margin: 5px; background: #eee; padding: 5px 0; font-size: 1.2em; }
		  #sortable3 {width:100%;}
		  </style>
		  <script>
		  var $j = jQuery.noConflict();
		  $j(function() {
		    $j( "ul.droptrue" ).sortable({
		      connectWith: "ul",
		      items: "li:not(.ui-state-disabled)"
		    });
		 
		    $j( "ul.dropfalse" ).sortable({
		      connectWith: "ul",
		      dropOnEmpty: false
		    });
		 
		    $j( "#sortable1, #sortable2, #sortable3" ).enableSelection();
		  });
		  </script>';
	}


}

add_action('admin_footer', 'cb_adminfooter');
function cb_adminfooter() {
	if (isset($_GET['page']) && substr($_GET['page'], 0,5) == 'cbaf_') {
		echo '
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script type="text/javascript">
      var $j = jQuery.noConflict();
      $j(function(){
        $j(document).on(\'click\',\'.folder\', function() {
          var idmember = this.id;            
          if ( $j("#downline"+idmember ).length ) {
            $j("#downline"+idmember).remove();
            $j("#down"+idmember).attr(\'src\',\''.site_url().'/wp-content/plugins/wp-affiliasi/img/folder.gif\');
          } else {
            $j("#member"+idmember).append(\' <img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/load.gif" id="load"/>\');
            $j.get("'.site_url().'/wp-content/plugins/wp-affiliasi/member.php", { id: this.id },
                function(data){
                    $j("#load").remove();
                    $j("#member"+idmember).append(data);
                });
            $j("#down"+idmember).attr(\'src\',\''.site_url().'/wp-content/plugins/wp-affiliasi/img/folderopen.gif\');
          }
        });

        $j("#detilprofil").hide();
        
        $j(document).on(\'click\',\'.close\', function() {
          $j("#detilprofil").hide();
        });

        $j(document).on(\'click\',\'.detil\', function() {
          var idmember = this.id;
          if ($j("#themember").length) {
            $j("#themember").remove();
          }
          $j("#detilprofil").show();
          $j("#detilprofil").append(\' <img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/load.gif" id="load"/>\');
          $j.get("'.site_url().'/wp-content/plugins/wp-affiliasi/member.php", { member: this.id },
            function(data){
                $j("#load").remove();
                $j("#detilprofil").append(data);
            });            
        });
      })
    </script>
    ';
	}
}

function cb_adminhead() {
    if (isset($_GET['page']) && ($_GET['page'] == 'pengaturan' || substr($_GET['page'], 0,5) == 'cbaf_')) {
    	$url = site_url() . '/wp-content/plugins/wp-affiliasi/adminstyle.css';
    	echo "\n";
    	$datakomisi = get_option('komisi');
		  if ($datakomisi !== FALSE && isset($datakomisi['pps'])) { $countstart=count($datakomisi['pps'])+1; } else { $countstart = 2; }
    	echo '    	
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    	<link rel="stylesheet" type="text/css" href="'.$url.'" />
    	<script>
    		var counter = '.$countstart.';
    		var $j = jQuery.noConflict();    		
			$j(function(){
				$j(document).on(\'click\',\'#AddLevel\', function() {
					$j("#dynamicInput").replaceWith(\'<tr><td>\'+ counter +\'</td><td><input type="text" name="premium[]" class="form-control" size="5"/></td><td><input type="text" name="free[]" class="form-control" size="5"/></td><td><input type="text" name="lainpremium[]" class="form-control" size="5"/></td><td><input type="text" name="lainfree[]" class="form-control" size="5"/></td><td><input type="text" name="woopremium[]" class="form-control" size="5"/></td><td><input type="text" name="woofree[]" class="form-control" size="5"/></td></tr><tr id="dynamicInput"></tr>\');
					counter++;
				});
			});			
		</script>
    ';
    }
    wp_enqueue_style( 'wp-affiliasi', plugins_url('/wp-affiliasi/wp-affiliasi.css'));
}

add_action('admin_head', 'cb_adminhead');

function cb_wp_head() {	
	echo '<style>
.marquee {
  width: 300px;
  overflow: hidden;
}

.ver {
  height: 200px;
  width: 200px;
}
</style>
<script src="https://www.google.com/recaptcha/api.js"></script>';
#define('ONBOARD', 'yes');
}

add_action( 'wp_head', 'cb_wp_head' );

function cb_wp_footer() {
    echo '
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.marquee/1.4.0/jquery.marquee.min.js"></script>
    <script type="text/javascript">
      var $k = jQuery.noConflict();
      $k(function() {
          $k(".marquee").marquee({
              duplicated: true
          });
      });

      function readURL(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();
          
          reader.onload = function (e) {
            $k("#gambar").attr("src", e.target.result);
          }
          
          reader.readAsDataURL(input.files[0]);
        }
      }

      $k("#imgInp").change(function(){
          readURL(this);
      });
    </script>';
     ?>
    <script type="text/javascript">        
      var $sp = jQuery.noConflict();

      $sp(function () {
          var data = {
              'action': 'get_sponsor_data'
          };

          // Menggunakan fungsi jQuery.ajax untuk membuat permintaan ke datasponsor.php
          <?php if (defined('WP_HOME')) {
            $weburl = WP_HOME;
          } else {
            $weburl = get_bloginfo('wpurl');
          }
          ?>
          $sp.post('<?php echo $weburl.'/wp-content/plugins/wp-affiliasi/include/datasponsor.php'; ?>', data, function (response) {
              // Mengganti [sp_nama] dengan nama sponsor
              replaceShortcode(response);
          });
      }); 

      function replaceShortcode(response) {
        var contentElements = document.querySelectorAll('.datasponsor');
        contentElements.forEach(function(contentElement) {
          var bodyContent = contentElement.innerHTML;

          for (var field in response.sponsor) {
              if (response.sponsor.hasOwnProperty(field)) {
                  var regex = new RegExp('\\[sponsor_' + field + '\\]', 'g');
                  var replacement = response.sponsor[field];
                  bodyContent = bodyContent.replace(regex, replacement);
              }
          }

          // Loop melalui semua field dalam data member
          for (var field in response.member) {
            if (response.member.hasOwnProperty(field)) {
              var regex = new RegExp('\\[member_' + field + '\\]', 'g');
              bodyContent = bodyContent.replace(regex, response.member[field]);
            }
          }

          // Loop melalui semua field dalam data member
          for (var field in response.mysponsor) {
            if (response.mysponsor.hasOwnProperty(field)) {
              var regex = new RegExp('\\[mysponsor_' + field + '\\]', 'g');
              bodyContent = bodyContent.replace(regex, response.mysponsor[field]);
            }
          }            

          var imageRegex = new RegExp('https://cafebisnis.com/fotosponsor.jpg', 'g');
          var imageReplacement = (response['sponsor']['pic_profil'] !== null && response['sponsor']['pic_profil'] !== undefined) ? response['sponsor']['pic_profil'] : '';
          bodyContent = bodyContent.replace(imageRegex, imageReplacement);

          var imageRegex = new RegExp('https://cafebisnis.com/fotomember.jpg', 'g');
          var imageReplacement = (response['member']['pic_profil'] !== null && response['member']['pic_profil'] !== undefined) ? response['member']['pic_profil'] : '';
          bodyContent = bodyContent.replace(imageRegex, imageReplacement);
          
          var imageRegex = new RegExp('https://cafebisnis.com/fotomysponsor.jpg', 'g');
          var imageReplacement = (response['mysponsor']['pic_profil'] !== null && response['mysponsor']['pic_profil'] !== undefined) ? response['mysponsor']['pic_profil'] : '';
          bodyContent = bodyContent.replace(imageRegex, imageReplacement);

          var whatsappRegex = new RegExp('8888888888', 'g');
          var whatsappReplacement = (response['sponsor']['whatsapp'] !== null && response['sponsor']['whatsapp'] !== undefined) ? response['sponsor']['whatsapp'] : '';
          bodyContent = bodyContent.replace(whatsappRegex, whatsappReplacement);

          var whatsappmemberRegex = new RegExp('9999999999', 'g');
          var whatsappmemberReplacement = (response['member']['whatsapp'] !== null && response['member']['whatsapp'] !== undefined) ? response['member']['whatsapp'] : '';
          bodyContent = bodyContent.replace(whatsappmemberRegex, whatsappmemberReplacement);
          
          var whatsappmyspRegex = new RegExp('7777777777', 'g');
          var whatsappmyspReplacement = (response['mysponsor']['whatsapp'] !== null && response['mysponsor']['whatsapp'] !== undefined) ? response['mysponsor']['whatsapp'] : '';
          bodyContent = bodyContent.replace(whatsappmyspRegex, whatsappmyspReplacement);

          var regexAny = new RegExp('\\[(sponsor|member|mysponsor)_\\w+\\]', 'g');
          bodyContent = bodyContent.replace(regexAny, '');

          // Tetapkan kembali konten HTML yang telah dimanipulasi ke elemen "content"
          contentElement.innerHTML = bodyContent;
        });          
      }
    </script>
    <?php
    echo '
    <script type="text/javascript">
      var $j = jQuery.noConflict();
      $j(function(){
        $j(document).on(\'click\',\'.folder\', function() {
          var idmember = this.id;            
          if ( $j("#downline"+idmember ).length ) {
            $j("#downline"+idmember).remove();
            $j("#down"+idmember).attr(\'src\',\''.site_url().'/wp-content/plugins/wp-affiliasi/img/folder.gif\');
          } else {
            $j("#member"+idmember).append(\' <img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/load.gif" id="load"/>\');
            $j.get("'.site_url().'/wp-content/plugins/wp-affiliasi/member.php", { id: this.id },
                function(data){
                    $j("#load").remove();
                    $j("#member"+idmember).append(data);
                });
            $j("#down"+idmember).attr(\'src\',\''.site_url().'/wp-content/plugins/wp-affiliasi/img/folderopen.gif\');
          }
        });

        $j("#detilprofil").hide();
        
        $j(document).on(\'click\',\'.close\', function() {
          $j("#detilprofil").hide();
        });

        $j(document).on(\'click\',\'.detil\', function() {
          var idmember = this.id;
          if ($j("#themember").length) {
            $j("#themember").remove();
          }
          $j("#detilprofil").show();
          $j("#detilprofil").append(\' <img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/load.gif" id="load"/>\');
          $j.get("'.site_url().'/wp-content/plugins/wp-affiliasi/member.php", { member: this.id },
            function(data){
                $j("#load").remove();
                $j("#detilprofil").append(data);
            });            
        });
      })
    </script>
    ';
}

add_action( 'wp_footer', 'cb_wp_footer', 20 );


function cbthemehead() {	
	wp_enqueue_script("jquery");
	wp_enqueue_script( 'dtree', plugins_url('/wp-affiliasi/dtree.js'));
	wp_enqueue_script( 'tooltip', plugins_url('/wp-affiliasi/stickytooltip.js'));
	wp_enqueue_style( 'wp-affiliasi', plugins_url('/wp-affiliasi/wp-affiliasi.css'));
	/*	
	wp_enqueue_style( 'dtree', plugins_url('/wp-affiliasi/dtree.css'));
	wp_enqueue_style( 'tooltip', plugins_url('/wp-affiliasi/stickytooltip.css'));
	wp_enqueue_style( 'form', plugins_url('/wp-affiliasi/form.css'));
	wp_enqueue_style( 'produk', plugins_url('/wp-affiliasi/produk.css'));
	wp_enqueue_style( 'jaringan', plugins_url('/wp-affiliasi/jaringan.css'));
	*/
}

add_action('wp_enqueue_scripts', 'cbthemehead');
