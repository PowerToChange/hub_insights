<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $title = "Discover Contacts";
  $activeDiscover = "class='active'";

  $contacts = all_contacts();

  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>

  <script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({  
      cache: false  
    });

    $(".msgAction").click(function(event){
      event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
      var type = "2";
      var glyphicon = "glyphicon-earphone";
      var label = "Phone Call";
      if($(this).attr("href").indexOf("mailto") >= 0){
        type = "3";
        glyphicon = "glyphicon-envelope";
        label = "Email";
      }
      else if($(this).attr("href").indexOf("sms") >= 0){
        type = "4";
        glyphicon = "glyphicon-comment";
        label = "Text/SMS";
      }
      var msgID = "<?php echo $civicrm_id; ?>";
      var msgCID = $(this).parent().prev(".contactID").html();
      var name = "<?php echo $user['firstName'] . ' ' . $user['lastName']; ?>";
      label = $(this).parent().siblings("h3").html() + " " + label
      $.getJSON(
        "/discover/ajax/submitmsg.php", 
        "type="+type+"&msgID="+msgID+"&msgCID="+msgCID+"&name="+encodeURIComponent(name),  
        function(json){
          var alert = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' " +
            "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error!</strong> " + json.result + "</div>";
          if(json.result == 1){
            var alert = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' " +
              "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Success!</strong> " + label + " Added</div>";
          }
          $("#flash").html(alert);
          window.setTimeout(function() { 
            $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
            $(this).remove(); 
          })}, 4000);
        }
      );
      return true;
    });

    $(".contactLink").click(function(){
      var id = $(this).find("span.contactID").html();
      window.location = "/discover/contact/"+id+"/";
    });

    $("#addContact").click(function(){
      window.location = "/discover/new/";
    });


    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
      $('.selectpicker').selectpicker('mobile');
    }
    else {
      $('.selectpicker').selectpicker();
    }

    $("#inactiveContacts").click(function(){
      $("#inactiveSymbol").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
    });

    $("small").each(function() {
      var date = $(this).text();
      $(this).html(moment(date, "YYYY-MM-DD H:mm:ss").fromNow());
    });
  });
  </script>

  <div id="flash"></div>

  <div>
  <div class="row">
    <div class="col-sm-12">

    <?php
      if($contacts){
        $currentCampus = 0; $stillActive = 1; $first = 1;
        foreach($contacts as $key => $contact){
          if($contact["is_active"] == 0 && $stillActive == 1){
            if(!$first){
              echo "</div></div>";
            }
            $stillActive = 0; $currentCampus = 0; $first = 1;
            ?>
            <div id="addContact" class="btn-success fullWidth">
              <i class='glyphicon glyphicon-plus'></i> Add Contact
            </div>
            <br>
              <div id="inactiveContacts" data-toggle="collapse" data-target="#inactive" class="greyBack">
                <i id='inactiveSymbol' class='glyphicon glyphicon-chevron-right'></i> Inactive Contacts
              </div>
  
              <div id="inactive" class="collapse">
            <?php
          }
          if($contact["school_id"] != $currentCampus){
            if($first == 0){
              echo "</div></div>";
            }
            $currentCampus = $contact["school_id"];
            ?>
              <div class="panel panel-default schoolContacts">
                  <!-- Default panel contents -->
                <div class="panel-heading"><h4><?php echo $contact["school_name"]; ?></h4></div>
                <div class="list-group">
                  <div class="list-group-item contactLink row">
                    <div class="contactInfo pull-left">
                      <h3 class="list-group-item-heading"><?php echo $contact["name"]; ?></h3>
                      <span class="contactID hidden"><?php echo $contact["id"]; ?></span>
                      <div class="btn-group contactBtns">
                        <?php if(isset($contact["phone"]) && $contact["phone"]){ ?>
                        <a href="tel:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-earphone"></i>
                          <span><?php echo $contact["phone"]; ?></span>
                        </a>
                        <a href="sms:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-comment"></i>
                        </a>
                        <?php } if(isset($contact["email"]) && $contact["email"]){ ?>
                        <a href="mailto:<?php echo $contact["email"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-envelope"></i>
                          <span><?php echo $contact["email"]; ?></span>
                        </a>
                        <?php } ?>
                      </div>
                    </div>
                    <i class='glyphicon glyphicon-chevron-right pull-right contactArrow'></i>
                  </div>
            <?php
          }
          else {
            ?>
                  <div class="list-group-item contactLink row">
                    <div class="contactInfo pull-left">
                      <h3 class="list-group-item-heading"><?php echo $contact["name"]; ?></h3>
                      <span class="contactID hidden"><?php echo $contact["id"]; ?></span>
                      <div class="btn-group contactBtns">
                        <?php if(isset($contact["phone"]) && $contact["phone"]){ ?>
                        <a href="tel:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-earphone"></i>
                          <span><?php echo $contact["phone"]; ?></span>
                        </a>
                        <a href="sms:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-comment"></i>
                        </a>
                        <?php } if(isset($contact["email"]) && $contact["email"]){ ?>
                        <a href="mailto:<?php echo $contact["email"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-envelope"></i>
                          <span><?php echo $contact["email"]; ?></span>
                        </a>
                        <?php } ?>
                      </div>
                    </div>
                    <i class='glyphicon glyphicon-chevron-right pull-right contactArrow'></i>
                  </div>
            <?php
          }
          if($first == 1){
            $first = 0;
          }
        }
        if($stillActive){
          ?>
            </div></div>
            <div id="addContact" class="btn-success fullWidth">
              <i class='glyphicon glyphicon-plus'></i> Add Contact
            </div>
          <?php
        }else {
          echo "</div></div></div>";
        }
      }
      else {
        ?>
          <div id="addContact" class="btn-success fullWidth">
            <i class='glyphicon glyphicon-plus'></i> Add Contact
          </div>
        <?php
      }
    ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
  </body>
</html>
