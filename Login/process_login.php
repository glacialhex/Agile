<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "school_system";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['username']; 
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT password_hash FROM student WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            session_start();
            
            $stmt2 = $conn->prepare("SELECT Id FROM student WHERE email = ?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $stmt2->bind_result($student_id);
            $stmt2->fetch();
            
            $_SESSION['student_id'] = $student_id;
            $_SESSION['email'] = $email;
            
            $stmt2->close();
            
            header("Location: ../Dashboard/index.php");
            exit();
        } else {
            header("Location: index.php?error=Invalid+email+or+password");
            exit();
        }
    } else {
        header("Location: index.php?error=Invalid+email+or+password");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>
