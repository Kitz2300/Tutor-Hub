<?php
include "conn.php";
session_start();

$id = 0;
$name = '';
$email = '';
$password = '';
$contact = '';

// Check if logged in
if (isset($_SESSION['tutor_id'])) {
    $id = $_SESSION['tutor_id'];
    $sql = "SELECT * FROM tutor_info WHERE tutor_id = $id";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $contact = $row['contact_num'];
    }
} else {
    echo "<script>alert('You are not logged in'); window.location.href='user_type.php';</script>";
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    echo "<script>alert('Logged Out'); window.location.href='tutor_login.php';</script>";
    exit();
}

// Handle credentials update
if (isset($_POST['change_cred'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact_num'];

    if ($name != "") {
        mysqli_query($conn, "UPDATE tutor_info SET name = '$name' WHERE tutor_id = $id");
        echo "<script>alert('Name Changed');</script>";
    }
    if ($email != "") {
        mysqli_query($conn, "UPDATE tutor_info SET email = '$email' WHERE tutor_id = $id");
        echo "<script>alert('Email Changed');</script>";
    }
    if ($contact != "") {
        mysqli_query($conn, "UPDATE tutor_info SET contact_num = '$contact' WHERE tutor_id = $id");
        echo "<script>alert('Contact Number Changed');</script>";
    }

    echo "<script>window.location.reload();</script>";
    exit();
}

// Handle password change
if (isset($_POST['pass_change'])) {
    $password = $_POST['password'];

    if ($password != "") {
        mysqli_query($conn, "UPDATE tutor_info SET password = '$password' WHERE tutor_id = $id");
        echo "<script>alert('Password Changed');</script>";
    }
}

// Handle remove student
if (isset($_POST['remove'])) {
    $id_enroll = $_POST['id_enroll'];
    $id_billing = $_POST['id_billing'];

    $res = mysqli_query($conn, "SELECT student_id, course_id FROM enrolled WHERE enroll_id = $id_enroll");
    if ($row = mysqli_fetch_assoc($res)) {
        $student_id = $row['student_id'];
        $course_id = $row['course_id'];

        mysqli_query($conn, "DELETE FROM enrolled WHERE enroll_id = $id_enroll");
        mysqli_query($conn, "DELETE FROM payment WHERE billing_id = $id_billing");
        mysqli_query($conn, "DELETE FROM schedule WHERE tutor_id = $id AND student_id = $student_id AND course_id = $course_id");
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle save changes
if (isset($_POST['save'])) {
    $id_billing = $_POST['id_billing'];
    $status = $_POST['status'];
    $due = $_POST['due_date'];

    if (!empty($status)) {
        mysqli_query($conn, "UPDATE payment SET payment_status = '$status' WHERE billing_id = $id_billing");
    }

    if (!empty($due)) {
        mysqli_query($conn, "UPDATE payment SET due_date = '$due' WHERE billing_id = $id_billing");
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<html>
<head>
    <title>Tutor Homepage</title>
    <link rel="stylesheet" href="tutor_home.css" type="text/css"/>
    <link rel="icon" type="image/x-icon" href="icon.png"/>
</head>

<body>
<header>
    <img src="logo1.png" alt="Tutor Hub Logo" class="logo">
    <div class="head">
        <nav>
            <ul>
                <li><a href="tutor_home.php">Homepage</a></li>
                <li><a href="schedule.php">Schedule</a></li>
                <li><a href="record.php">Records</a></li>
                <li><a href="select_course.php">Apply for a Course</a></li>
                <li>
                    <div class="logout">
                        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
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
        <label>Name:</label> <?= $name ?><br>
        <label>Status:</label> Tutor<br>
        <label>Email:</label> <?= $email ?><br>
        <label>Contact #:</label> <?= $contact ?><br>

        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <select name="choose">
                <option></option>
                <option value="show">Show Password</option>
                <option value="change_pass">Change Password</option>
                <option value="change_credentials">Change Credentials</option>
                <option value="view">Tutored Courses</option>
            </select>
            <button name="pick">Go</button>
        </form>

        <?php
        if (isset($_POST['pick'])) {
            $chosen = $_POST['choose'];

            #show the password of the user
            if ($chosen == "show") {
                echo "<label>Password:</label> $password";
            } 
            # change the credentials of the user
            else if ($chosen == "change_credentials") {
                echo '
                <form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                    <input type="text" name="name" placeholder="Name"/><br>
                    <input type="email" name="email" placeholder="Email"/><br>
                    <input type="text" name="contact_num" placeholder="Contact #" maxlength="11" minlength="11" pattern="\d{11}" required oninput="this.value = this.value.replace(/\D/, \'\')">
                    <button name="change_cred">Change Credentials</button>
                </form>';
            } 
            # change password of the user
            else if ($chosen == "change_pass") {
                echo '
                <form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                    <input type="password" name="password" placeholder="New Password"/>
                    <button name="pass_change">Change Password</button>
                </form>';
            } 
            # View the Courses the tutor handles
            else if ($chosen == "view") {
                $query = "SELECT course.name AS course_name 
                FROM course 
                INNER JOIN tutor_course ON course.course_id = tutor_course.course_id 
                WHERE tutor_id = $id";
                $result = mysqli_query($conn, $query);
            echo "<ul>";
                while($row=mysqli_fetch_assoc($result)) {
                    $course_name = $row['course_name'];?>
                        <li><?= $course_name ?></li>
            <?php   }
            echo "</ul>";
            }
        }
        ?>
    </div>

    <div class="tutored_courses">
        <h1>Currently Tutored Students</h1>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <input type="text" name="search" placeholder="Search" required/>
            <button name="searched">Search</button>
        </form>

        <?php
        $search = "";
        if (isset($_POST['searched'])) {
            $search = $_POST['search'];
        }

        $query_base = "SELECT 
            student_info.name AS student_name, 
            course.name AS course_name, 
            enrolled.sessions,
            enrolled.enroll_id,
            enrolled.student_id,
            enrolled.tutor_id,
            enrolled.course_id,
            payment.billing_id,
            payment.amount, 
            payment.due_date, 
            payment.payment_status
            FROM enrolled
            INNER JOIN student_info ON enrolled.student_id = student_info.student_id
            INNER JOIN course ON enrolled.course_id = course.course_id
            INNER JOIN payment ON enrolled.student_id = payment.student_id 
            AND enrolled.course_id = payment.course_id 
            AND enrolled.student_id = payment.student_id 
            WHERE enrolled.tutor_id = $id";

        if (!empty($search)) {
            $query_base .= " AND student_info.name LIKE '%$search%'";
        }

        $result = mysqli_query($conn, $query_base);?>

            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Sessions</th>
                    <th>Payment Amount</th>
                    <th>Payment Due Date</th>
                    <th>Payment Status</th>
                    <th></th>
                    <th></th>
                </tr>
                <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        $student_name = $row['student_name'];
                        $course_name = $row['course_name'];
                        $sessions = $row['sessions'];
                        $enroll_id = $row['enroll_id'];
                        $billing_id = $row['billing_id'];
                        $amount = $row['amount'];
                        $due_date = $row['due_date'];
                        $payment_status = $row['payment_status'];
                        $tutor_id = $row['tutor_id'];
                        $student_id = $row['student_id'];
                        $course_id = $row['course_id'];?>
                <tr>
                    <td><?=$student_name?></td>
                    <td><?=$course_name?></td>
                    <td><?=$sessions?></td>
                    <td><?=$amount?></td>
                    <form action='" . $_SERVER['PHP_SELF'] . "' method='POST'>
                        <td>
                            <?=$due_date?><br>
                            <!-- edit due date -->
                            <input type='hidden' name='id_billing' value='$billing_id'/>
                            <input type='date' name='due_date'/>
                        </td>
                        <td>
                            <?=$payment_status?><br>
                            <!-- edit payment status -->
                            <select name='status'>
                                <option></option>
                                <option value='Paid'>Paid</option>
                                <option value='Unpaid'>Unpaid</option>
                            </select>    
                        </td>
                        <!-- Save button -->
                        <td><button name='save' class='save'>Save</button></td>
                    </form>
                    <form action='" . $_SERVER['PHP_SELF'] . "' method='POST'>
                        <td>
                            <!-- remove button -->
                            <div class='remove'>
                                    <input type='hidden' name='id_enroll' value='$enroll_id'/>
                                    <input type='hidden' name='id_billing' value='$billing_id'/>
                                    <button name='remove'>Remove</button>
                            </div>
                        </td>
                    </form>
                </tr>
                <?php } ?>
            </table>
    </div>
</main>
</body>
</html>
