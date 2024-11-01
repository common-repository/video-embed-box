<?php
if (!defined('ABSPATH')) exit; // Restrict direct access
/*
 Plugin Name: Video Embed
 Plugin Url: http://topfreelancers.esy.es/video-embed-demo/
 Description: This plugin allow you to embed youtube, vimeo and videosuite videos with its PDF, audio file, links & embed code with just simple shortcode.
 Author: Prashant Rawal
 License: GPLv2 or later
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
 Version: 1.0
 Author URI: http://skyseainfo.com
    Copyright 2014  Prahsnat Rawal  (email : rawalprashant123@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


define('VIDEO_EMBED_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('VIDEO_EMBED_PDF_ICON', VIDEO_EMBED_PLUGIN_URL . 'pdf.png');
define('VIDEO_EMBED_AUDIO_ICON', VIDEO_EMBED_PLUGIN_URL . 'audio.png');
define('VIDEO_EMBED_LINK_ICON', VIDEO_EMBED_PLUGIN_URL . 'link.png');
define('VIDEO_EMBED_EMBED_ICON', VIDEO_EMBED_PLUGIN_URL . 'link.png');

function register_video_style(){
wp_register_style( 'video-embed.css', VIDEO_EMBED_PLUGIN_URL . 'video-embed.css', array(), '2.5.4.4' );
wp_enqueue_style( 'video-embed.css');
}
add_action('wp_head', 'register_video_style');

/* Code for plugin */
// Following will run when plugin is activated
function video_embed_plugin_activate() {
	global $wpdb;
	$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'video_embed` ( `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `title` VARCHAR( 255 ) NOT NULL, `video_embed_for` VARCHAR( 255 ) NOT NULL ,`url` VARCHAR( 255 ) NULL , `pdf` VARCHAR( 255 ) NULL , `audio` VARCHAR( 255 ) NULL , `useful_link` TEXT NULL );');
	add_option('video_embed_pdf_icon', VIDEO_EMBED_PDF_ICON);
	add_option('video_embed_audio_icon', VIDEO_EMBED_AUDIO_ICON);
	add_option('video_embed_link_icon', VIDEO_EMBED_LINK_ICON);
	add_option('video_embed_embed_icon', VIDEO_EMBED_EMBED_ICON);
	
	add_option('video_embed_box_bg');
	add_option('video_embed_box_bg_end');
	
	add_option('video_embed_des_box_bg');
	add_option('video_embed_des_box_bg_end');
	
	add_option('video_embed_active_bg');
	add_option('video_embed_active_bg_end');
	
	add_option('video_embed_link_color');
	add_option('video_embed_link_des_color');
}
register_activation_hook(__FILE__, 'video_embed_plugin_activate');

// Following will run when plugin is deactivated
function video_embed_plugin_deactivate() {
	global $wpdb;
	$wpdb->query('DROP TABLE `'.$wpdb->prefix.'video_embed`');
}
register_deactivation_hook(__FILE__, 'video_embed_plugin_deactivate');


//This will called when admin clicks on the lefthand side option -- List page
function video_embed_launch_page() {
	global $wpdb;
?>
	<div class="wrap">
		<h2 style="float: left; width: 100%;"><img src="<?php echo VIDEO_EMBED_PLUGIN_URL; ?>video-embed.png" style="float: left;" height="80px" width="80px" /><span style="line-height: 3; float: left;">Welcome To Video Embed</span></h2>
		<p>Here you can manage the video embed records</p>
		
		<div class="listing">
			<table id="video_embed_listing" border="1">
				<?php 
					$data = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'video_embed');
					if (count($data) > 0)
					{ ?>
						<thead>
							<tr>
								<th>Id</th>
								<th>Title</th>
								<th>Url</th>
								<th>Pdf path</th>
								<th>Audio Path</th>
								<th>Useful link</th>
								<th>Shortcode</th>
								<th>Option</th>
							</tr>
						</thead>
					<?php 
						wp_enqueue_script('video-embed-script', VIDEO_EMBED_PLUGIN_URL.'video-embed.js');
						foreach ($data as $k => $v)
						{
							echo '<tr>';
								echo '<td>'.$v->id.'</td>';
								echo '<td>'.$v->title.'</td>';
								echo '<td>'.$v->url.'</td>';
								echo '<td>'.$v->pdf.'</td>';
								echo '<td>'.$v->audio.'</td>';
								//echo '<td>'.htmlspecialchars_decode($v->useful_link).'</td>';
								echo '<td>'.$v->useful_link.'</td>';
								echo '<td>[video-embed id="'.$v->id.'"][/video-embed]</td>';
								echo '<td><a href="'.admin_url('admin.php?page=edit-video-embed&id=').$v->id.'">Edit</a> | <a href="'.admin_url('admin.php?page=delete-video-embed&id=').$v->id.'" onclick="return video_embed_confirm();">Delete</a></td>';
							echo '</tr>';
						}
					}
				?>
			</table>
		</div>
	</div>
<?php 
}

/* Delete record */
function delete_video_embed(){
	global $wpdb;
	$res = $wpdb->query( $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'video_embed WHERE `id` =  ' . $_GET['id']) );
	header('Location: ' . admin_url('admin.php?page=video-embed'));
}

/* Edit record */
function edit_video_embed(){
	global $wpdb;
	$data = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'video_embed WHERE `id` =  ' . $_GET['id']);
	if ($_GET['id'] != '') {
		if (isset($_POST['saved']) && $_POST['saved'] == 1)
		{
			if ($_POST['url'] == '')
				$error = true;
			else
			{
				global $wpdb;
				$wpdb->update(
							$wpdb->prefix.'video_embed', 
							array ('title' => $_POST['title'], 'video_embed_for' => $_POST['video_embed_for'] ,'url' => $_POST['url'], 'pdf' => $_POST['pdf'], 'audio' => $_POST['audio'], 'useful_link' => htmlspecialchars($_POST['useful_link'])),
							array( 'id' => $_GET['id'] )
						);
				unset($_POST);
				$edited = true;
				$data = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'video_embed WHERE `id` =  ' . $_GET['id']);
			}
		}
?>
	<div class="wrap">
		<h2 style="float: left; width: 100%;"><img src="<?php echo VIDEO_EMBED_PLUGIN_URL; ?>video-embed.png" style="float: left;" height="80px" width="80px" /><span style="line-height: 3; float: left;">Edit Video Embed</span></h2>
		<?php 
			if (isset($edited) && $edited)
				echo '<p>Record Edited</p>';
		?>
		<form action="" method="post" name="video_embed" style="float: left; width: 100%;" id="video_embed">
			<table>
				<tr>
					<td>Title: </td>
					<td><input type="text" name="title" value="<?php echo $data->title; ?>" /></td>
				</tr>
				<tr style="height: 50px;">
					<td>Video from: </td>
					<td>
						<input type="radio" name="video_embed_for" <?php echo ($data->video_embed_for == 'youtube') ? ('checked') : (''); ?> value="youtube" /> Youtube / Vimeo
						<input type="radio" name="video_embed_for" <?php echo ($data->video_embed_for == 'videosuit') ? ('checked') : (''); ?> value="videosuit" /> VideoSuit
					</td>
				</tr>
				<tr>
					<td>Youtube Embed Url: </td>
					<td>
						<input type="text" name="url" value="<?php echo $data->url; ?>"/>
						<?php if ($error) echo 'can not be blank';?>
						<td><p style="font-size: 11px;">Please specify complete path(including 'http')</p></td>
					</td>
				</tr>
				<tr>
					<td>Pdf Path: </td>
					<td><input type="text" name="pdf" value="<?php echo $data->pdf; ?>" /></td>
					<td><p style="font-size: 11px;">Please specify complete path(including 'http')</p></td>
				</tr>
				<tr>
					<td>Audio Path: </td>
					<td><input type="text" name="audio" value="<?php echo $data->audio; ?>" /></td>
					<td><p style="font-size: 11px;">Please specify complete path(including 'http')</p></td>
				</tr>
				<tr>
					<td>Useful Link: </td>
					<td><textarea name="useful_link"><?php echo $data->useful_link; ?></textarea></td>
					<td><p style="font-size: 11px;">Please specify your html content</p></td>
				</tr>
				<tr>
					<td><input type="submit" value="Save"/>
					<td><input type="hidden" name="saved" value="1" /></td>
				</tr>
			</table>
		</form>
	</div>
<?php }
}

/* To create new record */
function create_new_vide_embed() {
	if (isset($_POST['saved']) && $_POST['saved'] == 1)
	{
		if ($_POST['url'] == '')
			$error = true;
		else
		{
			global $wpdb;
			$wpdb->insert(
						$wpdb->prefix.'video_embed', 
						array ('title' => $_POST['title'], 'video_embed_for' => $_POST['video_embed_for'] ,'url' => $_POST['url'], 'pdf' => $_POST['pdf'], 'audio' => $_POST['audio'], 'useful_link' => htmlspecialchars($_POST['useful_link'])));
			unset($_POST);
			$saved = true;
		}
	}
?>
	<div class="wrap">
		<h2 style="float: left; width: 100%;"><img src="<?php echo VIDEO_EMBED_PLUGIN_URL; ?>video-embed.png" style="float: left;" height="80px" width="80px" /><span style="line-height: 3; float: left;">Add New Video Embed</span></h2>
		<?php 
			if (isset($saved) && $saved)
				echo '<p>Record added</p>';
		?>
		<form action="" method="post" name="video_embed" style="float: left; width: 100%;" id="video_embed">
			<table>
				<tr>
					<td>Title: </td>
					<td><input type="text" name="title" value="<?php echo $_POST['title']; ?>" /></td>
				</tr>
				<tr style="height: 50px;">
					<td>Video from: </td>
					<td>
						<input type="radio" name="video_embed_for" checked value="youtube" /> Youtube / Vimeo
						<input type="radio" name="video_embed_for" value="videosuit" /> VideoSuit
					</td>
				</tr>
				<tr>
					<td>Embed Url: </td>
					<td>
						<input type="text" name="url" />
						<?php if ($error) echo 'can not be blank';?>
						<p style="font-size: 11px;">Please specify complete path(including 'http')</p>
					</td>
				</tr>
				<tr>
					<td>Pdf Path: </td>
					<td><input type="text" name="pdf" value="<?php echo $_POST['pdf']; ?>" /></td>
					<td><p style="font-size: 11px;">Please specify complete path(including 'http')</p></td>
				</tr>
				<tr>
					<td>Audio Path: </td>
					<td><input type="text" name="audio" value="<?php echo $_POST['audio']; ?>" /></td>
					<td><p style="font-size: 11px;">Please specify complete path(including 'http')</p></td>
				</tr>
				<tr>
					<td>Useful Link: </td>
					<td><textarea name="useful_link" cols="41" rows="6"><?php echo $_POST['useful_link']; ?></textarea></td>
					<td><p style="font-size: 11px;">Please specify your html content</p></td>
				</tr>
				<tr>
					<td><input type="submit" value="Save"/>
					<td><input type="hidden" name="saved" value="1" /></td>
				</tr>
			</table>
		</form>
	</div>
<?php 
}

/* Options page */
function vide_embed_options() {
	if (isset($_POST['updated']) && $_POST['updated'] == 1)
	{
		unset($_POST['updated']);
		foreach ($_POST as $k => $v)
			update_option($k, $v);
		$updated = true;
	}
?>
	<h2 style="float: left; width: 100%;"><img src="<?php echo VIDEO_EMBED_PLUGIN_URL; ?>video-embed.png" style="float: left;" height="80px" width="80px" /><span style="line-height: 3; float: left;">Video Embed Options</span></h2>
	<form method="post" action="" style="float: left; width: 100%;" id="video_embed_options">
		<?php 
			if (isset($updated) && $updated)
				echo '<h3>Options are saved.</h3>';
		?>
		<table>
			<tr>
				<td>Pdf icon path: </td>
				<?php 
					$pdf_val = get_option('video_embed_pdf_icon');
					$pdf_val = ($pdf_val != '') ? ($pdf_val) : (VIDEO_EMBED_PDF_ICON);
				?>
				<td><input type="text" name="video_embed_pdf_icon" value="<?php echo $pdf_val; ?>" /></td>
				<td><p style="font-size: 11px;">Please specify complete path(including 'http') &amp; of 35pxx35px</p></td>
			</tr>
			<tr>
				<td>Audio icon path: </td>
				<?php 
					$audio_val = get_option('video_embed_audio_icon');
					$audio_val = ($audio_val != '') ? ($audio_val) : (VIDEO_EMBED_AUDIO_ICON);
				?>
				<td><input type="text" name="video_embed_audio_icon" value="<?php echo $audio_val; ?>" /></td>
				<td><p style="font-size: 11px;">Please specify complete path(including 'http') &amp; of 35pxx35px</p></td>
			</tr>
			<tr>
				<td>Link icon path: </td>
				<?php 
					$link_val = get_option('video_embed_link_icon');
					$link_val = ($link_val != '') ? ($link_val) : (VIDEO_EMBED_LINK_ICON);
				?>
				<td><input type="text" name="video_embed_link_icon" value="<?php echo $link_val; ?>" /></td>
				<td><p style="font-size: 11px;">Please specify complete path(including 'http') &amp; of 35pxx35px</p></td>
			</tr>
			<tr>
				<td>Embed icon path: </td>
				<?php 
					$embed_val = get_option('video_embed_embed_icon');
					$embed_val = ($embed_val != '') ? ($embed_val) : (VIDEO_EMBED_EMBED_ICON);
				?>
				<td><input type="text" name="video_embed_embed_icon" value="<?php echo $embed_val; ?>" /></td>
				
				<td><p style="font-size: 11px;">Please specify complete path(including 'http') &amp; of 35pxx35px</p></td>
			</tr>
			<tr>
				<td>Box background color(start): </td>
				<td><input type="text" name="video_embed_box_bg" class="my-color-field" value="<?php echo get_option('video_embed_box_bg'); ?>" /></td>
				<td>Box background color(end): </td>
				<td><input type="text" name="video_embed_box_bg_end" class="my-color-field" value="<?php echo get_option('video_embed_box_bg_end'); ?>" /></td>
			</tr>
			<tr>
				<td>Descryption Box background color(start): </td>
				<td><input type="text" name="video_embed_des_box_bg" class="my-color-field" value="<?php echo get_option('video_embed_des_box_bg'); ?>" /></td>
				<td>Descryption Box background color(end): </td>
				<td><input type="text" name="video_embed_des_box_bg_end" class="my-color-field" value="<?php echo get_option('video_embed_des_box_bg_end'); ?>" /></td>
			</tr>
			<tr>
				<td>Active tab color(start): </td>
				<td><input type="text" name="video_embed_active_bg" class="my-color-field" value="<?php echo get_option('video_embed_active_bg'); ?>" /></td>
				<td>Active tab color(end): </td>
				<td><input type="text" name="video_embed_active_bg_end" class="my-color-field" value="<?php echo get_option('video_embed_active_bg_end'); ?>" /></td>
			</tr>
			<tr>
				<td>Descryption Font color: </td>
				<td><input type="text" name="video_embed_link_des_color" class="my-color-field" value="<?php echo get_option('video_embed_link_des_color'); ?>" /></td>
			</tr>
			<tr>
				<td>Links font color: </td>
				<td><input type="text" name="video_embed_link_color" class="my-color-field" value="<?php echo get_option('video_embed_link_color'); ?>" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Update" /> <input type="hidden" value="1" name="updated" /></td>
			</tr>
		</table>
	</form>
<?php 		
}

/* Color */
function mw_enqueue_color_picker( $hook_suffix ) {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );


/* add menu to admins's left side panel */
function video_embed_menu() {
	add_menu_page('Video Embed','Video Embed','manage_options', 'video-embed','video_embed_launch_page', VIDEO_EMBED_PLUGIN_URL.'video-embed-icon.png');
	add_submenu_page("video-embed", "New", "New", 0, "new-video-embed", "create_new_vide_embed");
	add_submenu_page("video-embed", "Options", "Options", 1, "video-embed-options", "vide_embed_options");
	add_submenu_page(null, "Edit", "Edit", 0, "edit-video-embed", "edit_video_embed");
	add_submenu_page(null, "Delete", "Delete", 0, "delete-video-embed", "delete_video_embed");
}
add_action('admin_menu', 'video_embed_menu' );


/* Short code */
function video_embed_short_code($atts, $content=null) {
	shortcode_atts(array('id' => 1, 'height' => 315, 'width' => 560), $atts);
	global $wpdb;
	$data = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'video_embed WHERE `id` = ' . $atts['id']);
	
	if (!isset($atts['height']))
		$atts['height'] = '315';
	
	if (!isset($atts['width']))
		$atts['width'] = '560';
	
	if ($data != '')
	{
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', array(), null, false);
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script('video-embed-script', VIDEO_EMBED_PLUGIN_URL.'video-embed.js');
		
		$video_embed_pdf_icon = get_option('video_embed_pdf_icon');
		$video_embed_audio_icon = get_option('video_embed_audio_icon');
		$video_embed_link_icon = get_option('video_embed_link_icon');
		$video_embed_embed_icon = get_option('video_embed_embed_icon');
		
		$video_embed_box_bg = get_option('video_embed_box_bg');
		$video_embed_box_bg_end = get_option('video_embed_box_bg_end');
		
		$video_embed_des_box_bg = get_option('video_embed_des_box_bg');
		$video_embed_des_box_bg_end = get_option('video_embed_des_box_bg_end');

		$video_embed_active_bg = get_option('video_embed_active_bg');
		$video_embed_active_bg_end = get_option('video_embed_active_bg_end');
		
		$video_embed_link_color = get_option('video_embed_link_color');
		$video_embed_link_des_color = get_option('video_embed_link_des_color');
		
		$video_embed_pdf_icon 	= ($video_embed_pdf_icon == '') 	? (VIDEO_EMBED_PDF_ICON) 	: ($video_embed_pdf_icon);
		$video_embed_audio_icon = ($video_embed_audio_icon == '') 	? (VIDEO_EMBED_AUDIO_ICON) 	: ($video_embed_audio_icon);
		$video_embed_link_icon 	= ($video_embed_link_icon == '') 	? (VIDEO_EMBED_LINK_ICON) 	: ($video_embed_link_icon);
		$video_embed_embed_icon = ($video_embed_embed_icon == '') 	? (VIDEO_EMBED_EMBED_ICON) 	: ($video_embed_embed_icon);
		
		$video_embed_link_color 	= ($video_embed_link_color == '') 		? ('#000') : ($video_embed_link_color);
		$video_embed_link_des_color = ($video_embed_link_des_color == '') 	? ('#000') : ($video_embed_link_des_color);
		$video_embed_box_bg 		= ($video_embed_box_bg == '') 			? ('#fff') : ($video_embed_box_bg);
		$video_embed_box_bg_end 	= ($video_embed_box_bg_end == '') 		? ('#fff') : ($video_embed_box_bg_end);
		$video_embed_des_box_bg		= ($video_embed_des_box_bg == '')		? ('#fff') : ($video_embed_des_box_bg);
		$video_embed_des_box_bg_end	= ($video_embed_des_box_bg_end == '')	? ('#fff') : ($video_embed_des_box_bg_end);
		$video_embed_active_bg		= ($video_embed_active_bg == '') 		? ('#fff') : ($video_embed_active_bg);
		$video_embed_active_bg_end	= ($video_embed_active_bg_end == '') 	? ('#fff') : ($video_embed_active_bg_end);
		
		$grediant = 'background: -moz-linear-gradient(center top , :start, :end) repeat scroll 0 0 transparent;background: -webkit-linear-gradient(bottom, :end 15%, :end 34%, :start 82%);';
		$box_bg = str_replace(array(":start", ":end"), array($video_embed_box_bg, $video_embed_box_bg_end), $grediant);
		$des_bg = str_replace(array(":start", ":end"), array($video_embed_des_box_bg, $video_embed_des_box_bg_end), $grediant);
		$active_bg = str_replace(array(":start", ":end"), array($video_embed_active_bg, $video_embed_active_bg_end), $grediant);
		
		$html .= '<style>.video_embed_active{ '.$active_bg.' } .activated { '.$box_bg.' }</style>';
		
		$html .= '<div class="video_embed_wrap_all"><div class="video_embed_wrap_title" style="width: '.$atts['width'].'px;">'.$data->title.'<span id="arraw" class="down">&nbsp;</span></div>';
		$html .= '<div class="video_embed_wrap" style="'.$box_bg.'width: '.$atts['width'].'px;">';
			$html .= '<div class="video_embed_wrap_frame">';
				if ($data->video_embed_for == 'youtube')
					$html .= '<iframe width="'.$atts['width'].'" height="'.$atts['height'].'" frameborder="0" src="'.$data->url.'"></iframe>';
				else {
					wp_enqueue_script('video-suite-script', $data->url);
					$emp = explode('?', $data->url);
					parse_str($emp[1], $video_url);
					$html .= '<div style="width: '.$atts['width'].'px !important;" id="'.$video_url['container'].'" data-role="evp-video" data-evp-id="'.$video_url['id'].'"></div>';
				}
			$html .= '</div>';
			$html .= '<div class="video_embed_wrap_tab" style="'.$des_bg.'">';
				
				$html .= '<ul id="video_embed_wrap_tab_ul">';
					$html .= '<li class="left video_embed_active" ><a style="background-image: url(\''.$video_embed_pdf_icon.'\'); color: '.$video_embed_link_color.'" class="left" id="video_embed_wrap_tab_pdf" href="" onclick="javascript: return false;">Pdf</a></li>';
					$html .= '<li class="left"><a style="background-image: url(\''.$video_embed_audio_icon.'\'); color: '.$video_embed_link_color.'" class="left" id="video_embed_wrap_tab_audio" href="" onclick="javascript: return false;">Audio</a></li>';
					$html .= '<li class="left"><a style="background-image: url(\''.$video_embed_link_icon.'\'); color: '.$video_embed_link_color.'" class="left" id="video_embed_wrap_tab_link" href="" onclick="javascript: return false;">Links</a></li>';
					$html .= '<li class="left"><a style="background-image: url(\''.$video_embed_embed_icon.'\'); color: '.$video_embed_link_color.'" class="left" id="video_embed_wrap_tab_embed" href="" onclick="javascript: return false;">Embed</a></li>';
				$html .= '</ul>';
					
				$html .= '<div class="video_embed_wrap_tab_content" style="color: '.$video_embed_link_des_color.'">';
					
					$html .= '<div class="video_embed_wrap_tab_show video_embed_wrap_tab_pdf">';
						if ($data->pdf != '')
						{
							$html .= 'A PDF is available for download';
							$html .= '&nbsp; &nbsp; &nbsp; <a class="video_embed_down" href="'.$data->pdf.'">Download</a>';
						}
						else
							$html .= 'No PDF found.';
					$html .= '</div>';
					
					$html .= '<div class="video_embed_wrap_tab_hide video_embed_wrap_tab_audio">';
					if ($data->audio != '')
					{
						$html .= 'You can also play the audio only or download the mp3 file below. ';
						$html .= '<audio controls><source src="'.$data->audio.'" type="audio/mp3">
									Your browser does not support the audio element.
								  </audio> ';
						$html .= '<br />Or <a href="'.$data->audio.'">Download MP3</a>';
					}else
							$html .= 'No Audio found.';
					$html .= '</div>';
					
					$html .= '<div class="video_embed_wrap_tab_hide video_embed_wrap_tab_link">';
					if ($data->useful_link !=  '')
					{
						$html .= 'Useful Link:';
						$html .= '<br />'.html_entity_decode($data->useful_link);
					}
					else
							$html .= 'No Useful link found.';
					$html .= '</div>';
					
					$html .= '<div class="video_embed_wrap_tab_hide video_embed_wrap_tab_embed">Want to embed this video on your site? Copy the code below: <br />
								<code>&lt;iframe src="'.$data->url.'" &gt;&lt;/iframe&gt;</code>
							  </div>';
				$html .= '</div>';
				
			$html .= '</div>';
		$html .= '</div></div>';
	}
	return $html;
}
add_shortcode('video-embed', 'video_embed_short_code');
?>