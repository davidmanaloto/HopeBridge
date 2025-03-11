console.log("user_management.js is running!");

document.addEventListener('DOMContentLoaded', function() { //Important 
    loadUsers(this.value);   
    document.getElementById('searchInput').addEventListener('keyup', filterUsers);
});

document.getElementById('filterSelect').addEventListener('change', function () {
    loadUsers(this.value);
});
loadUsers();

function loadUsers(filter = "all") {
    fetch(`user-management.php?action=get_users&filter=${filter}`)
        .then(response => response.json())
        .then(users => {
            const tableBody = document.getElementById("userTableBody");
            if (!tableBody) {
                console.error("userTableBody element not found.");  
                return;
            }
            tableBody.innerHTML = "";
            
            if (users.length === 0) {
                tableBody.innerHTML = `<tr><td colspan='5' style='text-align: center;'>No accounts to display</td></tr>`;
                return;
            }

            users.forEach(user => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="username">${user.username}</td>
                    <td class="email">${user.email}</td>
                    <td class="role"><span class="user-role ${user.role.toLowerCase()}">${user.role}</span></td>
                    <td class="status">${user.status}</td>
                    <td class="action-buttons">
                        <button class="block-user" onclick="blockUser(${user.id}, this)">${user.status === "Active" ? "Block" : "Unblock"}</button>
                        <button class="delete-user" onclick="deleteUser(${user.id}, this)">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error("Error fetching users:", error);
        });
}



function filterUsers() {
    const searchValue = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#userTableBody tr");

    rows.forEach(row => {
        const username = row.querySelector(".username").textContent.toLowerCase();
        const email = row.querySelector(".email").textContent.toLowerCase();
        
        if (username.includes(searchValue) || email.includes(searchValue)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

function deleteUser(id, button) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`user-management.php?action=delete_user&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.closest('tr').remove(); // Remove the row
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error deleting user:', error);
            });
    }
}

function blockUser(id, button) {
    fetch(`user-management.php?action=block_user&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusCell = button.closest('tr').querySelector('.status');
                const newStatus = statusCell.innerText === 'Active' ? 'Blocked' : 'Active';
                statusCell.innerText = newStatus;
                button.innerText = newStatus === 'Active' ? 'Block' : 'Unblock';
            } else {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error blocking/unblocking user:', error);
        });
}
