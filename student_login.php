<?php
include 'conn.php';
session_start();

$correctCredentials = false; 
$submitted = false; 

if (isset($_POST['login'])) {
    $submitted = true; 

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM student_info WHERE email = '$email' AND password = '$password'";

    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['student_id'];
        $name = $row['name'];

        $correctCredentials = true;

        $_SESSION['student_name'] = $name;
        $_SESSION['student_id'] = $id;
    }
}
?>

<html>
    <head>
        <title>Log In</title>
        <link href="student_login.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/x-icon" href="icon.png"/>
    </head>
    <body>
        <main>
            <div class="login">
                <img src="logo1.png" alt="tutor hub logo" class="logo"/>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                    <h2>Student Log-In</h2>

                    <input placeholder="Email" type="email" name="email" required><br>

                    <input placeholder="Password" type="password" name="password" required><br>

                    <button type="submit" name="login">Log In</button><br>

                    <span>Don't have an Account yet? </span><a href="student_signup.php">Sign-up</a><br>
                    <span>Not a Student? </span><a href="user_type.php">Click Here!</a>

                    <?php if ($submitted) {
                        if ($correctCredentials) { ?>
                            <script>alert('Successfully Logged In');</script>
                            <script>
                                window.location.href = "student_home.php";
                            </script>
                        <?php } else { ?>
                            <script>alert('Incorrect Email or Password');</script>
                        <?php }
                    } ?>
                </form>
            </div>
        </main>
    </body>
</html>
