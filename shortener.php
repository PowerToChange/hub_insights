<?php
  global $civicrm_id;
  include $_SERVER['DOCUMENT_ROOT'].'/login.php';

  $result = "error";
  if($_POST){
    $yourls_params = array('signature' => YOURLS_SIGNATURE, 'action' => 'shorturl', 'format' => 'json', 
      'url' => $_POST["inputUrl"], 'keyword' => $_POST["inputShort"]);
    if(isset($_POST["inputTitle"]) && $_POST["inputTitle"]){ $yourls_params["title"] = $_POST["inputTitle"]; }
    $ch = curl_init(YOURLS_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POST,count($yourls_params));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$yourls_params);
    $reply = curl_exec($ch);
    if(!$reply){
      throw new Exception(curl_error($ch));
    }
    curl_close($ch);
    $result = json_decode($reply, TRUE);
  }

  $access = LEADER_VIS;
  $title = "P2C URL Shortener";
  $thisFile = "/shortener/";
  $crumbs = array("Home" => "/", $title => $thisFile);

  include $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#linkForm').validate({
      ignore: ':not(select:hidden, input:visible, textarea:visible)',
      rules: {
        inputUrl: {
          required: true
        },
        inputShort: {
          required: true
        }
      },
      errorElement: 'span',
      highlight: function(element) {
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
      },
      success: function(element) {
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(element).removeClass('error').addClass('valid').addClass('error');
      }
    });

    $("#inputUrl").focus();
  });
  </script>


  <div >
  <div class="row">
    <div class="col-md-12">
      <?php
        if($result){
          if($result["status"] == "fail"){
            echo "<div class='text-danger text-center'>";
            echo "<h3>" . $result["message"] . "</h3></div>";
          }
          else if($result["status"] == "success"){
            echo "<div class='text-success text-center'>";
            echo "<h3>Short Link \"" . $result["shorturl"] . "\" created for URL \"" . $result["url"]["url"] . "\"</h3></div>";
          }
        }
      ?>
      <div class="well square">
        <div>
          <h4 class="pull-left"><i class="glyphicon glyphicon-link"></i> Add p2c.sh short link</h4>
        </div>
        <form class="form-horizontal" id="linkForm" role="form" action="<?php echo $thisFile; ?>" method="post">
        <table class="table table-bordered table-striped">
          <tbody>
            <tr class="infoEdit">
              <td>URL</td>
              <td class="form-group">
                  <input type="text" class="form-control" id="inputUrl" name="inputUrl" placeholder="URL" value="<?php echo $_POST['inputUrl']; ?>">
              </td>
            </tr>
            <tr class="infoEdit">
              <td>Short Link</td>
              <td class="form-group">
                <div class="input-group">
                  <label class="input-group-addon">http://p2c.sh/</label>
                  <input type="text" class="form-control" id="inputShort" name="inputShort" placeholder="Shortlink" value="<?php echo $_POST['inputShort']; ?>">
                </div>
                <span class="error" for="inputShort"></span>
              </td>
            </tr>
            <tr class="infoEdit">
              <td>Optional Title</td>
              <td class="form-group">
                  <input type="text" class="form-control" id="inputTitle" name="inputTitle" placeholder="Optional" value="<?php echo $_POST['inputTitle']; ?>">
              </td>
            </tr>
          </tbody>
        </table>
        <button type="submit" class="btn btn-success">Submit</button>
        </form>
      </div>

    </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>

  </body>
</html>