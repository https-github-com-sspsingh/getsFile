<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/mechanics.php';
	$masters = new Masters();
	
	$basename	=   basename($_SERVER['PHP_SELF']);	
	$action      =   isset($_GET['a'])		?	$masters->Decrypt($_GET['a'])               :   'create';
	$id		  =    isset($_GET['i'])	   ?	$masters->Decrypt($_GET['i'])               :   '';
	$message	 =   isset($_GET['m'])		?	urldecode($masters->Decrypt($_GET['m']))	:   '';
	$type    	=   isset($_GET['t'])		?	$masters->Decrypt($_GET['t'])               :   '';

	$mfdateID	 =   isset($_GET['mfdateID'])	 ?	$_GET['mfdateID']       :	'';
	$mtdateID	 =   isset($_GET['mtdateID'])	 ?	$_GET['mtdateID']	   :	'';
	$searchbyID  =   isset($_GET['searchbyID'])  ?	$_GET['searchbyID']	:	'';
 
	$headTitle	=   'Mechanic Master';
	
	$arrMCH = ($_GET['i'] > 0 ? $login->select('mechanic_mst',array("*"), " WHERE recID = ".$_GET['i']." ") : '');
?>
<input type="hidden" id="fieldID" value="101" />
<input type="hidden" id="fieldsID" value="<?=($action == 'view' ? 0 :($action == 'popups' ? 0 : 1))?>" />
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
                                <div class="box-tools">
                                    <div class="input-group">
                                    	<div class="input-group-btn">
                                  
<?PHP
if($login->Decrypt($_GET['a']) == 'create' && ($_GET['i'] > 0))
{
?>
	<div class="col-xs-8" style="padding-left:10px !important;">
	<div style="margin-left:8px; min-height:80px; border:solid 2px #F56954; width: 470px; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">
		
		<input type="hidden" id="rowID" value="<?=($_GET['i'])?>" />
		
		<div style="display:inline-block; margin-top: 6px;">
			<label style="width: 85px;">Date : </label>
			<input type="datable" class="form-control datepicker" value="<?=$login->VdateFormat($arrMCH[0]['dateID'])?>" data-datable="ddmmyyyy" name="mdateID" id="mdateID" placeholder="Enter Date" style="width:150px; text-align:center;" />
			<div id="error_mdateID" style="margin-left: 88px;"></div>
		</div> 
		
		<br />
		<div style="display:inline-block; margin-top: 6px;">
			<label style="width: 85px;">Depot Name : </label>
			
			<select onchange="changes=true;" class="form-control" id="depotID" name="depotID">
			<option value="0" selected="selected" disabled="disabled">-- Select Depot --</option>
			
			<option value="1" <?=($arrMCH[0]['typeID'] == 1 ? 'selected="selected"' : '')?>>Beenyup, Karrinyup & Shenton Park</option>
			<option value="2" <?=($arrMCH[0]['typeID'] == 2 ? 'selected="selected"' : '')?>>Midvale & Beckenham</option>
			<option value="3" <?=($arrMCH[0]['typeID'] == 3 ? 'selected="selected"' : '')?>>Canning Vale & Southern River</option>
			<option value="4" <?=($arrMCH[0]['typeID'] == 4 ? 'selected="selected"' : '')?>>Bunbury & Busselton</option>
			<option value="5" <?=($arrMCH[0]['typeID'] == 5 ? 'selected="selected"' : '')?>>Albany</option>
			
			</select>
			<div id="error_depotID" style="margin-left: 88px;"></div>
		</div>	
		
		<br />
		<div style="display:inline-block; margin-top: 6px; margin-bottom: 6px;">
			<label style="width: 85px;">Staff Name : </label>
			
			<select onchange="changes=true;" class="form-control select2" id="staffID" name="staffID">
			<option value="0" selected="selected" disabled="disabled">-- Select Staff --</option>
			<?PHP
			
			$Qry = $login->DB->prepare("SELECT ID, code, full_name FROM employee WHERE status = 1 AND desigID In(418,445,10) Order By code ASC ");
			if($Qry->execute())	
			{	
			  $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			  foreach($login->rows as $row)
			  {
				  echo '<option value="'.$row['ID'].'" '.($arrMCH[0]['empID'] == $row['ID'] ? 'selected="selected"' : '').'>'.$row['full_name'].' ('.$row['code'].')'.'</option>';
			  }		  
			}
			
			?>
			</select>
			<div id="error_staffID" style="margin-left: 88px;"></div>
		</div>		

		<br />
		<div style="display:inline-block; margin-top: 6px; margin-bottom: 6px;">
			<label style="width: 85px;">&nbsp</label>
			
			<button class="btn btn-flat btn-warning" style="background:#F56954;" id="updateMechanic">Update Mechanic </button>
		</div>	
	</div>
	</div>
	
<?PHP
}
else
{
?>
	<div class="col-xs-2" style="padding-left:10px !important;" id="addSectionID">								  
		<div class="row" style="padding-left:10px !important;">
		<div class="col-md-1"><a style="margin-left:4px;" id="addNewID" class="btn btn-danger btn-flat btn-flat fa fa-plus">&nbsp;&nbsp;ADD New Mechanic </a></div> 	
		</div>
	</div>

	<div class="col-xs-8" style="padding-left:10px !important; display:none;" id="empgridID">
	<div style="margin-left:8px; min-height:80px; border:solid 2px #F56954; width: 470px; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">
		
		<div style="display:inline-block; margin-top: 6px;">
			<label style="width: 85px;">From Date : </label>
			<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="mfdateID" id="mfdateID" placeholder="Enter Date" style="width:150px; text-align:center;" />
			<div id="error_mfdateID" style="margin-left: 88px;"></div>
		</div>
		<br />
		
		<div style="display:inline-block; margin-top: 6px;">
			<label style="width: 85px;">To Date : </label>
			<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="mtdateID" id="mtdateID" placeholder="Enter Date" style="width:150px; text-align:center;" />
			<div id="error_mtdateID" style="margin-left: 88px;"></div>
		</div>
		
		<br />
		<div style="display:inline-block; margin-top: 6px;">
			<label style="width: 85px;">Depot Name : </label>
			
			<select onchange="changes=true;" class="form-control" id="depotID" name="depotID">
			<option value="0" selected="selected" disabled="disabled">-- Select Depot --</option>
			
			<option value="1">Beenyup, Karrinyup & Shenton Park</option>
			<option value="2">Midvale & Beckenham</option>
			<option value="3">Canning Vale & Southern River</option>
			<option value="4">Bunbury & Busselton</option>
			<option value="5">Albany</option>
			
			</select>
			<div id="error_depotID" style="margin-left: 88px;"></div>
		</div>	
		
		<br />
		<div style="display:inline-block; margin-top: 6px; margin-bottom: 6px;">
			<label style="width: 85px;">Staff Name : </label>
			
			<select onchange="changes=true;" class="form-control select2" id="staffID" name="staffID">
			<option value="0" selected="selected" disabled="disabled">-- Select Staff --</option>
			<?PHP
			
			$Qry = $login->DB->prepare("SELECT ID, code, full_name FROM employee WHERE status = 1 AND desigID In(418,445,10) Order By code ASC ");
			if($Qry->execute())	
			{	
			  $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			  foreach($login->rows as $row)
			  {
				  echo '<option value="'.$row['ID'].'">'.$row['full_name'].' ('.$row['code'].')'.'</option>';
			  }		  
			}
			
			?>
			</select>
			<div id="error_staffID" style="margin-left: 88px;"></div>
		</div>		

		<br />
		<div style="display:inline-block; margin-top: 6px; margin-bottom: 6px;">
			<label style="width: 85px;">&nbsp</label>
			
			<button class="btn btn-flat btn-warning" style="background:#F56954;" id="saveMechanic">Save Mechanic</button>
		</div>	
	</div>
	</div>
<?PHP
}
?>


                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                                       


                            </div><!-- /.box-header -->
                            	 
                           
                           	<div class="box-body table-responsive"> 
                                <?PHP 
									$masters->createForm();
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

<script type="text/javascript">
$("#updateMechanic").click(function()
{
	var mdateID = $("#mdateID").val();
	var depotID = $("#depotID").val();
	var staffID = $("#staffID").val();
	var rowID = $("#rowID").val();

	$("#error_mdateID,#error_depotID,#error_staffID").html('');
	
	if(mdateID == '' || depotID <= 0 || staffID <= 0)
	{
		$("#error_mdateID").html((mdateID == '' ? '<b style="color:red;"> Enter From Date.</b><br />' : ''));
		$("#error_depotID").html((depotID <= 0 ? '<b style="color:red;"> Plz Select Depot Name.</b><br />' : ''));
		$("#error_staffID").html((staffID <= 0 ? '<b style="color:red;"> Plz Select Staff Name.</b><br />' : ''));
	}
	else
	{ 
		$("#mdateID").prop('readonly',true);
		$("#depotID,#staffID").prop('disabled',true);
		$("#saveMechanic").prop('disabled',true);
	
		$.ajax({			
		url : '../ajax/ajax.php',
		type:'POST',
		data:{'request': 'Update_MechanicData' , 'dateID':mdateID , 'depotID':depotID , 'staffID':staffID, 'rowID':rowID},
		dataType:"json",				  
		success : function(data)			
		{
			if(data.statusID == 2)
			{
				var pageID = document.location.pathname.match(/[^\/]+$/)[0];
				alert('Data Is Updated Successfully...');
				window.location = pageID;
				
			}
			else if(data.statusID == 1)
			{
				alert('Entered From/To Date Is Not Valid ...');		
				$("#mdateID").prop('readonly',false);	
				$("#depotID,#staffID").prop('disabled',false);					
				$("#updateMechanic").prop('disabled',false);					
				$("#mdateID").val('');
				$("#mdateID").focus();
			}
			else
			{
				alert('Error In Code  ...');
				location.reload();
			}
			
		},
		error: function(res)  {console.log('ERROR in Form')}				  
		});
	}
	
	//errorString
});


$(".mecheditID").click(function()
{
	var rowID = $(this).attr('aria-sort');
	rowID = (isNaN(rowID) || rowID == '' || typeof rowID === 'undefined') ? 0 : parseInt(rowID);
	
	if(rowID > 0)
	{
		$("#addSectionID,#empgridID").hide();
		$("#empeditID").show();
		
		window.location.href = 'mechanics.php?a=n+8PH9A4YqZ5pUkEIGtfzqc5NL2jdQhpz4H2MpBICDM&i='+rowID;
	}
});

$("#saveMechanic").click(function()
{
	var mfdateID = $("#mfdateID").val();
	var mtdateID = $("#mtdateID").val();
	var depotID = $("#depotID").val();
	var staffID = $("#staffID").val();

	$("#error_mfdateID,#error_mtdateID,#error_depotID,#error_staffID").html('');
	
	if(mfdateID == '' || mtdateID == '' || depotID <= 0 || staffID <= 0)
	{
		$("#error_mfdateID").html((mfdateID == '' ? '<b style="color:red;"> Enter From Date.</b><br />' : ''));
		$("#error_mtdateID").html((mtdateID == '' ? '<b style="color:red;"> Enter To Date.</b><br />' : ''));
		$("#error_depotID").html((depotID <= 0 ? '<b style="color:red;"> Plz Select Depot Name.</b><br />' : ''));
		$("#error_staffID").html((staffID <= 0 ? '<b style="color:red;"> Plz Select Staff Name.</b><br />' : ''));
	}
	else
	{ 
		$("#mfdateID,#mtdateID").prop('readonly',true);
		$("#depotID,#staffID").prop('disabled',true);
		$("#saveMechanic").prop('disabled',true);
	
		$.ajax({			
		url : '../ajax/ajax.php',
		type:'POST',
		data:{'request': 'Insert_MechanicData' , 'fdateID':mfdateID , 'tdateID':mtdateID , 'depotID':depotID , 'staffID':staffID},
		dataType:"json",				  
		success : function(data)			
		{
			if(data.statusID == 2)
			{
				alert('Data Is Submit Successfully...');
				location.reload();
			}
			else if(data.statusID == 1)
			{
				alert('Entered From/To Date Is Not Valid ...');		
				$("#mfdateID,#mtdateID").prop('readonly',false);	
				$("#depotID,#staffID").prop('disabled',false);					
				$("#saveMechanic").prop('disabled',false);					
				$("#mfdateID,#mtdateID").val('');
				$("#mfdateID").focus();
			}
			else
			{
				alert('Error In Code  ...');
				location.reload();
			}
			
		},
		error: function(res)  {console.log('ERROR in Form')}				  
		});
	}
	
	//errorString
});

$("#addNewID").click(function()
{
	$("#addSectionID").hide();
	$("#empgridID").show();
	$(".select2").select2();
});

    $(".mechrowID").confirm
    ({
        title:"<b style='color:#1591E0;'>Delete confirmation</b>",
        text:"Are you sure you want to delete this record !. ",
        confirm: function(button) 
        {	
            var request = 'DELETE_MECHANIC_ROW';
            var ID      = $(button).attr('aria-sort');
            DELETE_MECHANIC_ROW(ID,request);
        },
        cancel: function(button) 
        {},
        cancelButtonClass: 'btn-primary btn-flat',
        confirmButtonClass: 'btn-primary btn-flat',
        confirmButton: "&nbsp;&nbsp;Yes&nbsp;&nbsp;",
        cancelButton: "&nbsp;&nbsp;No&nbsp;&nbsp;"
    });

    var DELETE_MECHANIC_ROW = function(ID,request)
    {
        if(parseInt(ID) > 0 && request != '')
        {
            $.ajax({			
            url : '../ajax/ajax_delete.php',
            type:'POST',
            data:{'request': request , 'ID':ID},
            dataType:"json",				  
            success : function(data)			
            {
                location.reload();
            },
            error: function(res)  {console.log('ERROR in Form')}				  
            });	
        }
    }; 
</script>