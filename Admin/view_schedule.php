<?php
require('headadmin.php');

// Xử lý bộ lọc
$whereClauses = [];
$params = [];
$types = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['doctor_id'])) {
        $whereClauses[] = "c.doctor_id = ?";
        $params[] = $_GET['doctor_id'];
        $types .= 'i';
    }
    if (!empty($_GET['date'])) {
        $whereClauses[] = "c.date = ?";
        $params[] = $_GET['date'];
        $types .= 's';
    }
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = "WHERE " . implode(' AND ', $whereClauses);
}

// Lấy danh sách lịch làm việc từ bảng calendar với bộ lọc
$sql = "SELECT c.date, c.start_time, c.end_time, d.name_doctor, c.doctor_id
        FROM calendar c 
        JOIN doctor d ON c.doctor_id = d.id_doctor 
        $whereSQL
        ORDER BY c.date ASC, c.start_time ASC";

$stmt = $con->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$schedules = $stmt->get_result();

// Lấy danh sách bác sĩ để hiển thị trong bộ lọc
$doctorSql = "SELECT id_doctor, name_doctor FROM doctor";
$doctors = $con->query($doctorSql);

// Chuyển dữ liệu lịch thành JSON cho FullCalendar
$events = [];
if ($schedules->num_rows > 0) {
    while ($row = $schedules->fetch_assoc()) {
        $events[] = [
            'id' => $row['doctor_id'], // Add ID for deletion
            'title' => $row['name_doctor'],
            'start' => $row['date'] . 'T' . $row['start_time'],
            'end' => $row['date'] . 'T' . $row['end_time'],
            'doctor_id' => $row['doctor_id']
        ];
    }
}

$con->close();
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>
        <div id="content2">
            <div class="container">
                <h2 class="header-title text-center">Doctors' Work Schedule</h2>
                <!-- Bộ Lọc -->
                <form class="d-flex flex-wrap align-items-end mb-4" method="GET">
                    <div class="me-2 mb-2">
                        <select class="form-select" id="doctor_id" name="doctor_id">
                            <option value="">All doctor</option>
                            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                <option value="<?php echo $doctor['id_doctor']; ?>" <?php if (isset($_GET['doctor_id']) && $_GET['doctor_id'] == $doctor['id_doctor']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($doctor['name_doctor']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="me-2 mb-2">
                        <input class="form-control" type="date" id="date" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>">
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-primary" type="submit">Lọc</button>
                    </div>
                    <div class="mb-2">
                        <a class="btn btn-secondary ms-2" href="view_schedule.php">Reset</a>
                    </div>
                </form>

                <!-- Hiển Thị Lịch Bằng FullCalendar -->
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo json_encode($events); ?>,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                firstDay: 1, // Bắt đầu tuần từ thứ Hai
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventClick: function(info) {
                    var eventDetails = `
                <div>
                    <h3>${info.event.title}</h3>
                    <p>Start: ${info.event.start.toLocaleString()}</p>
                    <p>End: ${info.event.end.toLocaleString()}</p>
                    <button class="btn btn-danger" id="delete-event" data-id="${info.event.id}">Delete Schedule</button>
                </div>
            `;

                    var popup = document.createElement('div');
                    popup.className = 'event-popup';
                    popup.innerHTML = eventDetails;

                    // Add a close button
                    var closeButton = document.createElement('button');
                    closeButton.textContent = 'Close';
                    closeButton.onclick = function() {
                        document.body.removeChild(popup);
                    };
                    popup.appendChild(closeButton);

                    document.body.appendChild(popup);

                    // Add event listener for delete button
                    var deleteButton = popup.querySelector('#delete-event');
                    if (deleteButton) {
                        deleteButton.addEventListener('click', function() {
                            var eventId = this.getAttribute('data-id');
                            deleteEvent(eventId, info.event);
                        });
                    }
                }
            });

            calendar.render();
        });

        // Function to delete the event
        function deleteEvent(eventId, event) {
            if (confirm('Are you sure you want to delete this schedule?')) {
                fetch('delete_schedule.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: eventId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Event deleted successfully');
                            // Remove the event from the calendar
                            if (event) {
                                event.remove();
                            }
                        } else {
                            alert('Error deleting event');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

    <?php require('footeradmin.php') ?>
</body>