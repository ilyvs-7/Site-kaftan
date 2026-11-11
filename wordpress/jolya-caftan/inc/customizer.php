<?php
/**
 * Réglages modifiables dans Apparence → Personnaliser.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jolya_defaults() {
	return array(
		'hero_eyebrow'  => 'Maison de caftans — Collection Signature',
		'hero_title'    => "L'élégance",
		'hero_title_2'  => 'intemporelle.',
		'hero_text'     => "Des caftans d'exception, brodés à la main, pour sublimer les instants précieux.",
		'manifesto'     => "Chaque caftan JOLYA est une pièce rare : des étoffes nobles, des broderies d'or façonnées à la main et une allure pensée pour traverser le temps.",
		'story_title'   => "Bien plus qu'une tenue.",
		'story_title_2' => 'Une présence.',
		'story_text'    => "Le caftan accompagne les moments qui comptent. JOLYA CAFTAN célèbre cette élégance à travers des créations raffinées, où le sens du détail rencontre une allure contemporaine, dans le respect d'un savoir-faire transmis de génération en génération.",
		'contact_title' => 'Une occasion particulière ?',
		'contact_text'  => 'Mariage, fiançailles, cérémonie : confiez-nous vos envies, nous vous guidons vers la pièce idéale ou la réalisons sur mesure.',
		'email'         => 'bonjour@jolyacaftan.fr',
		'whatsapp'      => '',
		'instagram'     => '',
		'announcement'  => 'Livraison offerte en France · Confection sur mesure sur demande',
	);
}

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_panel( 'jolya', array( 'title' => 'JOLYA — Contenu du site', 'priority' => 20 ) );

	$sections = array(
		'jolya_hero'    => array( 'Accueil — grande image', array(
			'hero_image'        => array( 'Photo (ordinateur, format large)', 'image' ),
			'hero_image_mobile' => array( 'Photo (téléphone, format vertical)', 'image' ),
			'hero_eyebrow'      => array( 'Petit texte au-dessus du titre', 'text' ),
			'hero_title'        => array( 'Titre — ligne 1', 'text' ),
			'hero_title_2'      => array( 'Titre — ligne 2 (italique)', 'text' ),
			'hero_text'         => array( 'Phrase sous le titre', 'textarea' ),
			'announcement'      => array( 'Bandeau en haut du site (vide = masqué)', 'text' ),
		) ),
		'jolya_story'   => array( 'La maison', array(
			'manifesto'     => array( "Phrase d'introduction", 'textarea' ),
			'story_image'   => array( 'Photo de la maison', 'image' ),
			'story_title'   => array( 'Titre — ligne 1', 'text' ),
			'story_title_2' => array( 'Titre — ligne 2 (italique)', 'text' ),
			'story_text'    => array( 'Texte', 'textarea' ),
		) ),
		'jolya_contact' => array( 'Contact', array(
			'contact_title' => array( 'Titre', 'text' ),
			'contact_text'  => array( 'Texte', 'textarea' ),
			'email'         => array( 'Adresse e-mail', 'text' ),
			'whatsapp'      => array( 'Numéro WhatsApp (ex. 33612345678)', 'text' ),
			'instagram'     => array( 'Lien Instagram (https://…)', 'url' ),
		) ),
	);

	$defaults = jolya_defaults();

	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section( $section_id, array( 'title' => $section[0], 'panel' => 'jolya' ) );

		foreach ( $section[1] as $key => $field ) {
			$id = 'jolya_' . $key;

			if ( 'image' === $field[1] ) {
				$wp_customize->add_setting( $id, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array(
					'label'     => $field[0],
					'section'   => $section_id,
					'mime_type' => 'image',
				) ) );
				continue;
			}

			$sanitize = 'url' === $field[1] ? 'esc_url_raw' : ( 'textarea' === $field[1] ? 'sanitize_textarea_field' : 'sanitize_text_field' );
			$wp_customize->add_setting( $id, array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
			) );
			$wp_customize->add_control( $id, array( 'label' => $field[0], 'section' => $section_id, 'type' => $field[1] ) );
		}
	}
} );
