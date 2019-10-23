<?PHP	
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';	
    include 'code/imp_hastus.php';
    $Imports = new Imports();

    $basename = basename($_SERVER['PHP_SELF']);

    $action	  = isset($_GET['a'])	?	$Imports->Decrypt($_GET['a'])			: 'view';
    $id       = isset($_GET['i'])	?	$Imports->Decrypt($_GET['i'])			: '';
    $message  = isset($_GET['m'])	?	urldecode($Imports->Decrypt($_GET['m']))	: '';
    $type	  = isset($_GET['t'])	?	$Imports->Decrypt($_GET['t'])		        : '';
	
	$srID	  = isset($_GET['srID'])	?	$Imports->Decrypt($_GET['srID'])		        : '';
	
    $caseID	  = 1;
	
    $headTitle  = 'Import Hastus Sheet';
    $titleText  = ($action == 'view' ?   'All Import Daily Sheet List' : (!empty($id) ? '' : '' ));
	
	$login->Fpermissions  = $login->GET_formPermissions($_SESSION[$login->website]['userRL'],'93');
	
?> 
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side <?=($_REQUEST['optionID'] == 3 ? 'strech' : '')?>">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                        <li><a href="<?=$login->home?><?=$basename?>"><?=$headTitle?></a></li>
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
	echo '<div class="row">';
	echo '<div class="col-md-12">';
		echo '<div style="min-height:80px; '.($message <> '' ? 'width: 530px;' : '').' border:solid 2px #3C8DBC; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">';
		echo '<form method="post" id="filterForm" action="?a='.$Imports->Encrypt('import').'" enctype="multipart/form-data">';
		
		echo '<input type="hidden" name="randID" value="'.round(rand(22,10).'0'.$_SESSION[$login->website]['compID'].'0'.date('his'),0).'" />';
		
		echo '<div style="display:inline-block; margin-top:5px;">';
			echo '<select class="form-control" id="optionID" name="optionID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Option --</option>';
			$optionID = $_REQUEST['optionID'] > 0 ? $_REQUEST['optionID'] : 1;
			echo '<option value="1" '.($optionID == 1 ? 'selected="selected"' : '').'>Import Sheet</option>';			
			echo '<option value="3" '.($optionID == 3 ? 'selected="selected"' : '').'>View Sheet</option>';
			echo '</select>';
		echo '</div>'; 
		
		if($optionID == 1)
		{
			echo '<div style="display:inline-block;">';
				echo '<input type="checkbox" name="hastusTYPE" style="margin-left: 14px;" /><b style="color:red; font-size: 16px;"> Charter</b>';
			echo '</div>';
		}
		
		echo '<div style="display:inline-block; '.($_REQUEST['optionID'] == 3 ? '' : 'display:none;').'" class="viewID">';
			echo '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="hfdateID" id="hfdateID" value="'.$_REQUEST['hfdateID'].'" placeholder="Enter From Date" style="text-align:center;" />';
		echo '</div>';
		
		echo '<div style="display:inline-block; '.($_REQUEST['optionID'] == 3 ? '' : 'display:none;').'" class="viewID">';
			echo '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="htdateID" id="htdateID" value="'.$_REQUEST['htdateID'].'" placeholder="Enter To Date" style="text-align:center;" />';
		echo '</div>';
		
		echo '<div class="btn bg-olive btn-flat btn-file" id="importID" style="margin-top: 10px; margin-left: 10px; '.($optionID == 1 ? '' : 'display:none;').' width: 151px;">';
			echo '<i class="fa fa-paperclip"></i>&nbsp;Import in .CSV';
			echo '<input type="file" name="upload" class="import" />';
		echo '</div>';
		
		echo '<button class="btn bg-navy btn-flat" name="Submit" style="margin-left:10px; margin-top: 9px;" type="submit">Submit</button>';
		
		echo '<a href="'.$basename.'" id="imp_partID_2" style="margin-left:10px; margin-top: 9px;" class="btn bg-navy btn-flat">Clear Filters</a>';
		
		if($_REQUEST['optionID'] == 3)
		{
			echo '<button class="btn bg-maroon btn-flat" onClick="exportToExcelIn()" style="margin-left:10px; margin-top: 9px;" type="button">Export Excel</button>';
		}
		
		
		echo '<span style="'.($optionID == 3 ? 'style="display:inline-block"' : 'style="display:none;"').'" class="spanDIV"><br /><br /></span>';		
		
		echo '<div class="spanDIV" style="display:'.($optionID == 3 ? 'inline-block' : 'none').';">';
			$companyID = (implode(',',$_REQUEST['filterID']) <> '' ? implode(',',$_REQUEST['filterID']) : $_SESSION[$login->website]['ecomID']);
			$explodeID = explode(",",$companyID);
            $Qry = $login->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID In(".$_SESSION[$login->website]['ecomID'].") Order By title ASC ");
            $Qry->execute();
            $login->resu = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<div class="row">';
            foreach($login->resu as $res)
            {
                echo '<div class="col-xs-2" style="padding-right:0px;">';
                echo '<input type="checkbox" name="filterID[]" value="'.$res['ID'].'" '.(in_array($res['ID'],$explodeID) ? 'checked="checked"' : '' ).' > <b style="font-size:14px; color:#367FA9;">'.$res['title'].'</b>';
                echo '</div>';
            }
			echo '</div>';
		echo '</div>';
		
		echo '</form>';
		echo '</div>';
	echo '</div>';
	
	
	echo '<div class="col-md-6" '.($_REQUEST['optionID'] <> 1 ? 'style="width: 105%; margin-top:20px;"' : '').'>';
		if(!empty($message)) 
		{
			if($login->Decrypt($_GET['rID']) > 0)
			{
				$Qry = $login->DB->prepare("SELECT * FROM temp_hastus WHERE randID = ".$login->Decrypt($_GET['rID'])." AND randDT = '".date('Y-m-d')."' ");
				$Qry->execute();
				$login->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);			
				foreach($login->rows_1 as $rows_1)
				{
					$message .= '<br /><b>Date : <b style="color:blue;">'.date('d-M-Y',strtotime($rows_1['fID_0'])).'</b>, Depot Code : <b style="color:blue;">'.$rows_1['fID_1'].'</b>, Start Shift : <b style="color:blue;">'.$rows_1['fID_2'].'</b>, Start Whrs : <b style="color:blue;">'.$rows_1['fID_3'].'</b>, Start Eslf : <b style="color:blue;">'.$rows_1['fID_4'].'</b></b>';
					//.', Split Shifts : '.$rows_1['fID_5'].', Split Whrs : '.$rows_1['fID_6'].', Split Eslf : '.$rows_1['fID_7'].', Casual Shifts : '.$rows_1['fID_8'].', Casual Whrs : '.$rows_1['fID_9'].', Casual Eslf : '.$rows_1['fID_10'].', PT Shifts : '.$rows_1['fID_11'].', PT Whrs : '.$rows_1['fID_12'].', PT Eslf : '.$rows_1['fID_13'].', Inverse Time : '.$rows_1['fID_14'].', Inverse KMS : '.$rows_1['fID_15'].', Total KMS : '.$rows_1['fID_16'].', Inverse Artic KMS : '.$rows_1['fID_17'].', Total Artic KM : '.$rows_1['fID_18'].', Sched Detail : '.$rows_1['fID_19'].', Sched Description : '.$rows_1['fID_20'].'</b>';
				}
			}

			echo '<div class="alert alert-'.$type.' alert-dismissable">';
			echo '<i class="fa fa-check"></i>';
			echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			echo '<b>'.$message.'</b>';
			echo '</div>';
			
			if($login->Decrypt($_GET['rID']) > 0)
			{
				$Qry = $login->DB->prepare("DELETE FROM temp_hastus WHERE randID = ".$login->Decrypt($_GET['rID'])." AND randDT = '".date('Y-m-d')."' ");
				$Qry->execute();
			}
		} 
	echo '</div>';	
   ?>
    </div>
    </div>
    </div>
    </div><!-- /.box-header -->
                            	
    <?PHP
        if($action == 'import') 
        {
			if($_POST['optionID'] == 1 || $_POST['optionID'] == 2 || $_POST['optionID'] == 3)
			{
				$Imports->Form = 'Submit';
				$Imports->GoToInnserSheet();
			}
        }	
     ?>
	 
                                
                                <div class="box-body table-responsive">&nbsp;</div>                                
                        </div><!-- /.box -->
                </div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type="text/javascript"> 
$(document).ready(function()
{   
	$("#optionID").on('change',function()
	{
		if(($("#optionID").val() == 1))
		{
			$("#importID").show();	$(".viewID").hide();	$(".spanDIV").hide();
		}
		else if(($("#optionID").val() == 2))
		{
			$("#importID").hide();	$(".viewID").show();	$(".spanDIV").hide();
		}
		else if(($("#optionID").val() == 3))
		{
			$("#importID").hide();	$(".viewID").show();	$(".spanDIV").show();
		}
		else
		{
			$("#importID").show();	$(".viewID").hide();	$(".spanDIV").hide();
		}
	}); 
});
</script>

<script type="text/javascript">
function exportToExcelIn()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
</script>