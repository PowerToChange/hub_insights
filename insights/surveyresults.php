<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $title = "Surveys - Results and Rejoiceables";
  $thisFile = "/insights/survey/results/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);

  $surveyOptions = true;
  $surResultsActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>

    <div class="col-md-9" >
      <?php
        $highlights = getSurveyHighlights($_POST);
      ?>
      <div class="container-fluid">
        <div class="col-xs-4 panel panel-default panel-body">
          <h3 class="text-center">Interested Contacts</h3>
          <h2 class="text-center"><?php echo $highlights["TOTAL"]; ?></h2>
        </div>
        <div class="col-xs-4 panel panel-default panel-body">
          <h3 class="text-center">Contacts In Motion</h3>
          <h2 class="text-center"><?php echo $highlights["COMPLETED"] + $highlights["IN PROGRESS"]; ?></h2>
        </div>
        <div class="col-xs-4 panel panel-default panel-body">
          <h3 class="text-center">Volunteers Helping</h3>
          <h2 class="text-center"><?php echo $highlights["VOLUNTEERS"]; ?></h2>
        </div>
      </div>
      <div class="clearfix"></div>
      

      <div class="panel panel-default panel-body">
      <h3>Results</h3>
      <h4>Reached out but ...</h4>
      <table class="table table-striped">
        <tbody>
        <?php
          $results = getSurveyResults($_POST);

          $badLabels = array(0 => "Bad Info", 1 => "No Response", 2 => "No Longer Interested");
          $badTotal = 0;
          foreach($badLabels as $id => $label){ 
            if(isset($results[$id])){
              $badTotal += $results[$id];
            }
            echo "<tr><td>" . $label . "</td>";
            echo "<td>" . ($results[$id] ?: 0) . "</td>";
          }
        ?>
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <td><?php print($badTotal); ?></td>
          </tr>
        </tfoot>
      </table>

      <h4>Met and ...</h4>
      <table class="table table-striped">
        <tbody>
        <?php
          $goodLabels = array(5 => "Request Fulfilled Digital", 7 => "Request Fulfilled Face-to-Face",
            8 => "Digital Interaction and Wants to Continue", 10 => "Face-to-face Interaction and Wants to Continue");
          $goodTotal = 0;
          foreach($goodLabels as $id => $label){ 
            if(isset($results[$id])){
              $goodTotal += $results[$id];
            }
            echo "<tr><td>" . $label . "</td>";
            echo "<td>" . ($results[$id] ?: 0) . "</td>";
          }
        ?>
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <td><?php print($goodTotal); ?></td>
          </tr>
        </tfoot>
      </table>
      <h4>No Follow-up Required Total: <?php echo $results[3]; ?></h4>
      </div>

      <div class="panel panel-default panel-body">
      <h3>Rejoiceables</h3>
      <table class="table table-striped">
        <tbody>
        <?php
          $titles = array("","Interaction", "Spiritual Conversation", "Gospel Presentation", "Indicated Decision", "Shared Spirit-Filled Life");
          $rejTotal = 0;
          $rejoiceables = getRejoiceables($_POST);
          for($i = 1; $i <= 5; $i++){ 
            if(isset($rejoiceables[$i])){
              $rejTotal += $rejoiceables[$i];
            }
            echo "<tr><td>" . $titles[$i] . "</td>";
            echo "<td>" . ($rejoiceables[$i] ?: 0) . "</td>";
          }
        ?>
        </tbody>
        <tfoot>
          <tr>
            <th>Total Rejoiceables</th>
            <td><?php print($rejTotal); ?></td>
          </tr>
        </tfoot>
      </table>
      </div>

      <div class="well well-sm">
        <h3>Help</h3>
        <p><strong>National Numbers:</strong></p>
        <p>Interested Contacts: Total number of contacts with priority not set to "Not Interested".
        <br>Contacts In Motion: Total number of interested contacts with status "In Progress" or "Completed".
        <br>Volunteers: Total number of people assigned to follow-up a contact.</p>
      </div>

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
