<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
	
    $todayID = date('Y-m-d');
    $casesID = ($_GET['casesID'] > 0 ? $_GET['casesID'] : 1);
	
    if(count($_REQUEST['filterID']) > 0 )
    {
		$_SESSION[$login->website]['filter_compID'] = implode(',',$_REQUEST['filterID']);
		
        $array = array();
        $array['userID'] = $_SESSION[$login->website]['userID'];
        $array['frmNM']  = 'DRIVER_SIGN_ON';
        $array['filterTX'] = implode(',',$_REQUEST['filterID']);
        $login->BuildAndRunInsertQuery('filter_logsid',$array);
    }
    
    $arrID = $login->select('filter_logsid',array("recID,filterTX"), " WHERE frmNM = 'DRIVER_SIGN_ON' AND userID = ".$_SESSION[$login->website]['userID']." Order By recID DESC LIMIT 1 ");
    
    if($arrID[0]['recID'] > 0)
    {
        $companyID = $arrID[0]['filterTX'];
        $SQL = "DELETE FROM filter_logsid WHERE recID <> ".$arrID[0]['recID']." AND frmNM = 'DRIVER_SIGN_ON' AND userID = ".$_SESSION[$login->website]['userID']." ";			
        $Qry = $login->DB->prepare($SQL);
        $Qry->execute();
    }
    else    {$companyID = (implode(',',$_REQUEST['filterID']) <> '' ? implode(',',$_REQUEST['filterID']) : $_SESSION[$login->website]['ecomID']);}
    
    /* automated generated signona allocations */
    $TIndex->GenerateSignOnAllocation($todayID,$companyID);
    
	/* colour-scheme */
    $duplicateDATA = $GIndex->SignOn_Duplicacy($todayID,$companyID);
	
    /* pending/signed counts */
    $pendingID = $GIndex->DriverSignOnSheets($todayID,$companyID,2,$duplicateDATA['empNO'],$duplicateDATA['busNO']);
    $singonsID = $GIndex->DriverSignOnSheets($todayID,$companyID,1,$duplicateDATA['empNO'],$duplicateDATA['busNO']);
    $busalloID = $GIndex->DriverBusAlcSheets($todayID,$companyID,2);
    $mechansID = $GIndex->DriverMechanicsSheets($todayID);
	
    echo '<aside class="right-side strech">';
    echo '<section class="content">';
	
    echo '<input type="hidden" id="SwappingStartEndTime" value="1" />';
	echo '<input type="hidden" id="SwappingRunningsTime" value="0" />';
    
    echo '<form method="get" id="SIGNON_form" action="'.$login->home.'profile_4.php" enctype="multipart/form-data" style="margin-top:-32px;">';
        echo '<div class="row" style="margin-top: 18px;margin-left: -5px;">'; 
        echo '<div class="col-xs-'.($casesID == 1 ? '6' : '7').'" style="border-radius:5px; border:#D9006C 2px solid;padding: 10px;">';
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
        
        echo '<div class="col-xs-1" style="border-radius:5px; border:#D9006C 2px solid;padding:8px; margin-left:10px;">';
            echo '<b style="font-size:15px; color:#367FA9;"> Timer : <b id="timerID">60</b> Sec</b>';
        echo '</div>'; 

        echo '<div class="col-xs-1"><input type="submit" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid;" value="Filter Data" /></div>';
        
        echo '<div class="col-xs-1"><a href="profile_4.php" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid;">Reset Filter</a></div>';    
        if($casesID == 1)
        {
            echo '<div class="col-xs-1"><a class="btn bg-navy btn-flat margin getinfoID" id="getinfoID" style="border: #F56954 1px solid;" disabled="disabled">Update Swapping</a></div>';
        }
		
		echo '<div class="col-xs-1"><a target="_blank" href="'.$login->home.'forms/stfare.php?a='.$login->Encrypt('create').'" class="btn bg-navy margin btn-flat" style="margin-left:40px; border: #F56954 1px solid;">RADIO LOG</a></div>';
        echo '</div>';
        echo '<input type="hidden" value="'.$casesID.'" name="casesID" />';
    echo '</form>';
    
    echo '<div class="row"><br />';
        echo '<div class="col-md-12">';
        echo '<div class="nav-tabs-custom">';
            echo '<ul class="nav nav-tabs pull-right">';
			
			echo '<li><a href="#tab_8-8" data-toggle="tab"><b style="color:#367FA9;">Reverse Allocation</b> <b style="color:#D9006C !important;">('.$busalloID['countID'].')</b></a></li>';
            echo '<li><a href="#tab_9-9" data-toggle="tab"><b style="color:#367FA9;">After Hour Mechanic</b> <b style="color:#D9006C !important;">('.$mechansID['countID'].')</b></a></li>';
            echo '<li><a href="#tab_7-7" data-toggle="tab"><b style="color:#367FA9;">Assigned</b> <b style="color:#D9006C !important;">('.$singonsID['countID'].')</b></a></li>';
            echo '<li class="active"><a href="#tab_6-6" data-toggle="tab"><b style="color:#367FA9;">Pending</b> <b style="color:#D9006C !important;">('.$pendingID['countID'].')</b></a></li>';
			
            echo '<li class="pull-left header"><i class="fa fa-th"></i> Driver SignOn <b style="margin-left:180px; color:#F20079;" id="ClockTimers"></b></li>';			
            echo '</ul>';

            echo '<div class="tab-content">';
			
				echo '<div class="tab-pane active" id="tab_6-6" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';
				echo '<div id="redips-drag">';
					echo ($pendingID['fileID']);
				echo '</div>';
				echo '</div>';
				
				echo '<div class="tab-pane" id="tab_7-7" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
					echo ($singonsID['fileID']);
				echo '</div>';
				
				echo '<div class="tab-pane" id="tab_9-9" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
					echo ($mechansID['fileID']);
				echo '</div>';
				
				echo '<div class="tab-pane" id="tab_8-8" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
					echo ($busalloID['fileID']);
				echo '</div>';
				
            echo '</div>';
        echo '</div>';
        echo '</div>';
    echo '</div>';
    
    echo '</section>';
    echo '</aside>';
    
    include 'footer.php'; 
?>
<!--<script type="text/javascript" src="<?=$login->home?>js/table-dnd/jquery.tablednd.js"></script>-->
<script type="text/javascript">
$(document).ready(function()
{
	$("#PrintPage").click(function()
	{
		data = $("#datatablesRSV").html();
		
		var content   = (data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
		var mywindow  = '';
		mywindow = window.open('', 'View Report', 'height=800,width=1250');
		mywindow.document.write('<html><head><title>Reverse Allocation Report</title>');
		mywindow.document.write('</head>');
		mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b>Reverse Allocation Report</b></th></tr></br></br>');
		mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');	
		mywindow.document.write('</body></html>');
		mywindow.print();
		mywindow.close();
		return true;
	
	});
});
</script>

<script type="text/javascript">

    function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
	
	$(".shifts_doneID").click(function()
	{
		var recID = $(this).attr('aria-sort');
		var stsID = $(this).attr('aria-busy');
		var empNM = $(this).attr('aria-title');
		var shfNO = $(this).attr('aria-label');

		var titSTRING = (stsID == 2 ? 'Shift Signon Confirmation' :(stsID == 1 ? 'Signon Undo Confirmation' : 'Shifts Confirmation'));
		
		var msgSTRING = 'Do you realy want to change the Sign-On status of <br /><br /><b style="color:blue; font-size:15px;">' + empNM + '</b> on shift no : <b style="color:red; font-size:15px;">' + shfNO + '</b>';
		
		ShiftSigonConfirm(msgSTRING,titSTRING,recID,stsID);
		
	});
	
	function ShiftSigonConfirm(msgSTRING,titSTRING,recID,stsID) 
    {
        $('<div></div>').appendTo('body')
        .html('<div><h6>'+msgSTRING+'</h6></div>')
        .dialog({
			modal: true, title: titSTRING, zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () 
				{
					if(recID != '' && stsID != '')
					{
						$.ajax({
						url : 'ajax/ajax_DBpopups.php',
						type:'POST',
						data:{'request': 'SHIFT_STATUS_CONFIRM' , 'recID':recID , 'stsID':stsID},
						dataType:"json",				  
						success : function(data)			
						{
							if(data.success == 1)
							{
								window.location.reload();
							}
					
						},	error: function(res)  {console.log('ERROR in Form')}
						});
					}
					
					$(this).dialog("close");
				},
				No: function () 
				{ 
					$(this).dialog("close");
				}
			},
			close: function (event, ui) 
			{
				$(this).remove();
			}
        });
	};
	
    $(document).ready(function()
    {		
        $(".shiftcomments").prop('tabIndex', -1);
        $(".othersinfos").prop('tabIndex', -1);
        
        $(".getinfoID").click(function()
        {
			$("#getinfoID").stop(true, true).fadeIn();
			
			var SwappingLISTS = [];
            $("tbody").find("tr").each(function() 
            {
                $("#SwappingStartEndTime").val(2);                
                $('.margin').attr('disabled','disabled');
                $('#getinfoID').attr('disabled','disabled');
                $('.SIGNONcaseID').attr('disabled','disabled');
                
                var rowID = $(this).find('td.metup').attr('aria-sort');
                var rowTX = $(this).find('td.metup').text().trim();
                
                if((rowID != '') && (rowTX != ''))
                {
                    var newCD = ''; var srID = 0;
                    for(srID = 0; srID <= 4;  srID++)
                    {
						if(rowTX[srID] != '')   {newCD += rowTX[srID];}
                    }
                    
                    var ar = {};
                    ar['rowID']  = rowID;
                    ar['rowTX']  = rowTX;
                    ar['newCD']  = newCD.trim();
                    SwappingLISTS.push(ar);
                }
            });
			
			/*console.log(SwappingLISTS);	console.log(SwappingLISTS.length);*/

            if(SwappingLISTS.length > 0)
            {
                $.ajax({			
                url : 'ajax/ajax_dragdrop.php',
                type:'POST',
                data:{'request': 'CHECK_DRAG_DROP_AB_STATUS' , 'partLISTS':SwappingLISTS},
                dataType:"json",				  
                success : function(data)			
                {
                    /*alert(JSON.stringify(data.DragDropLISTS));*/
                    /*alert(JSON.parse(data.DragDropLISTS));*/
                    
                    var jsonARRAY = data.DragDropLISTS;                    
                    var jsonCOUNT = data.countID;
                    
                    var runID = 0;  var retun_runID = 0;
                    $.each(jsonARRAY, function(jsonARRAY, obj) 
                    {
                        //alert(obj.statusID + ' - ' + obj.tagCD + ' - ' + obj.shiftNOS + ' - ' + obj.rowID + ' - ' + obj.rowTX + ' - ' + obj.newCD);
                        
                        var RES_statusID = parseInt(obj.statusID);
                        var RES_tagCD    = obj.tagCD;
                        var RES_shiftNOS = parseInt(obj.shiftNOS);
                        var RES_rowID    = obj.rowID;
                        var RES_rowTX    = obj.rowTX;
                        var RES_newCD    = obj.newCD;
                        
                        if(parseInt(RES_statusID) == 1)
                        {
                            ShiftConfirmDialog('Do you want to swap second half of '+RES_shiftNOS+' as well.',RES_rowID,RES_rowTX);
                        }
                        else
                        {
                            $.ajax({
                            url : 'ajax/ajax_dragdrop.php',
                            type:'POST',
                            data:{'request': 'UPDATE_DRAG_DROP_AB_STATUS' , 'rowID':RES_rowID , 'rowTX':RES_rowTX , 'statusID':1},
                            dataType:"json",				  
                            success : function(data)			
                            {},	error: function(res)  {console.log('ERROR in Form')}
                            });
                        }
						
                        //alert('jsonCOUNT : ' + jsonCOUNT + ' , runID : ' + runID);
                        runID++;
                        retun_runID = runID;                        
                    });            
                    
                    /*alert(jsonCOUNT + ' - ' + retun_runID);*/
                    
                    if(jsonCOUNT == retun_runID)
                    {
                        /*alert(retun_runID);   alert('thats are done .....');*/
                        
                        for(partID = 1; partID <= 2000; partID++)
                        {
                            if(parseInt(partID) == 2000)
                            {
                                //alert('Swapp elements are updated.....');                                
                                setTimeout(function()
                                {
                                    window.location.reload(true);
                                }, 15000);
								
                                document.getElementById('timerID').innerHTML = 15;
                            }
                        }
                    }
                },
                error: function(res)  {console.log('ERROR in Form')}				  
                });
            }
        });
        
		//setInterval('swappingPage()', 1000);
        setInterval('refreshPage()', 60000);
        timedCount();
    });

    function updateMASTERSoptions(valueTEXT,recID,fieldNM)
    {
        if(parseInt(recID) > 0 && fieldNM != '')
        {		
            $.ajax({			
            url : 'ajax/ajax_DBpopups.php',
            type:'POST',
            data:{'request': 'UPDATE_MASTERS_FIELDS' , 'recID':recID , 'valueTEXT':valueTEXT , 'fieldNM':fieldNM},
            dataType:"json",				  
            success : function(data)			
            {
            },
            error: function(res)  {console.log('ERROR in Form')}				  
            });
        }
    }
    
    function timedCount() 
    {
        var count = document.getElementById('timerID');
        timeoutfn = function()
        {
            count.innerHTML = parseInt(count.innerHTML) - 1;
            setTimeout(timeoutfn, 1000);
        };
        setTimeout(timeoutfn, 1000);
    }
	
    function refreshPage()  
    {
        if(document.getElementById('SwappingStartEndTime').value == 1)	{window.location.reload(true);}
    } 
    
    function ShiftConfirmDialog(message,rowID,rowTX) 
    {
        $('<div></div>').appendTo('body')
        .html('<div><h6>'+message+'?</h6></div>')
        .dialog({
			modal: true, title: 'CONFIRM', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () 
				{
					if(rowID != '' && rowTX != '')
					{
						$.ajax({
						url : 'ajax/ajax_dragdrop.php',
						type:'POST',
						data:{'request': 'UPDATE_DRAG_DROP_AB_STATUS' , 'rowID':rowID , 'rowTX':rowTX , 'statusID':2},
						dataType:"json",				  
						success : function(data)			
						{},	error: function(res)  {console.log('ERROR in Form')}
						});
					}
					
					$(this).dialog("close");
				},
				No: function () 
				{
					if(rowID != '' && rowTX != '')
					{
						$.ajax({
						url : 'ajax/ajax_dragdrop.php',
						type:'POST',
						data:{'request': 'UPDATE_DRAG_DROP_AB_STATUS' , 'rowID':rowID , 'rowTX':rowTX , 'statusID':1},
						dataType:"json",				  
						success : function(data)			
						{},	error: function(res)  {console.log('ERROR in Form')}
						});
					}

					$(this).dialog("close");
				}
			},
			close: function (event, ui) 
			{
				$(this).remove();
			}
        });
	};
</script>