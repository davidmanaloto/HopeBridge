document.addEventListener("DOMContentLoaded", () => {
    loadVerificationRequests();
});

function loadVerificationRequests() {
    fetch("verify_fetch.php")
        .then(response => response.json())
        .then(users => {
            const tableBody = document.getElementById("verification-table");
            tableBody.innerHTML = ""; // Clear previous data

            users.forEach(user => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.verification_reason}</td>
                    <td>${user.verfication_document}</td>
                    <td class="status">${user.verification_status}</td>
                    <td>
                        <button class="approve" onclick="updateVerification(${user.id}, 'Verified')">Approve</button>
                        <button class="reject" onclick="updateVerification(${user.id}, 'Rejected')">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching users:", error));
}

function updateVerification(userId, status) {
    if (!confirm(`Are you sure you want to mark this user as ${status.toLowerCase()}?`)) return;

    fetch("verify_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${userId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`User verification updated successfully.`);
            loadVerificationRequests(); // Refresh table dynamically
        } else {
            alert("Error updating verification.");
        }
    })
    .catch(error => console.error("Error:", error));
}
