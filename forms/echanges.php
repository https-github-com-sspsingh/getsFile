<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/'.basename($_SERVER['PHP_SELF']);
	$masters = new Masters();
	
	$basename   =   basename($_SERVER['PHP_SELF']);	
	$action     =   isset($_GET['a'])		?	$masters->Decrypt($_GET['a'])	:	'create';
	$id         =   isset($_GET['i'])		?	$masters->Decrypt($_GET['i'])	:	'';
	$message    =   isset($_GET['m'])		?	urldecode($masters->Decrypt($_GET['m']))	:	'';
	$type       =   isset($_GET['t'])		?	$masters->Decrypt($_GET['t'])	:	'';
	
	$headTitle  = 'Employee - Change Posithion';
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
                        <li><a href="<?=$login->home.'forms/'.$basename?>"><?=$headTitle?></a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
             	<div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            
                            <div class="box-header">
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
 
                     
								<?PHP }	?>
                                        
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                
<?PHP 
	if($action == 'view') 
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
    
    <div class="col-md-1" style="margin-left:30px;">
        <a onClick="exportToExcel()" class="btn btn-primary btn-flat btn-flat fa fa-download">&nbsp;&nbsp;Export Excel</a>
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
                                    elseif($action  == 'view')		 $masters->view($searchbyID);
                                    elseif($action == 'create')		 $masters->createForm($id);                                    
                                ?>
                            </div>
                            
                        </div><!-- /.box -->
				</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type="text/javascript">
function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTable').outerHTML));}
</script>

<script type='text/javascript'>   
$("#searchbyID").focus();
 
if(($("#fieldsID").val() == 1))
{
    var frmvalidator  = new Validator("register");    
    frmvalidator.EnableOnPageErrorDisplay();    
    frmvalidator.EnableMsgsTogether();
	
	frmvalidator.addValidation("empID","num","Plz select Employee Name");
    frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");
	
	frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
	frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion ");
}
</script>