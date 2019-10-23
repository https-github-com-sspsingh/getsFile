<?PHP
class Login extends Functions
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function checkLogin()
	{
		if(isset($_SESSION[$this->website]['login']) == true)
		{
			$param = array();
			$this->Print_Redirect('','dashboard.php?');
		}	
	}
	
	public function Signin()
	{
		if($this->Form_Variables() == true)
		{
			extract($_POST);
			if($this->Not_Empty(array($username,$password)) == true)
			{
				$Query = $this->DB->prepare("SELECT * FROM users U  WHERE U.isActive = 1 AND U.username = :username && U.password = :password ");
				$Query->bindParam(':username',$username);
				$Query->bindParam(':password',md5($password));
				if($Query->execute())
				{
					$this->rows = $Query->fetchAll(PDO::FETCH_ASSOC);
					$this->numRows = count($this->rows);
					if($this->numRows == 1)
					{
						$_SESSION[$this->website]['login']  = true;
						$_SESSION[$this->website]['userNM'] = $this->rows[0]['username'];
						$_SESSION[$this->website]['fullNM'] = strtoupper($this->rows[0]['first_name'].' '.$this->rows[0]['last_name']);                                            
						$_SESSION[$this->website]['userID'] = $this->rows[0]['ID'];
						$_SESSION[$this->website]['userTY'] = $this->rows[0]['user_type'];
						$_SESSION[$this->website]['userRL'] = $this->rows[0]['uroleID'];
						$_SESSION[$this->website]['userPR'] = $this->rows[0]['prtypeID'];
						$_SESSION[$this->website]['userLT'] = $this->rows[0]['lgtypeID'];
						$_SESSION[$this->website]['ecomID'] = $this->rows[0]['companyID'];
						$_SESSION[$this->website]['yearID'] = (date('m') >= 3 ? date('Y') : (date('Y') - 0));
						$_SESSION[$this->website]['dashID'] = $this->rows[0]['dsnotID'];
						$_SESSION[$this->website]['cdysID'] = $this->rows[0]['dcdaysID'];
						
						if($this->rows[0]['ID'] > 0 && ($this->rows[0]['companyID'] <> '')) 
						{
							$userID = $this->rows[0]['ID'];
							$employeeID = $this->rows[0]['driverID'];
							
							$srID = 1;
							$this->delete('sessiondb', " WHERE userID = ".$userID." ");
							foreach((explode(",", $this->rows[0]['companyID'])) as $companyID)
							{
								if($companyID > 0)
								{
									$arrCM = $companyID > 0  ? $this->select('company',array("title,pscode,code"), " WHERE ID = ".$companyID." ") : '';
									$arrUS = $employeeID > 0 ? $this->select('employee',array("companyID"), " WHERE ID = ".$employeeID." ") : '';
									
									$Insert = array();
									$Insert['userID'] = $userID;
									$Insert['companyID'] = $companyID;
									$Insert['companyNM'] = $arrCM[0]['title'].' - '.$arrCM[0]['pscode'];
									$Insert['companyCD'] = $arrCM[0]['code'];
									$Insert['orderID'] = ($arrUS[0]['companyID'] == $companyID ? 1 : 0);
									$this->BuildAndRunInsertQuery('sessiondb',$Insert);
								}
							}
							
							$arrSESS = $this->select('sessiondb',array("*"), " WHERE userID = ".$userID." Order By orderID DESC LIMIT 1");
							$_SESSION[$this->website]['compID'] = $arrSESS[0]['companyID'];
							$_SESSION[$this->website]['compNM'] = $arrSESS[0]['companyNM'];
							$_SESSION[$this->website]['compCD'] = $arrSESS[0]['companyCD'];
							
							if($arrSESS[0]['companyID'] > 0)
							{
								$Qry = $this->DB->prepare("SELECT Group_Concat(ID) as scompanyID FROM company_dtls WHERE companyID = :cID ");
								$Qry->bindParam(':cID',$arrSESS[0]['companyID']);
								$Qry->execute();
								$this->rowsC = $Qry->fetch(PDO::FETCH_ASSOC);
								$_SESSION[$this->website]['scompID'] = $this->rowsC['scompanyID'];
							}
						}

						if($this->rows[0]['ID'] > 0 && ($this->rows[0]['driverID'] > 0)) 
						{
							$driverID = $this->rows[0]['driverID'];									
							$arrDS = $driverID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$driverID." ") : '';
							$arrDN = $arrDS[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$arrDS[0]['desigID']." ") : '';

							$_SESSION[$this->website]['empDS'] = $arrDS[0]['desigID'];
							$_SESSION[$this->website]['empDN'] = $arrDN[0]['title'];
							$_SESSION[$this->website]['empID'] = $driverID;
							$_SESSION[$this->website]['empNM'] = $arrDS[0]['full_name'].' - ('.$arrDS[0]['code'].')';
						}
						
						$AllCompID = $this->select('company',array("Group_Concat(company.ID) AS compID"), " WHERE ID > 0 ");
						$_SESSION[$this->website]['AllCompID'] = $AllCompID[0]['compID'];
					}
					else
					{
						$this->msg	= urlencode('Username or password is incorrect');
						$this->user	= urlencode($username);
						$param	= array('m'=>$this->msg,'u'=>$this->user );
						$this->Print_Redirect($param,'index.php?');						
					}
				}
			}
		}
	}
	
	public function NotLogin_Redi()
	{
		if(isset($_SESSION[$this->website]['login']) == true)		
		{
		}
		else    {echo "<script>window.location=\"index.php\"</script>";}
	}
	
	public function Login_Redi()
	{
		if(isset($_SESSION[$this->website]['login']))
		{
			$location = "dashboard.php";		
			echo "<script>window.location=\"$location\"</script>";
		}	
	}
}
?>