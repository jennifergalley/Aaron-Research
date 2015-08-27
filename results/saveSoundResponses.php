<?php require_once ("../config/global.php"); 
    session_start();?>
<title>Test</title>
<?php
    $arr = decodeJSON($soundResponses);
    $results = array ();
    
    // Just Saving the Record 
    if (isset($_GET['participant'])) {
        $results["date"] = date("m-d-y h:i:s a");
        $results["type"] = $_GET['typeTest'];
        $results["participant"] = $_GET['participant'];
        $arr[$_GET['key']] = $results;
        encodeJSON ($soundResponses, $arr); ?>
        
        <!--Redirect to Test -->
        <script type="text/javascript">
            window.location = "<?php echo $subdir.'test/soundTest.php?record&type='.$_GET['typeTest']; ?>";
        </script>
<?php
    }
    
    
    // Saving the Results 
    else {
        $tests = decodeJSON ($soundTests);
        $test = $tests[$_GET['typeTest']];
        $correctAnswers = $test["Right Answers"];
        
        $block = $_GET['block'];
        $numQuestions = count($test["Block"]["$block"]);
        
        $offset = ($block - 1) * $numQuestions;
        
        $score = $score125 = $score200 = 0;
        $missed = 0;
        
        $RTcorrectSum = $RTwrongSum = $RTtotalSum = 0;
        $numCorrectResponses = $numWrongResponses = $numTotalResponses = 0;
        
        $RTwrong125Sum = $RTwrong200Sum = 0;
        $numWrong125 = $numWrong200 = 0;
        
        $questions = array ();
        
        for ($i = 1; $i <= $numQuestions; $i++) {
            $question = array ();
            
            $tone = $test["Block"][$block][$i]["tone"];
            $responseTime = $_POST["rt".$i];
            $response = $_POST["r".$i];
            
            $responded = true;
            
            //Get the response
            switch ($response) {
                case 37: //left
                    $question["answer"] = "left";
                    break;
                case 39: //right
                    $question["answer"] = "right";
                    break;
                default: //anything else
                    $question["answer"] = "no response";
                    $responded = false;
                    break;
            }
            
            //Save the response time, if they responded
            $question["response time"] = $responded ? $responseTime : "0";
            
            //If they got it right
            if ($correctAnswers[$i + $offset] == $question["answer"]) { //it's getting the correct index, and correct answers is fine
                $question["correct"] = "true";   
                $score++;
                
                //if they responded, get average response time
                if ($responded) {
                    $numCorrectResponses++; //to compute average - know what to divide by
                    $RTcorrectSum += $responseTime; //add response time to compute average
                }
                        
                //If there was a sound, increment score for that delay
                switch($tone) {
                    case "125": //125 ms delay
                        $score125++;
                        break;
                    case "200": //200 ms delay
                        $score200++;
                        break;
                    default: //no sound
                        break;
                }
            }
            
            //If they got it wrong
            else {
                $question["correct"] = "false";  
                
                switch ($tone) {
                    case "":
                        //No sound and they didn't respond
                        if (!$responded) {
                            $missed++; //first of all, this logic is definitely wrong.
                        }
                        break;
                        
                    case "125":
                        //Sound with 125 ms delay - they got it wrong so we know they responded, get average response time
                        $numWrong125++; //to compute average - know what to divide by
                        $RTwrong125Sum += $responseTime; //add response time to compute average
                        break;
                        
                    case "200":
                        //Sound with 200 ms delay - they got it wrong so we know they responded, get average response time
                        $numWrong200++; //to compute average - know what to divide by
                        $RTwrong200Sum += $responseTime; //add response time to compute average
                        break;
                }
                
                //Total wrong - if they responded, get average response time
                if ($responded) {
                    $numWrongResponses++; //to compute average - know what to divide by
                    $RTwrongSum += $responseTime; //add response time to compute average
                }
            }
            
            //Total Average Response time
            if ($responded) {
                $numTotalResponses++; //to compute average - know what to divide by
                $RTtotalSum += $responseTime; //add response time to compute average
            }
            
            //Save the question - response, correct, response time
            $questions[$i] = $question;
        }
        
        //Score
        $results["Score"]["total"] = $score;
        $results["Score"]["125"] = $score125;
        $results["Score"]["200"] = $score200;
        
        //Number of questions without a sound with no response
        $results["Missed"] = $missed;
        
        //Each question result - answer, correct, response time
        $results["Questions"] = $questions;
        
        //Response Times
        
        //Average Response time for correct responses (when there was a response)
        $results["Average Correct"] = round($RTcorrectSum / $numCorrectResponses, 2);
        
        //Average Response time for incorrect responses (when there was a response), divided by sound delay
        $results["Average Wrong"]["total"] = round($RTwrongSum / $numWrongResponses, 2);
        
        $results["Average Wrong"]["125"] = round($RTwrong125Sum / $numWrong125, 2);
        
        $results["Average Wrong"]["200"] = round($RTwrong200Sum / $numWrong200, 2);
        
        //Average Response time total (when there was a response)
        $results["Average Total"] = round($RTtotalSum / $numTotalResponses, 2);
    
        $arr[$_GET['key']]["Block"][$block] = $results;
        encodeJSON ($soundResponses, $arr);
    }
?>