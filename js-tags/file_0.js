$(document).ready(function()
{ 
	$('form').attr('autocomplete', 'off');

    /********************  GLOBAL VARIABLED  *******************************/
    var globalID = 0; 
    globalID = $("#fieldID").val(); 
    globalID = (isNaN(globalID) || globalID == '' || typeof globalID === 'undefined') ? 0 : globalID;
	
	var allocationID = 0; 
    allocationID = $("#allocationID").val(); 
    allocationID = (isNaN(allocationID) || allocationID == '' || typeof allocationID === 'undefined') ? 0 : allocationID;
    /********************  GLOBAL VARIABLED  *******************************/
	
	
	/** CHECKING-FORM-DUPLICACY-CASE **/
	
	$("#rptno,#EcodeID,#sldateID,#infrefno,#accrefno,#cmprefno").on('keyup',function()	{$("#DuplicateText").text('');});
	
	/** INCIDENT FORM DUPLICACY **/
	$("#increfno,#inccmrno,#plrefnoID,#ptarefNO").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['increfno']  = $("#increfno").val();
		arrVAL['inccmrno']  = $("#inccmrno").val();
		arrVAL['plrefnoID'] = $("#plrefnoID").val();
		arrVAL['ptarefNO']  = $("#ptarefNO").val();		
		arrVAL['transID']  = $("#transID").val();
		frmVAL.push(arrVAL);
		RunTransactionDuplicacy('Check_Incident',frmVAL,'Ref No');
	});
	
	/** COMPLAINT FORM DUPLICACY **/
	$("#cmprefno").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['cmprefno'] = $("#cmprefno").val();
		arrVAL['transID']  = $("#transID").val();
		frmVAL.push(arrVAL);
		RunTransactionDuplicacy('Check_Complaint',frmVAL,('Comment Line Ref No : ' + arrVAL['cmprefno']));
	});
	
	/** ACCIDENT FORM DUPLICACY **/
	$("#accrefno").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['accrefno'] = $("#accrefno").val();
		arrVAL['transID']  = $("#transID").val();
		frmVAL.push(arrVAL);
		RunTransactionDuplicacy('Check_Accident',frmVAL,('Ref No : ' + arrVAL['accrefno']));
	});
	
	/** INFRINGMENT FORM DUPLICACY **/
	$("#infrefno").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['infrefno'] = $("#infrefno").val();
		arrVAL['transID']  = $("#transID").val();
		frmVAL.push(arrVAL);
		RunTransactionDuplicacy('Check_Infringment',frmVAL,('Infringement No : ' + arrVAL['infrefno']));
	});
	
	/** INSPECTION FORM DUPLICACY **/
	$("#rptno").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['rptno'] = $("#rptno").val();
		arrVAL['transID']  = $("#transID").val();
		frmVAL.push(arrVAL);
		RunTransactionDuplicacy('Check_Inspection',frmVAL,('Report No : ' + arrVAL['rptno']));
	});
	
	/** EMPLOYEE FORM DUPLICACY **/
	$("#EcodeID").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['ecodeID'] = $("#EcodeID").val();
		arrVAL['transID']  = $("#transID").val();		
		frmVAL.push(arrVAL);
		RunTransactionDuplicacy('Check_EmployeeCode',frmVAL,('Employee Code : ' + arrVAL['ecodeID']));
	});
	
	/** SICK-LEAVE FORM DUPLICACY **/
	$("#sldateID").on('focusout',function()
	{
		var arrVAL = {};	var frmVAL = [];
		arrVAL['sldateID'] = $("#sldateID").val();
		arrVAL['transID']  = $("#transID").val();		
		arrVAL['empID']    = $("#empID").val();
		arrVAL['lvtypeID'] = $("#lvtypeID").val();
		frmVAL.push(arrVAL);		
		RunTransactionDuplicacy('Check_SickLeave',frmVAL);
	});
	
	$("#empID,#lvtypeID").on('change',function()
	{	
		var arrVAL = {};	var frmVAL = [];
		arrVAL['sldateID'] = $("#sldateID").val();
		arrVAL['transID']  = $("#transID").val();		
		arrVAL['empID']	   = $("#empID").val();
		arrVAL['lvtypeID'] = $("#lvtypeID").val();
		frmVAL.push(arrVAL);		
		RunTransactionDuplicacy('Check_SickLeave',frmVAL);
	});
	
	var RunTransactionDuplicacy = function(request,arrID,captionTX)
	{
		if(arrID != '' && request != '')
		{
			$.ajax({
				url : '../ajax/ajax_repeat.php',
				type:'POST',				
				data:{'arrID': arrID , 'request':request},
				dataType:"json",
				success : function(data)
				{
					$("#DuplicateText").text('');
					if(data.result >= 1)
					{
						alert('Already Exists in Our Database !...');						
						$("#DuplicateText").text((captionTX != '' ? captionTX + ' , ' : '') + 'Already Exists in Our Database !...');
						Unset_formfields(data.textBX,data.dropBX);
					}
				},
				error: function(res)	{console.log(res);}				
			});
		}
	}
	
	
	var Unset_formfields = function(textBX,dropBX)		
	{
		if(textBX != '')
		{
			var textBX_field = textBX.split(",");
			
			for(textID = 0; textID <= parseInt(textBX_field.length); textID++)
			{
				$("#"+textBX_field[textID]).val('');
			}
		}
		
		if(dropBX != '')
		{
			var dropBX_field = dropBX.split(",");
			
			for(dropID = 0; dropID <= parseInt(dropBX_field.length); dropID++)
			{
				$("#"+dropBX_field[dropID]).prop("selectedIndex", 0);
				$("#"+dropBX_field[dropID]).prop("selectedIndex", 0).trigger('change');
			}
		}
	}
	
	$(".empInfo").on('click',function()
	{
		var empID = $("#empID").val();
		empID = (isNaN(empID) || empID == '' || typeof empID === 'undefined') ? 0 : empID;
		
		if(empID > 0)
		{
			$.ajax({
				url : '../ajax/ajax_audits.php',
				type:'POST',				
				data:{'request':'viewEmployeeInfos' , 'ID' : empID},
				dataType:"json",
				success : function(data)
				{
					$('#audits_modal h4').html('<b style="color:red;">Employee Personal Info</b>');
					$('#audits_modal #modal_data').html(data.file_info);
					$('#audits_modal').modal('show'); 					
				},
				error: function(res)    {console.log(res);} 	
			   });
		}
		else	{alert('Please Select the employee name ...');}
	});
	
	$("#cmltypeID").on('change',function()
	{
		var cmltypeID = $(this).val(); 
		cmltypeID = (isNaN(cmltypeID) || cmltypeID == '' || typeof cmltypeID === 'undefined') ? 0 : cmltypeID;
		
		$("#cmprefno").val('');
		$("#cmprefno").prop('readonly',((cmltypeID == 491 || cmltypeID == 492) ? false : true));
		$("#cmprefno").prop('required',((cmltypeID == 491 || cmltypeID == 492) ? true : false));
		$("#cmprefno").val(((cmltypeID == 491 || cmltypeID == 492) ? '' : 'PH-'));
	});
	
	$("#uroleID").on('change',function()
	{
		var uroleID = $(this).val();
		uroleID = (isNaN(uroleID) || uroleID == '' || typeof uroleID === 'undefined') ? 0 : uroleID;
		
		if(parseInt(uroleID) > 0)
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',
				data:{'uroleID': uroleID , 'request':'GET_userroles_sheet'},
				dataType:"json",
				success : function(data)
				{
					$("#users_forms_detailsID").empty();
					$("#users_forms_detailsID").html(data.formFields);
					
					$('input[type="checkbox"]').on('click',function(e)
					{
						var chk = $(this).attr("id"); 
						var arr = chk.split("-",2);

						if(arr[1] === 'all')
						{
							var c = $(this).is(':checked');
							if(c) {$("#"+arr[0]+"-del, #"+arr[0]+"-add, #"+arr[0]+"-edit, #"+arr[0]+"-view").prop('checked',true);}	
							else  {$("#"+arr[0]+"-del, #"+arr[0]+"-add, #"+arr[0]+"-edit, #"+arr[0]+"-view").prop('checked',false);}
						}
						else
						{ 
							var d  = $(this).is(':checked');
							var ID = arr[0] + "-all";
							
							if(d) 
							{
								var counter = 0; 
								if(($("#" + arr[0] + "-del").is(":checked")))  counter ++;
								if(($("#" + arr[0] + "-add").is(":checked")))  counter ++;
								if(($("#" + arr[0] + "-edit").is(":checked"))) counter ++;
								if(($("#" + arr[0] + "-view").is(":checked"))) counter ++;
				
								if(counter == 4)	{$("#" + ID + "").prop('checked',true);}
								else                    {$("#" + ID + "").prop('checked',false);}
							}
							else    {$("#" + ID + "").removeAttr('checked');}
						}
					});
				},
				error: function(res)	{console.log(res);}				
			}); 
		}
		else	{$("#users_forms_detailsID").empty();}
	});
	
    /* START - HIDE DEVELOPER TOOLS */
    /*if(($("#currentID").val()) != 1)
    {
        $(document).keydown(function(e) {if(e.which === 123) {return false;}});    
        $(document).bind("contextmenu",function(e)  {e.preventDefault();});
        $(document).keydown(function(e)
        {
            var pressedKey = String.fromCharCode(event.keyCode).toLowerCase();
            if (event.ctrlKey && (pressedKey == "u")) {event.returnValue = false;}
        });
    }*/
	
    /* START - HIDE DEVELOPER TOOLS */
    if(allocationID == 111)
	{
		/* Do - NOTHINGS */
	}
	else
	{
        $('input[type="checkbox"]').on('click',function(e)
        {
			var chk = $(this).attr("id"); 
			var arr = chk.split("-",2);

			if(arr[1] === 'all')
			{
				var c = $(this).is(':checked');
				if(c) {$("#"+arr[0]+"-del, #"+arr[0]+"-add, #"+arr[0]+"-edit, #"+arr[0]+"-view").prop('checked',true);}	
				else  {$("#"+arr[0]+"-del, #"+arr[0]+"-add, #"+arr[0]+"-edit, #"+arr[0]+"-view").prop('checked',false);}
			}
			else
			{ 
				var d  = $(this).is(':checked');
				var ID = arr[0] + "-all";
				
				if(d) 
				{
					var counter = 0; 
					if(($("#" + arr[0] + "-del").is(":checked")))  counter ++;
					if(($("#" + arr[0] + "-add").is(":checked")))  counter ++;
					if(($("#" + arr[0] + "-edit").is(":checked"))) counter ++;
					if(($("#" + arr[0] + "-view").is(":checked"))) counter ++;
	
					if(counter == 4)	{$("#" + ID + "").prop('checked',true);}
					else                    {$("#" + ID + "").prop('checked',false);}
				}
				else    {$("#" + ID + "").removeAttr('checked');}
			}
        });
	}
	
	$("#3partyID,#insinvolvedID").on('click',function()
	{
		var checkBox_1 = $("#3partyID").prop('checked');
		var checkBox_2 = $("#insinvolvedID").prop('checked');
		
		if(checkBox_1 == true || checkBox_2 == true)  {$("#invnoID").prop('readonly',false);	$("#invnoID").prop('required',true);}
		else                       					  {$("#invnoID").prop('readonly',true); 	$("#invnoID").prop('required',false);}
	});
	
	$("#3partyID").on('click',function()
	{
            if($(this).is(':checked'))  {$("#thpnameID,#regisnoID,#thcontactID").prop('readonly',false);	$("#thpnameID,#regisnoID,#thcontactID").prop('required',true);}
            else                        {$("#thpnameID,#regisnoID,#thcontactID").prop('readonly',true); 	$("#thpnameID,#regisnoID,#thcontactID").prop('required',false);}
	});
	
	$("#insinvolvedID").on('click',function()
	{
            if($(this).is(':checked'))  {$("#insurerID,#claimnoID").prop('readonly',false);	$("#insurerID,#claimnoID").prop('required',true);}
            else                        {$("#insurerID,#claimnoID").prop('readonly',true); 	$("#insurerID,#claimnoID").prop('required',false);}
	});
	
	$("#witnessID").on('click',function()
	{
            if($(this).is(':checked'))  {$("#witnessName,#witnessContact").prop('readonly',false);	$("#witnessName,#witnessContact").prop('required',true);}
            else                        {$("#witnessName,#witnessContact").prop('readonly',true); 	$("#witnessName,#witnessContact").prop('required',false);}
	});
	
	$("#damagetobusID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 1)	{$("#rprcost").prop('readonly',false);	$("#rprcost").prop('required',true);	$("#rprcost").val('');}
		else						{$("#rprcost").prop('readonly',true);	$("#rprcost").prop('required',false);	$("#rprcost").val(0);}
	});
	
	$("#inftypeID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 162)	{$("#description").attr('disabled',false);	$("#description").val('');}
		else						  {$("#description").attr('disabled',true);	$("#description").val('');}
	});
	
	$("#resultsINV").on('change',function()
	{
		var resultsINV = $(this).val();
		resultsINV = (isNaN(resultsINV) || resultsINV == '' || typeof resultsINV === 'undefined') ? 0 : resultsINV;
		
		if(parseInt(resultsINV) == 8000)	{$("#otherINV").attr('readonly',false);	$("#otherINV").val('');}
		else							    {$("#otherINV").attr('readonly',true);	$("#otherINV").val('');}
	});
	
	$("#apID").on('change',function()
	{
		if($(this).is(':checked'))	{$("#mealID").val('');	$("#mealID").val('Not Applicable');	$("#mealID").attr('readonly',true);}
		else						  {$("#mealID").val('');	$("#mealID").attr('readonly',false);}
	});
	
	$("#stime_1,#etime_1").on('keyup',function()	{CalculateHoursMinutes($("#stime_1").val(),$("#etime_1").val(),"#hours_1");});	
	$("#stime_2,#etime_2").on('keyup',function()	{CalculateHoursMinutes($("#stime_2").val(),$("#etime_2").val(),"#hours_2");});
		
	$("#stime_1,#etime_1,#stime_2,#etime_2").on('change',function()	
	{
		CalculatePerDayHours($("#hours_1").val(),$("#hours_2").val());
		CalculateWeeklyHours($("#hours_days").val(),$("#fdayID").val(),$("#tdayID").val());
	});

	$("#fdayID,#tdayID").on('change',function()	
	{
		CalculateWeeklyHours($("#hours_days").val(),$("#fdayID").val(),$("#tdayID").val());
	});
	
	$("#stime_1,#etime_1,#stime_2,#etime_2,#hours_days,#hours_1,#hours_2,#hours_days").on('focusout',function()	
	{
		CalculateWeeklyHours($("#hours_days").val(),$("#fdayID").val(),$("#tdayID").val());
	});
	
	var CalculateHoursMinutes = function(timeID_1,timeID_2,hoursID)
	{
		if((timeID_1 != '') && (timeID_2 != ''))
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',				
				data:{'timeID_1': timeID_1 , 'timeID_2': timeID_2 , 'request':'CalculateHours'},
				dataType:"json",
				success : function(data)
				{ 
					$(hoursID).empty();
					$(hoursID).val(data.result);
				},
				error: function(res)	{console.log(res);}				
			});
		}
		else	{$(hoursID).empty();}
	}
	
	var CalculatePerDayHours = function(hoursID_1,hoursID_2)
	{
		if((hoursID_1 != '') && (hoursID_2 != ''))
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',				
				data:{'timeID_1': hoursID_1 , 'timeID_2': hoursID_2 , 'request':'CalculatePerDayHours'},
				dataType:"json",
				success : function(data)
				{ 
					$("#hours_days").empty();
					$("#hours_days").val(data.result);
				},
				error: function(res)	{console.log(res);}				
			});
		}
		else	{$("#hours_days").empty();}
	}
	
	var CalculateWeeklyHours = function(hoursID,fdayIID,tdayIID)
	{
		if((hoursID != '') && (fdayIID != '') && (tdayIID != ''))
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',				
				data:{'hoursID': hoursID , 'fdayIID': fdayIID , 'tdayIID': tdayIID , 'request':'CalculateWeeklyHours'},
				dataType:"json",
				success : function(data)
				{ 
					$("#hours_week").empty();
					$("#hours_week").val(data.result);
				},
				error: function(res)	{console.log(res);}				
			});
		}
		else	{$("#hours_week").empty();}
	}
	
	$("#fname,#lname").on('keyup',function()	
	{
            var fname = $("#fname").val();
            var lname = $("#lname").val();
            $("#full_name").val((fname + ' ' + lname));
	});
	
	
	change_layout();
	change_skin("skin-black");
	
        /*$(".datepicker").datetimepicker({format: 'DD/MM/YYYY'}).on('changeDate',function(){ });*/
	/*$(".datepicker").datetimepicker({format: 'DD/MM/YYYY'}).on('changeDate',function(){ });
	$(".filters").datetimepicker({format: 'DD/MM/YYYY'});*/
	
	$(".textarea").wysihtml5();
	
	var pageID = document.location.pathname.match(/[^\/]+$/)[0];	
	if(pageID.substring(0, 3) == 'rpt')
	{
		$('select[multiple].active.3col').multiselect({
			columns: 2,
			placeholder: 'Select Fields',
			search: true,
			searchOptions: {
				'default': 'Search Fields'
			},
			selectAll: true
		});		
	}
	else
	{
		var MultiSelectActivate = function() {$("").multiselect({noneSelectedText:'Select option'}).multiselectfilter();};	
		$("#wdayID,#offenceID,#serviceID,#mcompanyID,#FLT_companyID,#companyFL,#rpcompanyID,#rpt_fieldID").multiselect({noneSelectedText:'Select option'}).multiselectfilter();	
		MultiSelectActivate();
	}
	
	$('#dataTable').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": (globalID == 101 ? true: false),
            "bSort": (globalID == 101 ? true: false),
            "bInfo": true,
            "bAutoWidth": true
        });
	
	$('button.remove').click(function(){
			removeRow($(this));
			return false;
	});
	
});

function createURL(fileName,action,id)
{
	id 		=	(typeof id === 'undefined') ? '' : id; 
	action	=	(typeof action === 'undefined') ? '' : action;  
	var URL = fileName 
	URL		+=	(action  == '') ? '' :	'?action='+action;
	URL 	+= 	(id == '') ? ''	: '&id=' + id ;
	window.location	= URL;
}