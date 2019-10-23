<?PHP
class Reports extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	  =	basename($_SERVER['PHP_SELF']);		
		$this->tableName     =	'';
	}
	
	public function BuilderReport($filters)
    {
		echo $this->Generate_BuilderReport($filters,12);
    }
	
	public function ReportDisplay($filters)
	{
		if($filters['rtpyeID'] == 12) 
		{
			
		}
		else if($filters['rtpyeID'] >= 1 && $filters['rtpyeID'] <= 1) 
		{
			$pageID = 'reportPartID_'.$filters['rtpyeID'];
			
			echo $this->$pageID($filters);
		}		
	}

	public function reportPartID_1($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}			
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL =   "SELECT dateID FROM incident_regis WHERE ID > 0 AND dateID <> '' ".$filters." Group By dateID Order By dateID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>All Incident Report '.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center">Ref No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Driver</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Location</strong></div></td>';
			$file .= '<th><div align="center"><strong>Reported By</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Type</strong></div></td>';
			$file .= '<th width="350"><div align="center"><strong>Description</strong></div></td>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
						/*$file .= '<tr>';
							$file .= '<td colspan="11" style="padding-left:35px;"><b>Incident Date : '.date('d-m-Y',strtotime($rows_1['dateID'])).'</b></td>';
						$file .= '</tr>';*/

						if($rows_1['dateID'] <> '')
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND dateID = '".$rows_1['dateID']."' ".$filters." Order By ID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								$srsID = 1;
								foreach($this->rows_2 as $rows_2)
								{
									$IN_Array  = $rows_2['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
									$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
									
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';

													  $file .= '<tr>';
													 $file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
													 $file .= '<td align="center">'.$rows_2['refno'].'</td>';
													 $file .= '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
													 $file .= '<td>'.$rows_2['location'].'</td>';
													 $file .= '<td>'.$rows_2['reportby'].'</td>';
													 $file .= '<td>'.$IN_Array[0]['title'].'</td>';
													 $file .= '<td>'.$rows_2['description'].'</td>';
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
}
?>