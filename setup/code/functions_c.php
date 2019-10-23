<?PHP
class CFunctions extends SFunctions
{
    function __construct()
    {	
        parent::__construct();
		
		$this->companyID = $_SESSION[$this->website]['compID'];
    }

	public function Emp_LicensesExpiryCounts($empID,$lctypeID)
	{
		$fieldNM = ($lctypeID == 'Drivers' 	   	     ? 'ddlcdt' 	   :($lctypeID == 'WWC' 		? 'wwcprdt' 
				  :($lctypeID == 'GasFittingNO' 	 ? 'gfpnexpDT'     :($lctypeID == 'AConRefNO'   ? 'acpnexpDT' 
				  :($lctypeID == 'WorkSafeDoggingNO' ? 'wsdpnexpDT'    :($lctypeID == 'ForliftLcNO' ? 'flpnexpDT' : ''))))));
				 
				 
		$critera = ($lctypeID == 'Drivers' 	   	     ? "" 	   
				  :($lctypeID == 'WWC' 				 ? "" 
				  :($lctypeID == 'GasFittingNO' 	 ? "AND gfpermitNO <> ''"     
				  :($lctypeID == 'AConRefNO'   	     ? "AND acpermitNO <> ''" 
				  :($lctypeID == 'WorkSafeDoggingNO' ? "AND wsdpermitNO <> ''"    
				  :($lctypeID == 'ForliftLcNO' 		 ? "AND flpermitNO <> ''" : " "))))));
				  
				  
		$return = 0;
		$return = $this->count_rows('employee', " WHERE ID = ".$empID." AND (".$fieldNM." <= '".date("Y-m-d", strtotime(date('Y-m-d').'+7Days'))."') ".$critera." ");
		$return = ($return > 0 ? 'style="color:red; font-weight:bold;"' : 0);
		
		return $return;
	}
	
	public function GET_LicensesExpiryCounts($daysID)
	{
		$return = array();
		$return['DLC'] = $this->count_rows('employee', " WHERE (ddlcdt <= '".date("Y-m-d", strtotime(date('Y-m-d').'+'.$daysID.'Days'))."') AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 ");
		$return['WWC'] = $this->count_rows('employee', " WHERE (wwcprdt <= '".date("Y-m-d", strtotime(date('Y-m-d').'+'.$daysID.'Days'))."') AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 AND desigID In(9,208,209) ");
		$return['GLC'] = $this->count_rows('employee', " WHERE gfpermitNO <> '' AND (gfpnexpDT <= '".date("Y-m-d", strtotime(date('Y-m-d').'+'.$daysID.'Days'))."') AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 AND desigID In(418) ");
		$return['ALC'] = $this->count_rows('employee', " WHERE acpermitNO <> '' AND (acpnexpDT <= '".date("Y-m-d", strtotime(date('Y-m-d').'+'.$daysID.'Days'))."') AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 AND desigID In(418) ");
		$return['WLC'] = $this->count_rows('employee', " WHERE wsdpermitNO <> '' AND (wsdpnexpDT <= '".date("Y-m-d", strtotime(date('Y-m-d').'+'.$daysID.'Days'))."') AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 AND desigID In(418) ");
		$return['FLC'] = $this->count_rows('employee', " WHERE flpermitNO <> '' AND (flpnexpDT <= '".date("Y-m-d", strtotime(date('Y-m-d').'+'.$daysID.'Days'))."') AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 AND desigID In(418) ");
		
		return $return;
	}
	
    public function GET_Shifts_Master($ID,$dateID,$categoryID)
    {
        $return = '';
		
        $SQL = "SELECT spare_regis_dtl.fieldID_1 FROM spare_regis INNER JOIN spare_regis_dtl ON spare_regis_dtl.ID = spare_regis.ID WHERE 
        spare_regis.dateID = '".$dateID."' AND spare_regis_dtl.forID = ".$categoryID." AND spare_regis.companyID In(".$this->companyID.")  
        Order By recID ASC ";
			
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            if($categoryID == 1)
            {
                $arrayID = $rows['fieldID_1'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows['fieldID_1']." ") : '';

                $crtID = ($rows['fieldID_1'] == $ID ? 'selected="selected"' : '');
                $return .= '<option '.$crtID.' value="'.$rows['fieldID_1'].'">
                '.strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'].' - ('.$arrayID[0]['phone']).')</option>';
            }

            if($categoryID == 2)
            {
                $arrayID = $rows['fieldID_1'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows['fieldID_1']." ") : '';

                $crtID = ($rows['fieldID_1'] == $ID ? 'selected="selected"' : '');
                $return .= '<option '.$crtID.' value="'.$rows['fieldID_1'].'">'.
                strtoupper($arrayID[0]['busno'].' - '.$arrayID[0]['modelno'].' - '.$arrayID[0]['title']).'</option>';
            }
        }
        return $return;
    }
}
?>