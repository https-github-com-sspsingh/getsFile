<?PHP
class RFunctions extends FFunctions
{ 
	public function ExportReport_QueryBuilder($frmID,$fieldID,$companTX,$filterID,$criteriaTX,$passID = 0)
    {
        $inner = "";    $outer = "";    $prmID = "";    $joinID = "";   $parellID = ""; $sub_outer = ""; $sytaxID = "";
		
		$arrSYN  = ($frmID > 0 ? $this->select('rbuilder',array("sytaxID"), " WHERE sytaxID <> '' AND frmID = ".$frmID." ") : '');
		$sytaxID = $arrSYN[0]['sytaxID'] <> '' ? $arrSYN[0]['sytaxID'] : "";
		
        if(!empty($frmID))
        {
            $tableName = "";
            $Qry = $this->DB->prepare("SELECT * FROM rbuilder WHERE frmID In(".$frmID.") AND ID In(".$fieldID.") Order By filedCP ASC ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $srID = 1;
            foreach($this->rows as $rows)
            {
                $tableName = $rows['tableFR'];                
                $inner .= ($srID == 1 ? $tableName.".".$rows['filedNM'] : ', '.$tableName.".".$rows['filedNM']);				
                $srID++;
            }
        }
		
		$prmID .= $frmID == 1 ? " AND employee.status = 1 " : "";
		
		if($frmID == 1)
		{
			$SQL = "SELECT ".$tableName.".ID, ".$inner." FROM ".$tableName." ".$joinID." WHERE ".$tableName.".ID > 0 AND ".$tableName.".companyID In (".$companTX.") ".$prmID." ".$sytaxID." Order By ".$tableName.".companyID ASC  ";
		}
		else
		{
			$SQL = "SELECT ".$tableName.".ID, ".$inner." FROM ".$tableName." ".$joinID." WHERE ".$tableName.".ID > 0 AND ".$tableName.".companyID In (".$companTX.") ".$prmID." ".$sytaxID." ".$filterID." Order By ".$tableName.".companyID ASC  ";
		}
		
        return ($SQL);
    } 
	
	public function Report_QueryBuilder($frmID,$fieldID,$companTX,$filterID,$criteriaTX)
    {
        $inner = "";    $outer = "";    $prmID = "";    $joinID = "";   $parellID = ""; $sub_outer = ""; $sytaxID = "";
		
		$arrSYN  = ($frmID > 0 ? $this->select('rbuilder',array("sytaxID"), " WHERE sytaxID <> '' AND frmID = ".$frmID." ") : '');
		$sytaxID = $arrSYN[0]['sytaxID'] <> '' ? $arrSYN[0]['sytaxID'] : "";
		
        if(!empty($frmID))
        {
            $tableName = "";
            $Qry = $this->DB->prepare("SELECT * FROM rbuilder WHERE frmID In(".$frmID.") AND ID In(".implode(",",$fieldID).") Order By filedCP ASC ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $srID = 1;
            foreach($this->rows as $rows)
            {
                $tableName = $rows['tableFR'];                
                $inner .= ($srID == 1 ? $tableName.".".$rows['filedNM'] : ', '.$tableName.".".$rows['filedNM']);				
                $srID++;
            }
        }
		
		$prmID .= $frmID == 1 ? " AND employee.status = 1  " : "";
		
		if($frmID == 1)
		{
			$SQL = "SELECT ".$tableName.".ID, ".$inner." FROM ".$tableName." ".$joinID." WHERE ".$tableName.".ID > 0 AND ".$tableName.".companyID In (".$companTX.") ".$prmID." ".$sytaxID." Order By ".$tableName.".companyID ASC  ";
		}
		else
		{
			$SQL = "SELECT ".$tableName.".ID, ".$inner." FROM ".$tableName." ".$joinID." WHERE ".$tableName.".ID > 0 AND ".$tableName.".companyID In (".$companTX.") ".$prmID." ".$filterID." ".$sytaxID." Order By ".$tableName.".companyID ASC ";
		}
		
        return ($SQL);
    } 
}
?>