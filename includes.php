<?PHP
    error_reporting(0); 	
    session_start();	
	ini_set('session.gc_maxlifetime',3600);	
	ini_set('memory_limit', '1024M');	
	ini_set('post_max_size', 600);
	ini_set('upload_max_filesize', 500);
	ini_set('max_execution_time', 6000);
	ini_set('max_input_time', 6000);	
	set_time_limit(3000);	
    session_set_cookie_params(3600);	
    date_default_timezone_set('Australia/Perth');
	
	include 'setup/code/defines.php';
    include 'setup/code/connection.php'; 
    include 'setup/code/functions.php';
    include 'setup/code/functions_d.php';
    include 'setup/code/functions_v.php';
    include 'setup/code/functions_f.php';
	include 'setup/code/functions_r.php';
    include 'setup/code/functions_s.php';
    include 'setup/code/functions_c.php';
    include 'setup/code/functions_g.php';
    include 'setup/code/functions_e.php';
    include 'setup/code/functions_t.php';
	include 'setup/code/functions_a.php';
	include 'setup/code/functions_l.php';
    include 'setup/code/login.php';
	
    $login  = new Login();	
    $Index  = new Functions();
    $DIndex = new DFunctions();
    $VIndex = new VFunctions();
	$FIndex = new FFunctions();
	$RIndex = new RFunctions();
    $SIndex = new SFunctions();
    $CIndex = new CFunctions();
    $GIndex = new GFunctions();
    $EIndex = new EFunctions();
    $TIndex = new TFunctions();
	$AIndex = new AFunctions();
	$LIndex = new LFunctions();
?>