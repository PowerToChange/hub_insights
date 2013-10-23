<?php
  global $civicrm_id;
  include 'login.php';
  include 'dbcalls.php';

  $title = "Movement Snapshot - By Campus";
  $thisFile = "monbycampus.php";
  $msBCActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4, 5, 6, 7 ] }],\n";
  include 'header.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th></th>
            <th colspan="2">Exposures</th>
            <th colspan="2">Engagements</th>
            <th colspan="3">Involvement Thresholds</th>
          </tr>
          <tr>
            <th>Date</th>
            <th>Surveys</th>
            <th>Non-Christian Event Attendance</th>
            <th>Survey Results</th>
            <th>Unrecorded</th>
            <th>Growing</th>
            <th>Ministering</th>
            <th>Multiplying</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $byCampus = getMSByCampus($_POST);

            $surveyTotal = 0; $eventTotal = 0; $resultTotal = 0; $unrecTotal = 0;
            foreach($byCampus as $row){
              $surveyTotal += intval($row["SURVEY"]);
              $eventTotal += intval($row["EVENT"]);
              $resultTotal += intval($row["RESULT"]);
              $unrecTotal += intval($row["UNREC"]);
            }

            foreach($byCampus as $date => $info){
              echo "<tr><td>" . $date . "</td>";
              echo "<td>" . ($info["SURVEY"] ?: 0) . "</td>";
              echo "<td>" . ($info["EVENT"] ?: 0) . "</td>";
              echo "<td>" . ($info["RESULT"] ?: 0) . "</td>";
              echo "<td>" . ($info["UNREC"] ?: 0) . "</td>";
              echo "<td>" . ($info["GROW"] ?: 0) . "</td>";
              echo "<td>" . ($info["MIN"] ?: 0) . "</td>";
              echo "<td>" . ($info["MULT"] ?: 0) . "</td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $surveyTotal . "</th>";
              echo "<th>" . $eventTotal . "</th>";
              echo "<th>" . $resultTotal . "</th>";
              echo "<th>" . $unrecTotal . "</th>";
            ?>
            <th>N/A</th>
            <th>N/A</th>
            <th>N/A</th>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php include 'footer.php'; ?>

  </body>
</html>
