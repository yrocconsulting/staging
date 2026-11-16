<?php
/**
 * Lightweight forms (replaces Gravity Forms).
 *
 *   Shortcode: [yroc_form type="contact"]  or  [yroc_form type="audit"]
 *
 * - Works with or without JavaScript (JS submits via fetch; fallback is a normal POST + redirect).
 * - Spam protection: honeypot, signed time-trap (no nonces, so it survives page caching), per-IP rate limit.
 * - Every submission is saved under "Leads" in wp-admin and emailed to the address in Settings → General
 *   (or YROC_FORMS_TO in wp-config.php). The visitor gets a short confirmation email.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yroc_form_defs() {
	return array(
		'contact' => array(
			'title'  => 'Contact',
			'button' => 'Send message',
			'thanks' => 'Thanks! Your message is on its way. We usually reply within one business day.',
			'fields' => array(
				array( 'name', 'Full name', 'text', true, 'half', 'name' ),
				array( 'email', 'Email', 'email', true, 'half', 'email' ),
				array( 'phone', 'Phone', 'tel', false, 'half', 'tel' ),
				array( 'company', 'Company', 'text', false, 'half', 'organization' ),
				array( 'topic', 'What can we help with?', 'select', false, 'full', '', array( 'AI Search Optimization', 'AI Authority Engine', 'Reputation Management', 'Website Design', 'White Label Partnership', 'Something else' ) ),
				array( 'message', 'Message', 'textarea', true, 'full', '' ),
			),
		),
		'audit'   => array(
			'title'  => 'AI Visibility Audit Request',
			'button' => 'Request my audit',
			'thanks' => 'You\'re in! We\'ll start testing how AI sees your business and reach out within one business day.',
			'fields' => array(
				array( 'name', 'Full name', 'text', true, 'half', 'name' ),
				array( 'email', 'Email', 'email', true, 'half', 'email' ),
				array( 'phone', 'Phone', 'tel', false, 'half', 'tel' ),
				array( 'business', 'Business name', 'text', true, 'half', 'organization' ),
				array( 'website', 'Website', 'url', false, 'half', 'url' ),
				array( 'market', 'City / market you serve', 'text', false, 'half', '' ),
				array( 'goal', 'What do you want AI to recommend you for?', 'textarea', false, 'full', '', null, 'e.g. "best roofing company in Dallas" or "top tax resolution firm"' ),
				array( 'competitors', 'Main competitors (optional)', 'text', false, 'full', '' ),
			),
		),
	);
}

/* ---------- Leads post type ---------- */
add_action( 'init', function () {
	register_post_type( 'yroc_lead', array(
		'labels'          => array( 'name' => 'Leads', 'singular_name' => 'Lead', 'all_items' => 'All leads', 'edit_item' => 'Lead' ),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 25,
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
	add_shortcode( 'yroc_form', 'yroc_render_form' );
} );

add_filter( 'manage_yroc_lead_posts_columns', function ( $c ) {
	return array( 'cb' => $c['cb'], 'title' => 'Lead', 'form' => 'Form', 'email' => 'Email', 'date' => 'Received' );
} );
add_action( 'manage_yroc_lead_posts_custom_column', function ( $col, $id ) {
	if ( 'form' === $col ) {
		echo esc_html( get_post_meta( $id, '_form', true ) );
	}
	if ( 'email' === $col ) {
		$e = get_post_meta( $id, '_email', true );
		echo '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
	}
}, 10, 2 );

/* ---------- Spam helpers ---------- */
function yroc_form_token() {
	$t = time();
	return $t . '.' . substr( hash_hmac( 'sha256', (string) $t, wp_salt( 'nonce' ) ), 0, 20 );
}
function yroc_form_token_ok( $tok ) {
	$parts = explode( '.', (string) $tok );
	if ( 2 !== count( $parts ) ) {
		return false;
	}
	list( $t, $sig ) = $parts;
	$ok  = hash_equals( substr( hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) ), 0, 20 ), $sig );
	$age = time() - (int) $t;
	return $ok && $age >= 3; // humans take more than 3 seconds; page cache may make tokens old, which is fine
}

/* ---------- Render ---------- */
function yroc_render_form( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'contact' ), $atts );
	$defs = yroc_form_defs();
	$type = isset( $defs[ $atts['type'] ] ) ? $atts['type'] : 'contact';
	$def  = $defs[ $type ];
	$id   = 'yf-' . $type;
	$sent = isset( $_GET['sent'] ) && $_GET['sent'] === $type; // phpcs:ignore WordPress.Security.NonceVerification

	ob_start();
	?>
	<form class="yr-form" id="<?php echo esc_attr( $id ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-yr-form novalidate>
		<input type="hidden" name="action" value="yroc_form">
		<input type="hidden" name="yf_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="yf_token" value="<?php echo esc_attr( yroc_form_token() ); ?>" data-yr-token>
		<input type="hidden" name="yf_page" value="<?php echo esc_attr( get_permalink() ); ?>">
		<div class="yr-form__hp" aria-hidden="true"><label>Leave this empty <input type="text" name="yf_hp" tabindex="-1" autocomplete="off"></label></div>
		<div class="yr-form__grid">
			<?php foreach ( $def['fields'] as $f ) :
				list( $name, $label, $ftype, $req, $width, $ac ) = $f;
				$opts = $f[6] ?? null;
				$ph   = $f[7] ?? '';
				$fid  = $id . '-' . $name;
				?>
				<div class="yr-field yr-field--<?php echo esc_attr( $width ); ?>">
					<label for="<?php echo esc_attr( $fid ); ?>"><?php echo esc_html( $label ); ?><?php echo $req ? ' <span aria-hidden="true">*</span>' : ''; ?></label>
					<?php if ( 'textarea' === $ftype ) : ?>
						<textarea id="<?php echo esc_attr( $fid ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4" placeholder="<?php echo esc_attr( $ph ); ?>"<?php echo $req ? ' required' : ''; ?>></textarea>
					<?php elseif ( 'select' === $ftype ) : ?>
						<select id="<?php echo esc_attr( $fid ); ?>" name="<?php echo esc_attr( $name ); ?>">
							<option value="">Choose one…</option>
							<?php $pre = isset( $_GET[ $name ] ) ? sanitize_text_field( wp_unslash( $_GET[ $name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>
							<?php foreach ( $opts as $o ) : ?><option<?php selected( $pre, $o ); ?>><?php echo esc_html( $o ); ?></option><?php endforeach; ?>
						</select>
					<?php else : ?>
						<input id="<?php echo esc_attr( $fid ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $ftype ); ?>"<?php echo $ac ? ' autocomplete="' . esc_attr( $ac ) . '"' : ''; ?><?php echo $req ? ' required' : ''; ?> placeholder="<?php echo esc_attr( $ph ); ?>" value="<?php echo esc_attr( isset( $_GET[ $name ] ) ? sanitize_text_field( wp_unslash( $_GET[ $name ] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification ?>">
					<?php endif; ?>
					<p class="yr-field__err" aria-live="polite"></p>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="yr-form__foot">
			<button type="submit" class="yr-btn"><span><?php echo esc_html( $def['button'] ); ?></span></button>
			<p class="yr-form__fine">We'll never share your info. By submitting you agree to our <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">privacy policy</a>.</p>
		</div>
		<div class="yr-form__msg" role="status" aria-live="polite"<?php echo $sent ? '' : ' hidden'; ?>><?php echo $sent ? esc_html( $def['thanks'] ) : ''; ?></div>
	</form>
	<?php
	return ob_get_clean();
}

/* ---------- Handle ---------- */
add_action( 'admin_post_nopriv_yroc_form', 'yroc_handle_form' );
add_action( 'admin_post_yroc_form', 'yroc_handle_form' );

function yroc_handle_form() {
	$ajax = isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'application/json' );
	$defs = yroc_form_defs();
	$type = sanitize_key( $_POST['yf_type'] ?? '' );
	$back = esc_url_raw( wp_unslash( $_POST['yf_page'] ?? home_url( '/' ) ) );
	$back = wp_validate_redirect( $back, home_url( '/' ) );

	$fail = function ( $msg, $errors = array(), $code = 400 ) use ( $ajax, $back ) {
		if ( $ajax ) {
			wp_send_json( array( 'ok' => false, 'message' => $msg, 'errors' => $errors ), $code );
		}
		wp_safe_redirect( add_query_arg( 'form_error', rawurlencode( $msg ), $back ) );
		exit;
	};

	if ( ! isset( $defs[ $type ] ) ) {
		$fail( 'Unknown form.' );
	}
	$def = $defs[ $type ];

	// Bots: pretend success, store nothing.
	if ( ! empty( $_POST['yf_hp'] ) || ! yroc_form_token_ok( wp_unslash( $_POST['yf_token'] ?? '' ) ) ) {
		if ( $ajax ) {
			wp_send_json( array( 'ok' => true, 'message' => $def['thanks'] ) );
		}
		wp_safe_redirect( add_query_arg( 'sent', $type, $back ) . '#yf-' . $type );
		exit;
	}

	$ip  = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$key = 'yroc_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 5 ) {
		$fail( 'Too many submissions. Please try again in an hour or call us at 903.300.LEADS.', array(), 429 );
	}

	$data   = array();
	$errors = array();
	foreach ( $def['fields'] as $f ) {
		list( $name, $label, $ftype, $req ) = $f;
		$raw = wp_unslash( $_POST[ $name ] ?? '' );
		$val = 'textarea' === $ftype ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
		$val = mb_substr( $val, 0, 'textarea' === $ftype ? 5000 : 300 );
		if ( $req && '' === $val ) {
			$errors[ $name ] = 'This field is required.';
		} elseif ( 'email' === $ftype && $val && ! is_email( $val ) ) {
			$errors[ $name ] = 'Please enter a valid email.';
		} elseif ( 'select' === $ftype && $val && ! in_array( $val, $f[6], true ) ) {
			$val = '';
		}
		$data[ $name ] = array( $label, $val );
	}
	if ( $errors ) {
		$fail( 'Please fix the highlighted fields.', $errors, 422 );
	}
	set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

	$who   = $data['name'][1] . ( ! empty( $data['business'][1] ) ? ' | ' . $data['business'][1] : ( ! empty( $data['company'][1] ) ? ' | ' . $data['company'][1] : '' ) );
	$lines = array();
	foreach ( $data as $d ) {
		if ( '' !== $d[1] ) {
			$lines[] = $d[0] . ': ' . $d[1];
		}
	}
	$lines[] = 'Page: ' . $back;
	$body    = implode( "\n", $lines );

	$lead = wp_insert_post( array(
		'post_type'    => 'yroc_lead',
		'post_status'  => 'private',
		'post_title'   => $def['title'] . ': ' . $who,
		'post_content' => $body,
	) );
	if ( $lead && ! is_wp_error( $lead ) ) {
		update_post_meta( $lead, '_form', $def['title'] );
		update_post_meta( $lead, '_email', $data['email'][1] );
		update_post_meta( $lead, '_fields', $data );
	}

	$to = defined( 'YROC_FORMS_TO' ) ? YROC_FORMS_TO : get_option( 'admin_email' );
	wp_mail( $to, '[Website] ' . $def['title'] . ': ' . $who, $body . "\n\nView in WordPress: " . admin_url( 'edit.php?post_type=yroc_lead' ), array( 'Reply-To: ' . $data['name'][1] . ' <' . $data['email'][1] . '>' ) );
	wp_mail(
		$data['email'][1],
		'We got your request | Yroc Consulting',
		"Hi {$data['name'][1]},\n\n{$def['thanks']}\n\nIf it's urgent, call us at 903.300.LEADS (903.300.5323).\n\nThe Yroc Consulting team\nhttps://yrocconsulting.com"
	);

	if ( $ajax ) {
		wp_send_json( array( 'ok' => true, 'message' => $def['thanks'] ) );
	}
	wp_safe_redirect( add_query_arg( 'sent', $type, $back ) . '#yf-' . $type );
	exit;
}
