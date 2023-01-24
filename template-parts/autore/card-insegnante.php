<?php
global $nome;
global $cognome;
global $foto;
global $ID;
?>
<div class="card-avatar-img">
	<a href="<?php echo get_author_posts_url( $autore->ID);  ?>"><img src="<?php echo dsi_get_persona_avatar($foto, $ID); ?>" alt="<?php echo $foto['alt']; ?>"></a>
</div><!-- /card-avatar-img -->
<div class="card-avatar-content">
	<p class="font-weight-normal"><strong class="text-underline"><u><a href="<?php echo get_author_posts_url( $autore->ID);  ?>"><?php echo $nome . " " . $cognome; ?></a></u></strong></p>
</div><!-- /card-avatar-content -->
