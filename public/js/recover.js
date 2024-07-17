document.addEventListener('DOMContentLoaded', function () {
    function togglePasswordVisibility(passwordInput, eyeIcon) {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.src = '/images/eye-open.png';
        } else {
            passwordInput.type = "password";
            eyeIcon.src = '/images/eye-closed.png';
        }
    }

    let passwordEyeIcon = document.getElementById('password-eyeicon');
    let password = document.getElementById('password');

    if (passwordEyeIcon && password) {
        passwordEyeIcon.addEventListener('click', function () {
            togglePasswordVisibility(password, passwordEyeIcon);
        });
    }

    let confirmPasswordEyeIcon = document.getElementById('confirm-password-eyeicon');
    let confirmPassword = document.getElementById('password-confirm');

    if (confirmPasswordEyeIcon && confirmPassword) {
        confirmPasswordEyeIcon.addEventListener('click', function () {
            togglePasswordVisibility(confirmPassword, confirmPasswordEyeIcon);
        });
    }
});
