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
    $sql = "SELECT * FROM student_info WHERE student_id = $id";
    $result = mysqli_query($conn, $sql);
    
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
        <title>Schedule</title>
        <link href="stud_schedule.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/x-icon" href="icon.png"/>
    </head>
    <body>
    <?php if(!isset($_SESSION['student_id']) && !isset($_SESSION['student_name'])) {
            echo "<script>alert('you are not logged in')</script>";
            echo "<script>window.location.href='user_type.php'</script>";
        } else { ?>
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
            <h1>Schedule</h1><br>
            
                <div class="table">
                    <table>
                        <tr>
                            <th>Tutor Name</th>
                            <th>Course</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Day</th>
                        </tr>
                        <?php 
            $query = "SELECT tutor_info.name AS tutor_name,
                    course.name AS course_name,
                    schedule.start_time,
                    schedule.end_time,
                    schedule.day,
                    schedule.tutor_id,
                    schedule.course_id
                    FROM schedule
                    INNER JOIN tutor_info ON schedule.tutor_id = tutor_info.tutor_id
                    INNER JOIN course ON schedule.course_id = course.course_id
                    WHERE schedule.student_id = $id";
            $result = mysqli_query($conn, $query);
            
            while($row = mysqli_fetch_assoc($result)) {
                $tutor_name = $row['tutor_name'];
                $tutor_id = $row['tutor_id'];
                $course_name = $row['course_name'];
                $course_id = $row['course_id'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $day = $row['day'];?>
                        <tr>
                            <td><?= $tutor_name ?></td>
                            <td><?= $course_name ?></td>
                            <td><?= $start_time ?></td>
                            <td><?= $end_time ?></td>
                            <td>
                            <?php
                                if($day == "") {?>
                                    not scheduled<br>
                            <?php 
                                }else{?>
                                    <?= $day ?><br>
                            <?php }?>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
        </div>
        </main>
        <?php  } ?>
    </body>
</html>