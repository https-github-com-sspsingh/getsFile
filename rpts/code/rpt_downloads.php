<?PHP
class Reports extends SFunctions
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
        extract($filters);

        $return  = "";
		
        if($rtpyeID >= 1 && $rtpyeID <= 6)
        {
            $return .= " AND employee.companyID In (".($filterID <> '' ? $filterID : $_SESSION[$this->website]['compID']).") ";
        }
		else
		{
			$return .= " AND employee.companyID In (".$_SESSION[$this->website]['compID'].") ";
		}
		
		$fieldNO = '';
		$fieldNO = ($rtpyeID == 1 ? 'wwcprno'    :($rtpyeID == 2 ? 'ddlcno'	   :($rtpyeID == 3 ? 'gfpermitNO' 
				  :($rtpyeID == 4 ? 'acpermitNO' :($rtpyeID == 5 ? 'wsdpermitNO' :($rtpyeID == 6 ? 'flpermitNO' : ''))))));
				  
		$fieldDT = '';
		$fieldDT = ($rtpyeID == 1 ? 'wwcprdt'   :($rtpyeID == 2 ? 'ddlcdt'	 :($rtpyeID == 3 ? 'gfpnexpDT' 
				  :($rtpyeID == 4 ? 'acpnexpDT' :($rtpyeID == 5 ? 'wsdpnexpDT' :($rtpyeID == 6 ? 'flpnexpDT' : ''))))));
		
		if(is_array($filters) && count($filters) > 0 && !empty($filters['fromID']) && !empty($filters['toID']))
		{
			$return .= $this->Create_Reports_Date($filters,$fieldDT);
		}
		else
		{
			$return .= "And Date(".$fieldDT.") <= '".date('Y-m-d',strtotime('+60Days'))."'";
		}
		
		$return .= ($rtpyeID >= 3 && $rtpyeID <= 6 ? " AND desigID In(418) " :($rtpyeID == 2 ? " AND desigID In(9,208,209) " : ""));
		
		$arr = array();
		$arr['permitNO'] = $fieldNO;
		$arr['permitDT'] = $fieldDT;
		
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($rtpyeID == 1)        {echo $this->reportPartID_1($return);} 
        else if($rtpyeID == 2)	{echo $this->reportPartID_2($return,'Driver\'s Licence',$arr);} 
		else if($rtpyeID == 3)	{echo $this->reportPartID_2($return,'Gas Fitting Permit No',$arr);}
		else if($rtpyeID == 4)	{echo $this->reportPartID_2($return,'A/Con-Refrigerant Licence No Renewals',$arr);} 
		else if($rtpyeID == 5)	{echo $this->reportPartID_2($return,'Work Safe – Dogging Licence No',$arr);} 
		else if($rtpyeID == 6)	{echo $this->reportPartID_2($return,'Forklift Licence No',$arr);} 
	}
	
    public function reportPartID_1($filters)
    {
		$SQL = "Select * From employee Where status = 1 AND desigID In(9,208,209) ".$filters." Order By code ASC "; 
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="12" class="knob-labels notices" style="font-weight:600; font-size:18px;">Working With Children Card Renewals</th>';
            $file .= '</tr></thead>';

            $file .= '<thead><tr>'; 
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>SURNAME</th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>FIRST</th>';
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>ID</th>'; 
            $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>WWC REQ</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>WWC NOTICE NO</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>WWC EXPIRY DATE</th>';
			
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>EMPLOYEE ADVISED DATE</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>EMPLOYEE AKNOWLEDGEMENT SIGNATURE</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>DATE APPLIED</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>DATE APPLICATION CONFIRMED</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>CONFIRMED BY <br />(Initial)</th>';
			$file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>NEW EXPIRY DATE <br />(Taken From New Card)</th>';
			
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                { 
                    $file .= '<tr>'; 
					$file .= '<td>'.$rows_1['lname'].'</td>';
					$file .= '<td>'.$rows_1['fname'].'</td>';
					$file .= '<td align="center">'.$rows_1['code'].'</td>'; 
					$file .= '<td align="center">Yes</td>';
					$file .= '<td align="center">'.$rows_1['wwcprno'].'</td>';
					$file .= '<td align="center">'.$this->VdateFormat($rows_1['wwcprdt']).'</td>';
					
                    $file .= '<td></td>'; 
                    $file .= '<td></td>';
					$file .= '<td></td>';
					$file .= '<td></td>';
					$file .= '<td></td>';
					$file .= '<td></td>';
					
                    $file .= '</tr>';
                }
            }
            $file .= '</table>';			
        }

        return $file;
    } 
 
    public function reportPartID_2($filters,$captionTX,$arrID)
    {
		$SQL = "Select * From employee Where status = 1 ".$filters." Order By code ASC "; 
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="12" class="knob-labels notices" style="font-weight:600; font-size:18px;"><div align="center"><strong>'.$captionTX.' Renewals</strong></div></th>';
            $file .= '</tr></thead>';

            $file .= '<thead><tr>'; 
            $file .= '<th><div align="center"><strong>SURNAME</strong></div></th>';
            $file .= '<th><div align="center"><strong>FIRST</strong></div></th>';
            $file .= '<th><div align="center"><strong>ID</strong></div></th>'; 
            
			$file .= '<th><div align="center"><strong>Extension Required</strong></div></th>';
			$file .= '<th><div align="center"><strong>LICENSE NO</strong></div></th>';
			$file .= '<th><div align="center"><strong>LICENSE EXPIRY DATE</strong></div></th>';
			
			$file .= '<th><div align="center"><strong>EMPLOYEE ADVISED DATE</strong></div></th>';
			$file .= '<th><div align="center"><strong>EMPLOYEE AKNOWLEDGEMENT SIGNATURE</strong></div></th>';
			$file .= '<th><div align="center"><strong>NEW EXPIRY DATE</strong></div></th>';
			$file .= '<th><div align="center"><strong>F EXT CURRENT</strong></div></th>';
			$file .= '<th><div align="center"><strong>DATE CHECKED</strong></div></th>';
			$file .= '<th><div align="center"><strong>CHECKED BY</strong></div></th>';
			
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                { 
                    $file .= '<tr>'; 
					$file .= '<td>'.$rows_1['lname'].'</td>';
					$file .= '<td>'.$rows_1['fname'].'</td>';
					$file .= '<td align="center">'.$rows_1['code'].'</td>'; 
					$file .= '<td align="center">'.$rows_1['ftextID'].'</td>';
					$file .= '<td align="center">'.$rows_1[$arrID['permitNO']].'</td>';
					$file .= '<td align="center">'.$this->VdateFormat($rows_1[$arrID['permitDT']]).'</td>';
					
                    $file .= '<td></td>'; 
                    $file .= '<td></td>';
					$file .= '<td></td>';
					$file .= '<td></td>';
					$file .= '<td></td>';
					$file .= '<td></td>';					
                    $file .= '</tr>';
                }
            }
            $file .= '</table>';			
        } 
        return $file;
    } 

   
}
?>