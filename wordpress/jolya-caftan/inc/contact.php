<?php
/**
 * Formulaire de contact : [jolya_contact] ou [jolya_contact sujet="Sur mesure"].
 * Les messages sont envoyés par e-mail à l'adresse réglée dans Personnaliser → Contact.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jolya_contact_subjects() {
	return array( 'Un modèle de la collection', 'Sur mesure', 'Ma commande', 'Autre demande' );
}

add_shortcode( 'jolya_contact', function ( $atts ) {
	$atts    = shortcode_atts( array( 'sujet' => '' ), $atts );
	$status  = isset( $_GET['message'] ) ? sanitize_key( wp_unslash( $_GET['message'] ) ) : ''; // phpcs:ignore
	$email   = sanitize_email( jolya_opt( 'email' ) );
	$phone   = preg_replace( '/\D+/', '', (string) jolya_opt( 'whatsapp' ) );
	$insta   = esc_url( jolya_opt( 'instagram' ) );
	$current = get_permalink();

	ob_start();
	?>
	<div class="contact-block" id="formulaire">
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
				<li><span>Réponse</span>Nous répondons personnellement à chaque message, généralement sous 48 heures.</li>
			</ul>
		</div>

		<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php if ( 'envoye' === $status ) : ?>
				<p class="form__notice form__notice--ok" role="status">Merci, votre message a bien été envoyé. Nous revenons vers vous très vite.</p>
			<?php elseif ( 'erreur' === $status ) : ?>
				<p class="form__notice form__notice--error" role="alert">Le message n'a pas pu être envoyé. Vérifiez les champs ou écrivez-nous directement par e-mail.</p>
			<?php endif; ?>

			<input type="hidden" name="action" value="jolya_contact">
			<input type="hidden" name="retour" value="<?php echo esc_url( $current ); ?>">
			<?php wp_nonce_field( 'jolya_contact', 'jolya_nonce' ); ?>
			<p class="form__trap" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></p>

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
					<select id="jolya-sujet" name="sujet">
						<?php foreach ( jolya_contact_subjects() as $subject ) : ?>
							<option value="<?php echo esc_attr( $subject ); ?>" <?php selected( $atts['sujet'], $subject ); ?>><?php echo esc_html( $subject ); ?></option>
						<?php endforeach; ?>
					</select>
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

function jolya_handle_contact() {
	$back = isset( $_POST['retour'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['retour'] ) ), home_url( '/' ) ) : home_url( '/' ); // phpcs:ignore

	$valid = isset( $_POST['jolya_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jolya_nonce'] ) ), 'jolya_contact' );
	// Champ piège : rempli uniquement par les robots.
	$is_bot = ! empty( $_POST['site_web'] );

	$name    = isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['telephone'] ) ? sanitize_text_field( wp_unslash( $_POST['telephone'] ) ) : '';
	$subject = isset( $_POST['sujet'] ) ? sanitize_text_field( wp_unslash( $_POST['sujet'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! in_array( $subject, jolya_contact_subjects(), true ) ) {
		$subject = 'Autre demande';
	}

	$ok = false;
	if ( $is_bot ) {
		// On répond « envoyé » sans rien envoyer.
		$ok = true;
	} elseif ( $valid && $name && is_email( $email ) && $message && ! empty( $_POST['accord'] ) ) {
		$to = sanitize_email( jolya_opt( 'email' ) );
		if ( ! $to ) {
			$to = get_option( 'admin_email' );
		}
		$body  = "Nom : {$name}\nE-mail : {$email}\nTéléphone : {$phone}\nSujet : {$subject}\n\n{$message}\n";
		$reply = 'Reply-To: ' . str_replace( array( "\r", "\n" ), '', $name ) . ' <' . $email . '>';
		$ok    = wp_mail( $to, '[JOLYA CAFTAN] ' . $subject . ' — ' . $name, $body, array( $reply ) );
	}

	wp_safe_redirect( add_query_arg( 'message', $ok ? 'envoye' : 'erreur', $back ) . '#formulaire' );
	exit;
}
add_action( 'admin_post_nopriv_jolya_contact', 'jolya_handle_contact' );
add_action( 'admin_post_jolya_contact', 'jolya_handle_contact' );
