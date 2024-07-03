function validate() {
    const titleInput = document.getElementById("title");
    const authorInput = document.getElementById("author");
    const genreInput = document.getElementById("genre");
    const ratingInput = document.getElementById("rating");
    const reviewInput = document.getElementById("review");

    var titleValue = titleInput.value.trim();
    var authorValue = authorInput.value.trim();
    var genreValue = genreInput.value.trim();
    var ratingValue = ratingInput.value.trim();
    var reviewValue = reviewInput.value.trim();

    var titleError=document.getElementById("titleError");
    var authorError=document.getElementById("authorError");
    var genreError=document.getElementById("genreError");
    var ratingError=document.getElementById("ratingError");
    var reviewError=document.getElementById("reviewError");
    let isValidate=false;

    if (titleValue === "") {
       titleError.innerHTML="Title is required!";
       titleInput.classList.add('error-border');
       isValidate=true;
    }else{
        titleError.innerHTML="";
        titleInput.classList.remove('error-border');
    }

    if (authorValue === "") {
        authorError.innerHTML = "Author is required!";
        authorInput.classList.add('error-border');
        isValidate = true;
    } else {
        authorError.innerHTML = "";
        authorInput.classList.remove('error-border');
    }

    if (genreValue === "") {
         genreError.innerHTML = "Genre is required!";
         genreInput.classList.add('error-border');
         isValidate = true;
    } else {
        genreError.innerHTML = "";
         genreInput.classList.remove('error-border');
    }

    if (ratingValue === "") {
        ratingError.innerHTML = "Rating is required!";
        ratingInput.classList.add('error-border');
        isValidate = true;
    } else if (isNaN(ratingValue) || ratingValue < 0 || ratingValue > 5) {
         ratingError.innerHTML = "Rating must be between 0 and 5!";
         ratingInput.classList.add('error-border');
         isValidate = true;
    } else {
        ratingError.innerHTML = "";
        ratingInput.classList.remove('error-border');
    }

    if (reviewValue === "") {
        reviewError.innerHTML = "Review is required!";
        reviewInput.classList.add('error-border');
        isValidate = true;
    } else {
        reviewError.innerHTML = "";
        reviewInput.classList.remove('error-border');
    }
    return !isValidate;
}
//Validates user registration form fields like email, username, password.
function validateSignup() {
    // Input elements
    const emailInput = document.getElementById("email");
    const newUsernameInput = document.getElementById("newUsername");
    const phoneInput = document.getElementById("phone");
    const newPasswordInput = document.getElementById("newPassword");

    // Values from inputs
    var emailValue = emailInput.value.trim();
    var newUsernameValue = newUsernameInput.value.trim();
    var phoneValue = phoneInput.value.trim();
    var newPasswordValue = newPasswordInput.value.trim();

    // Error display elements
    var emailError = document.getElementById("emailError");
    var newUsernameError = document.getElementById("newUsernameError");
    var phoneError = document.getElementById("phoneError");
    var newPasswordError = document.getElementById("newPasswordError");

    // Validation flag
    let isInvalid = false;

    // Email validation
    if (emailValue === "") {
        emailError.innerHTML = "Email is required!";
        emailInput.classList.add('error-border');
        isInvalid = true;
    } else if (!validateEmail(emailValue)) { // Assuming validateEmail function is defined
        emailError.innerHTML = "Email address should be in the format xyx@xyz.xyz.";
        emailInput.classList.add('error-border');
        isInvalid = true;
    } else {
        emailError.innerHTML = "";
        emailInput.classList.remove('error-border');
    }

    // New username validation
    if (newUsernameValue === "" || newUsernameValue.length < 4 || newUsernameValue.length > 10) {
        newUsernameError.innerHTML = "New username is required and should be between 4 and 10 characters long!";
        newUsernameInput.classList.add('error-border');
        isInvalid = true;
    } else {
        newUsernameError.innerHTML = "";
        newUsernameInput.classList.remove('error-border');
    }

    // PhoneNumber validation
    if (phoneValue === "") {
        phoneError.innerHTML = "Phone Number is required!";
        phoneInput.classList.add('error-border');
        isInvalid = true;
    }
    else {
        phoneError.innerHTML = "";
        phoneInput.classList.remove('error-border');
    }

    // New password validation
    if (newPasswordValue === "" || newPasswordValue.length < 4 || newPasswordValue.length > 10) {
        newPasswordError.innerHTML = "Password is required and should be between 4 and 10 characters long!";
        newPasswordInput.classList.add('error-border');
        isInvalid = true;
    } else {
        newPasswordError.innerHTML = "";
        newPasswordInput.classList.remove('error-border');
    }

    return !isInvalid;
}

// validateEmail function :Checks if the email format is correct.
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}


// Clears validation error messages dynamically when the user starts typing in an input field.
function clearErrorOnChange(errorElement, inputElement) {
    errorElement.innerHTML = "";
    inputElement.classList.remove('error-border');
  }

  window.onload = function() {
    const titleInput = document.getElementById("title");
    const authorInput = document.getElementById("author");
    const genreInput = document.getElementById("genre");
    const ratingInput = document.getElementById("rating");
    const reviewInput = document.getElementById("review");



    var titleError=document.getElementById("titleError");
    var authorError=document.getElementById("authorError");
    var genreError=document.getElementById("genreError");
    var ratingError=document.getElementById("ratingError");
    var reviewError=document.getElementById("reviewError");


    titleInput.addEventListener("input", clearErrorOnChange.bind(null, titleError, titleInput));
    authorInput.addEventListener("input", clearErrorOnChange.bind(null, authorError, authorInput));
    genreInput.addEventListener("input", clearErrorOnChange.bind(null, genreError, genreInput));
    ratingInput.addEventListener("input", clearErrorOnChange.bind(null, ratingError, ratingInput));
    reviewInput.addEventListener("input", clearErrorOnChange.bind(null, reviewError, reviewInput));

  }


//  preview Cover image and manages the preview of uploaded image files, specifically for book cover images.
function previewImage() {
    var preview = document.getElementById('cover-preview');
    var file = document.getElementById('cover').files[0];
    var reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result; // Set the new image as the source for preview
    };

    if (file) {
        reader.readAsDataURL(file); // Read the new image file
    }
}

// Function to clear error messages for a specific field.Event listeners
// are added to handle dynamic error message clearance and form field validation.
document.addEventListener('DOMContentLoaded', function() {
    var userEmailInput = document.getElementById('userEmail');
    var passwordInput = document.getElementById('password');
    var loginErrorDiv = document.getElementById('loginErrorDiv');

    var emailInput = document.getElementById('email');
    var newUsernameInput = document.getElementById('newUsername');
    var newPasswordInput = document.getElementById('newPassword');

    // Function to clear Login Error
    function clearLoginError() {
        if (loginErrorDiv) {
            loginErrorDiv.textContent = '';
        }
    }

    function clearSignupError() {
        if (signUpErrorDiv) {
            signUpErrorDiv.textContent = '';
        }
    }

    if (userEmailInput) {
        userEmailInput.addEventListener('input', clearLoginError);
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', clearLoginError);
    }


    if (emailInput) {
        emailInput.addEventListener('input', clearLoginError);
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', clearLoginError);
    }

    if (newUsernameInput) {
        newUsernameInput.addEventListener('input', clearLoginError);
    }

    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', clearLoginError);
    }

    // Function to clear error message
    function clearErrorMessage(inputElement, errorElementId) {
        var errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            errorElement.textContent = '';
        }
        inputElement.classList.remove('error-border');
    }

    // Attach event listeners to input fields
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            clearErrorMessage(emailInput, 'emailError');
        });
    }

    if (newUsernameInput) {
        newUsernameInput.addEventListener('input', function() {
            clearErrorMessage(newUsernameInput, 'newUsernameError');
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            clearErrorMessage(phoneInput, 'phoneError');
        });
    }

    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            clearErrorMessage(newPasswordInput, 'newPasswordError');
        });
    }
});