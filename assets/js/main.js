function resetForm($idForm, $idReset) {
	console.log("CIAO CIAO");
	var form = document.getElementById($idForm);
	var reset = document.getElementById($idReset);
	reset.value = "yes";
	form.submit();
}