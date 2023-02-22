<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = count( $items );
	define( 'ITEMS_PER_ROW', 3 );
?>

<section id="allegati">    
<div class="row pb-3">
<div class="card-wrapper card-teaser-wrapper">
<!--start card-->
<div class="card card-teaser rounded shadow ">
<div class="card-body">
<h3 class="card-title h5 ">
<svg class="icon">
<use href="bootstrap-italia/svg/sprites.svg#it-file-pdf"></use>
</svg>
<a href="#">Curriculum vitae&nbsp;</a>
</h3>
</div>
</div>
<!--end card-->
<!--start card-->
<div class="card card-teaser rounded shadow ">
<div class="card-body">
<h3 class="card-title h5 ">
<svg class="icon">
<use href="bootstrap-italia/svg/sprites.svg#it-file-pdf"></use>
</svg>
<a href="#">Elenco pubblicazioni PDF</a>
</h3>
</div>
</div>
<!--end card-->
</div>

</div>
</section>