document.addEventListener("DOMContentLoaded", function () {
    loadDonations();
    document.getElementById("searchInput").addEventListener("keyup", filterDonations);
    document.getElementById("filterStatus").addEventListener("change", loadDonations);
});
//Loads Donations
function loadDonations() {
    fetch(`donation_action.php?action=get_donations&status`)
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
                    ${donation.receipt_path ? `<a href="${donation.receipt_path.replace(/^\/?php\//, '')}" target="_blank" rel="noopener noreferrer>"View Receipt"</a>` : "No Receipt"}
                    </td>
                    <td>${donation.date_created}</td>
                    <td class="status">${donation.status}</td>
                    <td>
                        <button onclick="approveDonation(${donation.id})">Approve</button>
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
// Approves a donation and removes it from the table
function approveDonation(id) {
    if (confirm("Are you sure you want to approve this donation?")) {
        fetch("donation_action.php?action=update_status", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}&status=Completed`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`donation-${id}`).remove(); // Remove the row from the table
            } else {
                alert("Failed to approve donation.");
            }
        })
        .catch(error => console.error("Error approving donation:", error));
    }
}
