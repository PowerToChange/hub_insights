<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Stylesheets -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/datatables.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/daterangepicker-bs3.css">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.js"></script> 
    <script src="js/datatables.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/daterangepicker.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        $('.datatable').dataTable({
          <?php echo $tableConfig; ?>
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

        var selectStart = startThis;
        var selectEnd = endThis;
        <?php 
          if($_POST["selectSubmitted"]){
            echo "var selectStart = moment('" . $_POST["hiddenStart"] . "', 'YYYY-MM-DD');\n";
            echo "var selectEnd = moment('" . $_POST["hiddenEnd"] . "', 'YYYY-MM-DD');\n";
            echo "$('#selectCampus').selectpicker('val', '" . $_POST["selectCampus"] . "');\n";
          }
        ?>
            
        $('#reportrange').daterangepicker({
          ranges: {
             'This Month': [moment().startOf('month'), moment().endOf('month')],
             'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
             'This Year': [startThis, endThis],
             'Last Year': [startLast, endLast],
             'All Time': [moment().subtract('years', 100), moment().add('years', 100)]
          },
          startDate: selectStart,
          endDate: selectEnd,
          format: 'YYYY-MM-DD',
        },
        function(start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
          $('#hiddenStart').val(start.format('YYYY-MM-DD'));
          $('#hiddenEnd').val(end.format('YYYY-MM-DD'));
        });
        $('#reportrange span').html(selectStart.format('MMMM D, YYYY') + ' - ' + selectEnd.format('MMMM D, YYYY'));
        $('#hiddenStart').val(selectStart.format('YYYY-MM-DD'));
        $('#hiddenEnd').val(selectEnd.format('YYYY-MM-DD'));
      });
    </script>
  </head>

  <body>
    <div class="jumbotron">
      <h1><?php echo $title; ?></h1>
      <div class="pull-right">
        <i class="glyphicon glyphicon-user"></i>
        <a href="?logout="> Logout of <?php echo $user["firstName"] . " " . $user["lastName"]; ?></a>
      </div>
      <?php //print_r($sends); ?>
    </div>

    <div class="container">
      <?php checkUser($isStaff); ?>

    <div class="row">

      <div class="col-md-3 col-sm-12">
        <div class="well">
          <h2>Sidebar</h2>

          <div class="well well-sm">
          <form id="rangeForm" role="form" action="<?php echo $thisFile; ?>" method="post">
            <select class="selectpicker" data-width="100%" data-size="10" id="selectCampus" name="selectCampus">
              <option selected="selected" value="0">All Campuses</option>
              <?php
                $schools = getSchools();
                foreach($schools as $id => $label){
                  echo "<option value=\"" . $id . "\">" . $label . "</option>";
                }
              ?>
            </select>
            <input type="hidden" id="hiddenStart" name="hiddenStart">
            <input type="hidden" id="hiddenEnd" name="hiddenEnd">
            <input type="hidden" id="hiddenAdd" name="hiddenAdd" value="<?php echo ($_POST["hiddenAdd"] == "true" ? "true" : "false"); ?>">
            <input type="hidden" name="selectSubmitted" value="true">
          </form>

          <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-bottom: 10px;">
            <i class="glyphicon glyphicon-calendar icon-calendar"></i>
            <span></span> <b class="caret"></b>
          </div>

          <a class="btn btn-warning" style="width:100%" onclick="$('#rangeForm')[0].submit();">Update Display</a>

        </div>

        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseOne" href="#collapseOne">
                    Indicated Decisions
                  </a>
                </h4>
              </div>
              <div id="collapseOne" class="list-group panel-collapse collapse in">
                  <a id="idAdd" href="javascript:{}" class="list-group-item active">Add/Edit Decisions</a>
                  <a id="idBigPicture" href="javascript:{}" class="list-group-item">Big Picture</a>
                  <a id="idByMethod" href="javascript:{}" class="list-group-item">By Method</a>
                  <a id="idByName" href="javascript:{}" class="list-group-item">By Name</a>
              </div>
            </div>
            <!-- <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo" href="#collapseTwo">
                    Reports
                  </a>
                </h4>
              </div>
              <div id="collapseTwo" class="list-group panel-collapse collapse">
                <a href="javascript:{}" class="list-group-item active">Indicated Decisions</a>
                <a href="javascript:{}" class="list-group-item">Big Picture</a>
                <a id="byMethod" href="javascript:{}" class="list-group-item">By Method</a>
                <a href="javascript:{}" class="list-group-item">By Name</a>
                <a href="javascript:{}" class="list-group-item active">Monthly Reports</a>
                <a href="javascript:{}" class="list-group-item">Big Picture</a>
                <a href="javascript:{}" class="list-group-item">By Campus</a>
                <a href="javascript:{}" class="list-group-item">Event Stats</a>
              </div>
            </div> -->
        </div>


        </div>
      </div>
