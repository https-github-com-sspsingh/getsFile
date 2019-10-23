<?PHP
class Reports extends SFunctions
{
    private	$tableName = '';
    private	$basefile  = '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	  =	basename($_SERVER['PHP_SELF']);		
        $this->tableName     =	'';
    }

	public function BuilderReport($filters)
    {
        echo $this->Generate_BuilderReport($filters,2);
    }
	
    public function ReportDisplay($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        $return .= $filters['fltID_1'] > 0     ? " AND employee.desigID = ".$filters['fltID_1'] : "";
		$return .= $filters['fltID_3'] > 0     ? " AND sicklv.lvtypeID = ".$filters['fltID_3']  : "";
		$return .= $filters['fltID_2'] > 0     ? " AND sicklv.dayID = ".$filters['fltID_2'] 	: "";
		$return .= $filters['filterID'] <> ''  ? " AND sicklv.companyID In (".$filters['filterID'].") " : " AND sicklv.companyID IN (".$_SESSION[$this->website]['compID'].") ";

        if($filters['rtpyeID'] == 1)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 2)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 3)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 4)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 6)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 7)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 8)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}

        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)    {echo $this->reportPartID_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)    {echo $this->reportPartID_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)    {echo $this->reportPartID_3($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 4)    {echo $this->reportPartID_4($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 6)    {echo $this->reportPartID_6($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 7)    {echo $this->reportPartID_7($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 8)    {echo $this->reportPartID_8($return,$dateSTR,$_SENDER);}
    } 

    public function reportPartID_1($filters,$dateSTR,$_SENDER)
    {
        $file = '';
        
        $SQL = "SELECT * FROM sicklv LEFT JOIN employee ON employee.ID = sicklv.empID WHERE sicklv.ID > 0 ".$filters." Order By sicklv.ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="12" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Personal Leave Report : '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $file .= '<thead><tr>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
                
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Designation';
                    $file .= '<select class="form-control" id="sicklv_fltID_1">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= $this->GET_Masters($_SENDER['fltID_1'],'12');
                $file .= '</select></th>';
            
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Start Date</strong></div></th>';                
                
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Days';
                    $file .= '<select class="form-control" id="sicklv_fltID_2">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= '<option value="1" '.($_SENDER['fltID_2'] == 1 ? 'selected="selected"' : '').'>Monday</option>';
                    $file .= '<option value="2" '.($_SENDER['fltID_2'] == 2 ? 'selected="selected"' : '').'>Tuesday</option>';
                    $file .= '<option value="3" '.($_SENDER['fltID_2'] == 3 ? 'selected="selected"' : '').'>Wednesday</option>';
                    $file .= '<option value="4" '.($_SENDER['fltID_2'] == 4 ? 'selected="selected"' : '').'>Thursday</option>';
                    $file .= '<option value="5" '.($_SENDER['fltID_2'] == 5 ? 'selected="selected"' : '').'>Friday</option>';
                    $file .= '<option value="6" '.($_SENDER['fltID_2'] == 6 ? 'selected="selected"' : '').'>Saturday</option>';
                    $file .= '<option value="7" '.($_SENDER['fltID_2'] == 7 ? 'selected="selected"' : '').'>Sunday</option>';
                $file .= '</strong></div></th>';
            
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Leave Type';
                    $file .= '<select class="form-control" id="sicklv_fltID_3">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= $this->GET_Masters($_SENDER['fltID_3'],'11');
                $file .= '</select></th>';
            
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Duration</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Category</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Doctor Certificate</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Reason</strong></div></th>';                
            $file .= '</tr></thead>';
            
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
                    $arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $SU_Array  = $rows_2['sid'] > 0  ? $this->select('suburbs',array("*"), " WHERE ID = ".$rows_2['sid']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
                    $DS_Array  = $EM_Array[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
                    
                    $file .= '<tr>';
                        $file .= '<td>'.$arrCMP[0]['title'].'</td>';
                        $file .= '<td align="center">'.$rows_2['ecodeID'].'</td>';
                        $file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                        $file .= '<td class="d-set">'.$DS_Array[0]['title'].'</td>';
                        $file .= '<td align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
                        $file .= '<td >'.($rows_2['dayID'] == 1 ? 'Monday' :($rows_2['dayID'] == 2 ? 'Tuesday' :($rows_2['dayID'] == 3 ? 'Wednesday' 
                                        :($rows_2['dayID'] == 4 ? 'Thursday'  :($rows_2['dayID'] == 5 ? 'Friday'  :($rows_2['dayID'] == 6 ? 'Saturday'
                                        :($rows_2['dayID'] == 7 ? 'Sunday'  :''))))))).'</td>';
                        $file .= '<td>'.$LT_Array[0]['title'].'</td>';
                        $file .= '<td align="center">'.$rows_2['duration'].'</td>';
                        $file .= '<td align="center">'.$rows_2[''].'</td>';
                        $file .= '<td align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
                        $file .= '<td>'.$this->Word_Wraping($rows_2['reason'],'45').'</td>';
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
		
        $SQL = "SELECT * FROM sicklv WHERE ID > 0 AND duration <= '1' ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>One Day - Personal Leave Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Employee Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Employee Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Desgination</strong></div></th>';
            $file .= '<th><div align="center"><strong>Commencement Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Commencement Day</strong></div></th>';            
            $file .= '<th><div align="center"><strong>Duration</strong></div></th>';
			$file .= '<th><div align="center"><strong>Leave Type</strong></div></th>';
            $file .= '<th><div align="center"><strong>Doctor Certificate</strong></div></th>';
            $file .= '<th><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
					$DG_Array  = $EM_Array[0]['desigID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';

                    $file .= '<tr>';
                            $file .= '<td>'.$arrCMP[0]['title'].'</td>';
                            $file .= '<td align="center">'.$rows_2['ecodeID'].'</td>';
                            $file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
							$file .= '<td >'.$DG_Array[0]['title'].'</td>';
                            $file .= '<td align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
                            $file .= '<td >'.($rows_2['dayID'] == 1 ? 'Monday'  
                                                     :($rows_2['dayID'] == 2 ? 'Tuesday' 
                                                     :($rows_2['dayID'] == 3 ? 'Wednesday' 
                                                     :($rows_2['dayID'] == 4 ? 'Thursday'  
                                                     :($rows_2['dayID'] == 5 ? 'Friday'  
                                                     :($rows_2['dayID'] == 6 ? 'Saturday'
                                                     :($rows_2['dayID'] == 7 ? 'Sunday'  
                                                      :''))))))).'</td>';
                            $file .= '<td align="center">'.$rows_2['duration'].'</td>';
                            $file .= '<td>'.$LT_Array[0]['title'].'</td>';
                            $file .= '<td align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
                            $file .= '<td>'.$this->Word_Wraping($rows_2['reason'],'45').'</td>';


                    $file .= '</tr>';
                }
                            
                
            }
            $file .= '</table>';			
        } 

        return $file;
    }
	
	public function reportPartID_3($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT * FROM sicklv WHERE ID > 0 AND duration > '1' AND duration <= '2' ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Two Day - Personal Leave Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Employee Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Employee Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Desgination</strong></div></th>';
            $file .= '<th><div align="center"><strong>Commencement Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Commencement Day</strong></div></th>';
            $file .= '<th><div align="center"><strong>Duration</strong></div></th>';
            $file .= '<th><div align="center"><strong>Leave Type</strong></div></th>';
            $file .= '<th><div align="center"><strong>Doctor Certificate</strong></div></th>';
            $file .= '<th><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
					$DG_Array  = $EM_Array[0]['desigID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
					
                    $file .= '<tr>';
                            $file .= '<td>'.$arrCMP[0]['title'].'</td>';
                            $file .= '<td align="center">'.$rows_2['ecodeID'].'</td>';
                            $file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
							$file .= '<td >'.$DG_Array[0]['title'].'</td>';
                            $file .= '<td align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
                            $file .= '<td >'.($rows_2['dayID'] == 1 ? 'Monday'  
                                                     :($rows_2['dayID'] == 2 ? 'Tuesday' 
                                                     :($rows_2['dayID'] == 3 ? 'Wednesday' 
                                                     :($rows_2['dayID'] == 4 ? 'Thursday'  
                                                     :($rows_2['dayID'] == 5 ? 'Friday'  
                                                     :($rows_2['dayID'] == 6 ? 'Saturday'
                                                     :($rows_2['dayID'] == 7 ? 'Sunday'  
                                                      :''))))))).'</td>';
                            $file .= '<td align="center">'.$rows_2['duration'].'</td>';
                            $file .= '<td>'.$LT_Array[0]['title'].'</td>';
                            $file .= '<td align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
                            $file .= '<td>'.$this->Word_Wraping($rows_2['reason'],'45').'</td>';


                    $file .= '</tr>';
                }
            }
            $file .= '</table>';			
        } 

        return $file;
    }
	
	public function reportPartID_4($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT * FROM sicklv WHERE ID > 0 AND duration >= '3' ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Three Day - Personal Leave Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Employee Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Employee Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Desgination</strong></div></th>';
            $file .= '<th><div align="center"><strong>Commencement Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Commencement Day</strong></div></th>';
            $file .= '<th><div align="center"><strong>Duration</strong></div></th>';
            $file .= '<th><div align="center"><strong>Leave Type</strong></div></th>';
            $file .= '<th><div align="center"><strong>Doctor Certificate</strong></div></th>';
            $file .= '<th><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
					$DG_Array  = $EM_Array[0]['desigID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';

                    $file .= '<tr>';
						$file .= '<td>'.$arrCMP[0]['title'].'</td>';
						$file .= '<td align="center">'.$rows_2['ecodeID'].'</td>';
						$file .= '<td >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
						$file .= '<td >'.$DG_Array[0]['title'].'</td>';
						$file .= '<td align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
						$file .= '<td >'.($rows_2['dayID'] == 1 ? 'Monday' :($rows_2['dayID'] == 2 ? 'Tuesday' 
												 :($rows_2['dayID'] == 3 ? 'Wednesday' 
												 :($rows_2['dayID'] == 4 ? 'Thursday'  
												 :($rows_2['dayID'] == 5 ? 'Friday'  
												 :($rows_2['dayID'] == 6 ? 'Saturday'
												 :($rows_2['dayID'] == 7 ? 'Sunday'  
												  :''))))))).'</td>';
						$file .= '<td align="center">'.$rows_2['duration'].'</td>';
						$file .= '<td>'.$LT_Array[0]['title'].'</td>';
						$file .= '<td align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
						$file .= '<td>'.$this->Word_Wraping($rows_2['reason'],'45').'</td>';
                    $file .= '</tr>';
                }
            }
            $file .= '</table>';			
        }
        return $file;
    }
	
	public function reportPartID_6($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT FO.companyID, FO.monthID, FO.yearID, Sum(FO.DY_C1) AS DY_C1,  Sum(FO.DY_D1) AS DY_D1, Sum(FO.DY_C2) AS DY_C2,  Sum(FO.DY_D2) AS DY_D2, Sum(FO.DY_C3) AS DY_C3, Sum(FO.DY_D3) AS DY_D3 FROM
		(SELECT sicklv.ID, Month(sicklv.sldateID) AS monthID, Year(sicklv.sldateID) AS yearID, sicklv.sldateID, sicklv.empID, employee.desigID, sicklv.companyID,  If(Coalesce(sicklv.duration, 0) <= 1, 1, 0) AS DY_C1, If(Coalesce(sicklv.duration, 0) <= 1, Coalesce(sicklv.duration, 0), 0) AS DY_D1,
		If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), 1, 0) AS DY_C2, If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), Coalesce(sicklv.duration, 0), 0) AS DY_D2, If((Coalesce(sicklv.duration, 0) > 2), 1, 0) AS DY_C3,
		If((Coalesce(sicklv.duration, 0) > 2), Coalesce(sicklv.duration, 0), 0) AS DY_D3 FROM sicklv LEFT JOIN employee ON employee.ID = sicklv.empID WHERE sicklv.sldateID <> '' AND employee.desigID In(9,209) AND sicklv.lvtypeID In(1,7) ".$filters." ORDER BY sicklv.sldateID) AS FO WHERE FO.monthID > 0 AND FO.yearID > 0 GROUP BY FO.companyID, FO.monthID, FO.yearID ORDER BY FO.monthID, FO.yearID DESC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="20" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong> Sick Leave Summary Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th rowspan="2"><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th rowspan="2"><div align="center"><strong>Month Year</strong></div></th>';
            $file .= '<th colspan="6"><div align="center"><strong>Only - Drivers</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $file .= '<thead><tr>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th colspan="2"><div align="center"><strong>1 Days</strong></div></th>';
                $file .= '<th colspan="2"><div align="center"><strong>2 Days</strong></div></th>';
                $file .= '<th colspan="2"><div align="center"><strong>3 Days Plus</strong></div></th>'; 
            $file .= '</tr></thead>';
            
           $file .= '<thead><tr>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th><div align="center"><strong>Incidence</strong></div></th>';
                $file .= '<th><div align="center"><strong>Days</strong></div></th>';                
                $file .= '<th><div align="center"><strong>Incidence</strong></div></th>';
                $file .= '<th><div align="center"><strong>Days</strong></div></th>';                
                $file .= '<th><div align="center"><strong>Incidence</strong></div></th>';
                $file .= '<th><div align="center"><strong>Days</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
				$incID = 0;	$dayID = 0;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					
					$file .= '<tr>';
						$file .= '<td>'.$arrCMP[0]['title'].'</td>';
						
                        $file .= '<td align="center">'.$months[(int)$rows_2['monthID']].' - '.$rows_2['yearID'].'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_C1'] > 0 ? $rows_2['DY_C1'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_D1'] > 0 ? $rows_2['DY_D1'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_C2'] > 0 ? $rows_2['DY_C2'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_D2'] > 0 ? $rows_2['DY_D2'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_C3'] > 0 ? $rows_2['DY_C3'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_D3'] > 0 ? $rows_2['DY_D3'] : '').'</td>'; 
                    $file .= '</tr>'; 
                    
                    
                    $incID += ($rows_2['DY_C1']) + ($rows_2['DY_C2']) + ($rows_2['DY_C3']);
                    $dayID += ($rows_2['DY_D1']) + ($rows_2['DY_D2']) + ($rows_2['DY_D3']);
                }
				
				$file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="6" align="center"><b> Total Incidence : '.$incID.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Total Days : '.$dayID.'</b></td>';
				$file .= '</tr>';  
					
            }
            $file .= '</table>';			
        }
        return $file;
    }

	public function reportPartID_7($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT FO.companyID, FO.monthID, FO.yearID, Sum(FO.DY_C1) AS DY_C1,  Sum(FO.DY_D1) AS DY_D1, Sum(FO.DY_C2) AS DY_C2,  Sum(FO.DY_D2) AS DY_D2, Sum(FO.DY_C3) AS DY_C3, Sum(FO.DY_D3) AS DY_D3 FROM
		(SELECT sicklv.ID, Month(sicklv.sldateID) AS monthID, Year(sicklv.sldateID) AS yearID, sicklv.sldateID, sicklv.empID, employee.desigID, sicklv.companyID,  If(Coalesce(sicklv.duration, 0) <= 1, 1, 0) AS DY_C1, If(Coalesce(sicklv.duration, 0) <= 1, Coalesce(sicklv.duration, 0), 0) AS DY_D1,
		If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), 1, 0) AS DY_C2, If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), Coalesce(sicklv.duration, 0), 0) AS DY_D2, If((Coalesce(sicklv.duration, 0) > 2), 1, 0) AS DY_C3,
		If((Coalesce(sicklv.duration, 0) > 2), Coalesce(sicklv.duration, 0), 0) AS DY_D3 FROM sicklv LEFT JOIN employee ON employee.ID = sicklv.empID WHERE sicklv.sldateID <> '' AND employee.desigID <> 9 AND sicklv.lvtypeID In(1,7) ".$filters." ORDER BY sicklv.sldateID) AS FO WHERE FO.monthID > 0 AND FO.yearID > 0 GROUP BY FO.companyID, FO.monthID, FO.yearID ORDER BY FO.monthID, FO.yearID DESC ";  
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="20" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong> Sick Leave Summary Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th rowspan="2"><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th rowspan="2"><div align="center"><strong>Month Year</strong></div></th>';
            $file .= '<th colspan="6"><div align="center"><strong>Only - Drivers</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $file .= '<thead><tr>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th colspan="2"><div align="center"><strong>1 Days</strong></div></th>';
                $file .= '<th colspan="2"><div align="center"><strong>2 Days</strong></div></th>';
                $file .= '<th colspan="2"><div align="center"><strong>3 Days Plus</strong></div></th>'; 
            $file .= '</tr></thead>';
            
           $file .= '<thead><tr>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th>&nbsp;</th>';
                $file .= '<th><div align="center"><strong>Incidence</strong></div></th>';
                $file .= '<th><div align="center"><strong>Days</strong></div></th>';                
                $file .= '<th><div align="center"><strong>Incidence</strong></div></th>';
                $file .= '<th><div align="center"><strong>Days</strong></div></th>';                
                $file .= '<th><div align="center"><strong>Incidence</strong></div></th>';
                $file .= '<th><div align="center"><strong>Days</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
				$incID = 0;	$dayID = 0;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					
					$file .= '<tr>';
						$file .= '<td>'.$arrCMP[0]['title'].'</td>';
						
                        $file .= '<td align="center">'.$months[(int)$rows_2['monthID']].' - '.$rows_2['yearID'].'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_C1'] > 0 ? $rows_2['DY_C1'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_D1'] > 0 ? $rows_2['DY_D1'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_C2'] > 0 ? $rows_2['DY_C2'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_D2'] > 0 ? $rows_2['DY_D2'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_C3'] > 0 ? $rows_2['DY_C3'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['DY_D3'] > 0 ? $rows_2['DY_D3'] : '').'</td>'; 
                    $file .= '</tr>'; 
                    
                    
                    $incID += ($rows_2['DY_C1']) + ($rows_2['DY_C2']) + ($rows_2['DY_C3']);
                    $dayID += ($rows_2['DY_D1']) + ($rows_2['DY_D2']) + ($rows_2['DY_D3']);
                }
				
				$file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="6" align="center"><b> Total Incidence : '.$incID.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Total Days : '.$dayID.'</b></td>';
				$file .= '</tr>';  
					
            }
            $file .= '</table>';			
        }
        return $file;
    }

	public function reportPartID_8($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
		
		$SQL = " Select
    FO.companyID,
    FO.monthID,
    FO.yearNM,
    FO.desigID,
    FO.category,
    FO.incidentID,
    FO.daysID
From
    (Select
         CategoryONE.companyID,
         CategoryONE.monthID,
         CategoryONE.yearNM,
         CategoryONE.desigID,
         CategoryONE.category,
         CategoryONE.incidentID,
         CategoryONE.daysID
     From
         (Select
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              1 As category,
              Sum(SLSummary.countID) As incidentID,
              Sum(Coalesce(SLSummary.duration)) As daysID,
              SLSummary.companyID
          From
              (Select
                   sicklv.ID,
                   sicklv.sldateID,
                   Month(sicklv.sldateID) As monthID,
                   Year(sicklv.sldateID) As yearNM,
                   If(employee.desigID = 209, 9, employee.desigID) As desigID,
                   sicklv.duration,
                   1 As countID,
                   sicklv.companyID
               From
                   sicklv Inner Join
                   employee On employee.ID = sicklv.empID
               Where
				   sicklv.lvtypeID In(1,7) AND 	
                   sicklv.duration <= 1 And
                   sicklv.sldateID <> '' And
                   sicklv.sldateID <> '0000-00-00' And
                   sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary
          Where
              SLSummary.yearNM > 0
          Group By
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              1,
              SLSummary.companyID
          Order By
              SLSummary.yearNM,
              SLSummary.monthID) As CategoryONE
     UNION All
     Select
         categoryTWO.companyID,
         categoryTWO.monthID,
         categoryTWO.yearNM,
         categoryTWO.desigID,
         categoryTWO.category,
         categoryTWO.incidentID,
         categoryTWO.daysID
     From
         (Select
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              2 As category,
              Sum(SLSummary.countID) As incidentID,
              Sum(Coalesce(SLSummary.duration)) As daysID,
              SLSummary.companyID
          From
              (Select
                   sicklv.ID,
                   sicklv.sldateID,
                   Month(sicklv.sldateID) As monthID,
                   Year(sicklv.sldateID) As yearNM,
                   If(employee.desigID = 209, 9, employee.desigID) As desigID,
                   sicklv.duration,
                   1 As countID,
                   sicklv.companyID
               From
                   sicklv Inner Join
                   employee On employee.ID = sicklv.empID
               Where
				   sicklv.lvtypeID In(1,7) AND
                   sicklv.duration > 1 And
                   sicklv.duration <= 2 And
                   sicklv.sldateID <> '' And
                   sicklv.sldateID <> '0000-00-00' And
                   sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary
          Where
              SLSummary.yearNM > 0
          Group By
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              SLSummary.companyID,
              1
          Order By
              SLSummary.yearNM,
              SLSummary.monthID) As categoryTWO
     UNION All
     Select
         categoryTHREE.companyID,
         categoryTHREE.monthID,
         categoryTHREE.yearNM,
         categoryTHREE.desigID,
         categoryTHREE.category,
         categoryTHREE.incidentID,
         categoryTHREE.daysID
     From
         (Select
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              3 As category,
              Sum(SLSummary.countID) As incidentID,
              Sum(Coalesce(SLSummary.duration)) As daysID,
              SLSummary.companyID
          From
              (Select
                   sicklv.ID,
                   sicklv.sldateID,
                   Month(sicklv.sldateID) As monthID,
                   Year(sicklv.sldateID) As yearNM,
                   If(employee.desigID = 209, 9, employee.desigID) As desigID,
                   sicklv.duration,
                   1 As countID,
                   sicklv.companyID
               From
                   sicklv Inner Join
                   employee On employee.ID = sicklv.empID
               Where
				   sicklv.lvtypeID In(1,7) AND
                   sicklv.duration >= 3 And
                   sicklv.sldateID <> '' And
                   sicklv.sldateID <> '0000-00-00' And
                   sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary
          Where
              SLSummary.yearNM > 0
          Group By
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              SLSummary.companyID,
              1
          Order By
              SLSummary.yearNM,
              SLSummary.monthID) As categoryTHREE) As FO
Order By
    FO.companyID,
    FO.yearNM,
    FO.monthID,
    FO.category ASC ";
		
		/*$SQL = "Select FO.companyID, FO.monthID, FO.yearNM, FO.desigID, FO.category, FO.incidentID, FO.daysID From (Select CategoryONE.companyID, CategoryONE.monthID, CategoryONE.yearNM, CategoryONE.desigID, CategoryONE.category, CategoryONE.incidentID, CategoryONE.daysID From (Select SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, 1 As category, Sum(SLSummary.countID) As incidentID, Sum(Coalesce(SLSummary.duration)) As daysID, SLSummary.companyID
		From (Select sicklv.ID, sicklv.sldateID, Month(sicklv.sldateID) As monthID,  Year(sicklv.sldateID) As yearNM, employee.desigID, sicklv.duration, 1 As countID, sicklv.companyID From sicklv Inner Join employee On employee.ID = sicklv.empID Where sicklv.duration <= 1 AND sicklv.sldateID <> '' AND sicklv.sldateID <> '0000-00-00' AND sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary Where SLSummary.yearNM > 0 Group By SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, 1, SLSummary.companyID Order By SLSummary.yearNM,  SLSummary.monthID) As CategoryONE UNION All Select
		categoryTWO.companyID, categoryTWO.monthID, categoryTWO.yearNM, categoryTWO.desigID, categoryTWO.category, categoryTWO.incidentID, categoryTWO.daysID From (Select SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, 2 As category, Sum(SLSummary.countID) As incidentID, Sum(Coalesce(SLSummary.duration)) As daysID, SLSummary.companyID From (Select sicklv.ID, sicklv.sldateID, Month(sicklv.sldateID) As monthID, Year(sicklv.sldateID) As yearNM, employee.desigID, sicklv.duration, 1 As countID, sicklv.companyID 
		From sicklv Inner Join employee On employee.ID = sicklv.empID  Where (sicklv.duration > 1 And sicklv.duration <= 2) AND sicklv.sldateID <> '' AND sicklv.sldateID <> '0000-00-00' AND sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary Where SLSummary.yearNM > 0 Group By SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, SLSummary.companyID, 1 Order By SLSummary.yearNM, SLSummary.monthID) As categoryTWO UNION All Select categoryTHREE.companyID, categoryTHREE.monthID, categoryTHREE.yearNM, categoryTHREE.desigID, categoryTHREE.category, categoryTHREE.incidentID, 
		categoryTHREE.daysID From (Select SLSummary.monthID, SLSummary.yearNM,  SLSummary.desigID, 3 As category, Sum(SLSummary.countID) As incidentID, Sum(Coalesce(SLSummary.duration)) As daysID, SLSummary.companyID From (Select sicklv.ID, sicklv.sldateID, Month(sicklv.sldateID) As monthID, Year(sicklv.sldateID) As yearNM, employee.desigID, sicklv.duration, 1 As countID, sicklv.companyID From sicklv Inner Join employee On employee.ID = sicklv.empID Where sicklv.duration >= 3 AND sicklv.sldateID <> '' AND sicklv.sldateID <> '0000-00-00' AND sicklv.sldateID <> '1970-01-01' ".$filters.") 
		As SLSummary Where SLSummary.yearNM > 0 Group By SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, SLSummary.companyID, 1 Order By SLSummary.yearNM, SLSummary.monthID) As categoryTHREE) As FO Order By FO.companyID, FO.yearNM, FO.monthID, FO.category ASC";*/
		
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="20" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong> Sick Leave Summary Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Category</strong></div></th>'; 			
			$file .= '<th><div align="center"><strong>Designation</strong></div></th>';
			$file .= '<th><div align="center"><strong>Incident</strong></div></th>'; 
			$file .= '<th><div align="center"><strong>Days</strong></div></th>'; 
            $file .= '</tr></thead>';
            
            $months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
				$incID = 0;	$dayID = 0;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					$arrMST  = $rows_2['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['desigID']." ") : '';
					
					$file .= '<tr>';
						$file .= '<td>'.$arrCMP[0]['title'].'</td>';
						
                        $file .= '<td align="center">'.$months[(int)$rows_2['monthID']].' - '.$rows_2['yearNM'].'</td>';
                        $file .= '<td align="center">'.($rows_2['category'] == 1 ? 'ONE' :($rows_2['category'] == 2 ? 'TWO' :($rows_2['category'] == 3 ? 'THREE++' : 'N-M'))).'</td>';
                        $file .= '<td>'.($rows_2['desigID'] == 9 ? 'Driver / Coordinator' : $arrMST[0]['title']).'</td>';
                        $file .= '<td align="center">'.($rows_2['incidentID'] > 0 ? $rows_2['incidentID'] : '').'</td>';
                        $file .= '<td align="center">'.($rows_2['daysID'] > 0 ? $rows_2['daysID'] : '').'</td>'; 
                    $file .= '</tr>'; 
                    
                    
                    $incID += ($rows_2['incidentID']);
                    $dayID += ($rows_2['daysID']);
                }
				
				$file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="4" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2" align="center"><b> Total Incidence : '.$incID.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Total Days : '.$dayID.'</b></td>';
				$file .= '</tr>';  
					
            }
            $file .= '</table>';			
        }
        return $file;
    }
	
}
?>