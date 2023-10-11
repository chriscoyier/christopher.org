jQuery(document).ready(function($) {
  $('#sdate').datepicker({ dateFormat: 'yy-mm-dd', defaultDate: -30, minDate: PrliReport.min_date, maxDate: 0 });
  $('#edate').datepicker({ dateFormat: 'yy-mm-dd', minDate: PrliReport.min_date, maxDate: 0 });
  $('.filter_pane').hide();
  $('.filter_toggle').on('click', function () {
    $('.filter_pane').slideToggle();
  });
});

var drawPrliChart = function() {
  //Hits Chart
  var hitsChartData = new google.visualization.DataTable(PrliReport.chart);
  var hitsChart = new google.visualization.AreaChart(document.getElementById('my_chart'));
  hitsChart.draw(hitsChartData, {height: '300', title: PrliReport.titles});
};

google.charts.load('current', { packages: ['corechart'] });
google.charts.setOnLoadCallback(drawPrliChart);