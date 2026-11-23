<?php
/**
 * Formulaires du site :
 *  - [jolya_contact] ou [jolya_contact sujet="Location"] : demande de contact ;
 *  - [jolya_location] : demande de location d'un caftan.
 *
 * Chaque demande est envoyée par e-mail (adresse réglée dans Personnaliser → Contact)
 * ET enregistrée dans l'administration, menu « Demandes ».
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------
 * Enregistrement des demandes dans l'administration
 * ------------------------------------------------------------- */

add_action( 'init', function () {
	register_post_type( 'jolya_demande', array(
		'labels'          => array(
			'name'               => 'Demandes',
			'singular_name'      => 'Demande',
			'all_items'          => 'Toutes les demandes',
			'edit_item'          => 'Demande',
			'search_items'       => 'Rechercher une demande',
			'not_found'          => 'Aucune demande pour le moment.',
			'not_found_in_trash' => 'Aucune demande dans la corbeille.',
			'menu_name'          => 'Demandes',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_position'   => 25,
		'menu_icon'       => 'dashicons-email-alt',
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

add_filter( 'manage_jolya_demande_posts_columns', function ( $columns ) {
	return array(
		'cb'              => $columns['cb'],
		'title'           => 'Demande',
		'jolya_type'      => 'Type',
		'jolya_evenement' => "Date de l'événement",
		'jolya_email'     => 'E-mail',
		'date'            => 'Reçue le',
	);
} );

add_action( 'manage_jolya_demande_posts_custom_column', function ( $column, $post_id ) {
	if ( 'jolya_type' === $column ) {
		echo esc_html( 'location' === get_post_meta( $post_id, '_jolya_type', true ) ? 'Location' : 'Contact' );
	} elseif ( 'jolya_evenement' === $column ) {
		$date = get_post_meta( $post_id, '_jolya_evenement', true );
		echo esc_html( $date ? jolya_format_date( $date ) : '—' );
	} elseif ( 'jolya_email' === $column ) {
		$email = get_post_meta( $post_id, '_jolya_email', true );
		if ( $email ) {
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
		}
	}
}, 10, 2 );

/** Pastille avec le nombre de nouvelles demandes dans le menu. */
add_action( 'admin_menu', function () {
	global $menu;
	$count = (int) wp_count_posts( 'jolya_demande' )->private;
	if ( ! $count || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $key => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=jolya_demande' === $item[2] ) {
			$menu[ $key ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $count . '</span></span>';
		}
	}
}, 99 );

/** Une demande ouverte dans l'administration n'est plus « nouvelle ». */
add_action( 'load-post.php', function () {
	$id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore
	if ( $id && 'jolya_demande' === get_post_type( $id ) && 'private' === get_post_status( $id ) ) {
		wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
	}
} );

/* ---------------------------------------------------------------
 * Outils
 * ------------------------------------------------------------- */

function jolya_contact_subjects() {
	return array( 'Un modèle à la vente', 'Location', 'Ma commande', 'Autre demande' );
}

function jolya_event_types() {
	return array( 'Mariage', 'Fiançailles', 'Henné', 'Soirée', 'Autre' );
}

function jolya_sizes() {
	return array( 'S (36 – 38)', 'M (40 – 42)', 'L (44 – 46)', 'XL (48 – 50)', 'Je ne sais pas' );
}

/** Date AAAA-MM-JJ valide, sinon chaîne vide. */
function jolya_clean_date( $value ) {
	$value = sanitize_text_field( (string) $value );
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m ) || ! checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
		return '';
	}
	return $value;
}

function jolya_format_date( $value ) {
	$time = strtotime( $value );
	return $time ? date_i18n( 'j F Y', $time ) : $value;
}

/** Message de confirmation ou d'erreur au-dessus d'un formulaire. */
function jolya_form_notice() {
	$status = isset( $_GET['message'] ) ? sanitize_key( wp_unslash( $_GET['message'] ) ) : ''; // phpcs:ignore
	if ( 'envoye' === $status ) {
		return '<p class="form__notice form__notice--ok" role="status">Merci, votre demande a bien été envoyée. Nous revenons vers vous très vite.</p>';
	}
	if ( 'erreur' === $status ) {
		return '<p class="form__notice form__notice--error" role="alert">La demande n\'a pas pu être envoyée. Vérifiez les champs obligatoires, ou écrivez-nous directement par e-mail.</p>';
	}
	return '';
}

/** Champs cachés communs aux formulaires. */
function jolya_form_hidden( $form ) {
	ob_start();
	?>
	<input type="hidden" name="action" value="jolya_contact">
	<input type="hidden" name="formulaire" value="<?php echo esc_attr( $form ); ?>">
	<input type="hidden" name="retour" value="<?php echo esc_url( get_permalink() ); ?>">
	<?php wp_nonce_field( 'jolya_contact', 'jolya_nonce' ); ?>
	<p class="form__trap" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></p>
	<?php
	return ob_get_clean();
}

function jolya_select( $id, $name, $options, $selected = '', $placeholder = '' ) {
	$html = sprintf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
	if ( $placeholder ) {
		$html .= sprintf( '<option value="">%s</option>', esc_html( $placeholder ) );
	}
	foreach ( $options as $option ) {
		$html .= sprintf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $option ), selected( $selected, $option, false ), esc_html( $option ) );
	}
	return $html . '</select>';
}

/** Colonne « Nous joindre » à gauche des formulaires. */
function jolya_contact_aside( $extra = '' ) {
	$email = sanitize_email( jolya_opt( 'email' ) );
	$phone = preg_replace( '/\D+/', '', (string) jolya_opt( 'whatsapp' ) );
	$insta = esc_url( jolya_opt( 'instagram' ) );
	ob_start();
	?>
	<div class="contact-block__aside">
		<p class="eyebrow">Nous joindre</p>
		<ul class="contact-block__list">
			<?php if ( $email ) : ?>
				<li><span>E-mail</span><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<li><span>WhatsApp</span><a href="https://wa.me/<?php echo esc_attr( $phone ); ?>" target="_blank" rel="noopener">Démarrer une conversation</a></li>
			<?php endif; ?>
			<?php if ( $insta ) : ?>
				<li><span>Instagram</span><a href="<?php echo $insta; // phpcs:ignore ?>" target="_blank" rel="noopener">Suivre la maison</a></li>
			<?php endif; ?>
			<?php echo $extra; // phpcs:ignore ?>
			<li><span>Réponse</span>Nous répondons personnellement à chaque demande, généralement sous 48 heures.</li>
		</ul>
	</div>
	<?php
	return ob_get_clean();
}

/* ---------------------------------------------------------------
 * Formulaire de contact
 * ------------------------------------------------------------- */

add_shortcode( 'jolya_contact', function ( $atts ) {
	$atts = shortcode_atts( array( 'sujet' => '' ), $atts );

	ob_start();
	?>
	<div class="contact-block" id="formulaire">
		<?php echo jolya_contact_aside(); // phpcs:ignore ?>

		<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php echo jolya_form_notice() . jolya_form_hidden( 'contact' ); // phpcs:ignore ?>

			<div class="form__row">
				<p class="form__field">
					<label for="jolya-nom">Nom et prénom</label>
					<input id="jolya-nom" type="text" name="nom" required autocomplete="name">
				</p>
				<p class="form__field">
					<label for="jolya-email">E-mail</label>
					<input id="jolya-email" type="email" name="email" required autocomplete="email">
				</p>
			</div>
			<div class="form__row">
				<p class="form__field">
					<label for="jolya-tel">Téléphone <small>(facultatif)</small></label>
					<input id="jolya-tel" type="tel" name="telephone" autocomplete="tel">
				</p>
				<p class="form__field">
					<label for="jolya-sujet">Votre demande concerne</label>
					<?php echo jolya_select( 'jolya-sujet', 'sujet', jolya_contact_subjects(), $atts['sujet'] ); // phpcs:ignore ?>
				</p>
			</div>
			<p class="form__field">
				<label for="jolya-message">Votre message</label>
				<textarea id="jolya-message" name="message" rows="6" required placeholder="Date de l'événement, modèle souhaité, taille, questions…"></textarea>
			</p>
			<p class="form__field form__field--check">
				<label><input type="checkbox" name="accord" value="1" required> J'accepte que mes informations soient utilisées pour répondre à ma demande.</label>
			</p>
			<p>
				<button class="btn btn--dark" type="submit"><span>Envoyer le message</span><span class="btn__arrow" aria-hidden="true">↗</span></button>
			</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
} );

/* ---------------------------------------------------------------
 * Formulaire de demande de location : trois étapes, choix du caftan en photo
 * ------------------------------------------------------------- */

/** Caftans proposés à la location : nom et photo. */
function jolya_location_items() {
	$items = array();
	if ( jolya_has_shop() ) {
		foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => 100, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $product ) {
			$items[] = array(
				'name'  => $product->get_name(),
				'image' => $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' ),
			);
		}
	}
	if ( ! $items ) {
		foreach ( jolya_demo_products() as $product ) {
			$items[] = array( 'name' => $product['name'], 'image' => jolya_asset( $product['images'][0] ) );
		}
	}
	return $items;
}

add_shortcode( 'jolya_location', function () {
	$wanted = isset( $_GET['caftan'] ) ? sanitize_text_field( wp_unslash( $_GET['caftan'] ) ) : ''; // phpcs:ignore
	$items  = jolya_location_items();
	$advice = 'Je souhaite être conseillée';
	$today  = current_time( 'Y-m-d' );
	$sizes  = array(
		'S (36 – 38)'    => array( 'S', '36 – 38' ),
		'M (40 – 42)'    => array( 'M', '40 – 42' ),
		'L (44 – 46)'    => array( 'L', '44 – 46' ),
		'XL (48 – 50)'   => array( 'XL', '48 – 50' ),
		'Je ne sais pas' => array( '?', 'Je ne sais pas' ),
	);

	ob_start();
	?>
	<form class="form rent" id="formulaire" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php echo jolya_form_notice() . jolya_form_hidden( 'location' ); // phpcs:ignore ?>

		<section class="rent__step">
			<h2 class="rent__title"><span class="rent__num">1</span>Choisissez votre caftan</h2>
			<div class="rent__caftans">
				<?php foreach ( $items as $i => $item ) : ?>
					<label class="rent-card">
						<input type="radio" name="caftan" value="<?php echo esc_attr( $item['name'] ); ?>" <?php checked( $wanted, $item['name'] ); ?> required>
						<span class="rent-card__media">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async" width="360" height="540">
							<span class="rent-card__check" aria-hidden="true"></span>
						</span>
						<span class="rent-card__name"><?php echo esc_html( $item['name'] ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="rent__step">
			<h2 class="rent__title"><span class="rent__num">2</span>Votre événement</h2>
			<div class="rent__grid">
				<p class="form__field">
					<label for="jolya-date">Date de l'événement</label>
					<input id="jolya-date" type="date" name="date_evenement" required min="<?php echo esc_attr( $today ); ?>">
				</p>
				<fieldset class="form__field rent__sizes">
					<legend>Votre taille</legend>
					<div class="chips">
						<?php foreach ( $sizes as $value => $label ) : ?>
							<label class="chip-choice">
								<input type="radio" name="taille" value="<?php echo esc_attr( $value ); ?>">
								<span><strong><?php echo esc_html( $label[0] ); ?></strong><small><?php echo esc_html( $label[1] ); ?></small></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</section>

		<section class="rent__step">
			<h2 class="rent__title"><span class="rent__num">3</span>Vos coordonnées</h2>
			<div class="rent__grid rent__grid--three">
				<p class="form__field">
					<label for="jolya-nom">Nom et prénom</label>
					<input id="jolya-nom" type="text" name="nom" required autocomplete="name">
				</p>
				<p class="form__field">
					<label for="jolya-tel">Téléphone</label>
					<input id="jolya-tel" type="tel" name="telephone" required autocomplete="tel">
				</p>
				<p class="form__field">
					<label for="jolya-email">E-mail</label>
					<input id="jolya-email" type="email" name="email" required autocomplete="email">
				</p>
			</div>
			<p class="form__field">
				<label for="jolya-message">Un message ? <small>(facultatif)</small></label>
				<textarea id="jolya-message" name="message" rows="3" placeholder="Questions, précisions…"></textarea>
			</p>
		</section>

		<div class="rent__send">
			<p class="rent__summary" aria-live="polite" data-rent-summary>Choisissez un caftan et une date.</p>
			<p class="form__field form__field--check">
				<label><input type="checkbox" name="accord" value="1" required> J'accepte que mes informations soient utilisées pour traiter ma demande.</label>
			</p>
			<button class="btn btn--dark rent__button" type="submit"><span>Envoyer ma demande</span><span class="btn__arrow" aria-hidden="true">↗</span></button>
			<p class="rent__note">Rien n'est payé maintenant. Nous vérifions la disponibilité, puis nous vous confirmons le tarif, la caution et le retrait.</p>
		</div>
	</form>
	<?php
	return ob_get_clean();
} );

/** Ancien nom, toujours utilisé par le traitement des demandes. */
function jolya_location_caftans() {
	return wp_list_pluck( jolya_location_items(), 'name' );
}

/* ---------------------------------------------------------------
 * Traitement des deux formulaires
 * ------------------------------------------------------------- */

function jolya_handle_contact() {
	$post = wp_unslash( $_POST ); // phpcs:ignore
	$back = isset( $post['retour'] ) ? wp_validate_redirect( esc_url_raw( $post['retour'] ), home_url( '/' ) ) : home_url( '/' );

	$valid  = isset( $post['jolya_nonce'] ) && wp_verify_nonce( sanitize_text_field( $post['jolya_nonce'] ), 'jolya_contact' );
	$is_bot = ! empty( $post['site_web'] ); // Champ piège : rempli uniquement par les robots.
	$form   = isset( $post['formulaire'] ) && 'location' === $post['formulaire'] ? 'location' : 'contact';

	$text  = function ( $key ) use ( $post ) {
		return isset( $post[ $key ] ) ? sanitize_text_field( $post[ $key ] ) : '';
	};
	$among = function ( $key, $allowed ) use ( $text ) {
		$value = $text( $key );
		return in_array( $value, $allowed, true ) ? $value : '';
	};

	$name    = $text( 'nom' );
	$email   = isset( $post['email'] ) ? sanitize_email( $post['email'] ) : '';
	$phone   = $text( 'telephone' );
	$message = isset( $post['message'] ) ? sanitize_textarea_field( $post['message'] ) : '';

	if ( 'location' === $form ) {
		$caftan   = $among( 'caftan', array_merge( jolya_location_caftans(), array( 'Je souhaite être conseillée' ) ) );
		$event    = $among( 'evenement', jolya_event_types() );
		$size     = $among( 'taille', jolya_sizes() );
		$handover = $among( 'remise', array( 'Retrait sur place', 'Livraison', 'À définir ensemble' ) );
		$date     = jolya_clean_date( isset( $post['date_evenement'] ) ? $post['date_evenement'] : '' );
		$pickup   = jolya_clean_date( isset( $post['date_retrait'] ) ? $post['date_retrait'] : '' );
		$return   = jolya_clean_date( isset( $post['date_retour'] ) ? $post['date_retour'] : '' );

		$complete = $name && is_email( $email ) && $phone && $date;
		$subject  = 'Demande de location — ' . $name . ( $date ? ' — ' . jolya_format_date( $date ) : '' );
		$body     = "DEMANDE DE LOCATION\n\n"
			. 'Caftan : ' . ( $caftan ? $caftan : '—' ) . "\n"
			. 'Taille : ' . ( $size ? $size : '—' ) . "\n\n"
			. 'Événement : ' . ( $event ? $event : '—' ) . "\n"
			. 'Date de l\'événement : ' . ( $date ? jolya_format_date( $date ) : '—' ) . "\n"
			. 'Retrait souhaité : ' . ( $pickup ? jolya_format_date( $pickup ) : '—' ) . "\n"
			. 'Retour prévu : ' . ( $return ? jolya_format_date( $return ) : '—' ) . "\n"
			. "Remise : {$handover}\n\n"
			. "Nom : {$name}\nTéléphone : {$phone}\nE-mail : {$email}\n\n"
			. ( $message ? "Précisions :\n{$message}\n" : '' );
	} else {
		$topic    = $among( 'sujet', jolya_contact_subjects() );
		$topic    = $topic ? $topic : 'Autre demande';
		$date     = '';
		$complete = $name && is_email( $email ) && $message;
		$subject  = $topic . ' — ' . $name;
		$body     = "Nom : {$name}\nE-mail : {$email}\nTéléphone : {$phone}\nSujet : {$topic}\n\n{$message}\n";
	}

	$ok = false;
	if ( $is_bot ) {
		// On répond « envoyé » sans rien enregistrer ni envoyer.
		$ok = true;
	} elseif ( $valid && $complete && ! empty( $post['accord'] ) ) {
		// 1. Enregistrement dans l'administration (menu « Demandes »).
		$saved = wp_insert_post( array(
			'post_type'    => 'jolya_demande',
			'post_status'  => 'private',
			'post_title'   => $subject,
			'post_content' => $body,
		) );
		if ( $saved && ! is_wp_error( $saved ) ) {
			update_post_meta( $saved, '_jolya_type', $form );
			update_post_meta( $saved, '_jolya_email', $email );
			if ( $date ) {
				update_post_meta( $saved, '_jolya_evenement', $date );
			}
		}

		// 2. Envoi par e-mail.
		$to = sanitize_email( jolya_opt( 'email' ) );
		if ( ! $to ) {
			$to = get_option( 'admin_email' );
		}
		$reply = 'Reply-To: ' . str_replace( array( "\r", "\n" ), '', $name ) . ' <' . $email . '>';
		$sent  = wp_mail( $to, '[JOLYA CAFTAN] ' . $subject, $body, array( $reply ) );

		// La demande est conservée même si l'e-mail ne part pas.
		$ok = ( $saved && ! is_wp_error( $saved ) ) || $sent;
	}

	// Envoi depuis le site sans recharger la page : réponse courte au navigateur.
	if ( ! empty( $post['ajax'] ) ) {
		wp_send_json( array( 'ok' => (bool) $ok ) );
	}

	// Sans JavaScript : retour sur la page du formulaire avec un message.
	wp_safe_redirect( add_query_arg( 'message', $ok ? 'envoye' : 'erreur', $back ) . '#formulaire' );
	exit;
}
add_action( 'admin_post_nopriv_jolya_contact', 'jolya_handle_contact' );
add_action( 'admin_post_jolya_contact', 'jolya_handle_contact' );
