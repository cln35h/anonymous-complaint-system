<html>
    <head>
        <title>Anonymous Complaint System</title>
        <style>
             @media print {
                /* Hide elements with the "no-print" class */
                .no-print {
                    display: none !important;
                    }
                    }
</style>

    </head>
<body>
<?php
session_start();

// Function to redirect users based on their role
function redirectBasedOnRole($role) {
    switch ($role) {
        case "cm":
            header("Location: https://cln35h.notion.site/Documentation-for-Committee-Member-28df3be4cb7e4375a1d877da230ad314?pvs=4");
            break;
        case "cc":
            header("Location: https://cln35h.notion.site/Documentation-for-Committee-Convener-16b25da4b7584998ae9db8dfa8eb9beb?pvs=4");
            break;
        case "admin":
            header("Location: https://cln35h.notion.site/Documentation-for-Admin-b602305b841348999c514c754419524d?pvs=4");
            break;
        default:
            header("Location: https://cln35h.notion.site/Documentation-for-Users-5a06769c6f7747d2b21f078b810a566a?pvs=4");
            break;
    }
    exit;
}

// If no user is logged in, display documentation for users
if (!isset($_SESSION["user_id"])) {
    header("Location: https://cln35h.notion.site/Documentation-for-Users-5a06769c6f7747d2b21f078b810a566a?pvs=4");
    exit;
}

// If user is logged in, redirect based on their role
if (isset($_SESSION["role"])) {
    redirectBasedOnRole($_SESSION["role"]);
} else {
    // If role is not set, display documentation for users
    header("Location: https://cln35h.notion.site/Documentation-for-Users-5a06769c6f7747d2b21f078b810a566a?pvs=4");
    exit;
}
?>

<div>
    Display contents here
</div>
</body>
</html>
