<?php
/**
 Plugin Name: Post template plugin
 Plugin URI: http://hi.baidu.com/netwolf103
 Description: Post template plugin

 Version: 1.0
 Author: Alex Zhang
 Author URI: http://hi.baidu.com/netwolf103/
*/

function get_post_templates() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$page_templates = array ();

	if ( is_array( $templates ) ) {
		foreach ( $templates as $template ) {
			$template_data = implode( '', file(WP_CONTENT_DIR.$template ));

			$name = '';
			if ( preg_match( '|Post Template:(.*)$|mi', $template_data, $name ) )
				$name = _cleanup_header_comment($name[1]);

			if ( !empty( $name ) ) {
				$page_templates[trim( $name )] = basename( $template );
			}
		}
	}

	return $page_templates;
}

function post_template_dropdown( $default = '' ) {
	$templates = get_post_templates();
	ksort( $templates );
	foreach (array_keys( $templates ) as $template )
		: if ( $default == $templates[$template] )
			$selected = " selected='selected'";
		else
			$selected = '';
	echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
	endforeach;
}

function post_templates($post){
	$post->post_template = get_post_meta( $post->ID, '_post_template', true );

	if ( 0 != count( get_post_templates() ) ) { ?>

	<label class="screen-reader-text" for="post_template"><?php _e('Post Template') ?></label>
	<select name="post_template" id="post_template">
	<option value='default'><?php _e('Default Template'); ?></option>
	<?php post_template_dropdown($post->post_template); ?>
	</select>
<?php
	}
}

function add_post_meta_box()
{
	if(!function_exists('add_meta_box'))
		return false;

	add_meta_box('posttemplate', __('Template'), 'post_templates', 'post', 'side', 'core');
}

function save_post_templatedata($postID)
{
	if(isset($_POST['post_template']))
	if ( !update_post_meta($postID, '_post_template', $_POST['post_template']) )
		add_post_meta($postID, '_post_template', $_POST['post_template']);
}

add_action('admin_menu', 'add_post_meta_box');
add_action('save_post', 'save_post_templatedata');
?>