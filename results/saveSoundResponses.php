<?php require_once ("../config/global.php"); 
    session_start();?>
<title>Test</title>
<?php
    $arr = decodeJSON($soundResponses);
    $results = array ();
    $results["date"] = date("m-d-y h:i:s a");
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
            
            $timeout = $_GET[$k."_time"] > 1000; //timed out after 1 second
            if ($correctAnswers[$k] == $questions[$i]["answer"]) {
                $numCorrect = $timeout ? $numCorrect : $numCorrect+1; //to compute average - know what to divide by
                $correct += $timeout ? 0 : $_GET[$k."_time"]; //add response time to compute average
                $questions[$i]["correct"] = "true";   
                $score++;
            } else {
                if ($test["Block"][$j][$i]["tone"] == "" and $questions[$i]["answer"] == "no response") {
                    $missed++;
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
        $avg = round($total/$totalNum, 2);
        
        $results["Block"][$j]["Score"] = $score;
        $results["Block"][$j]["Missed"] = $missed;
        $results["Block"][$j]["Questions"] = $questions;
        $results["Block"][$j]["Average Correct"] = $avgCorrect."ms";
        $results["Block"][$j]["Average Wrong"] = $avgWrong."ms";
        $results["Block"][$j]["Average Total"] = $avg."ms";
        $offset += $numQuestions;
    }
    
    $arr[] = $results;
    encodeJSON ($soundResponses, $arr);
?>
<script type="text/javascript">
    window.location = "<?php 
        if ($_GET['all'] == "0") 
            echo $subdir.'test/soundTest.php?done';
        else 
            echo $subdir.'test/imageTest.php?n='.$_GET['participant']; ?>";
</script>