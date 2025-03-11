function loadPendingUsers() {
    fetch('verifyuser.php?action=get_pending_users')
        .then(response => response.json())
        .then(users => {
            let tableBody = document.getElementById('verifyTableBody');
            tableBody.innerHTML = "";

            if (users.length === 0) {
                // Show a message when no pending users are found
                tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:gray;">No users pending verification</td></tr>`;
            } else {
                users.forEach(user => {
                    let row = `<tr>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>${user.status}</td>
                        <td>
                            <button onclick="verifyUser(${user.id})">Verify</button>
                            <button onclick="rejectUser(${user.id})">Reject</button>
                        </td>
                    </tr>`;
                    tableBody.innerHTML += row;
                });
            }
        });
}


function verifyUser(id) {
    fetch('verifyuser.php?action=verify_user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              alert("User verified successfully!");
              loadPendingUsers(); // Refresh list
          } else {
              alert("Error verifying user.");
          }
      });
}

function rejectUser(id) {
    fetch('verifyuser.php?action=reject_user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              alert("User rejected.");
              loadPendingUsers(); // Refresh list
          } else {
              alert("Error rejecting user.");
          }
      });
}

document.addEventListener("DOMContentLoaded", loadPendingUsers);
