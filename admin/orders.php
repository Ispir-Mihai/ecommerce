<?php require_once "../includes/init.php"; ?>
<?php require_once "../includes/product.php"; ?>
<?php require_once "../includes/user.php"; ?>
<?php require_once "../includes/order.php"; ?>
<?php require_once "../includes/toast.php"; ?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["save"])) {
        $order = Order::getById($db, $_POST["id"]);
        $res = $order->update((int)$_POST["user_id"], $_POST["status"]);
        $order->updateOrderDetail($_POST["old_product_id"], $_POST["new_product_id"], $_POST["quantity"], $_POST["price"]);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);
        }
    } else if (isset($_POST["delete"])) {
        $res = Order::delete($db, $_POST["id"]);
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);
        }
    } else if (isset($_POST["add"])) {
        $res = Order::create($db, 0, "");
        if ($res["success"]) {
            Toast::success($res["message"]);
        } else {
            Toast::danger($res["message"]);
        }
    } else if (isset($_POST["add-detail"])) {
        $order = Order::getById($db, $_POST["id"]);
        $product = new Product($db, 0, "", "", 0, 0);
        // $order->addOrderDetail($product, 0);
    }
}

?>

<main class="container">
    <h1 class="title">Manage orders</h1>
    <table class="table-viewer">
        <thead>
            <tr>
                <th class="col-5">ID</th>
                <th class="col-30">Customer</th>
                <th class="col-25">Products</th>
                <th class="col-15">Status</th>
                <th class="col-5">Total</th>
                <th class="col-10">Created at</th>
                <th class="col-10">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (Order::getAll($db) as $order) {
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"]) && $_POST["id"] == $order->id)
                    $edit = "";
                else
                    $edit = "disabled";

                $user = User::getById($db, $order->user_id);
                $total = 0;

                echo "<tr>";
                echo "<form method=\"POST\" id=\"{$order->id}\"></form>";
                echo "<input type=\"hidden\" name=\"id\" value=\"{$order->id}\" form=\"{$order->id}\">";

                echo "<td># {$order->id}</td>";

                $cust_name = $user ? $user->first_name . " " . $user->last_name : "-";
                $cust_email = $user ? $user->email : "-";
                echo "<td><div class=\"child-row\">
                        <table class=\"child-table\">
                            <tr>
                                <th class=\"text-right\">ID</th>
                                <td class=\"text-left\">#<input type=\"number\" name=\"user_id\" value=\"{$order->user_id}\" form=\"{$order->id}\" $edit></td>
                            </tr>
                            <tr>
                                <th class=\"text-right\">Name</th>
                                <td class=\"text-left\">{$cust_name}</td>
                            </tr>
                            <tr>
                                <th class=\"text-right\">Email</th>
                                <td class=\"text-left\">{$cust_email}</td>
                            </tr>
                        </table>
                    </div></td>";


                echo "<td><div class=\"child-row\"><table class=\"child-table\">";
                if (!empty($order->order_details)) {
                    foreach ($order->order_details as $detail) {
                        $total += $detail->price * $detail->quantity;
                        echo "
                        <input type=\"hidden\" name=\"old_product_id\" value=\"{$detail->product->id} form=\"{$order->id}\"\">
                        <tr><th class=\"text-right\">ID</th><td class=\"text-left\"><select name=\"new_product_id\" form=\"{$order->id}\" $edit>";
                        foreach (Product::getAll($db) as $product) {
                            echo "<option value=\"{$product->id}\"># {$product->id} - {$product->name}</option>";
                        }
                        echo "<option value=\"{$detail->product->id}\" selected disabled hidden># {$detail->product->id}</option>";
                        echo "</select></td></tr>
                        <tr>
                            <th class=\"text-right\">Name</th>
                            <td class=\"text-left\">{$detail->product->name}</td>
                        </tr>
                        <tr>
                            <th class=\"text-right\">Quantity</th>
                            <td class=\"text-left\"><input type=\"number\" name=\"quantity\" value=\"{$detail->quantity}\" form=\"{$order->id}\" $edit></td>
                        </tr>
                        <tr>
                            <th class=\"text-right\">Price</th>
                            <td class=\"text-left\"><input type=\"number\" name=\"price\" value=\"{$detail->price}\" form=\"{$order->id}\" $edit></td>
                        </tr>
                        <tr class=\"separator-row\">
                            <td colspan=\"2\"><hr/></td>
                        </tr>";
                    }
                }
                echo "<tr>
                        <td class=\"table-actions\" colspan=\"2\">
                            <div class=\"tooltip tooltip-bottom\">
                                <button class=\"button-success\" type=\"submit\" name=\"add-detail\" form=\"{$order->id}\">
                                    <i class=\"fa-solid fa-plus\"></i>
                                </button>
                                <span class=\"tooltip-text\">Add</span>
                            </div>
                        </td>
                    </tr>";
                echo "</table></div></td>";

                echo "<td><input type=\"text\" name=\"status\" value=\"{$order->status}\" form=\"{$order->id}\" $edit></td>";
                echo "<td>{$total}</td>";
                echo "<td>{$order->created_at->format("d-m-Y")}</td>";

                echo "<td class=\"table-actions\">";
                if ($edit == "disabled")
                    echo "<div class=\"tooltip tooltip-left\"><button class=\"button-info\" type=\"submit\" name=\"edit\" form=\"$order->id\"><i class=\"fa-solid fa-pen\"></i></button><span class=\"tooltip-text\">Edit</span></div>";
                else
                    echo "<div class=\"tooltip tooltip-left\"><button class=\"button-success\" type=\"submit\" name=\"save\" form=\"$order->id\"><i class=\"fa-solid fa-check\"></i></button><span class=\"tooltip-text\">Save</span></div>";
                echo "<div class=\"tooltip tooltip-right\"><button class=\"button-danger\" type=\"submit\" name=\"delete\" form=\"$order->id\"><i class=\"fa-solid fa-minus\"></i></button><span class=\"tooltip-text\">Delete</span></div>";
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