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
      Sign up for the P2C newsletter <a href="http://p2c.com/students/subscribe" target="_blank">here</a>
    </p>
    <p class="muted credit text-center">
      Have questions? Find answers at <a href="https://getsatisfaction.com/powertochange" target="_blank">Get Satisfaction</a>
    </p>
  </div>
</div>