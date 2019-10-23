<?PHP
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';
    include 'code/rpt_shifts.php';
    $reports = new Reports();
    
    $basename  = basename($_SERVER['PHP_SELF']);	
    $headTitle = 'Driver Sign On Report';
    
    $rtpyeID    =   isset($_POST['rtpyeID'])      ?	$_POST['rtpyeID']    : '';	
    $fromID	=   isset($_POST['fromID'])	  ?	$_POST['fromID']     : '';
    $toID       =   isset($_POST['toID'])	  ?	$_POST['toID']	     : '';
    $statusID   =   isset($_POST['statusID'])     ?	$_POST['statusID']   : '';

    $filters = array();
    foreach($_POST as $key=>$value)  {$filters[$key] = $value;}	
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
                        <li><a href="<?=$login->home.'imports/'.$basename?>"><?=$headTitle?></a></li>
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

                                              
                                            
<form action="<?=$login->home?>imports/<?=$basename?>" method="post" enctype="multipart/form-data" id="formID">
<div class="row">
    <div class="col-md-2">
        <label for="section">Report Type</label><br />
        <select class="form-control" id="SL_rtpyeID" name="rtpyeID">
            <option value="0" selected="selected" disabled="disabled">-- Select Report Type --</option> 
            <option value="1" <?=$rtpyeID == 1 ? 'selected="selected"' : ''?>>Sign On Depot Wise</option>
            <option value="2" <?=$rtpyeID == 2 ? 'selected="selected"' : ''?>>Sign On Driver Wise</option>
        </select>
    </div> 
    
    <div id="SL_gridID"></div>
    
    <div class="col-md-3">
        <label for="section">Select The Date Range</label><br />
        <button class="btn btn-default pull-right form-control" id="daterange-btn">
            <i class="fa fa-calendar"></i> Date Range Picker &nbsp;
            <i class="fa fa-caret-down"></i>
        </button>
    </div>
    
    <input type="hidden" name="fromID" id="fromID" value="<?=($fromID <> '' ? $fromID : (date('d/m/Y', strtotime('first day of last month'))))?>" class="form-cotrol" />
    <input type="hidden" name="toID" id="toID" value="<?=($toID <> '' ? $toID : (date('d/m/Y', strtotime('last day of last month'))))?>" class="form-cotrol" />
    
    <div class="col-md-4">
    	<label for="section">&nbsp;</label><br />
    	<input type="submit" name="Submit" class="btn btn-danger btn-flat fa fa-filter" value="&nbsp;Filter Data" />
        
	    <label for="section">&nbsp;</label>
    	<a href="<?=$login->home?>imports/<?=$basename?>" class="btn btn-danger btn-flat fa fa-refresh">&nbsp;&nbsp;Clear Filters</a>
        
	    <label for="section">&nbsp;</label>
    	<a onClick="PrintPage('#dataTables')" class="btn btn-danger btn-flat fa fa-print">&nbsp;&nbsp;Print Report</a>
        
	    <label for="section">&nbsp;</label>
    	<a onClick="exportToExcel()" class="btn btn-danger btn-flat fa fa-download">&nbsp;&nbsp;Export Excel</a>
    </div> 

</div>

</form>
                                        
                                        </div>                                        
                                    </div>
                                    
                                </div>
                            </div><!-- /.box-header -->
                            	  
                           	<div class="box-body table-responsive"> 
                            
                            	<?PHP
									if(!empty($rtpyeID))
									{
										echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
										$reports->ReportDisplay($filters);
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
	Popup($(elem).html());
}

function Popup(data) 
{	
	var content   =	(data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
	var mywindow  =	'';
	mywindow = window.open('', 'View Report', 'height=800,width=1250');
	mywindow.document.write('<html><head><title>Driver Sign On Report</title>');
	mywindow.document.write('</head>');
	mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b>Driver Sign On Report</b></th></tr></br></br>');
	mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');	
	mywindow.document.write('</body></html>');
	mywindow.print();
	mywindow.close();
	return true;
}

function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
</script>