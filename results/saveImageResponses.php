<?php require_once ("../config/global.php"); 
    session_start();?>
<title>Test</title>
<?php
    $arr = decodeJSON($imageResponses);
    $results = array ();
    
    // Just Saving the Record 
    if (isset($_GET['participant'])) {
        $results["date"] = date("m-d-y h:i:s a");
        $results["type"] = $_GET['typeTest'];
        $results["participant"] = $_GET['participant'];
        $arr[$_GET['key']] = $results;
        encodeJSON ($imageResponses, $arr); ?>
        
        <!--Redirect to Test -->
        <script type="text/javascript">
            window.location = "<?php echo $subdir.'test/imageTest.php?record&type='.$_GET['typeTest']; ?>";
        </script>
<?php
    }
    
    
    // Saving the Results 
    else {
        $tests = decodeJSON ($imageTests);
        $test = $tests[$_GET['typeTest']];
        $correctAnswers = $test["Right Answers"];

        $block = $_GET['block'];
        $numQuestions = count($test["Block"][$block]) - 1;
        
        $offset = ($block-1) * $numQuestions;
        
        $total = $correct = $wrong = $score = $numWrong = 0;
        $first40score = $second20score = 0;
        $questions = array ();
        
        for ($i = 1; $i <= $numQuestions; $i++) {
            $questions[$i] = array ();
            
            if ($_POST["r".$i] == 39) { //right
                $questions[$i]["answer"] = "right"; 
            } elseif ($_POST["r".$i] == 37) { //left
                $questions[$i]["answer"] = "left";
            } else { //was saving participant as 'no response'
                $questions[$i]["answer"] = "no response"; //timed out
            }
            
            if ($correctAnswers[$i + $offset] == $questions[$i]["answer"]) { //got it correct
                $correct += $_POST["rt".$i]; //add response time to compute average
                
                if ($i <= 40) {
                    $first40score++;
                } else {
                    $second20score++;
                }
                
                $questions[$i]["correct"] = "true";   
                $score++; //increment their score
            } else { //got it wrong
                $numWrong++; //to compute average - know what to divide by
                $wrong += $_POST["rt".$i]; //add response time to compute average
                
                $questions[$i]["correct"] = "false";   
            }
        
            $total += $_POST["rt".$i]; //total response time
            $questions[$i]["response time"] = $_POST["rt".$i]."ms";
        }
        
        $results["Score First 40"] = $first40score;
        $results["Score Second 20"] = $second20score;
        
        $results["Score"] = $score;
        
        $avgCorrect = round($correct/$score, 2);
        $avgWrong = round($wrong/$numWrong, 2);
        $avg = round($total/$numQuestions, 2);
        
        $results["Average Correct"] = $avgCorrect."ms";
        $results["Average Wrong"] = $avgWrong."ms";
        $results["Average Total"] = $avg."ms";
        
        $results["Questions"] = $questions;
        
        $arr[$_GET['key']]["Block"][$block] = $results;
        
        encodeJSON ($imageResponses, $arr); 
    }
?>