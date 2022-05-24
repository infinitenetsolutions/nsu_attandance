
<?php $connection = '';
if($_SERVER['HTTP_HOST']=="localhost"){
	$connection = 	mysqli_connect("localhost","root","" ,"srinath_atteandance");
}
else{
    $connection = 	mysqli_connect("localhost","nsunivac_attendance","4Ckt6FML9","nsunivac_attendance");  
}

?>