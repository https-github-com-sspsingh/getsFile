$(document).ready(function()
{   
	$("#empID").on('change',function()
	{
		$("#endDT,#startDT,#desigTT").val('');
		
		$("#endDT").val($("#empID option:selected").attr('aria-title'));
		$("#startDT").val($("#empID option:selected").attr('aria-busy'));
		$("#desigTT").val($("#empID option:selected").attr('aria-scroll'));
	});

	$(".rleavingCLASS").click(function()
	{
		var rleavingID = $(this).attr('aria-sort');
		
		if(rleavingID == 1)		/* RESIGNED */
		{
			$("#rleavingDIV").empty();
			
			var HTML = '';
			
			$.ajax({
			url : '../ajax/ajax.php',
			type:'POST',				
			data:{'request':'regisnationLists' },
			dataType:"json",
			success : function(data)
			{
				HTML += '<div class="row">';			
					HTML += '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';		
					HTML += '</div>';
					
					HTML += '<div class="row">';			
						HTML += '<div class="col-xs-3">';
						HTML += '<label for="section">Reason For Resignation <span class="Maindaitory">*</span></label>';
						HTML += '<select onchange="changes=true;" class="form-control" id="resonrgID" name="resonrgID">';
						HTML += '<option value="0" selected="selected" disabled="disabled">-- Select Reason --</option>';
						HTML += data.listsTX;
						
						HTML += '</select>';
						HTML += '<span id="register_resonrgID_errorloc" class="errors"></span>';
					HTML += '</div>';

					HTML += '<div class="col-xs-4">';
						HTML += '<label for="section">Resignation - Other Details</label>';
						HTML += '<input type="text" onchange="changes=true;" class="form-control" id="termOther" name="termOther" placeholder="Enter Resignation - Other Details" readonly="readonly">';
						HTML += '<span id="register_termOther_errorloc" class="errors"></span>';
					HTML += '</div>';				
				HTML += '</div>';
			
				$("#rleavingDIV").html(HTML);
				$("#rleavingTX").val(rleavingID);
				
				
				var frmvalidator  = new Validator("register");    
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("empID","num","Plz select Employee Name");
				frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

				frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
				frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 

				frmvalidator.addValidation("resonrgID","num","Plz select Reason For Resignation");
				frmvalidator.addValidation("resonrgID","gt=0","Plz select Reason For Resignation");

				$("#resonrgID").on('change',function()
				{
					if($(this).val() == 311)
					{
						$("#termOther").prop('readonly',false);
						
						var frmvalidator  = new Validator("register");    
						frmvalidator.clearAllValidations();
						frmvalidator.EnableOnPageErrorDisplay();    
						frmvalidator.EnableMsgsTogether();

						frmvalidator.addValidation("empID","num","Plz select Employee Name");
						frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

						frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
						frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 

						frmvalidator.addValidation("resonrgID","num","Plz select Reason For Resignation");
						frmvalidator.addValidation("resonrgID","gt=0","Plz select Reason For Resignation");
						
						frmvalidator.addValidation("termOther","req","Enter Resignation - Other Details ");
					}
					else	
					{
						$("#termOther").prop('readonly',true);	
						$("#register_termOther_errorloc").text('');
						
						var frmvalidator  = new Validator("register");    
						frmvalidator.clearAllValidations();
						frmvalidator.EnableOnPageErrorDisplay();    
						frmvalidator.EnableMsgsTogether();

						frmvalidator.addValidation("empID","num","Plz select Employee Name");
						frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

						frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
						frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 

						frmvalidator.addValidation("resonrgID","num","Plz select Reason For Resignation");
						frmvalidator.addValidation("resonrgID","gt=0","Plz select Reason For Resignation"); 
					}
				});
				
			},
			error: function(res)    {console.log(res);} 	
		   }); 
		}
		
		else if(rleavingID == 2)		/* TERMINATED */
		{
			$("#rleavingDIV").empty();
			
			var HTML = '';
			
			$.ajax({
			url : '../ajax/ajax.php',
			type:'POST',				
			data:{'request':'terminationLists' },
			dataType:"json",
			success : function(data)
			{
				HTML += '<div class="row">';			
					HTML += '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';		
					HTML += '</div>';
					
					HTML += '<div class="row">';			
						HTML += '<div class="col-xs-3">';
						HTML += '<label for="section">Reason For Termination <span class="Maindaitory">*</span></label>';
						HTML += '<select onchange="changes=true;" class="form-control" id="terminationID" name="terminationID">';
						HTML += '<option value="0" selected="selected" disabled="disabled">-- Select Termination --</option>';
						HTML += data.listsTX;						
						HTML += '</select>';
						HTML += '<span id="register_terminationID_errorloc" class="errors"></span>';
					HTML += '</div>';

					HTML += '<div class="col-xs-4">';
						HTML += '<label for="section">Termination - Other Details</label>';
						HTML += '<input type="text" onchange="changes=true;" class="form-control" id="termOther" name="termOther" placeholder="Enter Termination - Other Details" readonly="readonly">';
						HTML += '<span id="register_termOther_errorloc" class="errors"></span>';
					HTML += '</div>';				
				HTML += '</div>';
			
				$("#rleavingDIV").html(HTML);				
				$("#rleavingTX").val(rleavingID);

				var frmvalidator  = new Validator("register");    
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("empID","num","Plz select Employee Name");
				frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

				frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
				frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 

				frmvalidator.addValidation("terminationID","num","Plz select Reason For Termination");
				frmvalidator.addValidation("terminationID","gt=0","Plz select Reason For Termination");
				
				$("#terminationID").on('change',function()
				{
					if($(this).val() == 481)
					{
						$("#termOther").prop('readonly',false);
						
						var frmvalidator  = new Validator("register");    
						frmvalidator.clearAllValidations();
						frmvalidator.EnableOnPageErrorDisplay();    
						frmvalidator.EnableMsgsTogether();

						frmvalidator.addValidation("empID","num","Plz select Employee Name");
						frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

						frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
						frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 

						frmvalidator.addValidation("terminationID","num","Plz select Reason For Termination");
						frmvalidator.addValidation("terminationID","gt=0","Plz select Reason For Termination");
						
						frmvalidator.addValidation("termOther","req","Enter Termination - Other Details ");
					}
					else	
					{
						$("#termOther").prop('readonly',true);	
						$("#register_termOther_errorloc").text('');
						
						var frmvalidator  = new Validator("register");    
						frmvalidator.clearAllValidations();
						frmvalidator.EnableOnPageErrorDisplay();    
						frmvalidator.EnableMsgsTogether();

						frmvalidator.addValidation("empID","num","Plz select Employee Name");
						frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

						frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
						frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 

						frmvalidator.addValidation("terminationID","num","Plz select Reason For Termination");
						frmvalidator.addValidation("terminationID","gt=0","Plz select Reason For Termination"); 
					}
				});
				
			},
			error: function(res)    {console.log(res);} 	
		   });  
		}
		
		else if(rleavingID == 3)		/* TRANSFERED */
		{
			$("#rleavingDIV").empty();
			
			var HTML = '';
			
			$.ajax({
			url : '../ajax/ajax.php',
			type:'POST',				
			data:{'request':'transferDepots' },
			dataType:"json",
			success : function(data)
			{
				HTML += '<div class="row">';			
				HTML += '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';		
				HTML += '</div>';
				
				HTML += '<div class="row">';			
					HTML += '<div class="col-xs-2">';
						HTML += '<label>Code Changes <span class="Maindaitory">*</span></label>';
						HTML += '<select onchange="changes=true;" class="form-control" id="ecodeTY" name="ecodeTY">';
						HTML += '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
						HTML += '<option value="1" selected="selected">Yes</option>';
						HTML += '<option value="2">No</option>';
						HTML += '</select>';
						HTML += '<span id="register_ecodeTY_errorloc" class="errors"></span>';
					HTML += '</div>';
					
					HTML += '<div class="col-xs-2">';
						HTML += '<label>New E. Code <span class="Maindaitory">*</span></label>';
						HTML += '<input type="text" onchange="changes=true;" class="form-control" name="encodeID" id="encodeID" placeholder="Enter E. Code" style="width:150px; text-align:center;" />';
						HTML += '<span id="register_encodeID_errorloc" class="errors"></span>';
					HTML += '</div>';
					
					HTML += '<div class="col-xs-4">';
						HTML += '<label>Transfer Depot <span class="Maindaitory">*</span></label>';	
						HTML += '<select onchange="changes=true;" class="form-control" id="tdepotID" name="tdepotID">';
						HTML += '<option value="" selected="selected" disabled="disabled">-- Select Depot Name --</option>';
						
						HTML += data.listsTX;
						
						HTML += '</select>';
						HTML += '<span id="register_tdepotID_errorloc" class="errors"></span>';
					HTML += '</div>';
					
					HTML += '<div class="col-xs-4">';
						HTML += '<label for="section">Transferred - Remarks</label>';
						HTML += '<input type="text" onchange="changes=true;" class="form-control" id="termOther" name="termOther" placeholder="Enter Transferred - Remarks">';
					HTML += '</div>';				
				HTML += '</div>';
				
				$("#rleavingDIV").html(HTML);
				$("#rleavingTX").val(rleavingID);
				
				var frmvalidator  = new Validator("register");    
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("empID","num","Plz select Employee Name");
				frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");

				frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
				frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion "); 
				frmvalidator.addValidation("encodeID","req","Enter Code ");

				frmvalidator.addValidation("ecodeTY","num","Plz select Code Changes");
				frmvalidator.addValidation("ecodeTY","gt=0","Plz select Code Changes");
				
				frmvalidator.addValidation("tdepotID","req","Plz select Transfer Depot");
				
				$("#ecodeTY").on('change',function()
				{
					if($(this).val() == 1)		
							{$("#encodeID").prop('readonly',false);		$("#encodeID").val('');}
					else	{$("#encodeID").prop('readonly',true);		$("#encodeID").val($("#ecodeID").val());}
				});
				
			},
			error: function(res)    {console.log(res);} 	
		   });
		} 
		
		else if(rleavingID == 4 || rleavingID == 5)		/* RETIRED Or DECEASED */
		{
			$("#rleavingDIV").empty();
			
			var HTML = '';
			 
			HTML += '<div class="row">';			
			HTML += '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';		
			HTML += '</div>';
			
			HTML += '<div class="row">';	
				HTML += '<div class="col-xs-6">';
					HTML += '<label for="section">'+(rleavingID == 4 ? 'Retired' :(rleavingID == 5 ? 'Deceased' : ''))+' - Other Details</label>';
					HTML += '<input type="text" onchange="changes=true;" class="form-control" id="termOther" name="termOther" placeholder="Enter '+(rleavingID == 4 ? 'Retired' :(rleavingID == 5 ? 'Deceased' : ''))+' - Other Details">';
				HTML += '</div>';				
			HTML += '</div>';
			
			$("#rleavingDIV").html(HTML); 
			$("#rleavingTX").val(rleavingID); 
			
			var frmvalidator  = new Validator("register");    
			frmvalidator.EnableOnPageErrorDisplay();
			frmvalidator.EnableMsgsTogether();
			
			frmvalidator.addValidation("empID","num","Plz select Employee Name");
			frmvalidator.addValidation("empID","gt=0","Plz select Employee Name");
			
			frmvalidator.addValidation("startDT","req","Enter First Date of New Posithion ");
			frmvalidator.addValidation("rleavingTX","req","Enter Change of Posithion ");
		} 
		
		else	{$("#rleavingDIV").empty();}
	});
	
	$(".undoLicenseNoDate").click(function()
	{
		var empID = $(this).attr('aria-sort');
		
		empID = (isNaN(empID) || empID == '' || typeof empID === 'undefined') ? 0 : empID;
		
		if(parseInt(empID) > 0)
		{
			$.ajax({
				url : '../ajax/ajax_DBaudits.php',
				type:'POST',				
				data:{'request':'undoWWCAppliedDate' , 'ID' : empID},
				dataType:"json",
				success : function(data)
				{
					if(data.statusID == 1)		{window.location.reload();}
				},
				error: function(res)    {console.log(res);} 	
			   });
		}
	});
	
	$(".fillLicenseNoDate").click(function()
	{
		var empID = $(this).attr('aria-sort');
		
		empID = (isNaN(empID) || empID == '' || typeof empID === 'undefined') ? 0 : empID;
		
		if(parseInt(empID) > 0)
		{
			$.ajax({
				url : '../ajax/ajax_audits.php',
				type:'POST',				
				data:{'request':'updateWWCAppliedDate' , 'ID' : empID},
				dataType:"json",
				success : function(data)
				{
					$('#audits_modal h4').html('<b style="color:red;">Update - WWC Permit No - Applied Date</b>');
					$('#audits_modal #modal_data').html(data.file_info);
					$('#audits_modal').modal('show');
					
					$.datable();
					
					$("#gen_form").submit(function()
                    {
                        var formData = $("#gen_form").serialize();
                        var $inputs  = $("#gen_form").find('input,button');
                        $inputs.attr('disabled','disabled');
                        updateEmployeeWWCAppliedDate(formData,$inputs);
                        return false;
                    });
					
				},
				error: function(res)    {console.log(res);} 	
			   });
		}
	});
	
	var updateEmployeeWWCAppliedDate = function (formData,$inputs)
    {
        $.ajax({			
                url : '../ajax/ajax_DBaudits.php',
                type:'POST',
                dataType:"json",
                data: formData ,
                success : function(data)
                {	
                    if(data.statusID == 1)
                    {
                        alert('Record Is Updated Successfully. !!!');
						window.location.reload();
                    }
					else if(data.statusID == 3)
                    {
						$inputs.removeAttr('disabled');
                        alert('Kindly Enter The Valid Date. !!!');
                    }					
                    else
                    {
                        $inputs.removeAttr('disabled');
                    }				
                },error: function(res){console.log('ERROR in Code !...')}
        });
        return false;
    }
	
	
	$("#ecodeTY").on('change',function()
	{
		var ecodeTY = $("#ecodeTY").val();
		ecodeTY = (isNaN(ecodeTY) || ecodeTY == '' || typeof ecodeTY === 'undefined') ? 0 : ecodeTY;
		
		if(ecodeTY == 1)	/* New-Code */
		{
			$("#encodeID").prop('readonly',false);		$("#encodeID").val('');
		}
		else if(ecodeTY == 2)	/* Existing-Code */
		{
			$("#encodeID").prop('readonly',true);		$("#encodeID").val($("#EcodeID").val());
		}			
	});
	
	$("#tdepotID").on('change',function()
	{
		$("#tdepotTX").val($(this).val());
	});
	
	$("#cstatusID,#crleavingID").on('change',function()
	{
		var estatusID = $("#cstatusID").val();
		var ereleavID = $("#crleavingID").val();
		
		estatusID = (isNaN(estatusID) || estatusID == '' || typeof estatusID === 'undefined') ? 0 : estatusID;
		ereleavID = (isNaN(ereleavID) || ereleavID == '' || typeof ereleavID === 'undefined') ? 0 : ereleavID;
		
		if(estatusID == 2 && ereleavID == 3)
		{
			$("#transferDiv").show();
			
			
			var frmvalidator  = new Validator("register");   
			frmvalidator.clearAllValidations();			
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("encodeID","req","Enter Code ");
			frmvalidator.addValidation("code","req","Enter E. Code ");
			frmvalidator.addValidation("fname","req","Enter First Name ");
			frmvalidator.addValidation("full_name","req","Enter Full Name ");
			frmvalidator.addValidation("address_1","req","Enter Address - 1 ");
			//frmvalidator.addValidation("pincode","req","Enter PostCode ");

			frmvalidator.addValidation("statusID","num","Plz select C. Employee");
			frmvalidator.addValidation("statusID","gt=0","Plz select C. Employee ");

			frmvalidator.addValidation("desigID","num","Plz select Desgination");
			frmvalidator.addValidation("desigID","gt=0","Plz select Desgination");

			frmvalidator.addValidation("sid","num","Plz select Suburb..");
			frmvalidator.addValidation("sid","gt=0","Plz select Suburb..");
			
			frmvalidator.addValidation("ecodeTY","num","Plz select E. Code Type");
			frmvalidator.addValidation("ecodeTY","gt=0","Plz select E. Code Type");
			
			frmvalidator.addValidation("tdepotTX","req","Plz select Transfer Depot");
			
			/*frmvalidator.addValidation("tdepotID","num","Plz select Transfer Depot");
			frmvalidator.addValidation("tdepotID","gt=0","Plz select Transfer Depot");*/
			
			if($("#sstatusID").val() == 1)
			{
				frmvalidator.addValidation("scompanyID","num","Plz select Sub Depot..");
				frmvalidator.addValidation("scompanyID","gt=0","Plz select Sub Depot..");
			}
			
		}
		else
		{
			
			var frmvalidator  = new Validator("register");   
			frmvalidator.clearAllValidations();			
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("code","req","Enter E. Code ");
			frmvalidator.addValidation("fname","req","Enter First Name ");
			frmvalidator.addValidation("full_name","req","Enter Full Name ");
			frmvalidator.addValidation("address_1","req","Enter Address - 1 ");
			//frmvalidator.addValidation("pincode","req","Enter PostCode ");

			frmvalidator.addValidation("statusID","num","Plz select C. Employee");
			frmvalidator.addValidation("statusID","gt=0","Plz select C. Employee ");

			frmvalidator.addValidation("desigID","num","Plz select Desgination");
			frmvalidator.addValidation("desigID","gt=0","Plz select Desgination");

			frmvalidator.addValidation("sid","num","Plz select Suburb..");
			frmvalidator.addValidation("sid","gt=0","Plz select Suburb..");
			
			if($("#sstatusID").val() == 1)
			{
				frmvalidator.addValidation("scompanyID","num","Plz select Sub Depot..");
				frmvalidator.addValidation("scompanyID","gt=0","Plz select Sub Depot..");
			}
			
			$("#transferDiv").hide();
		}
	});
	
	$(".cmp_report_date,.cmp_incident_date,.complaint_forms").on('click',function()
	{
		var rpt_dateID = $("#serDT").val();
		var ins_dateID = $("#dateID").val();
		
		check_inspection_date(rpt_dateID,ins_dateID,'serDT');
	});
	
	$(".hiz_report_date,.hiz_occurance_date,.hazards_forms").on('click',function()
	{
		var rpt_dateID = $("#rdateID").val();
		var ins_dateID = $("#dateID").val();
		
		check_inspection_date(rpt_dateID,ins_dateID,'rdateID');
	});
	
	$(".ins_report_date,.ins_inspect_date,.inspection_forms").on('click',function()
	{
		var rpt_dateID = $("#dateID").val();
		var ins_dateID = $("#dateID1").val();
		
		check_inspection_date(rpt_dateID,ins_dateID,'dateID');
	});
	
	$(".inc_report_date,.inc_incident_date,.incidents_forms").on('click',function()
	{
		var rpt_dateID = $("#rpdateID").val();
		var ins_dateID = $("#dateID").val();
		
		check_inspection_date(rpt_dateID,ins_dateID,'rpdateID');
	});
	
	var check_inspection_date = function(rpt_dateID,ins_dateID,fieldTX)
	{
		if(rpt_dateID != '' && ins_dateID != '')
		{
			$.ajax({
			url : '../ajax/ajax.php',
			type:'POST',				
			data:{'request':'date-check' , 'rpt_dateID':rpt_dateID , 'ins_dateID':ins_dateID},
			dataType:"json",
			success : function(data)
			{
				if(data.countID == 1)
				{
					$("#register_"+fieldTX+"_errorloc").text('');
					$("#register_"+fieldTX+"_errorloc").text('Plz enter valid date..');
				}
				else
				{
					$("#register_"+fieldTX+"_errorloc").text('');
				}
			},
			error: function(res)    {console.log(res);} 	
			});
		}
		else	{$("#register_"+fieldTX+"_errorloc").text('');}
	}
	
	$(".spdrgridID").on('click',function()
	{
		var dateID = $("#dateID").val();
		
		if(dateID != '')
		{
			$.ajax({
			url : '../ajax/ajax_vprints.php',
			type:'POST',				
			data:{'request':'SPARE_DRIVERS' , 'dateID':dateID},
			dataType:"json",
			success : function(data)
			{
				$('#dataTables tr:last').after(data.result);
				$(".select2").select2();
				$(".TPicker").clockface({format: 'HH : mm'}).clockface('hide', '14:30');

				$(".SpareGridID_1").click(function()    {var $this = $(this);    $this.closest('tr').hide().fadeOut(2000).remove();});

				$(".SP_driverID").change(function(e)
				{
					var $this = $(this);
					var driverID = $(this).parent().parent('tr').find('.SP_driverID').val();

					$this.parent().parent('tr').find('.SP_driverCD').val('');
					$this.parent().parent('tr').find('.SP_phoneNO').val('');
					$this.parent().parent('tr').find('.SP_phoneNO_1').val('');
					$this.parent().parent('tr').find('.SP_locationID').val('');
					$this.parent().parent('tr').find('.SP_suburbsID').val('');

					if(parseInt(driverID) > 0)
					{ 
					  $.ajax({
						url : '../ajax/ajax.php',
						type:'POST',	
						data:{'reqID': driverID , 'request': 'GET_EMP_FIGURES'},
						dataType:"json",
						success : function(data)
						{
								$this.parent().parent('tr').find('.SP_driverCD').val(data.code);
								$this.parent().parent('tr').find('.SP_phoneNO').val(data.phone);
								$this.parent().parent('tr').find('.SP_phoneNO_1').val(data.phone_1);
								$this.parent().parent('tr').find('.SP_locationID').val(data.suburb);
								$this.parent().parent('tr').find('.SP_suburbsID').val(data.suburbs);
						},
						error: function(res)
						{
								console.log(res);	
						}	
						});
					} 

				});

				$(".SP_avaiableID").change(function(e)
				{
					var $this = $(this);
					var avaiableID = $(this).parent().parent('tr').find('.SP_avaiableID').val();

					if(parseInt(avaiableID) > 0)
					{
						if(parseInt(avaiableID) == 2)
								{
									$this.parent().parent('tr').find('.SP_timeID').attr('readonly',true);
									$this.parent().parent('tr').find('.SP_timeID').attr('required',false);
								}
						else if(parseInt(avaiableID) == 1 || parseInt(avaiableID) == 3)
								{
									$this.parent().parent('tr').find('.SP_timeID').attr('readonly',false);
									$this.parent().parent('tr').find('.SP_timeID').attr('required',true);
								}
						else    {
									$this.parent().parent('tr').find('.SP_timeID').attr('readonly',true);
									$this.parent().parent('tr').find('.SP_timeID').attr('required',false);
								}
					}
				});

			},
			error: function(res)    {console.log(res);} 	
			});
		}
		else	{alert('Enter Date !....');}
	});
	
	$(".SP_avaiableID").change(function(e)
	{
		var $this = $(this);
		var avaiableID = $(this).parent().parent('tr').find('.SP_avaiableID').val();
		
		if(parseInt(avaiableID) > 0)
		{
			if(parseInt(avaiableID) == 2)       {$this.parent().parent('tr').find('.SP_timeID').attr('readonly',true);}
			else if(parseInt(avaiableID) == 1 || parseInt(avaiableID) == 3)
			{$this.parent().parent('tr').find('.SP_timeID').attr('readonly',false);}
			else        {$this.parent().parent('tr').find('.SP_timeID').attr('readonly',true);}
		}
	});
						
	$(".SP_driverID").change(function(e)
	{
		var $this = $(this);
		var driverID = $(this).parent().parent('tr').find('.SP_driverID').val();
		  
		$this.parent().parent('tr').find('.SP_driverCD').val('');
		$this.parent().parent('tr').find('.SP_phoneNO').val('');
		$this.parent().parent('tr').find('.SP_phoneNO_1').val('');
		$this.parent().parent('tr').find('.SP_locationID').val('');
		$this.parent().parent('tr').find('.SP_suburbsID').val('');
		
		if(parseInt(driverID) > 0)
		{ 
		  $.ajax({
			url : '../ajax/ajax.php',
			type:'POST',	
			data:{'reqID': driverID , 'request': 'GET_EMP_FIGURES'},
			dataType:"json",
			success : function(data)
			{
				$this.parent().parent('tr').find('.SP_driverCD').val(data.code);
				$this.parent().parent('tr').find('.SP_phoneNO').val(data.phone);
				$this.parent().parent('tr').find('.SP_phoneNO_1').val(data.phone_1);
				$this.parent().parent('tr').find('.SP_locationID').val(data.suburb);
				$this.parent().parent('tr').find('.SP_suburbsID').val(data.suburbs);
			},
			error: function(res)
			{
				console.log(res);	
			}	
			});
		} 
			  
	});
					
	$(".spbsgridID").on('click',function()
	{
		var dateID = $("#dateID").val();
		if(dateID != '')
		{
			$.ajax({
			url : '../ajax/ajax_vprints.php',
			type:'POST',				
			data:{'request':'SPARE_BUSNO'},
			dataType:"json",
			success : function(data)
			{
				$('#dataTables_1 tr:last').after(data.result);
				$(".select2").select2();
				
				$(".SpareGridID_2").click(function()    {var $this = $(this);    $this.closest('tr').hide().fadeOut(2000).remove();});
			},
			error: function(res)    {console.log(res);} 	
		   });
		}
		else    {alert('Enter Date !....');}
	});
	 
	$(".SpareGridID_1").click(function()    {var $this = $(this);    $this.closest('tr').hide().fadeOut(2000).remove();});
	$(".SpareGridID_2").click(function()    {var $this = $(this);    $this.closest('tr').hide().fadeOut(2000).remove();});
        
        
	$("#logintypeID").on('change',function()
	{
		var logintypeID = $(this).val();
		logintypeID = (isNaN(logintypeID) || logintypeID == '' || typeof logintypeID === 'undefined') ? 0 : logintypeID;
		
		if(parseInt(logintypeID) == 2)	
		{
			$("#tdateID_2").val('');    $("#tdateID_2").prop('disabled',false);
		}
		else													
		{
			$("#tdateID_2").val('');    $("#tdateID_2").prop('disabled',true);
		}		
	});

	
	$("#exprtypeID").click(function()
	{
		var vousID = $(this).attr('aria-sort');
		var roleID = $(this).attr('aria-busy');
		var userID = $(this).attr('aria-owns');
		var permID = $("#prtypeID").val();
		
		vousID = (isNaN(vousID) || vousID == '' || typeof vousID === 'undefined') ? 0 : vousID;
		roleID = (isNaN(roleID) || roleID == '' || typeof roleID === 'undefined') ? 0 : roleID;
		permID = (isNaN(permID) || permID == '' || typeof permID === 'undefined') ? 0 : permID;
		
		if(parseInt(roleID) > 0 && parseInt(vousID) > 0  && (parseInt(permID) == 2 || parseInt(permID) == 3))
		{
			$.ajax({
				url : '../ajax/ajax_popups.php',
				type:'POST',				
				data:{'request':'GET_FORMS_LIST' , 'vousID' : vousID , 'roleID' : roleID},
				dataType:"json",
				success : function(data)
				{
					$('#srch_modal h4').html('<b style="color:red;">' + userID + '</b>');
					$('#srch_modal #modal_data').html(data.file_info);
					$('#srch_modal').modal('show');
					
					$("select#spermissionsID").multiselect({noneSelectedText:'Select option'}).multiselectfilter();
                                        
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
					
					$.datable();
					
					//$("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
					//$("[data-mask]").inputmask();
					
					
					//$('.datepicker').datepick();
					
					$("#gen_form").submit(function()
                    {
                        var formData = $("#gen_form").serialize();
                        var $inputs  = $("#gen_form").find('input,button');
                        $inputs.attr('disabled','disabled');
                        Update_Users_Permissions(formData,$inputs);
                        return false;
                    });
					
				},
				error: function(res)    {console.log(res);} 	
			   });
		}
		else	{alert('Additional responsibility can be provided to existing users only....');}
	});
	
    var Update_Users_Permissions = function (formData,$inputs)
    {
        $.ajax({			
                url : '../ajax/ajax_DBpopups.php',
                type:'POST',
                dataType:"json",
                data: formData ,
                success : function(data)
                {	
                    if(data.success == true)
                    {
                        alert('Additional responsibilities provided successfully. !!!');
                        //$("#srch_modal").hide();
                    } 
                    else
                    {
                        //$("#srch_modal #danger b").html('Error...!Please try again.').show(500).fadeIn(500);
                        $inputs.removeAttr('disabled');
                    }				
                },error: function(res){console.log('ERROR in Code !...')}
        });
        return false;
    }
	
	$("#prtypeID").on('change',function()
	{
		var prtypeID = $(this).val();
		prtypeID = (isNaN(prtypeID) || prtypeID == '' || typeof prtypeID === 'undefined') ? 0 : prtypeID;
		
		if(parseInt(prtypeID) == 1 || parseInt(prtypeID) == 2)	{$("#uroleID").prop('disabled',false);}
		else													  {$("#uroleID").prop('disabled',true);}
		
		if(parseInt(prtypeID) == 1)
				{$("#exprtypeID").removeClass('btn btn-danger');		  $("#exprtypeID").addClass('btn btn-disabled');}
		else if(parseInt(prtypeID) == 2 || parseInt(prtypeID) == 3)
				{$("#exprtypeID").removeClass('btn btn-disabled');		$("#exprtypeID").addClass('btn btn-danger');}
		else	{$("#exprtypeID").removeClass('btn btn-danger');		  $("#exprtypeID").addClass('btn btn-disabled');}
		
		if(parseInt(prtypeID) == 3)	{$("#uroleID")[0].selectedIndex = 0;}
		
	});
	
	$("#driverID").on('change',function()
	{
		var valID = $("#driverID option:selected").text();		
		var strID = valID.split(' ');
		
		$("#usernameID").val('');
		$("#usernameID").val((strID.length) <= 2 ? (strID[0].charAt(0) + '' + strID[1]) : (strID[0].charAt(0) + '' + strID[1].charAt(0) + '' + strID[2]));
	});
	
	
	$("#desigID").on('change',function()
	{
            var ageID = $(this).val();
            ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
            if((parseInt(ageID) == 8)  && ($("#currentUT").val() == 'AD'))
            {
                $("#REP_companyID").show();
            }
            else    {$("#REP_companyID").hide();}
	});
        
	$("#MNG_rtpyeID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		if(parseInt(ageID) == 1)
		{
			$("#empID").prop('disabled',false);
		}
		else
		{
			$("#empID").prop('disabled',true);
		}
	});
	
	$("#emp_rtpyeID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
			
		if(parseInt(ageID) == 10 || parseInt(ageID) == 11)
			{$("#daterange-btn").prop('disabled',false);}
		else
			{$("#daterange-btn").prop('disabled',true);}
	});
	
	$("#respID").on('click',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		if(parseInt(ageID) <= 0 || (parseInt(ageID) == 46))    {$("#resdate").prop('readonly',true);}
		else                    {$("#resdate").prop('readonly',false);}
	});
	
    $(".dashboard_viewID").on('click',function()
    {
        var urlID = $(this).attr('aria-sort');
        var resID = '';
        resID = urlID.split("/");
        
        if(urlID != '')
        {
            $.ajax({
                url : 'ajax/ajax_popups.php',
                type:'POST',				
                data:{'request':resID[0] , 'ID':urlID},
                dataType:"json",
                success : function(data)
                {
                    $('#popups_modal h4').html((resID[0] == 'imp_persheets_e' ? 'Early Running' :(resID[0] == 'imp_persheets_l' ? 'Late First' : '')));
                    $('#popups_modal #modal_data').html(data.file_info);
                    $('#popups_modal').modal('show');            
                },
                error: function(res)    {console.log(res);} 	
               });
        }
        else	{alert('Error In Code !....');}
   });    
   

	$("#rpt_formID").on('change',function()
	{
		var frmID = $(this).val();
		if(parseInt(frmID) > 0)
		{
			$.ajax({
			url : '../ajax/ajax.php',
			type:'post',
			data:{'request':'Getform_fields','reqID':frmID},
			dataType:"json",
				success:function(data)
				{	
					//alert(JSON.stringify(data.formFields))
					if(data.formFields != '')
					{
						$("#rpt_fieldID").empty();
						$("#rpt_fieldID option:gt(0)").remove();
						$.each(data.formFields,function(index,value)
						{
							$("#rpt_fieldID").append($('<option>', { value: index, text : value }));				
							$("select#rpt_fieldID").multiselect('destroy');
							$("select#rpt_fieldID").multiselect({noneSelectedText:'Select option'}).multiselectfilter();
						});
						
						$("select#rpt_fieldID").multiselect('checkAll');
					}
					else
					{
						$("#rpt_fieldID option:gt(0)").remove();
						$("select#rpt_fieldID").multiselect('destroy');
						$("select#rpt_fieldID").multiselect({noneSelectedText:'Select option'}).multiselectfilter();
					}
				},
				error:function()
				{					
					alert('ERROR');
				}
			});
		}
	});
	
	$(".DisplayPendingShifts").click(function()
	{
		var dateID = $(this).attr('aria-sort');
		
		if(dateID != '')
		{
			$.ajax({
				url : '../ajax/ajax_popups.php',
				type:'POST',				
				data:{'request':'GET_PENDING_SHIFTS' , 'ID' : dateID},
				dataType:"json",
				success : function(data)
				{
					$('#popups_modal h4').html('<b style="color:red;">Pending Shifts  : Date - '+dateID+'</b>');
					$('#popups_modal #modal_data').html(data.file_info);
					$('#popups_modal').modal('show');
					
					$(".select-pending-modal").click(function()
					{					
						var reqID = $(this).attr('aria-sort');
						var $this = $(this);
						
						if(reqID != '')
						{
							$.ajax({
								url : '../ajax/ajax_DBpopups.php',
								type:'POST',				
								data:{'request': 'INSERT_PENDING_SHIFTS', 'reqID':reqID},
								dataType:"json",
								success : function(data)
								{
									
								},
								error: function(res)    {console.log(res);}
							});
							
							$this.closest('tr').slideUp('slow').remove();
						}
					});
					
				},
				error: function(res)    {console.log(res);} 	
			   });
		}
		else	{alert('Additional responsibility can be provided to existing users only....');}
	});
	
    var timeID = moment().format('hhmm');
	//alert(timeID);
    if((parseInt(timeID) >= 1500 && parseInt(timeID) <= 1500) && (timeID != ''))
    {
        var pageID = document.location.pathname.match(/[^\/]+$/)[0];
        var urlID = (pageID == 'dashboard.php' ? '' : '../');
		
		if(pageID == 'dashboard.php')
		{
			$.ajax({
				url : (urlID) + 'ajax/ajax.php',
				type:'POST',				
				data:{'request':'DBbakcup_log' },
				dataType:"json",
				success : function(data)
				{
					if(parseInt(data.countID) <= 0)
					{
						alert('Database backup is in progress..');
						window.open("DBbackup.php","_self");						
					}
				},
				error: function(res)    {console.log(res);} 	
			   });
		}
    }
    
    $("#uroleID").on('change',function()
    {
        var uroleID = $(this).val();
        if(parseInt(uroleID) == 9)  {$("select#mcompanyID").multiselect('checkAll');}
    });
    
    $("#isActive").on('change',function()
    {
        var isActive = $(this).val();		
        if(parseInt(isActive) == 1) {} else {$("select#rpcompanyID").multiselect('uncheckAll');}
    });
	
});