<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';

  $permissions["visibility"] = 2; //Student permissions
  $title = "Home";
  $thisFile = "index.php";
  $activeHome = "class='active'";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>
    <link rel="stylesheet" type="text/css" href="/css/jquery-jvectormap-1.2.2.css">
    <script src="/js/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="/js/jquery-jvectormap-ca-lcc-en.js"></script>

    <div class="row">
    <div class="col-md-12">
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
      <div id="map" style="height:510px; width:auto"></div>
      <script>
        $.getJSON('/insights/ajax/idmap.php', function(data){

          $('#map').vectorMap({
            map: 'ca_lcc_en',
            focusOn: {x: 0.5, y: 1, scale: 2},
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

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>