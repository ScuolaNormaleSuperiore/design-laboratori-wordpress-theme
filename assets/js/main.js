function resetForm($idForm, $idReset) {
	var form = document.getElementById($idForm);
	var reset = document.getElementById($idReset);
	reset.value = "yes";
	form.submit();
}


function addParameterAndReloadPage($par_name, $par_value) {
	const url = new URL(window.location.href);
	const params = url.searchParams;
	const newPath = url.pathname.replace(/\/page\/\d+\//, '/page/1/');
	url.pathname = newPath;

	if ($par_value) {
		params.set($par_name, $par_value);
	} else {
		params.delete($par_name);
	}

	window.location.href = url.toString();
}


function reloadWithSelectedItem( $id_item, $par_name) {
	const $par_value = document.getElementById($id_item).value.trim();
	addParameterAndReloadPage($par_name, $par_value);
}