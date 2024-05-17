/*// Get the password input fields and eye icons
let passwordInput = document.getElementById('password');
let passwordConfirmInput = document.getElementById('password-confirm');
let eyeIcons = document.querySelectorAll('.eye-icon');

// Add event listener to each eye icon
eyeIcons.forEach(icon => {
    icon.addEventListener('click', () => {
        // Toggle the type attribute of the password input fields
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordConfirmInput.type = 'text';
            // Update the eye icon source to open eye
            icon.src = 'images/eye-open.png';
        } else {
            passwordInput.type = 'password';
            passwordConfirmInput.type = 'password';
            // Update the eye icon source to closed eye
            icon.src = 'images/eye-closed.png';
        }
    });
});*/


/*
document.addEventListener('DOMContentLoaded', function () {
    let eyeicon = document.getElementById('eyeicon');
    let passwordInput = document.getElementById('password');
    let passwordConfirmInput = document.getElementById('password-confirm');

    eyeicon.onclick = function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordConfirmInput.type = "text";
            eyeicon.src = 'images/eye-open.png';
        } else {
            passwordInput.type = "password";
            passwordConfirmInput.type = "password";
            eyeicon.src = 'images/eye-closed.png';
        }
    };
});
*/



/*
    // Get the password input fields and eye icons
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password-confirm');
    const eyeIcons = document.querySelectorAll('.eye-icon');

    // Add event listener to each eye icon
    eyeIcons.forEach(icon => {
    icon.addEventListener('click', () => {
        // Toggle the type attribute of the password input fields
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordConfirmInput.type = 'text';
            // Update the eye icon source to open eye
            icon.src = "{{ asset('images/eye-open.png') }}";
        } else {
            passwordInput.type = 'password';
            passwordConfirmInput.type = 'password';
            // Update the eye icon source to closed eye
            icon.src = "{{ asset('images/eye-closed.png') }}";
        }
    });
});
*/


document.addEventListener('DOMContentLoaded', function () {
    // Função para alternar a visibilidade do campo de senha
    function togglePasswordVisibility(passwordInput, eyeIcon) {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.src = 'images/eye-open.png';
        } else {
            passwordInput.type = "password";
            eyeIcon.src = 'images/eye-closed.png';
        }
    }

    let passwordEyeIcon = document.getElementById('password-eyeicon');
    let password = document.getElementById('password');

    passwordEyeIcon.onclick = function () {
        togglePasswordVisibility(password, passwordEyeIcon);
    };

    let confirmPasswordEyeIcon = document.getElementById('confirm-password-eyeicon');
    let confirmPassword = document.getElementById('password-confirm');

    confirmPasswordEyeIcon.onclick = function () {
        togglePasswordVisibility(confirmPassword, confirmPasswordEyeIcon);
    };
});



