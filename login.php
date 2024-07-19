<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php
        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $referrer = $_POST['referrer'];

            require_once "database.php";

            $sql = "SELECT * FROM users WHERE email = ? AND user_type = ?";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $email, $referrer);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if ($user) {
                    if (password_verify($password, $user["password"])) {
                        $_SESSION["user"] = $user["user_type"];
                        if ($user["user_type"] === 'student') {
                            header("Location: student.php");
                        } elseif ($user["user_type"] === 'admin') {
                            header("Location: admin.php");
                        } elseif ($user["user_type"] === 'lecture') {
                            header("Location: lecture.php");
                        }
                        exit();
                    } else {
                        echo "<div class='alert alert-danger'>Password does not match</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Email or user type does not exist</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Something went wrong</div>";
            }
        }
        ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="email" placeholder="Enter Email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <select id="referrer" name="referrer" class="form-control" required>
                    <option value="">(select one)</option>
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                    <option value="lecture">Lecture</option>
                </select>
            </div>
            <div class="form-btn">
                <input type="submit" value="login" name="login" class="btn btn-primary">
            </div>
        </form>
        <div>
            <p>Not Registered Yet? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</body>

</html>
