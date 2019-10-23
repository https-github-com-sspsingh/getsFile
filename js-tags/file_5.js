$(document).ready(function()
{
	var messageSTRING = '';
	messageSTRING = '<img src="../img/fuses.jpg" /><b style="color:red;"> &nbsp;&nbsp;OOPS something went wrong!</b> &nbsp;&nbsp;Progress cannot be completed as there is <u>outstanding information</u>. Select “YES” to change to pending and select the "UPDATE" button to revisit later or “NO” to add information.';
	
	/* ------ INCIDENT FORM ------ */
	$("#incidentSubmit").on('click',function()
	{
		var statusID = $("#INCstatusID").val();				
		var sincID	  = $("#sincID").val();
		var offtypeID = $("#offtypeID").val();		
		var cmdiscID = $('#cmdiscID').val();		
		$("#IncidentValidGridID").html('');
		
		statusID  = (statusID == null || statusID == 'NaN' || isNaN(statusID) || statusID == '' || typeof statusID === 'undefined') ? 0 : statusID;
		sincID 	  = (sincID	 == null || sincID	 == 'NaN' || isNaN(sincID)	 || sincID == ''   || typeof sincID	  === 'undefined') ? 0 : sincID;
		offtypeID = (offtypeID == null || offtypeID == 'NaN' || isNaN(offtypeID) || offtypeID == '' || typeof offtypeID === 'undefined') ? 0 : offtypeID;
		cmdiscID  = (cmdiscID == null || cmdiscID == 'NaN' || isNaN(cmdiscID) || cmdiscID == '' || typeof cmdiscID === 'undefined') ? 0 : cmdiscID;
		
		AuditTrialCheck_INCIDENT(statusID,sincID,offtypeID,cmdiscID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="IncidentYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="IncidentNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#IncidentValidGridID").html('');
			$("#IncidentValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#IncidentYes").offset().top
			}, 2000);
			
			$("#IncidentNo").on('click',function()
			{
				$("#IncidentValidGridID").html('');				
				$("#sincID").focus(); 				
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#IncidentYes").on('click',function()
			{
				$("#IncidentValidGridID").html('');
				$("select#INCstatusID").prop('selectedIndex', 2);
				
				ClearErrorMessages('empID,cmdiscID,mcomments,wrtypeID,dmginjury,crossst,dmvalue,offtypeID,grfcolour,whbwdescID,grfitemID,ptarefNO,plcadno');
				ClearErrorMessages('plcvehicle,policename,plcactionID,timeID,location,reportby,description,action,busID,routeID,shiftID,plrefnoID,intvID,intvDate');
				ClearErrorMessages('suburb,actbyID,inctypeID');
		
				var frmvalidator  = new Validator("register");  
				frmvalidator.clearAllValidations();			
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("refno","req","Enter Ref No ");
				frmvalidator.addValidation("dateID","req","Enter Date Occured");
				frmvalidator.addValidation("description","req","Enter The Description");
			});
			
		}
		else	{$("#IncidentValidGridID").html('');}				
	});
	
	/* ------ COMPLAINT FORM ------ */
	$("#complaintSubmit").on('click',function()
	{
		var statusID  = $("#CMPLstatusID").val();
		var cmltypeID = $("#cmltypeID").val();
		var accID     = $("#accID").val();
		var respID    = $("#respID").val();
		var cmdiscID  = $('#cmdiscID').val();
		var locationID = $('#location').val();
		var substanID  = $('#substanID').val();
		var tickID_1   = $("#tickID_1").prop('checked');
		$("#ComplaintValidGridID").html('');
		
		statusID  = (statusID == null  || statusID == 'NaN'  || isNaN(statusID)  || statusID == ''  || typeof statusID === 'undefined')  ? 0 : statusID;
		accID 	  = (accID == null 	   || accID == 'NaN' 	 || isNaN(accID) 	 || accID == ''     || typeof accID === 'undefined')     ? 0 : accID;
		cmdiscID  = (cmdiscID == null  || cmdiscID == 'NaN'  || isNaN(cmdiscID)  || cmdiscID == ''  || typeof cmdiscID === 'undefined')  ? 0 : cmdiscID;
		cmltypeID = (cmltypeID == null || cmltypeID == 'NaN' || isNaN(cmltypeID) || cmltypeID == '' || typeof cmltypeID === 'undefined') ? 0 : cmltypeID;
		
		/* CHECK DATES - DUE DATE or REPORTED DATE */
			
		/*var sDate = $("#cmdueDT").val();
		var eDate = $("#serDT").val();
		var rstatus = 0;
		
		if(sDate != '' && eDate != '')
		{
			$("#DuplicateText").text('');
			
			$.ajax({
			url : '../ajax/ajax.php',
			type:'POST',				
			data:{'request':'audit-date-check' , 'sDate':sDate , 'eDate':eDate},
			dataType:"json",
			success : function(data)
			{
				if(data.countID == 1)
				{
					$("#DuplicateText").text('');
					$("#DuplicateText").text('Enter The Valid Due Date..');
					$("#cmdueDT").val('');
				}
			},
			error: function(res)    {console.log(res);} 	
			});
		}
		else	{rstatus = 1;}
		*/
		
		AuditTrialCheck_COMPLAINT(statusID,accID,respID,cmdiscID,locationID,substanID,tickID_1,cmltypeID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="ComplaintYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="ComplaintNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#ComplaintValidGridID").html('');
			$("#ComplaintValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#ComplaintYes").offset().top
			}, 2000);
			
			$("#ComplaintNo").on('click',function()
			{
				$("#ComplaintValidGridID").html('');				
				$("#serDT").focus(); 				
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#ComplaintYes").on('click',function()
			{
				$("#ComplaintValidGridID").html('');
				$("select#CMPLstatusID").prop('selectedIndex', 2);
				
				ClearErrorMessages('timeID,busID,routeID,location,suburb,cmltypeID,creasonID,description,empID,substanID,faultID,invID,invdate,furaction');
				ClearErrorMessages('respID,resdate,outcome,trisID,cmdiscID,CMPLstatusID,mcomments,wrtypeID,intvID,intvDate');
		
				var frmvalidator  = new Validator("register"); 
				frmvalidator.clearAllValidations();			
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();
				
				if(cmltypeID == 491 || cmltypeID == 492)	{frmvalidator.addValidation("cmprefno","req","Enter Ref No ");}
				frmvalidator.addValidation("dateID","req","Enter Incident Date");
				frmvalidator.addValidation("serDT","req","Enter Comment Received On");
				frmvalidator.addValidation("cmdueDT","req","Enter Due Date");
				
				frmvalidator.addValidation("accID","gt=0","Select Comment Line Type ");
				frmvalidator.addValidation("accID","num","Select Comment Line Type ");
				
				frmvalidator.addValidation("cmltypeID","gt=0","Customer feedback type ");
				frmvalidator.addValidation("cmltypeID","num","Customer feedback type ");
				
			});
			
		}
		else	{$("#ComplaintValidGridID").html('');}				
	});
	
	/* ------ ACCIDENT FORM ------ */
	$("#accidentsSubmit").on('click',function()
	{
		var statusID = $("#progressID").val();
		var tickID_1 = $("#tickID_1").prop('checked');
		var tpartyID = $("#3partyID").prop('checked');
		var insinvID = $("#insinvolvedID").prop('checked');
		var witnesID = $("#witnessID").prop('checked');
		var cmdiscID = $('#cmdiscID').val();
		$("#AccidentValidGridID").html('');
		
		statusID = (statusID == null || statusID == 'NaN' || isNaN(statusID) || statusID == '' || typeof statusID === 'undefined') ? 0 : statusID;
		cmdiscID = (cmdiscID == null || cmdiscID == 'NaN' || isNaN(cmdiscID) || cmdiscID == '' || typeof cmdiscID === 'undefined') ? 0 : cmdiscID;
		tickID_1 = (tickID_1 == null || tickID_1 == 'NaN' || isNaN(tickID_1) || tickID_1 == '' || typeof tickID_1 === 'undefined') ? 0 : tickID_1;
		insinvID = (insinvID == null || insinvID == 'NaN' || isNaN(insinvID) || insinvID == '' || typeof insinvID === 'undefined') ? 0 : insinvID;
		
		AuditTrialCheck_ACCIDENT(statusID,tickID_1,tpartyID,insinvID,witnesID,cmdiscID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="AccidentYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="AccidentNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#AccidentValidGridID").html('');
			$("#AccidentValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#AccidentYes").offset().top
			}, 2000);
			
			$("#AccidentNo").on('click',function()
			{
				$("#AccidentValidGridID").html('');				
				$("#refno").focus(); 				
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#AccidentYes").on('click',function()
			{
				$("#AccidentValidGridID").html('');
				$("select#progressID").prop('selectedIndex', 2);
				
				ClearErrorMessages('busID,timeID,tickID_2,empID,acccatID,plcntID,thpnameID,regisnoID,thcontactID,damagetobusID,witnessID,insinvolvedID,insurerID,claimnoID,invnoID');
				ClearErrorMessages('accID,responsibleID,location,suburb,description,rprcost,othcost,optID1,optID2,optID3,outcome,invID,cmdiscID,intvID,intvDate,mcomments,wrtypeID');
				
				var frmvalidator  = new Validator("register");
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("accrefno","req","Enter Ref No ");
				frmvalidator.addValidation("dateID","req","Enter Accident Date");   
			});
			
		}
		else	{$("#AccidentValidGridID").html('');}				
	});
	
	/* ------ INFINGMENT FORM ------ */
	$("#infringmentSubmit").on('click',function()
	{
		var statusID  = $("#INFRGstatusID").val();
		var dsce_stID = $('#description').prop('disabled');
		var dsce_vlID = $('#description').val();
		var cmdiscID  = $('#cmdiscID').val();
		$("#INFRGValidGridID").html('');
		
		statusID = (statusID == null || statusID == 'NaN' || isNaN(statusID) || statusID == '' || typeof statusID === 'undefined') ? 0 : statusID;
		cmdiscID = (cmdiscID == null || cmdiscID == 'NaN' || isNaN(cmdiscID) || cmdiscID == '' || typeof cmdiscID === 'undefined') ? 0 : cmdiscID;
		
		AuditTrialCheck_INFRINGMENT(statusID,dsce_stID,dsce_vlID,cmdiscID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="InfringYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="InfringNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#INFRGValidGridID").html('');
			$("#INFRGValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#InfringYes").offset().top
			}, 2000);
			
			$("#InfringNo").on('click',function()
			{
				$("#INFRGValidGridID").html('');
				$("#infrefno").focus();
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#InfringYes").on('click',function()
			{
				$("#INFRGValidGridID").html('');
				$("select#INFRGstatusID").prop('selectedIndex', 2);
				
				ClearErrorMessages('empID,timeID,dplostID,busID,dateID1,dateID2,dateID3,dateID4,invID,inftypeID,description,description1');
				ClearErrorMessages('cmdiscID,wrtypeID,mcomments,INFRGstatusID,intvID,intvDate');
		
				var frmvalidator  = new Validator("register");    
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("infrefno","req","Enter Ref No ");
				frmvalidator.addValidation("vehicle","req","Enter Vehicle Rego");
				frmvalidator.addValidation("dateID","req","Enter Date Occur");
			});
			
		}
		else	{$("#INFRGValidGridID").html('');}				
	});
	
	/* ------ INSPECTION FORM ------ */
	$("#inspectionSubmit").on('click',function()
	{
		var statusID = $("#INSPCstatusID").val();
		var fine_sID = $('#fineID').prop('disabled');
		var fine_vID = $('#fineID').val();		
		var cmdiscID = $('#cmdiscID').val();		
		$("#InspcValidGridID").html('');
		
		statusID = (statusID == null || statusID == 'NaN' || isNaN(statusID) || statusID == '' || typeof statusID === 'undefined') ? 0 : statusID;		
		fine_vID = (fine_vID == null || fine_vID == 'NaN' || isNaN(fine_vID) || fine_vID == '' || typeof fine_vID === 'undefined') ? 0 : fine_vID;
		cmdiscID = (cmdiscID == null || cmdiscID == 'NaN' || isNaN(cmdiscID) || cmdiscID == '' || typeof cmdiscID === 'undefined') ? 0 : cmdiscID;
		
		AuditTrialCheck_INSPECTION(statusID,fine_sID,fine_vID,cmdiscID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';				

				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="InspYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="InspNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#InspcValidGridID").html('');
			$("#InspcValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#InspYes").offset().top
			}, 2000);
			
			$("#InspNo").on('click',function()
			{
				$("#InspcValidGridID").html('');
				$("#rptno").focus();
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#InspYes").on('click',function()
			{
				$("#InspcValidGridID").html('');
				$("select#INSPCstatusID").prop('selectedIndex', 2);
				
				ClearErrorMessages('empID,inspectedby,insrypeID,fineID,dateID1,servicenoID,serviceinfID,srtpointID,shiftID,busID,timeID1');
				ClearErrorMessages('timeID2,timeID3,description,invstID,trisID,description2,description_3,cmdiscID,intvID,intvDate');
		
				var frmvalidator  = new Validator("register");
				frmvalidator.clearAllValidations();			
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();
				
				frmvalidator.addValidation("rptno","req","Enter Report No ");	
				frmvalidator.addValidation("dateID","req","Enter Report Date"); 
				
				frmvalidator.addValidation("INSPCstatusID","num","Select Closed");
				frmvalidator.addValidation("INSPCstatusID","gt=0","Select Closed");
			});
			
		}
		else	{$("#InspcValidGridID").html('');}				
	});

	/* ------ SIR-REGIS FORM ------ */
	$("#sirregisSubmit").on('click',function()
	{
		var statusID   = $("#SIRstatusID").val();
		var fupreqID   = $("#fupreqID").val(); 
		var resultsINV = $("#resultsINV").val();
		var sstatusID  = $("#sstatusID").val();
		
		$("#SirRegisValidGridID").html('');
		
		statusID = (isNaN(statusID) || statusID == '' || statusID == 'null' || typeof statusID === 'undefined') ? 0 : statusID;		
		fupreqID = (isNaN(fupreqID) || fupreqID == '' || fupreqID == 'null' || typeof fupreqID === 'undefined') ? 0 : fupreqID;
		resultsINV = (isNaN(resultsINV) || resultsINV == '' || resultsINV == 'null' || typeof resultsINV === 'undefined') ? 0 : resultsINV;
		
		//alert(statusID + '-' + acteffID + '-' + fupreqID);

		AuditTrialCheck_SIR(statusID,fupreqID,resultsINV,sstatusID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 2)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="SirYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="SirtNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#SirRegisValidGridID").html('');
			$("#SirRegisValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#SirYes").offset().top
			}, 2000);
			
			$("#SirtNo").on('click',function()
			{
				$("#SirRegisValidGridID").html('');
				$("#refno").focus(); 				
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#SirYes").on('click',function()
			{
				$("#SirRegisValidGridID").html('');
				$("select#SIRstatusID").prop('selectedIndex', 1);
				
				ClearErrorMessages('resultsINV,otherINV,invID,invDate,action,actID,actDate');		
				ClearErrorMessages('acteffID,fupreqID,fupreqDT,fupDesc,fupcmpID,fupcmpDT,clsoutDT,orgadvDT');
		
				var frmvalidator  = new Validator("register");
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("refno","req","Enter Improvement No ");
				frmvalidator.addValidation("issuetoDT","req","Enter Issued Date ");
				frmvalidator.addValidation("description","req","Enter Description ");

				frmvalidator.addValidation("srtypeID","num","Select SIR Type");
				frmvalidator.addValidation("srtypeID","gt=0","Select SIR Type");
				
				frmvalidator.addValidation("issuedTO","num","Select Issued To ");
				frmvalidator.addValidation("issuedTO","gt=0","Select Issued To ");

				frmvalidator.addValidation("originatorID","num","Select Originator");
				frmvalidator.addValidation("originatorID","gt=0","Select Originator");
			});
			
		}
		else	{$("#SirRegisValidGridID").html('');}				
	});
		
	/* ------ HIZ-REGIS FORM ------ */
	$("#hizregisSubmit").on('click',function()
	{
		$("#HizRegisValidGridID").html('');
		
		var statusID  = $("#HIZstatusID").val();		
		var acteffID  = $("#acteffID").val(); 
		var fupreqID  = $("#fupreqID").val();
		var sstatusID = $("#sstatusID").val();
		statusID = (isNaN(statusID) || statusID == '' || statusID == 'null' || typeof statusID === 'undefined') ? 0 : statusID;
		acteffID = (isNaN(acteffID) || acteffID == '' || acteffID == 'null' || typeof acteffID === 'undefined') ? 0 : acteffID;
		fupreqID = (isNaN(fupreqID) || fupreqID == '' || fupreqID == 'null' || typeof fupreqID === 'undefined') ? 0 : fupreqID;
		
		//alert(statusID + '-' + acteffID + '-' + fupreqID);

		AuditTrialCheck_HAZARD(statusID,acteffID,fupreqID,sstatusID);
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 2)
		{
			var HTML = '';
			
			HTML += '<div class="row alert-warning">';
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-10">';
					HTML += '<div class="col-xs-12"><label for="section">'+messageSTRING+'</label></div><br />';
					HTML += '<div class="col-xs-12">';
						HTML += '<input type="button" class="btn btn-danger" id="HizYes" value="Yes" />';
						HTML += '<input type="button" class="btn btn-danger" id="HizNo" value="No&nbsp;" style="margin-left: 19px;"/>';
					HTML += '</div>';
				HTML += '</div>';
				
				HTML += '<div class="col-xs-1"></div>';
				
				HTML += '<div class="col-xs-12"><hr style="border:#F56954 1px solid;" /></div>';
			HTML += '</div>';
			
			$("#HizRegisValidGridID").html('');
			$("#HizRegisValidGridID").html(HTML);
			
			$('html, body').animate({
				scrollTop: $("#HizYes").offset().top
			}, 2000);
			
			$("#HizNo").on('click',function()
			{
				$("#HizRegisValidGridID").html('');				
				$("#refno").focus(); 				
				$('html, body').animate({scrollTop: '0px'},50);						  
			});
			
			$("#HizYes").on('click',function()
			{
				$("#HizRegisValidGridID").html('');
				$("select#HIZstatusID").prop('selectedIndex', 1);
				
				ClearErrorMessages('dateID,timeID,descriptionACT');		
				ClearErrorMessages('optIDu1,optIDu2,optIDu3,optIDu4,optIDu5,optIDu6TX,optIDu7,descriptionINV,invID,invDate,descriptionACD,actID,actDate');
				ClearErrorMessages('optIDm1,optIDm2,optIDm3,optIDm4,optIDm5,optIDm6TX,optIDm7,acteffID,fupreqID,fupreqDT,fupcmpID,fupcmpDT,fupDesc,hzrconDT,empadvDT');
		
				var frmvalidator  = new Validator("register");    
				frmvalidator.clearAllValidations();
				frmvalidator.EnableOnPageErrorDisplay();    
				frmvalidator.EnableMsgsTogether();
				
				frmvalidator.addValidation("HIZrefno","req","Enter HZ No ");
				frmvalidator.addValidation("rdateID","req","Enter Report Date"); 
				frmvalidator.addValidation("location","req","Enter Location"); 
				frmvalidator.addValidation("description","req","Enter Description ");
				frmvalidator.addValidation("rcdateID","req","Enter Reciept Date ");
				
				frmvalidator.addValidation("reportBY","num","Select Reported By");
				frmvalidator.addValidation("reportBY","gt=0","Select Reported By"); 		
				
				frmvalidator.addValidation("jobID","num","Select Job Title");
				frmvalidator.addValidation("jobID","gt=0","Select Job Title");	
				
				frmvalidator.addValidation("hztypeID","num","Select Hazard Type");
				frmvalidator.addValidation("hztypeID","gt=0","Select Hazard Type");
				
				frmvalidator.addValidation("empID","num","Select Staff Name");
				frmvalidator.addValidation("empID","gt=0","Select Staff Name");
				
				frmvalidator.addValidation("fdesigID","num","Select Designation");
				frmvalidator.addValidation("fdesigID","gt=0","Select Designation");
			});
			
		}
		else	{$("#HizRegisValidGridID").html('');}	
		
	});
	
	var AuditTrialCheck_INCIDENT = function(statusID,sincID,offtypeID,cmdiscID)
	{
		var radioN = $("#radioN").prop('checked');
		var radioA = $("#radioA").prop('checked');
		var transN = $("#transN").prop('checked');
		var transA = $("#transA").prop('checked');
		var firebN = $("#firebN").prop('checked');
		var firebA = $("#firebA").prop('checked');
		var ambulN = $("#ambulN").prop('checked');
		var dutyoN = $("#dutyoN").prop('checked');
		var dutyoA = $("#dutyoA").prop('checked');
		var depotN = $("#depotN").prop('checked');
		var depotA = $("#depotA").prop('checked');
		var ptaopN = $("#ptaopN").prop('checked');
		var ptaopA = $("#ptaopA").prop('checked');				
		var westrN = $("#westrN").prop('checked');
		var westrA = $("#westrA").prop('checked');
		var vdeofA = $("#vdeofA").prop('checked');
		var bstatusID = $("#brs_statusID").prop('checked');
		var pstatusID = $("#plrefID").prop('checked');
		var pattendID = $("#attendedID_2").prop('checked');
		
		ClearErrorMessages('increfno,dateID,description,empID,cmdiscID,mcomments,wrtypeID,dmginjury,crossst,dmvalue,offtypeID,grfcolour,whbwdescID,grfitemID');
		ClearErrorMessages('ptarefNO,plcadno,plcvehicle,policename,plcactionID,timeID,location,reportby,description,action,busID,routeID,shiftID,plrefnoID');
		ClearErrorMessages('suburb,actbyID,inctypeID,intvID,intvDate');
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var frmvalidator  = new Validator("register");  
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			frmvalidator.addValidation("increfno","req","Enter Ref No ");
			frmvalidator.addValidation("dateID","req","Enter Date Occured");
			frmvalidator.addValidation("description","req","Enter The Description");
			
			frmvalidator.addValidation("empID","num","Select Driver Name ");
			frmvalidator.addValidation("empID","gt=0","Select Driver Name ");
			
			frmvalidator.addValidation("cmdiscID","num","Select Discipline Required");
			frmvalidator.addValidation("cmdiscID","gt=0","Select Discipline Required");
			
			if(parseInt(cmdiscID) > 0 && parseInt(cmdiscID) == 1)
			{
				frmvalidator.addValidation("mcomments","req","Enter Manager Comments");

				frmvalidator.addValidation("wrtypeID","num","Select Warning Type");
				frmvalidator.addValidation("wrtypeID","gt=0","Select Warning Type");
			}
			
			/* CHECK-BOX GROUP - SMS*/
			if(parseInt(sincID) == 1)	/*	Security Incident - YES */
			{  
				if(radioN == true || radioA == true || transN == true || transA == true || firebN == true || firebA == true || ambulN == true || dutyoN == true || dutyoA == true || depotN == true || depotA == true || ptaopN == true || ptaopA == true || westrN == true || westrA == true || vdeofA == true)
				{
					

					$("#checkbox_groupID").text('');
				}
				else
				{
					$("select#INCstatusID").prop('selectedIndex', 2);

					$("#checkbox_groupID").text('');
					$("#checkbox_groupID").text('Notified/Attended Checkbox Group is empty. You can\'t close without filling it');
				}
			} 
			
			if(parseInt(sincID) == 1)	/*	Security Incident - YES */
			{
				frmvalidator.addValidation("dmginjury","req","Enter Damage/Injury");
				
				frmvalidator.addValidation("crossst","req","Enter Cross Street");
				frmvalidator.addValidation("dmvalue","req","Enter Damage Value");
				
				frmvalidator.addValidation("offtypeID","num","Select Offence Type ");
				frmvalidator.addValidation("offtypeID","gt=0","Select Offence Type ");
				
				if(parseInt(offtypeID) == 144)
				{
					frmvalidator.addValidation("grfcolour","req","Enter Graffiti Colour");
					
					frmvalidator.addValidation("whbwdescID","req","Enter What has been written");
					
					frmvalidator.addValidation("grfitemID","num","Select Graffiti Item");
					frmvalidator.addValidation("grfitemID","gt=0","Select Graffiti Item"); 
				}
				
				if(transN == true || transA == true || ptaopN == true || ptaopA == true)
				{
					frmvalidator.addValidation("ptarefNO","req","Enter PTA Ref No ");
				}
				
				if(pattendID == true)
				{
					frmvalidator.addValidation("plcadno","req","Enter Police CAD No");
					frmvalidator.addValidation("plcvehicle","req","Enter Police Vehicle");
					frmvalidator.addValidation("policename","req","Enter Police Name");
					frmvalidator.addValidation("plcactionID","req","Select Police Action");
					frmvalidator.addValidation("plcactionID","gt=0","Select Police Action"); 
				} 
			}
			
			if(parseInt(sincID) == 2 || parseInt(sincID) == 1)	/*	Security Incident - NO */
			{
				frmvalidator.addValidation("timeID","req","Enter Time Occurred");
				frmvalidator.addValidation("location","req","Enter The Location");
				frmvalidator.addValidation("reportby","req","Enter The Reported By");
				frmvalidator.addValidation("description","req","Enter The Description");
				frmvalidator.addValidation("action","req","Enter The Action");
				
				if(bstatusID == true)
				{
					frmvalidator.addValidation("busID","req","Enter Bus No");
					frmvalidator.addValidation("routeID","req","Enter Route No");
					frmvalidator.addValidation("shiftID","req","Enter Shift No");
				}
				
				if(pstatusID == true)
				{
					frmvalidator.addValidation("plrefnoID","req","Enter Police Ref No");
				}
				
				frmvalidator.addValidation("suburb","num","Select Suburb ");
				frmvalidator.addValidation("suburb","gt=0","Select Suburb ");
				
				frmvalidator.addValidation("inctypeID","num","Select Incident Type ");
				frmvalidator.addValidation("inctypeID","gt=0","Select Incident Type ");
				
				frmvalidator.addValidation("actbyID","num","Select Action Taken By ");
				frmvalidator.addValidation("actbyID","gt=0","Select Action Taken By ");
			}

		}
		else
		{
			var frmvalidator  = new Validator("register");  
			frmvalidator.clearAllValidations();			
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("increfno","req","Enter Ref No ");
			frmvalidator.addValidation("dateID","req","Enter Date Occured");
			frmvalidator.addValidation("description","req","Enter The Description");			
			$("#checkbox_groupID").text('');
		}
		
	}
	
	var AuditTrialCheck_COMPLAINT = function(statusID,accID,respID,cmdiscID,locationID,substanID,tickID_1,cmltypeID)
	{
		ClearErrorMessages('cmprefno,serDT,dateID,timeID,busID,routeID,location,suburb,cmltypeID,creasonID,description,empID,accID,substanID,faultID,invID,invdate,furaction');
		ClearErrorMessages('respID,resdate,outcome,trisID,cmdiscID,CMPLstatusID,mcomments,wrtypeID,intvID,intvDate');
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var frmvalidator  = new Validator("register");    
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			if(cmltypeID == 491 || cmltypeID == 492)	{frmvalidator.addValidation("cmprefno","req","Enter Ref No ");}
			frmvalidator.addValidation("timeID","req","Enter Incident Time");
			frmvalidator.addValidation("dateID","req","Enter Incident Date");
			frmvalidator.addValidation("cmdueDT","req","Enter Due Date");
			frmvalidator.addValidation("serDT","req","Comment ReportedOn");
			frmvalidator.addValidation("furaction","req","Enter Customer Response Details");			
			frmvalidator.addValidation("outcome","req","Enter Action Taken / Recommendations");
			frmvalidator.addValidation("description","req","Enter Description");
			frmvalidator.addValidation("trisID","shouldselchk","Select TRIS Complete");
			frmvalidator.addValidation("cmltypeID","gt=0","Customer feedback type ");
			frmvalidator.addValidation("cmltypeID","num","Customer feedback type ");
			
			if(tickID_1 == false)
			{
				frmvalidator.addValidation("empID","num","Select Driver Name ");
				frmvalidator.addValidation("empID","gt=0","Select Driver Name ");
			}
			
			if(parseInt(accID) <= 0 || (parseInt(accID) == 52 || parseInt(accID) == 221 || parseInt(accID) == 41 || parseInt(accID) == 48 || parseInt(accID) == 49))
			{
				frmvalidator.addValidation("cmdiscID","num","Select Discipline Required");
				frmvalidator.addValidation("cmdiscID","gt=0","Select Discipline Required");
				
				if(parseInt(cmdiscID) > 0 && parseInt(cmdiscID) == 1)
				{
					frmvalidator.addValidation("mcomments","req","Enter Manager Comments");
					frmvalidator.addValidation("wrtypeID","num","Select Warning Type");
					frmvalidator.addValidation("wrtypeID","gt=0","Select Warning Type");
					
					frmvalidator.addValidation("intvDate","req","Enter Interviewed Date");
					frmvalidator.addValidation("intvID","num","Select Interviewed By");
					frmvalidator.addValidation("intvID","gt=0","Select Interviewed By");
				}
			}
			
			if(parseInt(respID) != 46)
			{		
				frmvalidator.addValidation("resdate","req","Enter Response Date");
				frmvalidator.addValidation("respID","num","Select Response Method ");
				frmvalidator.addValidation("respID","gt=0","Select Response Method "); 
			}
			
			if(parseInt(accID) <= 0)
			{
				frmvalidator.addValidation("substanID","num","Select Substantiated ");
				frmvalidator.addValidation("substanID","gt=0","Select Substantiated ");
				
				frmvalidator.addValidation("faultID","num","Select Fault/Not at Fault ");
				frmvalidator.addValidation("faultID","gt=0","Select Fault/Not at Fault ");
				
				frmvalidator.addValidation("invID","num","Select Investigated By ");
				frmvalidator.addValidation("invID","gt=0","Select Investigated By ");
				
				if($("#invID").val() == 1001)
				{
					/* DO - NOTHING */
				}
				else
				{
					frmvalidator.addValidation("invdate","req","Enter Investigated Date");
				}
			}
			
			frmvalidator.addValidation("creasonID","num","Select Comment Line Reason ");
			frmvalidator.addValidation("creasonID","gt=0","Select Comment Line Reason ");
			
			frmvalidator.addValidation("cmltypeID","num","Select C. Line Type");
			frmvalidator.addValidation("cmltypeID","gt=0","Select C. Line Type");
			
			frmvalidator.addValidation("accID","gt=0","Select Comment Line Type ");
			frmvalidator.addValidation("accID","num","Select Comment Line Type ");
			
			if(locationID != '')
			{
				frmvalidator.addValidation("suburb","num","Select Suburb ");
				frmvalidator.addValidation("suburb","gt=0","Select Suburb ");
			}
			
			if(parseInt(accID) == 52)
			{				
				frmvalidator.addValidation("busID","req","Enter Bus No");
				frmvalidator.addValidation("routeID","req","Enter Service No");
				frmvalidator.addValidation("location","req","Enter Location");
				
				frmvalidator.addValidation("substanID","num","Select Substantiated ");
				frmvalidator.addValidation("substanID","gt=0","Select Substantiated ");
				
				frmvalidator.addValidation("faultID","num","Select Fault/Not at Fault ");
				frmvalidator.addValidation("faultID","gt=0","Select Fault/Not at Fault "); 			
			}
			
			if(parseInt(accID) == 224 || parseInt(accID) == 52 || parseInt(accID) == 48 || parseInt(accID) == 221 || parseInt(accID) == 49)
			{
				frmvalidator.addValidation("invID","num","Select Investigated By ");
				frmvalidator.addValidation("invID","gt=0","Select Investigated By ");
				
				frmvalidator.addValidation("invdate","req","Enter Investigated Date");
			}
			
			if(parseInt(accID) == 52 || parseInt(accID) == 224 || parseInt(accID) == 221 || parseInt(accID) == 49)
			{
				if(parseInt(substanID) <= 0 || parseInt(substanID) == 1)
				{
					frmvalidator.addValidation("invID","num","Select Investigated By ");
					frmvalidator.addValidation("invID","gt=0","Select Investigated By ");
				}
				
				if($("#invID").val() == 1001)
				{
					/* DO - NOTHING */
				}
				else
				{
					if(parseInt(substanID) <= 0 || parseInt(substanID) == 1)
					{
						frmvalidator.addValidation("invdate","req","Enter Investigated Date");
					}
				}
			}
			
			if(parseInt(accID) == 48 && parseInt(cmdiscID) == 1)
			{
				if(parseInt(substanID) <= 0 || parseInt(substanID) == 1)
				{
					frmvalidator.addValidation("invID","num","Select Investigated By ");
					frmvalidator.addValidation("invID","gt=0","Select Investigated By ");
				}
				
				if($("#invID").val() == 1001)
				{
					/* DO - NOTHING */
				}
				else
				{
					if(parseInt(substanID) <= 0 || parseInt(substanID) == 1)
					{
						frmvalidator.addValidation("invdate","req","Enter Investigated Date");
					}
				}
			}
		}
		else
		{
			var frmvalidator  = new Validator("register"); 
			frmvalidator.clearAllValidations();			
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			if(cmltypeID == 491 || cmltypeID == 492)	{frmvalidator.addValidation("cmprefno","req","Enter Ref No ");}
			frmvalidator.addValidation("dateID","req","Enter Incident Date");
			frmvalidator.addValidation("serDT","req","Comment ReportedOn"); 
			frmvalidator.addValidation("cmdueDT","req","Enter Due Date");
			
			frmvalidator.addValidation("cmltypeID","gt=0","Customer feedback type ");
			frmvalidator.addValidation("cmltypeID","num","Customer feedback type ");
		}
	}
	
	var AuditTrialCheck_ACCIDENT = function(statusID,tickID_1,tpartyID,insinvID,witnesID,cmdiscID)
	{
		ClearErrorMessages('accrefno,busID,dateID,timeID,tickID_2,empID,acccatID,plcntID,thpnameID,regisnoID,thcontactID,damagetobusID,witnessID,insinvolvedID,insurerID,claimnoID,invnoID');
		ClearErrorMessages('accID,responsibleID,location,suburb,description,rprcost,othcost,optID1,optID2,optID3,outcome,invID,cmdiscID,intvID,intvDate,mcomments,wrtypeID');
		
		if(parseInt(statusID) > 0 && (parseInt(statusID) == 1 || parseInt(statusID) == 3))
		{
			var frmvalidator  = new Validator("register");
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("accrefno","req","Enter Ref No ");
			frmvalidator.addValidation("dateID","req","Enter Accident Date");
			frmvalidator.addValidation("busID","req","Enter Bus No");
			frmvalidator.addValidation("timeID","req","Enter Accident Time");
			
			if(insinvID == 1)
			{
				frmvalidator.addValidation("insurerID","req","Enter Insurer");
				frmvalidator.addValidation("claimnoID","req","Enter Claim No");
				frmvalidator.addValidation("invnoID","req","Enter Invoice No");
			}
			
			frmvalidator.addValidation("location","req","Enter Location");
			frmvalidator.addValidation("description","req","Enter Reason");
			frmvalidator.addValidation("outcome","req","Enter Investigation Outcome / Recommendations");
			frmvalidator.addValidation("rprcost","req","Enter Bus Repairs (Cost)");
			frmvalidator.addValidation("othcost","req","Enter Other Repairs (Cost)");
			
			if(parseInt(tickID_1) <= 0)
			{
				frmvalidator.addValidation("empID","num","Select Driver Name ");
				frmvalidator.addValidation("empID","gt=0","Select Driver Name ");
			}
			
			frmvalidator.addValidation("cmdiscID","num","Select Discipline Required");
			frmvalidator.addValidation("cmdiscID","gt=0","Select Discipline Required");
			
			if(parseInt(cmdiscID) > 0 && parseInt(cmdiscID) == 1)
			{
				frmvalidator.addValidation("mcomments","req","Enter Manager Comments");

				frmvalidator.addValidation("wrtypeID","num","Select Warning Type");
				frmvalidator.addValidation("wrtypeID","gt=0","Select Warning Type");
				
				frmvalidator.addValidation("intvDate","req","Enter Interviewed Date");
				
				frmvalidator.addValidation("intvID","num","Select Interviewed By");
				frmvalidator.addValidation("intvID","gt=0","Select Interviewed By");
			}
			
			if(tpartyID == true)
			{
				frmvalidator.addValidation("thpnameID","req","Enter Third Party Name");
				frmvalidator.addValidation("regisnoID","req","Enter Third Party Rego No");
				frmvalidator.addValidation("thcontactID","req","Enter Third Party Contact Info"); 
			}
			
			if(witnesID == true)
			{
				frmvalidator.addValidation("witnessName","req","Enter Witness Name");
				frmvalidator.addValidation("witnessContact","req","Enter Witness Contact No");
			}
			
			frmvalidator.addValidation("acccatID","num","Select Accident Category ");
			frmvalidator.addValidation("acccatID","gt=0","Select Accident Category ");
			
			frmvalidator.addValidation("suburb","num","Select Subrub ");
			frmvalidator.addValidation("suburb","gt=0","Select Subrub ");
			
			frmvalidator.addValidation("damagetobusID","num","Select Damage to Bus ");
			frmvalidator.addValidation("damagetobusID","gt=0","Select Damage to Bus ");
			
			frmvalidator.addValidation("invID","num","Select Interviewed By ");
			frmvalidator.addValidation("invID","gt=0","Select Interviewed By ");
			
			frmvalidator.addValidation("accID","num","Select Accident Details ");
			frmvalidator.addValidation("accID","gt=0","Select Accident Details ");
			
			frmvalidator.addValidation("responsibleID","num","Select Driver Responsible ");
			frmvalidator.addValidation("responsibleID","gt=0","Select Driver Responsible ");
			
			frmvalidator.addValidation("optID3","num","Select Driver Drug Tested ");
			frmvalidator.addValidation("optID3","gt=0","Select Driver Drug Tested ");
			
			frmvalidator.addValidation("optID1","num","Select Photographs of Damage ");
			frmvalidator.addValidation("optID1","gt=0","Select Photographs of Damage ");
			
			frmvalidator.addValidation("optID2","num","Select Driver Breath Tested");
			frmvalidator.addValidation("optID2","gt=0","Select Driver Breath Tested"); 
		}
		else
		{
			var frmvalidator  = new Validator("register");
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("accrefno","req","Enter Ref No ");
			frmvalidator.addValidation("dateID","req","Enter Accident Date");
		}
	}
	
	var AuditTrialCheck_INFRINGMENT = function(statusID,dsce_stID,dsce_vlID,cmdiscID)
	{
		ClearErrorMessages('infrefno,vehicle,empID,dateID,timeID,dplostID,busID,dateID1,dateID2,dateID3,dateID4,invID,inftypeID,description,description1');
		ClearErrorMessages('cmdiscID,wrtypeID,mcomments,INFRGstatusID,intvID,intvDate');
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{
			var frmvalidator  = new Validator("register");    
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("infrefno","req","Enter Ref No ");
			frmvalidator.addValidation("vehicle","req","Enter Vehicle Rego");
			frmvalidator.addValidation("dateID","req","Enter Date Occur");
			frmvalidator.addValidation("timeID","req","Enter Time");			
			frmvalidator.addValidation("dateID1","req","Enter Issue Date");
			frmvalidator.addValidation("dateID2","req","Enter Compliance Date");
			frmvalidator.addValidation("dateID3","req","Enter Date Recieved");
			frmvalidator.addValidation("dateID4","req","Enter Date Sent");
			
			frmvalidator.addValidation("empID","num","Select Employee Name ");
			frmvalidator.addValidation("empID","gt=0","Select Employee Name ");
			
			frmvalidator.addValidation("cmdiscID","num","Select Discipline Required");
			frmvalidator.addValidation("cmdiscID","gt=0","Select Discipline Required");
			
			if(dsce_stID == false)
			{
				if(dsce_vlID != '')	{}
				else
				{
					frmvalidator.addValidation("description","req","Enter Description");
				}
			}
			
			frmvalidator.addValidation("description1","req","Enter Location of Infringement");
					
			if(parseInt(cmdiscID) > 0 && parseInt(cmdiscID) == 1)
			{
				frmvalidator.addValidation("mcomments","req","Enter Manager Comments");

				frmvalidator.addValidation("wrtypeID","num","Select Warning Type");
				frmvalidator.addValidation("wrtypeID","gt=0","Select Warning Type");
				
				frmvalidator.addValidation("intvDate","req","Enter Interviewed Date");
				
				frmvalidator.addValidation("intvID","num","Select Interviewed By");
				frmvalidator.addValidation("intvID","gt=0","Select Interviewed By");
			}
			
			frmvalidator.addValidation("invID","num","Select Interviewed By ");
			frmvalidator.addValidation("invID","gt=0","Select Interviewed By ");
			
			frmvalidator.addValidation("inftypeID","num","Select Infringement Type ");
			frmvalidator.addValidation("inftypeID","gt=0","Select Infringement Type ");
		}
		else
		{
			var frmvalidator  = new Validator("register");    
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();

			frmvalidator.addValidation("infrefno","req","Enter Ref No ");
			frmvalidator.addValidation("vehicle","req","Enter Vehicle Rego");
			frmvalidator.addValidation("dateID","req","Enter Date Occur");
		}		
	}	
	
	var AuditTrialCheck_INSPECTION = function(statusID,fine_sID,fine_vID,cmdiscID)
	{
		ClearErrorMessages('rptno,dateID,empID,inspectedby,insrypeID,fineID,dateID1,servicenoID,serviceinfID,srtpointID,shiftID,busID,timeID1');
		ClearErrorMessages('timeID2,timeID3,description,invstID,trisID,description2,description_3,INSPCstatusID,cmdiscID,wrtypeID,wrtypeID,intvID,intvDate');
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 1)
		{	
			var frmvalidator  = new Validator("register");
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			frmvalidator.addValidation("rptno","req","Enter Report No ");
			frmvalidator.addValidation("dateID","req","Enter Report Date");
			frmvalidator.addValidation("serviceinfID","req","Enter Service Info ");			
			frmvalidator.addValidation("shiftID","req","Enter Shift No ");
			frmvalidator.addValidation("busID","req","Enter Bus No ");
			frmvalidator.addValidation("dateID1","req","Enter Date Inspected ");
			frmvalidator.addValidation("description","req","Enter Description ");
			frmvalidator.addValidation("description2","req","Enter PTA Response ");
			frmvalidator.addValidation("timeID1","req","Enter Scheduled Depature Time ");
			frmvalidator.addValidation("timeID2","req","Enter Timing Point Time ");
			frmvalidator.addValidation("timeID3","req","Enter Actual Time ");
			
			frmvalidator.addValidation("inspectedby","num","Select Inspected By ");
			frmvalidator.addValidation("inspectedby","gt=0","Select Inspected By ");
			
			frmvalidator.addValidation("insrypeID","num","Select Inspection Result ");
			frmvalidator.addValidation("insrypeID","gt=0","Select Inspection Result ");
			
			frmvalidator.addValidation("servicenoID","num","Select Service No ");
			frmvalidator.addValidation("servicenoID","gt=0","Select Service No ");
			
			frmvalidator.addValidation("srtpointID","num","Select Service Time Point ");
			frmvalidator.addValidation("srtpointID","gt=0","Select Service Time Point ");
			
			frmvalidator.addValidation("empID","num","Select Driver Name ");
			frmvalidator.addValidation("empID","gt=0","Select Driver Name ");
			
			frmvalidator.addValidation("invstID","num","Select Investigated By ");
			frmvalidator.addValidation("invstID","gt=0","Select Investigated By ");
			
			frmvalidator.addValidation("INSPCstatusID","num","Select Closed");
			frmvalidator.addValidation("INSPCstatusID","gt=0","Select Closed");
			
			frmvalidator.addValidation("cmdiscID","num","Select Discipline Required");
			frmvalidator.addValidation("cmdiscID","gt=0","Select Discipline Required");
			
			frmvalidator.addValidation("trisID","shouldselchk","Select TRIS Complete");
			
			if(fine_sID == false)
			{ 
				if(parseInt(fine_vID) <= 0)
				{
					frmvalidator.addValidation("fineID","num","Select Fine");
					frmvalidator.addValidation("fineID","gt=0","Select Fine");
				}
			}
			
			if(parseInt(cmdiscID) > 0 && parseInt(cmdiscID) == 1)
			{
				frmvalidator.addValidation("mcomments","req","Enter Manager Comments");

				frmvalidator.addValidation("wrtypeID","num","Select Warning Type");
				frmvalidator.addValidation("wrtypeID","gt=0","Select Warning Type");
				
				frmvalidator.addValidation("intvDate","req","Enter Interviewed Date");
				
				frmvalidator.addValidation("intvID","num","Select Interviewed By");
				frmvalidator.addValidation("intvID","gt=0","Select Interviewed By");
			}  
		}
		else
		{
			var frmvalidator  = new Validator("register");
			frmvalidator.clearAllValidations();			
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			frmvalidator.addValidation("rptno","req","Enter Report No ");	
			frmvalidator.addValidation("dateID","req","Enter Report Date");
			
			frmvalidator.addValidation("INSPCstatusID","num","Select Closed");
			frmvalidator.addValidation("INSPCstatusID","gt=0","Select Closed"); 
		}
	}
	
	var AuditTrialCheck_SIR = function(statusID,fupreqID,resultsINV,sstatusID)
	{
		ClearErrorMessages('issuetoDT,refno,srtypeID,description,issuedTO,originatorID,resultsINV,otherINV,invID,invDate,action,actID,actDate');		
		ClearErrorMessages('acteffID,fupreqID,fupreqDT,fupDesc,fupcmpID,fupcmpDT,clsoutDT,orgadvDT');

		if(parseInt(statusID) > 0 && parseInt(statusID) == 2)
		{
			var frmvalidator  = new Validator("register");    
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			frmvalidator.addValidation("refno","req","Enter Improvement No ");	
			
			frmvalidator.addValidation("srtypeID","num","Select SIR Type");
			frmvalidator.addValidation("srtypeID","gt=0","Select SIR Type");			
			
			frmvalidator.addValidation("description","req","Enter Description ");
			
			frmvalidator.addValidation("issuedTO","num","Select Issued To ");
			frmvalidator.addValidation("issuedTO","gt=0","Select Issued To ");
			
			frmvalidator.addValidation("originatorID","num","Select Originator");
			frmvalidator.addValidation("originatorID","gt=0","Select Originator");
			
			frmvalidator.addValidation("issuetoDT","req","Enter Issued Date ");
			
			frmvalidator.addValidation("resultsINV","num","Select Investigation Results");
			frmvalidator.addValidation("resultsINV","gt=0","Select Investigation Results");
			
			if(parseInt(resultsINV) == 8000)
			{
				frmvalidator.addValidation("otherINV","req","Enter Investigation Other");	
			}
			
			frmvalidator.addValidation("invID","num","Select Investigation By");
			frmvalidator.addValidation("invID","gt=0","Select Investigation By");
			
			frmvalidator.addValidation("invDate","req","Enter Investigation Date ");
			
			frmvalidator.addValidation("action","req","Enter Action ");
			frmvalidator.addValidation("actDate","req","Enter Action Date ");
			
			frmvalidator.addValidation("actID","num","Select Action By");
			frmvalidator.addValidation("actID","gt=0","Select Action By");
			
			frmvalidator.addValidation("acteffID","num","Select Action Effective");
			frmvalidator.addValidation("acteffID","gt=0","Select Action Effective");
			
			frmvalidator.addValidation("fupreqID","num","Select Follow-up Req");
			frmvalidator.addValidation("fupreqID","gt=0","Select Follow-up Req");
			
			if(parseInt(fupreqID) <= 1)
			{
				frmvalidator.addValidation("fupreqDT","req","Proposed Follow-up Date");
				frmvalidator.addValidation("fupDesc","req","Follow-up Details ");
				
				frmvalidator.addValidation("fupcmpDT","req","Follow-up Comp. Date ");
				frmvalidator.addValidation("fupcmpID","num","Select Follow-up Completed By");
				frmvalidator.addValidation("fupcmpID","gt=0","Select Follow-up Completed By");
			}
			
			frmvalidator.addValidation("clsoutDT","req","Close Out Date ");
			frmvalidator.addValidation("orgadvDT","req","Date Originator Advised "); 
			
			if(sstatusID == 1)
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
			
			frmvalidator.addValidation("refno","req","Enter Improvement No ");
			frmvalidator.addValidation("issuetoDT","req","Enter Issued Date ");
			frmvalidator.addValidation("description","req","Enter Description ");

			frmvalidator.addValidation("srtypeID","num","Select SIR Type");
			frmvalidator.addValidation("srtypeID","gt=0","Select SIR Type");

			frmvalidator.addValidation("issuedTO","num","Select Issued To ");
			frmvalidator.addValidation("issuedTO","gt=0","Select Issued To ");

			frmvalidator.addValidation("originatorID","num","Select Originator");
			frmvalidator.addValidation("originatorID","gt=0","Select Originator");
			
			if(sstatusID == 1)
			{
				frmvalidator.addValidation("scompanyID","num","Plz select Sub Depot..");
				frmvalidator.addValidation("scompanyID","gt=0","Plz select Sub Depot..");
			}
		}	
	}
	
	var AuditTrialCheck_HAZARD = function(statusID,acteffID,fupreqID,sstatusID)
	{
		ClearErrorMessages('HIZrefno,rdateID,dateID,timeID,reportBY,jobID,location,description,descriptionACT,hztypeID,fdesigID,empID,fdesigID,rcdateID');		
		ClearErrorMessages('optIDu1,optIDu2,optIDu3,optIDu4,optIDu5,optIDu6TX,optIDu7,descriptionINV,invID,invDate,descriptionACD,actID,actDate');
		ClearErrorMessages('optIDm1,optIDm2,optIDm3,optIDm4,optIDm5,optIDm6TX,optIDm7,acteffID,fupreqID,fupreqDT,fupcmpID,fupcmpDT,fupDesc,hzrconDT,empadvDT');
		
		if(parseInt(statusID) > 0 && parseInt(statusID) == 2)
		{
			var frmvalidator  = new Validator("register");    
			frmvalidator.clearAllValidations();
			frmvalidator.EnableOnPageErrorDisplay();    
			frmvalidator.EnableMsgsTogether();
			
			frmvalidator.addValidation("HIZrefno","req","Enter HZ No ");
			frmvalidator.addValidation("rdateID","req","Enter Report Date"); 
			frmvalidator.addValidation("location","req","Enter Location"); 
			frmvalidator.addValidation("description","req","Enter Description ");			
			frmvalidator.addValidation("rcdateID","req","Enter Reciept Date ");
			frmvalidator.addValidation("dateID","req","Enter Occurance Date ");
			frmvalidator.addValidation("timeID","req","Enter Time ");
			frmvalidator.addValidation("descriptionACT","req","Enter Action Already Taken ");
			frmvalidator.addValidation("descriptionINV","req","Enter Investigation Details ");
			frmvalidator.addValidation("invDate","req","Enter Inv. Date ");
			frmvalidator.addValidation("descriptionACD","req","Enter Action(s) Details ");
			frmvalidator.addValidation("actDate","req","Enter Act. Date ");
			
			frmvalidator.addValidation("reportBY","num","Select Reported By");
			frmvalidator.addValidation("reportBY","gt=0","Select Reported By"); 		
			
			frmvalidator.addValidation("jobID","num","Select Job Title");
			frmvalidator.addValidation("jobID","gt=0","Select Job Title");	
			
			frmvalidator.addValidation("hztypeID","num","Select Hazard Type");
			frmvalidator.addValidation("hztypeID","gt=0","Select Hazard Type");
			
			frmvalidator.addValidation("empID","num","Select Staff Name");
			frmvalidator.addValidation("empID","gt=0","Select Staff Name");
			
			frmvalidator.addValidation("fdesigID","num","Select Designation");
			frmvalidator.addValidation("fdesigID","gt=0","Select Designation");
			
			frmvalidator.addValidation("optIDu1","num","Select Likelihood");
			frmvalidator.addValidation("optIDu1","gt=0","Select Likelihood");
			
			frmvalidator.addValidation("optIDu3","num","Select Exposure");
			frmvalidator.addValidation("optIDu3","gt=0","Select Exposure");

			frmvalidator.addValidation("optIDu4","num","Select Consequence/Impact");
			frmvalidator.addValidation("optIDu4","gt=0","Select Consequence/Impact");
			
			frmvalidator.addValidation("optIDu5","num","Select Secondary Choice");
			frmvalidator.addValidation("optIDu5","gt=0","Select Secondary Choice");
			
			frmvalidator.addValidation("optIDu6TX","req","Enter Risk Category");
			
			frmvalidator.addValidation("optIDm1","num","Select Likelihood");
			frmvalidator.addValidation("optIDm1","gt=0","Select Likelihood");
			
			frmvalidator.addValidation("optIDm3","num","Select Exposure");
			frmvalidator.addValidation("optIDm3","gt=0","Select Exposure");

			frmvalidator.addValidation("optIDm4","num","Select Consequence/Impact");
			frmvalidator.addValidation("optIDm4","gt=0","Select Consequence/Impact");

			frmvalidator.addValidation("optIDm5","num","Select Secondary Choice");
			frmvalidator.addValidation("optIDm5","gt=0","Select Secondary Choice");

			frmvalidator.addValidation("optIDm6TX","req","Enter Risk Category");
			
			frmvalidator.addValidation("invID","num","Select Investigation By");
			frmvalidator.addValidation("invID","gt=0","Select Investigation By");  
			
			frmvalidator.addValidation("actID","num","Select Action By");
			frmvalidator.addValidation("actID","gt=0","Select Action By");
			
			if(parseInt(fupreqID) <= 1)
			{
				frmvalidator.addValidation("fupreqDT","req","Enter Date ");
				frmvalidator.addValidation("fupcmpDT","req","Enter Date ");
				
				frmvalidator.addValidation("fupDesc","req","Enter Follow-up Details ");
				
				frmvalidator.addValidation("fupcmpID","num","Select Follow-up Completed By");
				frmvalidator.addValidation("fupcmpID","gt=0","Select Follow-up Completed By");
			}
			
			frmvalidator.addValidation("fupreqID","num","Select Follow-up Required");
			frmvalidator.addValidation("fupreqID","gt=0","Select Follow-up Required");

			frmvalidator.addValidation("acteffID","num","Select Action Effective");
			frmvalidator.addValidation("acteffID","gt=0","Select Action Effective");
			
			frmvalidator.addValidation("hzrconDT","req","Enter Date ");
			frmvalidator.addValidation("empadvDT","req","Enter Date "); 
			
			if(sstatusID == 1)
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
			
			frmvalidator.addValidation("HIZrefno","req","Enter HZ No ");
			frmvalidator.addValidation("rdateID","req","Enter Report Date"); 
			frmvalidator.addValidation("location","req","Enter Location"); 
			frmvalidator.addValidation("description","req","Enter Description ");
			frmvalidator.addValidation("rcdateID","req","Enter Reciept Date ");
			
			frmvalidator.addValidation("reportBY","num","Select Reported By");
			frmvalidator.addValidation("reportBY","gt=0","Select Reported By"); 		
			
			frmvalidator.addValidation("jobID","num","Select Job Title");
			frmvalidator.addValidation("jobID","gt=0","Select Job Title");	
			
			frmvalidator.addValidation("hztypeID","num","Select Hazard Type");
			frmvalidator.addValidation("hztypeID","gt=0","Select Hazard Type");
			
			frmvalidator.addValidation("empID","num","Select Staff Name");
			frmvalidator.addValidation("empID","gt=0","Select Staff Name");
			
			frmvalidator.addValidation("fdesigID","num","Select Designation");
			frmvalidator.addValidation("fdesigID","gt=0","Select Designation");
			
			if(sstatusID == 1)
			{
				frmvalidator.addValidation("scompanyID","num","Plz select Sub Depot..");
				frmvalidator.addValidation("scompanyID","gt=0","Plz select Sub Depot..");
			}
		}
	}
	
	var ClearErrorMessages = function(arrfield)		
	{
		if(arrfield != '')
		{
			var datafield = arrfield.split(",");
			var arrlength = datafield.length; 
			
			//console.log('Data Field : ' + datafield + ' , Array Length : ' + arrlength);
			
			for(srID = 0; srID <= parseInt(arrlength); srID++)
			{
				$("#register_"+datafield[srID]+"_errorloc").html('');
			}
		}
	}
	
});