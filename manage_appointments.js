function updateStatus(appointmentId, status) {
    let reason = '';
    if (status === 'cancelled') {
        reason = prompt('Please enter cancellation reason:');
        if (reason === null) return; // User clicked Cancel
        if (reason.trim() === '') {
            alert('Cancellation reason is required');
            return;
        }
    }

    $.ajax({
        url: 'manage_appointments.php',
        type: 'POST',
        data: {
            action: 'update_status',
            appointment_id: appointmentId,
            status: status,
            reason: reason
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message || 'Error updating status');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Error updating appointment status');
            }
        },
        error: function() {
            alert('Error communicating with server');
        }
    });
} 