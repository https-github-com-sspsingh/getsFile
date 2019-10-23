<?PHP
class Functions extends DB
{
	public $Form = '';
	public $Post = array();
	public $validImageExtentions = array('jpeg','jpg','png','gif');	
	public $logoSize  = 100; 	
	public $imagePath = '';	
	public $videoSize = 25600; 	
	public $videoPath = '';
	
	public function GetFromAddress()
    {
        if(!empty($this->admin_email))	{return $this->admin_email;}

        $host = $_SERVER['SERVER_NAME'];

        $from ="noreply@$host";
        return $from;
    } 
	
	public function Word_Wraping($field,$len)
	{
		$return = '';
		if(!empty($field) || !empty($len))
		{
			$return = wordwrap($field,$len,"<br>\n");
		}
		return $return;
	}
	
	public function safeDisplay($value_name)
    {
        if(!is_array($_POST[$value_name]))
		{
			if(empty($_POST[$value_name]))
			{
				return '';
			}			
        	return htmlentities($_POST[$value_name]);
		}
		else	{return $_POST[$value_name];}
    }	
	
	public function printMessage($type, $msgTxt)
    {	
		$msg  = '';
        $msg .= '<div class="alert alert-'.$type.' alert-dismissable">';
		$msg .= '<i class="fa fa-check"></i>';
		$msg .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
		$msg .= '<b>'.$msgTxt.'</b>';
		$msg .= '</div>';
		echo $msg;
    }
	
	public function hashSSHA($password)
	{
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
	
	public function Form_Variables()
	{	
		if(isset($_POST[$this->Form]))
		{
			array_walk_recursive($_POST, 'self::clean');			
			$this->Post = $_POST;
			if(!empty($this->Post))		return true;			
			else						   return false;
		}
		else	{return false;}
	}
	
	private function clean(&$item) 
	{
		$item = strip_tags(trim($item));
	}
	
	protected function Print_Redirect($params,$URL = null)
	{		
		$this->paramString	=	'';
		
		if(is_array($params) && count($params) > 0) 
		{
			
			$this->counter = 0;
			foreach($params as $key=>$value) 
			{
				$this->counter++;
				$this->paramString	.=	($this->counter == 1) ?	''	:	'&';	
				$this->paramString	.=	$key . '=' . $this->Encrypt($value);
			}
			
		} else {
			$URL	=	substr($URL,0,(strlen($URL)-1));
			$params	=	'';
		}
		
		if($URL != null)
		{
			if($this->Not_Empty(array($params)) == true)
			{
				echo "<script>window.location=\"{$URL}{$this->paramString}\"</script>";
			}else
			{
				echo "<script>window.location=\"$URL\"</script>";
			}
		}
		$MSG   = $params['message'];
		$Class = $params['type'];
        echo '<span class="'.$Class.'">'.$MSG.'</span>';
	}

	protected function validateEmail($email) 
	{
 		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	protected function validateMobile($mobile) 
	{
 		return preg_match('/^\d{10,15}$/',$mobile);
	}

	protected function Slugs($str)
	{
		return preg_replace('/[^A-Za-z0-9\s-]/i','', $str); 
	}

	public function Not_Empty($arr)
	{
		$i = 0;
		foreach($arr AS $Value)
		{
			if(is_array($Value))
			{
			}else{$Value = trim($Value);}
			if(!empty($Value))
			{$i = 1;}else
			{$i = 0;break;}
		}
		if($i == 1)	{return true;}	else	{return false;}
	}

	public function Encrypt($string)
	{
		return $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->key), $string, MCRYPT_MODE_CBC, md5(md5($this->key))));
	}
	
	public function Decrypt($string)
	{
		$string = str_replace(" ","+",$string);
		return $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($this->key))), "\0");
	}
	 
	public function Ref_Match_Redi($url)
	{
		$Ref = $_SERVER['HTTP_REFERER'];
		if($this->Not_Empty(array($Ref)) == true )
		{
			if($Ref != $url)
			{
				$this->Print_Redirect('','',$Ref);
			}
		}
		else	{$this->Print_Redirect('','',$this->home);}
	}

	public function Duration_Format($Mins)
	{
		$Hour = floor($Mins / 60);
		$Hour = (strlen($Hour) < 2)?'0'.$Hour:$Hour;
		$Minutes = ($Mins % 60);
		$Minutes = (strlen($Minutes) < 2)?'0'.$Minutes:$Minutes;
		$Data = array();
		$Data['Hour'] = $Hour;
		$Data['Minutes'] = $Minutes;
		return $Data;
	} 	 

	public function VdateFormat($dateString)
	{
		if(!empty($dateString) && ($dateString <> '') && ($dateString <> '01-01-1970') && ($dateString <> '1970-01-01') && ($dateString <> '0000-00-00'))
			{$return = date('d/m/Y',strtotime($dateString));}
		else	
			{$return = '';}		
		return $return;		
	}
        
	public function dateFormat($dateString,$ret = 0)
	{
		if(!empty($dateString) && ($dateString <> '') && ($dateString <> '01-01-1970') && ($dateString <> '1970-01-01') && ($dateString <> '0000-00-00') && ($dateString <> '00-00-0000'))
		{
			list($day,$month,$year)	=	explode("/",$dateString);		
			$timestamp = strtotime(date($year.'-'.$month.'-'.$day));		
			$date = date('Y-m-d',$timestamp);		
			$return = ($ret == 1) ? $timestamp : $date;		
		}
		else	{$return = '00-00-0000';}		
		return $return;
	}
	
	public function uploadImage($file,$page,$sizeLimit = '')	
	{			
		if(!file_exists($file['tmp_name'])) return false;
		
		$this->logoSize	=	!empty($sizeLimit)	?	($sizeLimit*1024)	:	($this->logoSize*1024);
		list($fileName,$extn)=	explode(".",$file['name']);
		$this->ext	=	strtolower($extn);
		$this->size	=	$file['size'];		 
		$dir	=	$_SERVER['DOCUMENT_ROOT'].'/'.$this->sub_folder.'uploads/';
		if(!file_exists($dir)) 	mkdir($dir);		
		$sub_dir	=	$dir.$page.'/';
		if(!file_exists($sub_dir)) 	mkdir($sub_dir);
		$this->name	=	$fileName;
		$fileName     = str_replace(" ","-",str_replace("-","",$this->Slugs($this->name.md5(time())))).'.'.$this->ext;
		$dirPath	  = $sub_dir.$fileName;
		$storagePath  = '{site_name}uploads/'.$page.'/'.$fileName;
				
		if($this->size > $this->logoSize)
		{ return 0;}		
		else if(!in_array($this->ext,$this->validImageExtentions))
		{return 1;}		
		else
		{
			if(move_uploaded_file($file['tmp_name'],$dirPath))
			{					
				$this->imagePath	=	$storagePath;				
				return 2 ;			
			}
		}	
		return false;
	}
	
	public function manageAttachment($files,$page,$readyToStore = array(), $validates = array())
	{
		$attachments	=	array();
		$totalSize		=	0;
		
		foreach($files as $key=>$value)
		{
			foreach($value as $innerKey=>$innerValue)
			{
				$attachments[$innerKey][$key]	=	$innerValue;
				$totalSize	= ($key == 'size')	?	$totalSize + $innerValue	:	$totalSize + 0;		
			}	
		}
		
		if($this->checkSize('MB',$totalSize,'25'))		
		{
			foreach($attachments as $key=>$value)
			{
				$counter	=	0;
				
				$dir		=	$_SERVER['DOCUMENT_ROOT'].'/'.$this->sub_folder.'uploads/';
				if(!file_exists($dir)) 	mkdir($dir);
				
				$sub_dir	=	$dir.$page.'/';
				if(!file_exists($sub_dir)) 	mkdir($sub_dir);
				
				$fileName	=	str_replace(" ","-",$this->Slugs($value['name'].md5(time())).'.'.end(explode(".",$value['name'])));
				$dirPath	=	$sub_dir.$fileName;
				$storagePath=	$this->home.'uploads/'.$page.'/'.$fileName;
				
				if(!file_exists($dirPath))
				{
					if(move_uploaded_file($value['tmp_name'],$dirPath)){
						
						$attachments[$key]['storage_path']		=	$storagePath;
						$attachments[$key]['dir_path']			=	$dirPath;
						$attachments[$key]['update_name']		=	$fileName;
						unset($attachments[$key]['tmp_name']);
						$readyToStore[] 						=	$attachments[$key];
						$counter++;					
					} 
				} 
			} 				
		} 
		return $readyToStore;
	}
	
	private function checkSize($unit,$bytes,$limit)
	{
		$multiplier = ($unit == 'GB')	?	1073741824  : 0;
		$multiplier = ($unit == 'GB')	?	1048576	 : 0;
		$multiplier = ($unit == 'GB')	?	1024		: 0;		
		$limit	  = $limit * $multiplier;
		
		if($limit < $bytes ) return true;
		
		return false;
	}
	
	private function filesize_formatted($path)
	{
		$size = filesize($path);
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}
	
	public function getTableName($fileName)
	{
		list($tableName,$ext)	=	explode(".",$fileName);
		return	$tableName;
	}
	
	public function URLSlugs($str)
	{
		$result = preg_replace('/[^a-zA-Z0-9\s]/i', '',$str); 	// Remove non alphanum except whitespace
		$result = preg_replace('/^\s+|\s+$/','',$result);     	// Remove leading and trailing whitespace
		$result = preg_replace('/\s+/i', '-',$result);        	// Replace (multiple) whitespaces with a dash
		$result = strtolower($result);
			 
		return $result;
	} 
	
	public function regen_schedule($ID,$dateID)
	{
		$SQL = "SELECT * FROM w_shifts_grader WHERE reqID = ".$ID." AND sdateID = '".$dateID."' Order By recID ASC ";
		$Qry = $this->DB->prepare($SQL);
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($this->rows) &&(count($this->rows) > 0))
		{}
		else
		{
			$Qry = $this->DB->prepare("SELECT sdateID FROM w_shifts_grader WHERE reqID = ".$ID." ORDER BY sdateID DESC LIMIT 1");
			$Qry->execute();
			$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
                        $lastID = $this->rows['sdateID'];
			
			$Qry = $this->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$ID." AND sdateID = '".$lastID."' Order By recID ASC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$SH_1 = 0 ;
			foreach($this->rows as $row)
			{
				if($SH_1 > 0)	{}	else	{$SH_1 = $row['shiftID_1'];}
				if($SH_2 > 0)	{}	else	{$SH_2 = $row['shiftID_2'];}
				if($SH_3 > 0)	{}	else	{$SH_3 = $row['shiftID_3'];}
				if($SH_4 > 0)	{}	else	{$SH_4 = $row['shiftID_4'];}
				if($SH_5 > 0)	{}	else	{$SH_5 = $row['shiftID_5'];} 
				if($SH_6 > 0)	{}	else	{$SH_6 = $row['shiftID_6'];} 
				if($SH_7 > 0)	{}	else	{$SH_7 = $row['shiftID_7'];} 
				
				$SH = $this->select('w_shifts_grader',array("*"), " WHERE reqID = ".$ID." AND sdateID = '".$lastID."' 
				AND counterID = ".($row['counterID'] + 1)." ");
				
				$arr = array();
				$arr['counterID'] = $row['counterID'];
				$arr['reqID'] = $ID;
				$arr['sdateID'] = $dateID;
				$arr['edateID'] = date('Y-m-d',strtotime($dateID.'+7Days'));
				$arr['dayID'] = '7';
				$arr['vldateID'] = $row['vldateID'];
				$arr['segID'] = $row['segID'];
				$arr['empID'] = $row['empID'];
				$arr['rptID'] = '1';
				
				$arr['shiftID_1'] = $SH[0]['shiftID_1'] > 0 ? $SH[0]['shiftID_1'] : $SH_1;
				$arr['shiftID_2'] = $SH[0]['shiftID_2'] > 0 ? $SH[0]['shiftID_2'] : $SH_2;
				$arr['shiftID_3'] = $SH[0]['shiftID_3'] > 0 ? $SH[0]['shiftID_3'] : $SH_3;
				$arr['shiftID_4'] = $SH[0]['shiftID_4'] > 0 ? $SH[0]['shiftID_4'] : $SH_4;
				$arr['shiftID_5'] = $SH[0]['shiftID_5'] > 0 ? $SH[0]['shiftID_5'] : $SH_5;
				$arr['shiftID_6'] = $SH[0]['shiftID_6'] > 0 ? $SH[0]['shiftID_6'] : $SH_6;
				$arr['shiftID_7'] = $SH[0]['shiftID_7'] > 0 ? $SH[0]['shiftID_7'] : $SH_7;
				
				$arr['shiftcode'] = $SH[0]['shiftcode'];
				$arr['hoursID_1'] = $SH[0]['hoursID_1'];
				$arr['hoursID_2'] = $SH[0]['hoursID_2'];
				$arr['hoursID']   = $SH[0]['hoursID'];
				
				//echo '<pre>'; echo print_r($arr);	exit;
				$this->BuildAndRunInsertQuery('w_shifts_grader',$arr);
			}
		}
	}
}
?>