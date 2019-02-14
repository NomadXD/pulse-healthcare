'use strict';

/// Validate forms in the page
$(document).ready(function () {
    let forms = $('.needs-validation');

    Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            let phsrcRegex = /^PHSRC\/[A-Z]+\/[0-9]+$/;
            let emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            let faxRegex = /^\+?[0-9]+$/;


            function checkField(field, conditions) {
                field.removeClass("is-valid");
                field.removeClass("is-invalid");
                if (conditions) {
                    field.addClass("is-valid");
                    return true;
                } else {
                    field.addClass("is-invalid");
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            let name = $("#name");
            let phsrc = $("#phsrc");
            let email = $("#email");
            let fax = $("#fax");
            let phonenumber = $("#phonenumber");
            let address = $("#address");
            let postal = $("#postal");

            let nameValid = checkField(name, name.val() !== "");
            let phsrcValid = checkField(phsrc, phsrc.val() !== "" &&
                phsrcRegex.test(phsrc.val()));
            let emailValid = checkField(email, email.val() !== "" &&
                emailRegex.test(email.val()));
            let faxValid = checkField(fax, fax.val() === "" ||
                faxRegex.test(fax.val()));
            let phoneNumberValid = checkField(phonenumber, phonenumber.val() !== "");
            let addressValid = checkField(address, address.val() !== "");
            let postalValid = checkField(postal, postal.val() !== "");

            if(nameValid && phsrcValid && emailValid && faxValid && phoneNumberValid && addressValid && postalValid){
                form.submit();
            }
        }, false);
    });
});