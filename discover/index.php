<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $rejType = array("1" => "Interaction", "2" => "Spiritual Conversation", 
    "3" => "Gospel Presentation", "4" => "Indicated Decision", "5" => "Shared Spirit-Filled Life");
  $method = array("1" => "Random Evangelism", "2" => "Friendship Evangelism", 
    "3" => "MDA Outreach", "4" => "Campus Wide Event", 
    "5" => "Investigative Bible Study", "6" => "Leadership Luncheon", 
    "7" => "Weekly Meeting Follow-up", "8" => "SIQ Follow-up", 
    "9" => "Internet Evangelism", "10" => "Jesus Video", "11" => "Other");
  $integrated = array("0" => "Not Sure", "10" => "Integrated with P2C", "8" => "Integrated with Christian Community");

  if($_POST["inputCID"]){
    $conValues = $_POST;
    $conReturn = edit_contact($_POST);
  }

  $title = "Discover";
  $thisFile = "/discover/";
  $activeDiscover = "class='active'";

  $contactID = "60088";
  $contact_info = get_contact(array("id" => $contactID));
  $contact = $contact_info["values"][$contactID];
  $relationships = $contact_info["values"][$contactID]["api.Relationship.get"]["values"];
  $notes = $contact_info["values"][$contactID]["api.Note.get"]["values"];
  $activities = $contact_info["values"][$contactID]["api.Activity.get"]["values"];


  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>

  <script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({  
      cache: false  
    });

    jQuery.validator.addMethod('phoneUS', function(phone_number, element) {
      phone_number = phone_number.replace(/\s+/g, ''); 
      return this.optional(element) || phone_number.length > 9 &&
        phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, 'Enter a valid phone number.');

    $('#editForm').validate({
      ignore: ':not(select:hidden, input:visible, textarea:visible)',
      rules: {
        inputFirst: {
          required: true
        },
        inputLast: {
          required: true
        },
        selectCampus: {
          required: true
        },
        selectGender: {
          required: true
        },
        selectInter: {
          required: true
        },
        selectLevel: {
          required: true
        },
        inputEmail: {
          required: true,
          email: true
        },
        inputPhone: {
          required: true,
          phoneUS: true
        }
      },
      messages: {
        inputEmail: {
          email: "Enter a valid email."
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
    $("#submitInfo").click(function(){
      if($("#editForm").valid()){
        $("#editForm").submit();
      }
    });

    $('#rejForm').validate({
      ignore: ":hidden:not(.selectpicker)",
      rules: {
        inputType: {
          required: true
        },
        inputWitness: {
          required: function(element){ return $("#inputType").val() == 4; }
        },
        inputMethod: {
          required: function(element){ return $("#inputType").val() == 4; }
        },
        inputIntegrated: {
          required: function(element){ return $("#inputType").val() == 4; }
        }
      },
      highlight: function(element) {
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
      },
      success: function(element) {
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(element).removeClass('error').addClass('valid').addClass('error');
      },
      submitHandler: function(form) {
        $("#activityTable tbody").append("<tr id='loading'><td><img class='img-responsive centre' src='/images/loading.gif'></td></tr>");
        $.getJSON(
          "/discover/ajax/submitrejoice.php", 
          $(form).serialize(),  
          function(json){
            var alert = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' " +
              "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error!</strong> " + json.result + "</div>";
            if(json.result == 1){
              var alert = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' " +
                "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Success!</strong> Rejoiceable Added</div>";
              var newRejoice = "<tr><td><strong><i class='glyphicon glyphicon-certificate'></i> Rejoiceable | " + json.type + 
                "</strong><small class='pull-right'>" + moment(json.date, "YYYY-MM-DD H:mm:ss").fromNow() + 
                "</small><br><span>" + json.info + "</span></td></tr>\n";
              $("#activityTable tbody #loading").remove();
              $("#activityTable tbody").append(newRejoice);
            }
            $("#flash").html(alert);
            window.setTimeout(function() { 
              $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
              $(this).remove(); 
            })}, 4000);
          }
        );
        $('#rejoiceModal').modal('hide');
      }
    });

    $('#noteForm').validate({
      ignore: ":hidden:not(.selectpicker)",
      rules: {
        inputSubject: {
          required: true
        },
        inputNote: {
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
      submitHandler: function(form) {
        $("#noteTable tbody").append("<tr id='loading2'><td><img class='img-responsive centre' src='/images/loading.gif'></td></tr>");
        $.getJSON(
          "/discover/ajax/submitnote.php", 
          $(form).serialize(),  
          function(json){
            var alert = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' " +
              "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error!</strong> " + json.result + "</div>";
            if(json.result == 1){
              var alert = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' " +
                "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Success!</strong> Note Added</div>";
              var newNote = "<tr><td><strong><i class='glyphicon glyphicon-pencil'></i> " + json.subject + 
                "</strong><small class='pull-right'>" + moment(json.date, "YYYY-MM-DD H:mm:ss").fromNow() + 
                "</small><br><span>" + json.note + "</span></td></tr>\n";
              $("#noteTable tbody #loading2").remove();
              $("#noteTable tbody").append(newNote);
            }
            $("#flash").html(alert);
            window.setTimeout(function() { 
              $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
              $(this).remove(); 
            })}, 4000);
          }
        );
        $('#noteModal').modal('hide');
      }
    });

    $('#decisionForm').hide();
    $("#inputType").on('change', function(ev) {
      if($(this).val() == 4){
        $('#decisionForm').show();
      }
      else{
        $('#decisionForm').hide();
      }
    });

    $("#editInfoGroup").hide();
    $(".infoEdit").hide();
    $("#editInfo").click(function(){
      $("#editInfo").hide();
      $("#editInfoGroup").show();
      $(".infoDisplay").hide();
      $(".infoEdit").show();
    });
    $("#cancelInfo").click(function(){
      $("#editInfo").show();
      $("#editInfoGroup").hide();
      $(".infoDisplay").show();
      $(".infoEdit").hide();
    });

    $(".msgAction").click(function(){
      var type = "2";
      var glyphicon = "glyphicon-earphone";
      var label = "Phone Call";
      if($(this).attr("href").indexOf("mailto") >= 0){
        type = "3";
        glyphicon = "glyphicon-envelope";
        label = "Email";
      }
      else if($(this).attr("href").indexOf("sms") >= 0){
        type = "4";
        glyphicon = "glyphicon-comment";
        label = "Text/SMS";
      }
      var msgID = "<?php echo $civicrm_id; ?>";
      var msgCID = "<?php echo $contactID; ?>";
      var name = "<?php echo $user['firstName'] . ' ' . $user['lastName']; ?>";
      $("#activityTable tbody").append("<tr id='loading'><td><img class='img-responsive centre' src='/images/loading.gif'></td></tr>");
      $.getJSON(
        "/discover/ajax/submitmsg.php", 
        "id=60088&type="+type+"&msgID="+msgID+"&msgCID="+msgCID+"&name="+encodeURIComponent(name),  
        function(json){
          var alert = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' " +
            "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error!</strong> " + json.result + "</div>";
          if(json.result == 1){
            var alert = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' " +
              "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Success!</strong> " + label + " Added</div>";
            var newMsg = "<tr><td><strong><i class='glyphicon glyphicon-earphone'></i> " + label + 
              "</strong><small class='pull-right'>" + moment(json.date, "YYYY-MM-DD H:mm:ss").fromNow() + 
              "</small><br><span>" + name + "</span></td></tr>\n";
            $("#activityTable tbody #loading").remove();
            $("#activityTable tbody").append(newMsg);
          }
          $("#flash").html(alert);
          window.setTimeout(function() { 
            $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
            $(this).remove(); 
          })}, 4000);
        }
      );
      return true;
    });


    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
      $('.selectpicker').selectpicker('mobile');
    }
    else {
      $('.selectpicker').selectpicker();
    }

    $("small").each(function() {
      var date = $(this).text();
      $(this).html(moment(date, "YYYY-MM-DD H:mm:ss").fromNow());
    });
  });
  </script>

  <div id="flash"></div>

  <div >
  <div class="row">
    <div class="col-sm-12">

      <div class="contact">
        <h2 id="headerName"><?php echo $contact["display_name"]; ?></h2>
        <div class="btn-group">
          <a href="tel:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
            <i class="glyphicon glyphicon-earphone"></i>
            <span><?php echo $contact["phone"]; ?></span>
          </a>
          <a href="sms:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
            <i class="glyphicon glyphicon-comment"></i>
          </a>
          <a href="mailto:<?php echo $contact["email"]; ?>" target="_blank" class="btn btn-default msgAction">
            <i class="glyphicon glyphicon-envelope"></i>
            <span><?php echo $contact["email"]; ?></span>
          </a>
        </div>
      </div>

      <div class="well square">
        <div>
          <h4 class="pull-left"><i class="glyphicon glyphicon-user"></i> Contact Info</h4>
          <a id="editInfo" class="btn btn-default pull-right">Edit</a>
          <div id="editInfoGroup" class=" btn-group pull-right">
            <a id="cancelInfo" class="btn btn-default">Cancel</a>
            <a id="submitInfo" class="btn btn-success">Submit</a>
          </div>
        </div>
        <form class="form-horizontal" id="editForm" role="form" action="/discover/index.php" method="post">
        <table class="table table-bordered table-striped">
          <tbody>
            <?php 
              $levels = array(0 => "Unknown", 1 => "Know and trust a christian", 2 => "Become curious", 3 => "Become open to Change",
                4 => "Seek God", 5 => "Make a decision", 6 => "Grow in relationship with God");
              $international = "Not found"; $engagement = "Unknown"; $nextsteps = "";
              foreach ($contact["api.CustomValue.get"]["values"] as $key => $value) {
                if($value["id"] === "61"){
                  $international = ($value["latest"] ? ucfirst($value["latest"]) : "No" );
                }
                if($value["id"] === "124"){
                  $engagement = ($value["latest"] ? $levels[$value["latest"]] : "Unknown" );
                }
                if($value["id"] === "145"){
                  $nextsteps = ($value["latest"] ? $value["latest"] : "" );
                }
              }
              foreach($relationships as $key => $values){
                if($values["is_active"] == 1 && $values["relationship_type_id"] == 10){
                  $campusRel = $values;
                }
              }
              ?>
            <tr class="infoEdit">
              <td>First Name</td>
              <td class="form-group">
                  <input type="text" class="form-control" id="inputFirst" name="inputFirst" placeholder="First Name" value="<?php echo $contact['first_name'];?>">
              </td>
            </tr>
            <tr class="infoEdit">
              <td>Last Name</td>
              <td class="form-group">
                  <input type="text" class="form-control" id="inputLast" name="inputLast" placeholder="Last Name" value="<?php echo $contact['last_name'];?>">
              </td>
            </tr>
            <tr>
              <td>Campus</td>
              <td class="infoDisplay"><?php echo $campusRel["display_name"]; ?></td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectCampus" name="selectCampus">
                  <option selected="selected" disabled="disabled" value="0">Choose Campus</option>
                  <?php
                    $schools = getSchools();
                    foreach($schools as $id => $label){
                      $selected = ($campusRel["contact_id_b"] == $id ? "selected" : "");
                        echo "<option value=\"" . $id . "\" " . $selected . ">" . $label . "</option>";
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Gender</td><td class="infoDisplay"><?php echo $contact["gender"]; ?></td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectGender" name="selectGender">
                  <option value="2" <?php echo ($contact["gender_id"] == 2 ? "selected" : "");?>>Male</option>
                  <option value="1" <?php echo ($contact["gender_id"] == 1 ? "selected" : "");?>>Female</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Email</td><td class="infoDisplay"><?php echo $contact["email"]; ?></td>
              <td class="infoEdit form-group">
                <input type="text" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" value="<?php echo $contact['email'];?>">
              </td>
            </tr>
            <tr>
              <td>Phone</td><td class="infoDisplay"><?php echo $contact["phone"]; ?></td>
              <td class="infoEdit form-group">
                <input type="text" class="form-control" id="inputPhone" name="inputPhone" placeholder="Phone" value="<?php echo $contact['phone'];?>">
              </td>
            </tr>
            <tr>
              <td>International</td><td class="infoDisplay"><?php echo $international; ?></td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectInter" name="selectInter">
                  <option value="yes" <?php echo ($international == "Yes" ? "selected" : "");?>>Yes</option>
                  <option value="no" <?php echo ($international == "No" ? "selected" : "");?>>No</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Engagement Level</td><td class="infoDisplay"><?php echo $engagement; ?></td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectLevel" name="selectLevel">
                  <?php
                    foreach ($levels as $id => $label) {
                      $selected = ($label == $engagement ? "selected" : "");
                      echo "<option value=\"" . $id . "\" " . $selected . ">" . $label . "</option>";
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Next Steps</td><td class="infoDisplay"><?php echo $nextsteps; ?></td>
              <td class="infoEdit form-group">
                <textarea class="form-control" id="inputNext" name="inputNext" rows="3" placeholder="Optional"><?php echo $nextsteps; ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" id="inputCID" name="inputCID" value="<?php echo $contactID; ?>">
        <input type="hidden" id="phoneID" name="phoneID" value="<?php echo $contact['phone_id']; ?>">
        <input type="hidden" id="emailID" name="emailID" value="<?php echo $contact['email_id']; ?>">
        <input type="hidden" id="currentCampus" name="currentCampus" value="<?php echo $campusRel['contact_id_b']; ?>">
        <input type="hidden" id="relationshipID" name="relationshipID" value="<?php echo $campusRel['id']; ?>">
        </form>
      </div>

      <div id="activities" class="well square">
        <div class="pull-right">
          <div class="btn-group">
            <a id="rejModalBtn" data-toggle="modal" href="#rejoiceModal" class="btn btn-primary">
              <i class="glyphicon glyphicon-certificate"></i>
              <span>Add Rejoiceable</span>
            </a>
            <a id="noteModalBtn" data-toggle="modal" href="#noteModal" class="btn btn-warning">
              <i class="glyphicon glyphicon-pencil"></i>
              <span>Add Note</span>
            </a>
          </div>
        </div>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#history">
            <i class="glyphicon glyphicon-th-list"></i>
            <span>History</span>
          </a></li>
          <li><a data-toggle="tab" href="#notes">
            <i class="glyphicon glyphicon-pencil"></i>
            <span>Notes</span>
          </a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="history">
            <table id="activityTable" class="table table-bordered table-striped">
              <tbody>
                <?php
                  $rejoiceables = array(1 => "Interaction", 2 => "Spiritual Conversation", 3 => "Gospel Presentation",
                    4 => "Indicated Decision", 5 => "Shared Spirit-Filled Life");
                  foreach ($activities as $key => $activity) {
                    $title = "<i class='glyphicon glyphicon-ok'></i> " . $activity["activity_name"];
                    $details = $activity["details"];
                    if($activity["activity_type_id"] == 32){
                      $title = "<i class='glyphicon glyphicon-pencil'></i> Survey " . $activity["subject"];
                      $details = $activity["status"];
                    }
                    elseif($activity["activity_type_id"] == 47){
                      $title = "<i class='glyphicon glyphicon-certificate'></i> Rejoiceable | " . $rejoiceables[$activity["custom_143"]];
                      if($activity["custom_143"] == 4){
                        $details = $activity["custom_171"] . " (" . $method[$activity["custom_163"]] . ") " . $activity["details"];
                      }
                    }
                    elseif($activity["activity_type_id"] == 2){
                      $title = "<i class='glyphicon glyphicon-earphone'></i> " . $activity["activity_name"];
                    }
                    elseif($activity["activity_type_id"] == 3){
                      $title = "<i class='glyphicon glyphicon-envelope'></i> " . $activity["activity_name"];
                    }
                    elseif($activity["activity_type_id"] == 4){
                      $title = "<i class='glyphicon glyphicon-comment'></i> " . $activity["activity_name"];
                    }
                    echo "<tr><td><strong>" . $title . "</strong>";
                    echo "<small class='pull-right'>" . $activity["activity_date_time"] . "</small><br>";
                    echo "<span>" . $details . "</span></td></tr>\n";
                  }
                ?>
              </tbody>
            </table>
          </div>
          <div class="tab-pane" id="notes">
            <table id="noteTable" class="table table-bordered table-striped">
              <tbody>
                <?php
                  foreach ($notes as $key => $note) {
                    echo "<tr><td><strong><i class='glyphicon glyphicon-pencil'></i> " . $note["subject"] . "</strong>";
                    echo "<small class='pull-right'>" . $note["modified_date"] . "</small><br>";
                    echo "<span>" . $note["note"] . "</span></td></tr>\n";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php print_r(get_contact(array("id" => $contactID))); ?>
      <?php print_r($conValues); ?>
      <br><?php $conReturn; ?>
      <br><?php print_r($sends); ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

    <!-- Rejoiceable Modal -->
    <div class="modal" id="rejoiceModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Add Rejoiceable</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" id="rejForm" role="form" action="" method="post">
              <div class="form-group">
                <label rel="tooltip" title="What type of rejoiceable" for="inputType" class="col-lg-3 control-label">Type</label>
                <div class="col-lg-9">
                  <select class="selectpicker" data-width="100%" id="inputType" name="inputType">
                    <?php
                      foreach($rejType as $id => $label){
                        echo "<option value=\"" . $id . "\">" . $label . "</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div id="decisionForm">
                <div class="form-group">
                  <label for="inputWitness" class="col-lg-3 control-label">Witnesses</label>
                  <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputWitness" name="inputWitness" placeholder="Names" value="<?php echo $user["firstName"] . " " . $user["lastName"]; ?>">
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
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inputID" name="inputID" value="<?php echo $civicrm_id; ?>">
            <input type="hidden" id="inputCID" name="inputCID" value="<?php echo $contactID; ?>">
            <input type="hidden" id="inputCampus" name="inputCampus" value="<?php echo $contact["api.Relationship.get"]["values"][0]["contact_id_b"]; ?>">
            <button type="submit" class="btn btn-success">Submit</button>
            </form>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Note Modal -->
    <div class="modal" id="noteModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Add Note</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" id="noteForm" role="form" action="" method="post">
              <div class="form-group">
                <label for="inputSubject" class="col-lg-3 control-label">Subject</label>
                <div class="col-lg-9">
                  <input type="text" class="form-control" id="inputSubject" name="inputSubject" placeholder="">
                </div>
              </div>
              <div class="form-group">
                <label for="inputNote" class="col-lg-3 control-label">Story</label>
                <div class="col-lg-9">
                  <textarea class="form-control" id="inputNote" name="inputNote" rows="3" placeholder=""></textarea>
                </div>
              </div>
          <div class="modal-footer">
            <input type="hidden" id="inputID" name="inputID" value="<?php echo $civicrm_id; ?>">
            <input type="hidden" id="inputCID" name="inputCID" value="<?php echo $contactID; ?>">
            <button type="submit" class="btn btn-success">Submit</button>
            </form>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

  </body>
</html>
