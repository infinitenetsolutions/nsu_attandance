<?php

//header.php

include('database_connection.php');

session_start();

if (!isset($_SESSION["admin_id"])) {
  header('location:login.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Netaji Subhas Student Attendance System </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" sizes="56x56" href="../img/icon/icon.png">



  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/dataTables.bootstrap4.min.css">
  <script src="../js/jquery.min.js"></script>
  <script src="../js/popper.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery.dataTables.min.js"></script>
  <script src="../js/dataTables.bootstrap4.min.js"></script>
  <link rel="stylesheet" href="../css/style.css">

</head>

<body>

  <div class="jumbotron-small text-center" style="padding-bottom: 10px; margin-bottom: 1px ; background-color:#b42a16;">
    <img src="../nsu_logo.png" style="height:120px;">
    <h2 class="text-white">NSU ATTENDANCE</h2>
  </div>

  <nav class="navbar navbar-expand-sm bg-dark navbar-dark">

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav">
        <li class="nav-item <?php if ($page == 1) {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item <?php if ($page == 2) {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="grade.php">Semester</a>
        </li>
        <li class="nav-item <?php if ($page == 3) {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="teacher.php">Teacher</a>
        </li>

        <li class="nav-item <?php if ($page == 4) {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="student.php">Student</a>
        </li>
        <li class="nav-item <?php if ($page == 5) {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="add_attendance.php">Attendance</a>
        </li>
        <!--      
      <li class="nav-item">
        <a class="nav-link" href="attendance.php">Attendance Report</a>
      </li> -->

      <li class="nav-item <?php if ($page == 6) {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="finalreport.php">Report</a>
        </li>

      </ul>
    </div>
    <li class="nav-link ">
      <a class="nav-item text-white" title="log out button" href="logout.php"> Logout <i class="fa fa-power-off" aria-hidden="true"></i>
      </a>
    </li>


  </nav>