<?php
global $nome;
global $cognome;
global $foto;
global $ID;
global $disattiva_pagina_dettaglio;
global $link_persona;
?>
<div class="card-avatar-img">
	<a href="<?php echo $link_persona;  ?>"><img src="<?php echo dsi_get_persona_avatar($foto, $ID); ?>" alt="<?php echo $foto['alt']; ?>"></a>
?>
</div><!-- /card-avatar-img -->
<div class="card-avatar-content">
<?php
if(!$disattiva_pagina_dettaglio) {
?>
	<p class="font-weight-normal"><strong class="text-underline"><u><a href="<?php echo $link_persona;  ?>"><?php echo $nome . " " . $cognome; ?></a></u></strong></p>
<?php
	$terms = get_the_terms($ID, 'struttura');
	$nome_struttura = $terms[0]->name;
?>
	<p class="font-weight-normal"><strong><?php echo $nome_struttura; ?></strong></p>
<?php
} else {
?>
	<p class="font-weight-normal"><strong><?php echo $nome . " " . $cognome; ?></strong></p>
<?php
}
?>
</div><!-- /card-avatar-content -->
