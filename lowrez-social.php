<?php
/*
Plugin Name: LOW REZ Social Widget
Description: LOW REZ Social Widget
Author: LOW REZ
Version: 1.0
*/

class lowrez_social_widget extends WP_Widget {
 
    /** constructor -- name this the same as the class above */
    function __construct() {
        parent::__construct(false, $name = 'LOW REZ Social Widget');	
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {	
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = 'Follow LOW REZ';
        $message 	= $instance['message'];
		$facebook 	= $instance['facebook'];
		$twitter 	= $instance['twitter'];
		$mailchimp 	= $instance['mailchimp'];
		
		/*
		
    Twitter: #00a0d1
    Facebook: #3b5998
    RSS: #fa9b39
    YoutTube: #c4302b
    Vimeo Blue: #44bbff
    Vimeo Green: #aad450
    Pinterest: #910101
    Tumblr: #34526f

		*/
		
		/*
		
width: 0px;
height: 0px;
border-style: solid;
border-width: 5px 8.7px 5px 0;
border-color: transparent #007bff transparent transparent;

		
		*/
		
		//$badge = '<span class="badge" style="border:1px solid #ccc;background:white;color:#555;border-radius-bottom-left:0px;">%s</span>';
		$badge = '<div class="social-count-widget-count"><i></i><u></u><div class="social-count-widget-val">%s</div></div>';
		
        ?>
              <?php echo $before_widget; ?>
			  <div class="sidebar-nav popbox" style="text-align:center;">
					<ul class="nav nav-list">	
					<?php if ( $title ) echo $before_title . $title . $after_title; ?>
					</ul>
				  <div style="margin:0 0 20px;">
				  <?php
		if ( $facebook ) { 
			printf('<a href="%s" target="_blank" class="btn" style="margin-left:0;padding:4px;width:36px;" title="Follow LOW REZ on Facebook"><i class="icon-facebook-sign icon-2x" style="color:#3b5998;"></i></a> %s ', $facebook, sprintf($badge, flc_count()));
		}
				  ?>
				  <?php if ( $twitter ) printf('<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script> <a href="%s" target="_blank" class="btn" style="margin-left:0;padding:4px;width:36px;" title="Follow LOW REZ on Twitter"><i class="icon-twitter icon-2x" style="color:#00a0d1;"></i></a> %s<br>', $twitter, sprintf($badge, twitterCount('lowrezmelbourne'))); ?>
					  </div>
					  <?php if ( $mailchimp ) printf('<ul class="nav nav-list"><li class="nav-header">Mailing List</li></ul><form action="%s" method="post" target="_blank" novalidate>
	<input type="hidden" name="SOURCE" value="lowrez.com.au">
	<label for="mce-EMAIL" class="hidden" aria-hidden="true">Email</label>
	<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Email Address" style="width:150px;max-width:90%%;">
	<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn" title="Join the LOW REZ Mailing List on MailChimp">
	%s
	</div>
</form>', $mailchimp, sprintf($badge, '<script type="text/javascript" language="JavaScript" src="http://lowrez.us5.list-manage1.com/subscriber-count?b=00&u=7221e643-9446-4a29-942e-cfd77d165d57&id=09b17d3d12"></script>')); ?>
				</div>
              <?php echo $after_widget; ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
		$instance['facebook'] = strip_tags($new_instance['facebook']);
		$instance['twitter'] = strip_tags($new_instance['twitter']);
		$instance['mailchimp'] = strip_tags($new_instance['mailchimp']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
 
        $title 		= esc_attr($instance['title']);
        $message	= esc_attr($instance['message']);
		$facebook	= esc_attr($instance['facebook']);
		$twitter	= esc_attr($instance['twitter']);
		$mailchimp	= esc_attr($instance['mailchimp']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('Simple Message'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" value="<?php echo $message; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook URL'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo $facebook; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter URL'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo $twitter; ?>" />
        </p>
<p>
          <label for="<?php echo $this->get_field_id('mailchimp'); ?>"><?php _e('Mailchimp Subscribe URL'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('mailchimp'); ?>" name="<?php echo $this->get_field_name('mailchimp'); ?>" type="text" value="<?php echo $mailchimp; ?>" />
        </p>
        <?php 
    }
 
 
} // end class example_widget
add_action('widgets_init', create_function('', 'return register_widget("lowrez_social_widget");'));
?>