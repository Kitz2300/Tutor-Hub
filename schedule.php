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

if(isset($_POST['save'])) {
    $stud_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $start_hour = $_POST['start_hours'];
    $start_min = $_POST['start_min'];
    $end_hour = $_POST['end_hours'];
    $end_min = $_POST['end_min'];
    $day = $_POST['day'];
    $start_time = "";
    if(!empty($start_hour)) {
        $start_time = sprintf("%02d:%02d:00", $start_hour, $start_min);
        $start_query = "UPDATE schedule SET start_time = '$start_time' WHERE student_id = $stud_id AND course_id = $course_id";
        mysqli_query($conn, $start_query);
    }
    if(!empty($end_hour)) {
        $end_time = sprintf("%02d:%02d:00", $end_hour, $end_min);
        $end_query = "UPDATE schedule SET end_time = '$end_time' WHERE student_id = $stud_id AND course_id = $course_id";
        mysqli_query($conn, $end_query);
    }
    if(!empty($day)) {
        $day_query = "UPDATE schedule SET day = '$day' WHERE student_id = $stud_id AND course_id = $course_id";
        mysqli_query($conn, $day_query);
    }


}
?>

<html>
    <head>
        <title>Scheduling</title>
        <link href="schedule.css" rel="stylesheet" type="text/css"/>
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
            <h1>Schedule</h1><br>
            
                <div class="table">
                    <table>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Day</th>
                            <th></th>
                        </tr>
                        <?php 
            $query = "SELECT student_info.name AS student_name,
                    course.name AS course_name,
                    schedule.start_time,
                    schedule.end_time,
                    schedule.day,
                    schedule.student_id,
                    schedule.course_id
                    FROM schedule
                    INNER JOIN student_info ON schedule.student_id = student_info.student_id
                    INNER JOIN course ON schedule.course_id = course.course_id
                    WHERE schedule.tutor_id = $id";
            $result = mysqli_query($conn, $query);
            
            while($row = mysqli_fetch_assoc($result)) {
                $student_name = $row['student_name'];
                $student_id = $row['student_id'];
                $course_name = $row['course_name'];
                $course_id = $row['course_id'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $day = $row['day'];
                $start = $start_time == "00:00:00" ? "Not Set" : $start_time;
                $end = $end_time == "00:00:00" ? "Not Set" : $end_time;
                ?>
                        <tr>
                            <td><?= $student_name ?></td>
                            <td><?= $course_name ?></td>
                            <td>
                            <?= $start ?><br>
                            <form action="<?= $_SERVER['PHP_SELF']?>" method="POST">
                                <input type="number" name="start_hours" min="0" max="23" placeholder="hours"/>
                                <strong>:</strong> 
                                <input type="number" name="start_min" min="0" max="59" placeholder="min"/><br>
                            </td>
                            <td>
                            <?= $end ?><br>
                                <input type="number" name="end_hours" min="0" max="23" placeholder="hours"/>
                                <strong>:</strong> 
                                <input type="number" name="end_min" min="0" max="59" placeholder="min"/><br>

                            </td>
                            <td>
                            <?php
                                if($day == "") {?>
                                    not scheduled<br>
                            <?php 
                                }else{?>
                                    <?= $day ?><br>
                            <?php }?>
                                <div class="day">
                                    <input type="text" name="day"/>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" name="student_id" value="<?= $student_id?>"/>
                                <input type="hidden" name="course_id" value="<?= $course_id?>"/>
                                <div class="save_button">
                                    <button name="save">Save</button>
                                </div>
                            </td>
                            </form>
                        </tr>
                        
                        <?php } ?>
            </table>
        </div>
        </main>
        <?php  } ?>
    </body>
</html>