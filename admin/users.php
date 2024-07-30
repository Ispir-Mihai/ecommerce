<?php require_once "../includes/init.php"; ?>
<?php require_once "../includes/user.php"; ?>
<?php require_once "../includes/admin.php"; ?>
<?php require_once "../includes/toast.php"; ?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["save"])) {
        $user = User::getById($db, $_POST["id"]);
        $res = $user->update($_POST["first_name"], $_POST["last_name"], $_POST["email"], $user->password_hash, (int)$_POST["role_id"]);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);    
        }
    } else if (isset($_POST["delete"])) {
        $res = User::delete($db, $_POST["id"]);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);    
        }
    } else if (isset($_POST["add"])) {
        $res = User::create($db, "", "", "", 7);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);    
        }
    }
}

?>

<main class="container">
    <h1 class="title">Manage users</h1>
    <table class="table-viewer">
        <thead>
            <tr>
                <th class="col-5">ID</th>
                <th class="col-15">First name</th>
                <th class="col-15">Last name</th>
                <th class="col-35">Email</th>
                <th class="col-20">Role</th>
                <th class="col-10">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (User::getAll($db) as $user) {
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"]) && $_POST["id"] == $user->id)
                    $edit = "";
                else
                    $edit = "disabled";

                echo "<tr>";
                echo "<form method=\"POST\" id=\"{$user->id}\"></form>";
                echo "<input type=\"hidden\" name=\"id\" value=\"{$user->id}\" form=\"{$user->id}\">";

                echo "<td>{$user->id}</td>";
                echo "<td><input type=\"text\" name=\"first_name\" value=\"{$user->first_name}\" form=\"{$user->id}\" $edit></td>";
                echo "<td><input type=\"text\" name=\"last_name\" value=\"{$user->last_name}\" form=\"{$user->id}\" $edit></td>";
                echo "<td><input type=\"email\" name=\"email\" value=\"{$user->email}\" form=\"{$user->id}\" $edit></td>";

                echo "<td><select name=\"role_id\" form=\"$user->id\" $edit>";
                foreach (Role::getAll($db) as $role) {
                    echo "<option value=\"$role->id\"" . ($user->role_id == $role->id ? "selected" : "") . ">$role->name</option>";
                }
                echo "</select></td>";

                echo "<td class=\"table-actions\">";
                if ($edit == "disabled")
                    echo "<div class=\"tooltip tooltip-left\"><button class=\"button-info\" type=\"submit\" name=\"edit\" form=\"$user->id\"><i class=\"fa-solid fa-pen\"></i></button><span class=\"tooltip-text\">Edit</span></div>";
                else
                    echo "<div class=\"tooltip tooltip-left\"><button class=\"button-success\" type=\"submit\" name=\"save\" form=\"$user->id\"><i class=\"fa-solid fa-check\"></i></button><span class=\"tooltip-text\">Save</span></div>";
                echo "<div class=\"tooltip tooltip-right\"><button class=\"button-danger\" type=\"submit\" name=\"delete\" form=\"$user->id\"><i class=\"fa-solid fa-minus\"></i></button><span class=\"tooltip-text\">Delete</span></div>";
                echo "</td>";

                echo "</tr>";
            }
            ?>

            <tr>
                <form method="POST" id="add"></form>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="table-actions">
                    <div class="tooltip tooltip-bottom">
                        <button class="button-success" type="submit" name="add" form="add">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                        <span class="tooltip-text">Add</span>
                    </div>
                </td>
            </tr>

        </tbody>
    </table>
</main>

<?php include 'templates/footer.php'; ?>