<?PHP
	include '../includes.php';        
	$index = new Functions();
        
        $ID =   isset($_GET['i'])   ?   $index->Decrypt($_GET['i']) :   '';
        
        $PR_Array  = $ID > 0  ? $index->select('accident_regis',array("*"), " WHERE ID = ".$ID." ") : '';
        $DR_Array  = $PR_Array[0]['staffID'] > 0    ? $index->select('employee',array("*"), " WHERE ID = ".$PR_Array[0]['staffID']." ") : '';
?>

<html>
    <head>
        <title>Record of Action</title>
        <meta charset="windows-1252">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    
    <script type="text/javascript">
        function PrintData()
        {
            window.print();
        }
    </script>
    
    <body onload="PrintData()">

<table width="100%" border="0" style="border-collapse:collapse !important; border-color:#666" cellpadding="2" cellspacing="2">
  <tr>
    <td colspan="4"><div align="center"><b style="font-size:20px;">Record of Action </b></td>
  </tr>
  
  <tr><td colspan="4">&nbsp;</td></tr>
  <tr><td colspan="4">&nbsp;</td></tr>
  
  <tr>
      <td colspan="2" style="font-size:17px;">Ref No :&nbsp;&nbsp;<?=$PR_Array[0]['refno']?></td>    
      <td colspan="2" style="font-size:17px;">Date :&nbsp;&nbsp;<?=$index->VdateFormat($PR_Array[0]['dateID'])?></td>
  </tr>
  
  <tr>
      <td colspan="2" style="font-size:17px;">Claim No :&nbsp;&nbsp;<?=$PR_Array[0]['claimno']?></td>    
      <td colspan="2" style="font-size:17px;">Invoice No :&nbsp;&nbsp;<?=$PR_Array[0]['invno']?></td>
  </tr>
  <tr>
      <td colspan="2" style="font-size:17px;">Bus No :&nbsp;&nbsp;<?=$PR_Array[0]['busID']?></td>    
      <td colspan="2" style="font-size:17px;">Driver :&nbsp;&nbsp;<?=($DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].' - '.$DR_Array[0]['code'])?></td>
  </tr>
  
  
</table><br />

<table width="100%" border="1" style="border-collapse:collapse !important; border-color:#666" cellpadding="2" cellspacing="2">
    <tr style="font-weight:bold; background-color:#CECECE;">
    <td align="center">Sr. No.</td>
    <td align="center">Date</td>
    <td>Accidents Detail/Remarks</td>
  </tr>
  
<?PHP
    $Qry = $index->DB->prepare("SELECT * FROM accident_regis_dtl WHERE ID > 0 AND ID = ".$ID." ");
    $Qry->execute();
    $index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
    if(is_array($index->rows) && count($index->rows) > 0)
    {
        $srID = 1;
        foreach ($index->rows as $rows)
        {
?>
            <tr>
                    <td align="center"><?=$srID++?></td>
                    <td align="center"><?=$index->VdateFormat($rows['fieldID_1'])?></td>
                    <td><?=($rows['fieldID_2'])?></td>
           </tr>
<?PHP
        }
    }
?>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  
</table>
<p>&nbsp;</p>
</body>
</html>


    </body>
</html>
</script>