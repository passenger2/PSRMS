<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
$post = $_POST; //$key for this array is in the format x-y where x = answertype & y = questionID
$temp_answertype = ""; //temporary answertype variable
$temp_qid = "";   //temporary id variable
$query = "";

$db_handle->runUpdate("INSERT INTO `form_answers` (`FORM_ANSWERS_ID`, `USER_UserID`, `FORM_FormID`, `DateTaken`, `IDP_IDP_ID`) VALUES (NULL, '".$_SESSION['userID']."', '".$_SESSION['assessment_tool_ID']."', CURRENT_TIMESTAMP, ".$_SESSION['idpID'].")");
$idp_form_answers_id = $db_handle->getLastInsertID();

foreach($post as $key => $answers) {
    $temp_answertype = substr($key, 0, 1);
    $temp_qid = substr($key, 2);
    if($temp_answertype === '1') {
        $query .= "INSERT INTO `answers_quanti` (`ANSWERS_QUANTI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES ( NULL, '".$answers."', '".$temp_qid."', '".$idp_form_answers_id."', NULL);";
    } else if($temp_answertype === '2') {
        $query .= "INSERT INTO `answers_quali` (`ANSWERS_QUALI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANSWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES ( NULL, '".$answers."', '".$temp_qid."', '".$idp_form_answers_id."', NULL);";
    }
    $temp_answertype = "";
    $temp_qid = "";
}
$db_handle->runUpdate($query);
if($db_handle->getUpdateStatus()) {
    echo "<script type='text/javascript'>alert('Answers submitted!');
    location='".$_SESSION['apply_previous']."';
    </script>";
}
?>