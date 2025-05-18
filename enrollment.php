<?php
include "conn.php";
session_start();

$id = 0;
$name = '';
$email = '';
$password = '';
$contact = '';

if(isset($_SESSION['student_id'])) {
    $id = $_SESSION['student_id'];
    $sql = "SELECT * FROM student_info WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $contact = $row['contact_num'];
    }
}
?>

<html>
<head>
    <title>Enrollment</title>
    <link href="enrollment.css" rel="stylesheet" type="text/css"/>
    <link rel="icon" type="image/x-icon" href="icon.png"/>
</head>
<body>

<?php if(!isset($_SESSION['student_id'])) { ?>
    <span>You are not Logged In</span>
    <a href='user_type.php'>Log In!</a>
<?php } else { ?>
    <header>
        <img src="logo1.png" alt="tutor hub logo" class="logo"/>
        <div class="head">
            <nav>
                <ul>
                    <li><a href="student_home.php">Homepage</a></li>
                    <li><a href="stud_schedule.php">Schedule</a></li>
                    <li><a href="stud_record.php">Records</a></li>
                    <li><a href="enrollment.php">Enroll</a></li>
                </ul>
            </nav>
        </div>
    </header>
        <main>
            <div class="enroll">

                <h1>Enrollment</h1>
                <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                    <h3>Pick a course you want to enroll in:</h3>
                    <select name="courses" required>
                        <option value="">Select a course</option>
                        <?php 
                            $courses = "SELECT * FROM course";
                            $result_courses = mysqli_query($conn, $courses);
                            while($row = mysqli_fetch_assoc($result_courses)) {
                                echo "<option value='".$row['name']."'>".$row['name']."</option>";
                            }
                        ?>
                    </select>
                    <button name="select">Select Course</button>
                </form>
            
                <?php 
                if(isset($_POST['select']) && isset($_POST['courses']) && $_POST['courses'] != "") {
                    $course = $_POST['courses'];
                    $rate = 0;
                ?>
                    <h2>Available Tutors for the course "<?=$course?>"</h2>
                    <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                        <select name="tutor" required>
                            <option value="">Select a tutor</option>
                            <?php
                                $tutor_sql = "SELECT tutor_info.name AS tutor_name, tutor_course.rate
                                            FROM tutor_info 
                                            INNER JOIN tutor_course ON tutor_info.tutor_id = tutor_course.tutor_id 
                                            INNER JOIN course ON tutor_course.course_id = course.course_id 
                                            WHERE course.name = ?";
                                
                                $stmt = mysqli_prepare($conn, $tutor_sql);
                                mysqli_stmt_bind_param($stmt, "s", $course);
                                mysqli_stmt_execute($stmt);
                                $tutor_result = mysqli_stmt_get_result($stmt);
                                while($row = mysqli_fetch_assoc($tutor_result)) {
                                    $rate = (int) $row['rate'];
                                    echo "<option value='".$row['tutor_name']."'>".$row['tutor_name']." - ".$rate."PHP</option>";
                                }
                            ?>
                        </select><br>
                        <input type="hidden" name="course" value="<?=$course?>">
                        <input type="number" name="sessions" placeholder="Number of sessions" required min="1"/><br>
                        <button name="enroll">Enroll</button>
                    </form>
                <?php
                }
            
                if(isset($_POST['enroll']) && isset($_POST['tutor']) && isset($_POST['sessions']) && isset($_POST['course'])) {
                    $tutor_name = $_POST['tutor'];
                    $sessions = (int) $_POST['sessions'];
                    $course = $_POST['course'];
            
                    // Get course_id
                    $course_id = 0;
                    $course_query = "SELECT course_id FROM course WHERE name = ?";
                    $stmt = mysqli_prepare($conn, $course_query);
                    mysqli_stmt_bind_param($stmt, "s", $course);
                    mysqli_stmt_execute($stmt);
                    $course_result = mysqli_stmt_get_result($stmt);
                    if($row = mysqli_fetch_assoc($course_result)) {
                        $course_id = $row['course_id'];
                    }
            
                    // Get tutor_id
                    $tutor_id = 0;
                    $tutor_query = "SELECT tutor_id FROM tutor_info WHERE name = ?";
                    $stmt = mysqli_prepare($conn, $tutor_query);
                    mysqli_stmt_bind_param($stmt, "s", $tutor_name);
                    mysqli_stmt_execute($stmt);
                    $tutor_result = mysqli_stmt_get_result($stmt);
                    if($row = mysqli_fetch_assoc($tutor_result)) {
                        $tutor_id = $row['tutor_id'];
                    }
            
                    // Fetch the correct rate from tutor_course
                    $rate_query = "SELECT rate FROM tutor_course WHERE tutor_id = ? AND course_id = ?";
                    $stmt = mysqli_prepare($conn, $rate_query);
                    mysqli_stmt_bind_param($stmt, "ii", $tutor_id, $course_id);
                    mysqli_stmt_execute($stmt);
                    $rate_result = mysqli_stmt_get_result($stmt);

                    $rate = 0;
                    if ($row = mysqli_fetch_assoc($rate_result)) {
                    $rate = (int) $row['rate'];
                    }
            
                    // Calculate amount
                    $amount = $rate * $sessions;
            
                    // Check if already enrolled in the course
                    $check_query = "SELECT * FROM enrolled WHERE student_id = ? AND course_id = ?";
                    $stmt = mysqli_prepare($conn, $check_query);
                    mysqli_stmt_bind_param($stmt, "ii", $id, $course_id);
                    mysqli_stmt_execute($stmt);
                    $check_result = mysqli_stmt_get_result($stmt);
                    
                    if(mysqli_num_rows($check_result) > 0) {
                        echo "<span>You are already enrolled in this course.</span>";
                    } else {
                        // Insert into enrolled table
                        $enroll_query = "INSERT INTO enrolled (tutor_id, student_id, course_id, sessions) VALUES (?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $enroll_query);
                        mysqli_stmt_bind_param($stmt, "iiii", $tutor_id, $id, $course_id, $sessions);
            
                        $payment_query = "INSERT INTO payment(tutor_id, student_id, course_id, amount, payment_status) VALUES(?, ?, ?, ?, 'Unpaid')";
                        $stmt2 = mysqli_prepare($conn, $payment_query);
                        mysqli_stmt_bind_param($stmt2, "iiii", $tutor_id, $id, $course_id, $amount);
                        
                        $session_query = "INSERT INTO schedule(tutor_id, student_id, course_id) VALUES(?, ?, ?)";
                        $stmt3 = mysqli_prepare($conn, $session_query);
                        mysqli_stmt_bind_param($stmt3, "iii", $tutor_id, $id, $course_id);

                        if(mysqli_stmt_execute($stmt) && mysqli_stmt_execute($stmt2) && mysqli_stmt_execute($stmt3)) {
                            echo "<script>alert('Successfully Enrolled');</script>";
                            echo "<script>window.location.href='student_home.php'</script>";
                        } else {
                            echo "<span>Enrollment failed. Please try again.</span>";
                        }
                    }
                }
                ?>
            </div>
        </main>
    <?php } ?>
    </body>
</html>
