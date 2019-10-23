<?PHP
class LFunctions extends AFunctions
{
    function __construct()
    {	
        parent::__construct();
        
        $this->companyID = $_SESSION[$this->website]['compID'];

        $this->HB = 'style="border:solid 1px #006400;"'; 
        $this->FB = 'style="border:solid 1px #006400; font-weight:200; font-style:inherit;"'; 
    }
	
	public function EXPORT_INCIDENT_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
    {
        $str = "";
		if($auditID <> '')
		{
		  $str = " AND incident_regis.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	= explode("/",$fd);
				list($tdt,$tm,$ty)	= explode("/",$td);

				$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}

			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '')                          $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
			else                                                $str .= " AND inc_statusID = 0 ";

			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);

			if($tsystemID <> '')
			{
				$src .= " AND (incident_regis.tsystemID In(".$tsystemID.") Or incident_regis.systemID In(".$tsystemID.")) ";
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);
				$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND incident_regis.refno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND incident_regis.companyID In (".$this->companyID.") ";
			}
			
			/* SET - CRITERIA */
			$str .= ($dashID == 1 ? " " :($dashID == 2 ? " AND Date_Add(incident_regis.dateID, INTERVAL 7 DAY) < '".date("Y-m-d")."' " :($dashID == 4 ? " AND sincID = 1 " :($dashID == 3 ? " AND Date_Add(incident_regis.dateID, INTERVAL 7 DAY) < '".date("Y-m-d")."' AND sincID = 1 " : ""))));
		}
		
        $SQL = "SELECT  incident_regis.* FROM incident_regis LEFT JOIN employee ON employee.ID = incident_regis.driverID WHERE incident_regis.ID > 0 ".$str." ".$src." ORDER BY incident_regis.refno DESC ";                  
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="12" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Incident Register</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
                echo '<th '.$this->HB.'>Security<br />Incidents</th>';
                echo '<th '.$this->HB.'>Ref No</th>';			
                echo '<th '.$this->HB.'>Date</th>';
                echo '<th '.$this->HB.'>Driver Code</th>';
                echo '<th '.$this->HB.'>Driver Name</th>';
                echo '<th '.$this->HB.'>Incident Location</th>';
                echo '<th '.$this->HB.'>Reported By</th>';
                echo '<th '.$this->HB.'>Incident Type</th>';
                echo '<th '.$this->HB.'>Police Ref</th>';
                echo '<th '.$this->HB.'>Description</th>';
                echo '<th '.$this->HB.'>Pending</th>';
                echo '<th '.$this->HB.'>Pending</th>';
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $row)
                {
                    $DR_Array  = ($row['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$row['driverID']." ") : '');
                    $IN_Array  = ($row['inctypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['inctypeID']." ")  : '');

                    echo '<tr>';
                    echo '<td '.$this->FB.' align="center">'.($row['sincID'] == 1 ? 'Yes' :($row['sincID'] == 2 ? 'No' :'')).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['dateID']).'</td>';
                    echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
                    echo '<td '.$this->FB.'>'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.'>'.$row['location'].'</td>';
                    echo '<td '.$this->FB.'>'.$row['reportby'].'</td>';
                    echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.$row['plrefno'].'</td>';
                    echo '<td '.$this->FB.'>'.trim($row['description']).'</td>'; 

                    echo '<td '.$this->FB.' align="center"><b style="color:red;">'.($row['actbyID'] == 0 ? 'Operations' : '').'</b></td>';
                    echo '<td '.$this->FB.' align="center"><b style="color:red;">'.($row['inc_statusID'] == 0 ? 'Admin' : '').'</b></td>';
                    echo '</tr>'; 

                }
            }
            echo '</table>';
        } 
    }
    
    public function EXPORT_EMPLOYEE_REGISTER($searchbyID,$auditID)
    {
        $src = "";
        $src = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' " : " AND employee.code LIKE '%".$searchbyID."%' ")) : ("AND status = 1 "));

		if($auditID <> '')
		{
			$src .= " AND employee.ID In(".$auditID.") ";
		}
			
        $Qry = $this->DB->prepare("SELECT * FROM employee WHERE ID > 0 AND companyID In (".$_SESSION[$this->website]['compID'].") ".$src." 
        ORDER BY code DESC ");
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Employee Register</strong></div></th>';
            echo '</tr></thead>';


            echo '<thead><tr>';
                echo '<th '.$this->HB.'>E. Code</th>';
                echo '<th '.$this->HB.'>First Name</th>';
                echo '<th '.$this->HB.'>Last Name</th>';
                echo '<th '.$this->HB.'>Address</th>';
                echo '<th '.$this->HB.'>Suburb</th>';
                echo '<th '.$this->HB.'>PostCode</th>';
                echo '<th '.$this->HB.'>Mobile No</th>';
		echo '<th '.$this->HB.'>Email ID</th>';
                echo '<th '.$this->HB.'>Designation</th>';
                echo '<th '.$this->HB.' align="center">Casual/Part Time/Full Time</th>';
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $row)
                {
                    $SUB_Array  = $row['sid'] > 0 ? $this->select('suburbs',array("*"), " WHERE ID = ".$row['sid']." ") : '';
                    $DSG_Array  = $row['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['desigID']." ") : '';

                    $adds = '';
                    $adds .= $row['address_1'] <> '' ? $row['address_1'] : '';
                    $adds .= $row['address_2'] <> '' ? ' , '.$row['address_2'] : '';

                    $phon  = '';
                    $phon .= ((!empty($row['phone']) && !empty($row['phone_1'])) ? $row['phone'].' , '.$row['phone_1'] : '');
                    $phon .= ((!empty($row['phone']) && empty($row['phone_1']))  ? $row['phone'] : '');
                    $phon .= ((empty($row['phone']) && !empty($row['phone_1']))  ? $row['phone_1'] : '');
                    
                    echo '<tr>';
                    echo '<td '.$this->FB.' align="center">'.$row['code'].'</td>';
                    echo '<td '.$this->FB.'>'.$row['fname'].'</td>';
                    echo '<td '.$this->FB.'>'.$row['lname'].'</td>';
                    echo '<td '.$this->FB.'>'.$adds.'</td>';
                    echo '<td '.$this->FB.'>'.($row['sid'] > 0 ? $SUB_Array[0]['title'] : '').'</td>';
                    echo '<td '.$this->FB.' align="center">'.$row['pincode'].'</td>';
                    echo '<td '.$this->FB.'>'.$phon.'</td>';
                    echo '<td '.$this->FB.'>'.$row['emailID'].'</td>';
                    echo '<td '.$this->FB.'>'.$DSG_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.($row['casualID'] == 1 ? 'Full Time'  :($row['casualID'] == 2 ? 'Part Time' :($row['casualID'] == 3 ? 'Casual' : ''))).'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        } 
    }

    public function EXPORT_COMMENTLINE_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
    {
        $str = "";
		if($auditID <> '')
		{
			$str .= " AND complaint.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
					list($fdt,$fm,$fy)  =   explode("/",$fd);
					list($tdt,$tm,$ty)  =   explode("/",$td);

					$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
					$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}

			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '' )     $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
			else                            $str .= " AND statusID = 2 ";

			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);

			if($tsystemID <> '')    {$src .= " AND (complaint.tsystemID In(".$tsystemID.") Or complaint.systemID In(".$tsystemID.")) ";}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);

				$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND complaint.refno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND complaint.companyID In (".$this->companyID.") ";
			}
			
			/* SET - CRITERIA */
			$passSTR = "";
			$passSTR = ($dashID == 1 ? "red" :($dashID == 2 ? "orange" :($dashID == 3 ? "green" : "")));
		}
		
        $SQL = "SELECT  complaint.* FROM complaint LEFT JOIN employee ON employee.ID = complaint.driverID WHERE complaint.ID > 0 ".($passSTR <> '' ? " AND complaint.trisID <= 0 " : "")." ".$str." ".$src." ORDER BY complaint.refno ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Customer Feedback Register</strong></div></th>';
            echo '</tr></thead>';


            echo '<thead><tr>';
                echo '<th '.$this->HB.'>Ref No</th>';
                echo '<th '.$this->HB.'>Comment Received On</th>';			
                echo '<th '.$this->HB.'>Driver Code</th>';
                echo '<th '.$this->HB.'>Driver Name</th>';
                echo '<th '.$this->HB.'>Due Date</th>';
                echo '<th '.$this->HB.'>Comment Type</th>';                
                echo '<th '.$this->HB.'>Fault/Not at Fault</th>';
                echo '<th '.$this->HB.'>Customer Feedback Reason</th>';
                echo '<th '.$this->HB.'>Suburb</th>';
                echo '<th '.$this->HB.'>Description</th>';
            echo '</tr></thead>';

			if($passSTR <> '')
			{
				if(is_array($this->rows_1) && count($this->rows_1) > 0)			
				{
					$Start = 1; $dueDate = '';  $daysID = 0; $uscountsID = 0;   $drnameID = '';   $colorID = '';
					foreach($this->rows_1 as $row)
					{
						$daysID = ((strtotime(date('Y-m-d', strtotime($row['cmdueDT']))) - strtotime(date('Y-m-d'))) / 86400);
						$colorID = $daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green');
						
						$DR_Array  = $row['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$row['driverID']." ") : '';
						$CM_Array  = $row['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['creasonID']." ") : '';
						$LT_Array  = $row['accID'] > 0 	 ? $this->select('master',array("*"), " WHERE ID = ".$row['accID']." ") : '';
						$SB_Array  = $row['suburb'] > 0  ? $this->select('suburbs',array("*"), " WHERE ID = ".$row['suburb']." ") : '';
						
						if(in_array($passSTR,(array($colorID))))
						{
							echo '<tr>';
							echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
							echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['serDT']).'</td>';
							echo '<td '.$this->FB.'>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : $DR_Array[0]['code']).'</td>';
							echo '<td '.$this->FB.'>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : $DR_Array[0]['fname'].' '.$DR_Array[0]['lname']).'</td>';
							
							echo '<td '.$this->FB.' align="center" style="color:'.($daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green')).'; font-weight:bold;">'.$this->VdateFormat($row['cmdueDT']).'</td>';
							echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
							
							echo '<td '.$this->FB.' align="center">'.($row['substanID'] == 1 && $row['faultID'] == 1 ? 'At Fault - Driver' 
											   :($row['substanID'] == 1 && $row['faultID'] == 2 ? 'At Fault - Engineering' 
											   :($row['substanID'] == 1 && $row['faultID'] == 3 ? 'At Fault - Operations' 
											   :($row['substanID'] == 1 && $row['faultID'] == 4 ? 'Not At Fault' 
											   :($row['substanID'] == 2 && $row['faultID'] == 4 ? 'Not Applicable' 
											   :($row['substanID'] == 2 && $row['faultID'] == 5 ? 'Not At Fault' : '')))))).'</td>';

							echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
							echo '<td '.$this->FB.'>'.$SB_Array[0]['title'].'</td>';
							echo '<td '.$this->FB.'>'.($row['description']).'</td>';
							echo '</tr>';
						} 
					}
				}
			}
			else
			{
				if(is_array($this->rows_1) && count($this->rows_1) > 0)			
				{
					$Start = 1; $dueDate = '';  $daysID = 0; $uscountsID = 0;   $drnameID = '';   $colorID = '';
					foreach($this->rows_1 as $row)
					{
						$dueDate = date('d-m-Y', strtotime($row['serDT'].'+7 Days'));
						$daysID = ((strtotime(date('Y-m-d', strtotime($row['serDT'].'+7 Days'))) - strtotime(date('Y-m-d'))) / 86400);
						$colorID = $daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green');
						
						$DR_Array  = $row['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$row['driverID']." ") : '';
						$CM_Array  = $row['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['creasonID']." ") : '';
						$LT_Array  = $row['accID'] > 0 	 ? $this->select('master',array("*"), " WHERE ID = ".$row['accID']." ") : '';
						$SB_Array  = $row['suburb'] > 0  ? $this->select('suburbs',array("*"), " WHERE ID = ".$row['suburb']." ") : '';
						 
						echo '<tr>';
						echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
						echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['serDT']).'</td>';
						echo '<td '.$this->FB.'>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : $DR_Array[0]['code']).'</td>';
						echo '<td '.$this->FB.'>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : $DR_Array[0]['fname'].' '.$DR_Array[0]['lname']).'</td>';
						
						echo '<td '.$this->FB.' align="center" style="color:'.($daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green')).'; font-weight:bold;">'.$dueDate.'</td>';
						echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
						
						echo '<td '.$this->FB.' align="center">'.($row['substanID'] == 1 && $row['faultID'] == 1 ? 'At Fault - Driver' 
										   :($row['substanID'] == 1 && $row['faultID'] == 2 ? 'At Fault - Engineering' 
										   :($row['substanID'] == 1 && $row['faultID'] == 3 ? 'At Fault - Operations' 
										   :($row['substanID'] == 1 && $row['faultID'] == 4 ? 'Not At Fault' 
										   :($row['substanID'] == 2 && $row['faultID'] == 4 ? 'Not Applicable' 
										   :($row['substanID'] == 2 && $row['faultID'] == 5 ? 'Not At Fault' : '')))))).'</td>';

						echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.'>'.$SB_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.'>'.($row['description']).'</td>';
						echo '</tr>'; 
					}
				}
			}
			
            echo '</table>';
        } 
    }
    
	public function EXPORT_HIZ_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
	{
		$str = "";
		if($auditID <> '')
		{
			$str .= " AND hiz_regis.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	= explode("/",$fd);
				list($tdt,$tm,$ty)	= explode("/",$td);

				$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}
			
			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '')     $str .= " AND DATE(rdateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 		
			else                            $str .= " AND statusID = 1 ";
			
			
			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);
			
			/* DASHBOARD - SEARCHING */
			$str .= $passSTR;
			
			/* SEARCH BY  -  OPTIONS */
			if($tsystemID <> '')
			{
				$src .= " AND (hiz_regis.tsystemID In(".$tsystemID.") Or hiz_regis.systemID In(".$tsystemID.")) ";
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);				
				$src .= ($retID == 2 ? "AND hiz_regis.refno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND hiz_regis.companyID In (".$this->companyID.") ";	
			}
			
			/* SET - CRITERIA */
			$str .= ($dashID == 1 ? " " :($dashID == 2 ? " AND Date_Add(hiz_regis.rdateID, INTERVAL 28 DAY) < '".date("Y-m-d")."' " : ""));
		}
		
		$SQL = "SELECT  hiz_regis.* FROM hiz_regis WHERE hiz_regis.ID > 0 ".$str." ".$src." ORDER BY From_UnixTime(hiz_regis.dateID) DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';            
			echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Hazard Register</strong></div></th>';
			echo '</tr></thead>';
			
			echo '<thead><tr>';
				echo '<th '.$this->HB.'>HZ No</th>';
				echo '<th '.$this->HB.'>Report Date</th>';
				echo '<th '.$this->HB.'>Reported By</th>';
				echo '<th '.$this->HB.'>Location</th>';
				echo '<th '.$this->HB.'>Hazard Type</th>';
				echo '<th '.$this->HB.' width="350">Description</th>';
			echo '</tr></thead>';

			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $row)
				{
					$arrRP  = $row['reportBY'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$row['reportBY']." ") : '';
					$arrMS  = $row['hztypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['hztypeID']." ") : '';

					echo '<tr>';
					echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['rdateID']).'</td>';
					echo '<td '.$this->FB.'>'.$arrRP[0]['full_name'].'</td>';
					echo '<td '.$this->FB.'>'.$row['location'].'</td>';
					echo '<td '.$this->FB.'>'.($arrMS[0]['title']).'</td>';					
					echo '<td '.$this->FB.'>'.($row['description']).'</td>';					
					echo '</tr>';
				}
			}
			echo '</table>';
		} 
	}
	
	public function EXPORT_SIR_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
	{
		$str = "";
		if($auditID <> '')
		{
			$str .= " AND sir_regis.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	= explode("/",$fd);
				list($tdt,$tm,$ty)	= explode("/",$td);

				$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}
			
			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '')     $str .= " AND DATE(issuetoDT) BETWEEN '".$fd."' AND '".$td."' ".$src;
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 		
			else                            $str .= " AND statusID = 1 ";
			
			
			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);
			
			/* DASHBOARD - SEARCHING */
			$str .= $passSTR;
				
			/* SEARCH BY  -  OPTIONS */
			if($tsystemID <> '')
			{
				$src .= " AND (sir_regis.tsystemID In(".$tsystemID.") Or sir_regis.systemID In(".$tsystemID.")) ";
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);				
				$src .= ($retID == 2 ? "AND sir_regis.refno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND sir_regis.companyID In (".$this->companyID.") ";	
			}
			
			/* SET - CRITERIA */
			$str .= ($dashID == 1 ? " " :($dashID == 2 ? " AND Date_Add(sir_regis.issuetoDT, INTERVAL 28 DAY) < '".date("Y-m-d")."' " : ""));
		}
		
		$SQL = "SELECT  sir_regis.* FROM sir_regis WHERE sir_regis.ID > 0 ".$str." ".$src." ORDER BY From_UnixTime(sir_regis.issuetoDT) DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';            
			echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>SIR Register</strong></div></th>';
			echo '</tr></thead>';
			
			echo '<thead><tr>';
				echo '<th '.$this->HB.'>Improvement No</th>';
				echo '<th '.$this->HB.'>Date</th>';
				echo '<th '.$this->HB.'>Procedure</th>'; 
				echo '<th '.$this->HB.'>SIR Type</th>';
				echo '<th '.$this->HB.'>Originator</th>';
				echo '<th '.$this->HB.' width="350">Description</th>';
			echo '</tr></thead>';

			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $row)
				{
					$arrRP  = $row['originatorID'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$row['originatorID']." ") : '';
					$arrMS  = $row['srtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['srtypeID']." ") : '';

					echo '<tr>';
					echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['issuetoDT']).'</td>';
					echo '<td '.$this->FB.'>'.$row['prcedure'].'</td>';
					echo '<td '.$this->FB.'>'.($arrMS[0]['title']).'</td>';			
					echo '<td '.$this->FB.'>'.$arrRP[0]['full_name'].' ('.($arrRP[0]['code']).')</td>';					
					echo '<td '.$this->FB.'>'.($row['description']).'</td>';					
					echo '</tr>';
				}
			}
			echo '</table>';
		} 
	}
	
	public function EXPORT_STFARE_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
	{
		$str = "";
		if(!empty($fd) || !empty($td))
		{
			list($fdt,$fm,$fy)	=	explode("/",$fd);
			list($tdt,$tm,$ty)	=	explode("/",$td);
		
			$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
			$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
		}
		
		if($auditID <> '')
		{
			$src .= " AND stfare_regis.ID In(".$auditID.") ";
		}
		else
		{
			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '' )		 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
			else                             $str .= " ";
		}
		
	
		$SQL = "SELECT  stfare_regis.* FROM stfare_regis  WHERE stfare_regis.ID > 0 ".$str." ".$src." ORDER BY stfare_regis.dateID ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';            
			echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>ST Fare Evasion Register</strong></div></th>';
			echo '</tr></thead>';
			
			echo '<thead><tr>';
				echo '<th '.$this->HB.'>Shift No</th>';
				echo '<th '.$this->HB.'>Date</th>';
				echo '<th '.$this->HB.'>Depot</th>';                    
				echo '<th '.$this->HB.'>Route No</th>';
				echo '<th '.$this->HB.'>Contractor</th>';
				echo '<th '.$this->HB.'>Location</th>';
				echo '<th '.$this->HB.'>Suburb</th>';
				echo '<th '.$this->HB.'>No of Fare Evaders</th>';
			echo '</tr></thead>';

			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $row)
				{
					$arrCM  = $row['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$row['companyID']." ") : '';
					$arrRT  = $row['routenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$row['routenoID']." ") : '';
					$arrCT  = $row['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['contractID']." ") : '';
					$arrSB  = $row['suburbID'] > 0 ? $this->select('suburbs',array("*"), " WHERE ID = ".$row['suburbID']." ") : '';

					echo '<tr>';
					echo '<td '.$this->FB.'>'.var_export($row['shiftNO'],true).'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td '.$this->FB.'>'.$arrCM[0]['title'].'</td>';
					echo '<td '.$this->FB.'>'.$arrRT[0]['codeID'].'</td>';
					echo '<td '.$this->FB.'>'.$arrCT[0]['title'].'</td>';
					echo '<td '.$this->FB.'>'.$row['locationTX'].'</td>';
					echo '<td '.$this->FB.'>'.$arrSB[0]['title'].' ('.$arrSB[0]['pscode'].')</td>';
					echo '<td '.$this->FB.'>'.$row['nooffare'].'</td>';				
					echo '</tr>';
				}
			}
			echo '</table>';
		} 
	}
		
    public function EXPORT_ACCIDENT_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
    {
        $str = "";
		if($auditID <> '')
		{
			$str .= " AND accident_regis.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	= explode("/",$fd);
				list($tdt,$tm,$ty)	= explode("/",$td);

				$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}
			
			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '')     $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 		
			else                            $str .= " AND progressID = 2 ";
			
			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);

			if($tsystemID <> '')
			{
				$src .= " AND (accident_regis.tsystemID In(".$tsystemID.") Or accident_regis.systemID In(".$tsystemID.")) ";
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);
				$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND accident_regis.refno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND accident_regis.companyID In (".$this->companyID.") ";
			}

			/* SET - CRITERIA */
			 $str .= ($dashID == 1 ? " AND Date_Add(accident_regis.dateID, INTERVAL 7 DAY) <= '".date("Y-m-d")."' AND (engdoneID <= 0 || oprdoneID <= 0) " 
					:($dashID == 2 ? " AND Date_Add(accident_regis.dateID, INTERVAL 7 DAY) <= '".date("Y-m-d")."' AND (engdoneID <= 0 || oprdoneID <= 0) " 
					:($dashID == 3 ? " AND progressID = 2 AND engdoneID = 1 AND oprdoneID = 1 " : "")));
		}
        $SQL = "SELECT  accident_regis.* FROM accident_regis LEFT JOIN employee ON employee.ID = accident_regis.staffID WHERE accident_regis.ID > 0 ".$str." ".$src." ORDER BY accident_regis.dateID DESC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="13" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Accident Register</strong></div></th>';
            echo '</tr></thead>';
            
            echo '<thead><tr>';
                echo '<th '.$this->HB.'>Ref No</th>';
                echo '<th '.$this->HB.'>Date</th>';
                echo '<th '.$this->HB.'>Accident Location</th>';
                echo '<th '.$this->HB.'>Suburb</th>';
                echo '<th '.$this->HB.'>Bus No</th>';
                echo '<th '.$this->HB.'>Accident Category</th>';
                echo '<th '.$this->HB.'>Driver Code</th>';
                echo '<th '.$this->HB.'>Driver Name</th>';
                echo '<th '.$this->HB.'>Description</th>';
                echo '<th '.$this->HB.'>Pending</th>';
                echo '<th '.$this->HB.'>Pending</th>';
                echo '<th '.$this->HB.'>Pending</th>';
                echo '<th '.$this->HB.'>Damage Cost</th>';
                echo '<th '.$this->HB.'>Driver Responsible</th>';                                
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $Start = 1; $dueDate = '';  $daysID = 0; $uscountsID = 0;   $drnameID = '';
                foreach($this->rows_1 as $row)
                {
                    $SUB_Array  = $row['suburb'] > 0 ? $this->select('suburbs',array("*"), " WHERE ID = ".$row['suburb']." ") : '';
                    $DR_Array  = $row['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$row['staffID']." ") : '';
                    $AC_Array  = $row['acccatID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$row['acccatID']." ") : '';

                    $dsID_1 = ('Ref No : '.$row['refno'].' , Date : '.$this->VdateFormat($row['dateID'])).'<br/>'.($row['invno'] <> '' ? 'Invoice No : '.$row['invno'] : '').($row['claimno'] <> '' ? ' Claim No : '.$row['claimno'] : '');
                    $dsID_2 = ('Bus No : '.$row['busID'].' , Driver : '.($DR_Array[0]['fname'].' '.$DR_Array[0]['lname']).'-'.$DR_Array[0]['code']);

                    echo '<tr>';
                    echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['dateID']).'</td>';
                    echo '<td '.$this->FB.'>'.$row['location'].'</td>';
                    echo '<td '.$this->FB.'>'.($row['suburb'] > 0 ? $SUB_Array[0]['title'].'('.$SUB_Array[0]['pscode'].')' : '').'</td>';
                    echo '<td '.$this->FB.'>'.$row['busID'].'</td>';
                    echo '<td '.$this->FB.'>'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : ($DR_Array[0]['code'])).'</td>';
                    echo '<td '.$this->FB.'>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : ($DR_Array[0]['fname'].' '.$DR_Array[0]['lname'])).'</td>';
                    echo '<td '.$this->FB.'>'.($row['description']).'</td>'; 
                    echo '<td '.$this->FB.' align="center">'.($row['oprdoneID'] == 1 ? '' : 'Operations').'</td>';
                    echo '<td '.$this->FB.' align="center">'.($row['oprdoneID'] == 1 ? '' : 'Engineering').'</td>';
                    echo '<td '.$this->FB.' align="center">'.($row['progressID'] == 1 ? '' : 'Admin').'</td>';
                    
                    echo '<td '.$this->FB.' align="right">'.($row['rprcost'] + $row['othcost']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($row['responsibleID'] == 1 ? 'Yes' :($row['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        } 
    }
    
    public function EXPORT_INFRINGMENT_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
    {
        $str = "";
		
		if($auditID <> '')
		{
			$str .= " AND infrgs.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	=	explode("/",$fd);
				list($tdt,$tm,$ty)	=	explode("/",$td);

				$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}

			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '' )	 $str .= " AND DATE(infrgs.dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
			else                            $str .= " AND dateID_4 = '0000-00-00' ";

			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);

			if($tsystemID <> '')
			{
				$src .= " AND (infrgs.tsystemID In(".$tsystemID.") Or infrgs.systemID In(".$tsystemID.")) ";			
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);
				$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND infrgs.refno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND infrgs.companyID In (".$this->companyID.") ";
			}

			/* SET - CRITERIA */
			$str .= $dashID == 2 ? " AND Date_Add(dateID_3, INTERVAL 7 DAY) <= '".date("Y-m-d")."' " : "";
		}
        $SQL = "SELECT  infrgs.* FROM infrgs LEFT JOIN employee ON employee.ID = infrgs.staffID WHERE infrgs.ID > 0 ".$str." ".$src." ORDER BY infrgs.dateID DESC "; 
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Infringement Register</strong></div></th>';
            echo '</tr></thead>';
            
            echo '<thead><tr>';
                echo '<th '.$this->HB.'>Date</th>';
                echo '<th '.$this->HB.'>Infringement No</th>';
                echo '<th '.$this->HB.'>Employee Name</th>';
                echo '<th '.$this->HB.'>Employee Code</th>';
                echo '<th '.$this->HB.'>Demerit Points Lost</th>';
                echo '<th '.$this->HB.'>Vehicle No</th>';
                echo '<th '.$this->HB.'>Infringement Type</th>';			
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $row)
                {
                    $DR_Array  = $row['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$row['staffID']." ") : '';
                    $CM_Array  = $row['inftypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['inftypeID']." ") : '';

                    echo '<tr>';
                    /*echo '<td '.$this->FB.' align="center">'.$Start++.'</td>';*/
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($row['refno'],true).'</td>';
                    echo '<td '.$this->FB.'>'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.($row['dplostID'] <> '' ? $row['dplostID'] : 0).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$row['vehicle'].'</td>';
                    echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
                        
                    echo '</tr>';
                }
            }
            echo '</table>';
        } 
    }
    
    public function EXPORT_INSPECTION_REGISTER($fd,$td,$searchbyID,$dashID,$auditID)
    {
        $str = "";
		if($auditID <> '')
		{
			$str .= " AND inspc.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy) = explode("/",$fd);
				list($tdt,$tm,$ty) = explode("/",$td);

				$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}

			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '' )	 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
			else                             $str .= " AND (statusID = 0 || statusID = 2)";

			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);

			if($tsystemID <> '')
			{
				$src .= " AND (inspc.tsystemID In(".$tsystemID.") Or inspc.systemID In(".$tsystemID.")) ";
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);
				$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND inspc.rptno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND inspc.companyID In (".$this->companyID.") ";
			}

			/* SET - CRITERIA */
			$str .= ($dashID == 1 ? " AND DateDiff(Date_Add(inspc.dateID, INTERVAL 7 DAY),CURDATE()) < 1 " :($dashID == 2 ? " AND Date_Add(dateID, INTERVAL 7 DAY) >= '".date("Y-m-d")."' " :($dashID == 3 ? " AND DateDiff(Date_Add(inspc.dateID, INTERVAL 7 DAY),CURDATE()) >= 3 " : "")));
		}
        $SQL = "SELECT  inspc.* FROM inspc LEFT JOIN employee ON employee.ID = inspc.empID WHERE inspc.ID > 0 ".$str." ".$src." ORDER BY inspc.rptno ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="12" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Infringement Register</strong></div></th>';
            echo '</tr></thead>';
            
            echo '<thead><tr>';
                echo '<th '.$this->HB.'>Report No</th>';
                echo '<th '.$this->HB.'>Report Date</th>';
                echo '<th '.$this->HB.'>Due Date</th>';
                echo '<th '.$this->HB.'>Driver Code</th>';
                echo '<th '.$this->HB.'>Driver Name</th>';
                echo '<th '.$this->HB.'>Inspection Result</th>';
                echo '<th '.$this->HB.'>Contractor</th>';
                echo '<th '.$this->HB.'>Contract</th>';
                echo '<th '.$this->HB.'>Service No</th>';
                echo '<th '.$this->HB.'>Service Info</th>';
                echo '<th '.$this->HB.'>Service Time Point</th>';
                echo '<th '.$this->HB.'>Bus No</th>';
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $row)
                {
                    $dueDate = date('d-m-Y', strtotime($row['dateID'].'+7 Days'));
                    $daysID = ((strtotime(date('Y-m-d', strtotime($row['dateID'].'+7 Days'))) - strtotime(date('Y-m-d'))) / 86400);

                    $EMP_Array  = $row['empID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$row['empID']." ") : '';
                    $INS_Array  = $row['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['insrypeID']." ") : '';
                    $CNT_Array  = $row['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['contractID']." ") : '';
                    $SRN_Array  = $row['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$row['servicenoID']." ") : '';
                    $STP_Array  = $row['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$row['srtpointID']." ") : '';
                    $CTN_Array  = $row['contractorID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['contractorID']." ") : '';

                    echo '<tr>';
                    echo '<td '.$this->FB.' align="center">'.var_export($row['rptno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($row['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center" style="color:'.($daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green')).'; font-weight:bold;">'.$dueDate.'</td>';
                    echo '<td '.$this->FB.'>'.$EMP_Array[0]['code'].'</td>';
                    echo '<td '.$this->FB.'>'.$EMP_Array[0]['fname'].' '.$EMP_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.'>'.$INS_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.$CTN_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.$CNT_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.$SRN_Array[0]['codeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$row['serviceinfID'].'</td>';
                    echo '<td '.$this->FB.'>'.$STP_Array[0]['fileID_1'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$row['busID'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        } 
    }
	
	public function EXPORT_MNGCOMMENTS_REGISTER($fd,$td,$searchbyID,$auditID)
	{
		$str = "";
		if($auditID <> '')
		{
			$str .= " AND mng_cmn.ID In(".$auditID.") ";
		}
		else
		{
			if(!empty($fd) || !empty($td))
				{
					list($fdt,$fm,$fy)	=	explode("/",$fd);
					list($tdt,$tm,$ty)	=	explode("/",$td);
					
					$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
					$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
				}
				
				/* START - DATE - SEARCHING */		
				if($fd <> '' && $td <> '')                          		 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
				else     $str .= " AND DATE(dateID) BETWEEN '".(date("Y-m-d", strtotime(date('Y-m-d').'-10Days')))."' AND '".date("Y-m-d")."' ";
				/* END - DATE - SEARCHING */
				
				/* START - SEARCH BY  -  OPTIONS */
				$src = "";
				$tsystemID  = $this->filter_employee_systemID($searchbyID);
				
				if($tsystemID <> '')
				{
					$src .= " AND (mng_cmn.tsystemID In(".$tsystemID.") Or mng_cmn.systemID In(".$tsystemID.")) ";
				}
				else
				{
					$retID = $this->CheckIntOrStrings($searchbyID);
					
					$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND mng_cmn.scodeID LIKE '%".$searchbyID."%'" : "");
					$src .= " AND mng_cmn.companyID In (".$this->companyID.") ";
				}
		}
        $SQL = "SELECT  mng_cmn.* FROM mng_cmn LEFT JOIN employee ON employee.ID = mng_cmn.staffID WHERE mng_cmn.ID > 0 ".$str." ".$src." ORDER BY mng_cmn.dateID DESC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';            
            echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Manager Comments Register</strong></div></th>';
            echo '</tr></thead>';
            
            echo '<thead><tr>';
                echo '<th '.$this->HB.'>Date</th>';
				echo '<th '.$this->HB.'>Driver Name</th>';
				echo '<th '.$this->HB.'>Driver ID</th>';
                echo '<th '.$this->HB.'>Interviewed By</th>';
                echo '<th '.$this->HB.'>Description</th>';
                echo '<th '.$this->HB.'>Manager Commens</th>';
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $row)
                {
                    $ST_Array  = $row['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$row['staffID']." ") : '';
					$DR_Array  = $row['invID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$row['invID']." ") : '';

                    echo '<tr>'; 
					echo '<td  '.$this->FB.'align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
					echo '<td '.$this->FB.'>'.$row['scodeID'].'</td>';
					echo '<td '.$this->FB.'>'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].' </td>';
					echo '<td '.$this->FB.'>'.$row['description'].'</td>';
					echo '<td '.$this->FB.'>'.$row['mcomments'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        } 
	}
	
   public function EXPORT_REPORT_EMPLOYEE($filters)
   {
		extract($filters);

		$return  = "";
		$return .= $filters['compID'] <> ''   ? " AND employee.companyID In (".$filters['compID'].") " : "";
		
		$fieldNO = '';
		$fieldNO = ($rtpyeID == 1 ? 'wwcprno'    :($rtpyeID == 2 ? 'ddlcno'	     :($rtpyeID == 3 ? 'gfpermitNO' 
				  :($rtpyeID == 4 ? 'acpermitNO' :($rtpyeID == 5 ? 'wsdpermitNO' :($rtpyeID == 6 ? 'flpermitNO' : ''))))));
				  
		$fieldDT = '';
		$fieldDT = ($rtpyeID == 1 ? 'wwcprdt'   :($rtpyeID == 2 ? 'ddlcdt'	   :($rtpyeID == 3 ? 'gfpnexpDT' 
				  :($rtpyeID == 4 ? 'acpnexpDT' :($rtpyeID == 5 ? 'wsdpnexpDT' :($rtpyeID == 6 ? 'flpnexpDT' : ''))))));
				 
		if(is_array($filters) && count($filters) > 0)   
		{
			if(!empty($fromID) && !empty($toID))
			{
				$return .= $this->Create_Reports_Date($filters,$fieldDT);
				$dateSTR = (($fromID <> '') ? '-  (<b>From : '.$fromID.' - To : '.$toID.')</b>' : '');
			}
			else
			{
				$return .= "And Date(".$fieldDT.") <= '".date('Y-m-d',strtotime('+60Days'))."'";
				$dateSTR = '-  (<b>Date : '.date('d/m/Y',strtotime('+60Days')).')</b>';
			}
		} 
		
		$arr = array();
		$arr['permitNO'] = $fieldNO;
		$arr['permitDT'] = $fieldDT;
		
		$return .= ($rtpyeID >= 3 && $rtpyeID <= 6 ? " AND desigID In(418) " :($rtpyeID == 2 ? " AND desigID In(9,208,209) " : ""));
		
		$passID = ''; 
		
		if($rtpyeID == 1)        {echo $this->EXPORT_REPORT_EMPLOYEE_1($return,$_SENDER,$dateSTR);} 
		else if($rtpyeID == 2)	 {echo $this->EXPORT_REPORT_EMPLOYEE_2($return,$_SENDER,$dateSTR,'Driver\'s Licence',$arr);}
		else if($rtpyeID == 3)	 {echo $this->EXPORT_REPORT_EMPLOYEE_2($return,$_SENDER,$dateSTR,'Gas Fitting Permit No',$arr);}
		else if($rtpyeID == 4)	 {echo $this->EXPORT_REPORT_EMPLOYEE_2($return,$_SENDER,$dateSTR,'A/Con-Refrigerant Licence No Renewals',$arr);}
		else if($rtpyeID == 5)	 {echo $this->EXPORT_REPORT_EMPLOYEE_2($return,$_SENDER,$dateSTR,'Work Safe Dogging Licence No',$arr);}
		else if($rtpyeID == 6)	 {echo $this->EXPORT_REPORT_EMPLOYEE_2($return,$_SENDER,$dateSTR,'Forklift Licence No',$arr);}
	}
	
	public function EXPORT_REPORT_EMPLOYEE_1($filters,$passID,$dateSTR)
    {
		$SQL = "Select * From employee Where status = 1 AND desigID In(9,208,209)  ".$filters." Order By code ASC "; 
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="12" class="knob-labels notices" style="font-weight:600; font-size:18px;"><div align="center"><strong>Working With Children Card Renewals : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>SURNAME</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>FIRST</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>WWC REQ</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>WWC NOTICE NO</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>WWC EXPIRY DATE</strong></div></th>';
			
			echo '<th '.$this->HB.'><div align="center"><strong>EMPLOYEE ADVISED DATE</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>EMPLOYEE AKNOWLEDGEMENT SIGNATURE</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>DATE APPLIED</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>DATE APPLICATION CONFIRMED</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>CONFIRMED BY <br />(Initial)</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>NEW EXPIRY DATE <br />(Taken From New Card)</strong></div></th>';
			
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                { 
                    echo '<tr>'; 
					echo '<td '.$this->FB.'>'.$rows_1['lname'].'</td>';
					echo '<td '.$this->FB.'>'.$rows_1['fname'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_1['code'].'</td>'; 
					echo '<td '.$this->FB.' align="center">Yes</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_1['wwcprno'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_1['wwcprdt']).'</td>';
					
                    echo '<td '.$this->FB.'></td>'; 
                    echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }
	}	

	public function EXPORT_REPORT_EMPLOYEE_2($filters,$passID,$dateSTR,$catptionTX,$arrID)
    {
		$SQL = "Select * From employee Where status = 1 ".$filters." Order By code ASC "; 
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="12" class="knob-labels notices" style="font-weight:600; font-size:18px;"><div align="center"><strong>'.$catptionTX.' Renewals : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>SURNAME</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>FIRST</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>'; 
            
			echo '<th '.$this->HB.'><div align="center"><strong>Extension Required</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>LICENSE NO</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>LICENSE EXPIRY DATE</strong></div></th>';
			
			echo '<th '.$this->HB.'><div align="center"><strong>EMPLOYEE ADVISED DATE</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>EMPLOYEE AKNOWLEDGEMENT SIGNATURE</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>NEW EXPIRY DATE</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>F EXT CURRENT</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>DATE CHECKED</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>CHECKED BY</strong></div></th>';
			
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                { 
                    echo '<tr>'; 
					echo '<td '.$this->FB.'>'.$rows_1['lname'].'</td>';
					echo '<td '.$this->FB.'>'.$rows_1['fname'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_1['code'].'</td>'; 
					echo '<td '.$this->FB.' align="center">'.$rows_1['ftextID'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_1[$arrID['permitNO']].'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_1[$arrID['permitDT']]).'</td>';
					
                    echo '<td '.$this->FB.'></td>'; 
                    echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					echo '<td '.$this->FB.'></td>';
					
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }  
    } 	
}
?>