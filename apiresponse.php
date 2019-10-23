<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
	
	$typeID_3  = ($_SESSION[$login->website]['compID'] > 0 ? $login->select('api_mappings',array("*"), " WHERE dbaID = ".$_SESSION[$login->website]['compID']." AND typeID = 3 ")  : '');
	
    echo '<aside class="right-side strech">';
    echo '<section class="content">';    
    echo '<div class="row">'; 
    
    echo '<div class="col-xs-3">';
    echo '<label for="section" style="color:#00A65A;">Company Name</label>';
    echo '<select class="form-control" id="FLT_companyID" multiple="multiple">';
    $Qry = $login->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID In(".$_SESSION[$login->website]['ecomID'].") Order By title ASC ");
    $Qry->execute();
    $login->resu = $Qry->fetchAll(PDO::FETCH_ASSOC);    
    foreach($login->resu as $res)
    {
        echo '<option value="'.$res['ID'].'">'.$res['title'].'</option>';
    }
    echo '</select>';
    echo '</div>';
    
    echo '<div class="col-xs-2">';
    echo '<label for="section" style="color:#00A65A;">API Type</label>';
    echo '<select class="form-control" id="FLT_apiID">';
    echo '<option value="0" selected="selected" disabled="disabled">-- Select Option --</option>';
        echo '<option value="1">Incident</option>';
        echo '<option value="2">Offence</option>';
    echo '</select>';
    echo '</div>';
	
    echo '<div class="col-xs-2">';
    echo '<label for="section" style="color:#00A65A;">From Date</label>';
    echo '<input type="datable" id="FLT_fdateID" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter From Date"/>';
    echo '</div>';
    
    echo '<div class="col-xs-2">';
    echo '<label for="section" style="color:#00A65A;">To Date</label>';
    echo '<input type="datable" id="FLT_tdateID" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter To Date" />';
    echo '</div>';

    echo '<div class="col-xs-3">';
    echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
    echo '<input type="button" class="btn btn-flat btn-round btn-danger GET_API_RESPONSE" value="FILTER" />';	 
    
    echo '<label for="section" style="color:#00A65A;">&nbsp;</label>';
    echo '<input type="button" class="btn btn-flat btn-round btn-danger RESET_RESPONSE" value="RESET" />';
	
	echo '<label for="section" style="color:#00A65A;">&nbsp;</label>';
    echo '<input type="button" class="btn btn-flat btn-round btn-danger PRINT_RESPONSE" value="PRINT" />';
	
	echo '<label for="section" style="color:#00A65A;">&nbsp;</label>';
    echo '<input type="button" class="btn btn-flat btn-round btn-danger EXPORT_RESPONSE" value="EXPORT" />';
    echo '</div>'; 
	
    echo '<br />';
?>
	<div class="col-xs-12">
	<div class="nav-tabs-custom">
	<ul class="nav nav-tabs pull-right">                
		<li class="active"><a href="#gridID_1" data-toggle="tab"><b>API - SHEET</b></a></li>
		<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;" id="api_labelID">Data Sender - API request</b></li>
	</ul>
	<div class="tab-content no-padding">
		<div class="chart tab-pane active" id="gridID_1" style="position: relative; height: 600px; overflow-y: scroll; overflow-x: scroll;"></div>
	</div>
	</div>
	</div>
	
	<div class="col-xs-3"></div>  
	</section>
	</aside>
    </div>    
<?PHP 
    include 'footer.php';
?>
