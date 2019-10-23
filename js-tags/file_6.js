$(document).ready(function()
{
	createSigonUtility('onLoad');

	setInterval('refreshPage()', 1800000);

	setInterval('createSigonUtility("onTimer")', 60000);

	$(".depotID").on('click',function()
	{
		$("#wrk_loads").addClass('wrk_loads');
		$(".wrk_loads").fadeIn(1000).show(1000);
			createSigonUtility('onTimer');
		$(".wrk_loads").fadeOut(2500).hide(2500);
	});
});
	
	function refreshPage()  
    {
		window.location.reload(true);
    } 
	
	function createSigonUtility(caseTX)
	{
		var depotVAL = [];
		$('.depotID:checked').each(function()	{depotVAL.push($(this).val());});
		
		if(depotVAL != '')
		{
			$.ajax({
				url : 'ajax/ajax_drivers.php',
				type:'POST',				
				data:{'request': 'checkSigonUtility' , 'caseTX': caseTX , 'depotVAL': depotVAL , 'last_pendingID': $("#tabID_1").val() , 'last_completeID': $("#tabID_2").val()},
				dataType:"json",
				success : function(data)
				{
					$(".shiftcomments").prop('tabIndex', -1);
					$(".othersinfos").prop('tabIndex', -1);

					//$.getScript( "js/dragdrop/redips-drag-min.js", function( data, textStatus, jqxhr ) {});
					//$.getScript( "js/dragdrop/script.js", function( data, textStatus, jqxhr ) {});

					if(data.sigonPending == 1)
					{
						$("#tabID_1").val(data.pendingData);
						
						$("#captionID_1").text(data.pendingData);
						$("#redips-drag").html('');
						$("#redips-drag").html(data.pendingList); 
					}
					
					if(data.sigonAssigned == 1)
					{
						$("#tabID_2").val(data.assignedData);
						
						$("#captionID_2").text(data.assignedData);
						$("#tab_7-7").html('');
						$("#tab_7-7").html(data.assignedList);
					}

					if(data.sigonPending == 1 || data.sigonAssigned == 1)
					{
						$(".shifts_doneID").unbind("click");
						$(".swipe_modelID").unbind("click");
						$(".swipe_undoID").unbind("click");
						$(".chopped_undoID").unbind("click");

						/* ---- STARTING --  SHIFT STATUS - CONFIRM*/
						$(".shifts_doneID").click(function()
						{
							var recID = $(this).attr('aria-sort');
							var stsID = $(this).attr('aria-busy');
							var empNM = $(this).attr('aria-title');
							var shfNO = $(this).attr('aria-label');
							var titSTRING = (stsID == 2 ? 'Shift Signon Confirmation' :(stsID == 1 ? 'Signon Undo Confirmation' : 'Shifts Confirmation'));						
							var msgSTRING = 'Do you realy want to change the Sign-On status of <br /><br /><b style="color:blue; font-size:15px;">' + empNM + '</b> on shift no : <b style="color:red; font-size:15px;">' + shfNO + '</b>';

							ShiftSigonConfirm(msgSTRING,titSTRING,recID,stsID);
							
						}); 
						/* ---- ENDING --  SHIFT STATUS - CONFIRM*/

						/* ---- STARING --  EMPLOYEE/BUS SPARE - ALLOCATIONS */
						$(".swipe_modelID").click(function()
						{
							var resuID = $(this).attr('aria-sort');
							var returnID = resuID.split('_');
							var pageID = document.location.pathname.match(/[^\/]+$/)[0];
							var urlID = (pageID == 'drvsigon.php' || pageID == 'profile_4.php' ? '' : '../');
							
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
																	if(data.success == 1)	{createSigonUtility('updateSpares');}	else	{alert('Error In Api...');}
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
																SwapsBusesConfirmDialogBox('Are you sure that you want to update same shift '+retID[4]+' In B - PART.',urlID,request,retID[5],retID[0],retID[1],temp_busID,temp_empID,temp_avaiableID,temp_timeID);
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
																	if(data.success == 1)	{createSigonUtility('updateSpares');}	else	{alert('Error In Api...');}
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
												SwapsBusesConfirmDialogBox('Are you sure that you want to update same shift '+retID[4]+' In B - PART.',urlID,request,retID[5],retID[0],retID[1],temp_busID,temp_empID,temp_avaiableID,temp_timeID);
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
														if(data.success == 1)	{createSigonUtility('updateSpares');}	else	{alert('Error In Api...');}
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
													if(data.success == 1)	{createSigonUtility('updateSpares');}	else	{alert('Error In Api...');}
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

												SwapsConfirmDialogBox('Are you sure that you want to update same shift '+retID[6]+' In B - PART.',urlID,retID[7],send_request,send_spareID,send_shiftsID,send_empID,send_dateID,send_spr_empID);
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
													if(data.success == 1){createSigonUtility('updateSpares');}	else	{alert('Error In Api...');}
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
						/* ---- ENDING --  EMPLOYEE/BUS SPARE - ALLOCATIONS */
	 
						
						/* ---- STARTING --  UNDO-SPARE-ALLOCATION */	
						$(".swipe_undoID").confirm
						({
							title:"<b style='color:#1591E0;'>Swap confirmation</b>",
							text:"Do you really want to undo the change? ",
							confirm: function(button) 
							{
								var resultID = $(button).attr('aria-sort');
								var returnID = resultID.split('_');
								var pageID = document.location.pathname.match(/[^\/]+$/)[0];
								var urlID = (pageID == 'drvsigon.php' || pageID == 'profile_4.php' ? '' : '../');

								$.ajax({
										url : (urlID) + 'ajax/ajax_DBpopups.php',
										type:'POST',				
										data:{'request': 'UNDO_' + returnID[0], 'dateID': returnID[1], 'changesID': returnID[2], 'empID': returnID[3], 'recID': returnID[4]},
										dataType:"json",
										success : function(data)
										{
											if(data.success == 1)       {createSigonUtility('undoSpares');}
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
						/* ---- ENDING --  UNDO-SPARE-ALLOCATION */	

						/* ---- STARTING --  CHOPPED */	
						$(".chopped_undoID").confirm
						({
							title:"<b style='color:#1591E0;'>Swap confirmation</b>",
							text:"Do you really want to undo the change? ",
							confirm: function(button) 
							{
								var recID = $(button).attr('aria-sort');
								var pageID = document.location.pathname.match(/[^\/]+$/)[0];
								var urlID = (pageID == 'drvsigon.php' || pageID == 'profile_4.php' ? '' : '../');

								$.ajax({
										url : (urlID) + 'ajax/ajax_DBpopups.php',
										type:'POST',				
										data:{'request': 'UNDO_CHOPPED', 'recID': recID},
										dataType:"json",
										success : function(data)
										{
											if(data.success == 1)	{createSigonUtility('undoChopped');}
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
						/* ---- ENDING --  CHOPPED */	
						
	
						/*$.cachedScript("../js/dragdrop/redips-drag-min.js").done(function( script, textStatus ) {
						  console.log( textStatus );
						});
						
						$.cachedScript("../js/dragdrop/script.js").done(function( script, textStatus ) {
						  console.log( textStatus );
						});*/
					}

				 
				},
				error: function(res)	{console.log(res);}				
				});			
		}
		else
		{
			alert('Plz Specify the depot : ');
		}
		//console.clear();
	}


	/* ---- STARTING --  SHIFT COMMENTS/ON ROAD C/O */
	function updateMASTERSoptions(valueTEXT,recID,fieldNM)
	{
		if(parseInt(recID) > 0 && fieldNM != '')
		{		
			$.ajax({			
			url : 'ajax/ajax_DBpopups.php',
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
	/* ---- ENDING --  SHIFT COMMENTS/ON ROAD C/O */

	/* ---- STARTING --  SHIFT STATUS CONFIRMATIONS */
	function ShiftSigonConfirm(msgSTRING,titSTRING,recID,stsID) 
	{
		$('<div></div>').appendTo('body')
		.html('<div><h6>'+msgSTRING+'</h6></div>')
		.dialog({
			modal: true, title: titSTRING, zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Yes: function () 
				{
					if(recID != '' && stsID != '')
					{
						$.ajax({
						url : 'ajax/ajax_DBpopups.php',
						type:'POST',
						data:{'request': 'SHIFT_STATUS_CONFIRM' , 'recID':recID , 'stsID':stsID},
						dataType:"json",				  
						success : function(data)			
						{
							if(data.success == 1)	{createSigonUtility();}
					
						},	error: function(res)  {console.log('ERROR in Form')}
						});
					}
					
					$(this).dialog("close");
				},
				No: function () 
				{ 
					$(this).dialog("close");
				}
			},
			close: function (event, ui) 
			{
				$(this).remove();
			}
		});
	}
	/* ---- ENDING --  SHIFT STATUS CONFIRMATIONS */

	/* ---- STARTING --  EMPLOYEE/BUS SPARE ALLOCATION DIALOG-BOX */
	var SwapsConfirmDialogBox = function (message,urlID,companyID,fID_1,fID_2,fID_3,fID_4,fID_5,fID_6)
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
							if(data.success == 1){createSigonUtility();}	else	{alert('Error In Api...');}
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
							if(data.success == 1){createSigonUtility();}	else	{alert('Error In Api...');}
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
	/* ---- ENDING --  EMPLOYEE/BUS SPARE ALLOCATION DIALOG-BOX */

	/* ---- STARING --  EMPLOYEE/BUS SPARE ALLOCATION DIALOG-BOX-PART-2 */
	var SwapsBusesConfirmDialogBox = function (message,urlID,fID_1,companyID,fID_2,fID_3,fID_4,fID_5,fID_6,fID_7)
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
	/* ---- ENDING --  EMPLOYEE/BUS SPARE ALLOCATION DIALOG-BOX-PART-2 */