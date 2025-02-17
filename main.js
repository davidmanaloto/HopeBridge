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
// Get the current page from the URL
const currentPage = window.location.pathname.split("/").pop();

// Function to set the active link
function setActiveLink() {
    const links = document.querySelectorAll('.sidebar ul li a');
    links.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active'); // Remove active class from other links
        }
    });
}

// Call the function to set the active link
setActiveLink();
