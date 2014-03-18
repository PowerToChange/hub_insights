<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $access = STAFF_VIS;
  $schoolInfo = 0;
  if($_POST){
    $schoolInfo = edit_school($_POST);
  }

  $call = get_school($_GET["id"]);
  $school = $call["values"][$_GET["id"]];
  $customValues = array();
  foreach ($school["api.CustomValue.get"]["values"] as $key => $value) {
    $customValues[$value["id"]] = $value["latest"];
  }

  function getCheck($id){
    global $customValues;
    if(isset($customValues[$id]) && $customValues[$id]){
      echo "checked";
    }
  }

  function getChoice($id, $label){
    global $customValues;
    if(isset($customValues[$id]) && $customValues[$id] == $label){
      echo "checked";
    }
  }

  $title = $school["display_name"] . " Info";
  $thisFile = "/insights/schoolinfo/" . $_GET["id"];
  $activeInsights = "class='active'";
  $crumbs = array("Home" => "/", "Insights" => "/insights/", "Schools" => "/insights/schools/", $title => $thisFile);

  $schoolActive = "active";
  $tableConfig = "'aaSorting': [[ 0, 'asc' ]],\n";
  $tableSorting = "'aoColumnDefs': [{'asSorting':['desc','asc'], 'aTargets': [ 1, 2, 3, 4, 5, 6, 7 ] }],\n";
  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
  include $_SERVER['DOCUMENT_ROOT'].'/insights/header_insights.php';
?>
    <script type="text/javascript">
    $(document).ready(function() {
      jQuery.validator.addMethod('phoneUS', function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, ''); 
        return this.optional(element) || phone_number.length > 9 &&
          phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
      }, 'Enter a valid phone number.');

      $('#schoolForm').validate({
        ignore: ":hidden:not(.selectpicker)",
        rules: {
          phone: {
            phoneUS: true
          },
          geo_code_1: {
            number: true
          },
          geo_code_2: {
            number: true
          },
          custom_90: {
            required: true,
            digits: true
          },
          custom_91: {
            required: true,
            digits: true
          },
          custom_92: {
            required: true,
            digits: true
          },
          custom_93: {
            required: true,
            digits: true
          },
          custom_94: {
            required: true,
            digits: true
          },
          custom_95: {
            required: true,
            digits: true
          },
          custom_96: {
            required: true,
            digits: true
          }
        },
        highlight: function(element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function(element) {
          $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
          $(element).removeClass('error').addClass('valid').addClass('error');
        }
      });
    });
    </script>

    <div class="col-md-9" >
      <?php
        if($schoolInfo){
          if($schoolInfo == 1){
            ?>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Success!</strong> School Saved.
            </div>
          <?php } else { ?>
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>Error!</strong> Failed to edit School. Please inform CC Team. Error message: "<?php echo $schoolInfo; ?>"
            </div>
          <?php
          }
        }
      ?>

      <form class="form-horizontal" id="schoolForm" role="form" action="<?php echo $thisFile; ?>" method="post">
      <div class="row">
        <div class="well well-sm col-sm-6">
          <h3>Address</h3>
          <table class="table table-striped">
            <tbody>
              <tr><td>Street Address</td>   
                <td><input type="text" class="form-control" name="street_address" value="<?php echo $school["street_address"]; ?>"></td></tr>
              <tr><td>City</td>             
                <td><input type="text" class="form-control" name="city" value="<?php echo $school["city"]; ?>"></td></tr>
              <tr><td>Province</td>         
                <td><input type="text" class="form-control" name="state_province" value="<?php echo $school["state_province"]; ?>"></td></tr>
              <tr><td>Country</td>          
                <td><input type="text" class="form-control" name="country" value="<?php echo $school["country"]; ?>"></td></tr>
              <tr><td>Postal Code</td>      
                <td><input type="text" class="form-control" name="postal_code" value="<?php echo $school["postal_code"]; ?>"></td></tr>
              <tr><td>Phone</td>            
                <td><input type="text" class="form-control" name="phone" value="<?php echo $school["phone"]; ?>"></td></tr>
              <tr><td>Latitude</td>         
                <td><input type="text" class="form-control" name="geo_code_1" value="<?php echo $school["geo_code_1"]; ?>"></td></tr>
              <tr><td>Longitude</td>        
                <td><input type="text" class="form-control" name="geo_code_2" value="<?php echo $school["geo_code_2"]; ?>"></td></tr>
            </tbody>
          </table>
        </div>

        <div class="well well-sm col-sm-6">
          <h3>Ministry</h3>
          <table class="table table-striped">
            <tbody>
              <tr>
                <td>Ministry Presence:</td>
                <td>
                  <input type="radio" name="custom_73" id="minPresYes" value="Yes" <?php getChoice(73, "Yes"); ?>><label for="minPresYes">Yes</label>
                  <input type="radio" name="custom_73" id="minPresNo" value="No" <?php getChoice(73, "No"); ?>><label for="minPresNo">No</label>
                </td>
              </tr>
              <tr>
                <td>SLM Q1:</td>
                <td>
                  <input type="radio" name="custom_68" id="slm1Yes" value="Yes" <?php getChoice(68, "Yes"); ?>><label for="slm1Yes">Yes</label>
                  <input type="radio" name="custom_68" id="slm1No" value="No" <?php getChoice(68, "No"); ?>><label for="slm1No">No</label>
                </td>
              </tr>
              <tr>
                <td>SLM Q2:</td>
                <td>
                  <input type="radio" name="custom_69" id="slm2Yes" value="Yes" <?php getChoice(69, "Yes"); ?>><label for="slm2Yes">Yes</label>
                  <input type="radio" name="custom_69" id="slm2No" value="No" <?php getChoice(69, "No"); ?>><label for="slm2No">No</label>
                </td>
              </tr>
              <tr>
                <td>SLM Q3:</td>
                <td>
                  <input type="radio" name="custom_70" id="slm3Yes" value="Yes" <?php getChoice(70, "Yes"); ?>><label for="slm3Yes">Yes</label>
                  <input type="radio" name="custom_70" id="slm3No" value="No" <?php getChoice(70, "No"); ?>><label for="slm3No">No</label>
                </td>
              </tr>
              <tr>
                <td>Is it SLM?</td>
                <td>
                  <input type="radio" name="custom_71" id="slmYes" value="Yes" <?php getChoice(71, "Yes"); ?>><label for="slmYes">Yes</label>
                  <input type="radio" name="custom_71" id="slmNo" value="No" <?php getChoice(71, "No"); ?>><label for="slmNo">No</label>
                </td>
              </tr>
              <tr>
                <td>Type:</td>
                <td>
                  <input type="radio" name="custom_72" id="typeStaffed" value="Staffed" <?php getChoice(72, "Staffed"); ?>><label for="typeStaffed">Staffed</label>
                  <input type="radio" name="custom_72" id="typeCatalytic" value="Catalytic" <?php getChoice(72, "Catalytic"); ?>><label for="typeCatalytic">Catalytic</label>
                  <input type="radio" name="custom_72" id="typeUnreached" value="Unreached" <?php getChoice(72, "Unreached"); ?>><label for="typeUnreached">Unreached</label>
                </td>
              </tr>
              <tr>
                <td>Fundraising:</td>
                <td>
                  <input type="radio" name="custom_74" id="fundSponsor" value="Sponsor" <?php getChoice(74, "Sponsor"); ?>><label for="fundSponsor">Sponsor</label>
                  <input type="radio" name="custom_74" id="fundLaunch" value="Launch" <?php getChoice(74, "Launch"); ?>><label for="fundLaunch">Launch</label>
                  <input type="radio" name="custom_74" id="fundUnreached" value="Unreached" <?php getChoice(74, "Unreached"); ?>><label for="fundUnreached">Unreached</label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="well well-sm col-sm-12">
          <h3>Details</h3>
          <table class="table table-striped">
            <tbody>
              <tr><td>Order from West to East</td>   
                <td><input type="text" class="form-control" name="custom_84" value="<?php echo $customValues["84"]; ?>"></td></tr>
              <tr><td>Student Population Center</td>             
                <td><input type="text" class="form-control" name="custom_85" value="<?php echo $customValues["85"]; ?>"></td></tr>
              <tr><td>Description</td>         
                <td><textarea rows="3" class="form-control" name="custom_86"><?php echo $customValues["86"]; ?></textarea></td></tr>
              <tr><td>Wiki</td>          
                <td><input type="text" class="form-control" name="custom_88" value="<?php echo $customValues["88"]; ?>"></td></tr>
              <tr><td>Website</td>      
                <td><input type="text" class="form-control" name="custom_89" value="<?php echo $customValues["89"]; ?>"></td></tr>
              <tr><td>Notes on Sourcing</td>            
                <td><textarea rows="3" class="form-control" name="custom_110"><?php echo $customValues["110"]; ?></textarea></td></tr>
              <tr><td>Other Evangelistic Presence</td>         
                <td><input type="checkbox" name="custom_111" value="1" <?php getCheck(111); ?>></td></tr>
              <tr><td>Sourcing</td>        
                <td><input type="text" class="form-control" name="custom_112" value="<?php echo $customValues["112"]; ?>"></td></tr>
              <tr><td>P2C Website</td>        
                <td><input type="text" class="form-control" name="custom_113" value="<?php echo $customValues["113"]; ?>"></td></tr>
              <tr><td>P2C Facebook Group</td>        
                <td><input type="text" class="form-control" name="custom_114" value="<?php echo $customValues["114"]; ?>"></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="well well-sm col-sm-12">
          <h3>Enrolment</h3>
          <table class="table table-striped">
            <thead>
              <tr>
                <td></td>
                <td class="text-center" colspan="2">Full Time</td>
                <td class="text-center" colspan="2">Part Time</td>
                <td colspan="2"></td>
              </tr>
              <tr>
                <td class="text-center">Cégep</td>
                <td class="text-center">Undergrad</td>
                <td class="text-center">Grad</td>
                <td class="text-center">Undergrad</td>
                <td class="text-center">Grad</td>
                <td class="text-center">International</td>
                <td class="text-center">Total</td>
              </tr>
            </thead>
              <tr>
                <td><input type="text" class="form-control" name="custom_90" id="custom_90" value="<?php echo ($customValues["90"] ?: 0); ?>"></td>
                <td><input type="text" class="form-control" name="custom_91" id="custom_91" value="<?php echo ($customValues["91"] ?: 0); ?>"></td>
                <td><input type="text" class="form-control" name="custom_92" id="custom_92" value="<?php echo ($customValues["92"] ?: 0); ?>"></td>
                <td><input type="text" class="form-control" name="custom_93" id="custom_93" value="<?php echo ($customValues["93"] ?: 0); ?>"></td>
                <td><input type="text" class="form-control" name="custom_94" id="custom_94" value="<?php echo ($customValues["94"] ?: 0); ?>"></td>
                <td><input type="text" class="form-control" name="custom_95" id="custom_95" value="<?php echo ($customValues["95"] ?: 0); ?>"></td>
                <td><input type="text" class="form-control" name="custom_96" id="custom_96" value="<?php echo ($customValues["96"] ?: 0); ?>"></td>
              </tr>
              <tr>
                <td>Enrolment Source</td>
                <td colspan="5"><input type="text" class="form-control" name="custom_97" value="<?php echo $customValues["97"]; ?>"></td>
                <td colspan="1"></td>
              </tr>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="well well-sm col-sm-12">
          <h3>Ministry Presence</h3>
          <table class="table table-striped">
            <thead>
              <tr>
                <td class="text-center">2007/08</td>
                <td class="text-center">2008/09</td>
                <td class="text-center">2009/10</td>
                <td class="text-center">2010/11</td>
                <td class="text-center">2011/12</td>
                <td class="text-center">2012/13</td>
                <td class="text-center">2013/14</td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center"><input type="checkbox" value="1" name="custom_98"  <?php getCheck(98); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_99"  <?php getCheck(99); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_100"  <?php getCheck(100); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_101"  <?php getCheck(101); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_102"  <?php getCheck(102); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_103"  <?php getCheck(103); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_<?php echo API_SCHOOL_MP_13; ?>"  <?php getCheck(API_SCHOOL_MP_13); ?>></td>
              </tr>              
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="well well-sm col-sm-12">
          <h3>SLM</h3>
          <table class="table table-striped">
            <thead>
              <tr>
                <td class="text-center">2007/08</td>
                <td class="text-center">2008/09</td>
                <td class="text-center">2009/10</td>
                <td class="text-center">2010/11</td>
                <td class="text-center">2011/12</td>
                <td class="text-center">2012/13</td>
                <td class="text-center">2013/14</td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center"><input type="checkbox" value="1" name="custom_104" <?php getCheck(104); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_105" <?php getCheck(105); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_106" <?php getCheck(106); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_107" <?php getCheck(107); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_108" <?php getCheck(108); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_109" <?php getCheck(109); ?>></td>
                <td class="text-center"><input type="checkbox" value="1" name="custom_<?php echo API_SCHOOL_SLM_13; ?>" <?php getCheck(API_SCHOOL_SLM_13); ?>></td>
              </tr>              
            </tbody>
          </table>
        </div>
      </div>

      <input type="hidden" name="contact_id" id="contact_id" value="<?php echo $_GET['id']; ?>">
      <input type="hidden" name="address_id" id="address_id" value="<?php echo $school['address_id']; ?>">
      <input type="hidden" name="phone_id" id="phone_id" value="<?php echo $school['phone_id']; ?>">

      <div class="text-center">
        <div class="btn-group">
          <a id="backBtn" href="/insights/schools/" class="btn btn-default btn-lg">Back</a>
          <button type="submit" id="formSubmit" class="btn btn-success btn-lg">Save Changes</button>
        </div>
        <br><br>
      </div>

      </form>

      <!--<div class="row">
        <div class="well well-sm col-sm-12">
          <h2>Custom Values</h2>
          <table class="table table-striped">
            <tbody>
          <?php
            foreach ($customValues as $key => $value) {
              echo "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>\n";
            }
          ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="well well-sm col-sm-12">
          <h2>POST</h2>
          <table class="table table-striped">
            <tbody>
          <?php
            global $sends;
            foreach ($sends["params"] as $key => $value) {
              echo "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>\n";
            }
          ?>
            </tbody>
          </table>
        </div>
      </div>-->

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
