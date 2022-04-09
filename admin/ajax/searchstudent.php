<?php
include '../../include/connection.php';
include('../database_connection.php');
session_start();

$search = $_GET["search"];
 $query = ' SELECT * FROM tbl_student 
LEFT JOIN tbl_attendance 
ON tbl_attendance.student_id = tbl_student.student_id 
INNER JOIN tbl_grade 
ON tbl_grade.grade_id = tbl_student.student_grade_id 
INNER JOIN tbl_teacher 
ON tbl_teacher.teacher_grade_id = tbl_grade.grade_id  
WHERE tbl_student.student_name LIKE "%' .$search. '%" 
|| tbl_student.student_roll_number LIKE "%' . $search. '%" 
|| tbl_grade.grade_name LIKE "%' . $search. '%" 
|| tbl_teacher.teacher_name LIKE "%' . $search. '%" 
&& '.$_SESSION['condition'].'
LIMIT 50 ';
$result = mysqli_query($connection, $query);

?>


<?php
$s_no=1;
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