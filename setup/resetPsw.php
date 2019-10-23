<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/'.basename($_SERVER['PHP_SELF']);
	$category = new ChangePassword();
	
	$basename  =	basename($_SERVER['PHP_SELF']);
	$action	=	isset($_GET['a'])	 ?	$category->Decrypt($_GET['a'])	:	'create';
	$id		=	isset($_GET['i'])	 ?	$category->Decrypt($_GET['i'])	:	'';
	$message   =	isset($_GET['m'])	 ?	urldecode($category->Decrypt($_GET['m']))	:	'';
	$type	  =	isset($_GET['t'])	 ?	$category->Decrypt($_GET['t'])	:	'';
	$sec	   =	isset($_GET['sec'])   ?	$category->Decrypt($_GET['sec'])  :	'';
	
	$headTitle = 'Change Password';
	$titleText = '';
	
	if($sec > 0)
	{
		$response = "Password Changed Successfully";
		echo('<script type="text/javascript">alert("' . $response . '"); </script>');    
		echo "<script>location.href = '".$login->home."logout.php'</script>";
	}
?>
<input type="hidden" id="fieldID" value="101" />
<input type="hidden" id="fieldsID" value="<?=($action == 'view' ? 0 : 1)?>" />
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                        <li><a href="<?=$login->home?>admin/<?=$basename?>"><?=$headTitle?></a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
             	<div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            
                            <div class="box-header">
                                <h3 class="box-title"><?=$titleText?></h3>
                                <div class="box-tools">
                                    <div class="input-group">
                                    	<div class="input-group-btn">
                                      	<?PHP if($action == 'view') {	?>
                                                                          
                                <a href="<?=$basename?>?a=<?=$category->Encrypt("create")?>"> 
                                		
										<?PHP } else if($action == 'create')	{ ?> 
                                        
   <!--<button class="btn btn-primary" onclick="createURL('<?=$basename?>');" style="margin-right:50px; float:right" type="button">View Assignment List</button>-->
                                
                                		<?PHP	}	?>
                                        
                                        
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div><!-- /.box-header -->
                            	
							<?PHP
                               
								if($action == 'edit') 
                                {
									$category->Form = 'site_configs';
                                    $category->update();
								}
																	
                            ?>
                            <?PHP if(!empty($message)) { ?> 	
                          	<div class="alert alert-<?=$type?> alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b><?=$message?></b>
                                    </div>
							<?PHP } ?>
                           
                           	<div class="box-body table-responsive"> 
								<?PHP  
                                    if($action  == 'view')			$category->view();
                                    elseif($action == 'create' )	$category->createForm($id);
                                    elseif($action == 'remove' )	$category->removetrans($id);
                                ?>
                            </div>
                            
                        </div><!-- /.box -->
				</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type='text/javascript'>    
if(($("#fieldsID").val() == 1))
{
    var frmvalidator  = new Validator("register");    
    frmvalidator.EnableOnPageErrorDisplay();    
    frmvalidator.EnableMsgsTogether();
	
	frmvalidator.addValidation("TxtOP","req","Enter Old Password ");
	
	frmvalidator.addValidation("TxtNP","req","Enter New Password ");
	
	frmvalidator.addValidation("TxtRTP","req","Enter Retype Password ");
	
	
   
}
</script>
