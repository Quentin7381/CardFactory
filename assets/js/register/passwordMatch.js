

function validatePassword() {
    let pwdMessage = document.getElementById("passwordMatchMessage");
    if (!pwdMessage) {
        pwdMessage = document.createElement("ul");
        pwdMessage.id = "passwordMatchMessage";

        let child = document.createElement("li");
        child.textContent = "Passwords do not match";

        pwdMessage.appendChild(child);

        let container = document.getElementById("registration_form_passwordConfirm").parentNode;
        let input = document.getElementById("registration_form_passwordConfirm");
        container.insertBefore(pwdMessage, input);
    }

    let pass = document.getElementById("registration_form_plainPassword").value;
    let confirmPass = document.getElementById("registration_form_passwordConfirm").value;

    if(!pass || !confirmPass) {
        pwdMessage.style.display = "none";
    } else if (pass === confirmPass) {
        pwdMessage.style.display = "none";
    } else {
        pwdMessage.style.display = "block";
    }
}

function watchPassword() {
    let pass = document.getElementById("registration_form_plainPassword");
    let confirmPass = document.getElementById("registration_form_passwordConfirm");

    pass.addEventListener("input", validatePassword);
    confirmPass.addEventListener("input", validatePassword);
}

// On dom load
document.addEventListener("DOMContentLoaded", function() {
    watchPassword();
});
