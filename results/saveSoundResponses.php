<?php require_once ("../config/global.php"); 
    session_start();?>
<title>Test</title>
<?php
    $arr = decodeJSON($soundResponses);
    $results = array ();
    $results["date"] = date("m-d-y h:i:s a");
    $results["type"] = $_GET['typeTest'];
    $results["participant"] = $_GET['participant'];
    $tests = decodeJSON ($soundTests);
    $test = $tests["test"];
    $correctAnswers = $test["Right Answers"];
    $numBlocks = count($test["Block"]);
    
    $blocks = array ();
    $offset = 0;
    
    for ($j = 1; $j <= $numBlocks; $j++) {
        $numQuestions = count($test["Block"][$j]);
        $numCorrect = $numWrong = $totalNum = 0;
        $correct125 = $correct200 = $wrong125 = $wrong200 = 0;
        $total125 = $total200 = $score125 = $score200 = 0;
        $numCorrect125 = $numCorrect200 = $numWrong125 = $numWrong200 = 0;
        $correct = $wrong = $total = 0;
        $score = $missed = 0;
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
            
            $timeout = $_GET[$k."_time"] > 750; //timed out
            if ($correctAnswers[$k] == $questions[$i]["answer"]) {
                $numCorrect = $timeout ? $numCorrect : $numCorrect+1; //to compute average - know what to divide by
                $correct += $timeout ? 0 : $_GET[$k."_time"]; //add response time to compute average
                $questions[$i]["correct"] = "true";   
                $score++;
            } else {
                if ($test["Block"][$j][$i]["tone"] == "" and $questions[$i]["answer"] == "no response") {
                    $missed++;
                }
                if ($test["Block"][$j][$i]["tone"] == "200") {
                    $wrong200 += $timeout ? 0 : $_GET[$k."_time"]; //add response time to compute average
                    $numWrong200 = $timeout ? $numWrong200 : $numWrong200+1; 
                } else if ($test["Block"][$j][$i]["tone"] == "125") {
                    $wrong125 += $timeout ? 0 : $_GET[$k."_time"]; //add response time to compute average
                    $numWrong125 = $timeout ? $numWrong125 : $numWrong125+1; 
                }
                $numWrong = $timeout ? $numWrong : $numWrong+1; //to compute average - know what to divide by
                $wrong += $timeout ? 0 : $_GET[$k."_time"]; //add response time to compute average
                $questions[$i]["correct"] = "false";   
            }
            
            $questions[$i]["response time"] = $timeout ? "0ms" : $_GET[$k."_time"]."ms";
            $total += $timeout ? 0 : $_GET[$k."_time"];
            $totalNum = $timeout ? $totalNum : $totalNum+1; 
        }
        $avgCorrect = round($correct/$numCorrect, 2);
        
        $avgWrong = round($wrong/$numWrong, 2);
        $avgWrong125 = round($wrong125 / $numWrong125, 2);
        $avgWrong200 = round($wrong200 / $numWrong200, 2);
        
        $avgTotal = round($total/$totalNum, 2);
        
        $results["Block"][$j]["Score"]["total"] = $score;
        $results["Block"][$j]["Score"]["125"] = $score125;
        $results["Block"][$j]["Score"]["200"] = $score200;
        
        $results["Block"][$j]["Missed"] = $missed;
        $results["Block"][$j]["Questions"] = $questions;
        
        $results["Block"][$j]["Average Correct"] = $avgCorrect."ms";
        
        $results["Block"][$j]["Average Wrong"]["total"] = $avgWrong."ms";
        $results["Block"][$j]["Average Wrong"]["125"] = $avgWrong125."ms";
        $results["Block"][$j]["Average Wrong"]["200"] = $avgWrong200."ms";
        
        $results["Block"][$j]["Average Total"] = $avgTotal."ms";
        
        $offset += $numQuestions;
    }
    
    $arr[] = $results;
    encodeJSON ($soundResponses, $arr);
    
?>
<script type="text/javascript">
    window.location = "<?php echo $results["type"] == "test" ? $subdir.'test/soundTest.php?done' : $subdir.'test/soundTest.php?pdone'; ?>";
</script>