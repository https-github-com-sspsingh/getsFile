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
        echo $this->Generate_BuilderReport($filters,7);
    }
	
	public function ReportDisplay($filters)
	{
            $_SENDER = $filters;

            $return  = "";
            $return .= $filters['fltID_1'] > 0    ? " AND inftypeID = ".$filters['fltID_1'] : "";		
            $return .= $filters['fltID_2'] > 0    ? " AND wrtypeID = ".$filters['fltID_2'] : "";
            $return .= $filters['filterID'] <> '' ? " AND companyID In (".$filters['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
            
            if($filters['rtpyeID'] == 1)    {if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}
			if($filters['rtpyeID'] == 2)    {if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}
			if($filters['rtpyeID'] == 3)    {if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}
			

            $dateSTR = "";
            $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

            if($filters['rtpyeID'] == 1)         {echo $this->reportPartID_1($return,$dateSTR,$_SENDER);}
			if($filters['rtpyeID'] == 2)         {echo $this->reportPartID_2($return,$dateSTR,$_SENDER);}
			if($filters['rtpyeID'] == 3)         {echo $this->reportPartID_3($return,$dateSTR,$_SENDER);}
	} 
	
	 public function reportPartID_1($filters,$dateSTR,$_SENDER)
	 {
            $file = '';

            $SQL = "SELECT * FROM infrgs WHERE ID > 0 ".$filters." Order By ID ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {  
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Infringement Details Report : '.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Infringement No</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Date</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Time</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Location</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Demerit Points Lost</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Date Received</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Date Sent</strong></div></th>';
                    
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Infringement Type';
                        $file .= '<select class="form-control" id="infrgs_fltID_1">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= $this->GET_Masters($_SENDER['fltID_1'],'22');
                        $file .= '</select>';		
                    $file .= '</strong></div></th>';
                    
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Managers Comments</strong></div></th>';
                    
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Warning Type';
                    $file .= '<select class="form-control" id="infrgs_fltID_2">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= $this->GET_Masters($_SENDER['fltID_2'],'23');
                        $file .= '</select>';		
                    $file .= '</strong></div></th>';                    
                $file .= '</tr></thead>';
                
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;
                    foreach($this->rows_1 as $rows_2)
                    {
                        $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                        $WP_Array  = $rows_2['wrtypeID'] > 0   ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['wrtypeID']." ")   : '';
                        $IN_Array  = $rows_2['inftypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['inftypeID']." ")  : '';
                        $EM_Array  = $rows_2['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ")  : '';
                        
                        $file .= '<tr>';
                            $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                            $file .= '<td align="center">'.$rows_2['stcodeID'].'</td>';
                            $file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                            $file .= '<td class="d-set">'.$rows_2['refno'].'</td>';
                            $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                            $file .= '<td>'.$rows_2['timeID'].'</td>';
                            $file .= '<td>'.$rows_2['description_1'].'</td>';
                            $file .= '<td>'.($rows_2['dplostID'] <> '' ? $rows_2['dplostID'] : 0).'</td>';
                            $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_3']).'</td>';
                            $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_4']).'</td>';
                            $file .= '<td>'.$IN_Array[0]['title'].'</td>';
                            $file .= '<td>'.$rows_2['mcomments'].'</td>';
                            $file .= '<td>'.$WP_Array[0]['title'].'</td>';
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
		
		$SQL = "SELECT inftypeID FROM infrgs WHERE ID > 0 AND inftypeID <> '' ".$filters." Group By inftypeID Order By inftypeID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Infringement Type - Infringement Report '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Infringement No.</strong></div></th>';
			$file .= '<th><div align="center"><strong>Vehicle Rego</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date Occurred</strong></div></th>';
			$file .= '<th><div align="center"><strong>Demerit Points</strong></div></th>';
			$file .= '<th><div align="center"><strong>Bus No.</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver ID</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Issue Date </strong></div></th>';
			$file .= '<th><div align="center"><strong>Compliance Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date Received</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date Sent</strong></div></th>';
			$file .= '<th><div align="center"><strong>Infringement Type</strong></div></th>';
			$file .= '<th><div align="center"><strong>Location Of Infringement</strong></div></th>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $rows_1)
				{
					$INF_Array  = $rows_1['inftypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['inftypeID']." ") : '';

					$file .= '<tr><td colspan="16" style="padding-left:35px;"><b>Type: '.$INF_Array[0]['title'].'</b></td></tr>';

					if($rows_1['inftypeID'] <> '')
					{
						$SQL_2 = "SELECT * FROM infrgs WHERE ID > 0 AND inftypeID = '".$rows_1['inftypeID']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
							    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

								$file .= '<tr>';
									$file .= '<td>'.$CP_Array[0]['title'].'</td>';
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td align="center">'.$rows_2['vehicle'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									$file .= '<td >'.$rows_2['dplostID'].'</td>';
									$file .= '<td >'.$rows_2['busID'].'</td>';
									$file .= '<td >'.$rows_2['stcodeID'].'</td>';
									$file .= '<td >'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_2']).'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_3']).'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_4']).'</td>';
									$file .= '<td>'.$INF_Array[0]['title'].'</td>';
									$file .= '<td>'.$rows_2['description_1'].'</td>';
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
		
		$SQL = "SELECT dplostID FROM infrgs WHERE ID > 0 AND dplostID <> '' ".$filters." Group By dplostID Order By dplostID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Demerit Points - Infringement Report</ Wise'.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Infringement No.</strong></div></th>';
			$file .= '<th><div align="center"><strong>Vehicle Rego</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date Occurred</strong></div></th>';
			$file .= '<th><div align="center"><strong>Bus No.</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver ID</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Issue Date </strong></div></th>';
			$file .= '<th><div align="center"><strong>Compliance Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date Received</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date Sent</strong></div></th>';
			$file .= '<th><div align="center"><strong>Infringement Type</strong></div></th>';
			$file .= '<th><div align="center"><strong>Location Of Infringement</strong></div></th>'; 
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $rows_1)
				{
					$file .= '<tr>';
							$file .= '<td colspan="15" style="padding-left:35px;"><b>Demerit Points : '.($rows_1['dplostID']).'</b></td>';
					$file .= '</tr>';

					if($rows_1['dplostID'] <> '')
					{
						$SQL_2 = "SELECT * FROM infrgs WHERE ID > 0 AND dplostID = '".$rows_1['dplostID']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
							   $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';
							   $INF_Array  = $rows_2['inftypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['inftypeID']." ") : '';
							   
								$file .= '<tr>';
									$file .= '<td>'.$CP_Array[0]['title'].'</td>';
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td align="center">'.$rows_2['vehicle'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									$file .= '<td >'.$rows_2['busID'].'</td>';
									$file .= '<td>'.$rows_2['stcodeID'].'</td>';
									$file .= '<td >'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_2']).'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_3']).'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID_4']).'</td>';
									$file .= '<td>'.$INF_Array[0]['title'].'</td>';
									$file .= '<td>'.$rows_2['description_1'].'</td>';
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