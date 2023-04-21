<!-- PRESENTAZIONE -->
<?php
$testo_sezione_laboratorio = dli_get_configuration_field_by_lang( 'descrizione_laboratorio', 'la_scuola' );
$etichetta_laboratorio     = dli_get_configuration_field_by_lang( 'etichetta', 'la_scuola' );

if ( $testo_sezione_laboratorio ) {
	?>
<section id="presentazione" aria-describedby="Presentazione del laboratorio" class="section section-muted pt-5">
	<div>
		<div class="container my-12">
			<h2 class="h3 pb-1"><?php echo $etichetta_laboratorio; ?></h2>
			<p>
				<?php
					echo $testo_sezione_laboratorio;
				?>
			</p>
		</div>
	</div>
</section>
<?php
}
?>
