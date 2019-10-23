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
	
	public function ReportDisplay($request)
	{ 
		$file = '';
		
		$filters = "";
		
		if($request['fromID'] <> '' && $request['toID'] <> '')
		{
			
		}
		else
		{
			$request['fromID'] = date('d/m/Y');
			$request['toID'] = date('d/m/Y');
		}
		
		$filters = "";
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
		
		$filters .= ($request['actionID'] == 1 ? " AND actionID <> '' " 
				   :($request['actionID'] == 2 ? " AND actionID = 'A' " 
				   :($request['actionID'] == 3 ? " AND actionID = 'E' " 
				   :($request['actionID'] == 4 ? " AND actionID = 'D' " : ""))));
		
		$SQL = "SELECT * FROM uslogs WHERE recID > 0 AND companyID In (".$_SESSION[$this->website]['compID'].") AND dateID <> '' ".$filters." ".($request['frmID'] > 0 ? " AND frmID = ".$request['frmID'] : "")." Order By recID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$prID ='-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>';
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$file .= '<table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Users Log Report '.$prID.'</strong></div></th>';
			$file .= '<thead><tr>';
			$file .= '<th><div align="center"><strong>Sr. No.</strong></div></th>';
			$file .= '<th><div align="center"><strong>Form Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Action</strong></div></th>';
			$file .= '<th><div align="center"><strong>Date</strong></div></th>';
			$file .= '<th><div align="center"><strong>Time</strong></div></th>';
			$file .= '<th><div align="center"><strong>User Name</strong></div></th>';
			$file .= '<th><div align="center"><strong>Employee Name (Code)</strong></div></th>';
			$file .= '<th><div align="center"><strong>Description</strong></div></th>';
			$file .= '<th><div align="center"><strong>Go To Page</strong></div></th>';
			$file .= '</tr></thead>';
			
			$frmNM = '';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;	$empID = '';	$actID = '';
				foreach($this->rows_1 as $rows_1)
				{
					$actID = ($rows_1['actionID'] == 'A' ? 'style="color:green;"' :($rows_1['actionID'] == 'E' ? 'style="color:blue;"' :($rows_1['actionID'] == 'D' ? 'style="color:red;"' : '')));

					$US_Array  = $rows_1['userID'] > 0  ? $this->select('users',array("*"), " WHERE ID = ".$rows_1['userID']." ") : '';
					$EM_Array  = $rows_1['empID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['empID']." ") : '';
					$FM_Array  = $rows_1['frmID'] > 0  ? $this->select('frmset',array("*"), " WHERE ID = ".$rows_1['frmID']." ") : '';

					$empID = ($EM_Array[0]['fname'] <> '' ? $EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].' ('.$rows_1['empCD'].')' : '');

					$frmNM = ($rows_1['frmID'] == 37 ? 'emp'	         :($rows_1['frmID'] == 38 ? 'sicklv' 
							:($rows_1['frmID'] == 39 ? 'prpermits'       :($rows_1['frmID'] == 40 ? 'cmplnt' 
							:($rows_1['frmID'] == 41 ? 'incident' 		 :($rows_1['frmID'] == 42 ? 'accident' 
							:($rows_1['frmID'] == 43 ? 'infrgs'          :($rows_1['frmID'] == 44 ? 'inspc' 
							:($rows_1['frmID'] == 45 ? 'mng_cmn' 		 : '')))))))));

					$file .= '<tr>';
						$file .= '<td align="center">'.$srID++.'</td>';
						$file .= '<td>'.$FM_Array[0]['title'].' </td>';								
						$file .= '<td align="center" '.$actID.'><b>'.($rows_1['actionID'] == 'A' ? 'NEW-ENTRY' :($rows_1['actionID'] == 'E' ? 'EDIT-ENTRY' :($rows_1['actionID'] == 'D' ? 'DELETE-ENTRY' : ''))).'</b></td>';
						$file .= '<td align="center">'.$this->VdateFormat($rows_1['dateID']).'</td>';
						$file .= '<td align="center">'.$rows_1['timeID'].' </td>';
						$file .= '<td>'.$US_Array[0]['username'].' ('.$US_Array[0]['first_name'].' '.$US_Array[0]['last_name'].')'.' </td>';
						$file .= '<td>'.$empID.'</td>';
						$file .= '<td>'.base64_decode($rows_1['descTX']).'</td>';
						
						if($rows_1['actionID'] == 'A' || $rows_1['actionID'] == 'E')
						{
							$file .= '<td align="center"><a target="_blank" class="fa fa-folder-open" 
							href="'.$this->home.'forms/'.$frmNM.'.php?i='.$this->Encrypt($rows_1['vouID']).'&a='.$this->Encrypt('create').'"></a></td>';
						}
						else
						{
							$file .= '<td>&nbsp;</td>';
						}
					$file .= '</tr>';
				}
			}
			$file .= '</table>';
		}
		echo $file;
	} 
	
	
}
?>