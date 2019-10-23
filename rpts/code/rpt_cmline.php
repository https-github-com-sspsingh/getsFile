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
        echo $this->Generate_BuilderReport($filters,4);
    }
	
    public function ReportDisplay($filters)
    {
        $_SENDER = $filters;	//echo '<pre>'; echo print_r($filters);

        $return  = "";
        $return .= $filters['fltID_1'] > 0    ? " AND complaint.accID = ".$filters['fltID_1'] : "";		
        $return .= $filters['fltID_2'] > 0    ? " AND complaint.substanID = ".$filters['fltID_2'] : "";
        $return .= $filters['filterID'] <> '' ? " AND complaint.companyID In (".$filters['filterID'].") " : " AND complaint.companyID IN (".$_SESSION[$this->website]['compID'].") ";

        if($filters['rtpyeID'] == 1)								{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'complaint.serDT');}}
		if($filters['rtpyeID'] >= 2 && $filters['rtpyeID'] <= 9)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'complaint.serDT');}}
        
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)         {echo $this->reportPartID_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->reportPartID_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->reportPartID_3($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 4)         {echo $this->reportPartID_4($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 5)         {echo $this->reportPartID_5($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 6)         {echo $this->reportPartID_6($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 7)         {echo $this->reportPartID_7($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 8)         {echo $this->reportPartID_8($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 9)         {echo $this->reportPartID_9($return,$dateSTR,$_SENDER);}
    } 
        
    public function reportPartID_1($filters,$dateSTR,$_SENDER)
    {
        $file = '';
        
        $SQL = "SELECT * FROM complaint WHERE ID > 0 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {

                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="13" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Customer Feedback Register Report : '.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Serco Ref</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Serco Date</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Incident Time</strong></div></th>';
                
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Customer Feedback Type';
                        $file .= '<select class="form-control" id="cmline_fltID_1">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= $this->GET_Masters($_SENDER['fltID_1'],'17');
                        $file .= '</select>';		
                    $file .= '</strong></div></th>';
                
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Substantiated';
                        $file .= '<select class="form-control" id="cmline_fltID_2">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= '<option value="1" '.($_SENDER['fltID_2'] == '1' ? 'selected="selected"' : '').'>Yes</option>';
                        $file .= '<option value="2" '.($_SENDER['fltID_2'] == '2' ? 'selected="selected"' : '').'>No</option>';
                        $file .= '</select>';
                    $file .= '</strong></div></th>';
                
                    
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Fault/Not at Fault</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Route</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Location</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Customer Name</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Reason</strong></div></th>';
                $file .= '</tr></thead>';
                
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    foreach($this->rows_1 as $rows_2)
                    { 
                        $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                        $RS_Array  = $rows_2['creasonID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
                        $TY_Array  = $rows_2['accID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['accID']." ") : '';
                        $EM_Array  = $rows_2['driverID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
                        $DS_Array  = $EM_Array[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';

                        $file .= '<tr>';
                        $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                        $file .= '<td align="center">'.$rows_2['dcodeID'].'</td>';
                        $file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                        $file .= '<td class="d-set">'.$rows_2['refno'].'</td>';
                        $file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>';
                        $file .= '<td>'.$rows_2['timeID'].'</td>';
                        $file .= '<td>'.$TY_Array[0]['title'].'</td>';
                        $file .= '<td class="d-set"align="center">'.($rows_2['substanID'] == 1 ? 'Yes'   
                        :($rows_2['substanID'] == 2 ? 'No' : '')).'</td>';
                        $file .= '<td>'.$rows_2['faultID'].'</td>';
                        $file .= '<td>'.$rows_2['routeID'].'</td>';
                        $file .= '<td>'.$rows_2['location'].'</td>';
                        $file .= '<td>'.$rows_2['cmp_name'].'</td>';
                        $file .= '<td>'.$RS_Array[0]['title'].'</td>';
                        $file .= '</tr>';
                    }
                }
                $file .= '</table>';			
        } 

        echo  $file;
   }
	
	public function reportPartID_2($filters,$dateSTR,$_SENDER)
	{       
		$file = '';
		$SQL =  "SELECT accID FROM complaint WHERE ID > 0 AND accID <> '' ".$filters." Group By accID Order By accID DESC "; 
		
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>All Customer Feedback Type - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';
			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>ID</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Reason</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Outcome</strong></div></th>';
			$file .= '<th><div align="center"><strong>Accountability</strong></div></th>';
			$file .= '<th><div align="center"><strong>Fault</strong></div></th>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
					foreach($this->rows_1 as $rows_1)
					{      
						$LT_Array  = $rows_1['accID'] > 0	? $this->select('master',array("*"), " WHERE ID = ".$rows_1['accID']." ") : '';
						
						$file .= '<tr><td colspan="17" style="padding-left:35px;"><b>Customer Feedback Type : '.$LT_Array[0]['title'].'</b></td></tr>';
						
						if($rows_1['accID'] > 0)
						{
							$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND accID = '".$rows_1['accID']."' ".$filters." Order By ID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								$srID = 1;
								foreach($this->rows_2 as $rows_2)
								{
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
									$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
									//	$IN_Array  = $rows_2['invID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invID']." ") : '';
									
									$file .= '<tr>';
									$file .= '<td>'.$CP_Array[0]['title'].'</td>';
									$file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									$file .= '<td>'.$DR_Array[0]['code'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td>'.$CM_Array[0]['title'].'</td>';
									$file .= '<td>'.$rows_2['description'].'</td>';
									$file .= '<td>'.$rows_2['outcome'].'</td>';                                        
									$file .= '<td>'.($rows_2['accID'] == 52 ? ($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')) : '').'</td>';
									$file .= '<td>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
									$file .= '</tr>';
								}
							}
						 }
					} 
			}
			$file .= '</table>';			
		} 

		echo $file;
	}
	
 
	public function reportPartID_3($filters,$dateSTR,$_SENDER)
	{
		$file = '';
		
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND accID <> '' AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>All Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>ID</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Reason</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Outcome</strong></div></th>';
			$file .= '<th><div align="center"><strong>Accountability</strong></div></th>';
			$file .= '<th><div align="center"><strong>Fault</strong></div></th>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								$file .= '<tr>';
									$file .= '<td align="center"><a target="blank" href="'.$this->home.'forms/cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									$file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									$file .= '<td>'.$DR_Array[0]['code'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td>'.$CM_Array[0]['title'].'</td>';
									$file .= '<td>'.$rows_2['description'].'</td>';
									$file .= '<td>'.$rows_2['outcome'].'</td>';                                        
									$file .= '<td>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
									$file .= '<td>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
								$file .= '</tr>';

						   }
						}
					}
				}
			}
			$file .= '</table>';			
		} 

		echo $file;
	}
        
	public function reportPartID_4($filters,$dateSTR,$_SENDER)
	{
		$file = '';
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND statusID = 1 AND accID <> '' AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{   
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Completed Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>ID</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Reason</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Outcome</strong></div></th>';
			$file .= '<th><div align="center"><strong>Accountability</strong></div></th>';
			$file .= '<th><div align="center"><strong>Fault</strong></div></th>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND statusID = 1 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								$file .= '<tr>';
									$file .= '<td align="center"><a target="blank" href="'.$this->home.'forms/cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									$file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									$file .= '<td>'.$DR_Array[0]['code'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td>'.$CM_Array[0]['title'].'</td>';
									$file .= '<td>'.$rows_2['description'].'</td>';
									$file .= '<td>'.$rows_2['outcome'].'</td>';                                        
									$file .= '<td>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
									$file .= '<td>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
								$file .= '</tr>';

						   }
						}
					}
				}
			}
			$file .= '</table>';			
		} 

		echo $file;
	}
	
	public function reportPartID_5($filters,$dateSTR,$_SENDER)
	{
		$file = '';
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND statusID <> 1 AND accID <> '' AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Pending Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>ID</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
			
			$file .= '<th><div align="center"><strong>Time</strong></div></th>';
			$file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Route No</strong></div></th>';
			
			$file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Reason</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
			$file .= '<th width="250"><div align="center"><strong>Outcome</strong></div></th>';
			$file .= '<th><div align="center"><strong>Accountability</strong></div></th>';
			$file .= '<th><div align="center"><strong>Fault</strong></div></th>';
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND statusID <> 1 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								$file .= '<tr>';
									$file .= '<td align="center"><a target="blank" href="'.$this->home.'forms/cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									$file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									$file .= '<td>'.$DR_Array[0]['code'].'</td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									
									$file .= '<td>'.$rows_2['timeID'].'</td>';
									$file .= '<td>'.$rows_2['busID'].'</td>';
									$file .= '<td>'.$rows_2['routeID'].'</td>';
									
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td>'.$CM_Array[0]['title'].'</td>';
									$file .= '<td>'.$rows_2['description'].'</td>';
									$file .= '<td>'.$rows_2['outcome'].'</td>';                                        
									$file .= '<td>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
									$file .= '<td>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
								$file .= '</tr>';

						   }
						}
					}
				}
			}
			$file .= '</table>';			
		} 

		echo $file;
	} 
	
	public function reportPartID_6($filters,$dateSTR,$_SENDER)
	{
		$file = '';
		
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND accID <> '' AND accID = 224 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Commendations - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			$file .= '</tr></thead>';

			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Incident Date</strong></div></th>';                
			$file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>ID</strong></div></th>';                
			$file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
			$file .= '<th><div align="center"><strong>Route No</strong></div></th>';                
			$file .= '<th><div align="center"><strong>Location</strong></div></th>';                
			$file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
			
			$file .= '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND accID = 224 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								$file .= '<tr>';
									$file .= '<td align="center"><a target="blank" href="'.$this->home.'forms/cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									$file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';                                         
									$file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									$file .= '<td>'.$DR_Array[0]['code'].'</td>';                                        
									$file .= '<td align="center">'.$rows_2['refno'].'</td>';
									$file .= '<td align="center">'.$rows_2['busID'].'</td>';
									$file .= '<td align="center">'.$rows_2['routeID'].'</td>';
									$file .= '<td>'.$rows_2['location'].'</td>';
									$file .= '<td>'.($rows_2['description']).'</td>';
								$file .= '</tr>';

						   }
						}
					}
				}
			}
			$file .= '</table>';			
		} 

		echo $file;
	}
        
	public function reportPartID_7($filters,$dateSTR,$_SENDER)
	{
            $file = '';
			
            $SQL =  "SELECT companyID, creasonID FROM complaint WHERE ID > 0 AND creasonID <> '' AND accID In(52,48,221,49,50,51,220,54) ".$filters." Group By companyID, creasonID Order By creasonID ASC ";            
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Customer Feedback Summary - Customer Feedback Report'.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
                $file .= '<th><div align="center"><strong>Customer Feedback Reasons / All Customer Feedback Types </strong></div></th>';
                $file .= '<th><div align="center"><strong>At Fault - Driver</strong></div></th>';                
                $file .= '<th><div align="center"><strong>At Fault - Engineering</strong></div></th>';
                $file .= '<th><div align="center"><strong>At Fault - Operations</strong></div></th>';                
                $file .= '<th><div align="center"><strong>Not Applicable</strong></div></th>';
                $file .= '<th><div align="center"><strong>Not At Fault</strong></div></th>';
                $file .= '<th><div align="center"><strong>G.Total</strong></div></th>';                   
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;  $returnID = '';
                    $fID_1; $fID_2; $fID_3; $fID_4; $fID_5; $fID_6;
                    foreach($this->rows_1 as $rows_1)
                    {
						$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                        $CM_Array  = $rows_1['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['creasonID']." ") : '';
                        
                        $returnID = $this->GETCountComplaints($filters,$rows_1['companyID'],$rows_1['creasonID']);
                        
                        //echo '<pre>'; echo print_r($returnID);
                        
                        $file .= '<tr>';
                            $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                            $file .= '<td>'.$CM_Array[0]['title'].'</td>'; 
                            $file .= '<td align="center">'.($returnID[0]['faultID1'] > 0 ? $returnID[0]['faultID1'] : '').'</td>';
                            $file .= '<td align="center">'.($returnID[0]['faultID2'] > 0 ? $returnID[0]['faultID2'] : '').'</td>';
                            $file .= '<td align="center">'.($returnID[0]['faultID3'] > 0 ? $returnID[0]['faultID3'] : '').'</td>';
                            $file .= '<td align="center">'.($returnID[0]['faultID4'] > 0 ? $returnID[0]['faultID4'] : '').'</td>';
                            $file .= '<td align="center">'.($returnID[0]['faultID5'] > 0 ? $returnID[0]['faultID5'] : '').'</td>';
                            $file .= '<td align="center">'.($returnID[0]['faultID1'] + $returnID[0]['faultID2'] + $returnID[0]['faultID3'] + $returnID[0]['faultID4'] + $returnID[0]['faultID5']).'</td>';
                            
                        $file .= '</tr>';
                        
                        $fID_1 += $returnID[0]['faultID1'];
                        $fID_2 += $returnID[0]['faultID2'];
                        $fID_3 += $returnID[0]['faultID3'];
                        $fID_4 += $returnID[0]['faultID4'];
                        $fID_5 += $returnID[0]['faultID5'];
                        $fID_6 += ($returnID[0]['faultID1'] + $returnID[0]['faultID2'] + $returnID[0]['faultID3'] + $returnID[0]['faultID4'] + $returnID[0]['faultID5']);
                    }
                    
                        $file .= '<tr>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" colspan="2" align="center">GTotal : </td>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_1.'</td>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_2.'</td>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_3.'</td>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_4.'</td>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_5.'</td>';
                            $file .= '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_6.'</td>';
                            
                        $file .= '</tr>';
                }
                $file .= '</table>';			
            } 

            echo $file;
	}
        

	public function reportPartID_8($filters,$dateSTR,$_SENDER)
	{
            $file = '';
			
            $SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND substanID=1 AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Substantiated Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
                $file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
                $file .= '<th><div align="center"><strong>ID</strong></div></th>';
                $file .= '<th><div align="center"><strong>Date</strong></div></th>';
                $file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
                $file .= '<th><div align="center"><strong>Reason</strong></div></th>';
                $file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
                $file .= '<th width="250"><div align="center"><strong>Outcome</strong></div></th>';
                $file .= '<th><div align="center"><strong>Accountability</strong></div></th>';
                $file .= '<th><div align="center"><strong>Fault</strong></div></th>';
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;
                    foreach($this->rows_1 as $rows_1)
                    {
                        if($rows_1['serDT'] <> '')
                        {
                            $SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND substanID = 1 AND accID = 52 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
                            $Qry_2 = $this->DB->prepare($SQL_2);
                            $Qry_2->execute();
                            $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                            if(is_array($this->rows_2) && count($this->rows_2) > 0)
                            {
                                foreach($this->rows_2 as $rows_2)
                                {
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                    $DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
                                    $CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
                                    
                                    $file .= '<tr>';
                                        $file .= '<td align="center"><a target="blank" href="'.$this->home.'forms/cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
                                        $file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
                                        $file .= '<td>'.$DR_Array[0]['code'].'</td>';
                                        $file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
                                        $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                                        $file .= '<td>'.$CM_Array[0]['title'].'</td>';
                                        $file .= '<td>'.$rows_2['description'].'</td>';
                                        $file .= '<td>'.$rows_2['outcome'].'</td>';                                        
                                        $file .= '<td>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
                                        $file .= '<td>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
                                    $file .= '</tr>';

                               }
                            }
                        }
                    }
                }
                $file .= '</table>';			
            } 

            echo $file;
	}
		
	public function reportPartID_9($filters,$dateSTR,$_SENDER)
	{
            $file = '';
			
            $SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND substanID = 2 AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="17" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Unsubstantiated complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
                $file .= '<th><div align="center"><strong>Driver Name</strong></div></th>';
                $file .= '<th><div align="center"><strong>ID</strong></div></th>';
                $file .= '<th><div align="center"><strong>Date</strong></div></th>';
                $file .= '<th><div align="center"><strong>Serco Ref No</strong></div></th>';
                $file .= '<th><div align="center"><strong>Reason</strong></div></th>';
                $file .= '<th width="250"><div align="center"><strong>Description</strong></div></th>';
                $file .= '<th width="250"><div align="center"><strong>Outcome</strong></div></th>';
                $file .= '<th><div align="center"><strong>Accountability</strong></div></th>';
                $file .= '<th><div align="center"><strong>Fault</strong></div></th>';
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;
                    foreach($this->rows_1 as $rows_1)
                    {
                        if($rows_1['serDT'] <> '')
                        {
                            $SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND substanID = 2 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
                            $Qry_2 = $this->DB->prepare($SQL_2);
                            $Qry_2->execute();
                            $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                            if(is_array($this->rows_2) && count($this->rows_2) > 0)
                            {
                                foreach($this->rows_2 as $rows_2)
                                {
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                    $DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
                                    $CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
                                    
                                    $file .= '<tr>';
                                        $file .= '<td align="center"><a target="blank" href="'.$this->home.'forms/cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
                                        $file .= '<td>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
                                        $file .= '<td>'.$DR_Array[0]['code'].'</td>';
                                        $file .= '<td align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
                                        $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                                        $file .= '<td>'.$CM_Array[0]['title'].'</td>';
                                        $file .= '<td>'.$rows_2['description'].'</td>';
                                        $file .= '<td>'.$rows_2['outcome'].'</td>';                                        
                                        $file .= '<td>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
                                        $file .= '<td>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
                                    $file .= '</tr>';
                               }
                            }
                        }
                    }
                }
                $file .= '</table>';
            }			
		echo $file;
	}
	
}
?>