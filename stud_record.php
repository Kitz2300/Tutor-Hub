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
        <title>Records</title>
        <link href="stud_record.css" rel="stylesheet" type="text/css"/>
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
            <h1>Records</h1><br>
            </div>
                <table>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Task</th>
                        <th>Score</th>
                        <th>Items</th>
                        <th>Rating</th>
                    </tr>
                    <?php 
            $query = "SELECT tutor_info.name AS tutor_name,
                course.name AS course_name,
                records.task,
                records.score,
                records.items,
                records.rating
                FROM records
                INNER JOIN tutor_info ON records.tutor_id = tutor_info.tutor_id
                INNER JOIN course ON records.course_id = course.course_id
                WHERE records.student_id = $id";
            $result = mysqli_query($conn, $query);
            while($row=mysqli_fetch_assoc($result)) {
                $tutor_name = $row['tutor_name'];
                $course_name = $row['course_name'];
                $task = $row['task'];
                $score = $row['score'];
                $items = $row['items'];
                $rating = $row['rating'];?>
                    <tr>
                        <td><?=$tutor_name?></td>
                        <td><?=$course_name?></td>
                        <td><?=$task?></td>
                        <td><?=$score?></td>
                        <td><?=$items?></td>
                        <td><?=$rating?></td>
                    </tr>
                    <?php } ?>
                </table>
        </main>
        <?php  } ?>
    </body>
</html>