<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ride Fare Estimator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card {
            border-radius: 15px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.25);
        }
        .fare-display {
            font-size: 2.5rem;
            font-weight: bold;
            color: #6a11cb;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }
        .loading-spinner {
            display: none;
            width: 2rem;
            height: 2rem;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: .75s linear infinite spinner-border;
        }
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        .trip-row:hover {
            background-color: rgba(106, 17, 203, 0.05);
            cursor: pointer;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-booked {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg mb-5">
                <div class="card-header">
                    <h2 class="mb-0"><i class="bi bi-car-front"></i> Ride Fare Estimator</h2>
                </div>
                <div class="card-body p-4">
                    <div id="message" class="alert d-none alert-dismissible fade show">
                        <span id="messageText"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <form id="fareForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label"><i class="bi bi-person"></i> User</label>
                                <select class="form-select" id="user_id" required>
                                    <option value="">Select User</option>
                                </select>
                                <div class="invalid-feedback">Please select a valid User</div>
                            </div>

                            <div class="col-md-6">
                                <label for="vehicle_type_id" class="form-label"><i class="bi bi-car"></i> Vehicle Type</label>
                                <select class="form-select" id="vehicle_type_id" required>
                                    <option value="">Select Vehicle</option>
                                    <option value="1"><i class="bi bi-car"></i> Economy</option>
                                    <option value="2"><i class="bi bi-car-front"></i> Standard</option>
                                    <option value="3"><i class="bi bi-car-front-fill"></i> Luxury</option>
                                </select>
                                <div class="invalid-feedback">Please select a vehicle type</div>
                            </div>

                            <div class="col-md-6">
                                <label for="distance" class="form-label"><i class="bi bi-signpost"></i> Distance (km)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="distance" required min="0.1" step="0.1">
                                    <span class="input-group-text">km</span>
                                </div>
                                <div class="invalid-feedback">Please enter a valid distance</div>
                            </div>

                            <div class="col-md-6">
                                <label for="requests_per_minute" class="form-label"><i class="bi bi-speedometer2"></i> Requests Per Minute</label>
                                <input type="number" class="form-control" id="requests_per_minute" required min="1">
                                <div class="invalid-feedback">Please enter valid requests per minute</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="calculateFare" class="btn btn-primary btn-lg">
                                <span id="calculateText">Calculate Fare</span>
                                <span id="calculateSpinner" class="loading-spinner ms-2"></span>
                            </button>
                            <button type="button" id="bookTrip" class="btn btn-success btn-lg d-none">
                                <i class="bi bi-check-circle"></i> Book Trip
                            </button>
                        </div>
                    </form>

                    <div class="fare-display mt-4">
                        <div class="text-muted mb-2">Estimated Fare</div>
                        <div>₹<span id="fareDisplay">--</span></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-journal-check"></i> Confirmed Trips</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="filterVehicle" class="form-label"><i class="bi bi-funnel"></i> Filter by Vehicle Type</label>
                            <select class="form-select" id="filterVehicle">
                                <option value="">All Vehicles</option>
                                <option value="1">Economy</option>
                                <option value="2">Standard</option>
                                <option value="3">Luxury</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person"></i> User</th>
                                    <th><i class="bi bi-car"></i> Vehicle</th>
                                    <th><i class="bi bi-signpost"></i> Distance</th>
                                    <th><i class="bi bi-cash"></i> Fare</th>
                                    <th><i class="bi bi-info-circle"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody id="tripsTableBody" class="align-middle">
                                <!-- Dynamically populated rows will go here -->
                                <tr id="noTripsRow">
                                    <td colspan="5" class="text-center text-muted py-4">No trips booked yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Fetch users and populate the dropdown
    const response = await fetch('/api/users');
    const users = await response.json();
    const userSelect = document.getElementById('user_id');

    // Populate the user dropdown with fetched data
    users.forEach(user => {
        const option = document.createElement('option');
        option.value = user.id;
        option.textContent = user.name;  // Assuming the user has a name field
        userSelect.appendChild(option);
    });
});

document.getElementById('calculateFare').addEventListener('click', async function () {
    const user_id = document.getElementById('user_id').value;
    const vehicle_type_id = document.getElementById('vehicle_type_id').value;
    const distance = document.getElementById('distance').value;
    const rpm = document.getElementById('requests_per_minute').value;

    // Clear any previous error messages
    clearErrors();

    let isValid = true;

    // Validate the form fields
    if (!user_id) {
        isValid = false;
        document.getElementById('user_id').classList.add('is-invalid');
    }

    if (!vehicle_type_id) {
        isValid = false;
        document.getElementById('vehicle_type_id').classList.add('is-invalid');
    }

    if (!distance || distance <= 0) {
        isValid = false;
        document.getElementById('distance').classList.add('is-invalid');
    }

    if (!rpm || rpm <= 0) {
        isValid = false;
        document.getElementById('requests_per_minute').classList.add('is-invalid');
    }

    if (!isValid) {
        return; // Stop if any validation fails
    }

    // Show loading state
    const calculateBtn = document.getElementById('calculateFare');
    const calculateText = document.getElementById('calculateText');
    const spinner = document.getElementById('calculateSpinner');

    calculateText.textContent = 'Calculating...';
    spinner.style.display = 'inline-block';
    calculateBtn.disabled = true;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  // Get CSRF token

    try {
        const response = await fetch('/api/calculate-fare', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token  // Include CSRF token in the headers
            },
            body: JSON.stringify({ user_id, vehicle_type_id, distance, requests_per_minute: rpm })
        });

        const data = await response.json();

        const messageBox = document.getElementById('message');
        const messageText = document.getElementById('messageText');

        if (response.ok) {
            document.getElementById('fareDisplay').textContent = data.fare;
            document.getElementById('bookTrip').classList.remove('d-none');
            messageBox.className = 'alert alert-success alert-dismissible fade show';
            messageText.textContent = 'Fare calculated successfully!';
            messageBox.classList.remove('d-none');

            // Scroll to fare display
            document.querySelector('.fare-display').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        } else {
            messageBox.className = 'alert alert-danger alert-dismissible fade show';
            messageText.textContent = data.error || 'Error calculating fare';
            messageBox.classList.remove('d-none');
            document.getElementById('bookTrip').classList.add('d-none');
        }
    } catch (error) {
        console.error('Error:', error);
        const messageBox = document.getElementById('message');
        const messageText = document.getElementById('messageText');
        messageBox.className = 'alert alert-danger alert-dismissible fade show';
        messageText.textContent = 'Network error. Please try again.';
        messageBox.classList.remove('d-none');
    } finally {
        // Reset loading state
        calculateText.textContent = 'Calculate Fare';
        spinner.style.display = 'none';
        calculateBtn.disabled = false;
    }
});

document.getElementById('bookTrip').addEventListener('click', async function () {
    const user_id = document.getElementById('user_id').value;
    const vehicle_type_id = document.getElementById('vehicle_type_id').value;
    const distance = document.getElementById('distance').value;
    const fare = document.getElementById('fareDisplay').textContent;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  // Get CSRF token

    // Show loading state on book button
    const bookBtn = document.getElementById('bookTrip');
    const originalHtml = bookBtn.innerHTML;
    bookBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Booking...';
    bookBtn.disabled = true;

    try {
        const response = await fetch('/api/trip', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token  // Include CSRF token in the headers
            },
            body: JSON.stringify({ user_id, vehicle_type_id, distance, fare })
        });

        const data = await response.json();
        const messageBox = document.getElementById('message');
        const messageText = document.getElementById('messageText');

        if (response.ok) {
            // Hide the "no trips" row if it exists
            const noTripsRow = document.getElementById('noTripsRow');
            if (noTripsRow) noTripsRow.style.display = 'none';

            // Add the new trip to the table
            const tripRow = document.createElement('tr');
            tripRow.className = 'trip-row';
            tripRow.innerHTML = `
                <td>${user_id}</td>
                <td>${getVehicleTypeName(vehicle_type_id)}</td>
                <td>${distance} km</td>
                <td>₹${fare}</td>
                <td><span class="status-badge status-booked">Booked</span></td>
            `;
            document.getElementById('tripsTableBody').prepend(tripRow);

            messageBox.className = 'alert alert-info alert-dismissible fade show';
            messageText.textContent = 'Trip booked successfully!';
            messageBox.classList.remove('d-none');

            // Reset form
            document.getElementById('bookTrip').classList.add('d-none');

            // Scroll to trips table
            document.getElementById('tripsTableBody').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            messageBox.className = 'alert alert-danger alert-dismissible fade show';
            messageText.textContent = data.error || 'Failed to book trip!';
            messageBox.classList.remove('d-none');
        }
    } catch (error) {
        console.error('Error:', error);
        const messageBox = document.getElementById('message');
        const messageText = document.getElementById('messageText');
        messageBox.className = 'alert alert-danger alert-dismissible fade show';
        messageText.textContent = 'Network error. Please try again.';
        messageBox.classList.remove('d-none');
    } finally {
        // Reset button state
        bookBtn.innerHTML = originalHtml;
        bookBtn.disabled = false;
    }
});

document.getElementById('filterVehicle').addEventListener('change', function () {
    const filterValue = this.value;
    const rows = document.getElementById('tripsTableBody').getElementsByTagName('tr');
    let visibleRows = 0;

    for (const row of rows) {
        if (row.id === 'noTripsRow') continue;

        const vehicleType = row.cells[1].textContent;
        if (filterValue === '' || vehicleType === getVehicleTypeName(filterValue)) {
            row.style.display = '';
            visibleRows++;
        } else {
            row.style.display = 'none';
        }
    }

    // Show "no trips" message if all rows are filtered out
    const noTripsRow = document.getElementById('noTripsRow');
    if (noTripsRow) {
        if (visibleRows === 0 && filterValue !== '') {
            noTripsRow.style.display = '';
            noTripsRow.cells[0].colSpan = 5;
            noTripsRow.cells[0].textContent = 'No trips match the selected filter';
        } else if (visibleRows === 0) {
            noTripsRow.style.display = '';
            noTripsRow.cells[0].colSpan = 5;
            noTripsRow.cells[0].textContent = 'No trips booked yet';
        } else {
            noTripsRow.style.display = 'none';
        }
    }
});

function getVehicleTypeName(id) {
    switch(id) {
        case '1': return 'Economy';
        case '2': return 'Standard';
        case '3': return 'Luxury';
        default: return '';
    }
}

// Clear all error messages
function clearErrors() {
    const fields = document.querySelectorAll('.form-control, .form-select');
    fields.forEach(field => field.classList.remove('is-invalid'));
}

// Add input validation on blur
document.querySelectorAll('input, select').forEach(element => {
    element.addEventListener('blur', function() {
        if (this.required && !this.value) {
            this.classList.add('is-invalid');
        } else if (this.type === 'number' && this.value <= 0) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
});
</script>
</body>
</html>
