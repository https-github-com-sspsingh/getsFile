<?PHP
	include 'main/includes.php';
	$login->NotLogin_Redi();
	include 'header.php';
	include 'sidebar.php';
	include 'code/'.basename($_SERVER['PHP_SELF']);
	$masters = new Masters();
	
	$basename	=	basename($_SERVER['PHP_SELF']);	
	$action	  =	isset($_GET['a'])		?	$masters->Decrypt($_GET['a'])	:	'view';
	$id		  =	isset($_GET['i'])		?	$masters->Decrypt($_GET['i'])	:	'';
	$message	 =	isset($_GET['m'])		?	urldecode($masters->Decrypt($_GET['m']))	:	'';
	$type		=	isset($_GET['t'])		?	$masters->Decrypt($_GET['t'])	:	'';
	
	$headTitle	=	'Currency Master';
	$titleText	=	$action ==	'view'	?	'All Currency Master List' : (!empty($id) ? 'Edit Currency' : 'Add New Currency' );
	
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
                                <h3 class="box-title"><?=$titleText?></h3>
                                <div class="box-tools">
                                    <div class="input-group">
                                    	<div class="input-group-btn">
                                 
                                      	<?PHP 
											if($action == 'view' || ($action == 'create' && !empty($id)) ) 
											{
												$aclass="btn-primary btn-flat"; $alink	= $basename.'?a='.$masters->Encrypt("create"); 
										?>

                                 
  <a href="<?=$alink?>"><button class="btn <?=$aclass?>"  style="margin-right:20px; float:right; display:inline-block" type="button">Add New</button></a>
 
                     
								<?PHP }  if($action <> 'view') { ?> 
                                
                                    <button class="btn btn-primary btn-flat" 
                                            onclick="createURL('<?=$basename?>');" 
                                            style="margin-right:50px; float:right" 
                                            type="button" >
                                            View List
                                    </button>
                                <?PHP	}	?>
                                        
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
								if($action == 'edit') 
                                {
									$masters->Form = 'Submit';
                                    $masters->update();
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
									if($action  == 'view' && $id <> '')	 $masters->createForm($id);
                                    elseif($action  == 'view')			 $masters->view();
                                    elseif($action == 'create')			 $masters->createForm($id);                                    
                                ?>
                            </div>
                            
                        </div><!-- /.box -->
				</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?PHP include 'footer.php'; ?>