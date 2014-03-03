<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $title = "Surveys - National Follow-Up";
  $thisFile = "/insights/survey/natfollowup/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $surveyOptions = true;
  $surNatFollowActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <div class="col-md-9" >
      <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Notice:</strong> Campus selection has no impact on Big Picture Results
      </div>

      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Campus</th>
            <th>Uncontacted</th>
            <th>In Progress</th>
            <th>Completed</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $report = getNationalFollowup($_POST);
            $campuses = getSchools();

            $sumUn = 0; $sumIn = 0; $sumCom = 0; $sumTotal = 0;
            foreach($report as $row){
              $sumUn += intval($row["UNCONTACTED"]); $sumIn += intval($row["IN PROGRESS"]); 
              $sumCom += intval($row["COMPLETED"]); $sumTotal += intval($row["TOTAL"]);
            }

            foreach($campuses as $id => $label){
              echo "<tr><td>" . $label . "</td>";
              echo "<td>" . ($report[$id]["UNCONTACTED"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["IN PROGRESS"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["COMPLETED"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["TOTAL"] ?: 0) . "</td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $sumUn . "</th>";
              echo "<th>" . $sumIn . "</th>";
              echo "<th>" . $sumCom . "</th>";
              echo "<th>" . $sumTotal . "</th>";
            ?>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
