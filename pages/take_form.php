<?php include ('head.php'); ?>
<?php include ('footer.php'); ?>
<?php
    require('check_credentials.php');
    require_once("dbcontroller.php");
    include("css_include.php");
    include("core_include.php");
    $formID = $_SESSION['assessment_tool_ID'];
    $idpID = $_SESSION['idpID'];
    $idp_name = $_SESSION['idp_name'];
    $userID = $_SESSION['userID'];
    $db_handle = new DBController();
    $form_info = $db_handle->runFetch("SELECT * FROM `form` WHERE FormID = ".$formID);
    $questions = $db_handle->runFetch("SELECT * FROM `questions` WHERE FORM_FormID = ".$formID);
    $html_forms = $db_handle->runFetch("SELECT * FROM `html_form` WHERE 1");
?>
    <style>
        .container-fluid {
            margin-left: 20%;
            margin-right: 20%;
        }

        #btn-submit-form{
            margin-left: 40px;
            margin-top: 50px;
        }
    </style>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary" style="margin-top: 50px;">
                      <div class="panel-heading text-center"><h2><?php if(!empty($form_info)) { foreach ($form_info as $info) { echo($info['FormType']); }} ?></h2></div>
                      <div class="panel-body" style="padding: 20px; 50px;">
                        <h3 style="margin: 10px 40px;">
                            Current IDP: <b><?php echo ($idp_name); ?></b><br>
                            IDP ID: <b><?php echo ($idpID); ?></b>
                        </h3>
                        <h4 style="margin: 20px 40px;"><b>Instructions: </b><?php if(!empty($form_info)) { foreach ($form_info as $info) { echo($info['Instructions']); }} ?></h4>
                        <div style="margin: 30px 40px;" class="header"><h3><b>Questions:</b></h3></div>
                        <form action="submit_answers.php" method="post" onsubmit="return check_empty()">
                            <?php
                            if(!empty($questions)) {
                                foreach ($questions as $question) {
                            ?>
                            <table align="center" cellspacing="3" cellpadding="3" width="90%" class=" table-responsive">
                                <tr>
                                    <td align="left" style="width:90%" name="no">
                                        <h4>
                                            <?php echo($question['Question']); ?>
                                        </h4>
                                    </td>   
                                </tr>
                                <tr name="preview-wrapper" style="margin-bottom: 30px;">
                                    <td id="preview-wrapper<?php echo($question['QuestionsID']); ?>" >
                                        <?php 
                                            $outputArray = array();
                                            //if $question['HTML_FORM_HTML_FORM_ID'] exists, create these elements
                                            if(isset($question['HTML_FORM_HTML_FORM_ID'])) {
                                                $array_iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($html_forms));
                                                //find html form row corresponding with $question['HTML_FORM_HTML_FORM_ID']
                                                foreach ($array_iterator as $sub) {
                                                    $subArray = $array_iterator->getSubIterator();
                                                        if ($subArray['HTML_FORM_ID'] === $question['HTML_FORM_HTML_FORM_ID']) {
                                                        $outputArray[] = array_values(iterator_to_array($subArray));
                                                    }
                                                }
                                                $qid_form_range[] = $outputArray[0]; //will be used for setting default dropdown values in 
                                                echo '<fieldset id="q-a-'.$question['QuestionsID'].'">';
                                                $formRange = $outputArray[0][2];
                                                $formType = $outputArray[0][1];
                                                if($outputArray[0][2] !== null) { //if formRange is not null. It means html form is either checkbox or radio
                                                    //html_form inline echo loop
                                                    for($i = 0; $i < $formRange; $i++) {
                                                        echo'<label class="'.$formType.'-inline"><input type="'.$formType.'" name="'.$question['AnswerType'].'-'.$question['QuestionsID'].'" value="'.$i.'">'.$i.'</label>';
                                                    }

                                                } else {
                                                    if($formType === "textarea") {
                                                        echo '<textarea class="form-control" rows="5" id="comment" name="'.$question['AnswerType'].'-'.$question['QuestionsID'].'"></textarea>';
                                                    } else if($formType === "text") {
                                                        echo '<input class="form-control" id="inputdefault" type="'.$formType.'" name="'.$question['AnswerType'].'-'.$question['QuestionsID'].'">';
                                                    }
                                                }
                                                echo '</fieldset>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <?php
                                }
                            } else { ?>
                            <table align="center" cellspacing="3" cellpadding="3" width="90%" class="table-responsive">
                                <tr>

                                    <td align="left">
                                        <h4>No questions for this form yet!</h4>
                                    </td>

                                </tr>
                            </table>
                            <?php
                            }
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <button id="btn-submit-form" class="btn btn-primary btn-lg" type="submit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                                </div>   
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <script type="text/javascript" src="../js/validate-input.js"></script>
