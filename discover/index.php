<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $access = STUDENT_VIS; //Student permissions
  $title = "Discover Contacts";
  $activeDiscover = "active";
  $crumbs = array("Home" => "/", "Discover" => "/discover/");

  $contacts = all_contacts($civicrm_id, 1);

  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>

  <script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({  
      cache: false  
    });

    $("#wrap").on("click", ".msgAction", function(event){
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

    $("#wrap").on("click", ".inactiveBtn", function(event){
      event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
      var inactiveBtn = $(this);
      $(this).closest(".contactLink").remove();
      $.getJSON(
        "/discover/ajax/changestatus.php",
        "inputActive="+$(this).data("active")+"&inputRelID="+$(this).data("relid"),
        function(json){
          var alert = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' " +
            "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error!</strong> " + json.result + "</div>";
          if(json.result == 1){
            var label = " inactive.";
            if(json.active == 0){
               label = " active. Reload the page to see changes.";
            }
            var alert = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' " +
              "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Success!</strong> " + 
              inactiveBtn.data("name") + " marked as " + label + "</div>";
          }
          $("#flash").html(alert);
          window.setTimeout(function() {
            $(".alert").fadeTo(2000, 0).slideUp(2000, function(){
            $(this).remove();
          })}, 4000);
        }
      );
    });

    $("#wrap").on("click", ".contactLink", function(){
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

    var getInactive = 1;
    $("#inactiveContacts").click(function(){
      $("#inactiveSymbol").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
      if(getInactive){
        getInactive = 0;
        $("#inactive").html("<div class='panel-heading'><img class='img-responsive centre' src='/images/loading.gif'></div>");
        $.get(
          "/discover/ajax/getinactive.php",
          {id: "<?php echo $civicrm_id; ?>" },
          function(contacts){
            $("#inactive").html(contacts);
          }
        );
      }
    });

    $("#wrap").on("click", ".collapsable", function(){
      $(this).find(".collSymbol").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
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
    <div id="addContact" class="btn-success fullWidth">
      <i class='glyphicon glyphicon-plus'></i> Add New Contact
    </div>

    <?php
      if($contacts){
        $currentCampus = 0; $first = 1; $idVal = 0;
        foreach($contacts as $key => $contact){
          if($contact["school_id"] != $currentCampus){
            if($first == 0){
              echo "</div></div>";
            }
            $currentCampus = $contact["school_id"];
            ?>
              <div class="panel panel-default schoolContacts">
                  <!-- Default panel contents -->
                <div class="panel-heading collapsable" data-toggle="collapse" data-target="#schoolColl<?php echo $idVal; ?>">
                  <h4>
                    <i class='glyphicon glyphicon-chevron-down collSymbol'></i>
                    <?php echo $contact["school_name"]; ?>
                  </h4>
                </div>
                <div id="schoolColl<?php echo $idVal++; ?>" class="list-group collapse in">
                  <div class="list-group-item contactLink row">
                    <div class="contactInfo pull-left">
                      <h3 class="list-group-item-heading"><?php echo $contact["name"]; ?></h3>
                      <span class="contactID hidden"><?php echo $contact["id"]; ?></span>
                      <div class="btn-group contactBtns">
                        <a href="javascript:{}" data-relid="<?php echo $contact['relationship']; ?>" data-active="1"
                          data-name="<?php echo $contact["name"]; ?>" class="btn btn-danger inactiveBtn">
                          <i class="glyphicon glyphicon-remove"></i>
                          <span>Mark Inactive</span>
                        </a>
                        <?php if(isset($contact["phone"]) && $contact["phone"]){ ?>
                        <a href="tel:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-earphone"></i>
                          <span class="hidden-xs"><?php echo $contact["phone"]; ?></span>
                        </a>
                        <a href="sms:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-comment"></i>
                        </a>
                        <?php } if(isset($contact["email"]) && $contact["email"]){ ?>
                        <a href="mailto:<?php echo $contact["email"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-envelope"></i>
                          <span class="hidden-xs"><?php echo $contact["email"]; ?></span>
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
                        <a href="javascript:{}" data-relid="<?php echo $contact['relationship']; ?>" data-active="1"
                          data-name="<?php echo $contact["name"]; ?>" class="btn btn-danger inactiveBtn">
                          <i class="glyphicon glyphicon-remove"></i>
                          <span>Mark Inactive</span>
                        </a>
                        <?php if(isset($contact["phone"]) && $contact["phone"]){ ?>
                        <a href="tel:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-earphone"></i>
                          <span class="hidden-xs"><?php echo $contact["phone"]; ?></span>
                        </a>
                        <a href="sms:<?php echo $contact["phone"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-comment"></i>
                        </a>
                        <?php } if(isset($contact["email"]) && $contact["email"]){ ?>
                        <a href="mailto:<?php echo $contact["email"]; ?>" target="_blank" class="btn btn-default msgAction">
                          <i class="glyphicon glyphicon-envelope"></i>
                          <span class="hidden-xs"><?php echo $contact["email"]; ?></span>
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
        echo "</div></div>";
      }
    ?>
    <div id="inactiveContacts" data-toggle="collapse" data-target="#inactive" class="greyBack">
      <i id='inactiveSymbol' class='glyphicon glyphicon-chevron-right'></i> Inactive Contacts
    </div>

    <div id="inactive" class="collapse">
    </div>
    <div class='alert alert-info alert-dismissable'>
      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
      <strong>Note:</strong> P2C will under no circumstances send any communication to your contacts.
      This is a personal tool for you to use to see how God is working in your friends' lives.
    </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
  </body>
</html>
