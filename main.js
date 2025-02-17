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
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("signup") === "success") {
        openModal('loginModal');
    }
});