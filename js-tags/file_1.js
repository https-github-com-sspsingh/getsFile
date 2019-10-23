$(document).ready(function()
{
	$("#visatypeID").on('change',function()
    {
        var visatypeID = $(this).val();
		
		if(parseInt(visatypeID) == 1)
		{
			$("#visaDetails,#workingResc").val('');	$(".visa_emp_DIV").show();
		}
		else
		{
			$("#visaDetails,#workingResc").val('');	$(".visa_emp_DIV").hide();
		}
    });
	
    $("#suburbID").on('focusout',function()
    {
        var codeID = $(this).attr('aria-sort');
        
        $("#pincode").val('');
        $("#pincode").val(codeID);
    });
	
	$("#dayNM,#dateID,#shiftNO,#companyID").on('click',function()
	{
		var sldateID = $("#dateID").val();
		var masterID = '';

		if(sldateID != '')
		{
			$.ajax({
			url : '../ajax/ajax.php',
			type:'POST',				
			data:{'reqID': sldateID , 'request' : 'GET_DayID'},
			dataType:"json",
			success : function(data)
			{ 
				$("#dayID,#dayNM").val('');
				$.each(data.result,function(index,value)
				{
					$("#dayID").val(index);
					$("#dayNM").val(value);
				})
			},
			error: function(res)	{console.log(res);}				
			});
		}
	});
	
	$("#dayNM,#lvtypeID,#duration").on('click',function()
	{
            var sldateID = $("#sldateID").val();
            var masterID = '';

            if(sldateID != '')
            {
                $.ajax({
                url : '../ajax/ajax.php',
                type:'POST',				
                data:{'reqID': sldateID , 'request' : 'GET_DayID'},
                dataType:"json",
                success : function(data)
                { 
                    $("#dayID,#dayNM").val('');
                    $.each(data.result,function(index,value)
                    {
                        $("#dayID").val(index);
                        $("#dayNM").val(value);
                    })
                },
                error: function(res)	{console.log(res);}				
                });
            }
	});
    
	$("#sldateID").on('focusout',function()
	{
            var sldateID = $(this).val();
            var masterID = '';

            if(sldateID != '')
            {
                $.ajax({
                url : '../ajax/ajax.php',
                type:'POST',				
                data:{'reqID': sldateID , 'request' : 'GET_DayID'},
                dataType:"json",
                success : function(data)
                { 
                    $("#dayID,#dayNM").val('');
                    $.each(data.result,function(index,value)
                    {
                        $("#dayID").val(index);
                        $("#dayNM").val(value);
                    })
                },
                error: function(res)	{console.log(res);}				
                });
            }
	});
	
	$("#fdateID").on('focusout',function()
	{
		var fdateID = $(this).val();
		if(fdateID != '')
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',				
				data:{'reqID': fdateID , 'request' : 'CHECK_WeekStartDay'},
				dataType:"json",
				success : function(data)
				{ 
					if(data.countID == 1)
					{
						alert('Please Select Valid Date of Monday !...');
						$("#tdateID,#fdateID").val('');
					}
					else	{$("#tdateID").val(data.tdateID);}
				},
				error: function(res)	{console.log(res);}				
			});
		}
		else	{$("#tdateID").val('');}
	});
	
	/*$("#EcodeID").on('focusout',function()	{run_ajax_check_duplicacy(($(this).val()),'Check_EmployeeCode','#EcodeID');});*/
	$("#ScodeID").on('keyup',function()	{run_ajax_check_duplicacy(($(this).val()),'Check_Shifts_Code','#ScodeID');});
	
	var run_ajax_check_duplicacy = function(reqID,request,filedID)
	{
		if((reqID != '') && (request != '') && (filedID != ''))
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',				
				data:{'reqID': reqID , 'request':request},
				dataType:"json",
				success : function(data)
				{ 
					if(data.result >= 1)
					{
						alert('Already Exists in Our Database !...');
						$(filedID).val('');
					}
				},
				error: function(res)	{console.log(res);}				
			});
		}
		else	{$(filedID).val('');}
	}
	
	$(".genscheduleID").click(function()
	{
		var fdateID = $("#fdateID").val();
		var tdateID = $("#tdateID").val();
		var scatgID = $("#scategoryID").val();
		scatgID = (isNaN(scatgID) || scatgID == '' || scatgID == 'null' || typeof scatgID === 'undefined') ? 0 : scatgID;
		
		if((fdateID != '') && (tdateID != '') && (parseInt(scatgID) > 0))
		{
			$.ajax({
				url : '../ajax/ajax_grid.php',
				type:'POST',				
				data:{'timeID_1': fdateID , 'timeID_2': tdateID , 'reqID': scatgID , 'request':'GenerateWeeklyScheduler'},
				dataType:"json",
				success : function(data)
				{ 
					$("#schedularDIV_ID").empty();
					$("#schedularDIV_ID").html(data.result);					
					$("#submit_buttonID").show();
				},
				error: function(res)	{console.log(res);}				
			});
		}
		else
		{
			$("#schedularDIV_ID").empty();
			alert('Weekly Roster Parameter Is Missing !..');
			
			set_focusingID("#fdateID",fdateID);
			set_focusingID("#tdateID",tdateID);
			set_focusingID("#scategoryID",scatgID); 
		}
	});
	
	var set_focusingID = function(fieldID,valueID)
	{
		console.log(fieldID + ' , ' + valueID);
		if(valueID != '' || valueID != 'null')	{}
		else 				 {$(fieldID).focus();}
	}
	
	
	$("#wtypeID").on('change',function()
	{
		var HTML = '';
		var wtypeID = $(this).val();
		wtypeID = (isNaN(wtypeID) || wtypeID == '' || typeof wtypeID === 'undefined') ? 0 : wtypeID;
		
		if(parseInt(wtypeID) == 1)
		{
			HTML += '<div class="col-xs-2">';
				HTML += '<label for="section">F. Day <span class="Maindaitory">*</span></label>';
				HTML += '<select name="fdayID" class="form-control" id="fdayID">';
					HTML += '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
					HTML += '<option value="1">Monday</option>';
					HTML += '<option value="2">Tuesday</option>';
					HTML += '<option value="3">Wednesday</option>';
					HTML += '<option value="4">Thursday</option>';
					HTML += '<option value="5">Friday</option>';
				HTML += '</select>';	
			HTML += '</div>';
			
			HTML += '<div class="col-xs-2">';
				HTML += '<label for="section">T. Day <span class="Maindaitory">*</span></label>';
				HTML += '<select name="tdayID" class="form-control" id="tdayID">';
					HTML += '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
				HTML += '</select>';	
			HTML += '</div>';
		}
		
		if(parseInt(wtypeID) == 2)
		{
			HTML += '<div class="col-xs-4">';
				HTML += '<label for="section">Week Day <span class="Maindaitory">*</span></label>'; 
				HTML += '<select name="wdayID[]" class="form-control" id="wdayID" multiple="multiple">';
					HTML += '<option value="1">Monday</option>';
					HTML += '<option value="2">Tuesday</option>';
					HTML += '<option value="3">Wednesday</option>';
					HTML += '<option value="4">Thursday</option>';
					HTML += '<option value="5">Friday</option>';
					HTML += '<option value="6">Saturday</option>';
					HTML += '<option value="7">Sunday</option>';
				HTML += '</select>';	
			HTML += '</div>';
		}
		
		$("#working_rangeID").empty();
		$("#working_rangeID").html(HTML);
		$("select#wdayID").multiselect({noneSelectedText:'Select option'}).multiselectfilter();
		
		$("#fdayID").on('change',function()
		{
			var fdayID = $(this).val();
			fdayID = (isNaN(fdayID) || fdayID == '' || typeof fdayID === 'undefined') ? 0 : fdayID;
			
			if(parseInt(fdayID) > 0)
			{
				var HT_ML = '';
				HT_ML += '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
				
				var fdayTX = '';
				
				for(fdayID > 0; fdayID <= 5; fdayID++)
				{
					fdayTX = fdayID == 1 ? 'Monday' :(fdayID == 2 ? 'Tuesday' :(fdayID == 3 ? 'Wednesday' :(fdayID == 4 ? 'Thursday' :(fdayID == 5 ? 'Friday' : ''))));
					
					HT_ML += '<option value="' + fdayID + '">'+ (fdayTX) +'</option>';
				}
				
				$("#tdayID").empty();	
				$("#tdayID").html(HT_ML);
			}
		});
		
		$("#wdayID").on('change',function()	
		{
			var stringID = $(this).val();
			CalculateSpecificDayHours($("#hours_days").val(),stringID);
		});
	});
	
	var CalculateSpecificDayHours = function(hoursID,stringID)
	{
		if((hoursID != '') && (stringID != ''))
		{
			$.ajax({
				url : '../ajax/ajax.php',
				type:'POST',				
				data:{'hoursID': hoursID , 'fdayIID': stringID , 'request':'CalculateSpecificDayHours'},
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
	
	$("#fdayID").on('change',function()
	{
		var fdayID = $(this).val();
		fdayID = (isNaN(fdayID) || fdayID == '' || typeof fdayID === 'undefined') ? 0 : fdayID;
		
		if(parseInt(fdayID) > 0)
		{
			var HT_ML = '';
			HT_ML += '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
			
			var fdayTX = '';
			
			for(fdayID > 0; fdayID <= 5; fdayID++)
			{
				fdayTX = fdayID == 1 ? 'Monday' :(fdayID == 2 ? 'Tuesday' :(fdayID == 3 ? 'Wednesday' :(fdayID == 4 ? 'Thursday' :(fdayID == 5 ? 'Friday' : ''))));
				
				HT_ML += '<option value="' + fdayID + '">'+ (fdayTX) +'</option>';
			}
			
			$("#tdayID").empty();	
			$("#tdayID").html(HT_ML);
		}
	});
	
	
	
	$("#empID").on('change',function()
	{
		$("#ecodeID").val($("#empID option:selected").attr('aria-sort'));
	});
	
	
	$("#FB_frmID").on('change',function()   
    {
        $("#tableFR").val($("#FB_frmID option:selected").attr('aria-sort'));
        $("#formNM").val($("#FB_frmID option:selected").attr('aria-title') + '.php');
    });
    
    
	$("#accID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 50 || parseInt(ageID) == 51 || parseInt(ageID) == 220 || parseInt(ageID) == 54)
		{
			$("#cmdiscID").prop('selectedIndex', 0);
			/*$("#invID").prop('disabled',false);
			$("#invdate").prop('readonly',false);*/
			$("#mcomments").prop('disabled',true);
			$("#wrtypeID").prop('disabled',true);
			$("#wrtypeID").prop('selectedIndex', 3);
			$(".partID_1").hide();  $(".partID_3").hide();  $(".partID_4").hide(); $(".partID_2").show();
		}
		else if(parseInt(ageID) == 48)
		{
			$("#cmdiscID").prop('selectedIndex', 0);
			$("#cmdiscID").prop('selectedIndex', 2);
			/*$("#invID").prop('disabled',true);
			$("#invdate").prop('readonly',true);*/
			$("#mcomments").prop('disabled',true);
			$("#wrtypeID").prop('disabled',true);
			$("#wrtypeID").prop('selectedIndex', 3);
			$(".partID_1").hide();  $(".partID_3").show();  $(".partID_4").show(); $(".partID_2").hide();
		}
		else if(parseInt(ageID) == 224)
		{
			$("#cmdiscID").prop('selectedIndex', 0);
			$("#cmdiscID").prop('selectedIndex', 1);
			/*$("#invID").prop('disabled',false);
			$("#invdate").prop('readonly',false);*/
			$("#mcomments").prop('disabled',false);
			$("#wrtypeID").prop('disabled',true);
			$("#wrtypeID").prop('selectedIndex', 3);
			$(".partID_1").hide();  $(".partID_3").show();  $(".partID_4").hide(); $(".partID_2").hide();
		}
		else
		{
			$("#cmdiscID").prop('selectedIndex', 0);
			/*$("#invID").prop('disabled',false);
			$("#invdate").prop('readonly',false);*/
			$("#mcomments").prop('disabled',true);
			$("#wrtypeID").prop('disabled',true);
			$("#wrtypeID").prop('selectedIndex', 0);
			$(".partID_1").show();  $(".partID_3").show();  $(".partID_4").show(); $(".partID_2").hide();
		}
	});
	
	$("#cmdiscID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 1)
		{
			$("#actbyID").prop('disabled',false);        $("#actbyDate").prop('readonly',false);
			/*$("#intvID").prop('disabled',false);        $("#intvDate").prop('readonly',false);*/
			$("#wrtypeID").prop('disabled',false);	   $("#mcomments").prop('disabled',false);
			$("#wrtypeID").prop('selectedIndex', 0);	  $("#mcomments").val('');
		}
		else
		{
			$("#actbyID").prop('disabled',true);        $("#actbyDate").prop('readonly',true);
			/*$("#intvID").prop('disabled',true);        $("#intvDate").prop('readonly',true);*/
			$("#wrtypeID").prop('disabled',true);		$("#mcomments").prop('disabled',true);
			$("#wrtypeID").prop('selectedIndex', 0);	  $("#mcomments").val('');
		}
	});

	$("#accID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 52 || parseInt(ageID) == 391)
		{
			$("#substanID").prop('disabled',false);		$("#faultID").prop('disabled',false);
			$("#substanID").prop('selectedIndex', 0);	  $("#faultID").prop('selectedIndex', 0);
		}
		else
		{
			$("#substanID").prop('disabled',true);	$("#faultID").prop('disabled',true);
			$("#substanID").prop('selectedIndex', 0);	  $("#faultID").prop('selectedIndex', 0);
		}
	});
	
	$("#substanID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		var HTML  = '';
		if((parseInt(ageID) == 1)) /* YES */
		{
			HTML += '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
			HTML += '<option value="1">At Fault - Driver</option>';
			HTML += '<option value="2">At Fault - Engineering</option>';
			HTML += '<option value="3">At Fault - Operations</option>'; 
			HTML += '<option value="4">Not At Fault</option>';
		}
		else if(parseInt(ageID) == 2) /* YES */
		{
			HTML += '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
			HTML += '<option value="4">Not Applicable</option>';
			HTML += '<option value="5">Not At Fault</option>';
		}
		else
		{
			HTML += '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
		}
		
		$("#faultID").empty();
		$("#faultID").html(HTML);
	});

	$("#cstatusID").on('change',function()
	{
		var cstatusID = $("#cstatusID").val();
		var leavingID = $("#crleavingID").val();
		
		cstatusID = (isNaN(cstatusID) || cstatusID == '' || typeof cstatusID === 'undefined') ? 0 : cstatusID;
		leavingID = (isNaN(leavingID) || leavingID == '' || typeof leavingID === 'undefined') ? 0 : leavingID;
		
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1;		
		var yyyy = today.getFullYear();
		if(dd < 10){dd='0'+dd;} 
		if(mm < 10){mm='0'+mm;} 
		var TDates = dd+'/'+mm+'/'+yyyy;
				
		if(parseInt(cstatusID) == 2)
		{
			$("#enddate").val(TDates);
			$("#enddate").prop('readonly',false);
			$("#crleavingID").attr('disabled',false);
			$("#crleavingID,#resonrgID").prop('selectedIndex', 0); 	
		}
		else
		{
			$("#enddate").val('');
			$("#enddate").prop('readonly',true);
			$("#crleavingID").attr('disabled',true);
			$("#crleavingID,#resonrgID").prop('selectedIndex', 0);
		}
	});
	
	$("#sincID").on('change',function()
	{
		var statusID = $(this).val();
		
		if(parseInt(statusID) == 1)
		{
			$(".breakPOINT").html('<br />');
			$(".gridID_2,.gridID_0").show();

			var frmvalidator  = new Validator("register");    
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			frmvalidator.clearAllValidations();

			frmvalidator.addValidation("refno","req","Enter Ref No ");
			frmvalidator.addValidation("rpdateID","req","Enter Reported Date");
			frmvalidator.addValidation("dateID","req","Enter Incident Date");
			frmvalidator.addValidation("description","req","Enter The Description");
			
			frmvalidator.addValidation("offdtlsID","num","Plz Select Offence Detail");
			frmvalidator.addValidation("offdtlsID","gt=0","Plz Select Offence Detail");
		}
		else if(parseInt(statusID) == 2)
		{
			$(".breakPOINT").html('');
			$(".gridID_2").hide();
			$(".gridID_0").show();
			
			var frmvalidator  = new Validator("register");    
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			frmvalidator.clearAllValidations();
			
			frmvalidator.addValidation("refno","req","Enter Ref No ");
			frmvalidator.addValidation("dateID","req","Enter Incident Date");
			frmvalidator.addValidation("description","req","Enter The Description");
		}
		else
		{
			$(".breakPOINT").html('');
			$(".gridID_2,.gridID_0").hide();
		}
	});
	
	$("#tempID").on('click',function()
	{
		var HTML = '';
		
		if($(this).is(':checked'))	
                {
                    $("#gridID_1").show();	
                    $("#gridID_2").hide();
                    
                    var frmvalidator  = new Validator("register");    
                    frmvalidator.EnableOnPageErrorDisplay();    
                    frmvalidator.EnableMsgsTogether();
                    
                    frmvalidator.clearAllValidations();
                    
                    frmvalidator.addValidation("empID","num","Plz select Employee Name");
                    frmvalidator.addValidation("empID","gt=0","Plz select Employee Name ");  
                }
		else    
                {
                    $("#gridID_2").show();	
                    $("#gridID_1").hide();
                    
                    var frmvalidator  = new Validator("register");    
                    frmvalidator.EnableOnPageErrorDisplay();    
                    frmvalidator.EnableMsgsTogether();
                    
                    frmvalidator.clearAllValidations();
                    
                    frmvalidator.addValidation("empname","req","Enter employee name ");
                    
                    frmvalidator.addValidation("contractorID_1","req","Enter Contractor ");
                }
	});
	
	$("#plrefID,#attendedID_2").on('click',function()
	{
		if(($("#plrefID").is(':checked')) || ($("#attendedID_2").is(':checked')))
		{
			$("#plrefnoID").attr('readonly',false);	 $("#plcadno").attr('readonly',false);  $("#plcvehicle").attr('readonly',false);
			$("#plrefnoID").attr('required',true);	 $("#plcadno").attr('required',true);	$("#plcvehicle").attr('required',true); 

			$("#policename").attr('readonly',false); $("#plcactionID").attr('disabled',false);
			$("#policename").attr('required',true);	 $("#plcactionID").attr('required',true);
		}
		else
		{
			$("#plrefnoID").attr('readonly',true); 	  $("#plcadno").attr('readonly',true);  $("#plcvehicle").attr('readonly',true);
			$("#plrefnoID").attr('required',false);	  $("#plcadno").attr('required',false); $("#plcvehicle").attr('required',false); 
			
			$("#policename").attr('readonly',true);	  $("#plcactionID").attr('disabled',true);
			$("#policename").attr('required',false);  $("#plcactionID").attr('required',false);
		}
	});

	$("#prpmgridID").on('click',function()
	{
            var frmvalidator  = new Validator("register");    
            frmvalidator.EnableOnPageErrorDisplay();    
            frmvalidator.EnableMsgsTogether();
            
            
            var minNumber = 1; // le minimum
            var maxNumber = 450; // le maximum
            var rollID = Math.floor(Math.random() * (maxNumber + 1) + minNumber);
            
            var HTML  = '';

            HTML += '<div class="col-xs-12"><br />';

                HTML += '<div class="col-xs-1"><br />';
                HTML += '<input type="button" class="btn bg-olive btn-flat margin DLBTN" value="X" />';
                HTML += '</div>';

                HTML += '<input type="hidden" name="typeID[]" value="'+rollID+'" />';

                HTML += '<div class="col-xs-3">';
                HTML += '<label for="section">Vehicle Reg No</label>';
                HTML += '<input type="text" class="form-control" name="'+rollID+'_vehicleNO" id="'+rollID+'_vehicleNO" required="required" placeholder="Enter Vehicle Reg No">';
                HTML += '<span id="register_'+rollID+'_vehicleNO_errorloc" class="errors"></span>';
                HTML += '</div>';

                HTML += '<div class="col-xs-3">';
                HTML += '<label for="section">Permit No</label>';
                HTML += '<input type="text" class="form-control" name="'+rollID+'_permitNO" id="'+rollID+'_permitNO" required="required" placeholder="Enter Permit No">';
                HTML += '<span id="register_'+rollID+'_permitNO_errorloc" class="errors"></span>';
                HTML += '</div>';

                HTML += '<div class="col-xs-2">';
                HTML += '<label for="section">Issue Date</label>';
                HTML += '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="'+rollID+'_issueDATE" id="'+rollID+'_issueDATE" required="required" style="text-align:center;" placeholder="Enter Issue Date">';
                HTML += '<span id="register_'+rollID+'_issueDATE_errorloc" class="errors"></span>';
                HTML += '</div>';

                HTML += '<div class="col-xs-2">';
                HTML += '<label for="section">Returned Date</label>';
                HTML += '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="'+rollID+'_returnDATE" id="'+rollID+'_returnDATE" style="text-align:center;" placeholder="Enter Returned Date">';
                HTML += '</div>';
            HTML += '</div>';

            $('#dataTablesID').before(HTML);
            $('.datepicker').datepick();
            $.datable();
			
            frmvalidator.addValidation(""+rollID+"_vehicleNO","req","Enter Vehicle Reg No..");
            frmvalidator.addValidation(""+rollID+"_permitNO","req","Enter Permit No..");
            frmvalidator.addValidation(""+rollID+"_issueDATE","req","Enter Issue Date..");

            $(".DLBTN").click(function()
            {
                var $this = $(this);
                $this.closest('.col-xs-12').remove();
            });
	});
});