// Function to edit an appointment and show the status change options
function editAppointment(appointment_id) {
    currentAppointmentId = appointment_id; // Save the appointment ID
    document.getElementById('statusChangeForm').style.display = 'block'; // Show status change form

    // Set the current status as selected in the dropdown
    var currentStatus = document.getElementById('status_' + appointment_id).innerText.trim();
    document.getElementById('statusSelect').value = currentStatus;
}
