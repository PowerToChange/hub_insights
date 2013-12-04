<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';
  date_default_timezone_set('America/Toronto');

  $title = "Movement Snapshot - Monthly Breakdown";
  $thisFile = "/insights/monthlystats/bycampus/";
  $activeInsights = "class='active'";
  $msBCActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0, 1, 2, 3, 4, 5, 6, 7 ] },
    {'iDataSort':8, 'aTargets':[0]}, {'bVisible':false, 'aTargets': [ 8 ]}],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
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
            <th>Surveys Collected</th>
            <th>Non-Christian Event Attendance</th>
            <th rel="tooltip" title="# who desired further engagement after survey">Positive Survey Results</th>
            <th rel="tooltip" title="# who engaged with us, but we don't know their name">Unrecorded</th>
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
              echo "<tr><td>" . date("M Y", strtotime($date)) . "</td>";
              echo "<td>" . ($info["SURVEY"] ?: 0) . "</td>";
              echo "<td>" . ($info["EVENT"] ?: 0) . "</td>";
              echo "<td>" . ($info["RESULT"] ?: 0) . "</td>";
              echo "<td>" . ($info["UNREC"] ?: 0) . "</td>";
              echo "<td>" . ($info["GROW"] ?: 0) . "</td>";
              echo "<td>" . ($info["MIN"] ?: 0) . "</td>";
              echo "<td>" . ($info["MULT"] ?: 0) . "</td>";
              echo "<td>" . strtotime($date) . "</td></tr>";
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
        <p><strong>Exposures:</strong></p>
        <p>The number of people exposed to some sort of spiritual content with an opportunity to begin or continue engagement.</p>
        <p><strong>Engagements:</strong></p>
        <p>A person who begins, or continues, to be involved in gospel-themed conversations with someone from P2C,
          either face-to-face or digitally. (ex. we know their name, something about them and can contact them again).
          For online conversations, a person could be counted as an engagement if they are interacting (not just a random comment)
          and the conversation is gospel centred.</p>
        <p><i>Note:</i> Discover Tool Engagements will soon be summarized in this report.</p>
      </div>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
