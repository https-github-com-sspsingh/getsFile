<?PHP
class Masters extends SFunctions
{
    private $tableName  =   '';
    private $basefile   =  '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'employee';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		
        $this->frmID        = '73';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }
    
    public function createForm($searchbyID)
    {
        $this->action = 'add';
		
        echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
        echo '<div class="box-body" id="fg_membersite">';
		
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<div class="nav-tabs-custom">';
        echo '<ul class="nav nav-tabs">';
        echo '<li class="active"><a href="#tab_1" data-toggle="tab"><b>TransferedIn</b></a></li>';
        echo '<li><a href="#tab_2" data-toggle="tab"><b>TransferedOut</b></a></li>';
        echo '</ul>';
		
		echo '<div class="tab-content">';
			
			/* Employee TransferedIn Lists */
			echo '<div class="tab-pane active" id="tab_1">';
				echo '<div class="row" id="genMCH_FORM_1">';
				echo '<div class="col-xs-12">';
					echo '<table id="dataTableIn" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="10" style="background-color:#367FA9 !important; text-align:center !important; color:white;">
					Employee TransferedIn Lists <a onClick="exportToExcelIn()" style="border: white 1px solid;float: right;margin-top: -4px;margin-bottom: -2px;" class="btn btn-primary btn-flat btn-flat fa fa-download">&nbsp;&nbsp;Export Excel</a></th></tr></thead>';
					echo '<thead><tr>';					
					echo '<th style="text-align:center !important; color:#2F6F95;">E.Code</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Employee Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Address</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Suburb</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">PostCode</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile No</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Casual/Part Time/Full Time</th>';                
					echo '<th style="text-align:center !important; color:#2F6F95;">E. Code <br /><b style="color:red;">(OLD)<b/></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">E. Company <br /><b style="color:red;">(OLD)<b/></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Transfer<br /><b style="color:red;">(Date)<b/></th>';
					echo '</tr></thead>';
						$this->transferIn($searchbyID);
					echo '</table>';
				echo '</div>';       

				echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
				echo '</div>';
            echo '</div>';
			
			/* Employee TransferedOut Lists */
			echo '<div class="tab-pane" id="tab_2">';
				echo '<div class="row" id="genMCH_FORM_2">';
				echo '<div class="col-xs-12">';
					echo '<table id="dataTableOut" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="11" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Employee TransferedOut Lists 
					<a onClick="exportToExcelOut()" style="border: white 1px solid;float: right;margin-top: -4px;margin-bottom: -2px;" class="btn btn-primary btn-flat btn-flat fa fa-download">&nbsp;&nbsp;Export Excel</a></th></tr></thead>';

					echo '<thead><tr>';					
					echo '<th style="text-align:center !important; color:#2F6F95;">E. Code</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Employee Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Address</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Suburb</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">PostCode</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile No</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Casual/Part Time/Full Time</th>';                
					echo '<th style="text-align:center !important; color:#2F6F95;">E. Code <br /><b style="color:red;">(New)<b/></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">E. Company <br /><b style="color:red;">(New)<b/></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Log</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;"><b style="color:blue;">Transfer<br />Log<b/></th>';
					
					echo '</tr></thead>';
						$this->transferOut($searchbyID); 
					echo '</table>';
				echo '</div>';         

				echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
				echo '</div>';
            echo '</div>';  
			
        echo '</div>';
		
        echo '</div>';
        echo '</div>';
        echo '</div>';
		
        echo '</div>';
        echo '</form>';
    }
	
    public function transferIn($searchbyID)
    {
		$src = "";
		$src = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' " : " AND ".$this->tableName.".code LIKE '%".$searchbyID."%' ")) : (""));
			
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In (".$this->companyID.") ".$src." ORDER BY code DESC ");
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
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
    } 
	
	
	public function transferOut($searchbyID)
    {
		$src = "";
		$src = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' " : " AND ".$this->tableName.".code LIKE '%".$searchbyID."%' ")) : ("AND status = 2 AND refID > 0 "));
			
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In (".$this->companyID.") ".$src." ORDER BY code DESC ");
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		foreach($this->rows as $row)    
		{
			$arrTE  = ($row['refID'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$row['refID']." ") : '');
			$arrTC  = ($arrTE[0]['companyID'] > 0 ? $this->select('company',array("title"), " WHERE ID = ".$arrTE[0]['companyID']." ") : '');                    
			$arrSU  = $row['sid'] > 0 ? $this->select('suburbs',array("pscode,title"), " WHERE ID = ".$row['sid']." ") : '';
			
			$adds = '';
			$adds .= $row['address_1'] <> '' ? $row['address_1'] : '';
			$adds .= $row['address_2'] <> '' ? ' , '.$row['address_2'] : '';

			$phon  = '';
			$phon .= $row['phone'] <> '' ? $row['phone'] : '';
			$phon .= ($row['phone_1'] && $row['phone']) ? ',   ' : '';
			$phon .= $row['phone_1'] <> '' ? $row['phone_1'] : '';
			
			
			
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
				
				if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
				{
					if(($this->count_rows('uslogs', " WHERE vouID = ".$row['refID']." AND frmID = ".$this->frmID." ")) > 0)
					{
						echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['refID'].'_Employee Transfer Masters').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
					else	{echo '<td></td>';}
				}
				
				echo '<td align="center"><a class="fa fa-copy POPUP_uslogsID" aria-sort="'.('TR-LOG_'.$row['systemID'].'_Employee Transfer').'" style="text-decoration:none; cursor:pointer;"></a></td>';
			echo '</tr>';
		} 
    } 
}
?>