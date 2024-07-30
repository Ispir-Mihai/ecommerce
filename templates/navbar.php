<?php $page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>

<nav>
    <ul>
        <li class="<?= $page == 'index.php' ? 'nav-active':''; ?> nav-left"><a href="/">Home</a></li>
        <li class="<?= $page == 'reigster.php' ? 'nav-active':''; ?> nav-right"><a href="/register">Register</a></li>
        <li class="<?= $page == 'login.php' ? 'nav-active':''; ?> nav-right"><a href="/login">Login</a></li>
    </ul>
</nav>