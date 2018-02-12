<?php

/**
 * The plugin bootstrap file
 *
 * @link 		https://www.tccimfg.com
 * @since 		1.0.0
 * @author 		DCC Marketing <web@dccmarketing.com>
 * @package 	TCCI_Navboxes
 *
 * @wordpress-plugin
 * Plugin Name: 		T/CCI Nav Boxes
 * Plugin URI: 			http://www.tccimfg.com/tcci-navboxes/
 * Description: 		Creates navigation boxes on a page.
 * Version: 			1.0.0
 * DCC Marketing: 		DCC Marketing
 * DCC Marketing URI: 	http://demanddcc.com/
 * License: 			GPL-2.0+
 * License URI: 		http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 		tcci-navboxes
 * Domain Path: 		/assets/languages
 * Github Plugin URI: 	https://github.com/dccmarketing/tcci-navboxes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

add_action( 'init', 'tcci_navboxes_hooks' );

/**
 * Registers all the WordPress hooks and filters for this class.
 */
function tcci_navboxes_hooks() {

	add_shortcode( 'navboxes', 'tcci_navboxes_shortcode_navboxes' );

} // tcci_navboxes_hooks()

/**
 * Returns a WordPress menu for a shortcode.
 *
 * @hooked 		add_shortcode
 * @param 		array 		$atts 			Shortcode attributes
 * @param 		mixed 		$content 		The page content
 * @return 		mixed 						A WordPress menu
 */
function tcci_navboxes_shortcode_navboxes( $atts, $content = null ) {

	$defaults['type'] 		= 'pages'; // Could be: pages, cats, tax
	$defaults['include']	= array();
	$defaults['layout'] 	= 'third'; 	// Could be: full, half, third, fourth.
										// Could also include image sizes: short - 150px, tall - 250px
	$defaults['terms'] 		= '';
	$args					= shortcode_atts( $defaults, $atts, 'navboxes' );

	if ( empty( $args ) ) { return; }

	ob_start();

	if ( 'pages' === $args['type'] ) {

		$items = get_pages( $args );

	} elseif ( 'cats' === $args['type'] ) {

		$items = get_categories( $args );

	} elseif ( 'tax' === $args['type'] ) {

		$items = get_terms( $args['terms'] );

	}

	?><ul class="wrap-navboxes"><?php

	foreach ( $items as $item ) :

		if ( 'pages' === $args['type'] ) {

			$link = get_page_link( $item->ID );

			if ( empty( $link ) ) { continue; }

			$images = tcci_get_featured_images( $item->ID );

			if ( empty( $images ) ) { continue; }

			$imgsrc = $images['sizes']['full']['url'];
			$title = $item->post_title;

		} elseif ( 'cats' === $args['type'] ) {

			$link = get_category_link( $item->term_id );

			if ( empty( $link ) ) { continue; }

			$images = get_field( 'category_image', $item );
			$imgsrc = $images['url'];
			$title = $item->name;

		} elseif ( 'tax' === $args['type'] ) {

			$link = get_term_link( $item->term_id );

			//if ( empty( $link ) ) { continue; }

			$meta 	= get_term_meta( $item->term_id );

			//if ( empty( $meta ) ) { continue; }

			$imgsrc = wp_get_attachment_image_src( $meta['market-thumb'][0], 'medium' )[0];
			$title = $item->name;

		}

		?><li class="navbox <?php echo esc_attr( $args['layout'] ); ?>">
			<a class="navbox-link" href="<?php echo esc_url( $link ); ?>"><?php

				if ( ! empty( $imgsrc ) ) :

					?><div class="navbox-img" style="background-image:url(<?php echo esc_url( $imgsrc );?>);"></div><?php

				endif;

				?><h3><?php echo esc_html( $title ); ?></h3>
			</a>
		</li><?php

	endforeach;

	?></ul><?php

	$output = ob_get_contents();

	ob_end_clean();

	return $output;

} // tcci_navboxes_shortcode_navboxes()
