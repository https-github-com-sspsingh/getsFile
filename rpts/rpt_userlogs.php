<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/rpt_userlogs.php';
	$reports = new Reports();
	
	$basename  = basename($_SERVER['PHP_SELF']);	
	$headTitle = 'Users Log Report';
	$basename  = basename($_SERVER['PHP_SELF']);	
	$action	   = isset($_GET['a'])		?	$login->Decrypt($_GET['a'])	:	'view';
	
	$rtpyeID  =	1;	
	$fromID	  =	isset($_POST['fromID'])	   ?	$_POST['fromID']     :	'';
	$toID	  =	isset($_POST['toID'])	   ?	$_POST['toID']	     :	'';	
	$frmID	  =	isset($_POST['frmID'])	   ?	$_POST['frmID']	     :	'';
	$actionID =	isset($_POST['actionID'])  ?	$_POST['actionID']	 :	'';
	$actionID = $actionID > 0 ? $actionID : 1;
	
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

<form action="<?=$login->home?>rpts/<?=$basename?>" method="post"> 
		 <div class="row">
			  <div class="col-xs-3">
			  <label for="section">Select Form Detail</label><br />
			  <select name="frmID" class="form-control">
			  <option value="0" selected="selected"> --- Select Form Name --- </option>
              <?PHP
			  $Qry = $login->DB->prepare("SELECT frmID FROM uslogs WHERE frmID In(40,42,37,38,39,42,41,43,44,45,73,78,129,130,131) Group By frmID Order By frmID ASC ");
			  $Qry->execute();
			  $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			  foreach($login->rows as $rows)
			  {
				  $FRM_Array  = $rows['frmID'] > 0  ? $login->select('frmset',array("*"), " WHERE ID = ".$rows['frmID']." ") : '';
				  
				  echo '<option value="'.$rows['frmID'].'" '.($rows['frmID'] == $frmID ? 'selected="selected"' : '').'>'.$FRM_Array[0]['title'].'</option>';
			  }
			  ?>
			  </select>
		  </div>
		  
		  <div class="col-xs-2">
			  <label for="section">Action Detail</label><br />
			  <select name="actionID" class="form-control">
			  <option value="1" <?=($actionID == 1 ? 'selected="selected"' : '')?>>All Actions</option>
			  <option value="2" <?=($actionID == 2 ? 'selected="selected"' : '')?>>New-Entry</option>
			  <option value="3" <?=($actionID == 3 ? 'selected="selected"' : '')?>>Edit-Entry</option>
			  <option value="4" <?=($actionID == 4 ? 'selected="selected"' : '')?>>Delete-Entry</option>
			  </select>
		  </div>
		  
    <div class="col-md-3">
        <label for="section">Select The Date Range</label><br />
        <button class="btn btn-default pull-right form-control" id="daterange-btn">
            <i class="fa fa-calendar"></i> Date Range Picker &nbsp;
            <i class="fa fa-caret-down"></i>
        </button>
    </div>
    
    <input type="hidden" name="fromID" id="fromID" value="<?=($fromID <> '' ? $fromID : (date('d/m/Y')))?>" class="form-cotrol" />
    <input type="hidden" name="toID" id="toID" value="<?=($toID <> '' ? $toID : (date('d/m/Y')))?>" class="form-cotrol" />
    
    
    <div class="col-md-4">
    	<label for="section">&nbsp;</label><br />
    	<input type="submit" name="Submit" class="btn btn-danger btn-flat fa fa-filter" value="&nbsp;Filter" />
        
        <label for="section">&nbsp;</label>
    	<a href="<?=$login->home?>rpts/<?=$basename?>" class="btn btn-danger btn-flat fa fa-refresh">&nbsp;&nbsp;Clear</a>
        
        <label for="section">&nbsp;</label>
    	<a onClick="PrintPage('#dataTables')" class="btn btn-danger btn-flat fa fa-print">&nbsp;&nbsp;Print</a>
        
        <label for="section">&nbsp;</label>
    	<a onClick="exportToExcel()" class="btn btn-danger btn-flat fa fa-download">&nbsp;&nbsp;Export</a>
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
    var content     =   (data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
    var mywindow    =	'';
    mywindow = window.open('', 'View Report', 'height=800,width=1250');
    mywindow.document.write('<html><head><title>User Log\'s Report</title>');
    mywindow.document.write('</head>');
    mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b> User Log\'s Report</b></th></tr></br></br>');
    mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');	
    mywindow.document.write('</body></html>');
    mywindow.print()
    mywindow.close();
    return true;
}
	
function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
</script>