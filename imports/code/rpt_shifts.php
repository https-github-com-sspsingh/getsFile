<?PHP
class Reports extends CFunctions
{
    private	$tableName = '';
    private	$basefile  = '';

    function __construct()
    {	
            parent::__construct();		

            $this->basefile	  =	basename($_SERVER['PHP_SELF']);		
            $this->tableName     =	'';
    }

    public function ReportDisplay($filters)
    {
        if($filters['rtpyeID'] == 1)         {echo $this->reportPartID_1($filters);} 
        elseif($filters['rtpyeID'] == 2)     {echo $this->reportPartID_2($filters);}  
    }

    public function reportPartID_1($request)
    {
        $file = '';
        $filters = "";
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'dateID');}
        
        $SQL = "SELECT companyID FROM imp_shifts WHERE recID > 0 ".$filters." Group By companyID Order By companyID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $prID = (($request['fromID'] <> '') ? '-  (<b style="color:white;">From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th colspan="13" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Driver Signon Report '.$prID.'</strong></div></th>';
            $file .= '</tr></thead>';
            
            $file .= '<thead><tr>';
            $file .= '<th><div align="center"><strong>Sr. No</strong></div></th>';
            $file .= '<th><div align="center"><strong>SHIFT DATE</strong></div></th>';
            $file .= '<th><div align="center"><strong>SHIFT ID</strong></div></th>';
            $file .= '<th><div align="center"><strong>ON</strong></div></th>';
            $file .= '<th><div align="center"><strong>OFF</strong></div></th>';
            $file .= '<th><div align="center"><strong>HOURS</strong></div></th>';
            $file .= '<th><div align="center"><strong>ON</strong></div></th>';
            $file .= '<th><div align="center"><strong>OFF</strong></div></th>';
            $file .= '<th><div align="center"><strong>HOURS</strong></div></th>';
            $file .= '<th><div align="center"><strong>STAFF NAME</strong></div></th>';
            $file .= '<th><div align="center"><strong>STAFF CODE</strong></div></th>';
            $file .= '<th><div align="center"><strong>BUS NO</strong></div></th>';
            $file .= '<th><div align="center"><strong>SIGN-ON</strong></div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {
                    $arrayCM  = $rows_1['companyID'] > 0 ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                    
                     $file .=  '<tr>';
                        $file .=  '<td colspan="13" class="d-set" style="padding-left:35px;"><b style="color:#F56954;">Company Name : '.strtoupper($arrayCM[0]['title'].' - '.$arrayCM[0]['pscode']).'</b></td>';
                     $file .=  '</tr>';
                     
                     if($rows_1['companyID'] > 0)
                     {
                         $SQL_1 = "SELECT * FROM imp_shifts WHERE recID > 0 ".$filters." AND companyID = ".$rows_1['companyID']." Order By dateID, fielID_2 ASC ";
                         $Qry_1 = $this->DB->prepare($SQL_1);
                         $Qry_1->execute();
                         $this->rows_2 = $Qry_1->fetchAll(PDO::FETCH_ASSOC);
                         $busNO = ''; $empNM = '';
                         foreach($this->rows_2 as $rows_2)
                         {
                             if($rows_2['fielID_018'] > 0)
                             {
                                $arrayST = $rows_2['fielID_018'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['fielID_018']." ") : '';                                 
                                $empNM = strtoupper($arrayST[0]['fname'].' '.$arrayST[0]['lname']);
                             }
                             else
                             {
                                 $arrayST = $rows_2['fielID_013'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['fielID_013']." ") : '';                                 
                                 $empNM = strtoupper($arrayST[0]['fname'].' '.$arrayST[0]['lname']);
                             }
                             
                             if($rows_2['fielID_014'] > 0)
                             {
                                $arrayBUS = $rows_2['fielID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows_2['fielID_014']." ") : '';                                 
                                $busNO = strtoupper($arrayBUS[0]['busno'].' - '.$arrayBUS[0]['modelno'].' - '.$arrayBUS[0]['title']);
                             }
                             else   {$busNO = $rows_2['fielID_14'];}
                             
                             
                             
                             /// - '.$this->CalculateTimeHours($rows_2['fielID_2']).'
                             
                             
                            $file .= '<tr>';
                                $file .= '<td align="center">'.$srID++.'</td>';
                                $file .= '<td align="center"><b style="color:#367FA9;">'.(date('d-M-Y', strtotime($rows_2['dateID']))).'</b></td>';
                                $file .= '<td align="center"><b style="color:#367FA9;">'.$rows_2['fielID_1'].'</b></td>';
                                $file .= '<td align="center">'.$rows_2['fielID_2'].'</td>';
                                $file .= '<td align="center">'.$rows_2['fielID_3'].'</td>';
                                $file .= '<td align="center"><b style="color:#367FA9;">'.$rows_2['fielID_4'].'</b></td>';
                                $file .= '<td align="center">'.$rows_2['fielID_5'].'</td>';
                                $file .= '<td align="center">'.$rows_2['fielID_6'].'</td>';
                                $file .= '<td align="center"><b style="color:#367FA9;">'.$rows_2['fielID_7'].'</b></td>';
                                $file .= '<td>'.$empNM.'</td>';
                                $file .= '<td align="center"><b style="color:#367FA9;">'.($rows_2['fielID_18'] <> '' ? $rows_2['fielID_18'] : $rows_2['fielID_13']).'</b></td>';
                                $file .= '<td>'.$busNO.'</td>';
                                $file .= '<td align="center"><b style="color:#F56954;">'.$rows_2['singinID'].'</b></td>';
                            $file .= '</tr>';
                         }
                     }
                }
            }
            $file .= '</table>';			
        } 
        return $file;
    }
}
?>