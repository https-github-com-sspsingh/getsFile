<?PHP
class Reports extends SFunctions
{
    private	$tableName  = '';
    private	$basefile   = '';

    function __construct()
    {	
            parent::__construct();		

            $this->basefile	  =	basename($_SERVER['PHP_SELF']);		
            $this->tableName     =	'';
    }

	public function BuilderReport($filters)
    {
        echo $this->Generate_BuilderReport($filters,9);
    }
	
    public function ReportDisplay($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        
        if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'FOS.dateID');}

        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');
        
		$SQL = "SELECT FOS.ID, FOS.dateID, FOS.rptno, FOS.empID, FOS.description, FOS.mcomments, FOS.frmID, FOS.wrtypeID,  FOS.companyID, employee.fname, employee.lname, employee.code FROM (SELECT
		inspc.ID, inspc.dateID, inspc.rptno, inspc.empID, inspc.description, inspc.disciplineID, inspc.mcomments, 5 AS frmID, inspc.wrtypeID, inspc.companyID FROM inspc	LEFT JOIN employee ON employee.ID = inspc.empID
		WHERE inspc.disciplineID = 1 AND inspc.mcomments <> '' AND inspc.companyID In (".$filters['filterID'].") ".($filters['fltID_1'] > 0 ? " AND employee.systemID In(".$filters['fltID_1'].") " : "")." ".($filters['fltID_2'] > 0 ? " AND inspc.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT accident_regis.ID, accident_regis.dateID, accident_regis.refno, accident_regis.staffID,  accident_regis.description, accident_regis.disciplineID, accident_regis.mcomments, 3 AS frmID, accident_regis.wrtypeID, accident_regis.companyID FROM
		accident_regis LEFT JOIN employee ON employee.ID = accident_regis.staffID WHERE accident_regis.disciplineID = 1 AND accident_regis.mcomments <> '' AND accident_regis.companyID In (".$filters['filterID'].") ".($filters['fltID_1'] > 0 ? " AND employee.systemID In(".$filters['fltID_1'].") " : "")." ".($filters['fltID_2'] > 0 ? " AND accident_regis.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT complaint.ID, complaint.dateID, complaint.refno, complaint.driverID, complaint.description, complaint.disciplineID,  complaint.mcomments,  1 AS frmID,  complaint.wrtypeID, complaint.companyID
		FROM complaint LEFT JOIN employee ON employee.ID = complaint.driverID WHERE complaint.disciplineID = 1 AND complaint.mcomments <> '' AND complaint.companyID In (".$filters['filterID'].") ".($filters['fltID_1'] > 0 ? " AND employee.systemID In(".$filters['fltID_1'].") " : "")."  ".($filters['fltID_2'] > 0 ? " AND complaint.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT incident_regis.ID, incident_regis.dateID, incident_regis.refno, incident_regis.driverID,  incident_regis.description, incident_regis.disciplineID,  incident_regis.mcomments,  2 AS frmID, incident_regis.wrtypeID, incident_regis.companyID FROM incident_regis LEFT JOIN employee ON employee.ID = incident_regis.driverID WHERE incident_regis.disciplineID = 1 AND
		incident_regis.mcomments <> '' AND incident_regis.companyID In (".$filters['filterID'].") ".($filters['fltID_1'] > 0 ? " AND employee.systemID In(".$filters['fltID_1'].") " : "")." ".($filters['fltID_2'] > 0 ? " AND incident_regis.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT infrgs.ID, infrgs.dateID, infrgs.refno, infrgs.staffID, If(infrgs.description <> '', infrgs.description, master.title) AS description,  infrgs.disciplineID, infrgs.mcomments, 4 AS frmID, infrgs.wrtypeID, infrgs.companyID FROM infrgs LEFT JOIN employee ON employee.ID = infrgs.staffID LEFT JOIN master ON master.ID = infrgs.inftypeID WHERE
		infrgs.disciplineID = 1 AND infrgs.companyID In (".$filters['filterID'].") ".($filters['fltID_1'] > 0 ? " AND employee.systemID In(".$filters['fltID_1'].") " : "")." ".($filters['fltID_2'] > 0 ? " AND infrgs.wrtypeID = ".$filters['fltID_2'] : "")." UNION ALL SELECT mng_cmn.ID, mng_cmn.dateID, Concat('MN', '-', mng_cmn.ID) As vcodeID, mng_cmn.staffID, mng_cmn.description, mng_cmn.disciplineID, 
		mng_cmn.mcomments, 6 AS frmID, mng_cmn.wrtypeID, mng_cmn.companyID FROM mng_cmn LEFT JOIN employee ON employee.ID = mng_cmn.staffID WHERE mng_cmn.mcomments <> '' AND mng_cmn.companyID In (".$filters['filterID'].") ".($filters['fltID_1'] > 0 ? " AND employee.systemID In(".$filters['fltID_1'].") " : "")." ".($filters['fltID_2'] > 0 ? " AND mng_cmn.wrtypeID = ".$filters['fltID_2'] : "").") 
		AS FOS INNER JOIN employee ON employee.ID = FOS.empID WHERE FOS.rptno <> '' ".$return." ORDER BY FOS.dateID DESC";
        
        echo $this->reportPartID_1($return,$dateSTR,$_SENDER,$SQL);
    } 
	
    public function reportPartID_1($filters,$dateSTR,$_SENDER,$SQL)
    {
        $file = '';
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Manager Comments Register Report : '.$dateSTR.'</strong></div></th>'; 
            $file .= '</tr></thead>';


            $file .= '<thead><tr>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>DEPOT</strong></div></th>';                
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Date</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Ref No</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Comment Type</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Staff ID</strong></div></th>';

                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="200"><div align="left"><strong>Staff Name';
				if($_SENDER['rtpyeID'] == 1)
				{
                    $file .= '<select class="form-control" id="mngcmn_fltID_1">';
                    $file .= '<option value="0" selected="selected">-- Select --</option>';
                    $file .= $this->GET_Employees($_SENDER['fltID_1']," AND status = 1 AND desigID In (209,208) ");
                    $file .= '</select>';	
				}					
                $file .= '</strong></div></th>';

                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Description</strong></div></th>';
                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;"><div align="left"><strong>Manager Comments</strong></div></th>';

                $file .= '<th style="vertical-align: top; border-bottom:#367FA9 2px solid;" width="150"><div align="left"><strong>Warning Type';
				if($_SENDER['rtpyeID'] == 1)
				{
                        $file .= '<select class="form-control" id="mngcmn_fltID_2">';
                        $file .= '<option value="0" selected="selected">-- Select --</option>';
                        $file .= $this->GET_Masters($_SENDER['fltID_2'],'23');
                        $file .= '</select>';
				}
                $file .= '</strong></div></th>';                
            $file .= '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';                    
                    $WR_Array  = $rows_2['wrtypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['wrtypeID']." ") : '';

                    $file .= '<tr>';
                        $file .= '<td>'.$CP_Array[0]['title'].'</td>';
                        $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                        $file .= '<td align="center">'.$rows_2['rptno'].'</td>';
                        $file .= '<td align="center"><b>'.($rows_2['frmID'] == 1 ? 'Complaint' :($rows_2['frmID'] == 2 ? 'Incident' :($rows_2['frmID'] == 3 ? 'Accident' :($rows_2['frmID'] == 4 ? 'Infringement' :($rows_2['frmID'] == 5 ? 'Inspection' : 'Manager Comments'))))).'</b></td>';
                        
                        $file .= '<td align="center">'.$rows_2['code'].'</td>';
                        $file .= '<td >'.$rows_2['fname'].' '.$rows_2['lname'].'</td>';
                        
                        $file .= '<td>'.$rows_2['description'].'</td>';
                        $file .= '<td>'.$rows_2['mcomments'].'</td>';
                        $file .= '<td class="d-set">'.$WR_Array[0]['title'].'</td>';
                    $file .= '</tr>';

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
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'FOS.dateID');}            
        //$filters .= (!empty($request['driverID']) && ($request['driverID'] <> '') ? " AND employee.ID = ".$request['driverID'] : "");

        $SQL = "SELECT FOS.ID, FOS.dateID, FOS.rptno, FOS.empID, FOS.description, FOS.mcomments, FOS.frmID, FOS.wrtypeID, employee.fname, employee.lname, employee.code, FOS.companyID FROM (SELECT inspc.ID, inspc.dateID, inspc.rptno, inspc.empID, inspc.description,inspc.disciplineID, inspc.mcomments,  5 AS frmID, inspc.wrtypeID, inspc.companyID FROM inspc LEFT JOIN employee ON employee.ID = inspc.empID WHERE inspc.disciplineID = 1 AND ".($request['driverID'] > 0 ? "" : " AND inspc.companyID In(".$request['filterID'].") ")." inspc.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." 
		UNION ALL SELECT accident_regis.ID,accident_regis.dateID,  accident_regis.refno, accident_regis.staffID,accident_regis.description,  accident_regis.disciplineID, accident_regis.mcomments, 3 AS frmID,  accident_regis.wrtypeID, accident_regis.companyID FROM accident_regis LEFT JOIN employee ON employee.ID = accident_regis.staffID WHERE accident_regis.disciplineID = 1 AND accident_regis.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND accident_regis.companyID In(".$request['filterID'].") ")." UNION ALL SELECT complaint.ID, complaint.dateID, complaint.refno, complaint.driverID, complaint.description, complaint.disciplineID, complaint.mcomments,1 AS frmID, complaint.wrtypeID, complaint.companyID FROM complaint LEFT JOIN employee ON employee.ID = complaint.driverID WHERE complaint.disciplineID = 1 AND complaint.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND complaint.companyID In(".$request['filterID'].") ")." 
		UNION ALL SELECT incident_regis.ID, incident_regis.dateID, incident_regis.refno,incident_regis.driverID, incident_regis.description,  incident_regis.disciplineID, incident_regis.mcomments, 2 AS frmID, incident_regis.wrtypeID,incident_regis.companyID FROM incident_regis LEFT JOIN employee ON employee.ID = incident_regis.driverID WHERE incident_regis.disciplineID = 1 AND incident_regis.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND incident_regis.companyID In(".$request['filterID'].") ")." 
		UNION ALL SELECT infrgs.ID, infrgs.dateID, infrgs.refno,infrgs.staffID, If(infrgs.description <> '', infrgs.description, master.title) AS description, infrgs.disciplineID, infrgs.mcomments, 4 AS frmID, infrgs.wrtypeID, infrgs.companyID FROM infrgs LEFT JOIN master ON master.ID = infrgs.inftypeID LEFT JOIN employee ON employee.ID = infrgs.staffID WHERE infrgs.disciplineID = 1  ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND infrgs.companyID In(".$request['filterID'].") ")."
		UNION ALL SELECT mng_cmn.ID, mng_cmn.dateID, Concat('MN','-',mng_cmn.ID) as vcodeID, mng_cmn.staffID, mng_cmn.description,mng_cmn.disciplineID, mng_cmn.mcomments, 6 AS frmID, mng_cmn.wrtypeID, mng_cmn.companyID FROM mng_cmn LEFT JOIN employee ON employee.ID = mng_cmn.staffID WHERE mng_cmn.mcomments <> ''  ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND mng_cmn.companyID In(".$request['filterID'].") ")." ) AS FOS INNER JOIN employee ON employee.ID = FOS.empID WHERE FOS.rptno <> '' ".$filters." ORDER BY FOS.dateID DESC ";
         // echo $SQL;
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');

            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Driver Name/ID - Manager Comments Report  '.$prID.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Sr. No.</strong></div></th>';
            $file .= '<th><div align="center"><strong>Depot</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Comment Type</strong></div></th>';

            $file .= '<th><div align="center"><strong>Employee</strong></div></th>';
            $file .= '<th><div align="center"><strong>Warning Type</strong></div></th>';
            $file .= '<th><div align="center"><strong>Description</strong></div></th>';
            $file .= '<th><div align="center"><strong>Manager Comments</strong></div></th>';

            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
                    $WR_Array  = $rows_2['wrtypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['wrtypeID']." ") : '';
					$CM_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';

                    $file .= '<tr>';
                    $file .= '<td align="center">'.$srID++.'</td>';
					$file .= '<td align="center">'.$CM_Array[0]['title'].'</td>';
                    $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';                    
					$file .= '<td align="center">'.$rows_2['rptno'].'</td>';
                    $file .= '<td align="center"><b>'.($rows_2['frmID'] == 1 ? 'Complaint' :($rows_2['frmID'] == 2 ? 'Incident' :($rows_2['frmID'] == 3 ? 'Accident' :($rows_2['frmID'] == 4 ? 'Infringement' :($rows_2['frmID'] == 5 ? 'Inspection' : 'Manager Comments'))))).'</b></td>';
					
					$file .= '<td class="d-set">'.$rows_2['fname'].' '.$rows_2['lname'].' ('.$rows_2['code'].')</td>';
                    $file .= '<td class="d-set">'.$WR_Array[0]['title'].'</td>';
                    // $file .= '<td class="d-set">'.$rows_2['wrtypeID'].'</td>';
                    $file .= '<td width="400">'.($rows_2['description']).'</td>';
                    $file .= '<td width="400">'.($rows_2['mcomments']).'</td>';
                    $file .= '</tr>';
                }
            }
            $file .= '</table>';			
        }
        echo $file;
    } 
    
    public function reportPartID_3($request)
    {
        $file = '';
        $filters = "";
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'FOS.dateID');}            
        //$filters .= (!empty($request['driverID']) && ($request['driverID'] <> '') ? " AND employee.ID = ".$request['driverID'] : "");

        $SQL = "SELECT
  FOS.wrtypeID
FROM
  (SELECT
      FOS.wrtypeID
    FROM
        (SELECT
            inspc.ID,
            inspc.dateID,
            inspc.rptno,
            inspc.empID,
            inspc.description,
            inspc.disciplineID,
            inspc.mcomments,
            5 AS frmID,
            inspc.wrtypeID
          FROM
            inspc
          WHERE
            inspc.disciplineID = 1 AND
            inspc.mcomments <> '' AND
            inspc.companyID In (".$request['filterID'].")
          UNION ALL
          SELECT
            accident_regis.ID,
            accident_regis.dateID,
            accident_regis.refno,
            accident_regis.staffID,
            accident_regis.description,
            accident_regis.disciplineID,
            accident_regis.mcomments,
            3 AS frmID,
            accident_regis.wrtypeID
          FROM
            accident_regis
          WHERE
            accident_regis.disciplineID = 1 AND
            accident_regis.mcomments <> '' AND
            accident_regis.companyID In (".$request['filterID'].")
          UNION ALL
          SELECT
            complaint.ID,
            complaint.dateID,
            complaint.refno,
            complaint.driverID,
            complaint.description,
            complaint.disciplineID,
            complaint.mcomments,
            1 AS frmID,
            complaint.wrtypeID
          FROM
            complaint
          WHERE
            complaint.disciplineID = 1 AND
            complaint.mcomments <> '' AND
            complaint.companyID In (".$request['filterID'].")
          UNION ALL
          SELECT
            incident_regis.ID,
            incident_regis.dateID,
            incident_regis.refno,
            incident_regis.driverID,
            incident_regis.description,
            incident_regis.disciplineID,
            incident_regis.mcomments,
            2 AS frmID,
            incident_regis.wrtypeID
          FROM
            incident_regis
          WHERE
            incident_regis.disciplineID = 1 AND
            incident_regis.mcomments <> '' AND
            incident_regis.companyID In (".$request['filterID'].")
          UNION ALL
          SELECT
            infrgs.ID,
            infrgs.dateID,
            infrgs.refno,
            infrgs.staffID,
            infrgs.description,
            infrgs.disciplineID,
            infrgs.mcomments,
            4 AS frmID,
            infrgs.wrtypeID
          FROM
            infrgs
          WHERE
            infrgs.disciplineID = 1 AND
            infrgs.companyID In (".$request['filterID'].")
          UNION ALL
          SELECT
            mng_cmn.ID,
            mng_cmn.dateID,
            Concat('MN', '-', mng_cmn.ID) as vcodeID,
            mng_cmn.staffID,
            mng_cmn.description,
            mng_cmn.disciplineID,
            mng_cmn.mcomments,
            6 AS frmID,
            mng_cmn.wrtypeID
          FROM
            mng_cmn
          WHERE
            mng_cmn.mcomments <> '' AND
            mng_cmn.companyID In (".$request['filterID'].")) AS FOS
    WHERE
      FOS.rptno <> '' AND
      FOS.wrtypeID > 0 ".$filters.") AS FOS
WHERE
  FOS.wrtypeID > 0  
GROUP BY
  FOS.wrtypeID";
  
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');

            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Warning Type - Manager Comments Report  '.$prID.'</strong></div></th>'; 
            $file .= '</tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Sr. No.</strong></div></th>';
            $file .= '<th><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th><div align="center"><strong>Ref No</strong></div></th>';
            $file .= '<th><div align="center"><strong>Comment Type</strong></div></th>';
			$file .= '<th><div align="center"><strong>Warning Type</strong></div></th>';
            $file .= '<th><div align="center"><strong>Employee</strong></div></th>';
            $file .= '<th><div align="center"><strong>Description</strong></div></th>';
            $file .= '<th><div align="center"><strong>Manager Comments</strong></div></th>';

            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {
                    $WR_Array  = $rows_1['wrtypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['wrtypeID']." ") : '';

                    $file .= '<tr>';
                        $file .= '<td colspan="17" style="padding-left:35px;"><b>Warning Type : '.$WR_Array[0]['title'].'</b></td>';
                    $file .= '</tr>';
                    
                    if($rows_1['wrtypeID'] > 0)
                    {
                        $SQL_2 = "SELECT
FOS.ID,
FOS.dateID,
FOS.rptno,
FOS.empID,
FOS.description,
FOS.mcomments,
FOS.frmID,
FOS.wrtypeID,
employee.fname,
employee.lname,
employee.code
FROM
(SELECT
  inspc.ID,
  inspc.dateID,
  inspc.rptno,
  inspc.empID,
  inspc.description,
  inspc.disciplineID,
  inspc.mcomments,
  5 AS frmID,
  inspc.wrtypeID
FROM
  inspc
WHERE
  inspc.disciplineID = 1 AND
  inspc.mcomments <> ''
   AND inspc.companyID In (".$request['filterID'].")
UNION ALL
SELECT
  accident_regis.ID,
  accident_regis.dateID,
  accident_regis.refno,
  accident_regis.staffID,
  accident_regis.description,
  accident_regis.disciplineID,
  accident_regis.mcomments,
  3 AS frmID,
  accident_regis.wrtypeID
FROM
  accident_regis
WHERE
  accident_regis.disciplineID = 1 AND
  accident_regis.mcomments <> ''
  AND accident_regis.companyID In (".$request['filterID'].")
UNION ALL
SELECT
  complaint.ID,
  complaint.dateID,
  complaint.refno,
  complaint.driverID,
  complaint.description,
  complaint.disciplineID,
  complaint.mcomments,
  1 AS frmID,
  complaint.wrtypeID
FROM
  complaint
WHERE
  complaint.disciplineID = 1 AND
  complaint.mcomments <> ''
  AND complaint.companyID In (".$request['filterID'].")
UNION ALL
SELECT
  incident_regis.ID,
  incident_regis.dateID,
  incident_regis.refno,
  incident_regis.driverID,
  incident_regis.description,
  incident_regis.disciplineID,
  incident_regis.mcomments,
  2 AS frmID,
  incident_regis.wrtypeID
FROM
  incident_regis
WHERE
  incident_regis.disciplineID = 1 AND
  incident_regis.mcomments <> ''
  AND incident_regis.companyID In (".$request['filterID'].")
UNION ALL
SELECT
  infrgs.ID,
  infrgs.dateID,
  infrgs.refno,
  infrgs.staffID,
  If(infrgs.description <> '', infrgs.description, master.title) AS description,
  infrgs.disciplineID,
  infrgs.mcomments,
  4 AS frmID,
  infrgs.wrtypeID
FROM
  infrgs LEFT JOIN master ON master.ID = infrgs.inftypeID
WHERE
  infrgs.disciplineID = 1 
  AND infrgs.companyID In (".$request['filterID'].")
UNION ALL
SELECT
  mng_cmn.ID,
  mng_cmn.dateID,
  Concat('MN', '-', mng_cmn.ID) as vcodeID,
  mng_cmn.staffID,
  mng_cmn.description,
  mng_cmn.disciplineID,
  mng_cmn.mcomments, 6 AS frmID, mng_cmn.wrtypeID FROM mng_cmn WHERE mng_cmn.mcomments <> '' AND mng_cmn.companyID In (".$request['filterID'].") ) AS FOS INNER JOIN employee ON employee.ID = FOS.empID WHERE FOS.rptno <> '' ".$filters." AND FOS.wrtypeID = ".$rows_1['wrtypeID']." ORDER BY FOS.dateID DESC ";

                        $Qry_2 = $this->DB->prepare($SQL_2);
                        $Qry_2->execute();
                        $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                        if(is_array($this->rows_2) && count($this->rows_2) > 0)
                        {
                            $srID = 1;
                            foreach($this->rows_2 as $rows_2)
                            {
                                $INS_Array  = $rows_2['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
                                $CNT_Array  = $rows_2['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractID']." ") : '';
                                $SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
                                $STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
                                $INV_Array  = $rows_2['invstID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invstID']." ") : '';

                                $file .= '<tr>';
                                $file .= '<td align="center">'.$srID++.'</td>';
                                $file .= '<td align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                                $file .= '<td align="center">'.$rows_2['rptno'].'</td>';
                                $file .= '<td align="center"><b>'.($rows_2['frmID'] == 1 ? 'Complaint' :($rows_2['frmID'] == 2 ? 'Incident' :($rows_2['frmID'] == 3 ? 'Accident' :($rows_2['frmID'] == 4 ? 'Infringement' :($rows_2['frmID'] == 5 ? 'Inspection' : 'Manager Comments'))))).'</b></td>';
								$file .= '<td class="d-set">'.($WR_Array[0]['title']).'</td>';
                                $file .= '<td class="d-set">'.$rows_2['fname'].' '.$rows_2['lname'].' ('.$rows_2['code'].')</td>';
                                $file .= '<td width="400">'.($rows_2['description']).'</td>';
                                $file .= '<td width="400">'.($rows_2['mcomments']).'</td>';
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