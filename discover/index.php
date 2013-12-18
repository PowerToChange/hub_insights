<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $method = array("1" => "Random Evangelism", "2" => "Friendship Evangelism", 
    "3" => "MDA Outreach", "4" => "Campus Wide Event", 
    "5" => "Investigative Bible Study", "6" => "Leadership Luncheon", 
    "7" => "Weekly Meeting Follow-up", "8" => "SIQ Follow-up", 
    "9" => "Internet Evangelism", "10" => "Jesus Video", "11" => "Other");

  $title = "Discover Tab";
  $thisFile = "/discover/";
  $activeDiscover = "class='active'";

  $contact_info = get_contact(array("id" => 60088));
  $contact = $contact_info["values"]["60088"];
  $relationships = $contact_info["values"]["60088"]["api.Relationship.get"]["values"];
  $notes = $contact_info["values"]["60088"]["api.Note.get"]["values"];
  $activities = $contact_info["values"]["60088"]["api.Activity.get"]["values"];


  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>

  <script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({  
      cache: false  
    });

    $("small").each(function() {
      var date = $(this).text();
      $(this).html(moment(date, "YYYY-MM-DD").fromNow());
    });
  });
  </script>

  <div >
  <div class="row">
    <div class="col-sm-12">
      <div class="contact">
        <h2><?php echo $contact["display_name"]; ?></h2>
        <div class="btn-group">
          <a href="tel:<?php echo $contact["phone"]; ?>" class="btn btn-default">
            <i class="glyphicon glyphicon-earphone"></i>
            <span><?php echo $contact["phone"]; ?></span>
          </a>
          <a href="sms:<?php echo $contact["phone"]; ?>" class="btn btn-default">
            <i class="glyphicon glyphicon-comment"></i>
          </a>
          <a href="mailto:<?php echo $contact["email"]; ?>" class="btn btn-default">
            <i class="glyphicon glyphicon-envelope"></i>
            <span><?php echo $contact["email"]; ?></span>
          </a>
        </div>
      </div>
      <div class="well square">
        <div>
          <h4 class="pull-left"><i class="glyphicon glyphicon-user"></i> Contact Info</h4>
          <a class="btn btn-default pull-right">Edit</a>
        </div>
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
              $info = array("Campus" => $contact["api.Relationship.get"]["values"][0]["display_name"],
                "Gender" => $contact["gender"], "Email" => $contact["email"], "Phone" => $contact["phone"],
                "International" => $international, "Engagement Level" => $engagement, "Next Steps" => $nextsteps);
              foreach ($info as $label => $value) {
                echo "<tr><td>$label</td><td>$value</td></tr>\n";
              }
            ?>
          </tbody>
        </table>
      </div>

      <div id="activities" class="well square">
        <div class="pull-right">
          <div class="btn-group">
            <a href="#" class="btn btn-primary">
              <i class="glyphicon glyphicon-certificate"></i>
              <span>Add Rejoiceable</span>
            </a>
            <a href="#" class="btn btn-warning">
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
            <table class="table table-bordered table-striped">
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
            <table class="table table-bordered table-striped">
              <tbody>
                <?php
                  foreach ($notes as $key => $note) {
                    echo "<tr><td><strong><i class='glyphicon glyphicon-pencil'></i> " . $note["subject"] . "</strong>";
                    echo "<small class='pull-right'>" . $note["modified_date"] . "</small><br>";
                    echo "<span>" . $note["note"] . "</span></td></tr>\n";
                  }
                ?>
                <!--<tr><td><img class="img-responsive centre" src="/images/loading.gif"></td></tr>-->
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php //print_r(get_contact(array("id" => 60088))); ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
