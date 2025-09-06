<?php
require_once __DIR__ . '/../config/db.php';

// Fetch courses with semester info
$courses = [];
$result = $conn->query("
    SELECT c.Code, c.Name, s.id AS semester_id, CONCAT(s.Season, ' ', s.Year) as semester_name 
    FROM course c 
    JOIN semester s ON c.semester_id = s.id 
    ORDER BY c.Code
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Fetch all semesters for filtering
$semesters = [];
$result = $conn->query("
    SELECT id, CONCAT(Season, ' ', Year) as semester_name 
    FROM semester 
    ORDER BY Year DESC, FIELD(Season, 'Spring', 'Summer', 'Fall')
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Registration</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
         }
        .container { 
            display: flex; 
            gap: 20px; 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .panel { 
            background: #fff; 
            padding: 24px; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        .courses-panel { 
            flex: 2; 
        }
        .selected-panel { 
            flex: 1; 
        }
        .course-item { 
            padding: 12px; 
            margin: 8px 0; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            cursor: pointer; 
            transition: background 0.2s; 
        }
        .course-item:hover { 
            background: #f8f9fa; 
        }
        .course-item.selected { 
            background: #e7f3ff; 
            border-color: #007bff; 
        }
        .selected-course { 
            padding: 8px; 
            margin: 5px 0; 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            border-radius: 4px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .remove-btn { 
            background: #dc3545; 
            color: white; 
            border: none; 
            border-radius: 3px; 
            padding: 4px 8px; 
            cursor: pointer; 
            font-size: 12px; 
        }
        .remove-btn:hover { 
            background: #c82333; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #28a745; 
            color: #fff; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
        }
        button:hover { 
            background: #218838; 
        }
        .msg { 
            text-align: center; 
            margin-bottom: 15px; 
            padding: 12px;
            border-radius: 6px;
            font-weight: 500;
        }
        .msg.success { 
            color: #155724; 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
        }
        .msg.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        h2, h3 { 
            margin-top: 0; 
        }
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        #semesterFilter {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Available Courses Panel -->
        <div class="panel courses-panel">
            <div class="filter-section">
                <h2 style="margin: 0;">Available Courses</h2>
                <select id="semesterFilter" onchange="filterCourses()">
                    <option value="">All Semesters</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester['id']; ?>"><?php echo htmlspecialchars($semester['semester_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="msg success">Registration done successfully</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="msg error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <div id="coursesList">
                <?php foreach ($courses as $course): ?>
                    <div class="course-item" 
                         data-code="<?php echo htmlspecialchars($course['Code']); ?>"
                         data-name="<?php echo htmlspecialchars($course['Name']); ?>"
                         data-semester="<?php echo htmlspecialchars($course['semester_name']); ?>"
                         data-semester-id="<?php echo (int)$course['semester_id']; ?>"
                         onclick="addCourse(this)">
                        <strong><?php echo htmlspecialchars($course['Code']); ?></strong> - 
                        <?php echo htmlspecialchars($course['Name']); ?>
                        <br><small><?php echo htmlspecialchars($course['semester_name']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Selected Courses Panel -->
        <div class="panel selected-panel">
            <h3>Selected Courses</h3>
            
            <div id="selectedCourses">
                <p id="noSelection" style="color: #666; font-style: italic;">No courses selected</p>
            </div>

            <form action="regpage.php" method="POST" id="registrationForm" style="margin-top: 20px;">
                <button type="submit">Register Selected Courses</button>
            </form>
            
            <p style="text-align:center; margin-top:15px;">
                <a href="../Dashboard/index.php">Go to Dashboard</a>
            </p>
        </div>
    </div>

    <script>
        let selectedCourses = [];

        function addCourse(element) {
            const code = element.dataset.code;
            const name = element.dataset.name;
            const semester = element.dataset.semester;
            
            // Check if already selected and thea if so, remove it
            const existingIndex = selectedCourses.findIndex(course => course.code === code);
            if (existingIndex !== -1) {
                // Remove from selected courses
                selectedCourses.splice(existingIndex, 1);
                
                // Remove visual selection
                element.classList.remove('selected');
                
                // Update selected panel
                updateSelectedPanel();
                return;
            }
            
            // Add to selected courses
            selectedCourses.push({code, name, semester});
            
            // Mark as selected visually
            element.classList.add('selected');
            
            // Update selected panel
            updateSelectedPanel();
        }

        function removeCourse(code) {
            // Remove from array
            selectedCourses = selectedCourses.filter(course => course.code !== code);
            
            // Remove visual selection
            const courseElement = document.querySelector(`[data-code="${code}"]`);
            if (courseElement) {
                courseElement.classList.remove('selected');
            }
            
            // Update selected panel
            updateSelectedPanel();
        }

        function updateSelectedPanel() {
            const container = document.getElementById('selectedCourses');
            const noSelection = document.getElementById('noSelection');
            
            if (selectedCourses.length === 0) {
                container.innerHTML = '<p id="noSelection" style="color: #666; font-style: italic;">No courses selected</p>';
                updateFormData();
                return;
            }
            
            let html = '';
            selectedCourses.forEach(course => {
                html += `
                    <div class="selected-course">
                        <div>
                            <strong>${course.code}</strong> - ${course.name}
                            <br><small>${course.semester}</small>
                        </div>
                        <button type="button" class="remove-btn" onclick="removeCourse('${course.code}')">Remove</button>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            updateFormData();
        }

        function updateFormData() {
            const form = document.getElementById('registrationForm');
            
            // Remove existing course inputs
            const existingInputs = form.querySelectorAll('input[name="course_codes[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Add hidden inputs for selected courses
            selectedCourses.forEach(course => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'course_codes[]';
                input.value = course.code;
                form.appendChild(input);
            });
        }

        function filterCourses() {
            const filterValue = document.getElementById('semesterFilter').value;
            const courseItems = document.querySelectorAll('.course-item');

            courseItems.forEach(item => {
                const itemSemester = item.dataset.semesterId || '';
                if (filterValue === '' || itemSemester === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
