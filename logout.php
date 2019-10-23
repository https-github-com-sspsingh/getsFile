<?PHP
    error_reporting(0);
    session_start();
    include 'includes.php';
    unset($_SESSION[$login->website]);
    echo "<script>window.location=\"".$login->home."\"</script>";
?>