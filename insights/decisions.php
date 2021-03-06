<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $method = array("1" => "Random Evangelism", "2" => "Friendship Evangelism", 
    "3" => "MDA Outreach", "4" => "Campus Wide Event", 
    "5" => "Investigative Bible Study", "6" => "Leadership Luncheon", 
    "7" => "Weekly Meeting Follow-up", "8" => "SIQ Follow-up", 
    "9" => "Internet Evangelism", "10" => "Jesus Video", "11" => "Other");
  $integrated = array("0" => "Not Sure", "10" => "Integrated with P2C", "8" => "Integrated with Christian Community");

  $decInfo = 0;
  if($_POST["idSubmitted"]){
    include $_SERVER['DOCUMENT_ROOT'].'/insights/blackbox.php';
    $decInfo = add_decision($_POST);
  }

  $title = "Indicated Decisions";
  $thisFile = "/insights/decisions/";
  $activeInsights = "active";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  if($_POST["hiddenAdd"] == "true" || $_GET["add"] == "true"){
    $idAddActive = "active";
  }
  else {
    $idBNActive = "active";
  }
  $tableConfig = "'aaSorting': [[ 0, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <script type="text/javascript">
    $(document).ready(function() {
      $("#datatable tbody").on( "click", ".editID", function() {
        var edit = $(this);
        var modal = $('#myModal');
        var parent = edit.closest('tr');
        modal.find('#inputFirst').val(parent.find(".hiddenFirst").text());
        modal.find('#inputLast').val(parent.find(".hiddenLast").text());
        if(!($("#inputCampus option[value='" + parent.find('.hiddenCampus').text() + "']").length > 0)){
          $('#inputCampus').append($("<option/>",
            {value: parent.find('.hiddenCampus').text(), text: parent.find('.fCampus').text()}));
          $('#inputCampus').selectpicker('refresh');
        }
        modal.find('#inputCampus').selectpicker('val', parent.find('.hiddenCampus').text());
        modal.find('#inputDate').val(parent.find(".fDate").text());
        modal.find('#inputWitness').val(parent.find(".fWitness").text());
        modal.find('#inputMethod').selectpicker('val', parent.find(".hiddenMethod").text());
        modal.find('#inputIntegrated').selectpicker('val', parent.find('.hiddenInt').text());
        modal.find('#inputStory').val(parent.find('.fStory').text());
        modal.find('#inputID').val(parent.find('.hiddenID').text());
        modal.find('#inputCID').val(parent.find('.hiddenCID').text());
        modal.find('h4').text('Edit Indicated Decision');
        $("#idForm").validate().resetForm();
        $("#idForm").validate().reset();
        $("div .has-error").removeClass("has-error");
        $("div .has-success").removeClass("has-success");
      });

      $('#idForm').validate({
        ignore: ":hidden:not(.selectpicker)",
        rules: {
          inputFirst: {
            required: true
          },
          inputCampus: {
            required: true
          },
          inputDate: {
            required: true,
            dateISO: true
          },
          inputWitness: {
            required: true
          },
          inputMethod: {
            required: true
          },
          inputIntegrated: {
            required: true
          }
        },
        highlight: function(element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function(element) {
          $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
          $(element).removeClass('error').addClass('valid').addClass('error');
        },
        errorPlacement: function (error, element) {
        if ($(element).is('select')) {
            element.next().after(error); // special placement for select elements
        } else {
            error.insertAfter(element);  // default placement for everything else
        }
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

      $('#inputWitness').keyup(function(e) {
        var code = e.keyCode || e.which;
        if (code == '9') {
          $('#inputDate').datepicker('hide');
          $('#inputWitness').focus();
        }
      });

      $("#myModal .selectpicker").on('change', function(ev) {
        if($('#inputCampus').valid()){
          $ ('#inputCampus').removeClass('has-error').addClass('has-success');   
        }
        if($('#inputMethod').valid()){
          $ ('#inputMethod').removeClass('has-error').addClass('has-success');   
        }
        if($('#inputIntegrated').valid()){
          $ ('#inputIntegrated').removeClass('has-error').addClass('has-success');   
        }
      });

      $("#modalBtn").click(function() {
        $('#idForm')[0].reset();
        $("#inputCampus").selectpicker('val', $('#selectCampus').val());
        $("#inputMethod").selectpicker('val', 1);
        $("#inputIntegrated").selectpicker('val', 0);
        $('#myModal h4').text('Add Indicated Decision');
        $("#idForm").validate().resetForm();
        $("#idForm").validate().reset();
        $("div .has-error").removeClass("has-error");
        $("div .has-success").removeClass("has-success");
        $("#inputDate").val(moment().format('YYYY-MM-DD'));
      });

      $('#myModal').on('shown.bs.modal', function () {
        $('#inputFirst').focus();
      });

      if($('#hiddenAdd').val() == "true"){
        $('#modalBtn').click();
      }

    });
    </script>

    <div class="col-md-9" >
      <?php
        if($decInfo){
          $decType = "Added";
          if($_POST["inputCID"]){
            $decType = "Edited";
          }
          if($decInfo == 1){
            ?>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Success!</strong> Indicated Decision <?php echo $decType; ?>.
            </div>
          <?php } else { ?>
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Error!</strong> Failed to submit indicated decision. Please inform CC Team. Error message: "<?php echo $decInfo; ?>"
            </div>
          <?php
          }
        }
      ?>
      <div class="text-center">
        <a id="modalBtn" data-toggle="modal" href="#myModal" class="btn btn-success btn-large">Add Indicated Decision</a>
      </div>

      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>New Believer</th>
            <th>Witness</th>
            <th rel="tooltip" title="How this person came to know Christ">Method</th>
            <th>Story</th>
            <th rel="tooltip" title="Whether they are involved in a Christian community">Integrated?</th>
            <th>Campus</th>
            <th>Edit</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $decisions = getDecisions($_POST);
            foreach($decisions as $dec){
              echo "<tr><td class=\"fDate\">" . $dec["DATE"] . "</td>";
              echo "<td class=\"fNew\">" . $dec["BELIEVER"] . "<span class=\"hiddenFirst\">" . $dec["B_FIRST"] . "</span>
                <span class=\"hiddenLast\">" . $dec["B_LAST"] . "</span></td>";
              echo "<td class=\"fWitness\">" . $dec["WITNESS"] . "</td>";
              echo "<td class=\"fMethod\">" . $method[$dec["METHOD"]] . "<span class=\"hiddenMethod\">" . $dec["METHOD"] . "</span></td>";
              echo "<td class=\"fStory\">" . $dec["STORY"] . "</td>";
              echo "<td class=\"fIntegrated\">" . $integrated[$dec["INTEGRATED"]] . "<span class=\"hiddenInt\">" . $dec["INTEGRATED"] . "</span></td>";
              echo "<td class=\"fCampus\">" . $dec["CAMPUS"] . "</td>";
              echo "<td><span class=\"hiddenID\">" . $dec["ID"] . "</span><span class=\"hiddenCID\">" . $dec["BELIEVER_ID"] . "</span>";
              echo "<span class=\"hiddenCampus\">" . $dec["CAMPUS_ID"] . "</span>";
              echo "<a data-toggle=\"modal\" href=\"#myModal\" class=\"btn btn-primary editID\">Edit</a></td></tr>";
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
          <h4 class="modal-title">Add Indicated Decision</h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" id="idForm" role="form" action="/insights/decisions/" method="post">
            <div class="form-group">
              <label for="inputFirst" class="col-lg-3 control-label">New Believer Name</label>
              <div class="col-lg-4">
                <input type="text" class="form-control" id="inputFirst" name="inputFirst" placeholder="First Name">
              </div>
              <div class="col-lg-5">
                <input type="text" class="form-control" id="inputLast" name="inputLast" placeholder="Last Name - Optional">
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
              <label for="inputWitness" class="col-lg-3 control-label">Witnesses</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputWitness" name="inputWitness" placeholder="Names">
              </div>
            </div>
            <div class="form-group">
              <label rel="tooltip" title="How this person came to know Christ" for="inputMethod" class="col-lg-3 control-label">Method</label>
              <div class="col-lg-9">
                <select class="selectpicker" data-width="100%" id="inputMethod" name="inputMethod">
                  <?php
                    foreach($method as $id => $label){
                      echo "<option value=\"" . $id . "\">" . $label . "</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label rel="tooltip" title="Whether they are involved in a Christian community"
                for="inputIntegrated" class="col-lg-3 control-label">Integrated Believer</label>
              <div class="col-lg-9">
                <select class="selectpicker" data-width="100%" id="inputIntegrated" name="inputIntegrated">
                  <?php
                    foreach($integrated as $id => $label){
                      echo "<option value=\"" . $id . "\">" . $label . "</option>";
                    }
                  ?>
                </select>
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
          <input type="hidden" id="inputCID" name="inputCID">
          <input type="hidden" name="idSubmitted" value="true">
          <button type="submit" class="btn btn-success">Submit</button>
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  </body>
</html>
