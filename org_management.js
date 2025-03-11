document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM fully loaded");
    
    // Attach search functionality
    const searchInput = document.getElementById('searchOrg');
    if (searchInput) {
        searchInput.addEventListener('keyup', filterOrganizations);
    } else {
        console.error("Search input not found!");
    }

    // Attach delete functionality to buttons
    document.getElementById('orgList').addEventListener('click', function (event) {
        if (event.target.classList.contains('delete-btn')) {
            const orgId = event.target.dataset.id;
            deleteOrganization(orgId, event.target);
        }
    });
});

// Function to filter organizations based on search input
function filterOrganizations() {
    const searchValue = document.getElementById('searchOrg').value.toLowerCase();
    const rows = document.querySelectorAll('#orgList tr');

    rows.forEach(row => {
        const name = row.querySelector('.org-name')?.innerText.toLowerCase() || "";
        const tags = row.querySelector('.org-tags')?.innerText.toLowerCase() || "";
        const description = row.querySelector('.org-description')?.innerText.toLowerCase() || "";

        console.log(`Filtering: Name=${name}, Tags=${tags}, Description=${description}`);

        if (name.includes(searchValue) || tags.includes(searchValue) || description.includes(searchValue)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

// Function to delete an organization
function deleteOrganization(id, button) {
    if (confirm('Are you sure you want to delete this organization?')) {
        fetch(`organizations.php?action=delete_org&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Organization ${id} deleted`);
                    button.closest('tr').remove(); // Remove the row from the table
                } else {
                    alert(data.error || "Failed to delete the organization.");
                }
            })
            .catch(error => {
                console.error('Error deleting organization:', error);
            });
    }
}