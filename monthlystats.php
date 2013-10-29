<?php
  global $civicrm_id;
  include 'login.php';
  include 'dbcalls.php';
  date_default_timezone_set('America/Toronto');

  $monInfo = 0;
  if($_POST["monSubmitted"]){
    include 'blackbox.php';
    $monInfo = add_monthly($_POST);
  }

  $title = "Monthly Stats";
  $thisFile = "monthlystats.php";
  $monAddActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0, 2, 3, 4, 5 ] },
    {'iDataSort':7, 'aTargets':[ 0 ]}, {'bVisible':false, 'aTargets': [ 7 ]}],\n";
  include 'header.php';
?>

    <script type="text/javascript">
    $(document).ready(function() {
      $.ajaxSetup({  
        cache: false  
      });

      $("#datatable tbody").on( "click", ".editMON", function() {
        var edit = $(this);
        var modal = $('#myModal');
        var parent = edit.closest('tr');
        modal.find('#inputID').val(parent.find('.hiddenID').text());
        modal.find('#inputEdited').val("true");
        modal.find('#inputCampus').selectpicker('val', parent.find('.hiddenCampus').text());
        modal.find('#inputUnRec').val(parent.find(".fUnRec").text());
        modal.find('#inputGrow').val(parent.find('.fGrow').text());
        modal.find('#inputMin').val(parent.find('.fMin').text());
        modal.find('#inputMult').val(parent.find('.fMult').text());
        var tableDate = parent.find(".hiddenDate").text();
        modal.find('#inputDate').val(tableDate);
        modal.find('h4').text('Edit Monthly Stats - ' + moment(tableDate).format('MMMM YYYY'));
        $("#monForm").validate().resetForm();
        $("#monForm").validate().reset();
        $("div .has-error").removeClass("has-error");
        $("div .has-success").removeClass("has-success");
        modal.find('#inputCampus').prop('disabled',true);
        modal.find('#inputCampus').selectpicker('refresh');
        $('#monthWarn').hide();
      });

      $('#monForm').validate({
        ignore: ":hidden:not(.selectpicker)",
        rules: {
          inputCampus: {
            required: true
          },
          inputUnRec: {
            required: true,
            digits: true
          },
          inputGrow: {
            required: true,
            digits: true
          },
          inputMin: {
            required: true,
            digits: true
          },
          inputMult: {
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

      $("#myModal .selectpicker").on('change', function(ev) {
        $("#monForm").validate().element("#inputCampus");

        if(($("#inputEdited").val() == "") && ($("#inputCampus").val())){
          $.getJSON(
            "ajax/monthcheck.php", 
            {cid: $("#inputCampus").val()},  
            function(json){
              if(json.type == "double"){
                $("#inputID").val(json.aid)
                $("#inputUnRec").val(json.unrec);
                $("#monForm").validate().element("#inputUnRec");
                $("#inputGrow").val(json.grow);
                $("#monForm").validate().element("#inputGrow");
                $("#inputMin").val(json.min);
                $("#monForm").validate().element("#inputMin");
                $("#inputMult").val(json.mult);
                $("#monForm").validate().element("#inputMult");
                $("#monthWarn").show();
                $('#monthPop').hide();
              }
              else if(json.type == "autopop"){
                $("#inputUnRec").val("");
                $("#inputUnRec").closest('.form-group').removeClass('has-success');
                $("#inputGrow").val(json.grow);
                $("#monForm").validate().element("#inputGrow");
                $("#inputMin").val(json.min);
                $("#monForm").validate().element("#inputMin");
                $("#inputMult").val(json.mult);
                $("#monForm").validate().element("#inputMult");
                $('#monthWarn').hide();
                $('#monthPop').show();
              }
              else {
                $("#inputUnRec").val("");
                $("#inputUnRec").closest('.form-group').removeClass('has-success');
                $("#inputGrow").val("");
                $("#inputGrow").closest('.form-group').removeClass('has-success');
                $("#inputMin").val("");
                $("#inputMin").closest('.form-group').removeClass('has-success');
                $("#inputMult").val("");
                $("#inputMult").closest('.form-group').removeClass('has-success');
                $('#monthWarn').hide();
                $('#monthPop').hide();
              }
            }
          );
        }
 
      });

      $("#modalBtn").click(function() {
        $('#monForm')[0].reset();
        $('#inputID').removeAttr("value");
        $('#inputEdited').val("");
        $("#inputCampus").selectpicker('val', $('#selectCampus').val());
        $('#myModal h4').text('Add Monthly Stats - ' + moment().format('MMMM YYYY'));
        $("#monForm").validate().resetForm();
        $("#monForm").validate().reset();
        $("div .has-error").removeClass("has-error");
        $("div .has-success").removeClass("has-success");
        $("#inputDate").val(moment().format('YYYY-MM-DD'));
        $('#inputCampus').prop('disabled',false);
        $('#inputCampus').selectpicker('refresh');
      });

      $('#myModal').on('shown.bs.modal', function () {
        $('.dropdown-toggle').focus();
      });

      if($('#hiddenAdd').val() == "true"){
        $('#modalBtn').click();
      }

    });
    </script>

    <div class="col-md-9" >
      <?php
        if($monInfo){
          $monLabel = "Added";
          if($_POST["inputID"]){
            $monLabel = "Edited";
          }
          if($monInfo == 1){
            ?>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Success!</strong> Monthly Stats <?php echo $monLabel; ?>.
            </div>
          <?php } else { ?>
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Error!</strong> Failed to submit Monthly Stats. Please inform CC Team. Error message: "<?php echo $monInfo; ?>"
            </div>
          <?php
          }
        }
      ?>
      <div id="autoWarn" class="alert alert-warning alert-dismissable hiddenWarning">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Warning!</strong> Monthly Reports marked with a yellow Edit button need to be updated.
      </div>
      <div class="text-center">
        <a id="modalBtn" data-toggle="modal" href="#myModal" class="btn btn-success btn-large"></a>
        <script type="text/javascript">
          $("#modalBtn").html("Add Monthly Stats - " + moment().format('MMMM YYYY'));
        </script>
      </div>

      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Campus</th>
            <th>Unrecorded Engagements</th>
            <th>Growing</th>
            <th>Ministering</th>
            <th>Multiplying</th>
            <th>Edit</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $stats = getMonthly($_POST);
            $automated = false;
            foreach($stats as $mon){
              echo "<tr><td class=\"fDate\">" . date("M Y", strtotime($mon["DATE"])) .
                "<span class=\"hiddenDate\">" . $mon["DATE"] . "</span></td>";
              echo "<td class=\"fCampus\">" . $mon["CAMPUS"] . "<span class=\"hiddenCampus\">" . $mon["CAMPUS_ID"] . "</span></td>";
              echo "<td class=\"fUnRec\">" . $mon["UNRECORDED"] . "</td>";
              echo "<td class=\"fGrow\">" . $mon["GROWING"] . "</td>";
              echo "<td class=\"fMin\">" . $mon["MINISTERING"] . "</td>";
              echo "<td class=\"fMult\">" . $mon["MULTIPLYING"] . "</td>";
              echo "<td><span class=\"hiddenID\">" . $mon["ID"] . "</span>";
              if($mon["AUTOGEN"]){
                $automated = true;
                echo "<a data-toggle=\"modal\" href=\"#myModal\" class=\"btn btn-warning editMON\">Edit</a></td>";
              }
              else {
                echo "<a data-toggle=\"modal\" href=\"#myModal\" class=\"btn btn-primary editMON\">Edit</a></td>";
              }
              echo "<td>" . $mon["DATE"] . "</td></tr>";
            }
            if($automated){
              ?>
              <script type="text/javascript">
                $('#autoWarn').show();
              </script>
              <?php
            }
          ?>
        </tbody>
        <tfoot>
        </tfoot>
      </table>

      <div class="well well-sm">
        <h3>Help</h3>
        <p><strong>Growing disciples:</strong></p>
        <p>The number of people involved in a local campus movement that are doing this one thing:
          growing in their faith.</p>
        <p><strong>Ministering disciples:</strong></p>
        <p>The number of people involved in a local campus movement that are doing these two things:
          growing in their faith and sharing their faith with others.</p>
        <p><strong>Multiplying disciples:</strong></p>
        <p>The number of people involved in a local campus movement that are doing these three things:
          growing in their faith, sharing their faith with others and discipling others to do the same.</p>
        <p><strong>Engagements:</strong></p>
        <p>A person who begins, or continues, to be involved in gospel-themed conversations with someone from P2C,
          either face-to-face or digitally. (ex. we know their name, something about them and can contact them again).
          For online conversations, a person could be counted as an engagement if they are interacting (not just a random comment)
          and the conversation is gospel centred.</p>
      </div>
    </div>
  <?php include 'footer.php'; ?>

  <!-- Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Add Monthly Stats</h4>
        </div>
        <div class="modal-body">
          <div id="monthWarn" class="alert alert-warning hiddenWarning">
            <button type="button" class="close" onclick="$('#monthWarn').hide()" aria-hidden="true">&times;</button>
            <strong>Warning!</strong> Monthly Data entered for this campus already. Submitting this form will overwrite previous submission.
          </div>
          <div id="monthPop" class="alert alert-info hiddenWarning">
            <button type="button" class="close" onclick="$('#monthPop').hide()" aria-hidden="true">&times;</button>
            <strong>Notice!</strong> Involvement Thresholds populated from most recent monthly report, not from current Pulse information.
          </div>
          <form class="form-horizontal" id="monForm" role="form" action="monthlystats.php" method="post">
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
              <label for="inputUnRec" class="col-lg-3 control-label">Unrecorded Engagements</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputUnRec" name="inputUnRec" placeholder="Number">
              </div>
            </div>
            <div class="form-group">
              <p class="text-center"><strong>Involvement Thresholds</strong></p>
            </div>
            <div class="form-group">
              <label for="inputGrow" class="col-lg-3 control-label">Growing</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputGrow" name="inputGrow" placeholder="Number">
              </div>
            </div>
            <div class="form-group">
              <label for="inputMin" class="col-lg-3 control-label">Ministering</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputMin" name="inputMin" placeholder="Number">
              </div>
            </div>
            <div class="form-group">
              <label for="inputMult" class="col-lg-3 control-label">Multiplying</label>
              <div class="col-lg-9">
                <input type="text" class="form-control" id="inputMult" name="inputMult" placeholder="Number">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" id="inputID" name="inputID">
          <input type="hidden" id="inputAuto" name="inputAuto" value="0">
          <input type="hidden" id="inputEdited" name="inputEdited">
          <input type="hidden" id="inputDate" name="inputDate">
          <input type="hidden" name="monSubmitted" value="true">
          <button type="submit" class="btn btn-success">Submit</button>
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  </body>
</html>
