<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $title = "Surveys - National Priority";
  $thisFile = "/insights/survey/natpriority/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $surveyOptions = true;
  $surNatPriActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4, 5, 6 ] }],\n";
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
            <th>Hot</th>
            <th>Medium</th>
            <th>Mild</th>
            <th>Not Interested</th>
            <th>N/A</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $report = getNationalPriority($_POST);
            $campuses = getSchools();

            $sumHot = 0; $sumMedium = 0; $sumMild = 0; $sumNot = 0; $sumNA = 0; $sumTotal = 0;
            foreach($report as $row){
              $sumHot += intval($row["HOT"]); $sumMedium += intval($row["MEDIUM"]); 
              $sumMild += intval($row["MILD"]); $sumNot += intval($row["NOT"]); 
              $sumNA += intval($row["NA"]); $sumTotal += intval($row["TOTAL"]);
            }

            foreach($campuses as $id => $label){
              echo "<tr><td>" . $label . "</td>";
              echo "<td>" . ($report[$id]["HOT"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["MEDIUM"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["MILD"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["NOT"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["NA"] ?: 0) . "</td>";
              echo "<td>" . ($report[$id]["TOTAL"] ?: 0) . "</td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $sumHot . "</th>";
              echo "<th>" . $sumMild . "</th>";
              echo "<th>" . $sumMild . "</th>";
              echo "<th>" . $sumNot . "</th>";
              echo "<th>" . $sumNA . "</th>";
              echo "<th>" . $sumTotal . "</th>";
            ?>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
