<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $eventType = array("1" => "Campus Wide Outreach", "2" => "Weekly Meeting Outreach", 
    "3" => "MDA Outreach", "4" => "Online Outreach", "10" => "Other");
  $eventDisp = array("1" => "Campus Wide Outreach", "2" => "Weekly Meeting Outreach",
    "3" => "MDA Outreach", "4" => "Online Outreach", "10" => "Other", "11" => "Legacy Pulse Outreach");

  $evInfo = 0;
  if($_POST["evSubmitted"]){
    include $_SERVER['DOCUMENT_ROOT'].'/insights/blackbox.php';
    $evInfo = add_event($_POST);
  }

  $title = "Event Stats";
  $thisFile = "/insights/eventstats/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $evAddActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0, 4, 5 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <script type="text/javascript">
    $(document).ready(function() {
      $("#datatable tbody").on( "click", ".editEV", function() {
        var edit = $(this);
        var modal = $('#myModal');
        var parent = edit.closest('tr');
        modal.find('#inputName').val(parent.find(".fName").text());
        if(!($("#inputCampus option[value='" + parent.find('.hiddenCampus').text() + "']").length > 0)){
          $('#inputCampus').append($("<option/>",
            {value: parent.find('.hiddenCampus').text(), text: parent.find('.fCampus').text()}));
          $('#inputCampus').selectpicker('refresh');
        }
        modal.find('#inputCampus').selectpicker('val', parent.find('.hiddenCampus').text());
        modal.find('#inputDate').val(parent.find(".fDate").text());
        if(!($("#inputType option[value='" + parent.find('.hiddenType').text() + "']").length > 0)){
          $('#inputType').append($("<option/>",
            {value: parent.find('.hiddenType').text(), text: parent.find('.fType').text()}));
          $('#inputType').selectpicker('refresh');
        }
        modal.find('#inputType').selectpicker('val', parent.find(".hiddenType").text());
        modal.find('#inputTotal').val(parent.find('.fTotal').text());
        modal.find('#inputNon').val(parent.find('.fNon').text());
        modal.find('#inputStory').val(parent.find('.fStory').text());
        modal.find('#inputID').val(parent.find('.hiddenID').text());
        modal.find('h4').text('Edit Event Stats');
        $("#evForm").validate().resetForm();
        $("#evForm").validate().reset();
        $("div .has-error").removeClass("has-error");
        $("div .has-success").removeClass("has-success");
      });

      $('#evForm').validate({
        ignore: ":hidden:not(.selectpicker)",
        rules: {
          inputName: {
            required: true
          },
          inputCampus: {
            required: true
          },
          inputDate: {
            required: true,
            dateISO: true
          },
          inputType: {
            required: true
          },
          inputTotal: {
            required: true,
            digits: true
          },
          inputNon: {
            required: true,
            digits: true
          }
        },
        highlight: function(element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function(element) {
          $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
          $(element).removeClass('error').addClass('valid').addClass('error');
        }
      });

      $('#inputDate').datepicker({
        "format": "yyyy-mm-dd"
      }).on('changeDate', function(ev) {
        if($('#inputDate').valid()){
          $('#inputDate').removeClass('has-error').addClass('has-success');
          $('#inputDate').datepicker('hide');
        }
      });

      $('#inputTotal').keyup(function(e) {
        var code = e.keyCode || e.which;
        if (code == '9') {
          $('#inputDate').datepicker('hide');
          $('#inputTotal').focus();
        }
      });

      $("#myModal .selectpicker").on('change', function(ev) {
        if($('#inputCampus').valid()){
          $ ('#inputCampus').removeClass('has-error').addClass('has-success');   
        }
        if($('#inputType').valid()){
          $ ('#inputType').removeClass('has-error').addClass('has-success');   
        }
      });

      $("#modalBtn").click(function() {
        $('#evForm')[0].reset();
        $("#inputCampus").selectpicker('val', $('#selectCampus').val());
        $("#inputType").selectpicker('val', 1);
        $('#myModal h4').text('Add Event Stats');
        $("#evForm").validate().resetForm();
        $("#evForm").validate().reset();
        $("div .has-error").removeClass("has-error");
        $("div .has-success").removeClass("has-success");
        $("#inputDate").val(moment().format('YYYY-MM-DD'));
      });

      $('#myModal').on('shown.bs.modal', function () {
        $('#inputName').focus();
      });

      if($('#hiddenAdd').val() == "true"){
        $('#modalBtn').click();
      }

    });
    </script>

    <div class="col-md-9" >
      <?php
        if($evInfo){
          $evLabel = "Added";
          if($_POST["inputID"]){
            $evLabel = "Edited";
          }
          if($evInfo == 1){
            ?>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Success!</strong> Event Stats <?php echo $evLabel; ?>.
            </div>
          <?php } else { ?>
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Error!</strong> Failed to submit Event Stats. Please inform CC Team. Error message: "<?php echo $evInfo; ?>"
            </div>
          <?php
          }
        }
      ?>
      <div class="text-center">
        <a id="modalBtn" data-toggle="modal" href="#myModal" class="btn btn-success btn-large">Add Event Stats</a>
      </div>

      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Event Name</th>
            <th>Type</th>
            <th>Story</th>
            <th>Total Attendance</th>
            <th>Non-Christian Attendance</th>
            <th>Campus</th>
            <th>Edit</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $events = getEvents($_POST);
            foreach($events as $ev){
              echo "<tr><td class=\"fDate\">" . $ev["DATE"] . "</td>";
              echo "<td class=\"fName\">" . $ev["NAME"] . "</td>";
              echo "<td class=\"fType\">" . $eventDisp[$ev["TYPE"]] . "</td>";
              echo "<td class=\"fStory\">" . $ev["STORY"] . "</td>";
              echo "<td class=\"fTotal\">" . $ev["TOTAL"] . "</td>";
              echo "<td class=\"fNon\">" . $ev["NONCHRISTIAN"] . "</td>";
              echo "<td class=\"fCampus\">" . $ev["CAMPUS"] . "</td>";
              echo "<td><span class=\"hiddenID\">" . $ev["ID"] . "</span><span class=\"hiddenCampus\">" . $ev["CAMPUS_ID"] . "</span>";
              echo "<span class=\"hiddenType\">" . $ev["TYPE"] . "</span>";
              echo "<a data-toggle=\"modal\" href=\"#myModal\" class=\"btn btn-primary editEV\">Edit</a></td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
        </tfoot>
      </table>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  <!-- Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Add Event Stats</h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" id="evForm" role="form" action="/insights/eventstats/" method="post">
            <div class="form-group">
              <label for="inputName" class="col-lg-3 control-label">Name of Event</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputName" name="inputName" placeholder="Name">
              </div>
            </div>
            <div class="form-group">
              <label for="inputType" class="col-lg-3 control-label">Type of Event</label>
              <div class="col-lg-9">
                <select class="selectpicker" data-width="100%" id="inputType" name="inputType">
                  <?php
                    foreach($eventType as $id => $label){
                      echo "<option value=\"" . $id . "\">" . $label . "</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputCampus" class="col-lg-3 control-label">Campus</label>
              <div class="col-lg-9">
                <select class="selectpicker" data-width="100%" data-size="10" id="inputCampus" name="inputCampus">
                  <option selected="selected" disabled="disabled" value="0">Choose Campus</option>
                  <?php
                    $schools = getSchools();
                    foreach($schools as $id => $label){
                      echo "<option value=\"" . $id . "\">" . $label . "</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputDate" class="col-lg-3 control-label">Date</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputDate" name="inputDate" placeholder="YYYY-MM-DD">
              </div>
            </div>
            <div class="form-group">
              <label for="inputChristian" class="col-lg-3 control-label">Total Attendance</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputTotal" name="inputTotal" placeholder="Attendance">
              </div>
            </div>
            <div class="form-group">
              <label for="inputNon" class="col-lg-3 control-label">Non-Christian Attendance</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputNon" name="inputNon" placeholder="Attendance">
              </div>
            </div>
            <div class="form-group">
              <label for="inputStory" class="col-lg-3 control-label">Story</label>
              <div class="col-lg-9">
                <textarea class="form-control" id="inputStory" name="inputStory" rows="3" placeholder="Optional"></textarea>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" id="inputID" name="inputID">
          <input type="hidden" name="evSubmitted" value="true">
          <button type="submit" class="btn btn-success">Submit</button>
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  </body>
</html>
