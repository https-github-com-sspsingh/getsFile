$(document).ready(function()
{
	$("#desigID").on('change',function()
	{
		var desigID = $(this).val();
		
		if(parseInt(desigID) == 418)
		{
			$("#gfpermitNO,#acpermitNO,#wsdpermitNO,#flpermitNO").val('');
			$("#gfpnexpDT,#flpnexpDT,#acpnexpDT,#wsdpnexpDT").val('');
			
			$(".mch_emp_DIV").show();
		}
		else
		{
			$("#gfpermitNO,#acpermitNO,#wsdpermitNO,#flpermitNO").val('');
			$("#gfpnexpDT,#flpnexpDT,#acpnexpDT,#wsdpnexpDT").val('');
			
			$(".mch_emp_DIV").hide();
		}
	});
	
    $(".autditTRIAL").click(function()
    {
        var reqID = $(this).attr('aria-sort');
        var reqTX = $(this).attr('aria-busy');
        var reqHD = $(this).attr('aria-title');

        if(parseInt(reqID) > 0 && reqTX != '')
        {
            $.ajax({
            url : 'ajax/ajax_audits.php',
            type:'POST',				
            data:{'request':  'AUDIT_' + reqTX , 'ID':reqID},
            dataType:"json",
            success : function(data)
            {
                $('#audits_modal h4').html('<b style="color:#F56954;">Audit Trail - '+reqHD+'</b>');
                $('#audits_modal #modal_data').html(data.file_info);
                $('#audits_modal').modal('show'); 
                $(".datepicker").datetimepicker({format: 'DD/MM/YYYY'}).on('changeDate',function(){ });

                $("#gen_form").submit(function()
                {
                    var formData = $("#gen_form").serialize();
                    var $inputs  = $("#gen_form").find('input,button');
                    $inputs.attr('disabled','disabled');
                    RUN_AJAX_UPDATES_AUDITS(formData,$inputs);
                    return false;
                });
            },
            error: function(res)    {console.log(res);} 	
            });
        }
    });
	
    $("#suburbID").on('change',function()	{$("#pincode").val($("#suburbID option:selected").attr('aria-sort'));});
    
    $("#crleavingID").on('change',function()
    {
        var ID = $(this).val();
        ID = (isNaN(ID) || ID == '' || typeof ID === 'undefined') ? 0 : ID;
        
        if(parseInt(ID) == 1)   
		{
			$("#termOther").prop('readonly',true);			$("#termOther").val('');			
			$("#resonrgID").prop('disabled',false);			$("#resonrgID").prop('selectedIndex',0);			
			$("#terminationID").prop('disabled',true);		$("#terminationID").prop('selectedIndex',0);
		}		
		else if(parseInt(ID) == 2)
		{
			$("#termOther").prop('readonly',true);			$("#termOther").val('');			
			$("#resonrgID").prop('disabled',true);			$("#resonrgID").prop('selectedIndex',0);			
			$("#terminationID").prop('disabled',false);		$("#terminationID").prop('selectedIndex',0);
		} 
        else
		{
			$("#termOther").prop('readonly',true);			$("#termOther").val('');			
			$("#resonrgID,#terminationID").prop('disabled',true);	
			$("#resonrgID,#terminationID").prop('selectedIndex',0);
		}
    });  
	
	$("#terminationID").on('change',function()
    {
        var ID = $(this).val();
        ID = (isNaN(ID) || ID == '' || typeof ID === 'undefined') ? 0 : ID;
        
        if(parseInt(ID) == 481)   
				{$("#termOther").prop('readonly',false);	$("#termOther").val('');} 
        else	{$("#termOther").prop('readonly',true);		$("#termOther").val('');}
    }); 


    $("#acccatID").on('change',function()
    {
        var ID = $(this).val();
        ID = (isNaN(ID) || ID == '' || typeof ID === 'undefined') ? 0 : ID;
        
        if(parseInt(ID) == 18)   
        {
            $("#responsibleID").prop('selectedIndex',2);
        }
        else
        {
            $("#responsibleID").prop('selectedIndex',0);
        }
    });   
	
    $("#respID").on('change',function()
    {
        var respID = $(this).val();
		var factID = $("#furaction").val();
		
        respID = (isNaN(respID) || respID == '' || typeof respID === 'undefined') ? 0 : respID;
        
        if(parseInt(respID) == 46)  
		{
			if(factID != '')
					{/* $("#furaction").val(factID + 'N. R. R.'); */}
			else	{$("#furaction").val('N. R. R.');}
		} 
		else 
		{
			if(factID != '')	{}	else	{$("#furaction").val('');}
		}
    });
	
	$("#engdoneID").on('click',function()
	{
		if($(this).is(':checked'))
		{
			var rprcost = $("#rprcost").val(); 
			var othcost = $("#othcost").val();
			if((parseFloat(rprcost) == 0 || parseFloat(rprcost) > 0) && (parseFloat(othcost) == 0 || parseFloat(othcost) > 0))
			{
				/* DO - Nothing */
			}
			else
			{
				alert('Enter The Bus Repairs & Other Repair (Cost)');
				$("#rprcost").focus();
				$("#engdoneID").prop('checked',false);
			}
		}
	});
	
	$("#oprdoneID").on('click',function()
	{
		if($(this).is(':checked'))
		{
			var invID  = $("#invID").val();
			var discID = $("#cmdiscID").val();
			
			invID  = (isNaN(invID)  || invID == null || invID == ''  || typeof invID === 'undefined')  ? 0 : invID;
			discID = (isNaN(discID) || discID == null || discID == '' || typeof discID === 'undefined') ? 0 : discID;
			
			var alertTX = '';
			alertTX = (parseInt(invID) <= 0 && parseInt(discID) <= 0  ? 'Plz Select Interviewed By & Discipline Related.' :(parseInt(invID) <= 0 ? 'Plz Select Interviewed By.' :(parseInt(discID) <= 0 ? 'Plz Select Discipline Related.' : '')));
			
			if(parseInt(invID) <= 0 || parseInt(discID) <= 0)
			{
				alert(alertTX);
				$("#oprdoneID").prop('checked',false);
			}
		}
	});
	
	$("#brs_statusID").on('click',function()
	{
            if($(this).is(':checked'))
            {
                $("#busID").prop('readonly',false);
				$("#routeID").prop('readonly',false);
				$("#shiftID").prop('readonly',false);
            }
            else
            {
                $("#busID").prop('readonly',true);
				$("#routeID").prop('readonly',true);
				$("#shiftID").prop('readonly',true);
            }
	});

	$(".remove_ppermits").click(function()
	{
		var recID = $(this).attr('aria-sort');
		recID = (isNaN(recID) || recID == '' || typeof recID === 'undefined') ? 0 : recID;
		
		if(parseInt(recID) > 0)
		{
			if(confirm("Are you sure you want to delete this record !. "))
			{
				$.ajax({			
				  url : '../ajax/ajax_delete.php',
				  type:'post',
				  data:{'request': 'PrkPermits_rows' , 'ID':recID},
				  dataType:"json",				  
				  success : function(data)			
				  {	
				  	if(data.Status == 1)
					{
						location.reload();
					}
				  },
				  error: function(res)
				  {
					  console.log('ERROR in Form')
				  }
				  
			  });
			}
		}
		
	});
	
	$(".remove_acpop").click(function()
	{
		var recID = $(this).attr('aria-sort');
		recID = (isNaN(recID) || recID == '' || typeof recID === 'undefined') ? 0 : recID;
		
		if(parseInt(recID) > 0)
		{
			if(confirm("Are you sure you want to delete this record !. "))
			{
				$.ajax({			
				  url : '../ajax/ajax_delete.php',
				  type:'post',
				  data:{'request': 'Accident_rows' , 'ID':recID},
				  dataType:"json",				  
				  success : function(data)			
				  {	
				  	if(data.Status == 1)
					{
						location.reload();
					}
				  },
				  error: function(res)
				  {
					  console.log('ERROR in Form')
				  }
				  
			  });
			}
		}
		
	});
	
	$("#insrypeID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 300 || parseInt(ageID) == 268 || parseInt(ageID) == 261|| parseInt(ageID) == 271 || parseInt(ageID) == 301|| parseInt(ageID) == 377 ||parseInt(ageID) == 381|| parseInt(ageID) == 388 || parseInt(ageID) == 390 || parseInt(ageID) == 396  || parseInt(ageID) == 398 || parseInt(ageID) == 399)
		{
			$("#fineID").prop('disabled',false);   $("#fineID").prop('selectedIndex', 0);
		}
		else
		{
			$("#fineID").prop('disabled',true);   $("#fineID").prop('selectedIndex', 0);
		}
	});
        
	$("#driverID").on('change',function()
	{	
		$("#dcodeID,#first_name,#last_name,#email,#mobileno").empty();
		
		$("#dcodeID").val($("#driverID option:selected").attr('aria-sort'));
		$("#first_name").val($("#driverID option:selected").attr('aria-busy'));
		$("#last_name").val($("#driverID option:selected").attr('aria-setsize'));		
		$("#email").val($("#driverID option:selected").attr('aria-disabled'));
		$("#mobileno").val($("#driverID option:selected").attr('aria-valuenow'));
                
		/* -- generate password -- */ 
		$("#password").val(Math.round(Math.random() * 1000000000,2));
                
	});
	
	$("#offtypeID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		$("#offdtlsID").empty();
		$("#offdtlsID").html('<option value="0" selected="selected"> --- Select --- </option>');
		
		RUN_AJAX_TO_APPEND_DATA(ageID,'GET_OFFENCE_DETAILS','#offdtlsID');
	});

	$("#offtypeID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		
		if(parseInt(ageID) == 144)
		{
			$("#grfitemID").prop('disabled',false);	   $("#grfcolour").prop('readonly',false);	$("#whbwdescID").prop('readonly',false);
			$("#grfitemID").prop('selectedIndex', 0);	 $("#grfcolour").val('');				   $("#whbwdescID").val('');	
		}
		else
		{
			$("#grfitemID").prop('disabled',true);	   $("#grfcolour").prop('readonly',true);	  $("#whbwdescID").prop('readonly',true);
			$("#grfitemID").prop('selectedIndex', 0);	  $("#grfcolour").val('');				  $("#whbwdescID").val('');	
		}
	});

	$("#contractorID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		$("#servicenoID,#contractID").empty();
		$("#serviceinfID").val('');
		$("#servicenoID,#contractID").html('<option value="0" selected="selected"> --- Select --- </option>');
		
		RUN_AJAX_TO_APPEND_DATA(ageID,'GET_Contracts_Agst_Contractor','#contractID');
	});
        
	$("#contractID").on('change',function()
	{
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		$("#servicenoID").empty();
		$("#serviceinfID").val('');
		$("#servicenoID").html('<option value="0" selected="selected"> --- Select --- </option>');
		
		RUN_AJAX_TO_APPEND_DATA(ageID,'GET_Serviceno_Agst_ContractID','#servicenoID');
	});
	
	$("#servicenoID").on('change',function()
	{
		$("#serviceinfID").empty();
		$("#serviceinfID").val($("#servicenoID option:selected").attr('aria-sort'));
		
		var ageID = $(this).val();
		ageID = (isNaN(ageID) || ageID == '' || typeof ageID === 'undefined') ? 0 : ageID;
		var ageID_1 = $("#contractID").val();
		 
		if(parseInt(ageID) > 0)
		{
			$.ajax({			
					url : '../ajax/ajax-n.php',
					type:'post',
					data:{'request': 'GET_ServicenoTimePoints','reqID':ageID,'reqID_1':ageID_1},
					dataType:"json",
					success:function(data)
					{
						$("#srtpointID").empty();
						$("#srtpointID").append(data.result);
					},
					error:function()
					{				
					  alert('ERROR');
					}
			  });	
		}
	});
	
	
	$("#routenoID").on('change',function()
	{ 
		var serviceID = $(this).val();
		serviceID = (isNaN(serviceID) || serviceID == '' || typeof serviceID === 'undefined') ? 0 : serviceID;
		 
		if(parseInt(serviceID) > 0)
		{
			$("#routeInfo").val('');
			$("#routeInfo").val($('#routenoID option:selected').attr('aria-sort'));	

			$.ajax({			
                url : '../ajax/ajax-n.php',
                type:'POST',
                dataType:"json",
                data: {'request' : 'GET_StopsPoints' , 'serviceID':serviceID} ,
                success : function(data)
                {	
					$("#stopID").html(data.result);
                },
				error: function(res){console.log('ERROR in Code !...')}
			});			
		}
	});
	
	$(".srvgridID").click(function()
	{ 
		var HTML  = '';
		
		HTML  += '<tr>';
			HTML  += '<td align="center"><span style="cursor:pointer;" class="fa fa-trash-o DLBTN"></span></td>';		 
			HTML  += '<td><input type="text" class="form-control" name="fieldID_2[]" placeholder="Timing Point Name"></td>';			  
		HTML += '</tr>';
		
		
		$('#dataTablesID tr:last').after(HTML);
		
		$(".DLBTN").click(function()
		{
			var $this = $(this);					  
			$this.closest('tr').hide().fadeOut(2000).remove();
		}); 
	 
	});
	
	
	
	$("#fupreqID").on('change',function()
	{
		var fupreqID = $("#fupreqID").val();		
		fupreqID   = (isNaN(fupreqID)  || fupreqID == ''  || typeof fupreqID   === 'undefined') ? 0 : parseInt(fupreqID);
		
		if(fupreqID == 1)
		{
			$("#fupDesc").val('');			
			$("#fupDesc").prop('readonly',false);
		}
		else
		{
			$("#fupDesc").val('');
			$("#fupDesc").prop('readonly',true);
		}		
	});
	
	$("#optIDu1,#optIDu3,#optIDu4,#optIDu5").on('change',function()
	{
		calcy_determine_rslevel(($("#optIDu1").val()),($("#optIDu3").val()),($("#optIDu5").val()),'optIDu7','optIDu6','optIDu6TX');
	});
	
	$("#optIDm1,#optIDm3,#optIDm4,#optIDm5").on('change',function()
	{
		calcy_determine_rslevel(($("#optIDm1").val()),($("#optIDm3").val()),($("#optIDm5").val()),'optIDm7','optIDm6','optIDm6TX');
	});
	
	$("#optIDu4").on('change',function()	{SecondaryChoiceHTML(($(this).val()),'optIDu5');});
	$("#optIDm4").on('change',function()	{SecondaryChoiceHTML(($(this).val()),'optIDm5');});
	
    $(".RESET_RESPONSE").on('click',function()   
    {
        $(".loader").fadeIn(200).show(200);
        $("#gridID_1").empty(); 
		$("select#FLT_apiID").prop('selectedIndex', 0);
		$("#FLT_fdateID,#FLT_tdateID").val('');
		
		$(".loader").fadeOut(1000).hide(1000);		
    });
	
	$(".PRINT_RESPONSE").on('click',function()
	{
		var data = $("#dataTable").html();
		var apiID = $("#FLT_apiID").val();
		
		var content   =	(data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
		var mywindow  =	'';
		mywindow = window.open('', 'View Report', 'height=800,width=1250');
		mywindow.document.write('<html><head><title>'+(apiID == 1 ? 'Incident' :(apiID == 2 ? 'Offence' : ''))+' API Response Report</title>');
		mywindow.document.write('</head>');
		mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b>'+(apiID == 1 ? 'Incident' :(apiID == 2 ? 'Offence' : ''))+' API Response Report</b></th></tr></br></br>');
		mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');	
		mywindow.document.write('</body></html>');
		mywindow.print();
		mywindow.close();
		return true;
	
	});
	
	$(".EXPORT_RESPONSE").on('click',function()	{window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTable').outerHTML));});
	
	$(".GET_API_RESPONSE").on('click',function()
	{
		var FLT_companyID = $("#FLT_companyID").val();
        var FLT_fdateID   = $("#FLT_fdateID").val();
        var FLT_tdateID   = $("#FLT_tdateID").val();
        var FLT_apiID 	  = $("#FLT_apiID").val();
		
        FLT_apiID  = (isNaN(FLT_apiID)  || FLT_apiID == ''  || typeof FLT_apiID  === 'undefined') ? 0 : parseInt(FLT_apiID);
		
        $(".loader").fadeIn(200).show(200);
            
        if(parseInt(FLT_apiID) > 0 && FLT_companyID != '')
        {
            $.ajax({
                url : 'ajax/ajax_request.php',
                type:'POST',				
                data:{'request': 'API_RESPONSE', 'companyID': FLT_companyID, 'fdateID': FLT_fdateID, 'tdateID': FLT_tdateID, 'apiID': FLT_apiID },
                dataType:"json",
                success : function(data)    {$("#gridID_1").empty();     $("#gridID_1").html(data.records);},
                error: function(res)    {console.log(res);} 	
            }); 
       }
       else {alert('Plz select Parameters..');}
       
       $(".loader").fadeOut(1000).hide(1000);
	});
	
	$("#accID,#substanID,#faultID").on('change',function()
	{
		var accID  = $("#accID").val();
		var substanID = $("#substanID").val();
		var faultID   = $("#faultID").val();

		accID  = (isNaN(accID)  || accID == '' || typeof accID === 'undefined')     ? 0 : parseInt(accID);
		substanID = (isNaN(substanID) || substanID == '' || typeof substanID === 'undefined') ? 0   : parseInt(substanID);
		faultID   = (isNaN(faultID)  || faultID == ''   || typeof faultID === 'undefined')   ? 0 : parseInt(faultID);

		if(accID == 52 && substanID == 2 && faultID == 5)
		{ 
			$.ajax({
			url : '../ajax/ajax_request.php',
			type:'POST', 
			data:{'request': 'GET_INTERVIEWEDBY' },
			dataType:"json",
			success : function(data)    {$("#invID").empty();     $("#invID").html(data.records);},
			error: function(res)    {console.log(res);} 
			});
		}  
	});

	$("#invID").on('change',function()
	{
		var invID  = $("#invID").val();
		
		if(invID == 1001)
				{$("#invdate").prop('readonly',true);}
		else	{$("#invdate").prop('readonly',false);}
	});
	
    $(".SIGNONcaseID").on('click',function()
    {
        var casesID = $(this).attr('aria-sort');
        if(parseInt(casesID) > 0)
        {
            $("#SIGNON_form").submit();
        }
    });
    
    $(".RESET_REQUEST").on('click',function()   
    {
        $(".loader").fadeIn(200).show(200);
        $("#gridID_1,#gridID_2").empty(); 
		$("#api_labelID").text('Data Sender - API request');		
		$(".loader").fadeOut(1000).hide(1000);		
    });
    
	$(".PRINT_REQUEST").on('click',function()
	{
		var data = $("#dataTable").html();
		var apiID = $("#api_typeID").val();
		
		var content   =	(data.replace(/class="sorting_asc"/g,'')).replace(/class="sorting"/g,'');
		var mywindow  =	'';
		mywindow = window.open('', 'View Report', 'height=800,width=1250');
		mywindow.document.write('<html><head><title>'+(apiID == 1 ? 'Incident' :(apiID == 2 ? 'Offence' : ''))+' API Report</title>');
		mywindow.document.write('</head>');
		mywindow.document.write('<body><tr><th colspan="13" style="border:none 0px;"><b>'+(apiID == 1 ? 'Incident' :(apiID == 2 ? 'Offence' : ''))+' API Report</b></th></tr></br></br>');
		mywindow.document.write('<table align="center" style="border-collapse:collapse !important;" cellpadding="0" border="1" cellspacing="1" width="95%">' + data + '</table>');	
		mywindow.document.write('</body></html>');
		mywindow.print();
		mywindow.close();
		return true;
	
	});
	
	$(".EXPORT_REQUEST").on('click',function()	{window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTable').outerHTML));});
	
    $(".GET_API_REQUEST").on('click',function()
    {
        var companyFL = $("#companyFL").val();
        var fdateID = $("#fdateID").val();
        var tdateID = $("#tdateID").val();
        var api_typeID = $("#api_typeID").val();
        api_typeID = (isNaN(api_typeID) || api_typeID == '' || typeof api_typeID === 'undefined') ? 0 : parseInt(api_typeID);
        
		if(api_typeID == 1)
				{$("#api_labelID").text('Data Sender - Incident API request');}
		else if(api_typeID == 2)
				{$("#api_labelID").text('Data Sender - Offence API request');}
		else	{$("#api_labelID").text('Data Sender - API request');}
		
        $(".loader").fadeIn(200).show(200);
            
        if(parseInt(api_typeID) > 0 && companyFL != '')
        {
            $.ajax({
                url : 'ajax/ajax_request.php',
                type:'POST',				
                data:{'request': 'API_REQUEST', 'fdateID': fdateID, 'tdateID': tdateID, 'typeID': api_typeID, 'companyFL': companyFL, 'statusID': '0'},
                dataType:"json",
                success : function(data)    {$("#gridID_1").empty();     $("#gridID_1").html(data.records);},
                error: function(res)    {console.log(res);} 	
            }); 
       }
       else {alert('Plz select API request type..');$("#api_typeID").focus();}
       
       $(".loader").fadeOut(1000).hide(1000);
    });


	$("#tickID_1").on('click',function()
	{
		if($(this).is(':checked'))  
		{
			$("#empID").prop('disabled',true);
			$("select#responsibleID").prop('selectedIndex', 2);
		}
		else
		{
			$("#empID").prop('disabled',false);
			$("select#responsibleID").prop('selectedIndex', 0);
		}
	});
	
	$(".DTaccpopID").click(function()
	{
		var HTML = '';

		HTML  += '<tr>';
			HTML  += '<td align="center"><span style="cursor:pointer;" class="fa fa-trash-o DLBTN"></span></td>';
			HTML  += '<td><input type="text" class="form-control datepicker" name="fieldID_1[]" style="text-align:center;" required="required" placeholder="Date"></td>';
			HTML  += '<td width="890"><input type="text" class="form-control" name="fieldID_2[]" required="required" placeholder="Accidents Detail/Remarks"></td>';
		HTML += '</tr>';

		$('#dataTablesAC tr:last').after(HTML);
		$(function()    {$('.datepicker').datepick();});
	});
	
	
	var RUN_AJAX_TO_APPEND_DATA = function(reqID,request,fieldID)
	{
		if(parseInt(reqID) > 0)
		{
			$.ajax({			
					url : '../ajax/ajax-n.php',
					type:'post',
					data:{'request': request,'reqID':reqID},
					dataType:"json",
					success:function(data)
					{
						$(fieldID).empty();
						$(fieldID).append(data.result);
					},
					error:function()
					{				
					  alert('ERROR');
					}
			  });	
		}
		else	{$(fieldID).empty();}
	}
	
	var calcy_determine_rslevel = function(likehoodID,exposureID,sechoiceID,scoreID,rscategoryID,rscategoryTX)
	{
		likehoodID   = (isNaN(likehoodID)  || likehoodID == ''  || typeof likehoodID   === 'undefined') ? 0 : parseInt(likehoodID);
		exposureID   = (isNaN(exposureID)  || exposureID == ''  || typeof exposureID   === 'undefined') ? 0 : parseInt(exposureID);
		sechoiceID  = (isNaN(sechoiceID) || sechoiceID == '' || typeof sechoiceID  === 'undefined') ? 0 : parseInt(sechoiceID);
		
		var resultID = 0;
		resultID = parseInt(likehoodID) * parseInt(exposureID) * parseInt(sechoiceID);
		resultID  = (isNaN(resultID)  || resultID == ''  || typeof resultID  === 'undefined') ? 0 : parseInt(resultID);
		
		$("#"+scoreID).val('');
		
		if(resultID > 400)
		{
			$("#"+rscategoryID).val(1);
			$("#"+rscategoryTX).val('VERY HIGH');
		}
		else if(resultID >= 201 && resultID <= 400)
		{
			$("#"+rscategoryID).val(2);
			$("#"+rscategoryTX).val('HIGH');
		}
		else if(resultID >= 71 && resultID <= 200)
		{
			$("#"+rscategoryID).val(3);
			$("#"+rscategoryTX).val('MEDIUM');
		}
		else if(resultID >= 20 && resultID <= 70)
		{
			$("#"+rscategoryID).val(4);
			$("#"+rscategoryTX).val('LOW');
		}
		else if(resultID < 20)
		{
			$("#"+rscategoryID).val(5);
			$("#"+rscategoryTX).val('VERY LOW');
		}
		else
		{
			$("#"+rscategoryID).val(0);
			$("#"+rscategoryTX).val('');
		}
		
		$("#"+scoreID).val(resultID);
	}
	
	var SecondaryChoiceHTML = function(consequenceID,secondaryID)
	{ 
		var HTML = '';
		
		if(parseInt(consequenceID) == 1)
		{
			HTML += '<option value="0" selected="selected"> --- Select --- </option>';
			HTML += '<option value="10">Fatality or Permanent Disability</option>';
			HTML += '<option value="6">Serious Injury/Loss Time Injury or Disease</option>';
			HTML += '<option value="3">Medical Treated Injury or Disease</option>';
			HTML += '<option value="1">First Aid Treatment (on site) or Work Injury or Disease Report</option>';
		}
		else if(parseInt(consequenceID) == 2)
		{
			HTML += '<option value="0" selected="selected"> --- Select --- </option>';
			HTML += '<option value="10">Serious Environmental Harm</option>';
			HTML += '<option value="6">Moderate Environmental Impact</option>';
			HTML += '<option value="3">Minimal Environmental Harm</option>';
			HTML += '<option value="1">No Environmental Harm</option>';
		}
		else 
		{
			HTML += '<option value="0" selected="selected"> --- Select --- </option>';
		}
		
		$("#"+secondaryID).html('');
		$("#"+secondaryID).html(HTML);
	}
	
	var RUN_AJAX_UPDATES_AUDITS = function (formData,$inputs)
    {
        $.ajax({			
                url : 'ajax/ajax_DBaudits.php',
                type:'POST',
                dataType:"json",
                data: formData ,
                success : function(data)
                {	
                    if(data.success == true)
                    {
                        alert('Audit Trial request is updated successfully. !!!');
                        $("#audits_modal").hide();
                        window.location.reload();
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
		
});