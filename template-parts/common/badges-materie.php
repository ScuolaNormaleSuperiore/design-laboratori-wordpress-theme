<?php
global $badgeclass, $materia;
if(!isset($badgeclass))
	$badgeclass = "badge-outline-purplelight";
$argomenti = dsi_get_materie_of_post();
if(is_array($argomenti) && count($argomenti)) {
	?>
	<aside class="badges-wrapper badges-main">
	<h4><?php _e("Materia", 'design_laboratori_italia'); ?></h4>
	<div class="badges">
		<?php foreach ( $argomenti as $materia ) {
			?>
			<?php
			get_template_part( "template-parts/materia/card" );

			?>
			<?php
        } ?>
	</div><!-- /badges -->
	</aside><!-- /badges-wrapper -->
	<?php
}