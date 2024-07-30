<?php

class Toast
{
    public static function info(string $message)
    {
        echo
        "
        <script>
            Toastify({
                text: `{$message}`,
                duration: 3500,
                newWindow: true,
                close: true,
                gravity: \"bottom\",
                position: \"center\",
                stopOnFocus: true,
                style: {
                    background: \"#0096D6\",
                },
            }).showToast();
        </script>
        ";
    }
    public static function success(string $message)
    {
        echo
        "
        <script>
            Toastify({
                text: `{$message}`,
                duration: 3500,
                newWindow: true,
                close: true,
                gravity: \"bottom\",
                position: \"center\",
                stopOnFocus: true,
                style: {
                    background: \"#05af46\",
                },
            }).showToast();
        </script>
        ";
    }
    public static function danger(string $message)
    {
        echo
        "
        <script>
            Toastify({
                text: `{$message}`,
                duration: 3500,
                newWindow: true,
                close: true,
                gravity: \"bottom\",
                position: \"center\",
                stopOnFocus: true,
                style: {
                    background: \"#d40707\",
                },
            }).showToast();
        </script>
        ";
    }
}