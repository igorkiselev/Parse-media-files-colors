<?php if (!defined('ABSPATH')) {
    exit;
}



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
						printf( esc_html__( 'You have %1$s, images that dont have colors attached. Add a cron task to convert 60 images every hour.', 'media-colors' ), _get_all_attachments_count() );
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