<?PHP
class Reports extends SFunctions
{
    private $tableName = '';
    private $basefile  = '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	  = basename($_SERVER['PHP_SELF']);		
        $this->tableName  = '';
    }
	
	public function BuilderReport($filters)
    {
		echo $this->Generate_BuilderReport($filters,6);
    }

    public function ReportDisplay($filters)
    {
        $_SENDER = $filters;      //echo '<pre>'; echo print_r($filters);
        
        $return  = "";
        $return .= ($filters['fltID_1'] == 1 || $filters['fltID_1'] == 0) && $filters['fltID_1'] <> '' ? " AND tickID_2 = ".$filters['fltID_1'] : "";		
        $return .= ($filters['fltID_2'] == 1 || $filters['fltID_2'] == 0) && $filters['fltID_2'] <> '' ? " AND tickID_1 = ".$filters['fltID_2'] : "";
        
        $return .= $filters['fltID_3'] > 0 ? " AND acccatID = ".$filters['fltID_3'] : "";
        $return .= $filters['fltID_4'] > 0 ? " AND accID = ".$filters['fltID_4'] : "";
        $return .= $filters['fltID_5'] > 0 ? " AND responsibleID = ".$filters['fltID_5'] : "";
        $return .= $filters['filterID'] <> '' ? " AND companyID In (".$filters['filterID'].") " : " AND companyID IN (".$_SESSION[$this->website]['compID'].") ";
		$return .= $filters['fltID_6'] > 0 ? " AND progressID = ".$filters['fltID_6'] : "";

        if($filters['rtpyeID'] >= 1 && $filters['rtpyeID'] <= 6)	
		{
			if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}
		}
		
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)         {echo $this->reportPartID_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->reportPartID_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->reportPartID_3($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 4)         {echo $this->reportPartID_4($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 5)         {echo $this->reportPartID_5($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 6)         {echo $this->reportPartID_6($return,$dateSTR,$_SENDER);}
    } 

    public function reportPartID_1($filters,$dateSTR,$_SENDER)
    {
        $file = '';
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Accidents Register Report : '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>DEPOT</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Reference</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Date</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Time</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Bus</strong></div></th>'; 
                
                $file .= '<th width="250" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Training Accident';
                    $file .= '<select class="form-control" id="accident_fltID_1">';
                    $file .= '<option value="" selected="selected">-- Select --</option>';
                    $file .= '<option value="1" '.($_SENDER['fltID_1'] == '1' ? 'selected="selected"' : '').'>Yes</option>';
                    $file .= '<option value="2" '.($_SENDER['fltID_1'] == '2' ? 'selected="selected"' : '').'>No</option>';
                    $file .= '</select>';
                $file .= '</strong></div></th>';
            
                $file .= '<th width="250" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Driver Not Applicable';
                    $file .= '<select class="form-control" id="accident_fltID_2">';
                    $file .= '<option value="" selected="selected">-- Select --</option>';
                    $file .= '<option value="1" '.($_SENDER['fltID_2'] == '1' ? 'selected="selected"' : '').'>Yes</option>';
                    $file .= '<option value="2" '.($_SENDER['fltID_2'] == '2' ? 'selected="selected"' : '').'>No</option>';
                    $file .= '</select>';
                $file .= '</strong></div></th>';
            
                $file .= '<th width="250" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Accident Category';
                    $file .= '<select class="form-control" id="accident_fltID_3">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= $this->GET_Masters($_SENDER['fltID_3'],'21');
                    $file .= '</select>';		
                $file .= '</strong></div></th>';
            
                $file .= '<th width="250" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Accident Detail';
                    $file .= '<select class="form-control" id="accident_fltID_4">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= $this->GET_Masters($_SENDER['fltID_4'],'20');
                    $file .= '</select>';
                $file .= '</strong></div></th>';
            
                $file .= '<th width="250" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Responsible';
                    $file .= '<select class="form-control" id="accident_fltID_5">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= '<option value="1" '.($_SENDER['fltID_5'] == '1' ? 'selected="selected"' : '').'>Yes</option>';
                    $file .= '<option value="2" '.($_SENDER['fltID_5'] == '2' ? 'selected="selected"' : '').'>No</option>';
                    $file .= '</select>';
                $file .= '</strong></div></th>';
            
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Bus Repairs</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Other Repairs</strong></div></th>';            
                
                $file .= '<th width="250" style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Status';
                    $file .= '<select class="form-control" id="accident_fltID_6">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= '<option value="1" '.($_SENDER['fltID_6'] == '1' ? 'selected="selected"' : '').'>Complete</option>';
                    $file .= '<option value="2" '.($_SENDER['fltID_6'] == '2' ? 'selected="selected"' : '').'>Pending</option>';
                    $file .= '<option value="3" '.($_SENDER['fltID_6'] == '3' ? 'selected="selected"' : '').'>Written Off</option>';
                $file .= '</select></strong></div></th>';
            $file .= '</tr></thead>';
            
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                { 
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AD_Array  = $rows_2['accID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['accID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $EM_Array  = $rows_2['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';
                    $DS_Array  = $EM_Array[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    $file .= '<td  align="center">'.$rows_2['scodeID'].'</td>';
                    $file .= '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                    $file .= '<td>'.$rows_2['refno'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    $file .= '<td>'.$rows_2['timeID'].'</td>';
                    $file .= '<td>'.$rows_2['busID'].'</td>';
                    $file .= '<td align="center">'.($rows_2['tickID_2'] == 1 ? 'Yes' :($rows_2['tickID_2'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td align="center">'.($rows_2['tickID_1'] == 1 ? 'Yes' : '').'</td>';
                    $file .= '<td>'.$AC_Array[0]['title'].'</td>';
                    $file .= '<td>'.$AD_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td>'.$rows_2['rprcost'].'</td>';
                    $file .= '<td>'.$rows_2['othcost'].'</td>';
                    $file .= '<td align="center">'.($rows_2['progressID'] == 1 ? 'Complete'  :($rows_2['progressID'] == 2 ? 'Pending' :($rows_2['progressID'] == 3 ? 'Written Off' : ''))).'</td>';
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
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) >= 1000 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>All Greater than $1000 - Accident Report'.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';			
            $file .= '<th><div align="center"><strong>Staff Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Name</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Accident Details</strong></div></th>';
            $file .= '<th><div align="center"><strong>Cost</strong></div></th>';
            $file .= '<th><div align="center"><strong>Driver Responsible</strong></div></th>';
            $file .= '<th width="250"><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                {
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    $file .= '<td align="center">'.$rows_2['scodeID'].'</td>';
                    $file .= '<td>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                    $file .= '<td align="center">'.$rows_2['busID'].'</td>';
                    $file .= '<td align="center">'.$AC_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    $file .= '<td align="center">'.
                    ($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td>'.($rows_2['description']).'</td>';
                    $file .= '</tr>';
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    $file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2"></td>';
                    $file .= '</tr>';   
                    
            $file .= '</table>';			
        } 

        return $file;
    }
	
	public function reportPartID_3($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) >= 1000 AND responsibleID = 1 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Responsible Greater than $1000 - Accident Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Name</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Accident Details</strong></div></th>';
            $file .= '<th><div align="center"><strong>Cost</strong></div></th>';
            $file .= '<th><div align="center"><strong>Driver Responsible</strong></div></th>';
            $file .= '<th width="250"><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                {
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    $file .= '<td align="center">'.$rows_2['scodeID'].'</td>';
                    $file .= '<td>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                    $file .= '<td align="center">'.$rows_2['busID'].'</td>';
                    $file .= '<td align="center">'.$AC_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    $file .= '<td align="center">'.
                    ($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td>'.($rows_2['description']).'</td>';
                    $file .= '</tr>';
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    $file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2"></td>';
                    $file .= '</tr>';   
                    
            $file .= '</table>';			
        } 

        return $file;
    }
    
    public function reportPartID_4($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) <= 1000 AND responsibleID = 1 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Responsible Less than $1000 - Accident Report'.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Name</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Accident Details</strong></div></th>';
            $file .= '<th><div align="center"><strong>Cost</strong></div></th>';
            $file .= '<th><div align="center"><strong>Driver Responsible</strong></div></th>';
            $file .= '<th width="250"><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                foreach($this->rows_1 as $rows_2)
                { 
				$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    $file .= '<td align="center">'.$rows_2['scodeID'].'</td>';
                    $file .= '<td>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                    $file .= '<td align="center">'.$rows_2['busID'].'</td>';
                    $file .= '<td align="center">'.$AC_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    $file .= '<td align="center">'.
                    ($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td>'.($rows_2['description']).'</td>';
                    $file .= '</tr>';
                    
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    $file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2"></td>';
                    $file .= '</tr>';   
                    
            $file .= '</table>';			
        } 

        return $file;
    }
    
    public function reportPartID_5($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) >= 1000 AND responsibleID = 2 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Not Responsible Greater than $1000 - Accident Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Name</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Accident Details</strong></div></th>';
            $file .= '<th><div align="center"><strong>Cost</strong></div></th>';
            $file .= '<th><div align="center"><strong>Driver Responsible</strong></div></th>';
            $file .= '<th width="250"><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                { 
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    $file .= '<td align="center">'.$rows_2['scodeID'].'</td>';
                    $file .= '<td>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                    $file .= '<td align="center">'.$rows_2['busID'].'</td>';
                    $file .= '<td align="center">'.$AC_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    $file .= '<td align="center">'.
                    ($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td>'.($rows_2['description']).'</td>';
                    $file .= '</tr>';
                    
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    $file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2"></td>';
                    $file .= '</tr>';   
                    
            $file .= '</table>';			
        } 

        return $file;
    }
    
    public function reportPartID_6($filters,$dateSTR,$_SENDER)
    {
        $file = '';
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) <= 1000 AND responsibleID = 2 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Not Responsible Less than $1000 - Accident Report '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Code</strong></div></th>';
            $file .= '<th><div align="center"><strong>Staff Name</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Bus No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Accident Details</strong></div></th>';
            $file .= '<th><div align="center"><strong>Cost</strong></div></th>';
            $file .= '<th><div align="center"><strong>Driver Responsible</strong></div></th>';
            $file .= '<th width="250"><div align="center"><strong>Reason</strong></div></th>';
            $file .= '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                { 
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    $file .= '<td align="center">'.$rows_2['scodeID'].'</td>';
                    $file .= '<td>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    $file .= '<td align="center">'.$rows_2['refno'].'</td>';
                    $file .= '<td align="center">'.$rows_2['busID'].'</td>';
                    $file .= '<td align="center">'.$AC_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    $file .= '<td align="center">'.
                    ($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    $file .= '<td>'.($rows_2['description']).'</td>';
                    $file .= '</tr>';
                    
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    $file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="2"></td>';
                    $file .= '</tr>';   
                    
            $file .= '</table>';			
        } 

        return $file;
    }
}
?>