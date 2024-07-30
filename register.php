<?php require_once "includes/init.php"; ?>
<?php require_once "includes/user.php"; ?>
<?php require_once "includes/toast.php"; ?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<?php

$success = true;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = User::register($db, $_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["password"], $_POST["confirm_password"]);
    $success = $response["success"];
    $message = $response["message"];

    if ($success)
        Toast::success($message);
    else 
        Toast::danger($message);

    if ($success) {
        User::login($db, $_POST["email"], $_POST["password"]);
        header("refresh:1;url=/me");
    }
}

?>

<main class="container">
    <h1 class="title">Register</h1>
    <form class="w-25" method="POST">
        <div class="input-group w-50">
            <label for="first_name">First name</label>
            <input type="text" name="first_name" id="first_name" required>
        </div>
        <div class="input-group w-50">
            <label for="last_name">Last name</label>
            <input type="text" name="last_name" id="last_name" required>
        </div>
        <div class="input-group w-100">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="input-group w-50">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="input-group w-50">
            <label for="confirm_password">Confirm password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <button type="submit">Continue</button>
    </form>
</main>

<?php include 'templates/footer.php'; ?>