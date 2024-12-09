function formatImporto(value, len) {

    //    if (!/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value) )value=0;
    decSeparator = '.';
    curSeparator = '';
    if (value == ""){
        return value;
    }
    value = formatImportoBack(value);
    if (isNaN(value)) {
        return("");
    }
    var tmp = value;

    if (decSeparator == ',') {
        var idx = tmp.indexOf('.');
        if (idx > 0) {
            tmp = tmp.substring(0, idx) + ',' + tmp.substring(idx + 1);
        }
    }
    var sgn = false;
    if (tmp.substring(0, 1) == '-') {
        sgn = true;
        tmp = tmp.substring(1);
    }
    var arr = tmp.split(decSeparator);

    var intPart = arr[0];
    var len = intPart.length;
    var rem = len % 3;

    var result = "";
    for (i = len - 3; i > 0; i -= 3)
        result = curSeparator + intPart.substr(i, 3) + result;
    result = intPart.substring(0, rem == 0 ? 3 : rem) + result;

    if (sgn)
        result = "-" + result;

    result += decSeparator;

    len = 0;
    if (arr.length > 1) {
        result += arr[1];
        len = arr[1].length;
    }

    for (i = len; i < 2; i++) {
        result += '0';
    }

    return result;

}

function formatImportoBack(value) {

    if (value == "")
        return value;

    value = value.replace(',', '.');
    return value;

}

function formattaimporto(id) {
    newval = formatImporto(jQuery("#" + id).val(), 15);
    jQuery("#" + id).val(newval);
}

jQuery(document).ready(function(){

    if (jQuery('#ammap_importo_previsto').length > 0){
        jQuery('#ammap_importo_previsto').change(function(){
            formattaimporto('ammap_importo_previsto');
        });
    }

    if (jQuery('#ammap_importo').length > 0){
        jQuery('#ammap_importo').change(function(){
            formattaimporto('ammap_importo');
        });
    }

})