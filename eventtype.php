<?php
  global $civicrm_id;
  include 'login.php';
  include 'dbcalls.php';

  $eventType = array("1" => "Campus Wide Outreach", "2" => "Weekly Meeting Outreach", 
    "3" => "MDA Outreach", "4" => "Online Outreach", "10" => "Other", "11" => "Legacy Pulse Outreach");

  $title = "Event Stats - By Type";
  $thisFile = "eventtype.php";
  $evTypeActive = "active";
  $tableConfig = "'aaSorting': [],\n";
  $tableSorting = "'aoColumns': [null,{'sType':'numeric', 'asSorting':['desc','asc']},{'sType':'numeric', 'asSorting':['desc','asc']}],\n";
  include 'header.php';
?>

    <div class="col-md-9" >
      <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered">
        <thead>
          <tr>
            <th>Event Type</th>
            <th>Total number of Attendees</th>
            <th>Number of Non-Christian Atendees</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $byType = getEventsByType($_POST);
            $tTotal = 0; $nTotal = 0;
            foreach($byType as $row){
              $tTotal += intval($row["TOTAL"]);
              $nTotal += intval($row["NONCHRISTIAN"]);
            }

            foreach($eventType as $id => $label){
              echo "<tr><td>" . $label . "</td>";
              echo "<td>" . ($byType[$id]["TOTAL"] ?: 0) . "</td>";
              echo "<td>" . ($byType[$id]["NONCHRISTIAN"] ?: 0) . "</td>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <?php
              echo "<th>Totals</th>";
              echo "<th>" . $tTotal . "</th>";
              echo "<th>" . $nTotal . "</th>";
            ?>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php include 'footer.php'; ?>

  </body>
</html>
