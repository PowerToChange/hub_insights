<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/dbcalls.php';
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';
  date_default_timezone_set('America/Toronto');

  $access = STUDENT_VIS; //Student permissions
  $levels = array(0 => "Unknown", 1 => "Know and trust a christian", 2 => "Become curious", 3 => "Become open to Change",
    4 => "Seek God", 5 => "Make a decision", 6 => "Grow in relationship with God");  

  $title = "Discover Contacts - New";
  $thisFile = "/discover/new/";
  $activeDiscover = "class='active'";
  $crumbs = array("Home" => "/", "Discover" => "/discover/", "New Contact" => "/discover/new/");

  $conReturn = 0;
  if($_POST){
    $conReturn = new_contact($_POST);
    if(is_numeric($conReturn)){
      header("Location: http://" . $_SERVER['HTTP_HOST'] . "/discover/contact/" . $conReturn . "/");
      die();
    }
  }

  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>

  <script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({  
      cache: false  
    });

    jQuery.validator.addMethod('phoneUS', function(phone_number, element) {
      phone_number = phone_number.replace(/\s+/g, ''); 
      return this.optional(element) || phone_number.length > 9 &&
        phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, 'Enter a valid phone number.');

    $('#editForm').validate({
      ignore: ':not(select:hidden, input:visible, textarea:visible)',
      rules: {
        inputFirst: {
          required: true
        },
        selectCampus: {
          required: true
        },
        selectGender: {
          required: true
        },
        selectInter: {
          required: true
        },
        selectLevel: {
          required: true
        },
        inputEmail: {
          email: true
        },
        inputPhone: {
          phoneUS: true
        }
      },
      messages: {
        inputEmail: {
          email: "Enter a valid email."
        }
      },
      highlight: function(element) {
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
      },
      success: function(element) {
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(element).removeClass('error').addClass('valid').addClass('error');
      },
      errorPlacement: function (error, element) {
        if ($(element).is('select')) {
            element.next().after(error); // special placement for select elements
        } else {
            error.insertAfter(element);  // default placement for everything else
        }
      }
    });
    $("#submitInfo").click(function(){
      if($("#editForm").valid()){
        $("#editForm").submit();
      }
    });

    $("#backPage").click(function(){
      window.location = "/discover/";
    });

    if($(".alert").length>0){
      window.setTimeout(function() { 
        $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
        $(this).remove(); 
      })}, 4000);
    }

    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
      $('.selectpicker').selectpicker('mobile');
    }
    else {
      $('.selectpicker').selectpicker();
    }

    $("#inputFirst").focus();
  });
  </script>

  <div id="flash">
    <?php
      if($conReturn){
        ?>
        <div class='alert alert-danger alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <strong>Error!</strong> <?php echo $conReturn; ?> 
        </div>
        <?php
      }
    ?>
  </div>

  <div >
  <div class="row">
    <div class="col-sm-12">

      <div class="well square">
        <div>
          <h4 class="pull-left"><i class="glyphicon glyphicon-user"></i> Add Contact Info</h4>
          <div id="editInfoGroup" class=" btn-group pull-right">
            <a id="backPage" class="btn btn-default">Back</a>
            <a id="submitInfo" class="btn btn-success">Submit</a>
          </div>
        </div>
        <form class="form-horizontal" id="editForm" role="form" action="<?php echo $thisFile; ?>" method="post">
        <table class="table table-bordered table-striped">
          <tbody>
            <tr class="infoEdit">
              <td>First Name</td>
              <td class="form-group">
                  <input type="text" class="form-control" id="inputFirst" name="inputFirst" placeholder="First Name" value="<?php echo $_POST['inputFirst'];?>">
              </td>
            </tr>
            <tr class="infoEdit">
              <td>Last Name</td>
              <td class="form-group">
                  <input type="text" class="form-control" id="inputLast" name="inputLast" placeholder="Last Name" value="<?php echo $_POST['inputLast'];?>">
              </td>
            </tr>
            <tr>
              <td>Campus</td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectCampus" name="selectCampus">
                  <option selected="selected" disabled="disabled" value="0">Choose Campus</option>
                  <?php
                    $schools = getSchools();
                    foreach($schools as $id => $label){
                        $selected = ($_POST["selectCampus"] == $id ? "selected" : "");
                        echo "<option value=\"" . $id . "\" " . $selected . ">" . $label . "</option>";
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Gender</td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectGender" name="selectGender">
                  <option value="2" <?php echo ($_POST["selectGender"] == 2 ? "selected" : "");?>>Male</option>
                  <option value="1" <?php echo ($_POST["selectGender"] == 1 ? "selected" : "");?>>Female</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Email</td>
              <td class="infoEdit form-group">
                <input type="text" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" value="<?php echo $_POST['inputEmail'];?>">
              </td>
            </tr>
            <tr>
              <td>Phone</td>
              <td class="infoEdit form-group">
                <input type="text" class="form-control" id="inputPhone" name="inputPhone" placeholder="Phone" value="<?php echo $_POST['inputPhone'];?>">
              </td>
            </tr>
            <tr>
              <td>International</td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectInter" name="selectInter">
                  <option value="yes" <?php echo ($_POST["selectInter"] == "yes" ? "selected" : "");?>>Yes</option>
                  <option value="no" <?php echo ($_POST["selectInter"] == "no" ? "selected" : "");?>>No</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Engagement Level</td>
              <td class="infoEdit form-group">
                <select class="selectpicker" data-width="100%" data-size="10" id="selectLevel" name="selectLevel">
                  <?php
                    foreach ($levels as $id => $label) {
                      $selected = ($_POST["selectLevel"] == $id ? "selected" : "");
                      echo "<option value=\"" . $id . "\" " . $selected . ">" . $label . "</option>";
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Next Steps</td>
              <td class="infoEdit form-group">
                <textarea class="form-control" id="inputNext" name="inputNext" rows="3" placeholder="Optional"><?php echo $_POST['inputNext'];?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" id="inputID" name="inputID" value="<?php echo $civicrm_id; ?>">
        </form>
      </div>


    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>
