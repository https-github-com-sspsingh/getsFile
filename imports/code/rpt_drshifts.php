<?PHP
class Imports extends SFunctions
{
    private $basefile = '';	
    function __construct()
    {
        parent::__construct();
        $this->basefile = basename($_SERVER['PHP_SELF']);
    } 

    public function GoToInnserSheet($Status = 1)
    {
      extract($_POST);          //echo '<pre>';   echo print_r($_POST); exit;
      
      if(!empty($fdateID)  && ($optionID == 2))
      {
          $this->GeneratePastlyReportData($fdateID);
      }
	  
      else
      {
          $this->msg = urlencode('Please specify the required options. And Try Again...!!!');
          $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
          $this->Print_Redirect($param,$this->basefile.'?');
      }
    }
    
    public function GeneratePastlyReportData($fdateID)
    {
        if(($this->dateFormat($fdateID)) < date('Y-m-d'))
        {
            $this->msg = urlencode('Sorry! Back Date Transaction is Not Allowed.');
            $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }
        else
        {
            $fdateID = $this->dateFormat($fdateID);
            
            $SQL = "SELECT * FROM imp_shifts WHERE recID > 0 AND dateID = '".$fdateID."' AND companyID = ".$_SESSION[$this->website]['compID']." Order By recID ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                    echo '<th colspan="20" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Allocation Report ('.date('d - M - Y',strtotime($fdateID)).')</strong></div></th>'; 
                echo '</tr></thead>';

                echo '<thead><tr>';            
                echo '<th><div align="center"><strong>SHIFT ID</strong></div></th>';
                echo '<th><div align="center"><strong>ON</strong></div></th>';
                echo '<th><div align="center"><strong>EX Depot</strong></div></th>';
                echo '<th><div align="center"><strong>OFF</strong></div></th>';
                echo '<th><div align="center"><strong>HOURS</strong></div></th>';
                echo '<th><div align="center"><strong>ON</strong></div></th>';
                echo '<th><div align="center"><strong>OFF</strong></div></th>';
                echo '<th><div align="center"><strong>HOURS</strong></div></th>';
                echo '<th><div align="center"><strong>TOTAL</strong></div></th>';
                echo '<th><div align="center"><strong>WEEK</strong></div></th>'; 
                echo '<th><div align="center"><strong>DAY</strong></div></th>'; 
                echo '<th><div align="center"><strong>TYPE</strong></div></th>'; 
                echo '<th><div align="center"><strong>MEAL BREAK</strong></div></th>';
                echo '<th colspan="2"><div align="center"><strong>STAFF ID</strong></div></th>';                
                echo '<th colspan="2"><div align="center"><strong>BUS NUMBER</strong></div></th>';
                echo '<th><div align="center"><strong>BUS TYPE</strong></div></th>'; 
                echo '<th><div align="center"><strong>SHIFT COMMENTS</strong></div></th>'; 
                echo '<th><div align="center"><strong>OTHER INFO</strong></div></th>'; 

                echo '</tr></thead>';
                if(is_array($this->row) && count($this->row) > 0)			
                {
                    $srID = 1;  $row_colorID = '';
                    $empID = 0; $emp_reqID = 0;
                    $busID = 0; $bus_reqID = 0;
                    foreach($this->row as $rows)
                    {
                        $row_colorID = (empty($rows['fielID_14']) && empty($rows['fielID_014']) ? 'style="background:#F56954 !important; color:white !important;"' : '');
                        
                        $emp_reqID = ($rows['fielID_018'] > 0 ? 2 : 1);
                        $empID     = ($rows['fielID_018'] > 0 ? $rows['fielID_018'] : $rows['fielID_013']);

                        $bus_reqID = ($rows['fielID_014'] > 0 ? 2 : 1);
                        $busID     = ($rows['fielID_014'] > 0 ? $rows['fielID_014'] : $rows['fielID_14']);

                        $arrayID  = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';
                        $arrayBS  = $rows['fielID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows['fielID_014']." ") : '';

                        echo '<tr>';
                            echo '<td '.$row_colorID.' align="center"><b style="color:#367FA9;">'.$rows['fielID_1'].'</b></td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_2'].'</td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_0'].'</td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_3'].'</td>';
                            echo '<td '.$row_colorID.' align="center"><b style="color:#367FA9;">'.$rows['fielID_4'].'</b></td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_5'].'</td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_6'].'</td>';
                            echo '<td '.$row_colorID.' align="center"><b style="color:#367FA9;">'.$rows['fielID_7'].'</b></td>';
                            echo '<td '.$row_colorID.' align="center"><b style="color:#367FA9;">'.$rows['fielID_8'].'</b></td>';
                            echo '<td '.$row_colorID.' align="center"><b style="color:#367FA9;">'.$rows['fielID_9'].'</b></td>';

                            echo '<td '.$row_colorID.' align="center">'.(date('D',strtotime($rows['dateID']))).'</td>';

                            echo '<td '.$row_colorID.' align="center"><b style="color:#367FA9;">'.$rows['fielID_11'].'</b></td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_12'].'</td>';

                            /* EMPLOYEE  - DETAILS */
                            echo '<td '.$row_colorID.'>';
                                echo '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($emp_reqID == 2 ? '#F56954' : 'black').';" class="swipe_modelID" aria-sort="EMPLOYEE_'.$rows['recID'].'_'.$emp_reqID.'">'.(strtoupper($arrayID[0]['code'].'<br />'.$arrayID[0]['fname'].' '.$arrayID[0]['lname'])).'</a>';
                            echo '</td>';

                            echo '<td '.$row_colorID.'>';
                                if((int)$rows['fielID_018'] > 0)
                                {
                                    echo '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Staff" aria-sort="EMPLOYEE_'.$rows['dateID'].'_'.$emp_reqID.'_'.$empID.'_'.$rows['recID'].'"></a>';
                                }
                            echo '</td>';

                            /* BUS NO - DETAILS */
                            echo '<td align="center" '.$row_colorID.'>';
                                echo '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($bus_reqID == 2 ? '#F56954' : 'black').';" class="swipe_modelID" aria-sort="BUSES_'.$rows['recID'].'_'.$bus_reqID.'">';
                                    if((empty($rows['fielID_14']) && empty($rows['fielID_014'])))
                                    {
                                        echo '&nbsp; - &nbsp;';   
                                    }
                                    else
                                    {
                                        echo ($bus_reqID == 2 ? strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno']) : $rows['fielID_14']);
                                    }
                                echo '</a>';
                            echo '</td>';

                            echo '<td '.$row_colorID.'>';
                                if((int)$rows['fielID_014'] > 0)
                                {
                                    echo '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Staff" aria-sort="BUSES_'.$rows['dateID'].'_'.$bus_reqID.'_'.$busID.'_'.$rows['recID'].'"></a>';
                                }
                            echo '</td>';

                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_15'].'</td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_16'].'</td>';
                            echo '<td '.$row_colorID.' align="center">'.$rows['fielID_17'].'</td>';

                        echo '</tr>';
                    }
                }
                else
                {
                    echo '<tr><td align="center" colspan="19"><b style="color:red;">No Data Available for given date...</b></td></tr>';
                }
                echo '</table>';			
            }
        }
    }
}
?>