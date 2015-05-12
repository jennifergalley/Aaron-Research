<?php require_once ("../config/global.php"); 
    session_start();?>
<title>Test</title>
<?php
    $arr = decodeJSON($imageResponses);
    $results = array ();
    $results["date"] = date("m-d-y h:i:s a");
    $results["participant"] = $_GET['participant'];
    $tests = decodeJSON ($imageTests);
    $test = $tests["test"];
    $correctAnswers = $test["Right Answers"];
    $numBlocks = count($test["Block"]);
    
    $blocks = array ();
    $offset = 0;
    
    for ($j = 1; $j <= $numBlocks; $j++) {
        $numQuestions = count($test["Block"][$j]);
        $total = $correct = $wrong = $score = $numWrong = 0;
        $first40score = $second20score = 0;
        $questions = array ();
        
        for ($i = 1; $i <= $numQuestions; $i++) {
            $k = $i + $offset;
            $questions[$i] = array ();
            
            if ($_GET[$k] == 39) { //right
                $questions[$i]["answer"] = "right"; 
            } elseif ($_GET[$k] == 37) { //left
                $questions[$i]["answer"] = "left";
            } else { //was saving participant as 'no response'
                $questions[$i]["answer"] = "no response"; //timed out
            }
            
            if ($correctAnswers[$k] == $questions[$i]["answer"]) { //got it correct
                $correct += $_GET[$k."_time"]; //add response time to compute average
                
                if ($i <= 40) {
                    $first40score++;
                } else {
                    $second20score++;
                }
                
                $questions[$i]["correct"] = "true";   
                $score++; //increment their score
            } else { //got it wrong
                $numWrong++; //to compute average - know what to divide by
                $wrong += $_GET[$k."_time"]; //add response time to compute average
                
                $questions[$i]["correct"] = "false";   
            }
        
            $total += $_GET[$k."_time"]; //total response time
            $questions[$i]["response time"] = $_GET[$k."_time"]."ms";
        }
        
        $results["Block"][$j]["Score First 40"] = $first40score;
        $results["Block"][$j]["Score Second 20"] = $second20score;
        
        $results["Block"][$j]["Score"] = $score;
        
        $avgCorrect = round($correct/$score, 2);
        $avgWrong = round($wrong/$numWrong, 2);
        $avg = round($total/$numQuestions, 2);
        
        $results["Block"][$j]["Average Correct"] = $avgCorrect."ms";
        $results["Block"][$j]["Average Wrong"] = $avgWrong."ms";
        $results["Block"][$j]["Average Total"] = $avg."ms";
        
        $results["Block"][$j]["Questions"] = $questions;
        
        $offset += $numQuestions;
    }
    
    $arr[] = $results;
    encodeJSON ($imageResponses, $arr);  
?>
<script type="text/javascript">
    window.location = "<?php echo $subdir.'test/imageTest.php?done';?>";
</script>