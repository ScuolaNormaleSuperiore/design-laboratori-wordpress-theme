<?php
global $autore;
global $link_persona;
?>
<div class="card-avatar-img">
	<a href="<?php echo $link_persona;  ?>"><img src="<?php echo dsi_get_persona_avatar($foto, $ID); ?>" alt="<?php echo $foto['alt']; ?>"></a>
</div><!-- /card-avatar-img -->
<div class="card-avatar-content">
	<p class="font-weight-normal"><strong class="text-underline"><u><a href="<?php echo get_author_posts_url( $autore->ID);  ?>"><?php echo dsi_get_display_name($autore->ID); ?></a></u></strong></p>
	<p class="font-weight-normal"><strong class="text-underline"><u><a href="<?php echo $link_persona;  ?>"><?php echo $nome . " " . $cognome; ?></a></u></strong></p>
</div><!-- /card-avatar-content -->
