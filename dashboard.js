document.addEventListener('DOMContentLoaded', function() {
    updateDoctorsList();
    updateStats();
});

let doctors = [
    { id: 1, name: "Dr. Krishna Dhimal", department: "Cardiology", status: "active" },
    { id: 2, name: "Dr. Bishnu Rijal", department: "Neurology", status: "active" },
    { id: 3, name: "Dr. Arjun Shrestha", department: "Pediatrics", status: "inactive" }
];

function updateStats() {
    document.querySelector('.stats-container .count:nth-child(1)').textContent = doctors.length;
}

function updateDoctorsList() {
    const tbody = document.getElementById('doctorsList');
    tbody.innerHTML = '';

    doctors.forEach(doctor => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${doctor.name}</td>
            <td>${doctor.department}</td>
            <td><span class="status ${doctor.status}">${doctor.status}</span></td>
            <td>
                <button onclick="editDoctor(${doctor.id})" class="edit-btn">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteDoctor(${doctor.id})" class="delete-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function openAddDoctorModal() {
    const modal = document.getElementById('addDoctorModal');
    modal.style.display = 'block';
}

function closeModal() {
    const modal = document.getElementById('addDoctorModal');
    modal.style.display = 'none';
}

document.getElementById('addDoctorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const newDoctor = {
        id: doctors.length + 1,
        name: formData.get('name'),
        department: formData.get('department'),
        status: formData.get('status')
    };

    doctors.push(newDoctor);
    updateDoctorsList();
    updateStats();
    closeModal();
    this.reset();
});

function editDoctor(doctorId) {
    fetch('manage_doctors.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get&id=${doctorId}`
    })
    .then(response => response.json())
    .then(doctor => {
        document.getElementById('edit_doctor_id').value = doctor.doctor_id;
        document.getElementById('edit_doctor_name').value = doctor.doctor_name;
        document.getElementById('edit_department').value = doctor.department;
        document.getElementById('edit_email').value = doctor.email;
        document.getElementById('edit_status').value = doctor.status;
        
        document.getElementById('editDoctorModal').style.display = 'block';
    });
}

function closeEditModal() {
    document.getElementById('editDoctorModal').style.display = 'none';
}

document.getElementById('editDoctorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update');

    fetch('manage_doctors.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            alert('Doctor updated successfully');
            closeEditModal();
            loadDoctors();
        } else {
            alert('Error updating doctor: ' + result.message);
        }
    });
});

function deleteDoctor(id) {
    if(confirm('Are you sure you want to delete this doctor?')) {
        doctors = doctors.filter(d => d.id !== id);
        updateDoctorsList();
        updateStats();
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('addDoctorModal');
    if (event.target === modal) {
        closeModal();
    }
}

document.querySelector('.close').onclick = closeModal;