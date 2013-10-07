$(document).ready(function() {
  $('.datatable').dataTable({
    "sPaginationType": "bs_normal"
  }); 
  $('.datatable').each(function(){
    var datatable = $(this);
    // SEARCH - Add the placeholder for Search and Turn this into in-line form control
    var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
    search_input.attr('placeholder', 'Search');
    search_input.addClass('form-control input-sm');
    // LENGTH - Inline-Form control
    var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
    length_sel.addClass('form-control input-sm');
  });
  $('.dataTables_length').find('select').removeClass();

  if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
    $('.selectpicker').selectpicker('mobile');
  }
  else {
    $('.selectpicker').selectpicker();
  }

  $('#idAdd').click(function() {
    $('#rangeForm').attr("action", "decisions.php");
    $('#hiddenAdd').val("true");
    $('#rangeForm')[0].submit();
  });

  if($('#hiddenAdd').val() == "true"){
    $('#modalBtn').click();
  }

  $('#idBigPicture').click(function() {
    $('#rangeForm').attr("action", "idbigpicture.php");
    $('#rangeForm')[0].submit();
  });

  $('#idByMethod').click(function() {
    $('#rangeForm').attr("action", "idbymethod.php");
    $('#rangeForm')[0].submit();
  });

  $('#idByName').click(function() {
    $('#rangeForm').attr("action", "decisions.php");
    $('#hiddenAdd').val("false");
    $('#rangeForm')[0].submit();
  });

  var startThis = moment().month(8).startOf('month');
  var endThis = moment().month(7).add('years',1).endOf('month');
  var startLast = moment().month(8).subtract('years',1).startOf('month');
  var endLast = moment().month(7).endOf('month');
  if(moment().month() < 8){
    var startThis = moment().month(8).subtract('years',1).startOf('month');
    var endThis = moment().month(7).endOf('month');
    var startLast = moment().month(8).subtract('years',2).startOf('month');
    var endLast = moment().month(7).subtract('years',1).endOf('month');
  }
      
  $('#reportrange').daterangepicker({
    ranges: {
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
       'This Year': [startThis, endThis],
       'Last Year': [startLast, endLast],
       'All Time': [moment().subtract('years', 100), moment().add('years', 100)]
    },
    startDate: startThis,
    endDate: endThis,
    format: 'YYYY-MM-DD',
  },
  function(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('#hiddenStart').val(start.format('YYYY-MM-DD'));
    $('#hiddenEnd').val(end.format('YYYY-MM-DD'));
  });
  $('#reportrange span').html(startThis.format('MMMM D, YYYY') + ' - ' + endThis.format('MMMM D, YYYY'));
  $('#hiddenStart').val(startThis.format('YYYY-MM-DD'));
  $('#hiddenEnd').val(endThis.format('YYYY-MM-DD'));
});