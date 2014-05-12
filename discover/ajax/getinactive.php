<?php
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';

  $contacts = all_contacts($_GET["id"], 0);
  if($contacts){
    $currentCampus = 0; $first = 1; $idVal = 1000;
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
                    <a href="javascript:{}" data-relid="<?php echo $contact['relationship']; ?>" data-active="0"
                      data-name="<?php echo $contact["name"]; ?>" class="btn btn-success inactiveBtn">
                      <i class="glyphicon glyphicon-ok"></i>
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
                    <a href="javascript:{}" data-relid="<?php echo $contact['relationship']; ?>" data-active="0"
                      data-name="<?php echo $contact["name"]; ?>" class="btn btn-success inactiveBtn">
                      <i class="glyphicon glyphicon-ok"></i>
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
