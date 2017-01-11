<?php
/*
 * Plugin Name: Epilog - Bir Mısra
 * Plugin URI: http://epilog.mefhum.org/bir-misra
 * Description: Pulling random poems, verses from a list.
 * Author: Samet Altun
 * Version: 1.0
 * Author URI: http://github.com/sametaltun
 * License: GPLv2 or later
 * TextDomain: bir-misra
 * DomainPath: lang/
 */

$birmisra = new birmisra();

class birmisra {

	/**
	 *
	 */
	private $page_name;

	/**
	 *
	 */
	function __construct() {
		add_action( 'widgets_init',    array( $this, 'birmisra_load_widget' ) );

		add_action( 'admin_menu',      array( $this, 'menu' ) );
		add_action( 'contextual_help', array( $this, 'help'), 10, 3 );

		add_shortcode( 'erq', 'erq_shortcode' );
	}

	/**
	 *
	 */
	function birmisra_load_widget() {
		register_widget( 'birmisra_widget' );
	}

	/**
	 *
	 */
	function menu() {
		$this->page_name = add_options_page(
			__( 'Bir Mısra', 'easy-random-quotes' ),
			__( 'Bir Mısra', 'easy-random-quotes' ),
			'manage_options',
			__FILE__,
			array( $this, 'page' )
		);

		add_action( 'admin_head-'.$this->page_name, array( $this, 'verify_nonce' ) );

	}

	function verify_nonce() {
		if ( isset( $_POST['erq_add'] ) || isset( $_POST['erq_import'] ) ) {
			check_admin_referer( 'easyrandomquotes-update_add' );
		}
		if ( isset( $_POST['erq_quote'] ) ) {
			check_admin_referer( 'easyrandomquotes-update_edit' );
		}
	}
	/**
	 *
	 */
	function update() {

		if ( isset( $_POST['erq_add'] ) ) {
			$newquote = wp_filter_post_kses( $_POST['erq_newquote'] );

			if ( ! empty( $newquote ) ) {

				$theQuotes = get_option( 'kl-easyrandomquotes', array() ); //get existing

				$theQuotes[] = $newquote; //add new

				check_admin_referer( 'easyrandomquotes-update_add' );

				update_option( 'kl-easyrandomquotes', $theQuotes ); //successfully updated

				$this->alert_message( __( 'Bir Mısra eklendi' , 'easy-random-quotes' ) );

			} else {
				$this->alert_message( __( 'Eklenmedi' , 'easy-random-quotes' ) );
			}

		} // end if add

		if ( isset( $_POST['erq_import'] ) ) {

			$newquotes = array_filter( explode( "\n", $_POST['erq_newquote'] ) );
			$newquotes = array_map( 'wp_filter_post_kses', $newquotes );

			$erq_count = count( $newquotes );

			$theQuotes = get_option( 'kl-easyrandomquotes', array() );

			// array_merge messes up the keys, and using the '+' method will skip certain items
			foreach ( $newquotes as $newquote ) {
				$theQuotes[] = $newquote;
			}

			check_admin_referer( 'easyrandomquotes-update_add' );

			update_option( 'kl-easyrandomquotes', $theQuotes ); //successfully updated

			$this->alert_message( sprintf( __( '%d tane mısra eklendi', 'easy-random-quotes' ), $erq_count ) );

		} // end if add

		if ( isset( $_POST[ 'erq_quote' ] ) ) {

			$ids       = $_POST[ 'erq_quote' ];
			$dels      = isset( $_POST[ 'erq_del' ] ) ? $_POST[ 'erq_del' ] : array();
			$theQuotes =  get_option( 'kl-easyrandomquotes', array() );

			// update each quote
			foreach ( $ids as $id => $quote ) {
				$theQuotes[ $id ] = wp_filter_post_kses( $quote ); // update each part with new quote
			}

			// delete all checked quotes
			foreach ( $dels as $id => $quote ) {
				unset( $theQuotes[ $id ] );
			}

			check_admin_referer( 'easyrandomquotes-update_edit' );

			if ( update_option( 'kl-easyrandomquotes',$theQuotes ) ) {
				$this->alert_message( __( 'Bir mısra düzenlendi/silindi' , 'easy-random-quotes' ) );
			} else {
				$this->alert_message( __( 'Değişen yok' , 'easy-random-quotes' ) );
			}

		} // end if edit

		if ( isset( $_POST[ 'clear' ] ) ) {

			if ( ! isset( $_POST[ 'confirm' ] ) ) {

				$this->alert_message( __( 'You must confirm for a reset' , 'easy-random-quotes' ) );

			} elseif ( delete_option( 'kl-easyrandomquotes' ) ) {

				$this->alert_message( __( 'Tüm mısralar silindi' , 'easy-random-quotes' ) );

			}

		} // end if clear

	} // end update()


	function alert_message( $message, $type='updated' ) {
		echo '<div class="' . esc_attr( $type ) . '"><p>' . $message . '</p></div>';
	}

	/**
	 *
	 */
	function page() {
		$this->update();
		echo '<div class="wrap">';

		echo '<h2>' . __( 'Bir Mısra' , 'easy-random-quotes' ) . '</h2>';

		echo '<h3>' . __( 'Bir Mısra Ekle' , 'easy-random-quotes' ) . '</h3>';
			echo '<form method="post">';
			wp_nonce_field( 'easyrandomquotes-update_add' );
			echo '<table class="widefat page"><thead><tr><th class="manage-column" colspan = "2">' . __( 'Yeni Ekle' , 'easy-random-quotes' ) . '</th></tr></thead><tbody><tr>';
			echo '<td><textarea name="erq_newquote" rows="6" cols="60"></textarea></td>';
			echo '<td><p>';

			submit_button( __( 'Ekle' , 'easy-random-quotes' ), 'small', 'erq_add', false );
			echo ' ';
			submit_button( __( 'Aktar' , 'easy-random-quotes' ), 'small', 'erq_import', false );

			echo '</p><p>'. __( 'With Import, each new line will be treated as the start of a new quote', 'easy-random-quotes' ) .'</p></td>';
			echo '</tr></tbody></table>';
			echo '</form>';

		echo '<h3>' . __( 'Mısraları Düzenle' ) . '</h3>';
			echo '<form method="post">';
			wp_nonce_field( 'easyrandomquotes-update_edit' );

			$tblrows = '<tr><td class="manage-column column-cb check-column" id="cb"><input type="checkbox" /></th>
							<th scope="col" id="quote" class="manage-column column-quote">' . __( 'Mısra' , 'easy-random-quotes' ) . '</th>
							<th scope="col" id="shortcode" class="manage-column column-shortcode">' . __( 'Kısa Kod (yazı/sayfa)' , 'easy-random-quotes' ) . '</th></tr>';

			echo '<table class="widefat page"><thead>' . $tblrows . '</thead><tfoot>' . $tblrows . '</tfoot><tbody>';

			$theQuotes =  get_option( 'kl-easyrandomquotes', array() ) ;

			if ( ! empty( $theQuotes ) ) {
				foreach ( $theQuotes as $id=>$quote ) {
					echo '<tr>';
					echo '<th class="check-column"><input type="checkbox" name="erq_del[' . $id . ']" /></th>';
					echo '<td><textarea name="erq_quote[' . $id . ']" rows="6" cols="60">' . esc_textarea( stripslashes( $quote ) ) . '</textarea></td>';
					echo '<td>[erq id=' . $id . ']</td>';
					echo '</tr>';
				}
			} else {
				echo '<tr><th colspan="3">' . __( 'Mısra yok' , 'easy-random-quotes' ) . '</th></tr>';
			}

			echo '</tbody></table>';

			echo '<p>';
			submit_button( __( 'Kaydet' , 'easy-random-quotes' ), 'primary', 'submit', false );
			echo ' <span class="description">' . __( 'İşaretli mısralar silinecek' , 'easy-random-quotes' ) . '</span>';
			echo '</p>';
			echo '</form>';

		echo '</div>';

	}// end page()

	/**
	 *
	 */
	function help( $contextual_help, $screen_id, $screen ) {

		if ( $screen_id != $this->page_name ) return;

		$string1 = sprintf( __( 'Specific quote: %s' , 'easy-random-quotes' ), '<code>[erq id=2]</code>' );
		$string2 = sprintf( __( 'Random quote: %s' , 'easy-random-quotes' ), '<code>[erq]</code>' );
		$content = '<p><strong>' . __( 'Shortcode' , 'easy-random-quotes' ) . '</strong><br />'. $string1 .'<br />' . $string2 .'</p>';

		$string1 = sprintf( __( 'Specific quote: %s' , 'easy-random-quotes' ), '<code>' . htmlspecialchars( '<?php echo erq_shortcode(array(\'id\' => \'2\')); ?>' ) . '</code>' );
		$string2 = sprintf( __( 'Random quote: %s' , 'easy-random-quotes' ), '<code>' . htmlspecialchars( '<?php echo erq_shortcode(); ?>' ) . '</code>' );
		$content .= '<p><strong>' . __( 'Template tag' , 'easy-random-quotes' ) . '</strong><br />'. $string1 .'<br />' . $string2 .'</p>';

		$content .= '<p>' . __( 'Quotes retained when plugin deactivated. Quotes deleted when plugin removed.' , 'easy-random-quotes' ) . '</p>';

		$content .= '<form method="post">';
		$content .= get_submit_button( __( 'Delete All Quotes' , 'easy-random-quotes' ), 'secondary', 'clear', false ) .
		' <label><input type="checkbox" name="confirm" value="true" />' . __( 'Check to confirm' , 'easy-random-quotes' ) . '</label></form>';

		$screen->add_help_tab( array(
			'id'      => 'erq_help',
			'title'   => 'Help',
			'content' => $content,
		) );

	}

}

/**
 * shortcode/template tag
 * outside of class to make it more accessible
 */
function erq_shortcode( $atts=array() ) {
	$atts = shortcode_atts( array(
		'id' => 'rand',
	), $atts );

	$id = $atts['id'];

	$id = explode( ',', $id );
	shuffle( $id );
	$id = array_pop( $id );

	$theQuotes = get_option( 'kl-easyrandomquotes', array() ); //get exsisting
	$use = ( 'rand' == $id ) ? array_rand( $theQuotes ) : $id;
	if ( isset( $theQuotes[ $use ] ) ) {
		return stripslashes( $theQuotes[ $use ] );
	}
}

class birmisra_widget extends WP_Widget {

	/**
	 *
	 */
	function __construct() {
		$widget_ops = array(
			'classname'   => 'kl-erq',
			'description' => __( 'Displays random quotes', 'bir-misra' )
		);
		$control_ops = array();
		parent::__construct( 'kl-erq', __( 'Bir Mısra', 'bir-misra' ), $widget_ops, $control_ops );
	}

	/**
	 *
	 */
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		if ( ! $instance[ 'hide_title' ] ) {
			echo $before_title . apply_filters( 'widget_title', $instance[ 'title' ] ) . $after_title;
		}
		echo '<p>' . erq_shortcode() . '</p>';
		echo $after_widget;
	}

	/**
	 *
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']      = esc_attr( $new_instance['title'] );
		$instance['hide_title'] = (bool) $new_instance['hide_title'] ? 1 : 0;
		return $instance;
	}

	/**
	 *
	 */
	function form( $instance ) {
		$defaults = array(
			'title'      => __( 'A Random Poem', 'bir-misra' ),
			'hide_title' => 0
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bir-misra' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hide_title'); ?>" name="<?php echo $this->get_field_name('hide_title'); ?>"<?php checked( $instance['hide_title'] ); ?> />
			<label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide Title?', 'bir-misra' );?></label>
		</p>
		<?php
	}

}