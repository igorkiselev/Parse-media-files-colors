<?php
/**
 * Plugin Name: Media Colors
 * Plugin URI: http://www.igorkiselev.com/wp-plugin/media-colors/
 * Description: Plugin that analyses uploaded image and generates most used colors in it.
 * Version: 1.0.3
 * Author: Igor Kiselev, Joe Hoyle, Marc Pacheco, Markus Näsman,Gaurav Sharma, Wencheng Wu and Edul N. Dalal.
 * Author URI: http://www.igorkiselev.com/
 * License: A "JustBeNice" license name e.g. GPL2.
 */

if (! defined('ABSPATH')) {
    exit;
}

load_plugin_textdomain('media-colors', false, dirname(plugin_basename(__FILE__)) . '/languages/');



add_action('admin_init', function () {
    register_setting('media-colors', 'media-colors_settings');
});



add_action('admin_head', function () {
    echo	'<style>'.
            'span.color{width:2em; height:2em; margin-right:.5em; margin-bottom:.5em; display:inline-block; border-radius:2px; border:1px Solid rgba(0,0,0,0.1)}'.
                'span.marker{display:inline-block; padding:2px 8px; border-radius:2px;}'.
                'span.marker.dark{background-color:#000; color:#fff}'.
                'span.marker.light{background-color:#fff; color:#000}'.
            '</style>';
});

$set = get_option('media-colors_settings');

class mediaColor
{
	public function attachmentPallete($id){
		
	    $array = get_post_meta($id, 'media-color', true);

	    if ($array) {
			
	        $colors = "";
            
	        foreach ($array as &$value) {
	        
			    $colors .= '<span class="color" style="background-color:'.$value.';"></span>';
	        
			}
            
	        return $colors;
	    
		}
	
	}
	
	public function get_all_attachments_count(){
	    
		$query_images_args = array(
	        'post_type' => 'attachment',
			'post_mime_type' =>'image',
			'post_status' => 'inherit',
			'posts_per_page' => -1,
	        'meta_query' => array(
				array(
					'key' => 'media-color',
					'compare' => 'NOT EXISTS'
				),
	        ),
	    );

	    $query_images = new WP_Query($query_images_args);
    
	    return count($query_images->posts);
	}
	
	private function get_all_attachments(){
	    
	    global $set;
        
    
	    $query_images_args = array(
	        'post_type' => 'attachment',
	        'post_mime_type' =>'image',
	        'post_status' => 'inherit',
	        'posts_per_page' => 60,
	        'meta_query' => array(
	            array(
	                'key'     => 'media-color',
	                'compare' => 'NOT EXISTS',
	            ),
	        ),
	    );

	    $query_images = new WP_Query($query_images_args);
    
	    foreach ($query_images->posts as $image) {
			
	        $src = wp_get_attachment_image_src($image->ID, $set['quality'])[0];
        
	        $colors = $this->attachment_hex_array($src);
        
	        $tone = ($this->attachment_hex_average($colors) > (255 / 2)) ? true : false;
        
	        update_post_meta($image->ID, 'media-color', $colors);
        
	        update_post_meta($image->ID, 'media-tone', $tone);
	    }
		
	}
	
	public function cron_get_all_attachments(){
		$this->get_all_attachments();
	}
	
	private function attachment_hex_average($array){
	    
		$tone = 0;
    
	    foreach ($array as &$value) {
	        list($r, $g, $b) = sscanf($value, "#%02x%02x%02x");
        
	        $tone += ($r + $g + $b) / 3;
	    }
    
	    return floor($tone / count($array));
	    
	}
	
	private function attachment_hex_array($src){
	    
		require_once('colorsofimage.class.php');
		
	    $class = new ColorsOfImage($src);

	    $colors = $class->getProminentColors();
    
	    $background = $class->getBackgroundColor();
    
	    if ($background) {
	        $colors[] = $background;
	    }
    
	    return $colors;
	    
	}

}


$mediaColor = new mediaColor();


add_filter('attachment_fields_to_edit', function ($form_fields, $post) {
    global $mediaColor;
	global $set;

    $colors = $mediaColor->attachmentPallete($post->ID);
    
    if ($colors):
        
        $form_fields['media-color'] = array(
            
            'label' => __('Main colors in the image', 'media-colors'),
            
            'input' => 'html',
            
            'html' => $colors,
            
            'helps' => __('Colors that dominate in the image. Available for you as an array from post meta "media-color".', 'media-colors'),
            
        );
        
    $tone = (get_post_meta($post->ID, 'media-tone', true)) ? __('Light', 'media-colors') : __('Dark', 'media-colors');
        
    $form_fields['media-tone'] = array(
            
            'label' => __('Image brightness', 'media-colors'),
            
            'input' => 'html',
            
            'html' => $tone,
            
            'helps' => __('Brightness of the image (dark or light). Available as a boolean from post meta "media-tone".', 'media-colors'),
        );
    
    endif;

    return $form_fields;
	
}, 10, 2);


add_filter('attachment_fields_to_save', function ($post, $attachment) {
    global $set;
        
    $src = (!empty($set['quality'])) ? wp_get_attachment_image_src($post->ID, $set['quality'])[0] : $post['attachment_url'];
       
    $colors = $mediaColor->attachment_hex_array($src);
        
    $tone = ( $mediaColor->attachment_hex_average($colors) > (255 / 2)) ? true : false;
        
    update_post_meta($post['ID'], 'media-color', $colors);
        
    update_post_meta($post['ID'], 'media-tone', $tone);
        
    
    return $post;
}, 10, 2);


if (!empty($set['column'])) {
	
    add_filter('manage_media_columns', function ($columns) {
		
        $columns['colors'] = __('Colors', 'media-colors');
		
        $columns['theme'] = __('Theme', 'media-colors');
        
        return $columns;
		
    }, 10, 2);

    add_action('manage_media_custom_column', function ($column_name, $id) {
		
		global $mediaColor;
		
		switch ($column_name):
			
			case 'colors':
			
			echo $mediaColor->attachmentPallete($id);
				
			break;
			
			case 'theme':
				$theme = get_post_meta($id, 'media-tone');
			
				if (!empty($theme)):
					echo ($theme[0]) ?  '<span class="marker light">'.__('Light', 'media-colors').'</span>' : '<span class="marker dark">'.__('Dark', 'media-colors').'</span>';
				endif;
				
			break;
				
			default:
			
			break;
			
		endswitch;
    }, 10, 2);
}


if (!empty($set['cron'])) {
	
    if ($mediaColor->get_all_attachments_count() != 0) {
    
	
	
		register_activation_hook(__FILE__, function(){
		    if (! wp_next_scheduled ( 'justbenice_media_hourly_event' )) {
			wp_schedule_event(time(), 'hourly', 'justbenice_media_hourly_event');
		    }
		});

		add_action('justbenice_media_hourly_event', function(){
		    global $mediaColor;
			
			$mediaColor->cron_get_all_attachments();
			
		});

		register_deactivation_hook(__FILE__, function(){
			wp_clear_scheduled_hook('justbenice_media_hourly_event');
		});
	
	
	}

}

add_action('admin_menu', function () {
    
	add_options_page(
    __('Media parser', 'media-colors'),
    __('Media parser', 'media-colors'),
    'manage_options',
    'media-colors',
    
	    function () {
			
            (!current_user_can('manage_options')) ? wp_die(__('You do not have sufficient permissions to access this page.', 'media-colors')) : '';
	
			global $mediaColor;
        
            ?>
			<div class="wrap">
				<style>
					.form-table.form-table-border{border-top:1px Solid #fff;}
					.form-table.form-table-google{background-color:#fff;border-radius:20px;}
					.disabled{opacity:0.3;}
					.form-table td{vertical-align:top}
					.form-table th{width:15%;}
					p.description{width:80%;}
				</style>

				<h1><?php _e('Media parser', 'media-colors'); ?></h1>

				<form method="post" action="options.php">
	
				<?php settings_fields('media-colors');
    
			    $settings = get_option('media-colors_settings');
    
			    ?>
		
	
					<table class="form-table">
						<tr>
							<th scope="row">
								<?php _e('Basic settings', 'media-colors'); ?>
							</th>
							<td>
								<label><input name="media-colors_settings[column]" type="checkbox" value="1" <?php (isset($settings['column']) ? checked('1', $settings['column']) : false);?> /> <strong><?php _e('Colors column', 'media-colors')?></strong></label>
								<p class="description">
									<?php _e('Show collor palletes in media library', 'media-colors')?>
								</p>
				
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Image size for parcing', 'media-colors'); ?>
							</th>
							<td>
								<select name='media-colors_settings[quality]'>
									<?php foreach (get_intermediate_image_sizes() as $item) {
			        					$selected = ($settings['quality']==$item) ? 'selected="selected"' : '';
			        					echo '<option value="'.$item.'" '.$selected.'>'.$item.'</option>';
			    					} ?>
								</select>
								<p class="description">
									<?php _e('Select image parse quality (the smallest the fastest)', 'media-colors')?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Basic settings', 'media-colors'); ?>
							</th>
							<td>
								<label><input name="media-colors_settings[cron]" type="checkbox" value="1" <?php (isset($settings['cron']) ? checked('1', $settings['cron']) : false);?> /> <strong><?php _e('Cron task', 'media-colors')?></strong></label>
								<p class="description">
									<?php
									printf( esc_html__( 'You have %1$s, images that dont have colors attached. Add a cron task to convert 60 images every hour.', 'media-colors' ), $mediaColor->get_all_attachments_count() );
									?>
								</p>
				
							</td>
						</tr>
		
					</table>
		
				<?php do_settings_sections('theme-options'); ?>
				<?php submit_button(); ?>

				<p>
					<?php _e('Plugin to make work easier. Developed by Igor Kiselev in <a href="//www.justbenice.ru/">Just Be Nice</a>', 'media-colors'); ?>
				</p>
			</form>
			</div>
			<?php
        }
    );
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    return array_merge($links, array('<a href="' . admin_url('options-general.php?page=media-colors') . '">'.__('Settings', 'media-colors').'</a>'));
});
