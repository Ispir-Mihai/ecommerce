<?php $page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>

<nav>
    <ul>
        <li class="<?= $page == 'index.php' ? 'nav-active':''; ?> nav-left"><a href="/">Home</a></li>
        <li class="<?= $page == 'orders.php' ? 'nav-active':''; ?> nav-right"><a href="/orders">Orders</a></li>
        <li class="<?= $page == 'products.php' ? 'nav-active':''; ?> nav-right"><a href="/products">Products</a></li>
        <li class="<?= $page == 'users.php' ? 'nav-active':''; ?> nav-right"><a href="/users">Users</a></li>
    </ul>
</nav>