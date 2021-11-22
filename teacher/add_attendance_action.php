<?php

//attendance_action.php

include('database_connection.php');

session_start();

if(isset($_POST["action"]))
{
	if($_POST["action"] == "fetch" && !empty($_GET["grade"]))
	{
		$query = "
		SELECT * FROM tbl_attendance 
		INNER JOIN tbl_student 
		ON tbl_student.student_id = tbl_attendance.student_id 
		INNER JOIN tbl_grade 
		ON tbl_grade.grade_id = tbl_student.student_grade_id 
		WHERE tbl_attendance.grade_id = ".$_GET["grade"]." AND (
		";

		if(isset($_POST["search"]["value"]))
		{
			$query .= '
			tbl_student.student_name LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_student.student_roll_number LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_attendance.attendance_status LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_attendance.attendance_date LIKE "%'.$_POST["search"]["value"].'%") 
			';
		}
		if(isset($_POST["order"]))
		{
			$query .= '
			ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' 
			';
		}
		else
		{
			$query .= '
			ORDER BY tbl_attendance.attendance_id DESC 
			';
		}

		if($_POST["length"] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$data = array();
		$filtered_rows = $statement->rowCount();
		foreach($result as $row)
		{
			$sub_array = array();
			$status = '';
			if($row["attendance_status"] == "Present")
			{
				$status = '<label class="badge badge-success">Present</label>';
			}

			if($row["attendance_status"] == "Absent")
			{
				$status = '<label class="badge badge-danger">Absent</label>';
			}

			$sub_array[] = $row["student_name"];
			$sub_array[] = $row["student_roll_number"];
			$sub_array[] = $row["grade_name"];
			$sub_array[] = $status;
			$sub_array[] = $row["attendance_date"];
			$data[] = $sub_array;
		}

		$output = array(
			'draw'				=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	$filtered_rows,
			"recordsFiltered"	=>	get_total_records($connect, 'tbl_attendance'),
			"data"				=>	$data
		);

		echo json_encode($output);
	} else if($_POST["action"] == "fetch" && empty($_GET["grade"]))
	{
		$query = "
		SELECT * FROM tbl_attendance 
		INNER JOIN tbl_student 
		ON tbl_student.student_id = tbl_attendance.student_id 
		INNER JOIN tbl_grade 
		ON tbl_grade.grade_id = tbl_student.student_grade_id 
		";

		if(isset($_POST["search"]["value"]))
		{
			$query .= '
			WHERE tbl_student.student_name LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_student.student_roll_number LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_attendance.attendance_status LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_attendance.attendance_date LIKE "%'.$_POST["search"]["value"].'%"
			';
		}
		if(isset($_POST["order"]))
		{
			$query .= '
			ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' 
			';
		}
		else
		{
			$query .= '
			ORDER BY tbl_attendance.attendance_id DESC 
			';
		}

		if($_POST["length"] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$data = array();
		$filtered_rows = $statement->rowCount();
		foreach($result as $row)
		{
			$sub_array = array();
			$status = '';
			if($row["attendance_status"] == "Present")
			{
				$status = '<label class="badge badge-success">Present</label>';
			}

			if($row["attendance_status"] == "Absent")
			{
				$status = '<label class="badge badge-danger">Absent</label>';
			}

			$sub_array[] = $row["student_name"];
			$sub_array[] = $row["student_roll_number"];
			$sub_array[] = $row["grade_name"];
			$sub_array[] = $status;
			$sub_array[] = $row["attendance_date"];
			$data[] = $sub_array;
		}

		$output = array(
			'draw'				=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	$filtered_rows,
			"recordsFiltered"	=>	get_total_records($connect, 'tbl_attendance'),
			"data"				=>	$data
		);

		echo json_encode($output);
	}

	if($_POST["action"] == "Add")
	{
	    $grade_id = 0;
        if(!empty($_GET["grade"]))
            $grade_id = $_GET["grade"];
		$attendance_date = '';
		$error_attendance_date = '';
		$error = 0;
		if(empty($_POST["attendance_date"]))
		{
			$error_attendance_date = 'Attendance Date is required';
			$error++;
		}
		else
		{
			$attendance_date = $_POST["attendance_date"];
		}

		if($error > 0)
		{
			$output = array(
				'error'							=>	true,
				'error_attendance_date'			=>	$error_attendance_date
			);
		}
		else
		{
			$student_id = $_POST["student_id"];
			$query = '
			SELECT attendance_date FROM tbl_attendance 
			WHERE grade_id = "'.$grade_id.'" 
			AND attendance_date = "'.$attendance_date.'"
			';
			$statement = $connect->prepare($query);
			$statement->execute();
			if($statement->rowCount() > 0)
			{
				$output = array(
					'error'					=>	true,
					'error_attendance_date'	=>	'Attendance Data Already Exists on this date'
				);
			}
			else
			{
				for($count = 0; $count < count($student_id); $count++)
				{
					$data = array(
						':student_id'			=>	$student_id[$count],
						':attendance_status'	=>	$_POST["attendance_status".$student_id[$count]],
						':attendance_date'		=>	$attendance_date,
						':grade_id'		        =>	$grade_id,
						':teacher_id'			=>	0
					);
                    if($_POST["attendance_status".$student_id[$count]] == "Absent"){
			            $sub_query = "
                          SELECT * FROM tbl_student 
			              WHERE student_id = ".$student_id[$count]."
                        ";
                        $statement = $connect->prepare($sub_query);
                        $statement->execute();
                        $student_result = $statement->fetchAll();
                        foreach($student_result as $student)
                        {
                            $mobileNumber = $student["parent_mob_no_1"];
                            $mobileNumber2 = $student["parent_mob_no_2"];
                            $message="Dear Sir/Madam,\n Your ward is ABSENT today.\n\n Regards Srinath University, \nJamshedpur. ";
                            $senderId="SUJSR";
                            $serverUrl="msg.msgclub.net";
                            $authKey="fbfdee58a904a1d82641561a74c354";
                            $route="1";
                            $postData = array(
                                'mobileNumbers' => $mobileNumber.','.$mobileNumber2,
                                'smsContent' => $message,
                                'senderId' => $senderId,
                                'routeId' => $routeId,
                                "smsContentType" =>'english'
                            );
                            $data_json = json_encode($postData);
                            $url="http://".$serverUrl."/rest/services/sendSMS/sendGroupSms?AUTH_KEY=".$authKey;
                            $ch = curl_init();
                            curl_setopt_array($ch, array(
                                CURLOPT_URL => $url,
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json','Content-Length: ' . strlen($data_json)),
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POST => true,
                                CURLOPT_POSTFIELDS => $data_json,
                                CURLOPT_SSL_VERIFYHOST => 0,
                                CURLOPT_SSL_VERIFYPEER => 0
                            ));
                        	$output = curl_exec($ch);
                        }
                    }
                    $query = "
    					INSERT INTO tbl_attendance 
    					(student_id, attendance_status, attendance_date, teacher_id, grade_id) 
    					VALUES (:student_id, :attendance_status, :attendance_date, :teacher_id, :grade_id)
					";
					$statement = $connect->prepare($query);
					$statement->execute($data);

				}
				$output = array(
					'success'		=>	'Data Added Successfully',
				);
			}
		}
		echo json_encode($output);
	}

	if($_POST["action"] == "index_fetch")
	{
		$query = "
		SELECT * FROM tbl_attendance 
		INNER JOIN tbl_student 
		ON tbl_student.student_id = tbl_attendance.student_id 
		INNER JOIN tbl_grade 
		ON tbl_grade.grade_id = tbl_student.student_grade_id 
		WHERE tbl_attendance.teacher_id = '".$_SESSION["teacher_id"]."' AND (
		";
		if(isset($_POST["search"]["value"]))
		{
			$query .= '
			tbl_student.student_name LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_student.student_roll_number LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_grade.grade_name LIKE "%'.$_POST["search"]["value"].'%" )
			';
		}
		$query .= 'GROUP BY tbl_student.student_id ';
		if(isset($_POST["order"]))
		{
			$query .= '
			ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' 
			';
		}
		else
		{
			$query .= '
			ORDER BY tbl_student.student_roll_number ASC 
			';
		}

		if($_POST["length"] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$data = array();
		$filtered_rows = $statement->rowCount();
		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = $row["student_name"];
			$sub_array[] = $row["student_roll_number"];
			$sub_array[] = $row["grade_name"];
			$sub_array[] = get_attendance_percentage($connect, $row["student_id"]);
			$sub_array[] = '<button type="button" name="report_button" id="'.$row["student_id"].'" class="btn btn-info btn-sm report_button">Report</button>';
			$data[] = $sub_array;
		}
		$output = array(
			'draw'					=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	$filtered_rows,
			"recordsFiltered"	=>	get_total_records($connect, 'tbl_student'),
			"data"				=>	$data
		);
		echo json_encode($output);
	}
}


?>