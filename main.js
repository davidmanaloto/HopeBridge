    //toast
    document.addEventListener("DOMContentLoaded", function () {
        let toast = document.getElementById("toast");
        if (toast) {
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
        }
    });

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

// Active nav-menu when clicked
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
    
    // Page transition effect
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
    
    // Fade page transition
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
    
    // Button for Slides
    document.addEventListener("DOMContentLoaded", function () {
        const iconCards = document.querySelector('.icon-cards');
        const cards = Array.from(document.querySelectorAll('.icon-card'));
        const totalCards = cards.length; // Total number of unique cards
        let currentIndex = 0; // Track the current index of the first visible card
    
        // Bar Chart for Monthly Disaster Counts
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], // Months
                datasets: [
                    {
                        label: 'Fire',
                        data: [25, 20, 15, 30, 40, 35, 50, 45, 30, 20, 15, 10], // Monthly data for Fire
                        backgroundColor: '#ff5733', // Color for Fire
                    },
                    {
                        label: 'Storm',
                        data: [1, 0, 0, 2, 3, 4, 5, 6, 2, 3, 1, 0], // Monthly data for Storm
                        backgroundColor: '#3498db', // Color for Storm
                    },
                    {
                        label: 'Flood',
                        data: [5, 3, 4, 6, 10, 8, 12, 15, 7, 5, 4, 2], // Monthly data for Flood
                        backgroundColor: '#2980b9', // Color for Flood
                    },
                    {
                        label: 'Earthquake',
                        data: [0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0], // Monthly data for Earthquake
                        backgroundColor: '#e74c3c', // Color for Earthquake
                    },
                    {
                        label: 'Tornado',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // Monthly data for Tornado
                        backgroundColor: '#f39c12', // Color for Tornado
                    },
                    {
                        label: 'Landslide',
                        data: [1, 0, 2, 1, 0, 1, 3, 2, 1, 0, 1, 0], // Monthly data for Landslide
                        backgroundColor: '#27ae60', // Color for Landslide
                    }
                ]
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
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 10
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false, // Do not stack bars
                        barPercentage: 0.5, // Adjust bar width
                        categoryPercentage: 0.8 // Adjust spacing between groups of bars
                    },
                    y: {
                        beginAtZero: true,
                        max: 60 // Set maximum value for the y-axis
                    }
                }
            }
        });
    
        // Line Chart for Disaster Incidents Per Year (2016-2025)
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'], // Years
                datasets: [{
                    label: 'Total Disaster Incidents',
                    data: [234, 289, 145, 245, 130, 198, 301, 256, 99, 20], // Updated total incidents for each year
                    borderColor: '#2980b9',
                    backgroundColor: 'rgba(41, 128, 185, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1000 // Set maximum value for the y-axis
                    }
                }
            }
        });
    
        // Example data for the cards based on the charts
        const disasterData = [
            {
                type: "Fire",
                incidents: barChart.data.datasets[0].data.reduce((a, b) => a + b, 0), // Total incidents for Fire
                trend: "↑ 10%",
                trendText: "More than last month"
            },
            {
                type: "Storm",
                incidents: barChart.data.datasets[1].data.reduce((a, b) => a + b, 0), // Total incidents for Storm
                trend: "↑ 60%",
                trendText: "More than last month"
            },
            {
                type: "Flood",
                incidents: barChart.data.datasets[2].data.reduce((a, b) => a + b, 0), // Total incidents for Flood
                trend: "↓ 5%",
                trendText: "Less than last month"
            },
            {
                type: "Earthquake",
                incidents: barChart.data.datasets[3].data.reduce((a, b) => a + b, 0), // Total incidents for Earthquake
                trend: "↓ 9%",
                trendText: "Less than last month"
            },
            {
                type: "Tornado",
                incidents: barChart.data.datasets[4].data.reduce((a, b) => a + b, 0), // Total incidents for Tornado
                trend: "↓ 30%",
                trendText: "Less than last month"
            },
            {
                type: "Landslide",
                incidents: barChart.data.datasets[5].data.reduce((a, b) => a + b, 0), // Total incidents for Landslide
                trend: "↓ 23%",
                trendText: "Less than last month"
            }
        ];
    
        // Function to update the visible cards
        function updateVisibleCards() {
            // Create a new order based on the current index
            const newOrder = [];
            for (let i = 0; i < totalCards; i++) {
                newOrder.push(cards[(currentIndex + i) % totalCards]);
            }
    
            // Immediately update the DOM to reflect the new order
            iconCards.innerHTML = ''; // Clear the current cards
            newOrder.forEach((card, index) => {
                const dataIndex = (currentIndex + index) % totalCards; // Get the correct data index
                const data = disasterData[dataIndex]; // Get the corresponding data
    
                // Update card content with icons
                card.innerHTML = `
                    <i class="fas fa-${data.type.toLowerCase()} fa-3x"></i>
                    <h3>${data.type}</h3>
                    <p>${data.incidents} Incidents</p>
                    <span class="${data.trend.startsWith('↑') ? 'green' : 'red'}">${data.trend}</span>
                    <p class="small-text ${data.trend.startsWith('↑') ? '' : 'red-text'}">${data.trendText}</p>
                `;
    
                iconCards.appendChild(card); // Append cards in new order
                card.classList.add('jump'); // Add jump class to each card
            });
    
            // Remove the jump class after the animation completes
            setTimeout(() => {
                newOrder.forEach(card => card.classList.remove('jump'));
            }, 300); // Match the timeout with the CSS animation duration
        }
    
        // Initial display
        updateVisibleCards();
    
        // Automatic click on the "next" button every second
        setInterval(() => {
            document.getElementById('next').click();
        }, 5000); // Click every 5000 milliseconds (5 second)
    
        document.getElementById('next').addEventListener('click', function () {
            // Move to the next set of cards
            currentIndex = (currentIndex + 1) % totalCards; // Loop back to the start
            updateVisibleCards(); // Update the visible cards
        });
    
        document.getElementById('prev').addEventListener('click', function () {
            // Move to the previous set of cards
            currentIndex = (currentIndex - 1 + totalCards) % totalCards; // Loop back to the end
            updateVisibleCards(); // Update the visible cards
        });
    });
    //Recent Activities
    // Array to hold recent activities
let recentActivities = JSON.parse(localStorage.getItem('recentActivities')) || [];

// Function to log user activity
function logActivity(page, action) {
    const now = new Date();
    const activity = {
        page: page,
        time: now.toLocaleString(),
        action: action
    };

    // Always log the activity at the beginning of the array
    recentActivities.unshift(activity); // Add new activity to the beginning of the array

    // Limit to the last 5 activities (FIFO)
    if (recentActivities.length > 5) {
        recentActivities.pop(); // Remove the oldest activity from the end
    }

    updateActivityTable(); // Update the table
    localStorage.setItem('recentActivities', JSON.stringify(recentActivities)); // Store in localStorage
}

// Function to update the activity table
function updateActivityTable() {
    const tableBody = document.getElementById("activityTableBody");
    tableBody.innerHTML = ""; // Clear existing rows

    // Render activities in the order they are stored (most recent first)
    recentActivities.forEach(activity => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${activity.page}</td>
            <td>${activity.time}</td>
            <td>${activity.action}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Example of logging activities when navigating
document.querySelectorAll(".menu-sidebar a").forEach(link => {
    link.addEventListener("click", function () {
        const page = this.textContent; // Get the page name from the link text
        const action = "Visited"; // Define the action
        logActivity(page, action); // Log the activity
    });
});

// Initialize the activity table on page load
document.addEventListener("DOMContentLoaded", updateActivityTable);


// Dation Tracker
// Function to generate a random 6-digit donation ID
function generateDonationId() {
    return Math.floor(100000 + Math.random() * 900000); // Generates a number between 100000 and 999999
}

// Function to add a new donation entry
function addDonationEntry(name, email, number, state, datePending, dateApprovedDeclined) {
    const tableBody = document.getElementById("donationTableBody");

    // Create a new row
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td>${generateDonationId()}</td>
        <td>${name}</td>
        <td>${email}</td>
        <td>${number}</td>
        <td>${state}</td>
        <td>${datePending}</td>
        <td>${dateApprovedDeclined || ''}</td> <!-- If no date, leave it empty -->
    `;

    // Append the new row to the table body
    tableBody.appendChild(newRow);
}

// Example usage: Adding some donation entries
// These values should come from your Android app
addDonationEntry("qq", "q@example.com", "(123) 456-7890", "Approved", "2023-10-01", "2023-10-02");
addDonationEntry("saw", "s@example.com", "(987) 654-3210", "Pending", "2023-10-03", "");
addDonationEntry("three", "t@example.com", "(555) 123-4567", "Declined", "2023-10-04", "2023-10-05");