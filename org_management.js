function filterUsers() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#userTableBody tr');
    
    rows.forEach(row => {
        const username = row.querySelector('.username').innerText.toLowerCase();
        const email = row.querySelector('.email').innerText.toLowerCase();
        const role = row.querySelector('.role').innerText.toLowerCase();
        
        if (username.includes(searchValue) || email.includes(searchValue) || role.includes(searchValue)) {
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

