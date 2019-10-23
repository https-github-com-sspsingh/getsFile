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
		echo $this->Generate_BuilderReport($filters,5);
    }
	
	public function ReportDisplay($filters)
	{
		if($filters['rtpyeID'] == 11) 
		{
			
		}
		else if($filters['rtpyeID'] >= 1 && $filters['rtpyeID'] <= 10) 
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
        

	public function reportPartID_2($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL =   "SELECT dateID FROM incident_regis WHERE ID > 0 AND sincID = 2 AND dateID <> '' ".$filters." Group By dateID Order By dateID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Non Security Incidents Report - Date '.$prID.'</strong></div></th>'; 
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
					if($rows_1['dateID'] <> '')
					{
						$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0 AND sincID = 2  AND dateID = '".$rows_1['dateID']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							$srsID = 1;
							foreach($this->rows_2 as $rows_2)
							{
								$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$IN_Array  = $rows_2['inctypeID'] > 0   ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
								$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

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

	public function reportPartID_3($request)
	{
            $file = '';
            $filters = "";
            if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
			$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
			
            $SQL =   "SELECT dateID FROM incident_regis WHERE ID > 0 AND sincID = 1 AND dateID <> '' ".$filters." Group By dateID Order By dateID DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
                <div align="center"><strong>Security Incidents Report - Date '.$prID.'</strong></div></th>'; 
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
                        if($rows_1['dateID'] <> '')
                        {
                            $SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0 AND sincID = 1  AND dateID = '".$rows_1['dateID']."' ".$filters." Order By ID ASC ";
                            $Qry_2 = $this->DB->prepare($SQL_2);
                            $Qry_2->execute();
                            $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                            if(is_array($this->rows_2) && count($this->rows_2) > 0)
                            {
                                $srsID = 1;
                                foreach($this->rows_2 as $rows_2)
                                {
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                    $IN_Array  = $rows_2['inctypeID'] > 0   ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
                                    $EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

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

	public function reportPartID_4($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL =   "SELECT suburb FROM incident_regis WHERE ID > 0  AND suburb > 0 ".$filters." Group By suburb Order By suburb DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incidents Report - By Suburb '.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center">Security Incidents</strong></div></td>';
			$file .= '<th><div align="center">Date</strong></div></td>';
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
					$SUB_Array  = $rows_1['suburb'] > 0   ?  $this->select('suburbs',array("*"), " WHERE ID = ".$rows_1['suburb']." ") : '';
					
					if($rows_1['suburb'] > 0 && ($SUB_Array[0]['title'] <> ''))
					{
						$file .= '<tr>';
							$file .= '<td colspan="11" style="padding-left:35px;"><b>Suburb : '.$SUB_Array[0]['title'].'</b></td>';
						$file .= '</tr>';
					
					
						$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND suburb = ".$rows_1['suburb']." ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							$srsID = 1;
							foreach($this->rows_2 as $rows_2)
							{
								$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$IN_Array  = $rows_2['inctypeID'] > 0   ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
								$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

								$file .= '<tr>';
								$file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
								$file .= '<td align="center">'.($rows_2['sincID'] == 1 ? 'Yes' :($rows_2['sincID'] == 2 ? 'NO' : '')).'</td>';
								$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
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

	public function reportPartID_5($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL =   "SELECT inctypeID FROM incident_regis WHERE ID > 0 AND inctypeID > 0 ".$filters." Group By inctypeID Order By inctypeID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - Incident Type'.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center">Ref No</strong></div></td>';
			$file .= '<th><div align="center">Date</strong></div></td>';
			$file .= '<th><div align="center"><strong>Driver</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Location</strong></div></td>';
			$file .= '<th><div align="center"><strong>Reported By</strong></div></td>';                
			$file .= '<th width="350"><div align="center"><strong>Description</strong></div></td>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
						$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';
						
						$file .= '<tr>';
							$file .= '<td colspan="11" style="padding-left:35px;"><b>Incident Category : '.$IN_Array[0]['title'].'</b></td>';
						$file .= '</tr>';

						if($rows_1['inctypeID'] > 0)
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND inctypeID = ".$rows_1['inctypeID']." ".$filters." Order By ID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								$srsID = 1;
								foreach($this->rows_2 as $rows_2)
								{
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
													$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
													

													  $file .= '<tr>';
													 $file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
													 $file .= '<td align="center">'.$rows_2['refno'].'</td>';
													 $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
													 $file .= '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
													 $file .= '<td>'.$rows_2['location'].'</td>';
													 $file .= '<td>'.$rows_2['reportby'].'</td>';
													 $file .= '<td>'.(($rows_2['description'])).'</td>';
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

	public function reportPartID_6($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL =   "SELECT driverID FROM incident_regis WHERE ID > 0 AND driverID > 0 ".$filters." Group By driverID Order By driverID ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - Driver '.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center">Ref No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Date</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Location</strong></div></td>';
			$file .= '<th><div align="center"><strong>Reported By</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Type</strong></div></td>';
			$file .= '<th width="350"><div align="center"><strong>Description</strong></div></td>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
						$EM_Array  = $rows_1['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_1['driverID']." ") : '';
						
							$file .= '<tr>';
									$file .= '<td colspan="11" style="padding-left:35px;"><b>Driver : '.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</b></td>';
							$file .= '</tr>';

						if($rows_1['driverID'] > 0)
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND driverID = ".$rows_1['driverID']." ".$filters." Order By dateID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								$srsID = 1;
								foreach($this->rows_2 as $rows_2)
								{
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$IN_Array  = $rows_2['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
													  $file .= '<tr>';
													 $file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
													 $file .= '<td align="center">'.$rows_2['refno'].'</td>';
													 $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
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
	
	public function reportPartID_7($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL =   "SELECT inctypeID FROM incident_regis WHERE ID > 0 AND inctypeID <> '' ".$filters." Group By inctypeID Order By inctypeID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - Incident Type'.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center">Ref No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Date</strong></div></td>';
			$file .= '<th><div align="center"><strong>Driver</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Location</strong></div></td>';
			$file .= '<th><div align="center"><strong>Reported By</strong></div></td>';
			$file .= '<th width="350"><div align="center"><strong>Description</strong></div></td>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
							$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';

							$file .= '<tr>';
									$file .= '<td colspan="11" style="padding-left:35px;"><b>Incident Type: '.$IN_Array[0]['title'].'</b></td>';
							$file .= '</tr>';

							 if($rows_1['inctypeID'] <> '')
				   {
									$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND inctypeID = '".$rows_1['inctypeID']."' ".$filters." Order By ID ASC ";
									//echo  $SQL_2;
									$Qry_2 = $this->DB->prepare($SQL_2);
									$Qry_2->execute();
									$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
									if(is_array($this->rows_2) && count($this->rows_2) > 0)
									{
											$srsID = 1;
											foreach($this->rows_2 as $rows_2)
											{
												$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
													$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

													  $file .= '<tr>';
													 $file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
													 $file .= '<td align="center">'.$rows_2['refno'].'</td>';
													 $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
													 $file .= '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
													 $file .= '<td>'.$rows_2['location'].'</td>';
													 $file .= '<td>'.$rows_2['reportby'].'</td>';
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
        
	public function reportPartID_8($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL = "SELECT busID FROM incident_regis WHERE ID > 0 AND busID <> '' ".$filters." Group By busID Order By busID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - By Bus No '.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center">Ref No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Date</strong></div></td>';
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
						$file .= '<tr>';
							$file .= '<td colspan="11" style="padding-left:35px;"><b> Bus No : '.$rows_1['busID'].'</b></td>';
						$file .= '</tr>';

						if($rows_1['busID'] <> '')
						{
						$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND busID = '".$rows_1['busID']."' ".$filters." Order By ID ASC ";
									//echo  $SQL_2;
									$Qry_2 = $this->DB->prepare($SQL_2);
									$Qry_2->execute();
									$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
									if(is_array($this->rows_2) && count($this->rows_2) > 0)
									{
											$srsID = 1;
											foreach($this->rows_2 as $rows_2)
											{
												$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
													$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
													$IN_Array  = $rows_2['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : ''; 

													  $file .= '<tr>';
													 $file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
													 $file .= '<td align="center">'.$rows_2['refno'].'</td>';
													 $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
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

	public function reportPartID_9($request)
	{
		$file = '';
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		
		$SQL = "SELECT * FROM incident_regis WHERE ID > 0  AND plrefID = 1 ".$filters." Order By ID ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - Date '.$prID.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';                
			$file .= '<th><div align="center"><strong>Security Incident</strong></div></td>';
			$file .= '<th><div align="center">Ref No</strong></div></td>';
			$file .= '<th><div align="center"><strong>Driver</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Location</strong></div></td>';
			$file .= '<th><div align="center"><strong>Reported By</strong></div></td>';
			$file .= '<th><div align="center"><strong>Incident Type</strong></div></td>';
			$file .= '<th width="350"><div align="center"><strong>Description</strong></div></td>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
				$srsID = 1;
					foreach($this->rows_1 as $rows_1)
					{ 
						$arrCM  = $rows_1['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
						$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';
						$EM_Array  = $rows_1['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_1['driverID']." ") : '';

						$file .= '<tr>';
						$file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
						$file .= '<td align="center">'.($rows_1['sincID'] == 1 ? 'YES' : 'NO').'</td>';
						$file .= '<td align="center">'.$rows_1['refno'].'</td>';
						$file .= '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
						$file .= '<td>'.$rows_1['location'].'</td>';
						$file .= '<td>'.$rows_1['reportby'].'</td>';
						$file .= '<td>'.$IN_Array[0]['title'].'</td>';
						$file .= '<td>'.$rows_2['description'].'</td>';
						$file .= '</tr>';
					}
			}
			$file .= '</table>';			
		} 

		return $file;
	} 
        
	public function reportPartID_10($request)
	{
            $file = '';
            $filters = "";
            if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
			$filters .= $request['filterID'] <> '' ? " AND companyID In (".$request['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
			
            $SQL =   "SELECT inctypeID FROM incident_regis WHERE ID > 0  AND inctypeID In (248,249) ".$filters." Group By inctypeID Order By inctypeID DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
				$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
                <div align="center"><strong>Incident Report - Passenger Injury '.$prID.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
                $file .= '<th><div align="center">Ref No</strong></div></td>';
                $file .= '<th><div align="center"><strong>Date</strong></div></td>';
                $file .= '<th><div align="center"><strong>Driver</strong></div></td>';
                $file .= '<th><div align="center"><strong>Incident Location</strong></div></td>';
                $file .= '<th><div align="center"><strong>Reported By</strong></div></td>';
                $file .= '<th width="350"><div align="center"><strong>Description</strong></div></td>';
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                        foreach($this->rows_1 as $rows_1)
                        {
                                $IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';

                                $file .= '<tr>';
                                        $file .= '<td colspan="11" style="padding-left:35px;"><b>Incident Type: '.$IN_Array[0]['title'].'</b></td>';
                                $file .= '</tr>';

                                 if($rows_1['inctypeID'] <> '')
                       {
                                        $SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND inctypeID = '".$rows_1['inctypeID']."' ".$filters." Order By ID ASC ";
                                        //echo  $SQL_2;
                                        $Qry_2 = $this->DB->prepare($SQL_2);
                                        $Qry_2->execute();
                                        $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                                        if(is_array($this->rows_2) && count($this->rows_2) > 0)
                                        {
                                                $srsID = 1;
                                                foreach($this->rows_2 as $rows_2)
                                                {
													$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                                        $EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

                                                          $file .= '<tr>';
                                                         $file .= '<td  align="center">'.$arrCM[0]['title'].'</td>';
                                                         $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                                                         $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                                                         $file .= '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                                                         $file .= '<td>'.$rows_2['location'].'</td>';
                                                         $file .= '<td>'.$rows_2['reportby'].'</td>';
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