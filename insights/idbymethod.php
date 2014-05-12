<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $method = array("1" => "Random Evangelism", "2" => "Friendship Evangelism", 
    "3" => "MDA Outreach", "4" => "Campus Wide Event", 
    "5" => "Investigative Bible Study", "6" => "Leadership Luncheon", 
    "7" => "Weekly Meeting Follow-up", "8" => "SIQ Follow-up", 
    "9" => "Internet Evangelism", "10" => "Jesus Video", "11" => "Other");

  $title = "Indicated Decisions - By Method";
  $thisFile = "/insights/decisions/bymethod/";
  $activeInsights = "active";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $idBMActive = "active";
  $tableConfig = "'aaSorting': [],\n'iDisplayLength': 25,\n";
  $tableSorting = "'aoColumns': [null,{'sType':'numeric', 'asSorting':['desc','asc']}, { 'sType': 'percent', 'asSorting':['desc','asc']},
    {'sType':'numeric', 'asSorting':['desc','asc']},{'sType':'numeric', 'asSorting':['desc','asc']}],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th rel="tooltip" title="How this person came to know Christ">Method</th>
            <th>Number of Decisions</th>
            <th>Percentage of Total</th>
            <th rel="tooltip" title="The number that got involved with P2C after making a decision">Number Integrated with P2C</th>
            <th rel="tooltip" title="The number that got involved with another Christian community">Number Integrated Elsewhere</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $byMethod = getDecByMethod($_POST);
            $total = 0; $p2cTotal = 0; $otherTotal = 0;
            foreach($byMethod as $row){
              $total += intval($row["TOTAL"]);
              $p2cTotal += intval($row["P2C"]);
              $otherTotal += intval($row["OTHER"]);
            }

            foreach($method as $id => $label){
              echo "<tr><td>" . $label . "</td>";
              echo "<td>" . ($byMethod[$id]["TOTAL"] ?: 0) . "</td>";
              echo "<td>" . round((($byMethod[$id]["TOTAL"] ?: 0) / $total) * 100) . "%</td>";
              echo "<td>" . ($byMethod[$id]["P2C"] ?: 0) . "</td>";
              echo "<td>" . ($byMethod[$id]["OTHER"] ?: 0) . "</td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $total . "</th>";
              echo "<th>100%</th>";
              echo "<th>" . $p2cTotal . "</th>";
              echo "<th>" . $otherTotal . "</th>";
            ?>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
