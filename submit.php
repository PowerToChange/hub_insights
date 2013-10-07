<?php
  include 'blackbox.php';
?>
<html>
<head>
  <title>Survey Submit</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <h1>Power to Change - Students</h1>
  <h2>Survey Submission</h2>

  <div class="success">
    <?php
      if($_POST){
        try {
          new_contact($_POST);
          echo "<h1 class=\"success\">Success!</h1>";
        }
        catch(Exception $e){
          echo "<h1 class=\"error\">" . $e->getMessage() . "</h1>";
        }
      }
    ?>
    <form name="input" action="survey.php" method="get">

    <input type="hidden" name="s" value="<?php echo $_POST["s"]; ?>">
    <input type="hidden" name="school" value="<?php echo $_POST["School"]["contact_id_b"]; ?>">
    <input type="hidden" name="submitter" value="<?php echo $_POST["Survey"]["custom_120"]; ?>">

    <input type="submit" value="Submit Another Form">
    </form>
		
  </div>

</body>
</html>
