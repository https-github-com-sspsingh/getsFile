$(function()
{ 
	var pageID = document.location.pathname.match(/[^\/]+$/)[0];
	
	if(pageID == 'dashboard.php')
	{	
		$("#Remove_Dash_Search").click(function()   {/*$("#dashCD").val('');*/   $("#divResult").hide();});
			
		/* START - DASHBOARD - SEARCHING - CRITERIAS */
		$("#dashCD").on('keyup',function()
		{
			var inputSearch = $(this).val();        
			if(inputSearch != '')
			{
				$.ajax({
					type: "POST",
					url: "ajax/ajax_search.php",
					data: {'ID':inputSearch,'request':'inputSearch','fdateID' : $("#fdateID").val(),'tdateID' : $("#tdateID").val()},
					cache: false,
					success: function(html) {$("#divResult").html(html).show();}
				});
			}   else   {}
		});
		/* ENDSS - DASHBOARD - SEARCHING - CRITERIAS */
		
		
	  var passedID = 0;
	  passedID = $("#passedID").val();
	  
	  if(parseInt(passedID) == 786)  
	  {
		var dateID = '';
		dateID = $("#dateID").val();
		var statusID = 0;
		statusID = $("#getsID").val();

		var yrID = 0;
		yrID = $("#pyrID").attr('data-rel');
		yrID = (isNaN(yrID) || yrID == '' || typeof yrID === 'undefined') ? 0 : yrID;

		var arr_1 = [];	var arr_2 = [];	var arr_3 = [];	var arr_4 = [];	var arr_5 = [];	var arr_6 = [];
		
		var request = parseInt(statusID) == 786 ? 'GET_DASHBOARDS' : 'GET_WEEKLY_ROSTER';
		 
		/*$.ajax({
				url : 'ajax/ajax_dash.php',
				type: 'POST',
				data: {'dateID':dateID ,'yrID':yrID , 'request':request},
				dataType:"json",
				success:function(data)
				{	
						if(request == 'GET_DASHBOARDS')
						{
								var optionID_1 = data.optionID_1;
								var optionID_2 = data.optionID_2;
								var optionID_3 = data.optionID_3;
								var optionID_4 = data.optionID_4;
								var optionID_5 = data.optionID_5;
							
								for(var i= 0; i < optionID_1.length; i++)
								{
										var ar	=	{};
										ar['titleID'] = optionID_1[i]['titleID']	
										ar['countID'] = optionID_1[i]['countID']
										arr_1.push(ar);
								}
								
								for(var i= 0; i < optionID_5.length; i++)
								{
										var ar	=	{};
										ar['titleID'] = optionID_5[i]['titleID']	
										ar['countID'] = optionID_5[i]['countID']
										arr_6.push(ar);
								}
	
	                            for(var i= 0; i < optionID_2.length; i++)
	                            {
	                                    var ar	=	{};
	                                    ar['titleID'] = optionID_2[i]['titleID']	
	                                    ar['countID'] = optionID_2[i]['countID']
	                                    arr_2.push(ar);
	                            }
	
	                            for(var i= 0; i < optionID_3.length; i++)
	                            {
	                                    var ar = {};
	                                    ar['date']  = optionID_3[i]['dateID']	
	                                    ar['Compliment']   = optionID_3[i]['Compliment']
	                                    ar['Complaint']    = optionID_3[i]['Complaint']	
	                                    ar['Accident']     = optionID_3[i]['Accident']
	                                    ar['Incident']     = optionID_3[i]['Incident']
	                                    ar['Infringement'] = optionID_3[i]['Infringement']
	                                    ar['Inspection']   = optionID_3[i]['Inspection']					
	                                    arr_3.push(ar);
	                                    arr_4.push(ar);
	                            }
								
	                            for(var i= 0; i < optionID_4.length; i++)
	                            {
	                                    var ar = {};
										ar['frmID']  = optionID_4[i]['frmID']
	                                    ar['country']  = optionID_4[i]['country']	
	                                    ar['A']   = optionID_4[i]['A']
	                                    ar['E']    = optionID_4[i]['E']	
	                                    ar['D']     = optionID_4[i]['D']
	                                    arr_5.push(ar);
	                            }

								fill_chartID_1(arr_1);
	                            fill_chartID_2(arr_2);
	                            fill_chartID_3(arr_3);                                
	                            fill_chartID_4(arr_4);
	                            fill_chartID_5(arr_5);
								fill_chartID_6(arr_6);
						}


	
	                    if(request == 'GET_WEEKLY_ROSTER')
	                    {
	                            $("#wrosterID").html(data.wrosterID);
	                    }
	
	                    $('#pendingTable').dataTable({"bPaginate": false,
	                            "bLengthChange": false,
	                            "bFilter": false,
	                            "bSort": false,
	                            "bInfo": false,
	                            "bAutoWidth": true,
	                            "iDisplayLength": 5,
	                            "pagingType": 'simple'});	 
				}, 			
		}); */

		/*function fill_chartID_1(data)
		{
			var chart = AmCharts.makeChart( "VS_Service", 
			{
			  "type": "pie",
			  "theme": "light",
			  "dataProvider": data,
			  "valueField": "countID",
			  "titleField": "titleID",
			  "outlineAlpha": 2,
			  "depth3D": 10,
			  "balloonText": "<b>[[titleID]]</b><br><span style='font-size:14px'><b>[[countID]]</b></span>",
			  "angle": 10,
			  "export": {
					"enabled": false
			  }
			});
		}
		
		function fill_chartID_6(data)
		{
			var chart = AmCharts.makeChart( "VS_SignOnStatus", 
			{
			  "type": "pie",
			  "theme": "light",
			  "dataProvider": data,
			  "valueField": "countID",
			  "titleField": "titleID",
			  "outlineAlpha": 2,
			  "depth3D": 10,
			  "balloonText": "<b>[[titleID]]</b><br><span style='font-size:14px'><b>[[countID]]</b></span>",
			  "angle": 10,
			  "export": {
					"enabled": false
			  }
			} );
		}
	
	    function fill_chartID_2(data)
	    {
	            var chart = AmCharts.makeChart("VS_Order", 
	            {
	              "type": "pie",
	              "startDuration": 0,
	               "theme": "light",
	              "addClassNames": true,
	              "legend":{
	                    "position":"right",
	                    "marginRight":40,
	                    "autoMargins":false
	              },
	              "innerRadius": "30%",
	              "defs": {
	                    "filter": [{
	                      "id": "shadow",
	                      "width": "400%",
	                      "height": "300%",
	                      "feOffset": {
	                            "result": "offOut",
	                            "in": "SourceAlpha",
	                            "dx": 0,
	                            "dy": 0
	                      },
	                      "feGaussianBlur": {
	                            "result": "blurOut",
	                            "in": "offOut",
	                            "stdDeviation": 5
	                      },
	                      "feBlend": {
	                            "in": "SourceGraphic",
	                            "in2": "blurOut",
	                            "mode": "normal"
	                      }
	                    }]
	              },
	              "dataProvider": data,
	              "valueField": "countID",
	              "titleField": "titleID",
	              "export": {
	                    "enabled": false
	              }
	            });
	
	            chart.addListener("init", handleInit);
	            chart.addListener("rollOverSlice", function(e) {
	              handleRollOver(e);
	            });
	
	            function handleInit(){
	              chart.legend.addListener("rollOverItem", handleRollOver);
	            }
	
	            function handleRollOver(e){
	              var wedge = e.dataItem.wedge.node;
	              wedge.parentNode.appendChild(wedge);
	            }
	    }
	
	    function fill_chartID_3(data)
	    {  
	            var chart = AmCharts.makeChart("VS_TC", {
	              "type": "serial",
	              "theme": "light",
	              "dataDateFormat": "YYYY-MM-DD",
	              "precision": 2,
	              "valueAxes": [{
	                    "id": "v1",
	                    "title": "Transactional - Counting",
	                    "position": "left",
	                    "autoGridCount": false,
	                    "labelFunction": function(value) {
	                      return value;
	                    }
	              }],
	              "graphs": [{
	                    "id": "g1",
	                    "valueAxis": "v1",
	                    "lineColor": "#939393",
	                    "fillColors": "#0073B7",
	                    "fillAlphas": 1,
	                    "type": "column",
	                    "title": "Compliment",
	                    "valueField": "Compliment",
	                    "clustered": false,
	                    "columnWidth": 1.1,
	                    "legendValueText": "[[Compliment]]",
	                    "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[Compliment]]</b>"
	              },{
	                    "id": "g3",
	                    "valueAxis": "v1",
	                    "lineColor": "#939393",
	                    "fillColors": "#00C0EF",
	                    "fillAlphas": 1,
	                    "type": "column",
	                    "title": "Complaint",
	                    "valueField": "Complaint",
	                    "clustered": false,
	                    "columnWidth": 1.1,
	                    "legendValueText": "[[Complaint]]",
	                    "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[Complaint]]</b>"
	              }, {
	                    "id": "g4",
	                    "valueAxis": "v1",
	                    "lineColor": "#939393",
	                    "fillColors": "#00A65A",
	                    "fillAlphas": 1,
	                    "type": "column",
	                    "title": "Accident",
	                    "valueField": "Accident",
	                    "clustered": false,
	                    "columnWidth": 1.1,
	                    "legendValueText": "[[Accident]]",
	                    "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[Accident]]</b>"
	              },   {
	                    "id": "g2",
	                    "valueAxis": "v1",
	                    "lineColor": "#939393",
	                    "fillColors": "#D05947",
	                    "fillAlphas": 1,
	                    "type": "column",
	                    "title": "Incident",
	                    "valueField": "Incident",
	                    "clustered": false,
	                    "columnWidth": 0.6,
	                    "legendValueText": "[[Incident]]",
	                    "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[Incident]]</b>"
	              }, {
	                    "id": "g5",
	                    "valueAxis": "v1",
	                    "lineColor": "#939393",
	                    "fillColors": "#333333",
	                    "fillAlphas": 1,
	                    "type": "column",
	                    "title": "Infringement",
	                    "valueField": "Infringement",
	                    "clustered": false,
	                    "columnWidth": 1.1,
	                    "legendValueText": "[[Infringement]]",
	                    "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[Infringement]]</b>"
	              }],
	              "chartScrollbar": {
	                    "graph": "g1",
	                    "oppositeAxis": false,
	                    "offset": 30,
	                    "scrollbarHeight": 50,
	                    "backgroundAlpha": 0,
	                    "selectedBackgroundAlpha": 0.1,
	                    "selectedBackgroundColor": "#E6E6E6",
	                    "graphFillAlpha": 0,
	                    "graphLineAlpha": 0.5,
	                    "selectedGraphFillAlpha": 0,
	                    "selectedGraphLineAlpha": 1,
	                    "autoGridCount": true,
	                    "color": "black"
	              },
	              "chartCursor": {
	                    "pan": true,
	                    "valueLineEnabled": true,
	                    "valueLineBalloonEnabled": true,
	                    "cursorAlpha": 0,
	                    "valueLineAlpha": 0.2
	              },
	              "categoryField": "date",
	              "categoryAxis": {
	                    "parseDates": true,
	                    "dashLength": 1,
	                    "minorGridEnabled": true
	              },
	              "legend": {
	                    "useGraphSettings": true,
	                    "position": "top"
	              },
	              "balloon": {
	                    "borderThickness": 1.2,
	                    "shadowAlpha": 0
	              },
	              "export": {"enabled": false},
	              "dataProvider": data
	            });
	    }  
	
	    function fill_chartID_4(chartData)
	    {
	        var chart = AmCharts.makeChart("LG_Graph", {
	            "type": "serial",
	            "theme": "dark",
	            "legend": 
	            {
	                "useGraphSettings": true
	            },
	            "dataProvider": chartData,
	            "synchronizeGrid":true,
	            "valueAxes": [{
	                "id":"v1",
	                "axisColor": "#FF6600",
	                "axisThickness": 2,
	                "axisAlpha": 1,
	                "position": "left"
	            }, {
	                "id":"v1",
	                "axisColor": "#FF6600",
	                "axisThickness": 2,
	                "axisAlpha": 1,
	                "position": "right"
	            }],
	            "graphs": [{
	                "valueAxis": "v1",
	                "lineColor": "#FF6600",
	                "bullet": "round",
	                "bulletBorderThickness": 1,
	                "hideBulletsCount": 30,
	                "title": "Compliment",
	                "valueField": "Compliment",
	                "fillAlphas": 0
	            }, {
	                "valueAxis": "v2",
	                "lineColor": "#FCD202",
	                "bullet": "round",
	                "bulletBorderThickness": 1,
	                "hideBulletsCount": 30,
	                "title": "Complaint",
	                "valueField": "Complaint",
	                "fillAlphas": 0
	            }, {
	                "valueAxis": "v3",
	                "lineColor": "#B0DE09",
	                "bullet": "round",
	                "bulletBorderThickness": 1,
	                "hideBulletsCount": 30,
	                "title": "Accident",
	                "valueField": "Accident",
	                "fillAlphas": 0
	            }, {
	                "valueAxis": "v4",
	                "lineColor": "#4796C5",
	                "bullet": "round",
	                "bulletBorderThickness": 1,
	                "hideBulletsCount": 30,
	                "title": "Incident",
	                "valueField": "Incident",
	                "fillAlphas": 0
	            }, {
	                "valueAxis": "v5",
	                "lineColor": "#FF379B",
	                "bullet": "round",
	                "bulletBorderThickness": 1,
	                "hideBulletsCount": 30,
	                "title": "Infringement",
	                "valueField": "Infringement",
	                "fillAlphas": 0
	            }],
	            "chartScrollbar": {},
	            "chartCursor": {
	                "cursorPosition": "mouse"
	            },
	            "categoryField": "date",
	            "categoryAxis": {
	                "parseDates": true,
	                "axisColor": "#DADADA",
	                "minorGridEnabled": true
	            }
	        });
	
	        chart.addListener("dataUpdated", zoomChart);
	        zoomChart();
	
	        function zoomChart()    {chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);}
	    }
		
		
		function fill_chartID_5(chartDatas)
		{
			alert(JSON.stringify(chartDatas));
			
			var chart = AmCharts.makeChart("TR_Logs", {
				"theme": "dark",
				"type": "serial",
				"dataProvider": chartDatas,
				"valueAxes": [{
					"unit": "",
					"position": "left",
					"title": "",
				}],
				"startDuration": 1,
				"graphs": [{
					"balloonText": "ADD - NEW In [[frmID]] : <b>[[value]]</b>",
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "ADD",
					"type": "column",
					"valueField": "A"
				},{
					"balloonText": "EDIT In [[frmID]] : <b>[[value]]</b>",
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "EDIT",
					"type": "column",
					"valueField": "E"
				},  {
					"balloonText": "DELETE In [[frmID]] : <b>[[value]]</b>",
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "DELETE",
					"type": "column",
					"clustered":false,
					"columnWidth":0.5,
					"valueField": "D"
				}],
				"plotAreaFillAlphas": 0.1,
				"categoryField": "country",
				"categoryAxis": {
					"gridPosition": "start"
				},
				"export": {
					"enabled": false
				 }
			
			});
		}*/
		
	  }	
	}
	
	if(pageID == 'profile_2.php')
	{
		$.ajax({		
            url : 'ajax/ajax_profile.php',
            type: 'POST',
            data: {'request':'GET_PROFILE','memberID' : $("#memberID").val(),'fdateID' : $("#fdateID").val(),'tdateID' : $("#tdateID").val()},
            dataType:"json",
            success:function(data)
            { 
				$("#sick_leaveID").html(data.optionID_1);
				$("#comlaintsID").html(data.optionID_2);
				$("#accidentsID").html(data.optionID_3);
				$("#incidentsID_ER").html(data.optionID_4);
				$("#incidentsID_LF").html(data.optionID_5);

				$('#pendingTable').dataTable({"bPaginate": false,
						"bLengthChange": false,
						"bFilter": false,
						"bSort": false,
						"bInfo": false,
						"bAutoWidth": true,
						"iDisplayLength": 5,
						"pagingType": 'simple'});	 
            }, 			
		});  
	}
	
	$("#filterID").on('change',function()
	{
		var ID = $(this).val();		  
		if(parseInt(ID) == 1)		{$("#profile_ecodeID").show();}
		else						{$("#profile_ecodeID").hide();}
	});
});