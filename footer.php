  </div>
  </div>
  <div id="push"></div>
</div>
<div id="footer">
  <div class="container">
    <p class="muted credit text-center">
      <?php
        global $crumbs;
        $first = 1;
        foreach ($crumbs as $label => $link) {
          if($first){
            echo "<a href='" . $link . "'>" . $label . "</a>";
            $first = 0;
          }
          else {
            echo " / <a href='" . $link . "'>" . $label . "</a>";
          }
        }
      ?>
    </p>
    <p class="muted credit text-center">
      Sign up for the <a href="http://p2c.com/students/subscribe" target="_blank">P2C Newsletter</a><br>
      Check out the <a href="http://www.facebook.com/groups/p2cpray/" target="_blank">P2C Prayer Facebook Group</a><br>
      Have questions? <a href="https://groups.google.com/a/p2c.com/forum/#!forum/p2c-s-help-desk" target="_blank">Find help here</a>
    </p>
  </div>
</div>
