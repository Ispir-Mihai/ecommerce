<?php require_once "../includes/init.php"; ?>
<?php require_once "../includes/product.php"; ?>
<?php require_once "../includes/admin.php"; ?>
<?php require_once "../includes/toast.php"; ?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["save"])) {
        $product = Product::getById($db, $_POST["id"]);
        $res = $product->update($_POST["name"], $_POST["description"], (int)$_POST["stock"], (float)$_POST["price"]);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);    
        }
    } else if (isset($_POST["delete"])) {
        $res = Product::delete($db, $_POST["id"]);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);    
        }
    } else if (isset($_POST["add"])) {
        $res = Product::create($db, "", "", 0, 0);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);    
        }
    }
}

?>

<main class="container">
    <h1 class="title">Manage products</h1>
    <table class="table-viewer">
        <thead>
            <tr>
                <th class="col-5">ID</th>
                <th class="col-25">Name</th>
                <th class="col-40">Description</th>
                <th class="col-10">Stock</th>
                <th class="col-10">Price</th>
                <th class="col-10">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (Product::getAll($db) as $product) {
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"]) && $_POST["id"] == $product->id)
                    $edit = "";
                else
                    $edit = "disabled";

                echo "<tr>";
                echo "<form method=\"POST\" id=\"{$product->id}\"></form>";
                echo "<input type=\"hidden\" name=\"id\" value=\"{$product->id}\" form=\"{$product->id}\">";

                echo "<td>{$product->id}</td>";
                echo "<td><input type=\"text\" name=\"name\" value=\"{$product->name}\" form=\"{$product->id}\" $edit></td>";
                echo "<td><textarea name=\"description\" form=\"{$product->id}\" $edit>{$product->description}</textarea></td>";
                echo "<td><input type=\"number\" name=\"stock\" value=\"{$product->stock}\" form=\"{$product->id}\" $edit></td>";
                echo "<td><input type=\"number\" name=\"price\" value=\"{$product->price}\" form=\"{$product->id}\" $edit></td>";

                echo "<td class=\"table-actions\">";
                if ($edit == "disabled")
                    echo "<div class=\"tooltip tooltip-left\"><button class=\"button-info\" type=\"submit\" name=\"edit\" form=\"$product->id\"><i class=\"fa-solid fa-pen\"></i></button><span class=\"tooltip-text\">Edit</span></div>";
                else
                    echo "<div class=\"tooltip tooltip-left\"><button class=\"button-success\" type=\"submit\" name=\"save\" form=\"$product->id\"><i class=\"fa-solid fa-check\"></i></button><span class=\"tooltip-text\">Save</span></div>";
                echo "<div class=\"tooltip tooltip-right\"><button class=\"button-danger\" type=\"submit\" name=\"delete\" form=\"$product->id\"><i class=\"fa-solid fa-minus\"></i></button><span class=\"tooltip-text\">Delete</span></div>";
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