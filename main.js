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

// Password hide / show
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

// Flash message
document.addEventListener("DOMContentLoaded", function () {
    let toast = document.getElementById("toast");
    let toastMessage = toast.getAttribute("data-message");
    let toastType = toast.getAttribute("data-type");

    if (toastMessage) {
        toast.classList.add(toastType); // Apply success/error styling
        toast.innerText = toastMessage;
        toast.style.display = "block";

        setTimeout(() => {
            toast.style.display = "none";
        }, 3000);
    }
});


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
//active nav-menu when clicked
document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".menu-sidebar a");

    links.forEach(link => {
        link.addEventListener("click", function () {
            // Remove "active" from all links
            links.forEach(l => l.classList.remove("active"));
            // Add "active" class to the clicked link
            this.classList.add("active");
            // Store active page in localStorage
            localStorage.setItem("activeNav", this.getAttribute("href"));
        });
    });

    // Maintain active state after page reload
    const activePage = localStorage.getItem("activeNav");
    if (activePage) {
        links.forEach(link => {
            if (link.getAttribute("href") === activePage) {
                link.classList.add("active");
            }
        });
    }
});
//page transition effect
document.addEventListener("DOMContentLoaded", function() {
    document.body.classList.add("active");

    const links = document.querySelectorAll(".menu-sidebar a");
    links.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault(); 
            const target = this.getAttribute("href");
            document.body.classList.remove("active");
            setTimeout(() => {
                window.location.href = target;
            }, 500); // Delay matches transition time
        });
    });
});
//fade page transition
document.addEventListener("DOMContentLoaded", function () {
    document.body.classList.add("fade", "active");

    const links = document.querySelectorAll(".menu-sidebar a");
    links.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault(); 
            const target = this.getAttribute("href");

            document.body.classList.remove("active");

            setTimeout(() => {
                window.location.href = target;
            }, 500);
        });
    });
});
//animation slide
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('donationChart').getContext('2d');

    const disasterChart = new Chart(ctx, {
        type: 'bar', // Bar chart for disasters
        data: {
            labels: ['Fire', 'Storm', 'Flood', 'Earthquake', 'Tornado', 'Landslide'], // Disaster categories
            datasets: [{
                label: 'Number of Incidents',
                data: [5, 100, 3, 1, 6, 10], // Replace with real data
                backgroundColor: ['#ff5733', '#3498db', '#2980b9', '#e74c3c', '#f39c12', '#27ae60'],
                borderColor: '#333',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top', // Move legend to the top
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 10
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
