<?PHP	
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';	
    include 'code/imp_daily.php';
    $Imports = new Imports();

    $basename = basename($_SERVER['PHP_SELF']);

    $action	  = isset($_GET['a'])	?	$Imports->Decrypt($_GET['a'])			: 'view';
    $id       = isset($_GET['i'])	?	$Imports->Decrypt($_GET['i'])			: '';
    $message  = isset($_GET['m'])	?	urldecode($Imports->Decrypt($_GET['m']))	: '';
    $type	  = isset($_GET['t'])	?	$Imports->Decrypt($_GET['t'])		        : '';
	
	$srID	  = isset($_GET['srID'])	?	$Imports->Decrypt($_GET['srID'])		        : '';
	
    $caseID	  = 1;
	
    $headTitle  = 'Import Daily Sheet (Driver Sign On)';
    $titleText  = ($action == 'view' ?   'All Import Daily Sheet List' : (!empty($id) ? '' : '' ));
	
	$login->Fpermissions  = $login->GET_formPermissions($_SESSION[$login->website]['userRL'],'93');
	
	$arrID = $srID > 0 ? $login->select('temp_shift_errors', array("*")," WHERE sheetID = ".$srID." ") : '';
	if($message <> '' && $type <> 'danger')
	{
		$message .= ($arrID[0]['strTX'] <> '' ? base64_decode($arrID[0]['strTX']) : '');
	}
?> 
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">      
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
        if($action == 'view' || ($action == 'create' && !empty($id)) || $action == 'displayreport' || $action == 'import') 
        {
			$chooseID = $_REQUEST['chooseID'] > 0 ? $_REQUEST['chooseID'] : 1;
            if((($action == 'view') || $action == 'import') && ($caseID == 2 || $caseID == 1)) 
            {   
    ?>
            <div style="border:solid 1px #DDD; padding:2px; float:left; margin-right:20px; display:inline-block;">
            <form method="post" id="filterForm" action="?a=<?=$Imports->Encrypt('import')?>&cs=<?=$Imports->Encrypt($caseID)?>" enctype="multipart/form-data">
            <input type="hidden" name="optionID" value="<?=$caseID?>" />
            <input type="hidden" name="sheet_randID" value="<?=round(rand(22,10).'0'.$_SESSION[$login->website]['compID'].'0'.date('his'),0)?>" />
			
            <div style="display:inline-block;">
                <select class="form-control" id="chooseID" name="chooseID">
                <option value="0" selected="selected" disabled="disabled">-- Select Option --</option>
                <option value="1" <?=($chooseID == 1 ? 'selected="selected"' : '')?>>Import Sheet</option>
			<?PHP
			if($login->Fpermissions['delID'] == 1 || $_SESSION[$login->website]['userTY'] == 'AD')
			{
			?>
                <option value="2" <?=($chooseID == 2 ? 'selected="selected"' : '')?>>Delete Sheet</option>
			<?PHP
			}
			?>
			
                <option value="3" <?=($chooseID == 3 ? 'selected="selected"' : '')?>>View Sheet</option>
                <option value="4" <?=($chooseID == 4 ? 'selected="selected"' : '')?>>Download Sheet</option>
                </select>
            </div>
            <?PHP 
            if($caseID == 1)
            {
            $sheetID = ($_POST['sheetID'] > 0 ? $_POST['sheetID'] : 1);
            ?>
                <input type="hidden" name="sheetID" value="1" />
                
                <div style="display:inline-block;">
                    <input type="text" class="form-control datepicker" name="dateID" id="dateID" value="<?=$_REQUEST['dateID']?>" placeholder="Enter Shift Date" style="text-align:center;" />
                </div>            
              
            <?PHP
            
            
            if($chooseID == 1)
            {
				?>
				<div class="btn btn-success btn-file" id="imp_partID" style="margin-top: 10px;">
					<i class="fa fa-paperclip"></i>&nbsp;Import in .xlsx
					<input type="file" name="upload" class="import" />
				</div>
				<?PHP
            }
            ?>            
            <button class="btn btn-primary" style="margin-top: 9px;" name="Submit" id="imp_partID_1" type="submit">Submit</button>
            <button class="btn btn-danger delete_import_log" id="del_partID" style="margin-top: 9px; display:none;" type="button">Delete Import Sheet</button>
            
            <a href="<?=$basename?>?cs=<?=$Imports->Encrypt($caseID)?>" id="imp_partID_2" style="margin-top: 9px;" class="btn btn-primary">Clear Filters</a>
            
            
            <?PHP
            
            if($_POST['dateID'] <> '' && $sheetID == 2)
            {
                ?>
                        <a data-title="imp_shifts" data-rel="80" data-ajax="<?=$_POST['fdateID']?>" class="btn btn-danger Delete_Confirm">Delete Log 
                        <?=($_POST['dateID'] <> '' ? ' : '.$_POST['dateID'] : '')?></a>	
                <?PHP
            }
            ?>
            
            
            <?PHP
            if($chooseID == 3)
            {
                ?>
                        <a class="btn btn-danger" style="margin-top: 9px;" href="<?=$login->home.'exportCSV.php?s=DAILY_SHEET&fromID='.$_POST['dateID']?>">Download Excel</a>	
                <?PHP
            }
              
            if($chooseID == 4)
            {
                ?>
                        <a class="btn btn-danger" style="margin-top: 10px;" href="<?=$login->home.'exportCSV.php?s=DAILY_SHEET_GENERATOR&fromID='.$_POST['dateID']?>">Download Import Format</a>	
                <?PHP
            }
            
            }
            ?>
            </form>
         </div>
<?PHP }	
        }
    ?>
    </div>
    </div>
    </div>
    </div><!-- /.box-header -->
                            	
    <?PHP
        if($action == 'import') 
        {
			if($_POST['sheetID'] == 1)
			{
				$Imports->Form = 'Submit';
				$Imports->GoToInnserSheet();
			}
        }	
     ?>
                           
    
     <?PHP if(!empty($message)) { ?> 	
        <div class="alert alert-<?=$type?> alert-dismissable">
        <i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <b><?=$message?></b>
        </div>
    <?PHP } ?>
                                
                                <div class="box-body table-responsive">&nbsp;</div>                                
                        </div><!-- /.box -->
                </div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type="text/javascript">
function PrintPage(elem)    {Popup($(elem).html());}

function Popup(data) 
{
    var content     =  (data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
    var mywindow    =  '';
    mywindow = window.open('', 'View Report', 'height=800,width=1250');
    mywindow.document.write('<html><head><title>Performance Report</title>');
    mywindow.document.write('</head>');
    mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b> Performance Report</b></th></tr></br></br>');
    mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');
    mywindow.document.write('</body></html>');
    mywindow.print();
    mywindow.close();
    return true;
}

function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}

$(document).ready(function()
{   
	$("#chooseID").on('change',function()
	{
            var chooseID = $(this).val();

            if(parseInt(chooseID) == 2)
            {
                $("#imp_partID,#imp_partID_1,#imp_partID_2").hide();    $("#day_ID").hide();	$("#del_partID").show();	$("#dateID").val('');
            }
            else if(parseInt(chooseID) == 4)
            {
                $("#imp_partID").hide();     $("#day_ID").hide();   $("#imp_partID_1,#imp_partID_2").show(); $("#del_partID").hide();	$("#dateID").val('');
            }
            else if(parseInt(chooseID) == 3)
            {
                $("#imp_partID").hide();    $("#day_ID").hide();    $("#imp_partID_1,#imp_partID_2").show(); $("#del_partID").hide();	$("#dateID").val('');
            }
            else
            {
                $("#del_partID").hide();    $("#day_ID").show();	$("#imp_partID,#imp_partID_1,#imp_partID_2").show();	$("#dateID").val('');
            }
	});
	
	$(".delete_import_log").click(function()
	{
		var dateID = $("#dateID").val();
				
		if(dateID != '')
		{
			ConfirmDialog('Are you sure that you want to delete import sheet for:',dateID);
		}
		else
		{
			alert('Please enter the date !...');
		}
				

	});
	
		
	function ConfirmDialog(message,dateID) 
	{
		$('<div></div>').appendTo('body')
		.html('<div><h6>'+message+'?</h6></div>')
		.dialog({
			modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () 
				{
                                    $('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');

                                    if(dateID != '')
                                    {
                                        $.ajax({			
                                                url : '../ajax/ajax_delete.php',
                                                type:'POST',
                                                data:{'request': 'Import_Sheet_Log' , 'ID':dateID},
                                                dataType:"json",				  
                                                success : function(data)			
                                                {
                                                    if(parseInt(data.Counts) > 0)   
                                                    {
                                                        alert(String(data.Msg));
                                                        location.reload();
                                                    }
                                                    else if(data.Status == 1)       
                                                    {
                                                        alert(String(data.Msg));
                                                        location.reload();
                                                    }
                                                },
                                                error: function(res)  {console.log('ERROR in Form')}				  
                                        });	
                                    }
                                    
                                    $(this).dialog("close");
				},
				No: function () {                                                                 
					$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');
	
					$(this).dialog("close");
				}
			},
			close: function (event, ui) {
				$(this).remove();
			}
		});
	};
});
</script>