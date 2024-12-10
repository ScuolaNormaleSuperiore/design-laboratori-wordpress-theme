function resetForm($idForm, $idReset) {
	var form = document.getElementById($idForm);
	var reset = document.getElementById($idReset);
	reset.value = "yes";
	form.submit();
}


function addParameterAndReloadPage($par_name, $par_value) {
	// Ottieni i parametri attuali dell'URL.
	const url = new URL(window.location.href);
	const params = url.searchParams;

	// Usa una regex per trovare "page/{numero}/" nel pathname e sostituirlo con "page/1/"
	// cioè mostra sempre la prima pagina della paginazione.
	const newPath = url.pathname.replace(/\/page\/\d+\//, '/page/1/');
	url.pathname = newPath; 

	// Aggiungi o aggiorna il parametro $par_name con il valore $par_value.
	params.set($par_name, $par_value);

	// Ricarica la pagina con il nuovo URL
	window.location.href = url.toString();
}


function reloadWithSelectedItem( $id_item, $par_name) {
	// Recupera il valore selezionato nella select con id=$id_item.
	const $par_value = document.getElementById($id_item).value;
	// Ricarica la pagina aggiungendo il parametro $par_name con $par_value.
	if ($par_value) {
		addParameterAndReloadPage($par_name, $par_value);
	}
}