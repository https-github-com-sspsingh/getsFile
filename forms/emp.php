<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/'.basename($_SERVER['PHP_SELF']);
	$masters = new Masters();
	
	$basename		=   basename($_SERVER['PHP_SELF']);	
	$action         =   isset($_GET['a'])			?	$masters->Decrypt($_GET['a'])	:	'view';
	$id				=   isset($_GET['i'])			?	$masters->Decrypt($_GET['i'])	:	'';
	$message		=   isset($_GET['m'])			?	urldecode($masters->Decrypt($_GET['m']))	:	'';
	$type			=   isset($_GET['t'])			?	$masters->Decrypt($_GET['t'])	:	'';
	$searchbyID     =   isset($_GET['searchbyID'])  ?	$_GET['searchbyID']             :	'';
	$auditID        =   isset($_GET['auditID'])     ?	$masters->Decrypt($_GET['auditID'])	:	'';
        
	$headTitle	=   'Employee Master';
	$titleText	=   $action ==	'view'	?   'All Employee Master List' : (!empty($id) ? 'Edit Employee' : 'Add New Employee' );
?>
<input type="hidden" id="dialogID" value="<?=($action == 'create' && empty($id) ? 1 : 0)?>" />
<input type="hidden" id="transID" value="<?=($id > 0 ? $id : 0)?>" />
<input type="hidden" id="fieldID" value="101" />
<input type="hidden" id="fieldsID" value="<?=($action == 'view' ? 0 : 1)?>" />

<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?> <span style="margin-left: 35px;font-size: 18px;color: red;font-weight: bold;" class="blinkingTX" id="DuplicateText"></span></h1>
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
    
    <div class="col-md-1" style="margin-left:30px;">
        <!--<a onClick="exportToExcel()" class="btn btn-primary btn-flat btn-flat fa fa-download">&nbsp;&nbsp;Export Excel</a>-->
        
        
        <a class="btn btn-primary btn-flat btn-flat fa fa-download" href="<?=$login->home.'downloadCSV.php?s=EMPLOYEE_REGISTER&searchbyID='.$searchbyID.'&auditID='.$auditID?>"> Export Excel</a>
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
                                    elseif($action  == 'view')		 $masters->view($searchbyID,$auditID);
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
$(($("#fieldsID").val() == 1 ? "#EcodeID" : "#searchbyID")).focus();

if(($("#fieldsID").val() == 1))
{	
	var frmvalidator  = new Validator("register");    
	frmvalidator.EnableOnPageErrorDisplay();    
	frmvalidator.EnableMsgsTogether();

	frmvalidator.addValidation("code","req","Enter E. Code ");
	frmvalidator.addValidation("fname","req","Enter First Name ");
	frmvalidator.addValidation("full_name","req","Enter Full Name ");
	frmvalidator.addValidation("address_1","req","Enter Address - 1 ");
	//frmvalidator.addValidation("pincode","req","Enter PostCode ");

	frmvalidator.addValidation("statusID","num","Plz select C. Employee");
	frmvalidator.addValidation("statusID","gt=0","Plz select C. Employee ");

	frmvalidator.addValidation("desigID","num","Plz select Desgination");
	frmvalidator.addValidation("desigID","gt=0","Plz select Desgination");

	frmvalidator.addValidation("sid","num","Plz select Suburb..");
	frmvalidator.addValidation("sid","gt=0","Plz select Suburb..");
	
	if($("#sstatusID").val() == 1)
	{
		frmvalidator.addValidation("scompanyID","num","Plz select Sub Depot..");
		frmvalidator.addValidation("scompanyID","gt=0","Plz select Sub Depot..");
	}
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



<?PHP if(empty($message)) { ?> 
<script>
$(document).ready(function()
{ 
    var dialogID = $("#dialogID").val();
    
    if(parseInt(dialogID) == 1)
    {
        ShiftConfirmDialog('This form is for adding new employee only. In case of transfer please follow transfer procedure.');
    }
});

    function ShiftConfirmDialog(message) 
    {
        $('<div></div>').appendTo('body')
        .html('<div><h6>'+message+'</h6></div>')
        .dialog({
                    modal: true, title: 'Alert : Employee', zIndex: 10000, autoOpen: true,
                width: 'auto', resizable: false,
                buttons: {
                    OK: function () 
                    {
                        $(this).dialog("close");
                    }
                }
        });
    };    
</script>    
<?PHP } ?> 
