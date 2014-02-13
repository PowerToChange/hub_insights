<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';
  date_default_timezone_set('America/Toronto');

  $title = "Discover Contacts - By Person";
  $thisFile = "/insights/discover/byperson/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $dcPersonActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'desc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 0, 1, 2, 3 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Person</th>
            <th>New Relationships</th>
            <th>Rejoiceables</th>
            <th rel="tooltip" title="# of calls, emails and texts">Interactions</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $byPerson = getDCByPerson($_POST);

            $newTotal = 0; $rejTotal = 0; $intTotal = 0;
            foreach($byPerson as $row){
              $newTotal += intval($row["NEW"]);
              $rejTotal += intval($row["REJOICEABLES"]);
              $intTotal += intval($row["INTERACTIONS"]);
            }

            foreach($byPerson as $date => $info){
              echo "<tr><td>" . date("M Y", strtotime($date)) . "</td>";
              echo "<td>" . ($info["NEW"] ?: 0) . "</td>";
              echo "<td>" . ($info["REJOICEABLES"] ?: 0) . "</td>";
              echo "<td>" . ($info["INTERACTIONS"] ?: 0) . "</td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $newTotal . "</th>";
              echo "<th>" . $rejTotal . "</th>";
              echo "<th>" . $intTotal . "</th>";
            ?>
          </tr>
        </tfoot>
      </table>

      <div class="well well-sm">
        <h3>Help</h3>
        <p><strong>New Relationships:</strong></p>
        <p>The number of blah</p>
      </div>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
