<?php
require_once __DIR__ . '/../config/db.php';

session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: ../Login/index.php?error=Please+login+first');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$course_codes = $_POST['course_codes'] ?? [];

if (empty($course_codes)) {
    header('Location: index.php?error=Please+select+at+least+one+course');
    exit;
}

$conn->begin_transaction();

try {
    $success_count = 0;
    foreach ($course_codes as $course_code) {
        $course_stmt = $conn->prepare("SELECT Id, semester_id FROM course WHERE Code = ?");
        $course_stmt->bind_param("s", $course_code);
        $course_stmt->execute();
        $course_result = $course_stmt->get_result();

        if ($course_result->num_rows > 0) {
            $course_data = $course_result->fetch_assoc();
            $semester_id = $course_data['semester_id'];
            $course_id = $course_data['Id'];
            $stmt = $conn->prepare("INSERT INTO registration (student_id, semester_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $semester_id);
            
            if ($stmt->execute()) {
                $success_count++;
                $reg_id = $conn->insert_id;
                $regdata_stmt = $conn->prepare("INSERT INTO regdata (RegId, NumberOfCourses, RegisteredAt, grade, course_id) VALUES (?, 1, NOW(), '', ?)");
                $regdata_stmt->bind_param("ii", $reg_id, $course_id);
                $regdata_stmt->execute();
            }
        }
    }
    
    $conn->commit();
    
    if ($success_count > 0) {
        header('Location: index.php?success=1');
    } else {
        header('Location: index.php?error=No+courses+found+or+insertion+failed');
    }
    
} catch (Exception $e) {
    $conn->rollback();
    header('Location: index.php?error=Database+error:+' . urlencode($e->getMessage()));
}

exit;
?>

