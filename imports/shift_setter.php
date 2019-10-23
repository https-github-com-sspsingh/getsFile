<?PHP
	include '../includes.php';
	$login->NotLogin_Redi();
	include '../header.php';
	include '../sidebar.php';
	include 'code/shift_setter.php';
	$masters = new Imports();
	
	$basename	=	basename($_SERVER['PHP_SELF']);	
	$action	  =	isset($_GET['a'])		?	$masters->Decrypt($_GET['a'])	:	'view';
	$id		  =	isset($_GET['i'])		?	$masters->Decrypt($_GET['i'])	:	'';
	$message	 =	isset($_GET['m'])		?	urldecode($masters->Decrypt($_GET['m']))	:	'';
	$type		=	isset($_GET['t'])		?	$masters->Decrypt($_GET['t'])	:	'';

	$fdateID		=	isset($_GET['fdateID'])		?	$_GET['fdateID']	:	'';
	$tdateID		=	isset($_GET['tdateID'])		?	$_GET['tdateID']	:	'';
		
	$headTitle  =   'Shift Setter';
	
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
                                            
                                        </div>                                        
                                    </div>                                    
                                </div>
                                
                                
<div class="row" style=" padding-left:10px !important;">

    <div class="col-xs-3">
        <label>Shift Day Type</label>
        <select name="stypeID" id="SS_stypeID" class="form-control" style="width:250px;">
        <option value="0" selected="selected" disabled="disabled">-- Select Shift Day Type --</option>
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
    
    <div class="col-md-4">
            <label></label><br />
            <a class="btn btn-primary btn-flat" id="shift_setter_requestID">Filter Data</a>
            
            <a class="btn btn-primary btn-flat" id="shift_setter_clearID" style="margin-left:20px;">Clear All Data</a>
    </div>
</div>
                            </div><!-- /.box-header -->			 
                           	<div class="box-body table-responsive">
                                    <?PHP   $masters->createForm($id);  ?>
                            </div>
                            
                        </div><!-- /.box -->
				</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include '../footer.php'; ?>