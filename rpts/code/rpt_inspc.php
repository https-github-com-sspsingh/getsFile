<?PHP
class Reports extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
            parent::__construct();		

            $this->basefile  =  basename($_SERVER['PHP_SELF']);		
            $this->tableName =  '';
	}
	
	public function BuilderReport($filters)
    {
        echo $this->Generate_BuilderReport($filters,8);
    }
	
	public function ReportDisplay($filters)
	{
		$_SENDER = $filters;

		$return  = "";
		$return .= $filters['fltID_1'] > 0    ? " AND inspc.insrypeID = ".$filters['fltID_1'] : "";		
		$return .= $filters['fltID_2'] > 0    ? " AND inspc.fineID = ".$filters['fltID_2'] : "";
		$return .= $filters['fltID_3'] > 0    ? " AND inspc.inspectedby = ".$filters['fltID_3'] : "";
		$return .= $filters['filterID'] <> '' ? " AND inspc.companyID In (".$filters['filterID'].") " : " AND inspc.companyID IN (".$_SESSION[$this->website]['compID'].") ";

		if($filters['rtpyeID'] == 1)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID_1');}}
		if($filters['rtpyeID'] == 2)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID_1');}}
		if($filters['rtpyeID'] == 3)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID_1');}}

		$dateSTR = "";
		$dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

		if($filters['rtpyeID'] == 1)         {echo $this->reportPartID_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->reportPartID_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->reportPartID_3($return,$dateSTR,$_SENDER);}
	} 
	
	public function reportPartID_1($filters,$dateSTR,$_SENDER)
	{
		$file = '';

		$SQL = "SELECT * FROM inspc WHERE ID > 0 ".$filters." Order By ID ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Inspection Register Report : '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';
			
			$file .= '<thead><tr>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Depot</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Staff ID</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Staff Name</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Report No</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Report Date</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Date Inspected</strong></div></th>';
			
				$file .= '<th width="300" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Inspection Result';
					$file .= '<select class="form-control" id="inspc_fltID_1">';
					$file .= '<option value="0" selected="selected">-- Select --</option>';
					$file .= $this->GET_Masters($_SENDER['fltID_1'],'27');
					$file .= '</select>';		
				$file .= '</strong></div></th>';
			
				$file .= '<th width="300" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Fine';
					$file .= '<select class="form-control" id="inspc_fltID_2">';
					$file .= '<option value="0" selected="selected">-- Select --</option>';
					$file .= $this->GET_Masters($_SENDER['fltID_2'],'61');
					$file .= '</select>';		
				$file .= '</strong></div></th>';
			
				$file .= '<th width="300" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Inspected By';
					$file .= '<select class="form-control" id="inspc_fltID_3">';
					$file .= '<option value="0" selected="selected">-- Select --</option>';
					$file .= $this->GET_Masters($_SENDER['fltID_3'],'66');
					$file .= '</select>';		
				$file .= '</strong></div></th>';
			
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Service No</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Service Info</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Service Timing Point</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Departure Time</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Timing Point Time</strong></div></th>';
				$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="center"><strong>Actual Time</strong></div></th>';
			$file .= '</tr></thead>';
			
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_2)
				{
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					$SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
					$STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
					$CTN_Array  = $rows_2['contractorID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractorID']." ") : '';
									
					$NP_Array  = $rows_2['inspectedby'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['inspectedby']." ") : '';
					$FN_Array  = $rows_2['fineID'] > 0       ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['fineID']." ") : '';
					$IN_Array  = $rows_2['insrypeID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
					$EM_Array  = $rows_2['empID'] > 0        ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
					
					$file .= '<tr>';
						$file .= '<td>'.$CP_Array[0]['title'].'</td>';
						$file .= '<td align="center">'.$rows_2['ecodeID'].'</td>';
						$file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
						$file .= '<td class="d-set">'.$rows_2['rptno'].'</td>';
						$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
						$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
						$file .= '<td>'.$IN_Array[0]['title'].'</td>';
						$file .= '<td>'.$FN_Array[0]['title'].'</td>';
						$file .= '<td>'.$NP_Array[0]['title'].'</td>';
						$file .= '<td>'.$SRN_Array[0]['codeID'].'</td>';
						$file .= '<td>'.$rows_2['serviceinfID'].'</td>';
						$file .= '<td>'.$STP_Array[0]['fileID_1'].'</td>';
									
						$file .= '<td>'.$rows_2['timeID_1'].'</td>';
						$file .= '<td>'.$rows_2['timeID_2'].'</td>';
						$file .= '<td>'.$rows_2['timeID_3'].'</td>';
					$file .= '</tr>';
				}
			}
			$file .= '</table>';			
		} 

		return $file;
	}
	
	
	public function reportPartID_2($filters,$dateSTR,$_SENDER)
	{
            $file = '';
			
            $SQL = "SELECT inspectedby FROM inspc WHERE ID > 0 AND inspectedby > 0 ".$filters." Group By inspectedby Order By inspectedby DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            { 
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
                <div align="center"><strong>Inspected By - Inspection Report'.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>'; 
				$file .= '<th><div align="center"><strong>Depot</strong></div></td>';
                $file .= '<th><div align="center"><strong>Report No</strong></div></td>';
                $file .= '<th><div align="center"><strong>Report Date</strong></div></td>';
                $file .= '<th><div align="center"><strong>Inspection Result</strong></div></td>'; 
                $file .= '<th><div align="center"><strong>Contract</strong></div></td>';
                $file .= '<th><div align="center"><strong>Service No</strong></div></td>';
                $file .= '<th><div align="center"><strong>Service Info</strong></div></td>';
                $file .= '<th><div align="center"><strong>Service Time Point</strong></div></td>';
                $file .= '<th><div align="center"><strong>Bus No</strong></div></td>';
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                        foreach($this->rows_1 as $rows_1)
                        {
                            $INSP_Array  = $rows_1['inspectedby'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['inspectedby']." ") : '';
							
                            $file .= '<tr>';
                                    $file .= '<td colspan="17" style="padding-left:35px;"><b>Inspected By : '.$INSP_Array[0]['title'].'</b></td>';
                            $file .= '</tr>';

                            if($rows_1['inspectedby'] > 0)
                            {
                                    $SQL_2 = "SELECT * FROM inspc WHERE ID > 0 AND inspectedby = ".$rows_1['inspectedby']." ".$filters." Order By ID ASC ";
                                    $Qry_2 = $this->DB->prepare($SQL_2);
                                    $Qry_2->execute();
                                    $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                                    if(is_array($this->rows_2) && count($this->rows_2) > 0)
                                    {
                                    $srID = 1;
                                    foreach($this->rows_2 as $rows_2)
                                    {
										$COMP_Array = $rows_2['companyID'] > 0 ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                        $INS_Array  = $rows_2['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
                                        $CNT_Array  = $rows_2['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractID']." ") : '';
                                        $SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
                                        $STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
                                        $INV_Array  = $rows_2['invstID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invstID']." ") : '';

                                        $file .= '<tr>';
                                        
										$file .= '<td class="d-set">'.$COMP_Array[0]['title'].'</td>'; 
                                        $file .= '<td align="center">'.$rows_2['rptno'].'</td>';
                                        $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                                        $file .= '<td class="d-set">'.$INS_Array[0]['title'].'</td>'; 
                                        $file .= '<td class="d-set">'.$CNT_Array[0]['title'].'</td>';
                                        $file .= '<td class="d-set">'.$SRN_Array[0]['codeID'].'</td>';
                                        $file .= '<td class="d-set">'.$rows_2['serviceinfID'].'</td>';
                                        $file .= '<td class="d-set">'.$STP_Array[0]['fileID_1'].'</td>';
                                        $file .= '<td align="center">'.$rows_2['busID'].'</td>';
                                        $file .= '</tr>';
                                    }
                                    }
                            }
                        } 
                }
                $file .= '</table>';			
            } 

            return $file;
	}
	
	public function reportPartID_3($filters,$dateSTR,$_SENDER)
	{
		$file = '';
		
		$SQL = "SELECT master.title, inspc.* FROM inspc Inner Join master On master.ID = inspc.fineID WHERE inspc.ID > 0 AND inspc.fineID > 0 AND inspc.fineID <> 299 ".$filters." Order By inspc.companyID, master.title, inspc.dateID_1 ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="12" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Fine - Inspection Report'.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>'; 
			$file .= '<th><div align="center"><strong>Depot</strong></div></td>';
			$file .= '<th><div align="center"><strong>Report No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Report Date</strong></div></td>';
			$file .= '<th><div align="center"><strong>Inspected Date</strong></div></td>';
			$file .= '<th><div align="center"><strong>Inspection Result</strong></div></td>';
			$file .= '<th><div align="center"><strong>Contract</strong></div></td>';
			$file .= '<th><div align="center"><strong>Service No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Service Info</strong></div></td>';
			$file .= '<th><div align="center"><strong>Service Time Point</strong></div></td>';
			$file .= '<th><div align="center"><strong>Bus No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Fine</strong></div></td>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$fine_tot = 0;
				foreach($this->rows_1 as $rows_2)
				{  
					
					$INS_Array  = $rows_2['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
					$COMP_Array = $rows_2['companyID'] > 0 ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					$CNT_Array  = $rows_2['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractID']." ") : '';
					$SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
					$STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
					$INV_Array  = $rows_2['invstID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invstID']." ") : '';
					
					$file .= '<tr>';
						$file .= '<td class="d-set">'.$COMP_Array[0]['title'].'</td>'; 
						$file .= '<td align="center">'.$rows_2['rptno'].'</td>';
						$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
						$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
						$file .= '<td class="d-set">'.$INS_Array[0]['title'].'</td>'; 
						$file .= '<td class="d-set">'.$CNT_Array[0]['title'].'</td>';
						$file .= '<td class="d-set">'.$SRN_Array[0]['codeID'].'</td>';
						$file .= '<td class="d-set">'.$rows_2['serviceinfID'].'</td>';
						$file .= '<td class="d-set">'.$STP_Array[0]['fileID_1'].'</td>';
						$file .= '<td align="center">'.$rows_2['busID'].'</td>';
						$file .= '<td align="center">'.$rows_2['title'].'</td>';
					$file .= '</tr>';
					
					$fine_tot += (float)$rows_2['title']; 
				} 
				
				$file .= '<tr>';
				$file .= '<td colspan="10" style="background:#367FA9; color:white;" align="right"><b> Grand Total : </b></td>';   
				$file .= '<td style="background:#367FA9; color:white;" align="center"><b> '.$fine_tot.'</b> </td>';

				$file .= '</tr>';
			}
			$file .= '</table>';			
		} 

		return $file;
	} 
}
?>