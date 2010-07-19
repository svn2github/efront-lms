
function showGraph(el, dataType, entity) {
 parameters = {ajax:dataType, entity: entity, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onShowGraph);
}
function onShowGraph(el, response) {
 var obj = response.evalJSON(true);

 switch (obj.type) {
  case 'horizontal_bar': showHorizontalBarGraph(el, obj); break;
  case 'bar': showBarGraph(el, obj); break;
  case 'pie': showPieGraph(el, obj); break;
  case 'line': default: showLineGraph(el, obj); break;
 }
}

function showHorizontalBarGraph(el, obj) {
 try {

  options = {"title": obj.title,
       "xaxis": {"showLabels": true, "ticks":obj.yLabels, "title":obj.yTitle},
       "yaxis": {"showLabels": true, "ticks":obj.xLabels, "title":obj.xTitle},
       "bars": {"show": true, "horizontal": true, "barWidth":0.2},
       "mouse": {"track":true, "position": "ne"}
       };

  series = [{label: obj.label,
       color: "#00A8F0",
       data: obj.data,
       xaxis: 1,
       yaxis: 1}
     ];
  el.setStyle({height:Math.max(500, obj.xLabels.length*30)+'px'});

  Flotr.draw(el, series, options);
 } catch (e) {
  alert(e);
 }
}
function showBarGraph(el, obj) {
 try {

  options = {"title": obj.title,
       "xaxis": {"showLabels": true, "ticks":obj.xLabels, "title":obj.xTitle},
       "yaxis": {"showLabels": true, "title":obj.yTitle},
       "bars": {"show": true, "horizontal": false},
       "mouse": {"track":true, "position": "ne"}
       };

  series = [{label: obj.label,
       color: "#00A8F0",
       data: obj.data,
       xaxis: 1,
       yaxis: 1}
     ];
  el.setStyle({height:Math.max(500, obj.xLabels.length*30)+'px'});

  Flotr.draw(el, series, options);
 } catch (e) {
  alert(e);
 }
}
function showLineGraph(el, obj) {
 try {
  options = {"title": obj.title,
       "HtmlText": false,
       "xaxis": {"showLabels": true, "ticks":obj.xLabels, "title":obj.xTitle, "labelsAngle":45},
       "yaxis": {"showLabels": true, "title":obj.yTitle},
       "lines": {"show": true},
       "mouse": {"track":true, "position": "ne"}
       };

  series = [{label: obj.label,
       color: "#00A8F0",
       data: obj.data,
       xaxis: 1,
       yaxis: 1}
     ];

  el.setStyle({height:Math.max(500, obj.xLabels.length*30)+'px'});

  Flotr.draw(el, series, options);
 } catch (e) {
  alert(e);
 }

}
function showPieGraph(el, obj) {
 try {
  options = {"title": obj.title,
       "xaxis": {"showLabels": false},
       "yaxis": {"showLabels": false},
          "grid": {"verticalLines": false, "horizontalLines": false},
       "pie": {"show": true}
       };

  series = [];
  for (var i = 0; i < obj.data.length; i++) {
   series.push({label: obj.labels[i],
     data: obj.data[i],
       xaxis: 1,
       yaxis: 1}
     );
  }
  el.setStyle({height:Math.max(500, obj.xLabels.length*30)+'px'});

  Flotr.draw(el, series, options);
 } catch (e) {
  alert(e);
 }


}
/*
if (1) {
	showGraph($('proto_chart'), 'graph_test_analysis');
}
*/
