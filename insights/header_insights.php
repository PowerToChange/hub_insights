    <script type="text/javascript">
      jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "percent-pre": function ( a ) {
          var x = (a == "-") ? 0 : a.replace( /%/, "" );
          return parseFloat( x );
        },
        "percent-asc": function ( a, b ) {
          return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "percent-desc": function ( a, b ) {
          return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
      });

      $(window).load(function(){
        $('#selectCampus').selectpicker('show');
      });

      $(document).ready(function() {

        $('.datatable').dataTable({
          <?php echo $tableConfig; ?>
          <?php echo $tableSorting; ?>
          "sPaginationType": "bs_normal"
        }); 
        $('.datatable').each(function(){
          var datatable = $(this);
          // SEARCH - Add the placeholder for Search and Turn this into in-line form control
          var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
          search_input.attr('placeholder', 'Search');
          search_input.addClass('form-control input-sm');
          // LENGTH - Inline-Form control
          var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
          length_sel.addClass('form-control input-sm');
        });
        $('.dataTables_length').find('select').removeClass();
      
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
          $('.selectpicker').selectpicker('mobile');
        }
        else {
          $('.selectpicker').selectpicker();
        }
      
        $('#idAdd').click(function() {
          $('#rangeForm').attr("action", "/insights/decisions/");
          $('#hiddenAdd').val("true");
          $('#rangeForm')[0].submit();
        });
      
        $('#idBigPicture').click(function() {
          $('#rangeForm').attr("action", "/insights/decisions/bigpicture/");
          $('#rangeForm')[0].submit();
        });
      
        $('#idByMethod').click(function() {
          $('#rangeForm').attr("action", "/insights/decisions/bymethod/");
          $('#rangeForm')[0].submit();
        });
      
        $('#idByName').click(function() {
          $('#rangeForm').attr("action", "/insights/decisions/");
          $('#hiddenAdd').val("false");
          $('#rangeForm')[0].submit();
        });

        $('#evAdd').click(function() {
          $('#rangeForm').attr("action", "/insights/eventstats/");
          $('#rangeForm')[0].submit();
        });

        $('#evType').click(function() {
          $('#rangeForm').attr("action", "/insights/eventtype/");
          $('#rangeForm')[0].submit();
        });

        $('#monAdd').click(function() {
          $('#rangeForm').attr("action", "/insights/monthlystats/");
          $('#rangeForm')[0].submit();
        });

        $('#msBigPicture').click(function() {
          $('#rangeForm').attr("action", "/insights/monthlystats/bigpicture/");
          $('#rangeForm')[0].submit();
        });

        $('#msByCampus').click(function() {
          $('#rangeForm').attr("action", "/insights/monthlystats/bycampus/");
          $('#rangeForm')[0].submit();
        });

        $('#schoolRep').click(function() {
          $('#rangeForm').attr("action", "/insights/schools/");
          $('#rangeForm')[0].submit();
        });

        $('#dcByMon').click(function() {
          $('#rangeForm').attr("action", "/insights/discover/bymonth/");
          $('#rangeForm')[0].submit();
        });

        $('#dcByPerson').click(function() {
          $('#rangeForm').attr("action", "/insights/discover/byperson/");
          $('#rangeForm')[0].submit();
        });

        $('#surveyNatPriority').click(function() {
          $('#rangeForm').attr("action", "/insights/survey/natpriority/");
          $('#rangeForm')[0].submit();
        });

        $('#surveyNatFollowup').click(function() {
          $('#rangeForm').attr("action", "/insights/survey/natfollowup/");
          $('#rangeForm')[0].submit();
        });

        $('#surveyResults').click(function() {
          $('#rangeForm').attr("action", "/insights/survey/results/");
          $('#rangeForm')[0].submit();
        });

        $('#surveyBreakdown').click(function() {
          $('#rangeForm').attr("action", "/insights/survey/breakdown/");
          $('#rangeForm')[0].submit();
        });

        $('#surveyVolunteers').click(function() {
          $('#rangeForm').attr("action", "/insights/survey/volunteers/");
          $('#rangeForm')[0].submit();
        });

        $('#overview').click(function() {
          $('#rangeForm').attr("action", "/insights/map/");
          $('#rangeForm')[0].submit();
        });
      
        var startThis = moment().month(8).startOf('month');
        var endThis = moment().month(7).add('years',1).endOf('month');
        var startLast = moment().month(8).subtract('years',1).startOf('month');
        var endLast = moment().month(7).endOf('month');
        if(moment().month() < 8){
          var startThis = moment().month(8).subtract('years',1).startOf('month');
          var endThis = moment().month(7).endOf('month');
          var startLast = moment().month(8).subtract('years',2).startOf('month');
          var endLast = moment().month(7).subtract('years',1).endOf('month');
        }

        var selectStart = startThis;
        var selectEnd = endThis;
        <?php 
          if($_POST["selectSubmitted"]){
            echo "var selectStart = moment('" . $_POST["hiddenStart"] . "', 'YYYY-MM-DD');\n";
            echo "var selectEnd = moment('" . $_POST["hiddenEnd"] . "', 'YYYY-MM-DD');\n";
            echo "$('#selectCampus').selectpicker('val', '" . $_POST["selectCampus"] . "');\n";
            echo "$('#selectSurvey').selectpicker('val', '" . $_POST["selectSurvey"] . "');\n";
          }
          elseif($_COOKIE["campus"]){
            echo "$('#selectCampus').selectpicker('val', '" . $_COOKIE["campus"] . "');\n";
          }
        ?>
            
        $('#reportrange').daterangepicker({
          ranges: {
             'This Month': [moment().startOf('month'), moment().endOf('month')],
             'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
             'This Year': [startThis, endThis],
             'Last Year': [startLast, endLast],
             'All Time': [moment().subtract('years', 100), moment().add('years', 100)]
          },
          startDate: selectStart,
          endDate: selectEnd,
          format: 'YYYY-MM-DD',
        },
        function(start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
          $('#hiddenStart').val(start.format('YYYY-MM-DD'));
          $('#hiddenEnd').val(end.format('YYYY-MM-DD'));
        });
        $('#reportrange span').html(selectStart.format('MMMM D, YYYY') + ' - ' + selectEnd.format('MMMM D, YYYY'));
        $('#hiddenStart').val(selectStart.format('YYYY-MM-DD'));
        $('#hiddenEnd').val(selectEnd.format('YYYY-MM-DD'));

        $('[rel=tooltip]').tooltip({container: 'body'});

        $('#filterWell').popover({'container': 'body', trigger: 'manual'});
        $('#navDecision').popover({'container': 'body', trigger: 'manual'});
        $('#insightsInfo').on('click', function(){
          $('#filterWell').popover('toggle');
          $('#navDecision').popover('toggle');
        });


      });
    </script>

    <div class="container">
    <div class="row">
      <br>
      <div class="col-md-3 col-sm-12">
        <div class="well side">
          <div class="container">
            <h2 class="pull-left" style="color: black">Insights</h2>
            <span id="insightsInfo" class="glyphicon glyphicon-question-sign" style="font-size:18px; margin-left:10px" rel="tooltip" title="Click for Help"></span>
          </div>

          <div class="well well-sm side" id="filterWell" data-toggle="popover" data-original-title="Filter Results" 
            data-content="Filters what is displayed to the right. You must press 'Update Display' or a navigation link below to save filter changes.">
          <form id="rangeForm" role="form" action="<?php echo $thisFile; ?>" method="post">
            <select class="selectpicker" data-width="100%" data-size="10" id="selectCampus" name="selectCampus" hidden>
              <?php if(!$permissions["isStudent"]){ ?>
              <option selected="selected" value="0">All Campuses</option>
              <?php
                }
                $schools = getSchools();
                foreach($schools as $id => $label){
                  echo "<option value=\"" . $id . "\">" . $label . "</option>";
                }
              ?>
            </select>
            <input type="hidden" id="hiddenStart" name="hiddenStart">
            <input type="hidden" id="hiddenEnd" name="hiddenEnd">
            <input type="hidden" id="hiddenAdd" name="hiddenAdd"
              value="<?php echo (($_POST["hiddenAdd"] == "true" || $_GET["add"] == "true") ? "true" : "false"); ?>">
            <input type="hidden" name="selectSubmitted" value="true">

          <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-bottom: 10px;">
            <i class="glyphicon glyphicon-calendar icon-calendar"></i>
            <span></span> <b class="caret"></b>
          </div>

          <div <?php if(!isset($surveyOptions)){ echo "class='hidden'"; }?> >
            <label for="selectSurvey">Survey Options</label>
            <select class="selectpicker" data-width="100%" data-size="10" data-dropup-auto="false" id="selectSurvey" name="selectSurvey" hidden>
              <option selected="selected" value="0">All Surveys</option>
              <?php
                $surveys = getSurveys();
                foreach ($surveys as $id => $label) {
                  echo "<option value=\"" . $id . "\">" . $label . "</option>";
                }
              ?>
            </select>

            <div class="checkbox">
              <label><input type="checkbox" id="onlyInt" name="onlyInt" <?php if(isset($_POST["onlyInt"])){ echo "checked"; }?> > International Students Only</label>
            </div>
          </div>

          </form>
          <a class="btn btn-warning" style="width:100%" onclick="$('#rangeForm')[0].submit();">Update Display</a>

        </div>

        <?php
          if($permissions["isStaff"] && $permissions["visibility"] >= 1){
            $idOpen = ""; $msOpen = "in"; $dcOpen = ""; $surveyOpen = "";
            if($idAddActive || $idBPActive || $idBMActive || $idBNActive){
              $idOpen = "in"; $msOpen = ""; $dcOpen = ""; $surveyOpen = "";
            }
            else if($evAddActive || $evTypeActive || $monAddActive || $msBPActive || $msBCActive || $schoolActive){
              $idOpen = ""; $msOpen = "in"; $dcOpen = ""; $surveyOpen = "";
            }
            else if($dcMonActive || $dcPersonActive){
              $idOpen = ""; $msOpen = ""; $dcOpen = "in"; $surveyOpen = "";
            }
            else if($surNatPriActive || $surNatFollowActive || $surResultsActive || $surBreakdownActive || $surVolunteersActive){
              $idOpen = ""; $msOpen = ""; $dcOpen = ""; $surveyOpen = "in";
            }
        ?>
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
              <div class="panel-heading" id="navDecision" data-toggle="popover" data-original-title="Site Navigation" 
                data-content="Dropdown menu to access insight reports and input.">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    Indicated Decisions
                  </a>
                </h4>
              </div>
              <div id="collapseOne" class="list-group panel-collapse collapse <?php echo $idOpen; ?>">
                  <a id="idAdd" href="javascript:{}" class="list-group-item <?php echo $idAddActive; ?>">Add/Edit Decisions</a>
                  <a id="idBigPicture" href="javascript:{}" class="list-group-item <?php echo $idBPActive; ?>">Big Picture</a>
                  <a id="idByMethod" href="javascript:{}" class="list-group-item <?php echo $idBMActive; ?>">By Method</a>
                  <a id="idByName" href="javascript:{}" class="list-group-item <?php echo $idBNActive; ?>">By Name</a>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                    Movement Snapshots
                  </a>
                </h4>
              </div>
              <div id="collapseTwo" class="list-group panel-collapse collapse <?php echo $msOpen; ?>">
                <a id="overview" href="javascript:{}" class="list-group-item <?php echo $overviewActive; ?>">Overview</a>
                <a id="evAdd" href="javascript:{}" class="list-group-item <?php echo $evAddActive; ?>">Add/Edit Event Stats</a>
                <a id="evType" href="javascript:{}" class="list-group-item <?php echo $evTypeActive; ?>">Event Stats By Type</a>
                <a id="monAdd" href="javascript:{}" class="list-group-item <?php echo $monAddActive; ?>">Add/Edit Monthly Stats</a>
                <a id="msBigPicture" href="javascript:{}" class="list-group-item <?php echo $msBPActive; ?>">Movement Snapshot - Evangelism Big Picture</a>
                <a id="msByCampus" href="javascript:{}" class="list-group-item <?php echo $msBCActive; ?>">Movement Snapshot - Monthly Breakdown</a>
                <a id="schoolRep" href="javascript:{}" class="list-group-item <?php echo $schoolActive; ?>">School Report</a>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                    Discover Contacts
                  </a>
                </h4>
              </div>
              <div id="collapseThree" class="list-group panel-collapse collapse <?php echo $dcOpen; ?>">
                <a id="dcByMon" href="javascript:{}" class="list-group-item <?php echo $dcMonActive; ?>">Discover Contacts - By Month</a>
                <a id="dcByPerson" href="javascript:{}" class="list-group-item <?php echo $dcPersonActive; ?>">Discover Contacts - By Person</a>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                    Surveys
                  </a>
                </h4>
              </div>
              <div id="collapseFour" class="list-group panel-collapse collapse <?php echo $surveyOpen; ?>">
                <a id="surveyNatPriority" href="javascript:{}" class="list-group-item <?php echo $surNatPriActive; ?>">National Priority</a>
                <a id="surveyNatFollowup" href="javascript:{}" class="list-group-item <?php echo $surNatFollowActive; ?>">National Follow-Up</a>
                <a id="surveyResults" href="javascript:{}" class="list-group-item <?php echo $surResultsActive; ?>">Results and Rejoiceables</a>
                <a id="surveyBreakdown" href="javascript:{}" class="list-group-item <?php echo $surBreakdownActive; ?>">Priority Breakdown</a>
                <a id="surveyVolunteers" href="javascript:{}" class="list-group-item <?php echo $surVolunteersActive; ?>">Volunteers Report</a>
              </div>
            </div>
        </div>
        <?php
          }
        ?>

        </div>
      </div>
