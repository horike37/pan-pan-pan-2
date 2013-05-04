<?php

/*
  Plugin Name: Pan Pan Pan 2
  Version: 1.0
  Plugin URI: http://kakunin-pl.us
  Description: Do you need pan-pan-pan?
  Author: horike37
  Author URI: http://kakunin-pl.us
  License: GPLv2 or later
 */

if ( ! defined( 'SPPANPANPAN_PLUGIN_URL' ) )
	define( 'SPPANPANPAN_PLUGIN_URL', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ));

if ( ! defined( 'SPPANPANPAN_PLUGIN_DIR' ) )
	define( 'SPPANPANPAN_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ));

add_theme_support('post-thumbnails');
//カスタム投稿タイプの作成
add_action( 'init', 'sppanpanpan_create_initial_post_types' );
function sppanpanpan_create_initial_post_types() {
	$labels = array(
		'name' => sprintf( __( '%s', 'panpanpan' ), __( '背景画像スライド', 'panpanpan' ) ),
		'singular_name' => sprintf( __( '%s', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'add_new_item' => sprintf( __( 'Add New %s', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'edit_item' => sprintf( __( 'Edit %s', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'new_item' => sprintf( __( 'New %s', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'view_item' => sprintf( __( 'View %s', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'search_items' => sprintf( __( 'Search %s', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'not_found' => sprintf( __( 'No %s found.', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
		'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'panpanpan' ), __( '背景画像', 'panpanpan' ) ),
	);
	$args = array(
		'labels' => $labels,
		'public' => false, // false ; show_ui=false, publicly_queryable=false, exclude_from_search=true, show_in_nav_menus=false
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array( 'title', 'thumbnail', 'page-attributes' ),
		'rewrite' => false,
	);
	register_post_type( 'sp-pan-pan-pan', $args );
}

//メタボックスの追加
//add_action( 'admin_menu', 'sppanpanpan_add_meta_boxes' );
function sppanpanpan_add_meta_boxes() {
	add_meta_box( 'add-sp-pan-pan-pan-link', __( 'Slide Links', 'sp-pan-pan-pan' ), 'sppanpanpan_add_link_box', 'sp-pan-pan-pan', 'normal', 'high' );
}

function sppanpanpan_add_link_box() {
	$post_id = get_the_ID();
	$get_noncename = 'slide_link_noncename';
	$url = esc_url( get_post_meta( $post_id, '_slide_link', true ) );
	$blank = (int) get_post_meta( $post_id, '_slide_blank', true );
	echo '<input type="hidden" name="' . $get_noncename . '" id="' . $get_noncename . '" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	echo '<p><label for="slide_link">' . __( 'Link : ', 'pan-pan-pan' );
	echo '<input type="text" name="slide_link" id="slide_link" value="' . $url . '" size="30"></label></p>';
	//echo '<p><label for="slide_blank"><input type="checkbox" name="slide_blank" id="slide_blank" value="1"' . checked( 1, $blank, false ) . '> ' . __( 'Open link in a new window/tab' ) . '</label></p>';
}

//データ登録
add_action( 'save_post', 'sp_pan_pan_pan_link_save' );
function sp_pan_pan_pan_link_save( $post_id ) {
	$get_noncename = 'slide_link_noncename';
	$key1 = '_slide_link';
	$post1 = 'slide_link';
	$key2 = '_slide_blank';
	$post2 = 'slide_blank';
	$get1 = esc_url( $_POST[$post1] );
	$get2 = (int) $_POST[$post2];
	if ( !isset( $_POST[$get_noncename] ) )
		return;
	if ( !wp_verify_nonce( $_POST[$get_noncename], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}
	if ( '' == get_post_meta( $post_id, $key1 ) ) {
		add_post_meta( $post_id, $key1, $get1, true );
	} else if ( $get1 != get_post_meta( $post_id, $key1 ) ) {
		update_post_meta( $post_id, $key1, $get1 );
	} else if ( '' == $get1 ) {
		delete_post_meta( $post_id, $key1 );
	}

	if ( '' == get_post_meta( $post_id, $key2 ) ) {
		add_post_meta( $post_id, $key2, $get2, true );
	} else if ( $get2 != get_post_meta( $post_id, $key2 ) ) {
		update_post_meta( $post_id, $key2, $get2 );
	} else if ( '' == $get2 ) {
		delete_post_meta( $post_id, $key2 );
	}
}

//stylesheetの登録
add_action( 'wp_print_styles', 'sppanpanpan_add_slide_style' );
function sppanpanpan_add_slide_style() {
	if ( !is_admin() ) {
		wp_enqueue_style( 'sp-pan-pan-pan-style', SPPANPANPAN_PLUGIN_URL . '/css/style.css' );
		wp_enqueue_style( 'sp-pan-pan-pan-supersized-style', SPPANPANPAN_PLUGIN_URL . '/css/supersized.css' );		
		wp_enqueue_style( 'sp-pan-pan-pan-supersized.shutter-style', SPPANPANPAN_PLUGIN_URL . '/theme/supersized.shutter.css' );
	}
}

//JavaScriptの登録
add_action( 'wp_print_scripts', 'sppanpanpan_add_slide_js' );
function sppanpanpan_add_slide_js() {
	if ( !is_admin() ) {
		wp_enqueue_script( 'sp-pan-pan-pan-easing', SPPANPANPAN_PLUGIN_URL . '/js/jquery.easing.min.js', array( 'jquery' ), '0.7.1.0' );
		wp_enqueue_script( 'sp-pan-pan-pan-supersized', SPPANPANPAN_PLUGIN_URL . '/js/supersized.3.2.7.min.js', array( 'jquery' ), '0.7.1.0' );
		wp_enqueue_script( 'sp-pan-pan-pan-supersized.shutter', SPPANPANPAN_PLUGIN_URL . '/theme/supersized.shutter.min.js', array( 'jquery' ), '0.7.1.0' );
		wp_enqueue_script( 'sp-pan-pan-pan-common', SPPANPANPAN_PLUGIN_URL . '/js/common.min.js', array( 'jquery' ), '0.7.1.0' );
	}
}

//テーマで呼び出す関数
function sppanpanpan_get_slide_post( $limit = -1 ) {
	$ret = '';
	$posts_array = array();
	$args = array(
	    'post_type' => 'sp-pan-pan-pan',
	    'posts_per_page' => $limit,
	    'orderby' => 'menu_order',
	    'order' => 'ASC',
	);
	$posts_array = get_posts( $args );
	$ret = '[';
	if ( $posts_array ) {    
	    foreach ( $posts_array as $post ) {
	    	$ret .= '{';
	    	$attachment_id = get_post_thumbnail_id( $post->ID );				
	    	$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' );
	    	$ret .= 'image:"'.$image_attributes[0].'",';
	    	$ret .= 'title:"'.esc_js( get_the_title( $post->ID ) ).'",';
	    	$image_attributes = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
	    	$ret .= 'thumb:"'.$image_attributes[0].'"';
	    	if ( get_post_meta( $post->ID, '_slide_link', true ) !== '' )
	    		$ret .= ',url:"'.get_post_meta( $post->ID, '_slide_link', true ).'"';
	    	$ret .= '},';
	    }
	}
	$ret = rtrim($ret, ',');
	$ret .= ']';
	return $ret;
}

function sppanpanpan_show_slide() {
	return sppanpanpan_get_slide_post();
}
add_shortcode( 'mlp_show_slide_background', 'sppanpanpan_show_slide' );

/*
 * 管理画面の一覧にサムネイルと順番を表示
 * 参照　http://www.warna.info/archives/1661/
 * 参照　http://www.webopixel.net/wordpress/167.html
 */

// カラムを追加
function sppanpanpan_manage_posts_columns( $posts_columns ) {
	$new_columns = array();
	foreach ( $posts_columns as $column_name => $column_display_name ) {
		if ( $column_name == 'date' ) {
			$new_columns['thumbnail'] = __('Thumbnail');
			$new_columns['order'] = __( 'Order' );
			add_action( 'manage_posts_custom_column', 'sppanpanpan_add_column', 10, 2 );
		}
		$new_columns[$column_name] = $column_display_name;
	}
	return $new_columns;

}

// 追加したカラムの中身
function sppanpanpan_add_column($column_name, $post_id) {
	$post_id = (int)$post_id;

	// アイキャッチ
	if ( $column_name == 'thumbnail') {
		$thum = ( get_the_post_thumbnail( $post_id, array(50,50), 'thumbnail' ) ) ? get_the_post_thumbnail( $post_id, array(50,50), 'thumbnail' ) : __('None') ;
		echo $thum;
	}

	// 順序
	if ( $column_name == 'order' ) {
		$post = get_post( $post_id );
		echo $post->menu_order;
	}
}

// 追加したカラムのスタイルシート
function sppanpanpan_add_menu_order_column_styles() {
	if ('sp-pan-pan-pan' == get_post_type()) {
		
?>
<style type="text/css" charset="utf-8">
.fixed .column-thumbnail {
	width: 10%;
}
.fixed .column-order {
	width: 7%;
	text-align: center;
}
</style>
<?php
	}
}

// 順序でソートできるように
function sp_add_menu_order_sortable_column( $sortable_column ) {
	$sortable_column['order'] = 'menu_order';
	return $sortable_column;
}

add_filter( 'manage_sp-pan-pan-pan_posts_columns', 'sppanpanpan_manage_posts_columns' );
add_action( 'admin_print_styles-edit.php', 'sppanpanpan_add_menu_order_column_styles' );
add_filter( 'manage_edit-sp-pan-pan-pan_sortable_columns', 'sp_add_menu_order_sortable_column' );

add_action( 'admin_menu', 'sppanpanpan_admin_menu' );

function sppanpanpan_admin_menu() {
	add_options_page( __( '背景画像 スライド設定' ), __( '背景画像 スライド設定' ), 'manage_options', 'sp_pan_pan_pan', 'sp_pan_pan_pan_options_page');
}

function sp_pan_pan_pan_options_page() {
?>
<div class="wrap">
<?php screen_icon(); ?>

<h2><?php _e( '背景画像 スライド設定' ); ?></h2>

<form action="options.php" method="post">
<?php settings_fields( 'sp_pan_pan_pan_options' ); ?>
<?php do_settings_sections( 'sp_pan_pan_pan' ); ?>

<p class="submit"><input name="Submit" type="submit" value="<?php _e( 'save' ) ?>" class="button-primary" /></p>
</form>

</div>
<?php
}

add_action( 'admin_init', 'sp_pan_pan_pan_admin_init' );

function sp_pan_pan_pan_admin_init() {
	register_setting( 'sp_pan_pan_pan_options', 'sp_pan_pan_pan_options', 'sp_pan_pan_pan_options_validate' );

	add_settings_section( 'sp_pan_pan_pan_main', __( '設定' ), 'sp_pan_pan_pan_section_text', 'sp_pan_pan_pan' );

	add_settings_field( 'sp_pan_pan_pan_slideshowSpeed', __( 'slideshowSpeed' ), 'sp_pan_pan_pan_setting_slideshowSpeed',
		'sp_pan_pan_pan', 'sp_pan_pan_pan_main' );

	add_settings_field( 'sp_pan_pan_pan_animationSpeed', __( 'animationSpeed' ), 'sp_pan_pan_pan_setting_animationSpeed',
		'sp_pan_pan_pan', 'sp_pan_pan_pan_main' );
		
	add_settings_field( 'sp_pan_pan_pan_slidetype', __( 'slidetype' ), 'sp_pan_pan_pan_setting_slidetype',
		'sp_pan_pan_pan', 'sp_pan_pan_pan_main' );

}

function sp_pan_pan_pan_section_text() {
}

function sp_pan_pan_pan_setting_slideshowSpeed() {
	$options = get_option( 'sp_pan_pan_pan_options' );
	$options['slideshowSpeed'] = $options['slideshowSpeed'] != '' ? $options['slideshowSpeed'] : 7000;

	echo '<input id="sp_pan_pan_pan_slideshowSpeed" name="sp_pan_pan_pan_options[slideshowSpeed]" size="40" type="text" value="' . esc_attr( $options['slideshowSpeed'] ) . '" /> second';
}

function sp_pan_pan_pan_setting_animationSpeed() {
	$options = get_option( 'sp_pan_pan_pan_options' );
	$options['animationSpeed'] = $options['animationSpeed'] != '' ? $options['animationSpeed'] : 600;

	echo '<input id="sp_pan_pan_pan_animationSpeed" name="sp_pan_pan_pan_options[animationSpeed]" size="40" type="text" value="' . esc_attr( $options['animationSpeed'] ) . '" /> second';
}

function sp_pan_pan_pan_setting_slidetype() {
	$options = get_option( 'sp_pan_pan_pan_options' );
	$options['slidetype'] = $options['slidetype'] != '' ? $options['slidetype'] : 1;

	echo '<select id="sp_pan_pan_pan_animationSpeed" name="sp_pan_pan_pan_options[slidetype]" >';
	echo '<option '.selected( $options['slidetype'], 1 ).' value="1">Fade</option>';
	echo '<option '.selected( $options['slidetype'], 2 ).' value="2">Slide Top</option>';
	echo '<option '.selected( $options['slidetype'], 3 ).' value="3">Slide Right</option>';
	echo '<option '.selected( $options['slidetype'], 4 ).' value="4">Slide Bottom</option>';
	echo '<option '.selected( $options['slidetype'], 5 ).' value="5">Slide Left</option>';
	echo '<option '.selected( $options['slidetype'], 6 ).' value="6">Carousel Right</option>';
	echo '<option '.selected( $options['slidetype'], 7 ).' value="7">Carousel Left</option>';
	echo '</select>';
}

function sp_pan_pan_pan_options_validate( $input ) {
	$newinput['slideshowSpeed'] = absint( $input['slideshowSpeed'] );
	$newinput['animationSpeed'] = absint( $input['animationSpeed'] );
	$newinput['slidetype'] = absint( $input['slidetype'] );

	return $newinput;
}

add_action( 'wp_footer', 'sppanpanpan_wp_footer' );
function sppanpanpan_wp_footer(){
	$options = get_option( 'sp_pan_pan_pan_options' );
	$slideshowSpeed = $options['slideshowSpeed'] != '' ? $options['slideshowSpeed'] : 7000;
	$animationSpeed = $options['animationSpeed'] != '' ? $options['animationSpeed'] : 600;
	$slidetype = $options['slidetype'] != '' ? $options['slidetype'] : 1;
?>
<script type="text/javascript">
	var sp_slide_conf = {
		slideshowSpeed : <?php echo esc_js( $slideshowSpeed ); ?>,
		animationSpeed : <?php echo esc_js( $animationSpeed ); ?>,
		slidetype : <?php echo esc_js( $slidetype ); ?>
		
	};
	var sp_slide_param = <?php echo sppanpanpan_get_slide_post(); ?>;
</script>
<?php
}

?>
