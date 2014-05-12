<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $title = "Indicated Decisions - Big Picture";
  $thisFile = "/insights/decisions/bigpicture/";
  $activeInsights = "active";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $idBPActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  $tableSorting = "'aoColumns': [null,{'sType':'numeric', 'asSorting':['desc','asc']},
    {'sType':'numeric', 'asSorting':['desc','asc']},{'sType':'numeric', 'asSorting':['desc','asc']}],\n";
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
            <th>Number of Decisions</th>
            <th rel="tooltip" title="The number that got involved with P2C after making a decision">Number Integrated with P2C</th>
            <th rel="tooltip" title="The number that got involved with another Christian community">Number Integrated Elsewhere</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $bigPicture = getDecBigPicture($_POST);
            $campuses = getSchools();

            $total = 0; $p2cTotal = 0; $otherTotal = 0;
            foreach($bigPicture as $row){
              $total += intval($row["TOTAL"]);
              $p2cTotal += intval($row["P2C"]);
              $otherTotal += intval($row["OTHER"]);
            }

            foreach($campuses as $id => $label){
              echo "<tr><td>" . $label . "</td>";
              echo "<td>" . ($bigPicture[$id]["TOTAL"] ?: 0) . "</td>";
              echo "<td>" . ($bigPicture[$id]["P2C"] ?: 0) . "</td>";
              echo "<td>" . ($bigPicture[$id]["OTHER"] ?: 0) . "</td></tr>";
            }
            echo "<tr><td>No Campus Set</td>";
            echo "<td>" . ($bigPicture[null]["TOTAL"] ?: 0) . "</td>";
            echo "<td>" . ($bigPicture[null]["P2C"] ?: 0) . "</td>";
            echo "<td>" . ($bigPicture[null]["OTHER"] ?: 0) . "</td></tr>";
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $total . "</th>";
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
