<?php
include 'conn.php';
if(isset($_POST['sign_up'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact_num'];
    $password = $_POST['password'];
    $retype = $_POST['retype'];

    $sql = "INSERT INTO tutor_info(name, email, password, contact_num) VALUES('$name', '$email', '$password', $contact)";

    if($retype == $password) {
        mysqli_query($conn, $sql);
    
        echo "<script>alert('Account Registered')</script>";
        echo "<script>";
        echo "window.location.replace('tutor_login.php')";
        echo "</script>";
    }
    else {
        echo "<script>alert('Your Password did not match')</script>";
    }
}
?>

<html>
    <head>
        <title>Tutor Sign-up</title>
        <link href="tutor_signup.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/x-icon" href="icon.png"/>
    </head>
    <body>
    <div class="signup">
        <h1>Tutor Sign-up</h1>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <input type="text" name="name" placeholder="Name" required/><br>

            <input type="email" name="email" placeholder="Email" required/><br>

            <input type="number" name="contact_num" placeholder="Contact_Num" maxlength="11" minlength="11" pattern="\d{11}" required oninput="this.value = this.value.replace(/\D/, '')"/><br>

            <input type="password" name="password" placeholder="Password" maxlength="16" minlength="8" required/><br>
            
            <input type="password" name="retype" placeholder="Re-type Password" required/><br>
            
            <button name="sign_up">Sign Up</button><br>

            Already have an account? <a href="tutor_login.php">Log in</a>
        </form>
    </div>
    </body>
</html>