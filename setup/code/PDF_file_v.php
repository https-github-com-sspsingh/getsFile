<?PHP
	error_reporting(0);
	ob_start();
	include_once '../main/includes.php';	
	//include_once 'trfile.php';	

	// include autoloader
	require_once 'dompdf/autoload.inc.php';
	
	// reference the Dompdf namespace
	use Dompdf\Dompdf;
	
	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	
	$index  = new Functions();	
	
	$dateID = isset($_REQUEST['dateID'])	? $index->Decrypt($_REQUEST['dateID'])	:	'';
	
	$file = '';
	if(!empty($dateID))
	{ 	
		$FL_Array = $index->select('w_shifts',array("*"), " Order By ID DESC LIMIT 1 ");
		$dateID_1 = $dateID;
			
		$file = '';
		
		$styleID = 'style="color:white;background:#317299;text-align:center;"'; 
		$file .= '<table width="100%" border="1" style="border-collapse:collapse !important; border-color:#666" cellpadding="2" cellspacing="2" 
		id="dataTables">';
		$file .= '<thead><tr>';
		$file .= '<th '.$styleID.' colspan="10">Weekly Roster Sheet 
		(From : '.date('d-M-Y',strtotime($dateID_1)).' / To : '.date('d-M-Y',strtotime($dateID_1.'+6Days')).')</th>';
		$file .= '</tr></thead>'; 
		$file .= '<thead><tr>';
		$file .= '<th '.$styleID.'>Sr. No.</th>';
		$file .= '<th '.$styleID.'>Emp - ID</th>';
		$file .= '<th '.$styleID.'>Emp - Name</th>';
		for($srID = 0; $srID < 7; $srID++)
		{
			$dateID = strtotime($dateID_1.'+'.$srID.'Days');
			$file .= '<th '.$styleID.'>'.date('D',$dateID).'<br />'.date('d-M-Y',$dateID).'</th>';
		}
		$file .= '</tr></thead>';
		$Qry = $index->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$FL_Array[0]['ID']." AND sdateID = '".$dateID_1."' Order By recID ASC ");
		$Qry->execute();
		$index->result = $Qry->fetchAll(PDO::FETCH_ASSOC);
		
		$counterID = 1;
		if(is_array($index->result) && (count($index->result) > 0))
		{
			$scodeID = '';
			foreach($index->result as $rows)
			{
				$EMPL = $index->select('employee',array("*"), " WHERE ID = ".$rows['empID']." ");
				
				$SH_1 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_1']." ");
				$SH_2 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_2']." ");
				$SH_3 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_3']." ");
				$SH_4 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_4']." ");
				$SH_5 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_5']." ");
				$SH_6 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_6']." ");
				$SH_7 = $index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_7']." ");
				
				$file .= '<tr>';
				$file .= '<td align="center">'.$counterID++.'</td>';
				$file .= '<td align="center">'.$EMPL[0]['code'].'</td>';
				$file .= '<td>'.strtoupper($EMPL[0]['fname'].' '.$EMPL[0]['lname']).'</td>';
				
				$file .= '<td align="center">'.$SH_1[0]['code'].'</td>';
				$file .= '<td align="center">'.$SH_2[0]['code'].'</td>';
				$file .= '<td align="center">'.$SH_3[0]['code'].'</td>';
				$file .= '<td align="center">'.$SH_4[0]['code'].'</td>';
				$file .= '<td align="center">'.$SH_5[0]['code'].'</td>';
				$file .= '<td align="center">'.$SH_6[0]['code'].'</td>';
				$file .= '<td align="center">'.$SH_7[0]['code'].'</td>'; 
				$file .= '</tr>';
			}
		}
		
		$file .= '</table>';
		
		$dompdf->loadHtml($file);
		
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		
		// Render the HTML as PDF
		$dompdf->render();
		
		// Output the generated PDF to Browser
		//$dompdf->stream();
		
		// Output the generated PDF (1 = download and 0 = preview)
		$dompdf->stream('Weekly_Roster_Sheets_'.date('d-M-Y',strtotime($dateID_1)),array("Attachment"=>1));	
	}
	else
	{
			$result[] = array('Status'=>0,'Message'=>'Error');	
			echo  json_encode($result);	
	}	  
	
?>