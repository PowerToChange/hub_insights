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
            <th rel="tooltip" title="# of calls, emails and texts">Connections</th>
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

            foreach($byPerson as $id => $info){
              echo "<tr><td>" . $info["NAME"] . "</td>";
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
        <p>The number of new relationships with contacts that were not already being journeyed with.</p>
        <p><strong>Rejoiceables:</strong></p>
        <p>The number of interactions, spiritual conversations, gospel presentations, indicated decisions and
          spirit-filled life sharings with discover contacts.</p>
        <p><strong>Connections:</strong></p>
        <p>The number of calls, emails, and texts sent out through the Pulse system to discover contacts.</p>
      </div>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
