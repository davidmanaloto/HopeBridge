<?php
session_start();
session_unset();
session_destroy();
<<<<<<< Updated upstream
setcookie("session_token", "", time() - 3600, "/");
=======
>>>>>>> Stashed changes
header("Location: home.php?logout=success");
exit();
?>
