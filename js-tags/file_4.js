$(document).ready(function()
{
	//alert(document.location.href.match(/[^\/]+$/)[0]);
	//alert(document.location.pathname.match(/[^\/]+$/)[0]);
	
	$(".chopped_undoID").confirm
	({
		title:"<b style='color:#1591E0;'>Swap confirmation</b>",
		text:"Do you really want to undo the change? ",
		confirm: function(button) 
		{
			var recID = $(button).attr('aria-sort');
			var pageID = document.location.pathname.match(/[^\/]+$/)[0];
			var urlID = (pageID == 'drvshifts.php' || pageID == 'profile_4.php' ? '' : '../');

			$.ajax({
					url : (urlID) + 'ajax/ajax_DBpopups.php',
					type:'POST',				
					data:{'request': 'UNDO_CHOPPED', 'recID': recID},
					dataType:"json",
					success : function(data)
					{
						if(data.success == 1)	{window.location.reload();}
						else					{alert('Error In Api...');}
					},
					error: function(res)    {console.log(res);} 	
			   });

		},
		cancel: function(button) 
		{},
		cancelButtonClass: 'btn-primary',
		confirmButtonClass: 'btn-primary',
		confirmButton: "&nbsp;&nbsp;Yes&nbsp;&nbsp;",
		cancelButton: "&nbsp;&nbsp;No&nbsp;&nbsp;"
	});	
	
	$(".swipe_undoID").confirm
	({
		title:"<b style='color:#1591E0;'>Swap confirmation</b>",
		text:"Do you really want to undo the change? ",
		confirm: function(button) 
		{
			var resultID = $(button).attr('aria-sort');
			var returnID = resultID.split('_');
			var pageID = document.location.pathname.match(/[^\/]+$/)[0];
			var urlID = (pageID == 'drvshifts.php' || pageID == 'profile_4.php' ? '' : '../');

			$.ajax({
					url : (urlID) + 'ajax/ajax_DBpopups.php',
					type:'POST',				
					data:{'request': 'UNDO_' + returnID[0], 'dateID': returnID[1], 'changesID': returnID[2], 'empID': returnID[3], 'recID': returnID[4]},
					dataType:"json",
					success : function(data)
					{
						if(data.success == 1)       {window.location.reload();}
						else	{alert('Error In Api...');}
					},
					error: function(res)    {console.log(res);} 	
			   });

		},
		cancel: function(button) 
		{},
		cancelButtonClass: 'btn-primary',
		confirmButtonClass: 'btn-primary',
		confirmButton: "&nbsp;&nbsp;Yes&nbsp;&nbsp;",
		cancelButton: "&nbsp;&nbsp;No&nbsp;&nbsp;"
	});
		
	$(".swipe_modelID").click(function()
	{
		var resuID = $(this).attr('aria-sort');
		var returnID = resuID.split('_');
		var pageID = document.location.pathname.match(/[^\/]+$/)[0];
		var urlID = (pageID == 'drvshifts.php' || pageID == 'profile_4.php' ? '' : '../');
		
		if(parseInt(returnID[1]) > 0)
		{
			$.ajax({
			url : (urlID) + 'ajax/ajax_popups.php',
			type:'POST',				
			data:{'request': 'SWAP_' + returnID[0] , 'ID':returnID[1] , 'changesID':returnID[2]},
			dataType:"json",
			success : function(data)
			{
				$('#swaps_modal h4').html('<b style="color:#F56954;">Replace - ' + returnID[0] + ' </b>');
				$('#swaps_modal #modal_data').html(data.file_info);
				$('#swaps_modal').modal('show');					
				$(".select2").select2();
				$(".select2").trigger('change');

				$("#searchbyID").on('keyup',function()
				{
					var requestID = $("#requestID").val();
					var searchbyID = $("#searchbyID").val();
					var companyID = $("#companyID").val();
					var date_REQID = $("#date_REQID").val();
					var spare_REQID = $("#spare_REQID").val();
					var tag_statusID = $("#tag_statusID").val();
					var shiftNO = $("#shiftNO").val();
					
					if((requestID == 'FILTER_ALL_BUSES' || requestID == 'FILTER_ALL_EMPLOYEES')  && searchbyID != '' && companyID != '')
					{
						$.ajax({
						url : (urlID) + 'ajax/ajax_filters.php',
						type:'POST',				
						data:{'request':requestID , 'ID':searchbyID , 'companyID':companyID , 'dateID':date_REQID , 'spareID':spare_REQID , 'tag_statusID':tag_statusID , 'shiftNO':shiftNO},
						dataType:"json",
						success : function(data)
						{
							if(data.countID > 0)
							{
								$("#filters_data").empty();
								$("#filters_data").html(data.filterDATA);

								$(".selection-chopped-modal").click(function()
								{
									var recID = $(this).attr('aria-sort');

									if(parseInt(recID) > 0)
									{
										$('#swaps_modal').modal('hide');
										
										$.ajax({
										url : (urlID) + 'ajax/ajax_DBpopups.php',
										type:'POST',				
										data:{'request': 'UPDATE_CHOPPED', 'recID':recID, 'choppedID':1},
										dataType:"json",
										success : function(data)
										{
												if(data.success == 1)	{window.location.reload();}	else	{alert('Error In Api...');}
										},
										error: function(res)    {console.log(res);} 	
										});
									}
								});
				
								$(".selection-temp-modal").click(function()
								{
									var sortID = $(this).attr('aria-busy');
									var resID = $(this).attr('aria-sort');

									var retID = resID.split('_');

									var temp_busID = (sortID == 'NEW_BUSES'     ? retID[2] : 0);
									var temp_empID = (sortID == 'NEW_EMPLOYEES' ? retID[2] : 0);

									var temp_avaiableID = $("#temp_avaiableID_"+temp_empID).val();
									var temp_timeID = $("#temp_timeID_"+temp_empID).val();

									var statusID = 0;
									var statusID = retID[3];

									var request = (parseInt(temp_busID) > 0 ? 'UPDATE_TEMP_BUSES' :(parseInt(temp_empID) > 0 ? 'UPDATE_TEMP_EMPLOYEES' : ''));

									if(parseInt(temp_busID) > 0 || parseInt(temp_empID) > 0)
									{
										$('#swaps_modal').modal('hide');

										if(parseInt(statusID) == 2)
										{
											SwapsBusesConfirmDialog('Are you sure that you want to update same shift '+retID[4]+' In B - PART.',urlID,request,retID[5],retID[0],retID[1],temp_busID,temp_empID,temp_avaiableID,temp_timeID);
										}
										else
										{
											$.ajax({
											url : (urlID) + 'ajax/ajax_DBpopups.php',
											type:'POST',				
											data:{'request': request, 'spareID':retID[0] , 'dateID':retID[1] , 'companyID':retID[5],'temp_busID':temp_busID , 'temp_empID':temp_empID , 'temp_avaiableID':temp_avaiableID , 'temp_timeID':temp_timeID , 'statusID':1},
											dataType:"json",
											success : function(data)
											{
												if(data.success == 1)	{window.location.reload();}	else	{alert('Error In Api...');}
											},
											error: function(res)    {console.log(res);} 	
											});
										}
									}
								});
							}                                                            
						},
						error: function(res)    {console.log(res);} 	
						});
					}
				});


				$(".selection-temp-modal").click(function()
				{
					var sortID = $(this).attr('aria-busy');
					var resID = $(this).attr('aria-sort');

					var retID = resID.split('_');

					var temp_busID = (sortID == 'NEW_BUSES'     ? retID[2] : 0);
					var temp_empID = (sortID == 'NEW_EMPLOYEES' ? retID[2] : 0);

					var temp_avaiableID = $("#temp_avaiableID_"+temp_empID).val();
					var temp_timeID = $("#temp_timeID_"+temp_empID).val();

					var statusID = 0;
					var statusID = retID[3];

					var request = (parseInt(temp_busID) > 0 ? 'UPDATE_TEMP_BUSES' :(parseInt(temp_empID) > 0 ? 'UPDATE_TEMP_EMPLOYEES' : ''));

					if(parseInt(temp_busID) > 0 || parseInt(temp_empID) > 0)
					{
						$('#swaps_modal').modal('hide');

						if(parseInt(statusID) == 2)
						{
							SwapsBusesConfirmDialog('Are you sure that you want to update same shift '+retID[4]+' In B - PART.',urlID,request,retID[5],retID[0],retID[1],temp_busID,temp_empID,temp_avaiableID,temp_timeID);
						}
						else
						{
							$.ajax({
							url : (urlID) + 'ajax/ajax_DBpopups.php',
							type:'POST',				
							data:{'request': request, 'spareID':retID[0] , 'dateID':retID[1] , 'companyID':retID[5],'temp_busID':temp_busID , 'temp_empID':temp_empID , 'temp_avaiableID':temp_avaiableID , 'temp_timeID':temp_timeID , 'statusID':1},
							dataType:"json",
							success : function(data)
							{
									if(data.success == 1)	{window.location.reload();}	else	{alert('Error In Api...');}
							},
							error: function(res)    {console.log(res);} 	
							});
						}
					}
				});
				
				$(".selection-chopped-modal").click(function()
				{
					var recID = $(this).attr('aria-sort');

					if(parseInt(recID) > 0)
					{
						$('#swaps_modal').modal('hide');
						
						$.ajax({
						url : (urlID) + 'ajax/ajax_DBpopups.php',
						type:'POST',				
						data:{'request': 'UPDATE_CHOPPED', 'recID':recID, 'choppedID':1},
						dataType:"json",
						success : function(data)
						{
								if(data.success == 1)	{window.location.reload();}	else	{alert('Error In Api...');}
						},
						error: function(res)    {console.log(res);} 	
						});
					}
				});
				
				$("#temp_avaiableID").change(function()
				{
					var avaiableID = $(this).val();

					if(parseInt(avaiableID) > 0)
					{
						if(parseInt(avaiableID) == 2)   {$("#temp_timeID").attr('readonly',true);    $("#temp_timeID").attr('required',false);}
						else if(parseInt(avaiableID) == 1 || parseInt(avaiableID) == 3)
								{$("#temp_timeID").attr('readonly',false);   $("#temp_timeID").attr('required',true);}
						else    {$("#temp_timeID").attr('readonly',true);    $("#temp_timeID").attr('required',false);}
					}
				});
				
				$(".selection-modal").click(function()
				{
					var resID = $(this).attr('aria-sort');
					var request  = $(this).attr('aria-busy'); 
					var retID = resID.split('_');

					var statusID = 0;
					var statusID = retID[5];

					if(parseInt(retID[0]) > 0 && parseInt(retID[1]) > 0 && (request != ''))
					{
						$('#swaps_modal').modal('hide');

						if(parseInt(statusID) == 2)
						{
							var send_request  = 'UPDATE_' + request;
							var send_spareID  = retID[0];
							var send_shiftsID = retID[1];
							var send_empID    = retID[2];
							var send_dateID   = retID[3];
							var send_spr_empID = retID[4];

							SwapsConfirmDialog('Are you sure that you want to update same shift '+retID[6]+' In B - PART.',urlID,retID[7],send_request,send_spareID,send_shiftsID,send_empID,send_dateID,send_spr_empID);
						}
						else
						{
							$.ajax({
							url : (urlID) + 'ajax/ajax_DBpopups.php',
							type:'POST',				
							data:{'request': 'UPDATE_' + request , 'spareID':retID[0] , 'companyID':retID[7] , 'shiftsID':retID[1] , 'empID':retID[2] , 'dateID':retID[3] , 'spr_empID':retID[4], 'statusID':1},
							dataType:"json",
							success : function(data)
							{
											if(data.success == 1){window.location.reload();}	else	{alert('Error In Api...');}
							},
							error: function(res)    {console.log(res);} 	
							});
						}
					}
				});
										
				$(".TPicker").clockface({format: 'HH : mm'}).clockface('hide', '14:30');
			},
			error: function(res)    {console.log(res);} 	
			});
		}
	});
		
	var SwapsConfirmDialog = function (message,urlID,companyID,fID_1,fID_2,fID_3,fID_4,fID_5,fID_6)
	{
		$('<div></div>').appendTo('body')
		.html('<div><h6>'+message+'?</h6></div>')
		.dialog({
			modal: true, title: 'Swapping message', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () 
				{
					$.ajax({
						url : (urlID) + 'ajax/ajax_DBpopups.php',
						type:'POST',				
						data:{'request':fID_1,'spareID':fID_2,'companyID':companyID,'shiftsID':fID_3,'empID':fID_4, 'dateID':fID_5, 'spr_empID':fID_6, 'statusID':2},
						dataType:"json",
						success : function(data)
						{
							if(data.success == 1){window.location.reload();}	else	{alert('Error In Api...');}
						},
						error: function(res)    {console.log(res);} 	
					});
							   
					$(this).dialog("close");
				},
				No: function () 
				{
					$.ajax({
						url : (urlID) + 'ajax/ajax_DBpopups.php',
						type:'POST',				
						data:{'request':fID_1,'spareID':fID_2,'companyID':companyID,'shiftsID':fID_3,'empID':fID_4, 'dateID':fID_5, 'spr_empID':fID_6, 'statusID':1},
						dataType:"json",
						success : function(data)
						{
							if(data.success == 1){window.location.reload();}	else	{alert('Error In Api...');}
						},
						error: function(res)    {console.log(res);} 	
					});
					
					$(this).dialog("close");
				}
			},
			close: function (event, ui) {
				$(this).remove();
			}
		});
	};
	
	var SwapsBusesConfirmDialog = function (message,urlID,fID_1,companyID,fID_2,fID_3,fID_4,fID_5,fID_6,fID_7)
	{
		$('<div></div>').appendTo('body')
		.html('<div><h6>'+message+'?</h6></div>')
		.dialog({
			modal: true, title: 'Swapping message', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () 
				{
					$.ajax({
						url : (urlID) + 'ajax/ajax_DBpopups.php',
						type:'POST',				
						data:{'request':fID_1,'spareID':fID_2,'dateID':fID_3,'companyID':companyID ,'temp_busID':fID_4, 'temp_empID':fID_5, 'temp_avaiableID':fID_6, 'temp_timeID':fID_7, 'statusID':2},
						dataType:"json",
						success : function(data)
						{
							if(data.success == 1){window.location.reload();}	else	{alert('Error In Api...');}
						},
						error: function(res)    {console.log(res);} 	
					});
							   
					$(this).dialog("close");
				},
				No: function () 
				{
					$.ajax({
						url : (urlID) + 'ajax/ajax_DBpopups.php',
						type:'POST',				
						data:{'request':fID_1,'spareID':fID_2,'dateID':fID_3,'companyID':companyID,'temp_busID':fID_4, 'temp_empID':fID_5, 'temp_avaiableID':fID_6, 'temp_timeID':fID_7, 'statusID':1},
						dataType:"json",
						success : function(data)
						{
							if(data.success == 1){window.location.reload();}	else	{alert('Error In Api...');}
						},
						error: function(res)    {console.log(res);} 	
					});
					
					$(this).dialog("close");
				}
			},
			close: function (event, ui) {
				$(this).remove();
			}
		});
	};
	
	
	$("#SL_rtpyeID").on('click',function()
	{
		var recID = $("#SL_rtpyeID").val();
		recID = (isNaN(recID) || recID == '' || typeof recID === 'undefined') ? 0 : recID;
		
		if(parseInt(recID) == 8)	/* SICK - LEAVE TYPE */
		{
			RUN_AJAX_REPORTS_GRID('GET_MASTERS','11','SL_gridID');
		}
		else if(parseInt(recID) == 14)	/* DRIVER - NAME  */
		{
			RUN_AJAX_REPORTS_GRID('GET_EMPLOYEES','0','SL_gridID');
		}
		else if(parseInt(recID) == 9)	/* LEAVE - DURATION  */
		{
			var HTML = '';
            HTML += '<div class="col-xs-2">';
			HTML += '<label for="section">Duration <span class="Maindaitory">*</span></label><br />';
			HTML += '<input type="text" class="form-control decimal_places_2 numeric positive" maxlength="4"  id="requestID" name="requestID" ';
			HTML += 'placeholder="Enter Duration" required="required" style="text-align:center;">';
            HTML += '</div>';
			
			$("#SL_gridID").empty();	$("#SL_gridID").html(HTML);
		}
		else	{$("#SL_gridID").empty();}
	});
	
	var RUN_AJAX_REPORTS_GRID = function(request,frmID,fieldID)
	{
		$.ajax({
			url : '../ajax/ajax_reports.php',
			type:'POST',				
			data:{'request':request , 'frmID':frmID},
			dataType:"json",
			success : function(data)
			{
				$("#"+fieldID).empty();
				$("#"+fieldID).html(data.result);
			},
			error: function(res)    {console.log(res);} 	
		   });
	}
	
    $("#n_codeID").on('change',function()
    {
        var codeID = $("#n_codeID").val();
		
        if(codeID != '')
        {
            $.ajax({
            url : '../ajax/ajax-n.php',
            type:'POST',				
            data:{'request':'TR_DUPLICACY' , 'codeID':codeID},
            dataType:"json",
            success : function(data)
            {
                if(data.countID > 0)
                {
                    alert('Employee Code : '+codeID+', Already exist in our database...');
                    $("#n_codeID").val('');
                }
            },
            error: function(res)    {console.log(res);}
            });
        }
    });
	
	$("#shift_setter_clearID").on('click',function()	{$("#shift_setter_gridID").empty();	$("#AllSetterGridsID").empty();});
	
    $("#shift_setter_requestID").on('click',function()
    {
		var stypeID = $("#SS_stypeID").val();
		stypeID = (isNaN(stypeID) || stypeID == '' || typeof stypeID === 'undefined') ? 0 : stypeID;
		
		if(parseInt(stypeID) > 0)
		{
	        $.ajax({
			url : '../ajax/ajax-n.php',
			type:'POST',
			data:{'request':'GET_SHIFT_SETTERS' ,'stypeID' : stypeID},
			dataType:"json",
			success : function(data)
			{
				$("#shift_setter_gridID").empty();
				$("#AllSetterGridsID").empty();
				$("#shift_setter_gridID").html(data.records);
				$('.datepicker').datepick();
				
				PREVIEW_ALL_SHIFTS();
				
				$(".updateSetterID").click(function()
				{
					var serialID = $(this).attr('aria-sort');
					var setterID = $(this).attr('aria-busy');
					var changeDT = $("#changeDT_" + serialID).val();
					var currentDT = $("#currentDT_" + serialID).val();
					
					if((parseInt(serialID) > 0) && (parseInt(setterID) > 0) && (changeDT != ''))
					{
						$.ajax({
							url : '../ajax/ajax-n.php',
							type:'POST',
							data:{'request':'UPDATE_SHIFT_SETTERS' , 'stypeID':serialID , 'reqID':setterID , 'changeDT' : changeDT , 'currentDT' : currentDT},
							dataType:"json",
							success : function(data)
							{
								if(data.statusID == 1)
								{
									$("#shift_setter_gridID").empty();
									$("#AllSetterGridsID").empty();
									
									$("#changeDT_" + serialID).prop('readonly',true);
									$("#buttonST_" + serialID).prop('disabled',true);
									
									PREVIEW_ALL_SHIFTS();
									
									alert('Update Done !....');
								}
								else if(data.statusID == 2)
								{
									alert('A Header Sheet already exist with this application date. Please choose another date.');
									$("#changeDT_" + serialID).val('');
								}
								else
								{
									alert('Error In API ........');
									$("#changeDT_" + serialID).val('');
								}
							},
							error: function(res)    {console.log(res);}
						});
					}
					else
					{
						alert("Enter Change Date !..");
						$("#changeDT_" + serialID).focus();
					}
					
				});
			},
			error: function(res)    {console.log(res);}
			});
		}
    });
	
	
	var PREVIEW_ALL_SHIFTS = function()
	{
		
		$.ajax({
		url : '../ajax/ajax-n.php',
		type:'POST',
		data:{'request':'GET_ALL_SHIFT_SETTERS'},
		dataType:"json",
		success : function(data)
		{
			$("#AllSetterGridsID").empty();
			$("#AllSetterGridsID").html(data.records);
			
			$.datable();
			
			$(".delete_setter_log").click(function()
			{
				var shiftID = $(this).attr('aria-sort');
				var message = 'Are you sure want to delete this record ';
				
				//alert(shiftID);
				
				$('<div></div>').appendTo('body')
				.html('<div><h6>'+message+'?</h6></div>')
				.dialog({
					modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
					width: 'auto', resizable: false,
					buttons: {
						Yes: function () 
						{
							//$('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');
							
							if(shiftID != '')
							{
								$.ajax({			
										url : '../ajax/ajax_delete.php',
										type:'POST',
										data:{'request': 'Shift_Setter_Log' , 'ID':shiftID},
										dataType:"json",				  
										success : function(data)			
										{
											if(parseInt(data.Counts) > 0)   
											{
												alert(String(data.Msg));
											}
											else if(data.Status == 1)       
											{
												//alert(String(data.Msg));
												
												PREVIEW_ALL_SHIFTS();
												
												//$(this).closest('tr').slideUp('slow').remove();
												//location.reload();
											}
										},
										error: function(res)  {console.log('ERROR in Form')}				  
								});	
							}
							
							$(this).dialog("close");
						},
						No: function () {                                                                 
							//$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');			
							$(this).dialog("close");
						}
					},
					close: function (event, ui) {
						$(this).remove();
					}
				});
			});
			 
		},
		error: function(res)    {console.log(res);}
		});
	}
	
	
});
