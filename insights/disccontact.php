<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';
  date_default_timezone_set('America/Toronto');

  $discAllCampuses = !(isset($_POST["selectCampus"]) ? $_POST["selectCampus"] : isset($_COOKIE["campus"]));

  $title = "Discover Contacts - Contact Summary";
  $thisFile = "/insights/discover/contact/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $dcContactActive = "active";
  $tableConfig = "'aaSorting': [[ 1, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0, 1, 2, 3, 4 ] }],\n";
  if($discAllCampuses){ $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0, 1, 2, 3, 4, 5 ] }],\n"; }
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Name</th>
            <th>Last Action</th>
            <th>Threshold</th>
            <th>Volunteer</th>
            <th>Next Step</th>
            <?php if($discAllCampuses){ echo "<th>Campus</th>\n"; } ?>
          </tr>
        </thead>
        <tbody>
          <?php
            $contacts = getDCContacts($_POST);

            $thresholdLabels = array(0 => "Unknown", 1 => "Know and trust a Christian", 2 => "Become curious", 
              3 => "Become open to change", 4 => "Seek God", 5 => "Make a decision", 6 => "Grow in relationship with God");

            foreach($contacts as $id => $values){
              echo "<tr><td>" . $values["NAME"] . "</td>";
              echo "<td>" . date('Y-m-d', strtotime($values["DATE"])) . "</td>";
              echo "<td>" . $thresholdLabels[$values["THRESHOLD"]] . "</td>";
              echo "<td>" . $values["DISCOVER"] . "</td>";
              echo "<td>" . $values["NEXTSTEP"] . "</td>";
              if($discAllCampuses){ echo "<td>" . $values["SCHOOLS"] . "</td>"; }
              echo "</tr>";
            }
          ?>
        </tbody>
      </table>

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
