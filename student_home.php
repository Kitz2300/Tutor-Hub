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

if(isset($_POST['change_cred'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact_num'];

    if($name != "") {
        $change_sql = "UPDATE student_info SET name = '$name' WHERE student_id = $id";
        mysqli_query($conn, $change_sql);
        echo "<script>alert('Name Changed')</script>";
    }
    if($email != "") {
        $change_sql = "UPDATE student_info SET email = '$email' WHERE student_id = $id";
        mysqli_query($conn, $change_sql);
        echo "<script>alert('Email Changed')</script>";
    }
    if($contact != "") {
        $change_sql = "UPDATE student_info SET contact_num = '$contact' WHERE student_id = $id";
        mysqli_query($conn, $change_sql);
        echo "<script>alert('Contact Number Changed')</script>";
    }

    echo "<script>window.location.href='student_home.php'</script>";

}

if(isset($_POST['pass_change'])) {
    $password = $_POST['password'];

    if($password != "") {
        $pass_sql = "UPDATE student_info SET password = '$password' WHERE student_id = $id";
        mysqli_query($conn, $pass_sql);
        
        echo "<script>alert('Password Changed')</script>";
    }
}

if(isset($_POST['logout'])) {
    $_SESSION['student_id'] = 0;
    session_destroy();

    echo "<script>alert('Logged Out')</script>";
    echo "<script>window.location.href='student_login.php'</script>";

}

?>
<html>
    <head>
        <title>Student Homepage</title>
        <link rel="stylesheet" href="student_home.css" type="text/css"/>
        <link rel="icon" type="image/x-icon" href="icon.png"/>
    </head>
    <body>
        <?php if(!isset($_SESSION['student_id']) && !isset($_SESSION['student_name'])) {
            echo "<script>alert('you are not logged in')</script>";
            echo "<script>window.location.href='user_type.php'</script>";
        } else {?>
            <header>
                <img src="logo1.png" alt="Tutor Hub Logo" class="logo">
                <div class="head">
                    <nav>
                        <ul>
                            <li><a href="student_home.php">Homepage</a></li>
                            <li><a href="stud_schedule.php">Schedule</a></li>
                            <li><a href="stud_record.php">Records</a></li>
                            <li><a href="enrollment.php">Enroll</a></li>
                            <li>
                                <div class="logout">
                                    <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                                        <button name="logout"><strong>Log Out</strong></button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </nav>
                </div>
            </header>
            <main>

                <div class="profile">
                    <h1>Profile</h1>
                    <label for="name">Name:</label>
                    <?=$name?><br>
                    <label for="status">Status:</label>
                    <span>Student</span><br>
                    <label for="email">Email:</label>
                    <?=$email?><br>
                    <label for="contact">Contact #:</label>
                    <?=$contact?><br>

                    <form action="<?= $_SERVER['PHP_SELF']?>" method="POST">
                        <select name="choose">
                            <option></option>
                            <option value="show">Show Password</option>
                            <option value="change_pass">Change Password</option>
                            <option value="change_credentials">Change Credentials</option>
                        </select>
                        <button name="pick">Go</button>
                    </form>

                    <?php
                    if(isset($_POST['pick'])) {
                        $chosen = $_POST['choose'];

                        if($chosen == "show") {?>
                            <label for="password">Password:</label>
                            <?=$password?>
                    <?php  }
                        else if($chosen == "change_credentials") {?>
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                <input type="text" name="name" placeholder="Name"/><br>

                                <input type="email" name="email" placeholder="Email"/><br>

                                <input type="text" name="contact_num" placeholder="Contact #" maxlength="11" minlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/\D/, '')">


                                <button name="change_cred">Change Credentials</button>
                            </form>

                    <?php }
                        else if($chosen == "change_pass") {?>
                            <form action="<?= $_SERVER['PHP_SELF']?>" method="POST">
                                <input type="password" name="password" placeholder="Password" maxlength="16" minlength="8"/>
                                <button name="pass_change">Change Password</button>
                            </form>
                    <?php }
                    }
                    ?>
                </div>
                
                <div class="enrolled_courses">
                    <h1>Currently Enrolled Courses</h1>
                    <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                        <input type="text" name="search" placeholder="Search" required/>
                        <button name="searched">Search</button>
                    </form>
                    <?php 
                    $search = "";
                    if(isset($_POST['searched'])) {
                        $search = $_POST['search'];
                    }
                    $search = "SELECT 
                            tutor_info.name AS tutor_name, 
                            course.name AS course_name, 
                            enrolled.sessions, 
                            payment.amount, 
                            payment.due_date, 
                            payment.payment_status
                            FROM enrolled
                            INNER JOIN tutor_info ON enrolled.tutor_id = tutor_info.tutor_id
                            INNER JOIN course ON enrolled.course_id = course.course_id
                            INNER JOIN payment ON enrolled.student_id = payment.student_id 
                            AND enrolled.course_id = payment.course_id 
                            AND enrolled.tutor_id = payment.tutor_id WHERE enrolled.student_id = $id AND tutor_info.name LIKE '%$search%'";
                    $search_result = mysqli_query($conn, $search);

                    $query = "SELECT 
                            tutor_info.name AS tutor_name, 
                            course.name AS course_name, 
                            enrolled.sessions, 
                            payment.amount, 
                            payment.due_date, 
                            payment.payment_status
                            FROM enrolled
                            INNER JOIN tutor_info ON enrolled.tutor_id = tutor_info.tutor_id
                            INNER JOIN course ON enrolled.course_id = course.course_id
                            INNER JOIN payment ON enrolled.student_id = payment.student_id 
                            AND enrolled.course_id = payment.course_id 
                            AND enrolled.tutor_id = payment.tutor_id WHERE enrolled.student_id = $id;";
                    $query_result = mysqli_query($conn, $query);

                    if(isset($_POST['searched'])) {
                        $set_result = $search_result;
                    }
                    else {
                        $set_result = $query_result;
                    }?>
                    <table>
                        <tr>
                            <th>Tutor Name</th>
                            <th>Course</th>
                            <th>Sessions</th>
                            <th>Payment Amount</th>
                            <th>Payment Due Date</th>
                            <th>Payment Status</th>
                        </tr>
                        <?php 
                        while($row=mysqli_fetch_assoc($set_result)) {
                            $tutor_name = $row['tutor_name'];
                            $course_name = $row['course_name'];
                            $sessions = $row['sessions'];
                            $amount = $row['amount'];
                            $due_date = (String) $row['due_date'];
                            $payment_status = $row['payment_status'];
                            $due = "";
                            if($due_date == "0000-00-00") {
                                $due = "Not Set";
                            }
                            else {
                                $due = $due_date;
                            }
                            ?>

                        <tr>
                            <td><?=$tutor_name?></td>
                            <td><?=$course_name?></td>
                            <td><?=$sessions?></td>
                            <td><?=$amount?></td>
                            <td><?=$due?></td>
                            <td><?=$payment_status?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </main>

            
        <?php } ?>
    </body>
</html>