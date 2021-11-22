<?php  
    if(isset($_POST["from_date"])){
        $from_date = $_POST["from_date"];
        $to_date = $_POST["to_date"];
        $withGrade = $_POST["withGrade"];
        $servername = "localhost";
        $username = "nsunivac_attendance";
        $password = "4Ckt6FML9";
        $dbname = "nsunivac_attendance";
        $con = new mysqli($servername, $username, $password, $dbname);
        if($withGrade == "all")
            $sql = "SELECT * 
                    FROM `tbl_attendance`
                    WHERE `tbl_attendance`.`attendance_date` >= '$from_date'  && `tbl_attendance`.`attendance_date` <= '$to_date'
                    ";  
        else
            $sql = "SELECT * 
                    FROM `tbl_attendance`
                    WHERE `tbl_attendance`.`attendance_date` >= '$from_date'  && `tbl_attendance`.`attendance_date` <= '$to_date' && `grade_id` = '$withGrade'
                    ";  
        $result = $con->query($sql);
        $columnHeader = '';  
        $columnHeader = "Attendance Date". "\t" ."Grade". "\t" ."Roll Number". "\t" ."Student Name"."\t" ."Attendance Status"; 
        $setData = '';  
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rowData .= date("d-m-Y", strtotime($row["attendance_date"]))."\t";
                $sqlGrade = "SELECT * 
                            FROM `tbl_grade`
                            WHERE `tbl_grade`.`grade_id` >= '".$row["grade_id"]."'
                            ";  
                $resultGrade = $con->query($sqlGrade);
                $rowGrade = $resultGrade->fetch_assoc();
                $rowData .= $rowGrade["grade_name"]."\t";
                $sqlStudent = "SELECT * 
                              FROM `tbl_student`
                              WHERE `tbl_student`.`student_id` >= '".$row["student_id"]."'
                              ";  
                $resultStudent = $con->query($sqlStudent);
                $rowStudent = $resultStudent->fetch_assoc();
                $rowData .= $rowStudent["student_roll_number"]."\t";
                $rowData .= $rowStudent["student_name"]."\t";
                $rowData .= $row["attendance_status"]."\t";
                $setData .= trim($rowData) . "\n";  
                $rowData = "";
            }   
        } 
        if($from_date == $to_date)
            $fileName = "Attendance-Report-".date("d-m-Y", strtotime($from_date));
        else
            $fileName = "Attendance-Report-From-".date("d-m-Y", strtotime($from_date))."-To-".date("d-m-Y", strtotime($to_date));
        header("Content-type: application/octet-stream");  
        header("Content-Disposition: attachment; filename=$fileName.xls");
        header("Pragma: no-cache");  
        header("Expires: 0");
        echo ucwords($columnHeader) . "\n" . $setData . "\n"; 
    }
 ?> 
 