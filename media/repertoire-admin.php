<?php

//include_once 'custom-taxon-meta.php';

include 'repertoire-post.php';
include 'repertoire-taxo.php';
  
  
add_action( 'submitpost_box', 'extra_column_edit_form' );
add_action( 'submitpage_box', 'extra_column_edit_form' );
function extra_column_edit_form() {
  global $post_type, $post;
  
	/*if( !in_array($post_type, array('attachment', 'repertoire-media', 'repertoire') ) ) {
		do_meta_boxes( $post_type, 'column3', $post );
		
		if(  'repertoire' == $post_type )
			do_meta_boxes( $post_type, 'column4', $post );
			}*/
	
	if( 'attachment' != $post_type ) {
    do_meta_boxes( $post_type, 'column3', $post );
    
    if( 'repertoire' == $post_type )
      do_meta_boxes( $post_type, 'column4', $post );
	  }
}

add_action( 'admin_print_styles-post.php',     'extra_column_css');
add_action( 'admin_print_styles-post-new.php', 'extra_column_css');

function extra_column_css () {
  global $post_type;
  if( 'attachment' == $post_type ) return;
?>
<style type="text/css">
  <?php

  if( 'repertoire' == $post_type ) {
    
  ?>
  
  #poststuff #post-body.columns-2 {
    margin-right: 900px !important;
  }
  #post-body.columns-2 #postbox-container-1 {
    margin-right: -900px !important;
    width: 880px !important;
  }
  #post-body-content {
    margin-bottom: 0 !important;
  }
  
  #cctmsong_year, #cctmarrangement_year {
    width: 60px !important;
  }
  #cctmcopyright_notice {
    width:100%;
    max-width: 400px;
    height: 80px;
  }
  
  <?php } else { ?>
  
  #poststuff #post-body.columns-2 {
    margin-right: 600px !important;
  }
  #post-body.columns-2 #postbox-container-1 {
    margin-right: -600px !important;
    width: 580px !important;
  }
  
  <?php } ?>
  
  #column3-sortables,
  #column4-sortables,
  #side-sortables {
    width: 280px;
    float: right;
    display: block;
    min-height: 200px;
  }
  
  #column4-sortables {
    margin-right:20px;
  }
  
  #side-sortables {
    float: left;
  }
  
  #category-all {
    height: 400px !important;
    max-height: 400px;
  }
  
  #slugdiv #post_name {
    width:100%;
  }
  
  
</style>
<?php }