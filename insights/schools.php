<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $access = STAFF_VIS;
  $title = "School Report";
  $thisFile = "/insights/schools/";
  $activeInsights = "active";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $schoolActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4, 5, 6, 7 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>School</th>
            <th>Ministry Presence</th>
            <th>SLM Q1</th>
            <th>SLM Q2</th>
            <th>SLM Q3</th>
            <th>Student Led Movement</th>
            <th>Type</th>
            <th>Fundraising</th>
            <th>View</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $schools = get_schools();
            $automated = false;
            foreach($schools["values"] as $id => $values){
              $info = array();
              foreach ($values["api.CustomValue.get"]["values"] as $key => $value) {
                if($value["id"] == 73){ $info["minPres"] = $value["latest"]; }
                else if($value["id"] == 68){ $info["q1"] = $value["latest"]; }
                else if($value["id"] == 69){ $info["q2"] = $value["latest"]; }
                else if($value["id"] == 70){ $info["q3"] = $value["latest"]; }
                else if($value["id"] == 71){ $info["slm"] = $value["latest"]; }
                else if($value["id"] == 72){ $info["type"] = $value["latest"]; }
                else if($value["id"] == 74){ $info["fund"] = $value["latest"]; }
              }
              echo "<tr><td>" . $values["display_name"] . "</td>";
              echo "<td>" . $info["minPres"] . "</td>";
              echo "<td>" . $info["q1"] . "</td>";
              echo "<td>" . $info["q2"] . "</td>";
              echo "<td>" . $info["q3"] . "</td>";
              echo "<td>" . $info["slm"] . "</td>";
              echo "<td>" . $info["type"] . "</td>";
              echo "<td>" . $info["fund"] . "</td>";
              echo "<td><a href=\"/insights/schoolinfo/" . $values["contact_id"] . 
                "\" class=\"btn btn-primary\">View</a></td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
        </tfoot>
      </table>

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
