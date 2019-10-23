<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/'.basename($_SERVER['PHP_SELF']);
	$masters = new Masters();
	
	$basename   =	basename($_SERVER['PHP_SELF']);	
	$action     =	isset($_GET['a'])	?	$masters->Decrypt($_GET['a'])			    :	'view';
	$id         =	isset($_GET['i'])	?	$masters->Decrypt($_GET['i'])	   		    :	'';
	$message    =	isset($_GET['m'])	?	urldecode($masters->Decrypt($_GET['m']))            :	'';
	$type       =	isset($_GET['t'])	?	$masters->Decrypt($_GET['t'])			    :	'';
	$f          = 	isset($_GET['f'])       ?	$masters->Decrypt($_GET['f'])                       : 	'';
	 
	$f_array    = 	$masters->select('frmset',array("*"), " WHERE ID = ".$f." ");	
	$headTitle  =	$f_array[0]['title'];
	
	$titleText  = $action == 'create' && empty($id) ? 'Create New ' :($action == 'view' ? 'View All ' :($action == 'create' && !empty($id) ? 'Edit ' :  ''));
	$titleText .= $headTitle;
?>
<input type="hidden" id="fieldID" value="100" />
<input type="hidden" id="fieldsID" value="<?=($action == 'view' ? 0 : 1)?>" />

            <aside class="right-side">      
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="<?=$login->home?>"><i class="fa fa-dashboard"></i>Home</a></li>
                        <li><a href="<?=$login->home?><?=$basename?>"><?=$headTitle?></a></li>
                    </ol>
                </section>
                
                <section class="content">
             	<div class="col-md-12">
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
                            $aclass="btn-primary btn-flat"; $alink	= $basename.'?a='.$masters->Encrypt("create").'&f='.$masters->Encrypt($f);							
                        }
                        else
                        {
                            $aclass = "btn-disabled";  $alink	="#"; 
                        }
                ?>                                         
                                
                                <a href="<?=$alink?>"><button class="btn <?=$aclass?>"  style="margin-right:20px; float:right; display:inline-block" 
                                type="button">Add New</button></a>
                                                    
	<?PHP }  if($action <> 'view') { ?>   
    	<a class="btn btn-primary btn-flat" href="<?=$masters->home?>setup/master.php?f=<?=$masters->Encrypt($f)?>&a=<?=$masters->Encrypt('view')?>" 
        style="margin-right:50px; float:right">View All <?=$headTitle?> Lists</a>
	<?PHP	}	?>
    
    </div>
    </div>
    </div>
    </div>
    <?PHP
				
          $_POST['basename']    = $basename;

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
          if($action == 'status') 
          {
              $masters->updateStatus($id);
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
                                        if($action  == 'view')		$masters->view($headTitle);
                                        elseif($action == 'create')	$masters->createForm($id,$headTitle);
                                    ?>
                            </div>
                        </div>
				</div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type="text/javascript">
function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTable').outerHTML));}
</script>

<script type='text/javascript'>    
if(($("#fieldsID").val() == 1))
{
    var frmvalidator  = new Validator("register");    
    frmvalidator.EnableOnPageErrorDisplay();    
    frmvalidator.EnableMsgsTogether();
	
	frmvalidator.addValidation("title","req","Enter Master Title ");   
}
</script>
