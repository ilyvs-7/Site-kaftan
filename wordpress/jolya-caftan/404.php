<?php
get_header();
?>
<div class="page page--plain page--center">
	<p class="eyebrow">Erreur 404</p>
	<h1 class="page__title">Cette page <em>s'est éclipsée.</em></h1>
	<p>La page que vous cherchez n'existe pas ou a été déplacée.</p>
	<p><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span>Retour à l'accueil</span><span class="btn__arrow" aria-hidden="true">↗</span></a></p>
</div>
<?php
get_footer();
