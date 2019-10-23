<?PHP
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';
    include 'code/'.basename($_SERVER['PHP_SELF']);
    $reports = new Reports();

    $basename  = basename($_SERVER['PHP_SELF']);	
    $headTitle = ' Inspection Report';

    $rpt_fieldID =   isset($_GET['rpt_fieldID'])	?	$_GET['rpt_fieldID']    : '';
    $filterID    =   isset($_GET['filterID'])	?	$_GET['filterID']    : '';
    $rtpyeID    =   isset($_GET['rtpyeID'])	?	$_GET['rtpyeID']     : '';
    $fromID     =   isset($_GET['fromID'])	?	$_GET['fromID']      : '';
    $toID       =   isset($_GET['toID'])	?	$_GET['toID']	     : '';
    
    $filters = array();
    foreach($_GET as $key=>$value)  {$filters[$key] = $value;}
    
    if($_GET['filterID'] <> '')
    { 
        $filters['filterID'] = implode(',',$_REQUEST['filterID']);
    }
    else if($_GET['fltID_7'] <> '')
    {
        $filters['filterID'] = $_GET['fltID_7'];
    }
    
    $requestID = $_GET['fltID_7'] <> '' ? $_GET['fltID_7'] : implode(',',$_REQUEST['filterID']);    
    $companyID = $requestID <> '' ? $requestID : $_SESSION[$login->website]['compID'];
?>
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side strech">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="<?=$login->home?>"><i class="fa fa-dashboard"></i>Home</a></li>
                       <li><a href="<?=$login->home.'rpts/'.$basename?>"><?=$headTitle?></a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
             	<div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <div class="box-tools">
                                    <div class="input-group">
                                    	<div class="input-group-btn">

<form action="<?=$login->home?>rpts/<?=$basename?>" method="get" >
<?PHP
$disabled = '';
$disabled = $rtpyeID > 0 ? '' : 'disabled="disabled"';

echo '<div class="row">'; 
    echo '<input type="hidden" id="FILTER_companyID" value="'.$companyID.'" />';
    
    echo '<div class="col-xs-12" style="border-radius:5px; border:#3C8DBC 2px solid; margin-left:10px; margin-right:10px; padding:10px; width:98.5%;">';
        $explodeID = explode(",",$companyID);
        $Qry = $login->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID In(".$_SESSION[$login->website]['ecomID'].") Order By title ASC ");
        $Qry->execute();
        $login->resu = $Qry->fetchAll(PDO::FETCH_ASSOC);
        echo '<div class="row">';
        foreach($login->resu as $res)
        {
            echo '<div class="col-xs-1" style="padding-right:0px;">';
            echo '<input type="checkbox" class="company_filterID" name="filterID[]" value="'.$res['ID'].'" '.(in_array($res['ID'],$explodeID) ? 'checked="checked"' : '' ).' > <b style="font-size:14px; color:#367FA9;">'.$res['title'].'</b>';
            echo '</div>';
        }
		
            echo '<div class="col-xs-1" style="padding-right:0px;">';
			echo '<input type="button" class="btn btn-success btn-flat btn-round" style="width: 99%;margin-left: 95px; font-weight: bold;" id="CheckAllCompany" value="Select All" />';
            echo '</div>';
			
        echo '</div>'; 
    echo '</div>';
    
    echo '<div class="col-md-2">';
        echo '<label for="section">Report Type</label><br />';
        echo '<select class="form-control" id="insp_fltID_rtpyeID" name="rtpyeID">';
        echo '<option value="0" selected="selected" disabled="disabled">-- Select Report Type --</option>';
        //echo '<option value="1" '.($rtpyeID == 1 ? 'selected="selected"' : '').'>Transactions Data</option>';
		echo '<option value="2" '.($rtpyeID == 2 ? 'selected="selected"' : '').'>Inspected By</option>';
		echo '<option value="3" '.($rtpyeID == 3 ? 'selected="selected"' : '').'>Fine Report</option>';
		echo '<option value="4" '.($rtpyeID == 4 ? 'selected="selected"' : '').'>Raw Data Report</option>';
        echo '</select>';
    echo '</div>';
	
    echo '<div class="col-xs-4" id="rpt_fields_divID" '.($rtpyeID == 4 ? '' : 'style="display:none;"').'>';
		echo '<label for="section">Report Fields</label><br />';
		echo '<select name="rpt_fieldID[]" multiple="multiple" class="3col active">';
		$GET_rpt_fieldID = explode(",",(implode(",",$rpt_fieldID)));
		$Qry = $login->DB->prepare("SELECT * FROM rbuilder WHERE frmID In(8) Order By srID ASC ");								  
		if($Qry->execute())	
		{	
		  $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		  foreach($login->rows as $row)
		  {
			  echo '<option value="'.$row['ID'].'" '.(in_array($row['ID'],$GET_rpt_fieldID) ? 'selected="selected"' : '').'>'.$row['filedCP'].'</option>';
		  }		  
		}
		echo '</select>';
    echo '</div>';
	
	echo '<div class="col-md-3">';
		echo '<label for="section">Select The Date Range</label><br />';
		echo '<button class="btn btn-default pull-right form-control" id="daterange-btn">';
		echo '<i class="fa fa-calendar"></i> Date Range Picker &nbsp;';
		echo '<i class="fa fa-caret-down"></i>';
		echo '</button>';
    echo '</div>';
	
	echo '<input type="hidden" name="fromID" id="fromID" value="'.($fromID <> '' ? $fromID : date('d/m/Y', strtotime('first day of last month'))).'" />';
    echo '<input type="hidden" name="toID" id="toID" value="'.($toID <> '' ? $toID : date('d/m/Y', strtotime('last day of last month'))).'" />';
	
	
	echo '<div class="col-md-3">';
		echo '<label for="section">&nbsp;</label><br />';
	    echo '<input type="submit" name="Submit" class="btn btn-danger btn-flat fa fa-filter" value="&nbsp;Filter" />';
		
		echo '<label for="section">&nbsp;</label>';
            echo '<a href="'.$login->home.'rpts/'.$basename.'" class="btn btn-danger btn-flat fa fa-refresh">&nbsp;&nbsp;Clear</a>';

            echo '<label for="section">&nbsp;</label>';
            echo '<a onClick="PrintPage()" class="btn btn-danger btn-flat fa fa-print">&nbsp;&nbsp;Print</a>';
		
        $urlID = '?s=REPORT_INSPECTION&rtpyeID='.$rtpyeID.'&fromID='.$_GET['fromID'].'&toID='.$_GET['toID'].'&compID='.$companyID.'&rpt_fieldID='.implode(",",$rpt_fieldID);
        if($rtpyeID >= 1)
        {
            //for($srID = 1; $srID <= 3; $srID++) 	{$urlID .= '&fltID_'.$srID.'='.$_GET['fltID_'.$srID];}
            
            echo '<label for="section">&nbsp;</label>';
            echo '<a class="btn btn-danger btn-flat fa fa-download" href="'.$login->home.'exportCSV.php'.$urlID.'"> Export</a>';
        } 
	echo '</div>';
	
echo '</div>';
?>    
</form>
                                        </div>                                        
                                    </div>
                                    
                                </div>
                            </div><!-- /.box-header -->
                            	  
                           	<div class="box-body table-responsive"> 
								<?PHP
                                    if(!empty($rtpyeID))
                                    {
                                        if(($rtpyeID >= 1) && ($rtpyeID <= 3))
										{
											$reports->ReportDisplay($filters);
										}
										elseif(($rtpyeID >= 4) && ($rtpyeID <= 4))
										{
											$reports->BuilderReport($filters);
										}
                                    }
                                ?>
                            </div>
                        </div><!-- /.box -->
				</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>
<script type="text/javascript">
function PrintPage(elem)
{
	Popup($("#dataTables").html());
}

function Popup(data) 
{	
	var content	=	(data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
	var mywindow	=	'';
	mywindow = window.open('', 'View Report', 'height=800,width=1250');
	mywindow.document.write('<html><head><title>Employee Report</title>');
	mywindow.document.write('</head>');
	mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b>  Employee Report</b></th></tr></br></br>');
	mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');	
	mywindow.document.write('</body></html>');
			mywindow.print()
	mywindow.close();
	return true;
}

$("#CheckAllCompany").on('click',function()	
{
	if($('.company_filterID').is(':checked'))
			{$('.company_filterID').prop('checked', false);}
	else	{$('.company_filterID').prop('checked', true);}
});

$("#insp_fltID_rtpyeID").on('change',function()
{
	var rtpyeID = $("#insp_fltID_rtpyeID").val();
	if(parseInt(rtpyeID) == 4)
			{$("select#rpt_fieldID").multiselect('checkAll');	$("#rpt_fields_divID").show();}
	else	{$("select#rpt_fieldID").multiselect('uncheckAll');	$("#rpt_fields_divID").hide();}
});

var send_getUrlParameter = function(k)
{
	var p = {};
	location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
	return k?p[k]:p;
}

var rtpyeID = decodeURIComponent(send_getUrlParameter('rtpyeID'));
var fromID  = decodeURIComponent(send_getUrlParameter('fromID'));
var toID	= decodeURIComponent(send_getUrlParameter('toID'));

var urlID = 'rpt_n_inspc.php?rtpyeID='+rtpyeID+'&fromID='+fromID+'&toID='+toID;
 
$("#inspc_fltID_1,#inspc_fltID_2,#inspc_fltID_3").on('change',function()	{setFilters(urlID);});

var setFilters = function(urlID)
{
	var fltID_1 = $("#inspc_fltID_1").val();
	var fltID_2 = $("#inspc_fltID_2").val();
	var fltID_3 = $("#inspc_fltID_3").val();
	var fltID_7 = $("#FILTER_companyID").val();
	
	var filterID = '&fltID_1='+fltID_1+'&fltID_2='+fltID_2+'&fltID_3='+fltID_3+'&fltID_7='+fltID_7;
	window.location = urlID + filterID;
}

function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
</script>