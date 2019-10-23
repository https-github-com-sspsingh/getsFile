<?PHP
    include_once '../includes.php';

    $ID       = isset($_POST['ID'])       ?	$_POST['ID']        :   ''  ;
    $request  = isset($_POST['request'])  ?	$_POST['request']   :   ''  ;
    $search   = isset($_GET['term'])      ?     $_GET['term']       :   ''  ;

    if($request == 'inputSearch')
    {
        /* start - date - prepare */
        $fdateID = date("d/m/Y", strtotime("-12 months"));
        $tdateID = date('d/m/Y');
        /* endss - date - prepare */

        if(!is_numeric($ID))
        {
			$SQL = "SELECT * FROM employee WHERE ID > 0 AND companyID IN (".$_SESSION[$login->website]['compID'].") AND full_name LIKE '%".$ID."%' AND status = 1 Order By full_name ASC LIMIT 10 ";
        }
        else
        {
			$SQL = "SELECT * FROM employee WHERE ID > 0 AND companyID IN (".$_SESSION[$login->website]['compID'].") AND code LIKE '%".$ID."%' AND status = 1 Order By full_name ASC LIMIT 10 ";
        }
		
		
		$fromID = '01/01/2000';//date('d/m/Y',strtotime(date('Y-m-d').'-1 Years'));
		$toID   = date('d/m/Y');

        $Qry = $Index->DB->prepare($SQL);
        $Qry->execute();
        while($row = $Qry->fetch(PDO::FETCH_ASSOC))
        {
			$Member_Name = ($row['full_name'].' - '.$row['code']);
			
			$Img = ($row['genderID'] == 164 ? $Index->home.'img/male.jpg' :($row['genderID'] == 165 ? $Index->home.'img/female.jpg' : $Index->home.'img/male.jpg')); 

			$b_username = '<b>'.$ID.'</b>';
			$b_email = '<b>'.$ID.'</b>';
			$final_username = str_ireplace($ID, $b_username, $Member_Name);
			$final_email = str_ireplace($ID, $b_email, $row['address_1']);

			echo '<div class="display_box select_specificID" align="left" aria-sort="'.$row['code'].'">';
			echo '<a target="blank" href="'.$login->home.'rpts/rpt_mngcmn.php?rtpyeID=2&driverID='.$row['systemID'].'&filterID[]='.$row['companyID'].'&fromID='.$fromID.'&toID='.$toID.'" style="text-decoration:none; padding:4px; font-size:12px; height:60px;">';
			
			echo '<img style="width:50px; height:50px; float:left; margin-right:6px;" src="'.$Img.'" />';
			echo '<span class="name">'.$final_username.'</span>&nbsp;<br/>'.$final_email.'<br/>';		
			echo '<span style="font-weight:bold; font-size:10px; color:#85469F;">'.$row['emailID'].' , '.$row['phone'].'</span></a></div>'; 
        } 
    } 		
?>