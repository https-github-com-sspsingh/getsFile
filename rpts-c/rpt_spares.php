<!doctype html>
<?PHP
	include '../includes.php';
	
	$ID 	=	isset($_GET['i'])		?	$Index->Decrypt($_GET['i'])	:	'';
	
	$arrSP = $ID > 0 ? $Index->select('spare_regis',array("*"), " WHERE ID = ".$ID." ") : ''; 
	
?>	
<html>

<head>

<script type="text/javascript">
function Print()
{
	window.print();
}
</script>

    <meta charset="utf-8">

    <title><?=($titleID)?></title>

	<link href="<?=$login->home?>css/reports.css" rel="stylesheet" type="text/css" />
</head>



<body onLoad="Print()">

<div class="invoice-box">

    
  
<table width="100%" rules="none" border="1" style="border-top:none;"  bordercolor="#000000" height="auto">

	<tr>
    <td width="85%" align="center" style="text-align:center;vertical-align:middle;"><strong class="main-title">Spare Master Lists</strong></td>
	</tr>
</table>

      <table width="100%" border="1" rules="none"cellspacing="0" cellpadding="0"  bordercolor="#000000">

  <tr class="heading">
    <td colspan="2">&nbsp;</td>
    <td colspan="5">Report Dated : <?=$login->VdateFormat($arrSP[0]['dateID'])?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  </tr>
</table>

 



   <?PHP
		$Qry_D = $Index->DB->prepare("SELECT * FROM spare_regis_dtl WHERE forID = 1 AND hiddenID <= 0 AND ID = ".$ID." Order By recID ASC ");
		$Qry_D->execute();
		$Index->rows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($Index->rows) && count($Index->rows) > 0)
		{
?>
	<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#000000" rules="all">

	<tr class="heading" >

	<td colspan="5" style="vertical-align: middle;" height="30"><div class="dts-hd-data" align="center">All Spare Drivers Lists</div></td>

	</tr> 

	<tr class="heading" >

	<td width="3%"><div class="dts-hd-data" align="center">Sr No</div></td>
	<td width="35%"><div class="dts-hd-data" align="center">Driver Name</div></td>
	<td width="10%"><div class="dts-hd-data" align="center">Phone No</div></td>
	<!--<td width="15%"><div class="dts-hd-data" align="center">Location</div></td>
	<td width="20%"><div class="dts-hd-data" align="center">Suburb</div></td>-->
	<td width="10%"><div class="dts-hd-data" align="center">Available</div></td>
	<td width="8%"><div class="dts-hd-data" align="center">Time</div></td>

	</tr> 


<?PHP
			$srID = 1;
			foreach($Index->rows as $row)
			{
				$arrEM  = (int)$row['fieldID_1'] > 0  ? $Index->select('employee',array("*"), " WHERE ID = ".((int)$row['fieldID_1'])." ") : '';
				$FN_Array  = (int)$row['fID_2'] > 0  ? $Index->select('mast',array("*"), " WHERE ID = ".((int)$row['fID_2'])." ") : '';
				
				  echo '<tr>';
					echo '<td><div class="dts-data" align="center">'.$srID++.'</div></td>';
					echo '<td><div class="dts-items-data" align="left">'.strtoupper($arrEM[0]['full_name']).' - <b>'.strtoupper($arrEM[0]['code']).'</b></div></td>';
					echo '<td><div class="dts-items-data">'.strtoupper($row['fieldID_2']).'</div></td>';
					
					/*echo '<td><div class="dts-items-data">'.strtoupper($row['fieldID_3']).'</div></td>';
					echo '<td><div class="dts-items-data">'.strtoupper($row['fieldID_4']).'</div></td>';*/
					
					echo '<td><div class="dts-items-data" align="center">'.($row['fieldID_8'] == 1 ? 'After' :($row['fieldID_8'] == 2 ? 'Any Time' :($row['fieldID_8'] == 3 ? 'Available Untill' : ''))).'</div></td>';
					
					echo '<td><div class="dts-items-data" align="center">'.($row['fieldID_6']).'</div></td>';
				  echo '</tr>'; 
				   
			}
?>
 
  </table>
  
<?PHP
		}
   ?>
  

 
	<table width="100%" border="1" rules="none">
	<tr><td colspan="3"><br /></td></tr>
	</table>
	
	

   <?PHP
		$Qry_D = $Index->DB->prepare("SELECT * FROM spare_regis_dtl WHERE forID = 2 AND hiddenID <= 0 AND ID = ".$ID." Order By recID ASC ");
		$Qry_D->execute();
		$Index->rows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($Index->rows) && count($Index->rows) > 0)
		{
	?>
		<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#000000" rules="all">

		<tr class="heading" >

		<td colspan="7" style="vertical-align: middle;" height="30"><div class="dts-hd-data" align="center">All Spare Buses Lists</div></td>

		</tr> 

		<tr class="heading" >

		<td width="3%"><div class="dts-hd-data" align="center">Sr No</div></td>
		<td width="35%"><div class="dts-hd-data" align="center">Bus No</div></td>

		</tr> 


	<?PHP
			$srID = 1;
			foreach($Index->rows as $row)
			{
				$arrBS  = (int)$row['fieldID_1'] > 0  ? $Index->select('buses',array("*"), " WHERE ID = ".((int)$row['fieldID_1'])." ") : '';
				
				  echo '<tr>';
					echo '<td><div class="dts-data" align="center">'.$srID++.'</div></td>';
					echo '<td><div class="dts-items-data" align="left">'.strtoupper($arrBS[0]['busno'].' - '.$arrBS[0]['modelno'].' - '.$arrBS[0]['title']).'</div></td>';
				  echo '</tr>'; 
			}
	?>
	
	</table>
	
	<?PHP
		}
   ?>
  
 
  
  
</div>

</body>

</html>

