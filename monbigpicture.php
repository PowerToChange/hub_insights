<?php
  global $civicrm_id;
  include 'login.php';
  include 'dbcalls.php';

  $title = "Movement Snapshot - Big Picture";
  $thisFile = "monbigpicture.php";
  $msBPActive = "active";
  $tableConfig = "'aaSorting': [],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4 ] }],\n";
  include 'header.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th></th>
            <th colspan="2">Exposures</th>
            <th colspan="2">Engagements</th>
          </tr>
          <tr>
            <th>Campus</th>
            <th>Surveys</th>
            <th>Non-Christian Event Attendance</th>
            <th>Survey Results</th>
            <th>Unrecorded</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $bigPicture = getMSBigPicture($_POST);
            $campuses = getSchools();

            $surveyTotal = 0; $eventTotal = 0; 
            $resultTotal = 0; $unrecTotal = 0;
            foreach($bigPicture as $row){
              $surveyTotal += intval($row["SURVEY"]);
              $eventTotal += intval($row["EVENT"]);
              $resultTotal += intval($row["RESULT"]);
              $unrecTotal += intval($row["UNREC"]);
            }

            foreach($campuses as $id => $label){
              echo "<tr><td>" . $label . "</td>";
              echo "<td>" . ($bigPicture[$id]["SURVEY"] ?: 0) . "</td>";
              echo "<td>" . ($bigPicture[$id]["EVENT"] ?: 0) . "</td>";
              echo "<td>" . ($bigPicture[$id]["RESULT"] ?: 0) . "</td>";
              echo "<td>" . ($bigPicture[$id]["UNREC"] ?: 0) . "</td></tr>";
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
          </tr>
        </tfoot>
      </table>
    </div>
  <?php include 'footer.php'; ?>

  </body>
</html>
