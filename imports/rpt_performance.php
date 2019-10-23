<?PHP	
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';	
    include 'code/rpt_performance.php';
    $Imports = new Imports();

    $basename = basename($_SERVER['PHP_SELF']);

    $action	= isset($_GET['a'])	?	$Imports->Decrypt($_GET['a'])			: 'view';
    $id         = isset($_GET['i'])	?	$Imports->Decrypt($_GET['i'])			: '';
    $message    = isset($_GET['m'])	?	urldecode($Imports->Decrypt($_GET['m']))	: '';
    $type	= isset($_GET['t'])	?	$Imports->Decrypt($_GET['t'])		        : '';
    $caseID	= isset($_GET['cs'])	?	$Imports->Decrypt($_GET['cs'])		        : '';

    $headTitle  = ($caseID == 1 ? 'Import Excel Sheets' :($caseID == 2 ? 'Performance Report' : ''));
    $titleText  = ($action ==	'view'	?   'All Import Sheet List' : (!empty($id) ? '' : '' ));
        
?> 
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side strech">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                        <li><a href="<?=$login->home?><?=$basename?>"><?=$headTitle?></a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
             	<div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            
                            <div class="box-header">
                                <div class="box-tools">
                                    <div class="input-group">
                                    	<div class="input-group-btn">
    <?PHP 
        if($action == 'view' || ($action == 'create' && !empty($id)) || $action == 'displayreport' || $action == 'import') 
        {            
            if((($action == 'view') || $action == 'import') && ($caseID == 2 || $caseID == 1)) 
            {   
    ?>
            <div style="border:solid 2px #85144B; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">
            <form method="post" id="filterForm" action="?a=<?=$Imports->Encrypt('import')?>&cs=<?=$Imports->Encrypt($caseID)?>" enctype="multipart/form-data">
            <input type="hidden" name="optionID" value="<?=$caseID?>" />

            <?PHP
            if($caseID == 1)
            {
            ?>
                <div style="display:inline-block;">
                    <select name="sheetID" id="sheetID" class="form-control" style="width:150px;">
                    <option value="1" selected="selected" disabled="disabled">-- Select Sheet --</option>
                    <option value="1">Punctuality Sheet</option>
                    <option value="2">Early Running Sheet</option>
                    <option value="3">Late First Sheet</option>
                    <option value="4">DriveRight Sheet</option>
                    </select>
                </div>
            <?PHP
            }
            ?>

            <?PHP
            if($caseID == 2)
            {
            ?>

            <div style="display:inline-block; border:#85144B 2px solid; padding:10px;">
            <label style="font-size:15px; color:#85144B;">From : </label><br />

            <div style="display:inline-block;">
                <select name="fmonthID" id="fmonthID" class="form-control" style="width:150px;" required="required">
                  <option value="1" selected="selected" disabled="disabled">-- Select Month --</option>
                  <option value="1" <?=($_POST['fmonthID'] == 1 ? 'selected="selected"' : '')?>>January</option>
                  <option value="2" <?=($_POST['fmonthID'] == 2 ? 'selected="selected"' : '')?>>February</option>
                  <option value="3" <?=($_POST['fmonthID'] == 3 ? 'selected="selected"' : '')?>>March</option>
                  <option value="4" <?=($_POST['fmonthID'] == 4 ? 'selected="selected"' : '')?>>April</option>
                  <option value="5" <?=($_POST['fmonthID'] == 5 ? 'selected="selected"' : '')?>>May</option>
                  <option value="6" <?=($_POST['fmonthID'] == 6 ? 'selected="selected"' : '')?>>June</option>
                  <option value="7" <?=($_POST['fmonthID'] == 7 ? 'selected="selected"' : '')?>>July</option>
                  <option value="8" <?=($_POST['fmonthID'] == 8 ? 'selected="selected"' : '')?>>August</option>
                  <option value="9" <?=($_POST['fmonthID'] == 9 ? 'selected="selected"' : '')?>>September</option>
                  <option value="10" <?=($_POST['fmonthID'] == 10 ? 'selected="selected"' : '')?>>October</option>
                  <option value="11" <?=($_POST['fmonthID'] == 11 ? 'selected="selected"' : '')?>>November</option>
                  <option value="12" <?=($_POST['fmonthID'] == 12 ? 'selected="selected"' : '')?>>December</option>
                </select>
            </div>   

            <div style="display:inline-block;">                                            
                <select name="fyearID" id="yearID" class="form-control" style="width:150px;">
                <option value="1" selected="selected" disabled="disabled">-- Select Year --</option>
                <?PHP
                for($yrsID = 2015; $yrsID <= (date('Y') + 1); $yrsID++)
                {
                    echo '<option value="'.$yrsID.'" '.($_POST['fyearID'] == $yrsID ? 'selected="selected"' : '').'>'.$yrsID.'</option>';
                } 
                ?>
                </select>
            </div>
            </div>

            <div style="display:inline-block; border:#85144B 2px solid; padding:10px;">
            <label style="font-size:15px; color:#85144B;">To : </label><br />

            <div style="display:inline-block">
                <select name="tmonthID" id="tmonthID" class="form-control" style="width:155px;" required="required">
                  <option value="1" selected="selected" disabled="disabled">-- Select Month --</option>
                  <option value="1" <?=($_POST['tmonthID'] == 1 ? 'selected="selected"' : '')?>>January</option>
                  <option value="2" <?=($_POST['tmonthID'] == 2 ? 'selected="selected"' : '')?>>February</option>
                  <option value="3" <?=($_POST['tmonthID'] == 3 ? 'selected="selected"' : '')?>>March</option>
                  <option value="4" <?=($_POST['tmonthID'] == 4 ? 'selected="selected"' : '')?>>April</option>
                  <option value="5" <?=($_POST['tmonthID'] == 5 ? 'selected="selected"' : '')?>>May</option>
                  <option value="6" <?=($_POST['tmonthID'] == 6 ? 'selected="selected"' : '')?>>June</option>
                  <option value="7" <?=($_POST['tmonthID'] == 7 ? 'selected="selected"' : '')?>>July</option>
                  <option value="8" <?=($_POST['tmonthID'] == 8 ? 'selected="selected"' : '')?>>August</option>
                  <option value="9" <?=($_POST['tmonthID'] == 9 ? 'selected="selected"' : '')?>>September</option>
                  <option value="10" <?=($_POST['tmonthID'] == 10 ? 'selected="selected"' : '')?>>October</option>
                  <option value="11" <?=($_POST['tmonthID'] == 11 ? 'selected="selected"' : '')?>>November</option>
                  <option value="12" <?=($_POST['tmonthID'] == 12 ? 'selected="selected"' : '')?>>December</option>
                </select>
            </div>

            <div style="display:inline-block">                                            
                <select name="tyearID" id="yearID" class="form-control" style="width:150px;">
                <option value="1" selected="selected" disabled="disabled">-- Select Year --</option>
                <?PHP
                for($yrsID = 2015; $yrsID <= (date('Y') + 1); $yrsID++)
                {
                    echo '<option value="'.$yrsID.'" '.($_POST['tyearID'] == $yrsID ? 'selected="selected"' : '').'>'.$yrsID.'</option>';
                }
                ?>
                </select>
            </div>
            </div>

<div style="display:inline-block; border:#85144B 2px solid; padding:10px;">
            <label style="font-size:15px; color:#85144B;">Employee Code : </label><br />

            <div style="display:inline-block">
                <input type="text" name="ecodeID" class="form-control numeric" style="width:155px;text-align:center;" placeholder="E. Code" value="<?=$_POST['ecodeID']?>" />
            </div>
            </div>
            
            <?PHP
            }
            ?>

            <?PHP
            if($caseID == 1)
            {
            ?>
            <div class="btn bg-olive btn-flat btn-file btn-file"  style="margin-top: 9px;">
                <i class="fa fa-paperclip"></i>&nbsp;Import in .xlsx
                <input type="file" name="upload" class="import" required="required" />
            </div>

            <?PHP
            }
            ?>

                <button class="btn bg-maroon btn-flat" style="margin-top: 8px;" name="Submit" type="submit">Submit</button>
                <a href="rpt_performance.php?cs=<?=$Imports->Encrypt($caseID)?>" style="margin-top: 8px;" class="btn bg-maroon btn-flat">Clear Filters</a>

            <?PHP
            if($caseID == 2)
            {
            ?>
            <a onClick="PrintPage('#dataTables')" style="margin-top: 8px;" class="btn bg-maroon btn-flat">Print Report</a>
            <a onClick="exportToExcel()" style="margin-top: 8px;" class="btn bg-navy btn-flat">Export Excel</a>

            <?PHP
            }
            ?> 

            </form>
         </div>

<?PHP }	
        }
    ?>
    </div>
    </div>
    </div>
    </div><!-- /.box-header -->
                            	
    <?PHP
        if($action == 'import') 
        {
            $Imports->Form = 'Submit';
            $Imports->GoToInnserSheet();
        }	
     ?>
                           
    
     <?PHP if(!empty($message)) { ?> 	
                          			<div class="alert alert-<?=$type?> alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b><?=$message?></b>
                                    </div>
    <?PHP } ?>
                                
                                <div class="box-body table-responsive">&nbsp;</div>                                
                        </div><!-- /.box -->
                </div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type="text/javascript">
function PrintPage(elem)    {Popup($(elem).html());}

function Popup(data) 
{
    var content     =  (data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
    var mywindow    =  '';
    mywindow = window.open('', 'View Report', 'height=800,width=1250');
    mywindow.document.write('<html><head><title>Performance Report</title>');
    mywindow.document.write('</head>');
    mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b> Performance Report</b></th></tr></br></br>');
    mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');
    mywindow.document.write('</body></html>');
    mywindow.print();
    mywindow.close();
    return true;
}

function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
</script>