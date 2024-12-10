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

	// Togliere la parte del path che mostra pagine diverse dalla prima,
	// cioè mostra sempre la prima pagina della paginazione.
	const newPath = url.pathname.replace(/\/page\/\d+\//, "/");
	url.pathname = newPath; 

	// Aggiungi o aggiorna il parametro $par_name con il valore $par_value.
	params.set($par_name, $par_value);

	// Ricarica la pagina con il nuovo URL
	window.location.href = url.toString();
}