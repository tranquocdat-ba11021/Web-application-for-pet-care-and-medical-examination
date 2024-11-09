<?php require('headdoctor.php'); ?>

<?php
// Kiểm tra xem bác sĩ có đăng nhập không
if (!isset($_SESSION['doctor_id'])) {
    header('Location: doctorlogin.php');
    exit();
}

$doctor_id = $_SESSION['doctor_id'];



// Xử lý bộ lọc
$whereClauses = [];
$params = [];
$types = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['date'])) {
        $whereClauses[] = "c.date = ?";
        $params[] = $_GET['date'];
        $types .= 's';
    }
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = "AND " . implode(' AND ', $whereClauses);
}

// Lấy danh sách lịch làm việc của bác sĩ
$sql = "SELECT c.date, c.start_time, c.end_time, d.name_doctor, c.doctor_id
        FROM calendar c 
        JOIN doctor d ON c.doctor_id = d.id_doctor 
        WHERE c.doctor_id = ?
        $whereSQL
        ORDER BY c.date ASC, c.start_time ASC";

$stmt = $con->prepare($sql);
$stmt->bind_param('i' . $types, $doctor_id, ...$params);
$stmt->execute();
$schedules = $stmt->get_result();

// Chuyển dữ liệu lịch thành JSON cho FullCalendar
$events = [];
if ($schedules->num_rows > 0) {
    while ($row = $schedules->fetch_assoc()) {
        $events[] = [
            'title' => $row['name_doctor'],
            'start' => $row['date'] . 'T' . $row['start_time'],
            'end' => $row['date'] . 'T' . $row['end_time']
        ];
    }
}

$con->close();
?>

<body>
<div class="wrapper">
    <?php require('navbardoctor.php'); ?>

    <div id="content2">
        <div class="container">
            <h2 class="header-title text-center">Your Schedule for the Week</h2>
            
            <!-- Bộ Lọc -->
            <form class="d-flex flex-wrap align-items-end mb-4" method="GET">
                <div class="me-2 mb-2">
                    <input class="form-control" type="date" id="date" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>">
                </div>
                <div class="mb-2">
                    <button class="btn btn-primary" type="submit">Sreach</button>
                </div>
                <div class="mb-2">
                    <a class="btn btn-secondary ms-2" href="doctor_schedule.php">Reset</a>
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
        initialView: 'dayGridWeek',
        firstDay: 1, // Start the week on Monday (0 = Sunday, 1 = Monday)
        events: <?php echo json_encode($events); ?>,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        eventClick: function(info) {
            // Hiển thị thông tin sự kiện trong pop-up
            var eventDetails = `
                <div>
                    <h3>${info.event.title}</h3>
                    <p>Start: ${info.event.start.toLocaleString()}</p>
                    <p>End: ${info.event.end.toLocaleString()}</p>
                </div>
            `;

            var popup = document.createElement('div');
            popup.className = 'event-popup';
            popup.innerHTML = eventDetails;

            // Thêm một nút đóng
            var closeButton = document.createElement('button');
            closeButton.textContent = 'Close';
            closeButton.onclick = function() {
                document.body.removeChild(popup);
            };
            popup.appendChild(closeButton);

            document.body.appendChild(popup);
        }
    });

    calendar.render();
});

</script>

<?php require('footerdoctor.php'); ?>
