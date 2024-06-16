<?php
include __DIR__ . "/database.php";

session_start();

// Ensure $mysqli is defined
if (!isset($mysqli)) {
    die("Database connection error.");
}

if (isset($_SESSION["user_id"])) {
    $sql = "SELECT * FROM user WHERE id = {$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
}

// Fetch total number of courses
$totalCoursesQuery = "SELECT COUNT(*) as total FROM courses";
$totalCoursesResult = $mysqli->query($totalCoursesQuery);
$totalCoursesData = $totalCoursesResult->fetch_assoc();
$totalCourses = $totalCoursesData['total'];

// Fetch total number of subjects
$totalSubjectsQuery = "SELECT COUNT(*) as total FROM subjects";
$totalSubjectsResult = $mysqli->query($totalSubjectsQuery);
$totalSubjectsData = $totalSubjectsResult->fetch_assoc();
$totalSubjects = $totalSubjectsData['total'];

// Handle adding a new course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_course"])) {
    $course_id = $_POST["course_id"];
    $course_name = $_POST["course_name"];
    $insert_sql = "INSERT INTO courses (id, name, description) VALUES ('$course_id', '$course_name', '')";
    $mysqli->query($insert_sql);

    // Fetch total number of courses after adding new course
    $totalCoursesQuery = "SELECT COUNT(*) as total FROM courses";
    $totalCoursesResult = $mysqli->query($totalCoursesQuery);
    $totalCoursesData = $totalCoursesResult->fetch_assoc();
    $totalCourses = $totalCoursesData['total'];
}

// Handle deleting a course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_course"])) {
    $course_id = $_POST["course_id"];
    $delete_sql = "DELETE FROM courses WHERE id = '$course_id'";
    $mysqli->query($delete_sql);

    // Fetch total number of courses after deleting course
    $totalCoursesQuery = "SELECT COUNT(*) as total FROM courses";
    $totalCoursesResult = $mysqli->query($totalCoursesQuery);
    $totalCoursesData = $totalCoursesResult->fetch_assoc();
    $totalCourses = $totalCoursesData['total'];
}

// Handle adding a new subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_subject"])) {
    $subject_id = $_POST["subject_id"];
    $subject_name = $_POST["subject_name"];
    $insert_sql = "INSERT INTO subjects (id, name) VALUES ('$subject_id', '$subject_name')";
    $mysqli->query($insert_sql);

    // Fetch total number of subjects after adding new subject
    $totalSubjectsQuery = "SELECT COUNT(*) as total FROM subjects";
    $totalSubjectsResult = $mysqli->query($totalSubjectsQuery);
    $totalSubjectsData = $totalSubjectsResult->fetch_assoc();
    $totalSubjects = $totalSubjectsData['total'];
}

// Handle deleting a subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_subject"])) {
    $subject_id = $_POST["subject_id"];
    $delete_sql = "DELETE FROM subjects WHERE id = '$subject_id'";
    $mysqli->query($delete_sql);

    // Fetch total number of subjects after deleting subject
    $totalSubjectsQuery = "SELECT COUNT(*) as total FROM subjects";
    $totalSubjectsResult = $mysqli->query($totalSubjectsQuery);
    $totalSubjectsData = $totalSubjectsResult->fetch_assoc();
    $totalSubjects = $totalSubjectsData['total'];
}

// Fetch and display saved courses
$courses_query = "SELECT * FROM courses";
$courses_result = $mysqli->query($courses_query);
$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row;
}

// Fetch and display saved subjects
$subjects_query = "SELECT * FROM subjects";
$subjects_result = $mysqli->query($subjects_query);
$subjects = [];
while ($row = $subjects_result->fetch_assoc()) {
    $subjects[] = $row;
}

// Fetch and display saved students
$students_query = "SELECT * FROM students";
$students_result = $mysqli->query($students_query);
$students = [];
while ($row = $students_result->fetch_assoc()) {
    $students[] = $row;
}



// Function to display a message for adding a new student as an alert
function displayAddStudentMessage($message, $type = 'success') {
    echo "<div class='alert alert-$type' role='alert'>$message</div>";
    echo "<a href='index.php' class='btn btn-primary'>Add Another Student</a>";
} 
// Handle adding a new student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_student"])) {
    // Retrieve form data
    $student_name = $_POST["student_name"];
    $student_id = $_POST["student_id"];
    $student_address = $_POST["student_address"];
    $student_dob = $_POST["student_dob"];
    $student_email = $_POST["student_email"];

    // Insert student data into the students table
    $insert_student_sql = "INSERT INTO students (name, student_id, address, date_of_birth, email) 
                           VALUES ('$student_name', '$student_id', '$student_address', '$student_dob', '$student_email')";

    // Execute the SQL query
    if ($mysqli->query($insert_student_sql) === TRUE) {
         // Student added successfully
         $addStudentMessage = "Student added successfully: $student_name";
        displayAddStudentMessage($addStudentMessage, 'success');
        } else {
            // Error occurred while adding student
            $addStudentMessage = "Error adding student: " . $mysqli->error;
        displayAddStudentMessage($addStudentMessage, 'danger');
        }
    // Send JSON response
    echo json_encode($addStudentMessage);
    exit; // Stop further execution
}
// HTML code for displaying saved students
function displayStudents($students) {
    $counter = 1;
    foreach ($students as $student) {
        echo "<div>";
        echo "<label><b>NEW STUDENT" . $counter . "</b></label><br>";
        echo "<label><b>Name:</b></label> " . htmlspecialchars($student["name"]) . "<br>";
        echo "<label><b>Email:</b></label> " . htmlspecialchars($student["email"]) . "<br>";
        echo "<label><b>Date Of Birth:</b></label> " . htmlspecialchars($student["date_of_birth"]) . "<br>";
        echo "<label><b>Address:</b></label> " . htmlspecialchars($student["address"]) . "<br>";
        // Delete button for each student
        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' onsubmit='return confirm(\"Are you sure you want to delete Student #" . $counter . "?\");'>";
        echo "<input type='hidden' name='student_id' value='" . htmlspecialchars($student["student_id"]) . "'>";
        echo "<button type='submit' name='delete_student' style='font-size: 12px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        $counter++;
    }
}
// Handle deleting a student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_student"])) {
    $student_id = $_POST["student_id"];
    $delete_sql = "DELETE FROM students WHERE student_id = '$student_id'";
    if ($mysqli->query($delete_sql) === TRUE) {
        // Student deleted successfully
        $delete_message = "Student deleted successfully.";
    } else {
        // Error occurred while deleting student
        $delete_message = "Error: " . $mysqli->error;
    }
}
// Check if subjects data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subjects"])) {
    $enrolledSubjects = json_decode($_POST["subjects"]);

    // Prepare and execute SQL statement to insert enrolled subjects into the database
    $success = true;
    foreach ($enrolledSubjects as $subjectID) {
        $insert_sql = "INSERT INTO enrolled_subjects (subject_id) VALUES ('$subjectID')";
        if (!$mysqli->query($insert_sql)) {
            $success = false;
            break;
        }
    }

    // Prepare response
    $response = array("success" => $success);
    echo json_encode($response);
} 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
       body {
            display: flex;
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-image: url('image/bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0; 
        }

        h1 {
            margin-top: 300px;
            margin-left: 10px;
            margin-right:600px;
            margin-bottom: 300px;
            color: rgb(4, 201, 255); 
        }

        p {
            margin-bottom: 200px;
            margin-left: 300px;
            color: white;
            font-size: 2em;    
        }

       /* Style for tab menu */
.tab-menu {
    display: flex;
    list-style-type: none;
    padding: 0;
    position: absolute;
    top: 0;
    left: 0;
    margin-left: 500px;
    height: 30px;
    background-color: white;
    z-index: 1000; /* Ensures it's above other content */
}

.tab-menu li {
    background-color: white;
    position: relative; /* Ensure submenu positioning is relative to this */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0px 0px 10px rgba(0, 0, 255, 0.5); /* Add glow effect */
}

.tab-menu li a {
    text-decoration: none;
    color: white;
    font-weight: bold;
    padding: 10px;
    background-color: black;
    position: relative;
    border-radius: 10px; /* Match the border-radius of the parent li */
}

.tab-menu li a:hover {
    background-color: navy;
}

        /* Style for submenu */
        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001; /* Ensure it's above other content */
        }

        .submenu li {
            margin-right: 0;
        }

        .submenu li a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
        }

        .submenu li a:hover {
            background-color: navy;
        }

        /* Style for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        .modal-content {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* CSS for about panel */
        .about-section {
            display: flex;
            justify-content: center;
        }

        .about-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .about-image img {
            width: 100px; /* Adjust the size as needed */
            height: auto;
            border-radius: 10px;
        }

        .image-description {
            margin-top: 5px;
            position: relative;
            font-size: 24sp;
            color: black;
        }

  /* About panel specific styling */
.about-content {
    padding: 20px;
    /* Add glowing effect */
    box-shadow: 0 0 20px rgba(0, 255, 255, 0.8); /* Adjust color and opacity as needed */
    animation: glow 1.5s ease-in-out infinite alternate;
}

/* Glowing animation */
@keyframes glow {
    from {
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.8);
    }
    to {
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.8);
    }
}

/* Rest of the CSS remains the same */
.about-section {
    display: flex;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
}

.about-card {
    margin: 10px;
    text-align: center;
}

.about-card img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Add a subtle shadow effect */
}

.card-description {
    margin-top: 10px;
}

.card-description h3 {
    margin-bottom: 5px;
    font-size: 18px;
}

.card-description p {
    font-size: 14px;
    color: #555;
}
       
    /* Panel base style */
.modal-content {
    background-color: rgba(255, 255, 255, 0.9); /* White with 10% opacity */
    width: 100%;
    max-width: 1500px; /* Adjust the max-width as needed */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0px 0px 10px rgba(0, 0, 255, 0.5); /* Add glow effect */
}
        

        /* Panel for About */
        #aboutModal .modal-content {
            background-color: rgba(255, 255, 255, 0.9); /* White with 10% opacity */
            width: 100%;
            max-width: 1500px; /* Adjust the max-width as needed */
        }

        /* Panel for Course */
        #coursePanel .modal-content {
            background-color: rgba(255, 255, 255, 0.9); /* White with 10% opacity */
            width:fit-content;
        }

        /* Panel for Subject */
        #subjectPanel .modal-content {
            background-color: rgba(255, 255, 255, 0.9); /* White with 10% opacity */
            width:fit-content;
        }

        /* Panel for Students */
        #studentsPanel .modal-content {
            background-color: rgba(255, 255, 255, 0.9); /* White with 10% opacity */
            width:fit-content;
        }

        /* Responsive layout - makes the menu and the panel stack on top of each other instead of next to each other on smaller screens (600px wide or less) */
        @media screen and (max-width: 600px) {
            .tab-menu, .modal-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    
    <!-- Tab menu -->
    <?php if (isset($user)): ?>
        <ul class="tab-menu">
            <li><a href="#" id="setup-menu">SETUP</a>
                <ul class="submenu">
                    <li><a href="#" id="course-submenu">Course</a></li>
                    <li><a href="#" id="subject-submenu">Subject</a></li>
                    <li><a href="#" id="students-submenu">Students</a></li>
                </ul>
            </li>
            <li><a href="#" id="transaction-menu">Transaction</a>
                <ul class="submenu">
                    <li><a href="#" id="enrollment-submenu">Enrollment</a></li>
                </ul>
            </li>
            <li><a href="#" id="reports-menu">Reports</a>
                <ul class="submenu">
                    <li><a href="#" id="assessment-submenu">Assessment</a></li>
                </ul>
            </li>
            <li><a href="#" id="about-menu">ABOUT</a></li>
        </ul>
    <?php endif; ?>

    <h1>Home</h1>

    <?php if (isset($user)): ?>
        <p>Hello <?= htmlspecialchars($user["name"]) ?></p>
    <?php else: ?>
        <p><a href="login.php">Log in</a> or <a href="signup.html">sign up</a></p>
    <?php endif; ?>

    <!-- Modal for About -->
    <div id="aboutModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content about-content">
        <span class="close">&times;</span>
        <h2>About Us</h2>
        <p style="color: rgb(4, 201, 255);">Welcome to our website! Simple Login-Signin Enrollment Page</p>
        <!-- Description and images -->
        <div class="about-section">
            <!-- Individual information cards -->
            <div class="about-card">
                <img src="image/airon.png" alt="Airon P. Cammagay">
                <div class="card-description">
                    <h3>Airon P. Cammagay</h3>
                    <label>Ugad Cabagan Isabela<br>BSIT 2B</label>
                </div>
            </div>
            <div class="about-card">
                <img src="image/ed.png" alt="Eduardo Talaue III">
                <div class="card-description">
                    <h3>Eduardo Talaue III</h3>
                    <label>Catabayungan Cabagan Isabela<br>BSIT 2B</label>
                </div>
            </div>
            <div class="about-card">
                <img src="image/mj.png" alt="Marc Japhet Mabazza">
                <div class="card-description">
                    <h3>Marc Japhet Mabazza</h3>
                    <label>Ngarag Cabagan Isabela<br>BSIT 2B</label>
                </div>
            </div>
            
        </div>
        <a href="logout.php" style="color: red;">Log out</a>
    </div>
</div>

     <!-- Panel for Course -->
     <div id="coursePanel" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Course</h2>  
        <!-- Display saved courses -->
        <div id="saved-courses">
        <?php foreach ($courses as $index => $course): ?>
            <div>
                <label><b>Course <?php echo $index + 1; ?></b></label><br>    
                <label>Course Code:</label> <?php echo htmlspecialchars($course['id']); ?><br>
                <label>Course Name:</label> <?php echo htmlspecialchars($course['name']); ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['id']); ?>">
                    <button type="submit" name="delete_course" style="font-size: 12px;">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
        <!-- Add course form -->
        <button id="add-course-btn">Add</button>
        <form id="add-course-form" style="display: none;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <input type="text" name="course_id" placeholder="Course ID" required><br>
            <input type="text" name="course_name" placeholder="Course Name" required><br>
            <button type="submit" name="add_course">Save</button>
        </form>
    </div>
</div>
    <!-- Panel for Subject -->
    <div id="subjectPanel" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Subject</h2>  
        <!-- Display saved subjects -->
        <div id="saved-subjects">
            <?php foreach ($subjects as $index => $subject): ?>
                <div>
                    <label><b>Subject <?php echo $index + 1; ?></b></label><br>
                    <label>Subject Code:</label> <?php echo htmlspecialchars($subject['id']); ?><br>
                    <label>Subject Name:</label> <?php echo htmlspecialchars($subject['name']); ?>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject['id']); ?>">
                        <button type="submit" name="delete_subject" style="font-size: 12px;">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Add subject form -->
        <button id="add-subject-btn">Add Subject</button>
        <form id="add-subject-form" style="display: none;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="subject_id" placeholder="Subject ID" required><br>
            <input type="text" name="subject_name" placeholder="Subject Name" required><br>
            <button type="submit" name="add_subject">Save</button>
        </form>
    </div>
</div>

    <!-- Panel for Students -->
    <div id="studentsPanel" class="modal" style="background-color: rgba(0, 0, 0, 0.5);">
    <!-- Panel content -->
    <div class="modal-content" style="background-color: #fefefe;">
        <span class="close">&times;</span>
        <h2>Students Information</h2>
        <!-- Display existing user information -->
        <?php if (isset($user)): ?>
            <div>
                <h4>User Information<h4>
                <label><b>Name:</b></label> <?= htmlspecialchars($user["name"]) ?><br>
                <label><b>Student ID:</b></label> <?= htmlspecialchars($user["student_id"]) ?><br>
                <label><b>Address:</b></label> <?= htmlspecialchars($user["address"]) ?><br>
                <label><b>Date of Birth:</b></label> <?= htmlspecialchars($user["date_of_birth"]) ?><br>
                <label><b>Email:</b></label> <?= htmlspecialchars($user["email"]) ?><br>
            </div>
        <?php endif; ?>

        <?php displayStudents($students); ?>
        <!-- Add student form -->
        <button id="add-student-btn">Add Student</button>
        <form id="add-student-form" style="display: none;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="student_name" placeholder="Student Name" required><br>
            <input type="text" name="student_id" placeholder="Student ID" required><br>
            <input type="text" name="student_address" placeholder="Student Address" required><br>
            <input type="date" name="student_dob" placeholder="Date of Birth" required><br>
            <input type="email" name="student_email" placeholder="Email" required><br>
            <button type="submit" name="add_student">Save</button>
        </form>
        <div id="add-student-message" style="display: none; color: green;"></div>
    </div>
</div>

<!-- Panel for Enrollment -->
<div id="enrollmentPanel" class="modal" style="background-color: rgba(0, 0, 0, 0.5);">
    <!-- Panel content -->
    <div class="modal-content" style="background-color: #fefefe;">
        <span class="close">&times;</span>
        <h2 style="color: black;">Enrollment</h2>
        <h4 style="color: black;">2nd Semester SY 2023 - 2024</h4>
        
        <!-- Add a checklist alongside the table -->
        <div style="overflow-x:auto;">
            <!-- Table for enrollment -->
            <table id="enrollment-table">
                <thead>
                    <tr>
                        <th>Enroll</th>
                        <th>Subject Code</th>
                        <th>Subject Description</th>
                        <th>Units</th>
                    </tr>
                </thead>
                <tbody>
                     <!-- Sample row, add more rows dynamically -->
                     <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT GE ELEC 4</td>
                        <td>The Entrepreneurial Mind</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>GEC 9</td>
                        <td>The Life and Works of Rizal</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT 221</td>
                        <td>Information Management</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT 222</td>
                        <td>Networking 1</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT 223</td>
                        <td>Quantitative Methods (including Modeling and Simulation)</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT 224</td>
                        <td>Integrative Programming and Technologies</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT 225</td>
                        <td>Accounting for Information Technology</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>IT APPDEV 1</td>
                        <td>Fundamentals of Mobile Technology</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="enroll_1"></td>
                        <td>PE 4</td>
                        <td>Physical Activity Towards Health and Fitness IV</td>
                        <td>2</td>
                    </tr>
                    <!-- Display subjects from enrolled_subject table if available -->
                    <?php if (!empty($enrolled_subjects)): ?>
                        <?php foreach ($enrolled_subjects as $subject): ?>
                            <tr>
                                <td><input type="checkbox" name="enroll_subject[]" value="<?php echo htmlspecialchars($subject['id']); ?>"></td>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_description']); ?></td>
                                <td><?php echo htmlspecialchars($subject['units']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Enroll button -->
        <button id="enroll-btn">Enroll</button>
        <!-- Message for successful enrollment -->
        <p id="enrollment-message" style="color: green; display: none;">Enrollment successful!</p>

        <!-- Label for enrolled subjects -->
        <h3>Enrolled Subjects</h3>
        <div id="enrolled-subjects"></div>
    </div>
</div>

      
  <!-- Assessment panel content -->
<div id="assessmentPanel" class="modal">
    <!-- Panel content -->
    <div class="modal-content">

    <span class="close">&times;</span>
        <h2 style="color: black;">Enrolled Subject</h2>
        <h4 style="color: black;">2nd Semester SY 2023 - 2024</h4>
        
            <!-- Table for enrollment -->
            <table id="enrollment-table">
                
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Description</th>
                        <th>Units</th>
                    </tr>
                <tbody>
                     <!-- Sample row, add more rows dynamically -->
                     <tr>
                        <td>IT GE ELEC 4</td>
                        <td>The Entrepreneurial Mind</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>GEC 9</td>
                        <td>The Life and Works of Rizal</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>IT 221</td>
                        <td>Information Management</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>IT 222</td>
                        <td>Networking 1</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>IT 223</td>
                        <td>Quantitative Methods (including Modeling and Simulation)</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>IT 224</td>
                        <td>Integrative Programming and Technologies</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>IT 225</td>
                        <td>Accounting for Information Technology</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>IT APPDEV 1</td>
                        <td>Fundamentals of Mobile Technology</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>PE 4</td>
                        <td>Physical Activity Towards Health and Fitness IV</td>
                        <td>2</td>
                    </tr>
                </tbody>
            </table>

        <!-- Print button -->
        <button id="print-assessment-btn">Print</button>
        </div>

    <!-- JavaScript to toggle submenu and modal -->
    <script>
        // Function to toggle submenu
function toggleSubMenu(index) {
    var submenus = document.querySelectorAll('.submenu');
    submenus.forEach(function(submenu, i) {
        submenu.style.display = (i === index) ? 'block' : 'none';
    });
}
 // Event listener for Enrollment Submenu
 document.getElementById('enrollment-submenu').addEventListener('click', function() {
        document.getElementById('enrollmentPanel').style.display = 'block';
    });

    
// Event listeners for submenu toggling
document.getElementById('setup-menu').addEventListener('click', function() {
    toggleSubMenu(0);
});

document.getElementById('transaction-menu').addEventListener('click', function() {
    toggleSubMenu(1);
});

document.getElementById('reports-menu').addEventListener('click', function() {
    toggleSubMenu(2);
});

// Event listener to display the About modal
document.getElementById('about-menu').addEventListener('click', function() {
    var modal = document.getElementById('aboutModal');
    modal.style.display = 'block';
});

// Event listeners to display different panels
document.getElementById('course-submenu').addEventListener('click', function() {
    document.getElementById('coursePanel').style.display = 'block';
});

document.getElementById('subject-submenu').addEventListener('click', function() {
    document.getElementById('subjectPanel').style.display = 'block';
});

document.getElementById('students-submenu').addEventListener('click', function() {
    document.getElementById('studentsPanel').style.display = 'block';
});

document.getElementById('assessment-submenu').addEventListener('click', function() {
    document.getElementById('assessmentPanel').style.display = 'block';
});

  // Event listener for the print button
  document.getElementById('print-assessment-btn').addEventListener('click', function() {
        printAssessmentTable();
    });

    // Function to generate a printable version of the table and initiate download
    function printAssessmentTable() {
        // Clone the table
        var printableTable = document.getElementById('enrollment-table').cloneNode(true);

        // Modify the cloned table if needed (e.g., remove checkboxes)
        // Example: printableTable.querySelector('thead input[type="checkbox"]').remove();

        // Create a new document to hold the printable content
        var printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write('<html><head><title>This is a printed Assessment</title></head><body>');

        // Append the cloned table to the new document
        printWindow.document.write('<h2>Enrolled Subject</h2>');
        printWindow.document.write('<h4>2nd Semester SY 2023 - 2024</h4>');
        printWindow.document.write(printableTable.outerHTML);

        // Close the new document
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Print the document
        printWindow.print();
    }


// Close modals when close buttons are clicked
var closeButtons = document.querySelectorAll('.close');
closeButtons.forEach(function(closeButton) {
    closeButton.addEventListener('click', function() {
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            modal.style.display = 'none';
        });
    });
});

// Close modals when clicking outside of them
window.addEventListener('click', function(event) {
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});

// Function to toggle form visibility
function toggleFormVisibility(formId) {
    var form = document.getElementById(formId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
// Function to toggle form visibility
function toggleFormVisibility(formId) {
    var form = document.getElementById(formId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

// Event listeners to toggle add course, add subject, and add student forms
document.getElementById('add-course-btn').addEventListener('click', function() {
    toggleFormVisibility('add-course-form');
});

document.getElementById('add-subject-btn').addEventListener('click', function() {
    toggleFormVisibility('add-subject-form');
});

// Event listener to toggle add student form visibility
document.getElementById('add-student-btn').addEventListener('click', function() {
    toggleFormVisibility('add-student-form');
});
// Event listener for enroll button
document.getElementById('enroll-btn').addEventListener('click', function() {
    // Display a message indicating successful enrollment
    document.getElementById('enrollment-message').innerText = 'You are now enrolled!';
    document.getElementById('enrollment-message').style.display = 'block';

    // Optionally, you can hide the message after a certain period of time
    setTimeout(function() {
        document.getElementById('enrollment-message').style.display = 'none';
    }, 3000); // Hide the message after 3 seconds (3000 milliseconds)
});
// Function to handle enrollment
function handleEnrollment() {
    // Get the checked subjects
    var checkedSubjects = document.querySelectorAll('input[name="enroll_subject[]"]:checked');

    // Prepare an array to store the subject IDs
    var subjectIDs = [];
    checkedSubjects.forEach(function(subject) {
        subjectIDs.push(subject.value);
    });

    // Send the enrolled subjects to the server
    fetch('enroll_subject.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ subjects: subjectIDs })
    })
    .then(response => response.json())
    .then(data => {
        // Handle the server's response
        if (data.success) {
            // Display a success message or response
            document.getElementById('enrollment-message').style.display = 'block';
            document.getElementById('enrollment-message').innerText = 'Enrollment successful!';
            // Optionally, you can update the UI or display a success message
        } else {
            // Handle errors
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>


</body>
</html>