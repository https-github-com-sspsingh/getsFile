<?PHP
    include_once '../includes.php';

    $request    =    isset($_POST['request'])    ?   $_POST['request']   :   '' ;
    
	if($request == 'undoWWCAppliedDate')
    {
		extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
		
		$arr = array();
		$arr['wwcapdt'] = '0000-00-00';
		$on['ID'] = $ID;
		$Index->BuildAndRunUpdateQuery('employee',$arr,$on);
		$sendRET['statusID'] = 1;
		echo json_encode($sendRET);
    }
	
	if($request == 'UPDATE_ACC_APPLIED_DATE')
    {
		$sendRET = array();
		
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
		
		if($login->dateFormat($wwcapDT) <> '1970-01-01')
		{
			if($login->dateFormat($wwcapDT) <= date('Y-m-d'))
			{
				$arr = array();
				$arr['wwcapdt'] = $login->dateFormat($wwcapDT);
				$on['ID'] = $ID;
				if($Index->BuildAndRunUpdateQuery('employee',$arr,$on))
						{$sendRET['statusID'] = 1;}
				else	{$sendRET['statusID'] = 0;}
			}
			else	{$sendRET['statusID'] = 3;}
		}	else	{$sendRET['statusID'] = 3;}
		
		echo json_encode($sendRET);
    }
	
    if($request == 'UPDATE_AUDIT_TRIAL')
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

        if($tableNM <> '')
        {
            $update = array();
            foreach($_POST as $key=>$value)	
            {
                if($value <> '' && $key <> '')
                {
                    $arrayFT  = (trim($key) <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$key."' ")     : '');

                    if($arrayFT[0]['ftypeID'] == 4) 
                            {$update[$key] = $login->dateFormat(trim($value));}
                    else    {$update[$key] = trim($value);}
                }
            }
            $ons['ID'] = $ID;
            unset($update['request'],$update['tableNM'],$update['ID'],$update['frmID']); 
            if($login->BuildAndRunUpdateQuery($tableNM,$update,$ons))
                            {echo json_encode(array('success'=>1));}
            else	{echo json_encode(array('success'=>0));}
        }
    }
        
?>