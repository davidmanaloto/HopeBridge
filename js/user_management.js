document.addEventListener('DOMContentLoaded', function () {
    loadUsers();

    document.getElementById('filterSelect').addEventListener('change', function () {
        loadUsers(this.value);
    });

    document.getElementById('searchInput').addEventListener('keyup', filterUsers);
});
//Load Users
function loadUsers(filter = "all") {
    fetch(`user_management.php?action=get_users&filter=${filter}`)
        .then(response => response.json())
        .then(users => {
            const tableBody = document.getElementById("userTableBody");
            tableBody.innerHTML = "";

            if (users.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='5'>No users found</td></tr>";
                return;
            }

            users.forEach(user => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td class="status">${user.status}</td>
                    <td>
                        <button onclick="toggleStatus(${user.id}, this)">${user.status === "Active" ? "Block" : "Unblock"}</button>
                        <button onclick="deleteUser(${user.id}, this)">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching users:", error));
}
//Change Status Users
function toggleStatus(id, button) {
    fetch("user_management.php?action=toggle_status", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = button.closest("tr");
            const statusCell = row.querySelector(".status");
            const newStatus = statusCell.textContent === "Active" ? "Blocked" : "Active";
            statusCell.textContent = newStatus;
            button.textContent = newStatus === "Active" ? "Block" : "Unblock";
        }
    })
    .catch(error => console.error("Error toggling status:", error));
}
//Delete Users
function deleteUser(id, button) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    fetch("user_management.php?action=delete_user", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.closest("tr").remove();
        }
    })
    .catch(error => console.error("Error deleting user:", error));
}
//Filter Users
function filterUsers() {
    const searchValue = document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll("#userTableBody tr").forEach(row => {
        const username = row.cells[0].textContent.toLowerCase();
        const email = row.cells[1].textContent.toLowerCase();
        row.style.display = username.includes(searchValue) || email.includes(searchValue) ? "" : "none";
    });
}
