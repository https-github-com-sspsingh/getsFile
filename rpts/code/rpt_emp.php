<?PHP
class Reports extends CFunctions
{
    private	$tableName	=	'';
    private	$basefile	 =	'';

    function __construct()
    {	
        parent::__construct();
		
		$this->basefile  = basename($_SERVER['PHP_SELF']);
        $this->tableName = '';
    }
	
	public function BuilderReport($filters)
    {
        echo $this->Generate_BuilderReport($filters,1);
    }
	
    public function ReportDisplay($filters)
    {
        $_SENDER = $filters;

        $return  = "";		
        $return .= $filters['fltID_3'] > 0    ? " AND employee.genderID = ".$filters['fltID_3'] : "";
        $return .= $filters['fltID_5'] > 0    ? " AND employee.desigID = ".$filters['fltID_5'] : "";
        $return .= $filters['fltID_2'] > 0    ? " AND employee.sid = ".$filters['fltID_2'] : "";
        $return .= $filters['fltID_1'] <> '0' && $filters['fltID_1'] <> '' ? " AND employee.ftextID = '".$filters['fltID_1']."' " : "";
        $return .= $filters['fltID_4'] > 0    ? " AND employee.casualID = ".$filters['fltID_4'] : "";
        $return .= $filters['fltID_6'] > 0    ? " AND employee.rleavingID = ".$filters['fltID_6'] : "";
        $return .= $filters['fltID_7'] > 0    ? " AND employee.desigID = ".$filters['fltID_7'] : "";
        $return .= $filters['fltID_8'] > 0    ? " AND employee.desigID = ".$filters['fltID_8'] : "";

        if($filters['rtpyeID'] == 1 || $filters['rtpyeID'] == 3 || $filters['rtpyeID'] == 4)
        {
            $return .= " AND employee.companyID In (".($filters['filterID'] <> '' ? $filters['filterID'] : $_SESSION[$this->website]['compID']).") ";
        }
        else if($filters['rtpyeID'] == 2)
        {
            $return .= " AND employee.companyID In (".($filters['filterID'] <> '' ? $filters['filterID'] : $_SESSION[$this->website]['compID']).") ";
			$return .= "";
        }
		else
		{
			$return .= " AND employee.companyID In (".$_SESSION[$this->website]['compID'].") ";
		}
		
		$passID = '';
		if($filters['rtpyeID'] == 2)
		{
			if($this->Decrypt($filters['dashID']) == 1)
			{
				$passID = 'Drivers';
			}
			else if($this->Decrypt($filters['dashID']) == 2)
			{
				$passID = 'WWC';
			}
			else if($this->Decrypt($filters['dashID']) == 3)
			{
				$passID = 'EnggLicense';
			} 
		}
		
        if($filters['rtpyeID'] == 3)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'esdate');}}
        if($filters['rtpyeID'] == 4)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'enddate');}}

        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)        {echo $this->reportPartID_1($return,$_SENDER);} 
        else if($filters['rtpyeID'] == 2)	
		{
			echo $this->reportPartID_2($return,$_SENDER,$passID);			
			echo $this->reportPartID_02($return,$_SENDER,$passID);
		} 
        else if($filters['rtpyeID'] == 3)	{echo $this->reportPartID_3($return,$_SENDER,$dateSTR);} 
        else if($filters['rtpyeID'] == 4)	{echo $this->reportPartID_4($return,$_SENDER,$dateSTR);} 
    } 

    public function reportPartID_1($filters,$_SENDER)
    {
        $file = '';

        $SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 1 ".$filters." Order By code ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {		    
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Employee Report</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Air Key No.</strong></div></th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Drive Right No</strong></div></th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Locker No.</strong></div></th>';

            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="150"><div align="left"><strong>Suburb';
                $file .= '<select class="form-control" id="emp_fltID_2">';
                $file .= '<option value="0" selected="selected">-- Select --</option>';
                $file .= $this->GET_SubUrbs($_SENDER['fltID_2'],'');
                $file .= '</select>';	
            $file .= '</strong></div></th>';

            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="150"><div align="left"><strong>Gender';
            $file .= '<select class="form-control" id="emp_fltID_3">';
                $file .= '<option value="0" selected="selected">-- Select --</option>';
                $file .= $this->GET_Masters($_SENDER['fltID_3'],'6');
            $file .= '</select>';
            $file .= '</strong></div></th>';    

            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="150"><div align="left"><strong>Causal Full Time';
            $file .= '<select class="form-control" id="emp_fltID_4">';
                $file .= '<option value="0" selected="selected">-- Select --</option>';
                $file .= '<option value="1" '.($_SENDER['fltID_4'] == 1 ? 'selected="selected"' : '').'>Full Time</option>';
                $file .= '<option value="2" '.($_SENDER['fltID_4'] == 2 ? 'selected="selected"' : '').'>Part Time</option>';
                $file .= '<option value="3" '.($_SENDER['fltID_4'] == 3 ? 'selected="selected"' : '').'>Casual</option>';
                $file .= '</select>';
            $file .= '</strong></div></th>';

            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="150"><div align="left"><strong>Designation';
            $file .= '<select class="form-control" id="emp_fltID_5">';
                $file .= '<option value="0" selected="selected">-- Select --</option>';
                $file .= $this->GET_Masters($_SENDER['fltID_5'],'12');
            $file .= '</select>';
            $file .= '</strong></div></th>';

            $file .= '</tr></thead>';


            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {
                    $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                    $GN_Array  = $rows_1['genderID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['genderID']." ") : '';
                    $DE_Array  = $rows_1['desigID'] > 0   ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
                    $SU_Array  = $rows_1['sid'] > 0       ? $this->select('suburbs',array("*"), " WHERE ID = ".$rows_1['sid']." ") : '';

                    $file .= '<tr>'; 
                        $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                        $file .= '<td align="center">'.$rows_1['code'].'</td>';
                        $file .= '<td >'.$rows_1['fname'].' '.$rows_1['lname'].'</td>';
                        $file .= '<td align="center" >'.$rows_1['arkno'].'</td>';	
                        $file .= '<td align="center">'.$rows_1['drvrightID'].'</td>';
                        $file .= '<td align="center">'.$rows_1['lockerno'].'</td>';
                        ///$file .= '<td align="center">'.$rows_1['ftextID'].'</td>';
                        $file .= '<td >'.$SU_Array[0]['title'].' - '.$SU_Array[0]['pscode'].'</td>';
                        $file .= '<td align="center">'.$GN_Array[0]['title'].'</td>';
                        $file .= '<td align="center">'.($rows_1['casualID'] == 1 ? 'Full Time'  :($rows_1['casualID'] == 2 ? 'Part Time' :($rows_1['casualID'] == 3 ? 'Casual' : ''))).'</td>';
                        $file .= '<td >'.$DE_Array[0]['title'].'</td>';
                    $file .= '</tr>';
                }
            }
            $file .= '</table>';			
        } 

        return $file;
    } 

    public function reportPartID_2($filters,$_SENDER,$passID)
    {
		//echo '<pre>'; echo print_r($_SENDER);
		
        $file = '';		
        $crtID .= " AND DATE(All_Data.lcnoDT) <= '".date('Y-m-d',strtotime('+'.$_SENDER['daysID'].'Days'))."' ";
		if($passID == 'EnggLicense')
		{
			$crtID .= " AND All_Data.typeID <> 'Drivers' AND All_Data.typeID <> 'WWC' ";
		}
		else if($passID <> '' )
		{
			$crtID .= " AND All_Data.typeID = '".$passID."' ";
		}
		else
		{
			$crtID .= " AND All_Data.typeID In('Drivers','WWC') ";
		}
		
		
        $SQL = "SELECT All_Data.ID, All_Data.code, All_Data.full_name, All_Data.desigID, All_Data.companyID, All_Data.lcnoID, All_Data.lcnoDT, All_Data.typeID
        FROM (SELECT ID, code, full_name, desigID, companyID, ddlcno AS lcnoID, ddlcdt AS lcnoDT, 'Drivers' AS typeID FROM employee	WHERE status = 1 ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, wwcprno AS lcnoID, wwcprdt AS lcnoDT, 'WWC' AS typeID FROM employee WHERE wwcapdt = '0000-00-00' AND status = 1 AND desigID In(9,208,209) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, gfpermitNO AS lcnoID, gfpnexpDT AS lcnoDT, 'GasFittingNO' AS typeID FROM employee WHERE gfpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, acpermitNO AS lcnoID, acpnexpDT AS lcnoDT, 'AConRefNO' AS typeID FROM employee WHERE acpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, wsdpermitNO AS lcnoID, wsdpnexpDT AS lcnoDT, 'WorkSafeDoggingNO' AS typeID FROM employee WHERE wsdpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, flpermitNO AS lcnoID, flpnexpDT AS lcnoDT, 'ForliftLcNO' AS typeID FROM employee WHERE flpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters.") AS All_Data WHERE All_Data.companyID > 0 
		".$crtID." Order By All_Data.lcnoDT ASC  ";	
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($this->rows_1) && count($this->rows_1) > 0)
			{
				$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
				$file .= '<thead><tr>';
				$file .= '<th colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Licences Expiring within '.$_SENDER['daysID'].' Days - Employee Report</strong></div></th>'; 
				$file .= '</tr></thead>';

				$file .= '<thead><tr>'; 
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Designation</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Licence Type</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Expiry Date</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>&nbsp;</strong></div></th>';
				$file .= '</tr></thead>';
				if(is_array($this->rows_1) && count($this->rows_1) > 0)			
				{
						$srID = 1;
						foreach($this->rows_1 as $rows_1)
						{
							$CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
							$DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
							
							$file .= '<tr>';
								$file .= '<td '.($this->Emp_LicensesExpiryCounts($rows_1['ID'],$rows_1['typeID'])).'>'.$CP_Array[0]['title'].'</td>';
								$file .= '<td '.($this->Emp_LicensesExpiryCounts($rows_1['ID'],$rows_1['typeID'])).' align="center">'.$rows_1['code'].'</td>';
								$file .= '<td '.($this->Emp_LicensesExpiryCounts($rows_1['ID'],$rows_1['typeID'])).' >'.$rows_1['full_name'].'</td>';
								$file .= '<td '.($this->Emp_LicensesExpiryCounts($rows_1['ID'],$rows_1['typeID'])).' >'.$DE_Array[0]['title'].'</td>';
								$file .= '<td '.($this->Emp_LicensesExpiryCounts($rows_1['ID'],$rows_1['typeID'])).' align="center">'.$rows_1['typeID'].'</td>';
								$file .= '<td '.($rows_1['typeID'] <> 'WWC' ? 'colspan="2"' : '').' '.($this->Emp_LicensesExpiryCounts($rows_1['ID'],$rows_1['typeID'])).' align="center">'.$this->VdateFormat($rows_1['lcnoDT']).'</td>';
								if($rows_1['typeID'] == 'WWC')
								{
									$file .= '<td align="center"><a class="fa fa-pencil fillLicenseNoDate" aria-sort="'.$rows_1['ID'].'" style="cursor:pointer;"></a></td>';
								}
							$file .= '</tr>';
						}
				}
				$file .= '</table>';	
			}				
        } 

        return $file;
    } 
	
	public function reportPartID_02($filters,$_SENDER,$passID)
    {
		//echo '<pre>'; echo print_r($_SENDER);
		
        $file = '';		
        //$crtID .= " AND DATE(All_Data.lcnoDT) <= '".date('Y-m-d',strtotime('-3 Months'))."' ";
		
		
		//echo $crtID;
		
        $SQL = "SELECT ID, code, full_name, desigID, companyID, wwcprno AS lcnoID, wwcprdt, wwcapdt FROM employee 
		WHERE status = 1 AND desigID In(9,208,209) AND wwcapdt <> '0000-00-00' ".$filters." ".$crtID." Order By wwcapdt ASC ";
		
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			
			if(is_array($this->rows_1) && count($this->rows_1) > 0)
			{
				$file .= '<br /><br />';
				
				$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
				$file .= '<thead><tr>';
				$file .= '<th colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>WWC Permit No Applied - Employee Report</strong></div></th>'; 
				$file .= '</tr></thead>';

				$file .= '<thead><tr>'; 
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Designation</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Licence Type</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Expiry Date</strong></div></th>';
				$file .= '<th style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>Applied Date</strong></div></th>';
				$file .= '<th colspan="2" style="border-bottom:#367FA9 2px solid;"><div align="left"><strong>&nbsp;</strong></div></th>';
				$file .= '</tr></thead>';
				if(is_array($this->rows_1) && count($this->rows_1) > 0)			
				{
					$srID = 1;
					foreach($this->rows_1 as $rows_1)
					{	
						if($rows_1['wwcapdt'] > date('Y-m-d',strtotime($rows_1['wwcprdt'].'-3 Months')))
						{
							$CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
							$DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
							
							$file .= '<tr>';
								$file .= '<td>'.$CP_Array[0]['title'].'</td>';
								$file .= '<td align="center">'.$rows_1['code'].'</td>';
								$file .= '<td >'.$rows_1['full_name'].'</td>';
								$file .= '<td >'.$DE_Array[0]['title'].'</td>';
								$file .= '<td align="center">WWC</td>';
								$file .= '<td align="center">'.$this->VdateFormat($rows_1['wwcprdt']).'</td>';
								$file .= '<td align="center">'.$this->VdateFormat($rows_1['wwcapdt']).'</td>';
								
								$file .= '<td align="center"><a title="Fill Date" class="fa fa-pencil fillLicenseNoDate" aria-sort="'.$rows_1['ID'].'" style="cursor:pointer;"></a></td>';								
								$file .= '<td align="center"><a title="Reset Date" class="fa fa-refresh undoLicenseNoDate" aria-sort="'.$rows_1['ID'].'" style="cursor:pointer;"></a></td>';
								
							$file .= '</tr>';
						}
					}
				}
				$file .= '</table>';
			}			
        } 

        return $file;
    } 

    public function reportPartID_3($filters,$_SENDER,$dateSTR)
    {
        $file = '';

        $SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 1 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="5" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>New Employee Report '.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';


                $file .= '<thead><tr>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';

                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="250"><div align="left"><strong>Designation';
                        /*$file .= '<select class="form-control" id="emp_fltID_8">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= $this->GET_Masters($_SENDER['fltID_8'],'12');
                        $file .= '</select>';*/
                    $file .= '</strong></div></th>';

                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Start Date</strong></div></th>';                
                $file .= '</tr></thead>';

                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                        $srID = 1;
                        foreach($this->rows_1 as $rows_1)
                        {
                            $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                            $DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';

                            $file .= '<tr>'; 
                                $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                                $file .= '<td align="center">'.$rows_1['code'].'</td>';
                                $file .= '<td >'.$rows_1['fname'].' '.$rows_1['lname'].'</td>';
                                $file .= '<td >'.$DE_Array[0]['title'].'</td>';
                                $file .= '<td align="center">'.$this->VdateFormat($rows_1['esdate']).'</td>';						
                            $file .= '</tr>'; 
                        }
                }
                $file .= '</table>';			
        } 

        return $file;
    } 

    public function reportPartID_4($filters,$_SENDER,$dateSTR)
    {
            $file = '';

            $SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 2 AND rleavingID <> 3 ".$filters." Order By code ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Ex Employee Report '.$dateSTR.'</strong></div></th>'; 
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Depot</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff Name</strong></div></th>';

                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Designation';
                        /*$file .= '<select class="form-control" id="emp_fltID_7">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= $this->GET_Masters($_SENDER['fltID_7'],'12');
                        $file .= '</select>';		*/
                    $file .= '</strong></div></th>';

                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>End Date</strong></div></th>';

                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Reason';
                        /*$file .= '<select class="form-control" style="width:180px;" id="emp_fltID_6">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= '<option value="1" '.($_SENDER['fltID_6'] == 1 ? 'selected="selected"' : '').'>Resigned</option>';
                        $file .= '<option value="2" '.($_SENDER['fltID_6'] == 2 ? 'selected="selected"' : '').'>Terminated</option>';
                        $file .= '<option value="3" '.($_SENDER['fltID_6'] == 3 ? 'selected="selected"' : '').'>Transferred</option>';
                        $file .= '<option value="4" '.($_SENDER['fltID_6'] == 4 ? 'selected="selected"' : '').'>Retired</option>';
                        $file .= '<option value="5" '.($_SENDER['fltID_6'] == 5 ? 'selected="selected"' : '').'>Deceased</option>';
                        $file .= '</select>';*/
                    $file .= '</strong></div></th>';


					$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong> Reason For Resignation</strong></div></th>';
					
                    $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Retention Period</strong></div></th>';                
                $file .= '</tr></thead>';

                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                        $srID = 1;  $sdateID = '';  $edateID = '';  $retentionID = '';
                        foreach($this->rows_1 as $rows_1)
                        {
                                $sdateID = $rows_1['esdate'];
                                $edateID = $rows_1['enddate'];

                                $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                                $DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
                                $RS_Array  = $rows_1['resonrgID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['resonrgID']." ") : '';



								/* ---- CALCULATE RETENTION PERIOD ---- */
								$date1 = strtotime($sdateID);
								$date2 = strtotime($edateID);
								$diff  = abs($date2 - $date1);
								

								/*$diff = abs($date2 - $date1);
								$years = floor($diff / (365*60*60*24));
								$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
								$days = floor(($diff - $years * 365*60*60*24 -  $months*30*60*60*24)/ (60*60*24)); 
								$hours = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24) / (60*60));
								$minutes = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24  - $hours*60*60)/ 60);
								$seconds = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));*/
								
                                $file .= '<tr>'; 
                                    $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                                    $file .= '<td align="center">'.$rows_1['code'].'</td>';
                                    $file .= '<td >'.$rows_1['fname'].' '.$rows_1['lname'].'</td>';
                                    $file .= '<td >'.$DE_Array[0]['title'].'</td>';
                                    $file .= '<td align="center">'.$this->VdateFormat($rows_1['enddate']).'</td>';
                                    $file .= '<td style="width:180px;" align="center">'.($rows_1['rleavingID'] == 1 ? 'Resigned' :($rows_1['rleavingID'] == 2 ? 'Terminated' :($rows_1['rleavingID'] == 3 ? 'Transferred' :($rows_1['rleavingID'] == 4 ? 'Retired' :($rows_1['rleavingID'] == 5 ? 'Deceased' : ''))))).'</td>';                                    
                                   

									$file .= '<td >'.$RS_Array[0]['title'].'</td>';
									
									 //$file .= '<td style="width:180px;" align="right">'.(sprintf("%d years, %d months, %d days", $years, $months, $days)).'</td>';
									 
									 //$file .= '<td style="width:180px;" align="center">'.(sprintf("%d.%d.%d", $years, $months, $days)).'</td>';
									 
									 $file .= '<td style="width:180px;" align="center">'.round((($diff / 86400) / 365),1).'</td>';
                                $file .= '</tr>';
                        }
                }
                $file .= '</table>';			
            } 
            return $file;
    }	
	
}
?>