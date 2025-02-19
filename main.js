// Open pop-up modal (Sign In / Sign Up)
function openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

// Close modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Switch between Sign In and Sign Up modals
function switchModal(closeModalId, openModalId) {
    closeModal(closeModalId);
    openModal(openModalId);
}

// Close modal if clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains("modal")) {
        event.target.style.display = "none";
    }
};

function togglePassword(inputId, icon) {
    // Find the closest modal that contains the clicked icon
    let modal = icon.closest(".modal");

    // Find the password field inside this modal
    let passwordField = modal.querySelector(`#${inputId}`);

    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.src = "show.png"; // Change to show icon
        icon.alt = "show";
    } else {
        passwordField.type = "password";
        icon.src = "hide.png"; // Change back to hide icon
        icon.alt = "hide";
    }
}



// Ensure the script runs after the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Open login modal if signup is successful
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("signup") === "success") {
        openModal('loginModal');
    }

    // Form validation for login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            let isValid = true;

            // Validate username1
            const username1 = document.querySelector('input[name="username1"]');
            const username2 = document.querySelector('input[name="username2"]');
            const password1 = document.querySelector('input[name="password1"]');
            const password2 = document.querySelector('input[name="password2"]');

            // Check username1
            if (username1.value.length < 8 || username1.value.length > 16) {
                alert('Username 1 must be between 8 and 16 characters.');
                isValid = false;
            }

            // Check username2
            if (username2.value.length < 8 || username2.value.length > 16) {
                alert('Username 2 must be between 8 and 16 characters.');
                isValid = false;
            }

            // Check password1
            if (password1.value.length < 8 || password1.value.length > 16) {
                alert('Password 1 must be between 8 and 16 characters.');
                isValid = false;
            }

            // Check password2
            if (password2.value.length < 8 || password2.value.length > 16) {
                alert('Password 2 must be between 8 and 16 characters.');
                isValid = false;
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Function to restrict input to alphanumeric characters
    function restrictInput(event) {
        const char = event.key; // Use event.key instead of String.fromCharCode(event.which)
        // Allow only alphanumeric characters
        if (!/[a-zA-Z0-9]/.test(char) && char !== 'Backspace' && char !== 'Tab') {
            event.preventDefault(); // Prevent the character from being entered
        }
    }

    // Attach the restrictInput function to all text and password fields
    document.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
        input.addEventListener('keypress', restrictInput);
    });

    // Function to toggle the search bar visibility
    function toggleSearch() {
        const searchContainer = document.getElementById('search-container');
        searchContainer.classList.toggle('hidden'); // Toggle the hidden class
        const searchBox = document.getElementById('searchBox');
        if (!searchContainer.classList.contains('hidden')) {
            searchBox.focus(); // Focus on the search box when it is shown
        }
    }

    // Attach the toggleSearch function to the search link
    const searchLink = document.querySelector('a[onclick="toggleSearch()"]');
    if (searchLink) {
        searchLink.addEventListener('click', toggleSearch);
    }

    // Attach the restrictInput function to the search box
    const searchBox = document.getElementById('searchBox');
    if (searchBox) {
        searchBox.addEventListener('keypress', restrictInput);
    }
});