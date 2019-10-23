<?PHP
	include '../includes.php';	
	
	$arrayST = ($login->Decrypt($_GET['i']) > 0 ? $login->select('shift_masters',array("*"), " WHERE ID = ".$login->Decrypt($_GET['i'])." ") : '');				
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shift Setter Reports</title>    
    <link href="<?=$login->home?>css/reports.css" rel="stylesheet" type="text/css" />    
</head>
<script src="<?=$login->home?>rpts-c/jquery.min.js"></script>
<script type="text/javascript">
function PrintPage()
{
	//window.print();
}

function UpdateBusType(busTYPE,tagID,recID)
{
	if(parseInt(recID) > 0 && parseInt(tagID) > 0)
	{
		/*alert(busTYPE + ' - ' + recID);*/
		
		$.ajax({			
		url : '../ajax/ajax.php',
		type:'POST',
		data:{'request': 'UPDATE_BUSTYPE' , 'tagID':tagID , 'recID':recID , 'busTYPE':busTYPE},
		dataType:"json",				  
		success : function(data)			
		{
			
		},
		error: function(res)  {console.log('ERROR in Form')}				  
		});
	}
}

</script>

<body onLoad="PrintPage()">

<div class="invoice-box" id="datasheets" style="max-width:99% !important;">
    
 <!-- <div align="center"><img src="logo.png" width="430" height="88"></div>-->

<table cellpadding="0" cellspacing="0" width="100%" border="1" style="border-bottom:hidden;" rules="none">
<tr class="information">
    <td><table></table></td>
</tr>

</table>
<table width="100%" rules="none" border="1" style="border-top:none;"  bordercolor="#000000" height="auto">

		<tr>
        
        <td>
            <a style="padding:5px; margin-top:35px; margin:10px; text-decoration:none; cursor:pointer; background:black; color:white;" class="btn btn-danger" href="<?=$login->home.'exportCSV.php?s=SHIFT_SETTER_SHEET&fromID='.$arrayST[0]['createDT'].'&i='.$login->Decrypt($_GET['i'])?>">Excel</a>
        </td>
	
    <td width="100%" align="center" style="text-align:center;vertical-align:middle; padding:10px;"><strong class="main-title">
    SHIFT SETTER REPORT : 
	
    <b style="font-size:18px; color:#C30;">
	<?PHP
		if($arrayST[0]['stypeID'] == 1)  {echo 'SIUO - School In University Out';}
		if($arrayST[0]['stypeID'] == 2)  {echo 'SOUI - School Out University In';}
		if($arrayST[0]['stypeID'] == 3)  {echo 'SOUO - School Out University Out';}
		if($arrayST[0]['stypeID'] == 4)  {echo 'SIUI - School In University In';}
		if($arrayST[0]['stypeID'] == 5)  {echo 'School IN';}
		if($arrayST[0]['stypeID'] == 6)  {echo 'School OUT';}
		if($arrayST[0]['stypeID'] == 7)  {echo 'Saturday';}
		if($arrayST[0]['stypeID'] == 8)  {echo 'Sunday';}
		if($arrayST[0]['stypeID'] == 9)  {echo 'Special Event';}
    ?>
    </b>
    
    </strong></td>

  </tr>
</table>

      



  <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#000000" rules="all">

  <tr class="heading">

    <td height="35" rowspan="2" style="vertical-align:middle"><div align="center">Sr. No.</div></td>
    <td height="35" rowspan="2" style="vertical-align:middle"><div align="center">Shift No</div></td>
    <td height="35" colspan="8" style="vertical-align:middle"><div align="center">SHIFT - FIRST HALF</div></td>
    <td height="35" colspan="9" style="vertical-align:middle"><div align="center">SHIFT - SECOND HALF</div></td>
    
    <td height="35" rowspan="2" style="vertical-align:middle"><div align="center">Total</div></td>
    <td height="35" rowspan="2" style="vertical-align:middle"><div align="center">OP Day</div></td>
  </tr>
  
  <tr class="heading">
    <td height="35" style="vertical-align:middle"><div align="center">On</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Ex Depot</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Stow</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Off</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Last Trip</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Last Loc</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Hours</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Bus Type</div></td>    
    
    <td height="35" style="vertical-align:middle"><div align="center">On</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Ex Depot</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Stow</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Off</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Last Trip</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Last Loc</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Hours</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">Bus Type</div></td>
    <td height="35" style="vertical-align:middle"><div align="center">SignOn Required</div></td>
  </tr>
  
<?PHP
	$Qry = $login->DB->prepare("SELECT * FROM shift_masters_dtl WHERE ID = ".$login->Decrypt($_GET['i'])." AND fID_1 <> '' Order By recID ASC  ");
	$Qry->execute();
	$login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
	$srID = 1;
	foreach($login->rows as $rows)
	{ 
	?>
	  
		  <tr>
			<td><div align="center"><?=$srID++?></div></td>
			<td><div align="center"><?=$rows['fID_1']?></div></td>
			<td align="center"><?=$rows['fID_2']?></td>
			<td align="center"><?=$rows['fID_3']?></td>
			<td align="center"><?=$rows['fID_4']?></td>
			<td align="center"><?=$rows['fID_7']?></td>                        
			<td align="center"><?=$rows['fID_5']?></td>
			<td><?=$rows['fID_6']?></td>                        
			<td align="center"><?=$rows['fID_8']?></td>
                        
                        
			<td align="center" style="vertical-align:middle; padding:2px;">
            	<input style="width:80px; text-align:center;" type="text" class="bt1" onkeydown="UpdateBusType(this.value,1,<?=$rows['recID']?>)" onChange="UpdateBusType(this.value,1,<?=$rows['recID']?>)" value="<?=$rows['fID_20']?>" />
            </td>
            
			<td align="center"><?=$rows['fID_9']?></td>
			<td align="center"><?=$rows['fID_10']?></td>
			<td align="center"><?=$rows['fID_11']?></td>
			<td align="center"><?=$rows['fID_12']?></td>
			<td align="center"><?=$rows['fID_13']?></td>
			<td><?=strtoupper($rows['fID_14'])?></td>            
			<td align="center"><?=$rows['fID_15']?></td>
            <td align="center" style="vertical-align:middle; padding:2px;">
            	<input style="width:80px; text-align:center;" type="text" class="bt2" onkeydown="UpdateBusType(this.value,2,<?=$rows['recID']?>)" onChange="UpdateBusType(this.value,2,<?=$rows['recID']?>)" value="<?=$rows['fID_21']?>" />
            </td>
            
            <td align="center" style="vertical-align:middle; <?=($rows['tickID'] == 1 ? 'background:#E6F9EC;' : '')?>">
            <input type="checkbox" class="choose_tickID bt3" aria-sort="<?=($rows['recID'])?>" aria-busy="<?=($rows['tickID'])?>" <?=($rows['tickID'] == 1 ? 'checked="checked"' : '')?> />
            </td>
			
			<td align="center"><?=$rows['fID_16']?></td>
			<td align="center"><?=$rows['fID_18']?></td>
		  </tr>
	
	<?PHP 
	}
?>


  <tr><td colspan="21" height="35"></td></tr>
  
</table>  
</div>
</body>
</html>
<script>
$(document).ready(function()
{
	/*$(".bt1").prop('tabIndex', -1);*/
	$(".bt2").prop('tabIndex', -1);
	$(".bt3").prop('tabIndex', -1);
        
        $(".choose_tickID").click(function()
        {
            var statusID = $(this).attr('aria-busy');
            var recID    = $(this).attr('aria-sort');
            
            $.ajax({			
                    url : '../ajax/ajax_DBpopups.php',
                    type:'POST',
                    data:{'request': 'UPDATE_SHIFT_CUTTOFF' , 'recID':recID},
                    dataType:"json",				  
                    success : function(data)			
                    {
                        if(data.success == true)
                        {
                            window.location.reload();
                        }
                    },
                    error: function(res)  {console.log('ERROR in Form')}				  
                    });
        });
});

</script>