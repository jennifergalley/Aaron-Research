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
        $numQuestions = count($test["Block"][$block]);
        
        $offset = ($block-1) * $numQuestions;
        
        $numCorrect = $numWrong = $totalNum = 0;
        $correct125 = $correct200 = $wrong125 = $wrong200 = 0;
        $total125 = $total200 = $score125 = $score200 = 0;
        $numCorrect125 = $numCorrect200 = $numWrong125 = $numWrong200 = 0;
        $correct = $wrong = $total = 0;
        $score = $missed = 0;
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
            
            $timeout = $_POST["rt".$i] >= 750; //timed out
            if ($correctAnswers[$i + $offset] == $questions[$i]["answer"]) {
                $numCorrect = $timeout ? $numCorrect : $numCorrect+1; //to compute average - know what to divide by
                $correct += $timeout ? 0 : $_POST["rt".$i]; //add response time to compute average
                
                $questions[$i]["correct"] = "true";   
                $score++;
            } else {
                if ($test["Block"][$j][$i]["tone"] == "" and $questions[$i]["answer"] == "no response") {
                    $missed++;
                }
                if ($test["Block"][$j][$i]["tone"] == "200") {
                    $wrong200 += $timeout ? 0 : $_POST["rt".$i]; //add response time to compute average
                    $numWrong200 = $timeout ? $numWrong200 : $numWrong200+1; 
                } else if ($test["Block"][$j][$i]["tone"] == "125") {
                    $wrong125 += $timeout ? 0 : $_POST["rt".$i]; //add response time to compute average
                    $numWrong125 = $timeout ? $numWrong125 : $numWrong125+1; 
                }
                $numWrong = $timeout ? $numWrong : $numWrong+1; //to compute average - know what to divide by
                $wrong += $timeout ? 0 : $_POST["rt".$i]; //add response time to compute average
                $questions[$i]["correct"] = "false";   
            }
            
            $questions[$i]["response time"] = $timeout ? "0" : $_POST["rt".$i];
            $total += $timeout ? 0 : $_POST["rt".$i];
            $totalNum = $timeout ? $totalNum : $totalNum+1; 
        }
        
        $avgCorrect = round($correct/$numCorrect, 2);
        
        $avgWrong = round($wrong/$numWrong, 2);
        $avgWrong125 = round($wrong125 / $numWrong125, 2);
        $avgWrong200 = round($wrong200 / $numWrong200, 2);
        
        $avgTotal = round($total/$totalNum, 2);
        
        $results["Score"]["total"] = $score;
        $results["Score"]["125"] = $score125;
        $results["Score"]["200"] = $score200;
        
        $results["Missed"] = $missed;
        $results["Questions"] = $questions;
        
        $results["Average Correct"] = $avgCorrect;
        
        $results["Average Wrong"]["total"] = $avgWrong;
        $results["Average Wrong"]["125"] = $avgWrong125;
        $results["Average Wrong"]["200"] = $avgWrong200;
        
        $results["Average Total"] = $avgTotal;
    
        $arr[$_GET['key']]["Block"][$block] = $results;
        encodeJSON ($soundResponses, $arr);
    }
?>