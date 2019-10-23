<?PHP
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';
    include 'code/'.basename($_SERVER['PHP_SELF']);
    $masters = new Masters();

    $basename	= basename($_SERVER['PHP_SELF']);	
    $action     = isset($_GET['a'])	?	$masters->Decrypt($_GET['a'])       :	'create';
    $id		= isset($_GET['i'])	?	$masters->Decrypt($_GET['i'])       :	'';
    $message	= isset($_GET['m'])	?	urldecode($masters->Decrypt($_GET['m']))	:	'';
    $type	= isset($_GET['t'])	?	$masters->Decrypt($_GET['t'])       :	'';

    $headTitle	= 'Password Reset';
    $titleText	= ($action ==	'view'	? 'All Password Reset List' : (!empty($id) ? 'Edit Password Reset' : 'Password Reset (User)'));
	
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
                        <li><a href="<?=$login->home?>"><i class="fa fa-dashboard"></i>Home</a></li>
                        <li><a href="<?=$login->home?><?=$basename?>"><?=$headTitle?></a></li>
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
               
                                            
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div><!-- /.box-header -->
                            	
                            <?PHP
                                if($action == 'add') 
                                { 
                                    $masters->Form = 'Submit';
                                    $masters->add();
                                
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
                                    if($action  == 'view' && $id <> '')	 $masters->createForm($id);
                                    elseif($action  == 'view')			 $masters->view();
                                    elseif($action == 'create')			 $masters->createForm($id);
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
	
	frmvalidator.addValidation("userID","num","Select User Name");
	frmvalidator.addValidation("userID","gt=0","Select User Name"); 		
			
	frmvalidator.addValidation("newPSW","req","Enter New Password ");   
	frmvalidator.addValidation("newPSW","minlen=8","New Password should be atleast 8 character"); 
	
	frmvalidator.addValidation("newCNF","req","Enter Confirm Password ");   
	frmvalidator.addValidation("newCNF","minlen=8","Confirm Password should be atleast 8 character"); 
}
</script>