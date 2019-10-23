<?PHP
    include '../includes.php';
    $login->NotLogin_Redi();
    include '../header.php';
    include '../sidebar.php';
    include 'code/rpt_allocsheet.php';
    $reports = new Reports();
    
    $basename  = basename($_SERVER['PHP_SELF']);	
    $headTitle = 'Allocation Sheet';
    
    $rtpyeID    =   isset($_POST['rtpyeID'])      ?	$_POST['rtpyeID']    : '';	
    $fromID	 =   isset($_POST['fromID'])	   ?	$_POST['fromID']     : '';
    $statusID   =   isset($_POST['statusID'])     ?	$_POST['statusID']   : '';

    $message    = isset($_GET['m'])	?	urldecode($reports->Decrypt($_GET['m']))	: '';
    $type	   = isset($_GET['t'])	?	$reports->Decrypt($_GET['t'])		        : '';

    $filters = array();
    foreach($_POST as $key=>$value)  {$filters[$key] = $value;}	
?>
<input type="hidden" id="allocationID" value="111" />

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
    
    <input type="hidden" name="rtpyeID" value="1" />
    
    <div class="col-md-2">
        <label for="section">Sheet Date</label><br />
        <input type="datable" name="fromID" id="fromID" class="form-control datepicker" data-datable="ddmmyyyy" style="text-align:center;" value="<?=($fromID <> '' ? $fromID : (date('d/m/Y')))?>" class="form-cotrol" />
    </div>
    
    <div class="col-md-4">
    	<label for="section">&nbsp;</label><br />
    	<input type="submit" name="Submit" class="btn btn-danger btn-flat fa fa-filter" value="&nbsp;Filter Data" />
        
	    <label for="section">&nbsp;</label>
    	<a href="<?=$login->home?>imports/<?=$basename?>" class="btn btn-danger btn-flat fa fa-refresh">&nbsp;&nbsp;Clear Filters</a>
        
	    <label for="section">&nbsp;</label>
    	<a onClick="PrintPage('#dataTables')" class="btn btn-danger btn-flat fa fa-print">&nbsp;&nbsp;Print Report</a>
        
    </div> 
</div>
</form>                             
                                        </div>
                                    </div>                                    
                                </div>
                            </div><!-- /.box-header -->
                            
     <?PHP if(!empty($message)) { ?> 	
        <div class="alert alert-<?=$type?> alert-dismissable">
        <i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <b><?=$message?></b>
        </div>
    <?PHP } ?>
                            <div class="box-body table-responsive">
                            	<?PHP
                                    if(!empty($rtpyeID))
                                    {
                                        //  echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
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
$(document).ready(function()
{
    $(".shiftcomments").prop('tabIndex', -1);
    $(".othersinfos").prop('tabIndex', -1);
	
	$(".update-signon-cuttoff").on('click',function()
	{
		if($(this).is(':checked'))  
		{
			$.ajax({			
            url : '../ajax/ajax_DBpopups.php',
            type:'POST',
            data:{'request': 'UPDATE_SHIFT_CUTTOFF' , 'recID':($(this).attr('aria-sort'))},
            dataType:"json",				  
            success : function(data)			
            {
				if(data.success == true)
				{
					alert('Shift Status Is Updated...');
				}
            },
            error: function(res)  {console.log('ERROR in Form')}				  
            });
		}
	});
});
    
function PrintPage(elem)
{
    Popup($(elem).html());
}


    function updateMASTERSoptions(valueTEXT,recID,fieldNM)
    {
        if(parseInt(recID) > 0 && valueTEXT != '' && fieldNM != '')
        {		
            $.ajax({			
            url : '../ajax/ajax_DBpopups.php',
            type:'POST',
            data:{'request': 'UPDATE_MASTERS_FIELDS' , 'recID':recID , 'valueTEXT':valueTEXT , 'fieldNM':fieldNM},
            dataType:"json",				  
            success : function(data)			
            {
            },
            error: function(res)  {console.log('ERROR in Form')}				  
            });
        }
    }
    
    function Popup(data) 
    {
        var content   = (data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
        var mywindow  = '';
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

</script>