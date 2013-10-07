<?php
  global $civicrm_id;
  include 'login.php';
  include 'dbcalls.php';

  $title = "Indicated Decisions - Big Picture";
  $thisFile = "idbigpicture.php";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  include 'header.php';
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
              <th>Number Integrated with P2C</th>
              <th>Number Integrated Elsewhere</th>
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
    </div>
  </div>

  </body>
</html>