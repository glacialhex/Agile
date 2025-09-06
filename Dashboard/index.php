<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../Login/index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "school_system";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT Id, FirstName, LastName, Email FROM student WHERE Id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Get registered courses - FIXED QUERY based on semester registration
$courses = [];
$stmt = $conn->prepare("
    SELECT c.Code, c.Name, c.Capacity, s.Season, s.Year
    FROM course c 
    INNER JOIN semester s ON c.semester_id = s.id
    INNER JOIN registration r ON r.semester_id = s.id 
    WHERE r.student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

$gpa = 'N/A';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #1a73e8, #6c5ce7);
            color: white;
            padding: 20px 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .brand {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .brand h1 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .nav-links {
            list-style: none;
            padding: 0 15px;
        }
        
        .nav-links li {
            margin-bottom: 10px;
        }
        
        .nav-links a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-links a:hover, .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-links i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main content styling */
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .welcome h2 {
            font-size: 24px;
            color: #2d3436;
        }
        
        .welcome p {
            color: #636e72;
        }
        
        .user-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #1a73e8;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0d5bba;
        }
        
        .btn-danger {
            background-color: #e84118;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c23613;
        }
        
        /* Dashboard cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .card-header h3 {
            font-size: 18px;
            color: #2d3436;
        }
        
        .card-content {
            padding: 10px 0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #f1f1f1;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #636e72;
            font-weight: 500;
        }
        
        .info-value {
            color: #2d3436;
            font-weight: 600;
        }
        
        /* Courses table */
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .courses-table th, .courses-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .courses-table th {
            background-color: #f8f9fa;
            color: #636e72;
            font-weight: 600;
        }
        
        .courses-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-completed {
            background-color: #e3f2fd;
            color: #1a73e8;
        }
        
        .status-inprogress {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .status-pending {
            background-color: #fff8e1;
            color: #f57c00;
        }
        
        /* GPA indicator */
        .gpa-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#1a73e8 0% 80%, #e0e0e0 80% 100%);
            margin: 0 auto;
            position: relative;
        }
        
        .gpa-value {
            position: absolute;
            font-size: 24px;
            font-weight: 700;
            color: #1a73e8;
        }
        
        .gpa-label {
            text-align: center;
            margin-top: 15px;
            font-weight: 600;
            color: #2d3436;
        }
        
        /* Responsive design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px 0;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .btn {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <h1>University Portal</h1>
            </div>
            
            <ul class="nav-links">
                <li><a href="#" class="active"><i>📊</i> Dashboard</a></li>
                <li><a href="../Registration/index.php"><i>📚</i> Register Courses</a></li>
                <li><a href="#"><i>📝</i> Grades</a></li>
                <li><a href="#"><i>👤</i> Profile</a></li>
                <li><a href="#"><i>⚙️</i> Settings</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome">
                    <h2>Welcome, <?php echo htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']); ?>!</h2>
                    <p>Here's your academic information at a glance</p>
                </div>
                
                <div class="user-actions">
                    <button class="btn btn-primary" onclick="registerCourses()">Register Courses</button>
                    <button class="btn btn-danger" onclick="logout()">Log Out</button>
                </div>
            </div>
            
            <div class="dashboard-cards">
                <!-- Student Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h3>Student Information</h3>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="info-label">Student ID:</span>
                            <span class="info-value">STU-<?php echo htmlspecialchars($student['Id']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($student['Email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total Registrations:</span>
                            <span class="info-value"><?php echo count($courses); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Academic Performance Card -->
                <div class="card">
                    <div class="card-header">
                        <h3>Academic Performance</h3>
                    </div>
                    <div class="card-content" style="display: flex; flex-direction: column; align-items: center;">
                        <div class="gpa-indicator">
                            <div class="gpa-value"><?php echo $gpa; ?></div>
                        </div>
                        <div class="gpa-label">Current GPA</div>
                        
                        <div style="margin-top: 20px; width: 100%;">
                            <div class="info-item">
                                <span class="info-label">Registered Courses:</span>
                                <span class="info-value"><?php echo count($courses); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Academic Standing:</span>
                                <span class="info-value">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Registered Courses Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Registered Courses</h3>
                </div>
                <div class="card-content">
                    <?php if (count($courses) > 0): ?>
                    <table class="courses-table">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Semester</th>
                                <th>Capacity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['Code']); ?></td>
                                <td><?php echo htmlspecialchars($course['Name']); ?></td>
                                <td><?php echo htmlspecialchars($course['Season'] . ' ' . $course['Year']); ?></td>
                                <td><?php echo htmlspecialchars($course['Capacity']); ?></td>
                                <td><span class="status-badge status-inprogress">Registered</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>You are not registered for any courses yet. <a href="../Registration/index.php">Register now</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "../Login/logout.php";
            }
        }
        
        function registerCourses() {
            window.location.href = "../Registration/index.php";
        }
    </script>
</body>
</html>