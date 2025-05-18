<?php 
if(isset($_POST['tutor'])) {
    echo "<script>window.location.href='tutor_login.php'</script>";
}
if(isset($_POST['student'])) {
    echo "<script>window.location.href='student_login.php'</script>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0px;
        padding: 0px;
        background-image: linear-gradient( 86.3deg,  rgb(1, 199, 133) 3.6%, rgb(8, 68, 46) 87.6% );
        color: #333;
        min-height: 100vh;
    }
    a {
        font-size: 30px;
    }
</style>

<html>
    <head>
        <title>User Login</title>
        <link href="user_type.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/x-icon" href="icon.png"/>
    </head>
    <body>
        <div class="choice">
            <img src="logo1.png" alt="Tutor hub logo" class="logo"/><br>
            <h1>User Type</h1>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <button name="tutor">Tutor</button>
                <button name="student">Student</button>
            </form>
        </div>
    </body>
</html>