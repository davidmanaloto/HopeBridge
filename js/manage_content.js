// Fetch and display fundraising events
function fetchEvents() {
    $.ajax({
        url: 'fetch_events.php',
        type: 'GET',
        success: function(response) {
            $('#eventList').html(response);
        }
    });
}

// Delete an event
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        $.ajax({
            url: 'delete_event.php',
            type: 'POST',
            data: { event_id: eventId },
            success: function(response) {
                alert(response);
                fetchEvents(); // Refresh the event list after deletion
            }
        });
    }
}

// Load events on page load
$(document).ready(function() {
    fetchEvents();
});
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const eventId = this.getAttribute('data-id');

            if (confirm("Are you sure you want to delete this event?")) {
                fetch('delete_event.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `event_id=${eventId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert("Event deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error deleting event.");
                    }
                });
            }
        });
    });
});
