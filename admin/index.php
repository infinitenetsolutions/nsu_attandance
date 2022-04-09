<?php

//index.php
$page = 1;
include('header.php');
include '../include/connection.php';
include '../include/function.inc.php';
// checking the grade has been seated into the url or not

if(isset($_GET['grade'])){
$_SESSION['grade']=$_GET['grade'];
}
 $_SESSION['grade'];
// checking the grade having or not into the session
if(!isset($_SESSION['grade']) || $_SESSION['grade']=='all' ){
  $_SESSION['condition']=1;
}else{
  $_SESSION['condition']="tbl_student.student_grade_id=".$_SESSION['grade'];
}

$limit = 10;
if (isset($_GET["page"])) {
  $page  = $_GET["page"];
} else {
  $page = 1;
};
$start_from = ($page - 1) * $limit;
$s_no = $start_from + 1;
 $query = "
SELECT * FROM tbl_student 
LEFT JOIN tbl_attendance 
ON tbl_attendance.student_id = tbl_student.student_id 
INNER JOIN tbl_grade 
ON tbl_grade.grade_id = tbl_student.student_grade_id 
INNER JOIN tbl_teacher 
ON tbl_teacher.teacher_grade_id = tbl_grade.grade_id 
WHERE ".$_SESSION['condition']."
LIMIT $start_from, $limit 
";
$result = mysqli_query($connection, $query);


?>

<div class="container" style="margin-top:30px">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-9">Overall Student Attendance Status</div>
        <div class="col-md-3" align="right">

        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <div class="row">
          <div class="col-sm-6">


          </div>
          <div class="col-sm-6 mb-2">
            <div class="row">
              <div class="col-sm-6">
                <select class="form-control form-control-sm selectpicker" onchange="changeGrade()" id="changeGradeId" data-show-subtext="true" data-live-search="true">
                  <option value="all" <?php if (isset($row['grade_id']) && ($row["grade_id"] == "all")) {
                                        echo "disabled selected";
                                      } ?>>All</option>
                  <?php
                  $query = "
            		SELECT * FROM tbl_grade ORDER BY grade_name ASC
            		";
                $result1 = mysqli_query($connection, $query);

                  while ($row1=mysqli_fetch_array($result1)) {
                  ?>
                    <option value="<?php echo $row1["grade_id"]; ?>
                      " <?php if (isset($_GET['grade'])) {
                          if ($row1["grade_id"] == $_GET["grade"]) {
                            echo "disabled selected";
                          }
                        } ?>><?php echo $row1["grade_name"]; ?></option>
                  <?php } ?>

                </select>
              </div>
              <div class="col-sm-6">
                <input type="text" onkeyup="search(this.value)" placeholder="Search student.." class="form-control form-control-sm">
              </div>
            </div>
          </div>
        </div>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>S.NO</th>
              <th>Student Name</th>
              <th>Roll Number</th>
              <th>Grade</th>
              <th>Teacher</th>
              <th>Attendance Percentage</th>
              <th>Report</th>
            </tr>
          </thead>
          <tbody id="search_data">

            <?php

            while ($row = mysqli_fetch_array($result)) { ?>
              <tr>
                <td><?= $s_no ?></td>
                <td><?= $row['student_name'] ?></td>
                <td><?= $row["student_roll_number"]; ?></td>
                <td><?= $row["grade_name"]; ?></td>
                <td><?= $row["teacher_name"]; ?></td>
                <td><?= get_attendance_percentage($connect, $row["student_id"]); ?></td>
                <td><?= '<button type="button" name="report_button" data-student_id="' . $row["student_id"] . '" class="btn btn-info btn-sm report_button">Report</button>&nbsp;&nbsp;&nbsp;<button type="button" name="chart_button" data-student_id="' . $row["student_id"] . '" class="btn btn-danger btn-sm report_button">Chart</button>'; ?></td>
              </tr>
            <?php $s_no++;
            }  ?>
          </tbody>
        </table>
        <?php paginate($connection, 'tbl_student', '10', 'index.php', $_SESSION['condition'], 'student_id') ?>
      </div>
    </div>
  </div>
</div>
<?php include '../include/footer.php'; ?>
</body>

</html>

<script type="text/javascript" src="../js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="../css/datepicker.css" />

<style>
  .datepicker {
    z-index: 1600 !important;
    /* has to be larger than 1050 */
  }
</style>

<div class="modal" id="formModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Make Report</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="form-group">
          <select name="report_action" id="report_action" class="form-control">
            <option value="pdf_report">PDF Report</option>
            <option value="chart_report">Chart Report</option>
          </select>
        </div>
        <div class="form-group">
          <div class="input-daterange">
            <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
            <span id="error_from_date" class="text-danger"></span>
            <br />
            <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
            <span id="error_to_date" class="text-danger"></span>
          </div>
        </div>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <input type="hidden" name="student_id" id="student_id" />
        <button type="button" name="create_report" id="create_report" class="btn btn-success btn-sm">Create Report</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<script>
  $(document).ready(function() {

    var dataTable = $('#student_table').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        url: "attendance_action.php",
        type: "POST",
        data: {
          action: 'index_fetch'
        }
      }
    });

    $('.input-daterange').datepicker({
      todayBtn: "linked",
      format: 'yyyy-mm-dd',
      autoclose: true,
      container: '#formModal modal-body'
    });

    $(document).on('click', '.report_button', function() {
      var student_id = $(this).data('student_id');
      $('#student_id').val(student_id);
      $('#formModal').modal('show');
    });

    $('#create_report').click(function() {
      var student_id = $('#student_id').val();
      var from_date = $('#from_date').val();
      var to_date = $('#to_date').val();
      var error = 0;
      var action = $('#report_action').val();
      if (from_date == '') {
        $('#error_from_date').text('From Date is Required');
        error++;
      } else {
        $('#error_from_date').text('');
      }
      if (to_date == '') {
        $('#error_to_date').text("To Date is Required");
        error++;
      } else {
        $('#error_to_date').text('');
      }

      if (error == 0) {
        $('#from_date').val('');
        $('#to_date').val('');
        $('#formModal').modal('hide');
        if (action == 'pdf_report') {
          window.open("report.php?action=student_report&student_id=" + student_id + "&from_date=" + from_date + "&to_date=" + to_date);
        }
        if (action == 'chart_report') {
          location.href = "chart.php?action=student_chart&student_id=" + student_id + "&from_date=" + from_date + "&to_date=" + to_date;
        }
      }

    });

  });
</script>
<script>
  function search(data) {
    if (data.length == '') {
      window.location.href
    } else {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        document.getElementById("search_data").innerHTML = this.responseText;
      };
      xmlhttp.open("GET", "./ajax/searchstudent.php?search=" + data, true);
      xmlhttp.send();
    }
  }
  
</script>
<script>
  function changeGrade() {
    var changeValue = document.getElementById('changeGradeId').value;
    window.location.href = "index.php?grade=" + changeValue;
  }
</script>