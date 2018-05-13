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
<<<<<<< HEAD
					<?php _e('Image size for parcing', 'media-colors'); ?>
=======
					&nbsp;
				</th>
				<td width="42%">
					<?php _custom_checkbox('disable-attachment-pages', __('Attachment pages', 'libraries'), __('Remove attachment pages. If you fill all titles, captions and descriptions on the media file, then maybe keep the pages. Improves SEO of the website.', 'libraries')); ?>
				</td>
				<td width="42%">
					
				</td>
			</tr>
		</table>
		<table class="form-table form-table-border">
			<tr>
				<th scope="row">
					<?php _e('Wordpress interface', 'libraries'); ?>
				</th>
				<td width="42%">
					<?php _custom_checkbox('disable-adminbar', __('Remove administrator bar', 'libraries'), __('Hide admin panel on the website', 'libraries')); ?>
				</td>
				<td width="42%">
					<?php _custom_checkbox('enable-navmenus', __('Menu in nav', 'libraries'), __('Move the menu item to the main navigation bar', 'libraries')); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					
				</th>
				<td width="42%">
					<?php _custom_checkbox('featured-admin-image', __('Feature images', 'libraries'), __('Add feature images in post and page lists', 'libraries')); ?>
				</td>
				<td width="42%">
					<?php _custom_checkbox('custom-filetypes', __('Custom filetypes', 'libraries'), __('Add support for custom filetypes (SVG)', 'libraries')); ?>
				</td>
			</tr>
			
			
		</table>
		<table class="form-table form-table-border">
			<tr>
				<th scope="row">
					<?php _e('Content parser', 'libraries'); ?>
				</th>
				<td width="42%">
					<?php _custom_checkbox('content-the_title', __('No title', 'libraries'), __('Display the phrase "No title" in the_title when the title of the post or page is empty', 'libraries')); ?>
				</td>
				<td width="42%">
					<?php _custom_checkbox('header-wp_title', __('Site name in title', 'libraries'), __('Display the name of the website (company) in the header after the page name (wp_title)', 'libraries')); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					&nbsp;
				</th>
				<td width="42%">
					<?php _custom_checkbox('settings-privateprefix', __('Remove "private"', 'libraries'), __('Remove "private" prefix from posts in loop', 'libraries')); ?>
				</td>
				<td width="42%">
					<?php _custom_checkbox('opengraph', __('Opengraph meta', 'libraries'), __('Opengraph meta fields in  &#60;head&#47;&#62;', 'libraries')); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					&nbsp;
				</th>
				
				<td width="42%">
					<?php _custom_checkbox('header-wp_title-separator', __('Change title separator', 'libraries'), __('Change the title separator', 'libraries')); ?><p><?php _custom_input('header-wp_title-separator-character', 'header-wp_title-separator', '', '', ''); ?></p>
				</td>
				<td width="42%">
					<?php _custom_checkbox('media_oembed_filter', __('oEmbed contols', 'libraries'), __('Remove controls from youtube, mixcloud and vimeo interface', 'libraries')); ?>
				</td>
			</tr>
			
		</table>
		<table class="form-table form-table-border">
			<tr>
				<th scope="row">
					<?php _e('RSS feed', 'libraries'); ?>
>>>>>>> 4323dc12d7400c800d34ba91588d35435f3c543a
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
					<?php _e('Debug', 'media-colors'); ?>
				</th>
				<td>
					<label><input name="media-colors_settings[debug]" type="checkbox" value="1" <?php (isset($settings['debug']) ? checked('1', $settings['debug']) : false);?> /> <strong><?php _e('Show debugger information')?></strong></label>
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