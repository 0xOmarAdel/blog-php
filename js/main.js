function validate_form(form){
    var elements = form.elements;
    for (var i = 0, element; element = elements[i++];) {
        if (element.type !== "submit" && element.value === ""){
            return false;
        }
    }  
}