<?PHP
class Masters extends SFunctions
{
    private $tableName = '';
    private $basefile  = '';
    
    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'employee';
        $this->companyID	= $_SESSION[$this->website]['compID'];
        $this->frmID	    = '73';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }
    
    public function view($searchbyID)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {
            /* SEARCH BY  -  OPTIONS */
            $src = "";
            $src = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' " : " AND ".$this->tableName.".code LIKE '%".$searchbyID."%' ")) : (""));
            $query = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In (".$this->companyID.") ".$src." ORDER BY code DESC ");
            if($query->execute())
            {
                $this->rows = $query->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';
                echo '<thead><tr>';
                echo '<th>E. Code</th>';
                echo '<th>Employee Name</th>';
                echo '<th>Address</th>';
                echo '<th>Suburb</th>';
                echo '<th>PostCode</th>';
                echo '<th>Mobile No</th>';
                echo '<th align="center">Casual/Part Time/Full Time</th>';                
                echo '<th style="text-align:center;">E. Code <br /><b style="color:red;">(OLD)<b/></th>';
                echo '<th style="text-align:center;">E. Company <br /><b style="color:red;">(OLD)<b/></th>';
                echo '<th style="text-align:center;">Transfer<br /><b style="color:red;">(Date)<b/></th>';
                echo '</tr></thead>';
                $Start = 1;
                foreach($this->rows as $row)			
                {
                    $arrTE  = $row['ID'] > 0 ? $this->select('employee',array("*"), " WHERE refID = ".$row['ID']." ") : '';
                    $arrTC  = $arrTE[0]['companyID'] > 0 ? $this->select('company',array("title"), " WHERE ID = ".$arrTE[0]['companyID']." ") : '';
                    $arrSU  = $row['sid'] > 0 ? $this->select('suburbs',array("pscode,title"), " WHERE ID = ".$row['sid']." ") : '';
                    
                    $adds = '';
                    $adds .= $row['address_1'] <> '' ? $row['address_1'] : '';
                    $adds .= $row['address_2'] <> '' ? ' , '.$row['address_2'] : '';

                    $phon  = '';
                    $phon .= $row['phone'] <> '' ? $row['phone'] : '';
                    $phon .= ($row['phone_1'] && $row['phone']) ? ',   ' : '';
                    $phon .= $row['phone_1'] <> '' ? $row['phone_1'] : '';

                    if($arrTE[0]['ID'] > 0)
                    {
                        echo '<tr>'; 
                            echo '<td align="center">'.$row['code'].'</td>';
                            echo '<td>'.$row['full_name'].'</td>';
                            echo '<td>'.$adds.'</td>';
                            echo '<td>'.($row['sid'] > 0 ? $arrSU[0]['title'].'('.$arrSU[0]['pscode'].')' : '').'</td>';
                            echo '<td align="center">'.$row['pincode'].'</td>';
                            echo '<td>'.$phon.'</td>';                     
                            echo '<td>'.($row['casualID'] == 1 ? 'Full Time'  :($row['casualID'] == 2 ? 'Part Time' :($row['casualID'] == 3 ? 'Casual' : ''))).'</td>';
                            echo '<td align="center" style="color:green;"><b>'.$arrTE[0]['code'].'</b></td>';
                            echo '<td align="center" style="color:green;"><b>'.$arrTC[0]['title'].'</b></td>';
                            echo '<td align="center" style="color:green;"><b>'.$this->VdateFormat($arrTE[0]['refDT']).'</b></td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';			
            } 
        }
        else
        {
                echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">
                  Sorry....you dont have permission to view <b>Employee Master</b> Page</div></div>';
        }
    } 
}
?>