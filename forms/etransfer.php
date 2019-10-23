<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/etransfer.php';
	$masters = new Masters();
	
	$basename	=   basename($_SERVER['PHP_SELF']);	
	$action     =   isset($_GET['a'])		?	$masters->Decrypt($_GET['a'])               :   'create';
	$id		 	=    isset($_GET['i'])	    ?	$masters->Decrypt($_GET['i'])               :   '';
	$message	=   isset($_GET['m'])		?	urldecode($masters->Decrypt($_GET['m']))	:   '';
	$type    	=   isset($_GET['t'])		?	$masters->Decrypt($_GET['t'])               :   '';

	$fdateID	 =   isset($_GET['fdateID'])	 ?	$_GET['fdateID']       :	'';
	$tdateID	 =   isset($_GET['tdateID'])	 ?	$_GET['tdateID']	   :	'';
	$searchbyID  =   isset($_GET['searchbyID'])  ?	$_GET['searchbyID']	:	'';
 
	$headTitle	=   'Employee Transfer - Lists';
?>
<input type="hidden" id="fieldID" value="101" />
<input type="hidden" id="fieldsID" value="<?=($action == 'view' ? 0 :($action == 'popups' ? 0 : 1))?>" />
<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">      
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div id="action_message"></div>
                    <div id="action_status"></div>
                    <h1><?=$headTitle?></h1>
                    <ol class="breadcrumb">
                        <li><a href="<?=$login->home?>"><i class="fa fa-dashboard"></i>Home</a></li>
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
                                                       
				<form action="<?=$login->home?>forms/<?=$basename?>" method="get" >                             
				<div class="row" style=" padding-left:10px !important;">

					<div class="col-xs-3">
						<input type="text" class="form-control" name="searchbyID" id="searchbyID" placeholder="Search By" value="<?=$searchbyID?>" />
					</div>
					
					<div class="col-md-1">
						<input type="submit" name="Submit" class="btn btn-primary btn-flat btn-flat fa fa-filter" value="&nbsp;Filter Data" />
					</div>
					
					<div class="col-md-1">
						<a href="<?=$login->home?>forms/<?=$basename?>" style="margin-left: 15px;" class="btn btn-primary btn-flat btn-flat fa fa-refresh">&nbsp;&nbsp;Clear Filters</a>
					</div>
					
					<!--<div class="col-md-1" style="margin-left:30px;">
						<a onClick="exportToExcel()" class="btn btn-primary btn-flat btn-flat fa fa-download">&nbsp;&nbsp;Export Excel</a>
					</div>-->
					
					
				   
				</div>
				</form> 
                
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                
                             


                            </div><!-- /.box-header -->
                            	
							<?PHP
                                if($action == 'add') 
                                { 
                                    $masters->Form = 'Submit';
                                    $masters->add();                                
                                }
                            ?>
                            <?PHP if(!empty($message)) { ?> 	
                          			<div class="alert alert-<?=$type?> alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b><?=$message?></b>
                                    </div>
							<?PHP } ?>
                           
                           	<div class="box-body table-responsive"> 
                                <?PHP 
									$masters->createForm($searchbyID);
                                ?>
                            </div>
                            
                        </div><!-- /.box -->
				</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>

<script type="text/javascript">
function exportToExcelIn()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTableIn').outerHTML));}
function exportToExcelOut()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTableOut').outerHTML));}
</script>
