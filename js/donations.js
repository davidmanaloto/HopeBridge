document.addEventListener("DOMContentLoaded", function () {
    loadDonations();
    document.getElementById("searchInput").addEventListener("keyup", filterDonations);
    document.getElementById("filterStatus").addEventListener("change", loadDonations);
});
//Loads Donations
function loadDonations() {
    const filter = document.getElementById("filterStatus").value;
    fetch(`donation_action.php?action=get_donations&status=${filter}`)
        .then(response => response.json())
        .then(donations => {
            const tableBody = document.getElementById("donationTableBody");
            tableBody.innerHTML = "";

            if (donations.length === 0) {
                tableBody.innerHTML = `<tr><td colspan='8' style='text-align: center;'>No donations found</td></tr>`;
                return;
            }

            donations.forEach(donation => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${donation.id}</td>
                    <td class="donor-name">${donation.donor_name}</td>
                    <td class="organization-name">${donation.organization_name}</td>
                    <td>$${parseFloat(donation.amount).toFixed(2)}</td>
                    <td>
                    ${donation.receipt_path ? `<a href="${donation.receipt_path}" target="_blank">View Receipt</a>` : "No Receipt"}
                    </td>
                    <td>${donation.date_created}</td>
                    <td class="status">${donation.status}</td>
                    <td>
                        <select onchange="updateStatus(${donation.id}, this)">
                            <option value="Pending" ${donation.status === "Pending" ? "selected" : ""}>Pending</option>
                            <option value="Completed" ${donation.status === "Completed" ? "selected" : ""}>Completed</option>
                            <option value="Failed" ${donation.status === "Failed" ? "selected" : ""}>Failed</option>
                        </select>
                        <button class="delete-donation" onclick="deleteDonation(${donation.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching donations:", error));
}
//Search Filter
document.getElementById("searchInput").addEventListener("input", function () {
    let searchValue = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach(row => {
        let donorName = row.querySelector(".donor-name").textContent.toLowerCase();
        let organizationName = row.querySelector(".organization-name").textContent.toLowerCase();
        let shouldShow = donorName.includes(searchValue) || organizationName.includes(searchValue);
        row.style.display = shouldShow ? "" : "none";
    });
});
//Filter Status
document.getElementById("filterStatus").addEventListener("change", function () {
    let filterValue = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach(row => {
        let status = row.querySelector(".status").textContent.toLowerCase();
        row.style.display = (filterValue === "all" || status === filterValue) ? "" : "none";
    });
});
//Delete Donation
function deleteDonation(id) {
    if (confirm("Are you sure you want to delete this donation?")) {
        fetch(`donation_actions.php?action=delete_donation&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadDonations();
                } else {
                    alert("Failed to delete donation.");
                }
            })
            .catch(error => console.error("Error deleting donation:", error));
    }
}
//Update Donation Status
function updateStatus(id, selectElement) {
    const newStatus = selectElement.value;
    fetch("donation_action.php?action=update_status", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&status=${newStatus}`
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("Failed to update status.");
            }
        })
        .catch(error => console.error("Error updating status:", error));
}
