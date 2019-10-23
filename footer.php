    <link href="<?=$login->home?>css/datepicker.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="<?=$login->home?>css/jquery.datepick.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="<?=$login->home?>css/datetimepicker.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="<?=$login->home?>css/jquery-ui.css" rel="stylesheet" type="text/css" />
	
<?PHP
    if((substr(($pageURL), 0, 3) == 'rpt'))
    {
?>
		<link href="<?=$login->home?>js/rpt-multi/jquery.multiselect.css" rel="stylesheet" type="text/css" />		
<?PHP
	}
	else
	{
?>
		<link href="<?=$login->home?>css/multi/jquery.multiselect.css" rel="stylesheet" type="text/css" media="screen"  />
		<link href="<?=$login->home?>css/multi/jquery.multiselect.filter.css" rel="stylesheet" type="text/css" media="screen"  />		
<?PHP
	} 
?>
		<link href="<?=$login->home?>css/multi/style.css" rel="stylesheet" type="text/css" media="screen"  />
		<link href="<?=$login->home?>css/multi/prettify.css" rel="stylesheet" type="text/css" media="screen"  />
		
<?PHP
    if((substr(($pageURL), 0, 3) == 'rpt'))
    {
?>
        <link href="<?=$login->home?>css/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen" />
<?PHP
    }
?> 
        <!-- jQuery 2.0.2 -->
        <script src="<?=$login->home?>js/jquery.min.js"></script>
        <script type="text/javascript" src="<?=$login->home?>js/jquery-ui.min.js"></script>
        <script src="<?=$login->home?>js/jquery.cookie.js" type="text/javascript"></script>
		
		<!-- Bootstrap -->
        <script src="<?=$login->home?>js/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="<?=$login->home?>js/app.js" type="text/javascript"></script>
		 
        <!-- AdminLTE for demo purposes -->
        <!--<script src="<?=$login->home?>js/demo.js" type="text/javascript"></script>-->
        
        <script src="<?=$login->home?>js/bootstrap-combobox.js" type="text/javascript"></script>
        <script src="<?=$login->home?>js/moment.js" type="text/javascript"></script>

        <!-- TimePicker -->
        <script src="<?=$login->home?>js/clockface.js"></script>
        <script src="<?=$login->home?>js/bootstrap-datetimepicker.min.js"></script>
        <!--<script src="<?=$login->home?>js/date-time-picker.min.js"></script>-->
        
        <script src="<?=$login->home?>js/jquery.plugin.min.js"></script>
        <script src="<?=$login->home?>js/jquery.datepick.js"></script>
        
        <script src="<?=$login->home?>js/jquery.dataTables.js" type="text/javascript"></script>
        <script src="<?=$login->home?>js/dataTables.bootstrap.js" type="text/javascript"></script> 
        <script src="<?=$login->home?>js/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        <script src="<?=$login->home?>js/jquery.confirm.js"></script>	
        <script src="<?=$login->home?>js/gen_validatorv31.js" type="text/javascript"></script>

        <?PHP
            if((substr(($pageURL), 0, 3) == 'rpt'))
            {
        ?>
            <script src="<?=$login->home?>js/daterangepicker.js" type="text/javascript"></script>
        <?PHP
            }
        ?>
        
        <script type="text/javascript">
            $(".Delete_Confirm").confirm
            ({
                title:"<b style='color:#1591E0;'>Delete confirmation</b>",
                text:"Are you sure you want to delete this record !. ",
                confirm: function(button) 
                {	
                    var request = $(button).attr('data-title');
                    var ID      = $(button).attr('data-ajax');
					
					if(parseInt(ID) > 0 && request != '')
					{
						$.ajax({			
						url : '../ajax/ajax_delete.php',
						type:'POST',
						data:{'request': request , 'ID':ID , 'frmID':($(button).attr('data-rel'))},
						dataType:"json",				  
						success : function(data)			
						{
							if(parseInt(data.Counts) > 0)   {alert(String(data.Msg));}
							else if(data.Status == 1)       {location.reload();}
						},
						error: function(res)  {console.log('ERROR in Form')}				  
						});	
					}
                },
                cancel: function(button) 
                {},
                cancelButtonClass: 'btn-primary',
                confirmButtonClass: 'btn-primary',
                confirmButton: "&nbsp;&nbsp;Yes&nbsp;&nbsp;",
                cancelButton: "&nbsp;&nbsp;No&nbsp;&nbsp;"
            }); 
            

			$(".POPUP_uslogsID").on('click',function()
			{
				var urlID = $(this).attr('aria-sort');
				var resID = '';
				resID = urlID.split("_");
				actionPopupView('POPUP_uslogsID','ajax_ulogs.php',resID,(resID[2] + ' Form - User Log\'s'),'popups_modal');
			}); 

			$(".POPUP_fieldsID").on('click',function()
			{
				var urlID = $(this).attr('aria-sort');
				var resID = '';
				resID = urlID.split("_"); 
				actionPopupView('POPUP_fieldsID','ajax_ulogs.php',resID,(resID[2] + ' <b style="color:red;"> - Missing Fields</b>'),'fields_modal');
			}); 
			
			$(".frm_viewID").on('click',function()
			{
				var urlID = $(this).attr('aria-sort');
				var resID = '';
				resID = urlID.split("_"); 
				actionPopupView('FORM_logsID','ajax_flogs.php',resID,(resID[2] + ' Form'),'flogs_modal');
			}); 
			
			$(".POPUP_apilogsID").on('click',function()
			{	
				actionPopupView('INCIDENT_API_LOGS','ajax_ulogs.php',($(this).attr('aria-sort')),'Incident API - Logs','popups_modal');
			}); 
			
			var actionPopupView = function(request,pageID,resID,captionTX,popupActionID)
			{
				if(request != '')
				{
					$.ajax({
					url : '../ajax/'+pageID,
					type:'POST',				
					data:{'request':request , 'frmID':resID[0] , 'ID':(request == 'INCIDENT_API_LOGS' ? resID : resID[1])},
					dataType:"json",
					success : function(data)
					{
						$('#'+popupActionID+' h4').html(captionTX);
						$('#'+popupActionID+' #modal_data').html(data.file_info);
						$('#'+popupActionID+'').modal('show');            
					},
					error: function(res)    {console.log(res);} 	
					});
				}
				else	{alert('Error In Code !....');}
			}
			
        </script>
		
        <!-- Select2 -->
        <script src="<?=$login->home?>js/select2.full.min.js"></script>
        <!-- Notification Jquery -->
        <script src="<?=$login->home?>js/notify/Lobibox.js"></script>
        <script src="<?=$login->home?>js/notify/demo.js"></script>        
        <!-- Numeric Field Settelments -->
        <script src="<?=$login->home?>js/jquery.numeric.js"></script>
		
<?PHP
    if((substr(($pageURL), 0, 3) == 'rpt'))
    {
?>
		<!-- Reports - MultiSelect -->
		<script src="<?=$login->home?>js/rpt-multi/jquery.multiselect.js" type="text/javascript"></script>
<?PHP
	}
	else
	{
?>
		<script src="<?=$login->home?>js/multi/jquery.multiselect.js" type="text/javascript"></script>
		<script src="<?=$login->home?>js/multi/jquery.multiselect.filter.js" type="text/javascript"></script>
		<script src="<?=$login->home?>js/multi/prettify.js" type="text/javascript"></script>
<?PHP		
	}
?>
        <script src="<?=$login->home?>js/printElement.js" type="text/javascript"></script>         
        <!-- Working Scripts -->        
		<script src="<?=$login->home?>js-tags/file.js" type="text/javascript"></script>
		<script src="<?=$login->home?>js-tags/file_0.js" type="text/javascript"></script>
        <script src="<?=$login->home?>js-tags/file_1.js" type="text/javascript"></script> 
        <script src="<?=$login->home?>js-tags/file_2.js" type="text/javascript"></script> 
        <script src="<?=$login->home?>js-tags/file_3.js" type="text/javascript"></script> 
        <script src="<?=$login->home?>js-tags/file_4.js" type="text/javascript"></script> 
		<script src="<?=$login->home?>js-tags/file_5.js" type="text/javascript"></script>
<?PHP
    if($pageURL == 'drvsigon.php')
    {
?>
		<script src="<?=$login->home?>js-tags/file_6.js" type="text/javascript"></script>
<?PHP
	}
?>
		<script src="<?=$login->home?>js/input-mask/datable.js" type="text/javascript"></script>		
        <script type="text/javascript">
            $(".numeric").numeric();
            $(".positive").numeric({negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
            $(".decimal_places_3").numeric({ decimalPlaces:3});
            $(".decimal_places_2").numeric({ decimalPlaces:2});
            $(".decimal_places_1").numeric({ decimalPlaces:1});
            $(".select2").select2();
            $(".TPicker").clockface({format: 'HH : mm'}).clockface('hide', '14:30');
			
            $(function()    {$('.datepicker').datepick();	$.datable();});
        </script> 
<?PHP
	if((substr(($pageURL), 0, 3) == 'rpt'))
	{
?>
        <script type="text/javascript">
            $(function() 
            { 
                $('#daterange-btn').daterangepicker(
                {
                ranges: 
                    {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                        'Last 7 Days': [moment().subtract('days', 6), moment()],
                        'Last 30 Days': [moment().subtract('days', 29), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                    },
                    //startDate: moment().subtract('days', 29),
                    //endDate: moment()
                },
                function(start, end)    
                {
                    $("#fromID").val(start.format('DD/MM/YYYY'));
                    $("#toID").val(end.format('DD/MM/YYYY')); 
                    $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                }
                );
            });
        </script>
		
<?PHP
	}
	
	include 'modal.php'; 
	include 'modal-src.php'; 
	include 'modal-pops.php';
	include 'modal-flogs.php';
	include 'modal-swaps.php';
	include 'modal-fields.php';
	include 'modal-audit.php';
?>

<script type="text/javascript">
function dobBlinking() 
{
	$(".blinkingTX").effect("pulsate");	
    $('.blinkingTX').fadeOut(1000);
    $('.blinkingTX').fadeIn(250);
}
setInterval(dobBlinking, 1000);

var pageID = document.location.pathname.match(/[^\/]+$/)[0];
if(pageID == 'profile_4.php' || pageID == 'drvsigon.php')
{
	function ClockTimers(id = 'ClockTimers')
	{
		var date = new Date().toLocaleString("en-US", {timeZone: "Australia/Perth"});
		date = new Date(date);
		year   = date.getFullYear();
		month  = date.getMonth();
		months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		d    = date.getDate();
		day  = date.getDay();
		days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		h    = date.getHours();
		
		if(h < 10)	{h = "0"+h;}
		
		m = date.getMinutes();
		
		if(m < 10)	{m = "0"+m;}
		s = date.getSeconds();
		
		if(s < 10)	{s = "0"+s;}
		
		result = ''+days[day]+', '+d+' '+months[month]+' '+year+', '+h+':'+m+':'+s;
		document.getElementById(id).innerHTML = result;
		setTimeout('ClockTimers("'+id+'");','1000');
		
		return true;
	}

	setInterval(ClockTimers, 100);
}
</script>
    <!--<div id="printer"></div>-->        
    </body>
</html>