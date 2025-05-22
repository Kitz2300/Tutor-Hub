<?php
include "conn.php";
session_start();

$id = 0;
$name = '';
$email = '';
$password = '';
$contact = '';

if(isset($_SESSION['tutor_id'])) {

    $id = $_SESSION['tutor_id'];
    $sql = "SELECT * FROM tutor_info WHERE tutor_id = $id";
    $result = mysqli_query($conn, $sql);
    
    while($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $contact = $row['contact_num'];
    }
}
?>

<html>
    <head>
        <title>Apply Course</title>
        <link href="select_course.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/x-icon" href="icon.png"/>
    </head>
    <body>
    <?php if(!isset($_SESSION['tutor_id']) && !isset($_SESSION['tutor_name'])) {
            echo "<script>alert('you are not logged in')</script>";
            echo "<script>window.location.href='user_type.php'</script>";
        } else { ?>
        <header>
            <img src="logo1.png" alt="tutor hub logo" class="logo"/>
            <div class="head">
                <nav>
                    <ul>
                        <li><a href="tutor_home.php">Homepage</a></li>
                        <li><a href="schedule.php">Schedule</a></li>
                        <li><a href="record.php">Records</a></li>
                        <li><a href="select_course.php">Apply to Teach</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main>
            <div class="select_course">
            <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                <h2>Apply to Teach:</h2>
                <select name="courses" required>
                    <option>Select a Course</option>
                    <?php 
                    $courses = "SELECT * FROM course";
                    $course_result = mysqli_query($conn, $courses);
                    while($row=mysqli_fetch_assoc($course_result)) {
                        $name = $row['name'];
                        echo "<option name='$name'>$name</option>";
                    }
                    ?>
                </select><br>
                <input type="number" name="rate" placeholder="Rate per Session" required/>

                <button name="add">Add to Profile</button>
            </form>

            <?php
            if(isset($_POST['add'])) {
                if(isset($_POST['courses'])) {
                    $course = $_POST['courses'];
                    $rate = $_POST['rate'];
                    $course_id = 0;
                    $sql_id = "SELECT course_id FROM course WHERE name = '$course'";
                    $id_result = mysqli_query($conn, $sql_id);
                    if($row=mysqli_fetch_assoc($id_result)) {
                        $course_id = $row['course_id'];
                    }
                    if($id != 0 && $course_id != 0 && $rate) {
                        $tut_course = "SELECT * FROM tutor_course WHERE tutor_id = $id";
                        $tut_course_result = mysqli_query($conn, $tut_course);
                        while($row=mysqli_fetch_assoc($tut_course_result)) {
                            $course_id2 = $row['course_id'];
                            if($course_id == $course_id2) {
                                echo "<span>Already have this course on your profile</span>";
                                return;
                            }
                        }
                        $insert = "INSERT INTO tutor_course(tutor_id, course_id, rate) VALUES($id, $course_id, $rate)";
                        mysqli_query($conn, $insert);
                        echo "<script>alert('Successfully added to profile')</script>";
                        echo "<script>window.location.href='tutor_home.php'</script>";
                    }
                }
            }
            ?>
            </div>
        </main>
        <?php } ?>
    </body>
</html>