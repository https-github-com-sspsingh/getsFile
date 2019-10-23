<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/'.basename($_SERVER['PHP_SELF']);
	$masters = new Masters();
	
	$basename	=   basename($_SERVER['PHP_SELF']);	
	$action     =   isset($_GET['a'])		?	$masters->Decrypt($_GET['a'])	:	'view';
	$id			=   isset($_GET['i'])		?	$masters->Decrypt($_GET['i'])	:	'';
	$message	=   isset($_GET['m'])		?	urldecode($masters->Decrypt($_GET['m']))	:	'';
	$type		=   isset($_GET['t'])		?	$masters->Decrypt($_GET['t'])	:	'';
	$searchbyID =   isset($_GET['searchbyID'])  ?	$_GET['searchbyID']             :	'';
	
	$headTitle	= 'Parking Permits';
	$titleText	= $action ==	'view'	?	'All Parking Permits List' : (!empty($id) ? 'Edit Parking Permits' : 'Add New Parking Permits' );
?>
<input type="hidden" id="fieldID" value="100" />
<input type="hidden" id="fieldsID" value="<?=($action == 'view' ? 0 : 1)?>" />
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="<?=$masters->home?>"><i class="fa fa-dashboard"></i>Home</a></li>
                        <li><a href="<?=$login->home.'forms/'.$basename?>"><?=$headTitle?></a></li>
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
                                 
                <?PHP 
                    if($action == 'view' || ($action == 'create' && !empty($id)) ) 
                    {
                        if($masters->permissions['addID'] == 1 || $_SESSION[$masters->website]['userTY'] == 'AD')
                        {
                            $aclass="btn-primary btn-flat"; $alink	= $basename.'?a='.$masters->Encrypt("create");
                        }
                        else
                        {
                            $aclass = "btn-disabled";  $alink	="#"; 
                        }
                ?>  


                                 
  <a href="<?=$alink?>"><button class="btn <?=$aclass?>"  style="margin-right:20px; float:right; display:inline-block" type="button">Add New</button></a>
 
                     
								<?PHP }  if($action <> 'view') { ?> 
                                
                                    <button class="btn btn-primary btn-flat" 
                                            onclick="createURL('<?=$basename?>');" 
                                            style="margin-right:50px; float:right" 
                                            type="button" >
                                            View List
                                    </button>
                                <?PHP	}	?>
                                        
                                        </div>                                        
                                    </div>                                    
                                </div>
                                
<?PHP 
	if($action == 'view' && empty($id))
	{
?>                                                    
            <form action="<?=$login->home?>forms/<?=$basename?>" method="get" >                             
            <div class="row" style=" padding-left:10px !important;">

                <div class="col-xs-3">
                    <input type="text" class="form-control" name="searchbyID" id="searchbyID" placeholder="Search By" value="<?=$searchbyID?>" />
                </div>

                <div class="col-md-1">
                    <input type="submit" name="Submit" class="btn btn-primary btn-flat btn-flat fa fa-filter" value="&nbsp;Filter Data" />
                </div>

                <div class="col-md-1">
                    <a href="<?=$login->home?>forms/<?=$basename?>" class="btn btn-primary btn-flat btn-flat fa fa-refresh">&nbsp;&nbsp;Clear Filters</a>
                </div>
            </div>
            </form>
<?PHP
	}
?>
                                
                            </div><!-- /.box-header -->
                            	
<?PHP
    if($action == 'add') 
    {
        $PR_Array  = ($_POST['empID'] > 0  ? $masters->select('prpermits',array("*"), " WHERE companyID = ".$_SESSION[$login->website]['compID']." AND empID = ".$_POST['empID']." ") : '');

        //echo 'ID : '.$PR_Array[0]['ID'];
        
        if($PR_Array[0]['ID'] > 0)
        {
            $_POST['ID'] = $PR_Array[0]['ID'];
            $masters->Form = 'Submit';
            $masters->submit_update();
        }
        else
        {
            $masters->Form = 'Submit';
            $masters->add();
        }
    }
    if($action == 'edit') 
    {
        $masters->Form = 'Submit';
        $masters->update();
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
                                    elseif($action  == 'view')			 $masters->view($searchbyID);
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
$("#searchbyID").focus();
    
if(($("#fieldsID").val() == 1))
{
    var frmvalidator  = new Validator("register");    
    frmvalidator.EnableOnPageErrorDisplay();    
    frmvalidator.EnableMsgsTogether();
	
    frmvalidator.addValidation("empID","num","Plz select Employee Name");
    frmvalidator.addValidation("empID","gt=0","Plz select Employee Name ");   
}

var changes = false;
window.onbeforeunload = function() 
{
	if (changes)
	{	 
		var message = "Unsaved entries exist. Are you sure you want to leave the page?";
		if (confirm(message)) return true;
		else return false;
	}
}
</script>