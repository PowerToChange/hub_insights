<?php
  global $civicrm_id;
  include 'login.php';
  include 'dbcalls.php';

  $title = "P2C-S Insights";
  $thisFile = "index.php";
  include 'header.php';
?>
    <link rel="stylesheet" type="text/css" href="css/jquery-jvectormap-1.2.2.css">
    <script src="js/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="js/jquery-jvectormap-ca-lcc-en.js"></script>

    <div class="col-md-9">
      <?php
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
      ?>

      <div class="text-center"><h2>Indicated Decisions <?php echo $dates["start"] . "-" . $dates["end"]; ?>: 
        <?php echo $newBelievers; ?> New Believers!</h2></div>
      <div id="map" style="height:510px; width:850px"></div>
      <script>
        $.getJSON('ajax/idmap.php', function(data){

          $('#map').vectorMap({
            map: 'ca_lcc_en',
            focusOn: {x: 0.5, y: 1, scale: 1.6},
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

    </div>
  <?php include 'footer.php'; ?>

  </body>
</html>