<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
	
    $arrID = $login->select('filter_logsid',array("recID,filterTX"), " WHERE frmNM = 'DRIVER_SIGN_ON' AND userID = ".$_SESSION[$login->website]['userID']." Order By recID DESC LIMIT 1 ");
    
    if($arrID[0]['recID'] > 0)
    {
        $companyID = $arrID[0]['filterTX'];
    }
    else    {$companyID = (implode(',',$_REQUEST['filterID']) <> '' ? implode(',',$_REQUEST['filterID']) : $_SESSION[$login->website]['ecomID']);}

    echo '<aside class="right-side strech">';
    echo '<section class="content">';
	
    echo '<input type="hidden" id="SwappingStartEndTime" value="1" /><input type="hidden" id="SwappingRunningsTime" value="0" />';
    echo '<input type="hidden" id="allocationID" value="111" />';

    echo '<form method="get" id="SIGNON_form" action="'.$login->home.'profile_4.php" enctype="multipart/form-data" style="margin-top:-32px;">';
        echo '<div class="row" style="margin-top: 18px;margin-left: -5px;">'; 
        echo '<div class="col-xs-9" style="border-radius:5px; border:#D9006C 2px solid;padding: 10px;">';
            $explodeID = explode(",",$companyID);
            $Qry = $login->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID In(".$_SESSION[$login->website]['ecomID'].") Order By title ASC ");
            $Qry->execute();
            $login->resu = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<div class="row">';
            foreach($login->resu as $res)
            {
                echo '<div class="col-xs-2" style="padding-right:0px;">';
                echo '<input style="cursor:pointer;" type="checkbox" class="depotID" value="'.$res['ID'].'" '.(in_array($res['ID'],$explodeID) ? 'checked="checked"' : '' ).'> <b style="font-size:14px; color:#367FA9;">'.$res['title'].'</b>';
                echo '</div>';
            }
            echo '</div>'; 
        echo '</div>';

        if($casesID == 1)
        {
            echo '<div class="col-xs-1"><a class="btn bg-navy btn-flat margin getinfoID" id="getinfoID" style="border: #F56954 1px solid;" disabled="disabled">Update Swapping</a></div>';
        }
		
		echo '<div class="col-xs-1"><a target="_blank" href="'.$login->home.'forms/stfare.php?a='.$login->Encrypt('create').'" class="btn bg-navy margin btn-flat" style="margin-left:40px; border: #F56954 1px solid;">RADIO LOG</a></div>';
        echo '</div>';
        echo '<input type="hidden" value="'.$casesID.'" name="casesID" />';
    echo '</form>';

    echo '<div class="row"><br />';
        echo '<div class="col-md-12">';
        echo '<div class="nav-tabs-custom">';
            echo '<ul class="nav nav-tabs pull-right">';			
				echo '<li><a href="#tab_7-7" data-toggle="tab"><b style="color:#367FA9;">Assigned</b> <b style="color:#D9006C !important;" id="captionID_2">'.$singonsID['countID'].'</b></a></li>';
				echo '<li class="active"><a href="#tab_6-6" data-toggle="tab"><b style="color:#367FA9;">Pending</b> <b style="color:#D9006C !important;" id="captionID_1">'.$pendingID['countID'].'</b></a></li>';			
				echo '<li class="pull-left header"><i class="fa fa-th"></i> Driver SignOn <input type="hidden" id="tabID_1" /><input type="hidden" id="tabID_2" /><b style="margin-left:180px; color:#F20079;" id="ClockTimers"></b></li>';			
            echo '</ul>';

            echo '<div class="tab-content">';			
				echo '<div class="tab-pane active" id="tab_6-6" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;"><div id="redips-drag"></div></div>';
				echo '<div class="tab-pane" id="tab_7-7" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;"></div>';
            echo '</div>';
        echo '</div>';
        echo '</div>';
    echo '</div>';
    
    echo '</section>';
    echo '</aside>';
    
    include 'footer.php'; 
?> 