<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $access = STUDENT_VIS; //Student permissions
  $title = "National Overview";
  $thisFile = "/insights/map/";
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", $title => $thisFile);
  $overviewActive = "active";

  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>
    <link rel="stylesheet" type="text/css" href="/css/jquery-jvectormap-1.2.2.css">
    <script src="/js/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="/js/jquery-jvectormap-ca-lcc-en.js"></script>

    <?php if(userAccess(MIN_VIS)){ ?>
    <div class="col-md-9">
    <?php } else { ?>
    <div class="col-md-12">
      <?php
      }
        date_default_timezone_set('America/Toronto');   
        $dates = array();
        if(date("Y-m-d") < date("Y-09-01")){
          $dates["start"] = date('Y', strtotime('-1 years'));
          $dates["end"] = date('Y');
        }
        else {
          $dates["start"] = date('Y');
          $dates["end"] = date('Y', strtotime('+1 years'));
        }

        $bigPicture = getDecBigPicture(array());
        $newBelievers = 0;
        foreach($bigPicture as $row){
          $newBelievers += intval($row["TOTAL"]);
        }
        $activeContacts = getActiveDiscover();
      ?>

      <div class="text-center"><h2>Indicated Decisions <?php echo $dates["start"] . "-" . $dates["end"]; ?>: 
        <?php echo $newBelievers; ?> New Believers!</h2></div>
      <div id="map" style="height:510px; width:auto"></div>
      <script>
        $.getJSON('/insights/ajax/idmap.php', function(data){

          $('#map').vectorMap({
            map: 'ca_lcc_en',
            focusOn: {x: 0.5, y: 1, scale: 1.7},
            regionStyle: {initial: {fill: '#428bca'}},
            markerStyle: {
              initial: {
                fill: '#F8E23B',
                stroke: '#383f47'
              }
            },
            markers: data.coords,
            series: {
              markers: [{
                attribute: 'r',
                scale: [4, 10],
                normalizeFunction: 'polynomial',
                values: data.counts,
              }]
            },
            onMarkerLabelShow: function(event, label, index){
              label.html(
                ''+data.names[index]+'<br>'+
                'Indicated Decisions: '+data.counts[index]
              );
            },
            onRegionLabelShow: function(event, label, code){
              event.preventDefault();
            }
          });
        });
      </script>
      <div class="text-center"><h2>Active Discover Contacts: <?php echo $activeContacts; ?> Students Journeying</h2></div>

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>