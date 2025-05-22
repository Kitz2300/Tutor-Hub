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

if(isset($_POST['add_record'])) {
    $student_course = $_POST['student'];
    if (strpos($student_course, '|') !== false) {  // make sure | exists
        list($student_id, $course_id) = explode("|", $student_course);
    } else {
        $student_id = 0;
        $course_id = 0;
    }
    $task = $_POST['task'];
    $score = $_POST['score'];
    $item = $_POST['items'];
    $rating = $_POST['rating'];


    if($student_id != 0 && $course_id != 0 && $task != "" && $score != 0 && $item != 0 && $rating != "") {
        $record_query = "INSERT INTO records(student_id, tutor_id, course_id, task, score, items, rating) VALUES($student_id, $id, $course_id, '$task', $score, $item, '$rating')";
        mysqli_query($conn, $record_query);

        echo "<script>alert('Record Added')</script>";
        echo "<script>window.location.href = 'record.php'</script>";
    }
    
}

if(isset($_POST['save'])) {
    $record_id = $_POST['record_id'];
    $task = $_POST['task_edit'];
    $score = $_POST['score_edit'];
    $items = $_POST['items_edit'];
    $rating = $_POST['rating_edit'];
    if($task != "") {
        $query = "UPDATE records SET task = '$task' WHERE record_id = $record_id";
        mysqli_query($conn, $query);

        echo "<script>alert('Successfully Updated')</script>";
        echo "<script>windows.location.href = 'record.php'</script>";
    }
    if($score != "") {
        $query = "UPDATE records SET score = $score WHERE record_id = $record_id";
        mysqli_query($conn, $query);

        echo "<script>alert('Successfully Updated')</script>";
        echo "<script>windows.location.href = 'record.php'</script>";
    }
    if($items != "") {
        $query = "UPDATE records SET items = $items WHERE record_id = $record_id";
        mysqli_query($conn, $query);

        echo "<script>alert('Successfully Updated')</script>";
        echo "<script>windows.location.href = 'record.php'</script>";
    }
    if($rating != "") {
        $query = "UPDATE records SET rating = '$rating' WHERE record_id = $record_id";
        mysqli_query($conn, $query);

        echo "<script>alert('Successfully Updated')</script>";
        echo "<script>windows.location.href = 'record.php'</script>";
    }

}

if(isset($_POST['remove'])) {
    $record_id = $_POST['record_id'];
    $query = "DELETE FROM records WHERE record_id = $record_id";
    mysqli_query($conn, $query);

    echo "<script>alert('Successfully Removed Record')</script>";
    echo "<script>windows.location.href = 'record.php'</script>";
    
}
?>

<html>
    <head>
        <title>Records</title>
        <link href="record.css" rel="stylesheet" type="text/css"/>
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
            <h2>Add and Grade Task:</h2><br>
            <div class="grading">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <select name="student" required>
                    <option value="">Student</option>
                        <?php
                        $student_query = "SELECT student_info.name AS student_name,
                        student_info.student_id,
                        course.name AS course_name,
                        course.course_id
                        FROM enrolled
                        INNER JOIN student_info ON enrolled.student_id = student_info.student_id
                        INNER JOIN course ON enrolled.course_id = course.course_id
                        WHERE enrolled.tutor_id = $id";
                        $result = mysqli_query($conn, $student_query);

                        while($row=mysqli_fetch_assoc($result)) {
                            $student = $row['student_name'];
                            $course = $row['course_name'];?>
                            <option value="<?=$row['student_id']?>|<?=$row['course_id']?>"><?=$student?> - <?=$course?></option>

                        <?php } ?>
                    </select>
                    <input type="text" name="task" placeholder="Task" required/><br>
                    <input type="number" name="score" placeholder="Score" required/>
                    <input type="number" name="items" placeholder="Items" required/><br>
                    <select name="rating" required>
                        <option value="">Rating</option>
                        <option value="Passed">Passed</option>
                        <option value="Fail">Fail</option>
                    </select><br>
                    
                    <button name="add_record">Add</button>
                </form>
            </div>
            <div class="records_table">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                   <input type="text" name="search" placeholder="Search" required/>
                   <button name="searched">Search</button>
               </form>
                <table>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Task</th>
                        <th>Score</th>
                        <th>Items</th>
                        <th>Rating</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php 
            $query = "SELECT student_info.name AS student_name,
                course.name AS course_name,
                records.record_id,
                records.task,
                records.score,
                records.items,
                records.rating
                FROM records
                INNER JOIN student_info ON records.student_id = student_info.student_id
                INNER JOIN course ON records.course_id = course.course_id
                WHERE records.tutor_id = $id";
            $result = mysqli_query($conn, $query);
            while($row=mysqli_fetch_assoc($result)) {
                $record_id = $row['record_id'];
                $student_name = $row['student_name'];
                $course_name = $row['course_name'];
                $task = $row['task'];
                $score = $row['score'];
                $items = $row['items'];
                $rating = $row['rating'];?>
                    <tr>
                        <td><?=$student_name?></td>
                        <td><?=$course_name?></td>
                    <form action="<?= $_SERVER['PHP_SELF']?>" method="POST">
                        <td>
                            <?=$task?><br>
                            <!-- Editing Task -->
                            <input type="text" placeholder="Task" name="task_edit" class="task"/>
                            <input type="hidden" value="<?= $record_id ?>" name="record_id"/>    
                        </td>
                        <td>
                            <?=$score?><br>
                            <!-- Editing Score -->
                            <input type="number" placeholder="Score" name="score_edit" class="score"/>
                        </td>
                        <td>
                            <?=$items?><br>
                            <!-- Editing Score -->
                            <input type="number" placeholder="Items" name="items_edit" class="items"/>
                        </td>
                        <td>
                            <?=$rating?><br>
                            <!-- Editing Rating -->
                             <select name="rating_edit" class="rating">
                                <option></option>
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                             </select>
                        </td>
                        <td>
                            <!-- save edit button -->
                             <div class="save">
                                 <button name="save">Save</button>
                             </div>
                        </td>
                        <td>
                            <!-- remove record button -->
                             <div class="remove">
                                 <button name="remove">Remove</button>
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