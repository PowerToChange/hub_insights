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
      
        $('.insightLink').click(function() {
          $('#rangeForm').attr("action", $(this).data("url"));
          $('#hiddenAdd').val($(this).data("input")); 
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
             'All Time': [moment().subtract('years', 50), moment().add('years', 50)]
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
      <?php if(userAccess(MIN_VIS)){ ?>
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
              <?php if(userAccess(STAFF_VIS)){ ?>
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
            <input type="hidden" id="hiddenAdd" name="hiddenAdd" value="<?php echo $_POST["hiddenAdd"]; ?>">
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
            $idOpen = ""; $msOpen = "in"; $dcOpen = ""; $surveyOpen = "";
            if($idAddActive || $idBPActive || $idBMActive || $idBNActive){
              $idOpen = "in"; $msOpen = ""; $dcOpen = ""; $surveyOpen = "";
            }
            else if($evAddActive || $evTypeActive || $monAddActive || $msBPActive || $msBCActive || $schoolActive || $overviewActive){
              $idOpen = ""; $msOpen = "in"; $dcOpen = ""; $surveyOpen = "";
            }
            else if($dcMonActive || $dcPersonActive || $dcThresholdActive){
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
                  <a href="javascript:{}" data-url="/insights/decisions/" data-input="true" class="insightLink list-group-item <?php echo $idAddActive; ?>">Add/Edit Decisions</a>
                  <a href="javascript:{}" data-url="/insights/decisions/bigpicture/" class="insightLink list-group-item <?php echo $idBPActive; ?>">Big Picture</a>
                  <a href="javascript:{}" data-url="/insights/decisions/bymethod/" class="insightLink list-group-item <?php echo $idBMActive; ?>">By Method</a>
                  <a href="javascript:{}" data-url="/insights/decisions/" data-input="false" class="insightLink list-group-item <?php echo $idBNActive; ?>">By Name</a>
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
                <a href="javascript:{}" data-url="/insights/map/" class="insightLink list-group-item <?php echo $overviewActive; ?>">Overview</a>
                <a href="javascript:{}" data-url="/insights/eventstats/" class="insightLink list-group-item <?php echo $evAddActive; ?>">Add/Edit Event Stats</a>
                <a href="javascript:{}" data-url="/insights/eventtype/" class="insightLink list-group-item <?php echo $evTypeActive; ?>">Event Stats By Type</a>
                <a href="javascript:{}" data-url="/insights/monthlystats/" class="insightLink list-group-item <?php echo $monAddActive; ?>">Add/Edit Monthly Stats</a>
                <a href="javascript:{}" data-url="/insights/monthlystats/bigpicture/" class="insightLink list-group-item <?php echo $msBPActive; ?>">Movement Snapshot - Evangelism Big Picture</a>
                <a href="javascript:{}" data-url="/insights/monthlystats/bycampus/" class="insightLink list-group-item <?php echo $msBCActive; ?>">Movement Snapshot - Monthly Breakdown</a>
                <?php if(userAccess(STAFF_VIS)){ ?>
                  <a href="javascript:{}" data-url="/insights/schools/" class="insightLink list-group-item <?php echo $schoolActive; ?>">School Report</a>
                <?php } ?>
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
                <a href="javascript:{}" data-url="/insights/discover/bymonth/" class="insightLink list-group-item <?php echo $dcMonActive; ?>">Discover Contacts - By Month</a>
                <a href="javascript:{}" data-url="/insights/discover/byperson/" class="insightLink list-group-item <?php echo $dcPersonActive; ?>">Discover Contacts - By Person</a>
                <a href="javascript:{}" data-url="/insights/discover/threshold/" class="insightLink list-group-item <?php echo $dcThresholdActive; ?>">Discover Contacts - Threshold Summary</a>
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
                <a href="javascript:{}" data-url="/insights/survey/natpriority/" class="insightLink list-group-item <?php echo $surNatPriActive; ?>">National Priority</a>
                <a href="javascript:{}" data-url="/insights/survey/natfollowup/" class="insightLink list-group-item <?php echo $surNatFollowActive; ?>">National Follow-Up</a>
                <a href="javascript:{}" data-url="/insights/survey/results/" class="insightLink list-group-item <?php echo $surResultsActive; ?>">Results and Rejoiceables</a>
                <a href="javascript:{}" data-url="/insights/survey/breakdown/" class="insightLink list-group-item <?php echo $surBreakdownActive; ?>">Priority Breakdown</a>
                <a href="javascript:{}" data-url="/insights/survey/volunteers/" class="insightLink list-group-item <?php echo $surVolunteersActive; ?>">Volunteers Report</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
