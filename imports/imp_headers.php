<?PHP	
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';	
    include 'code/imp_headers.php';
    $Imports = new Imports();

    $basename = basename($_SERVER['PHP_SELF']);

    $action	= isset($_GET['a'])	?	$Imports->Decrypt($_GET['a'])			: 'view';
    $id         = isset($_GET['i'])	?	$Imports->Decrypt($_GET['i'])			: '';
    $message    = isset($_GET['m'])	?	urldecode($Imports->Decrypt($_GET['m']))	: '';
    $type	= isset($_GET['t'])	?	$Imports->Decrypt($_GET['t'])		        : '';
    $caseID	= 1;

    $headTitle  = 'Import Header Sheet (Driver Sign On)';
    $titleText  = ($action ==	'view'	?   'All Import Sheet List' : (!empty($id) ? '' : '' ));
    
    $arrayCM = $login->select('company',array("*")," WHERE ID = ".$_SESSION[$login->website]['compID']." ");
    
?> 
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">      
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
            <div style="border:solid 2px #3C8DBC; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">
			
            <form method="post" id="filterForm" action="?a=<?=$Imports->Encrypt('import')?>&cs=<?=$Imports->Encrypt($caseID)?>" enctype="multipart/form-data">
            <input type="hidden" name="optionID" value="<?=$caseID?>" />

            <?PHP
            if($caseID == 1)
            {
                $sheetID = ($_POST['sheetID'] > 0 ? $_POST['sheetID'] : 1);
            ?>
                <input type="hidden" name="sheetID" value="1" />
             
                <div style="display:inline-block;">
                    <select name="stypeID" id="stypeID" class="form-control" required="required" style="width:250px;">
                    <option value="0" selected="selected" disabled="disabled">-- Select Shift Type --</option>
                    <?PHP
                    foreach((explode(",",$arrayCM[0]['stypeID'])) as $stypesID)
                    {
						if($stypesID > 0)
                        { 
							if($stypesID == 1)  {echo '<option value="1">SIUO - School In University Out</option>';}                            
                            if($stypesID == 2)  {echo '<option value="2">SOUI - School Out University In</option>';}                            
                            if($stypesID == 3)  {echo '<option value="3">SOUO - School Out University Out</option>';}                            
                            if($stypesID == 4)  {echo '<option value="4">SIUI - School In University In</option>';}
                            if($stypesID == 5)  {echo '<option value="5">School IN</option>';}
                            if($stypesID == 6)  {echo '<option value="6">School OUT</option>';}                           
                            if($stypesID == 7)  {echo '<option value="7">Saturday</option>';}
                            if($stypesID == 8)  {echo '<option value="8">Sunday</option>';}
                            if($stypesID == 9)  {echo '<option value="9">Special Event</option>';}
                        }
                    }
                    ?>                    
                    </select>
                </div>
            
                <div style="display:inline-block;">
                    <input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="createDT" placeholder="Enter Version Date" style="text-align:center;" />
                </div>
            
                <div style="display:inline-block;">
                    <input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="availDT" placeholder="Enter Applicable Date" style="text-align:center;" />
                </div>            
            
            <div class="btn bg-olive btn-flat btn-file" style="margin-top:10px;">
                <i class="fa fa-paperclip"></i>&nbsp;Import in .xlsx
                <input type="file" name="upload" required="required" class="import" />
            </div>
            
            <button class="btn bg-navy btn-flat" name="Submit" type="submit" style="margin-top: 9px;">Submit</button>
            <a href="<?=$basename?>?cs=<?=$Imports->Encrypt($caseID)?>" style="margin-top: 9px;" class="btn bg-navy btn-flat">Clear Filters</a>
            
            <?PHP
            
            if($_POST['fdateID'] <> '' && $sheetID == 2)
            {
            ?>
                <a data-title="imp_shifts" data-rel="80" data-ajax="<?=$_POST['fdateID']?>" class="btn bg-maroon btn-flat Delete_Confirm">Delete Log <?=($_POST['fdateID'] <> '' ? ' : '.$_POST['fdateID'] : '')?></a>
            <?PHP
            }
                        
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
			if($_POST['sheetID'] == 1)
			{
				$Imports->Form = 'Submit';
				$Imports->GoToInnserSheet();
			}
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