<?php require_once ("../config/global.php"); 
    session_start();?>
<title>Test</title>
<?php
    $arr = decodeJSON($soundResponses);
    $results = array ();
    $results["date"] = date("m-d-y h:i:s a");
    $results["participant"] = $_GET['participant'];
    $results["test version"] = $_GET['testVersion'];
    $correctAnswers = decodeJSON ($soundTests);
    $correctAnswers = $correctAnswers[$_GET['testVersion']]["Right Answers"];
    
    $numCorrect = $numWrong = $totalNum = 0;
    $correct = $wrong = $total = 0;
    $score = 0;
    $questions = array ();
    $numQuestions = count($correctAnswers);
    for ($i = 1; $i <= $numQuestions; $i++) {
        $questions[$i] = array ();
        
        if ($_GET[$i] == 39) { //right
            $questions[$i]["answer"] = "right"; 
        } elseif ($_GET[$i] == 37) { //left
            $questions[$i]["answer"] = "left";
        } else { //was saving participant as 'no response'
            $questions[$i]["answer"] = "no response"; //timed out
        }
        
        $timeout = $_GET[$i."_time"] > 1000; //timed out after 1 second
        if ($correctAnswers[$i] == $questions[$i]["answer"]) {
            $numCorrect = $timeout ? $numCorrect : $numCorrect+1; //to compute average - know what to divide by
            $correct += $timeout ? 0 : $_GET[$i."_time"]; //add response time to compute average
            $questions[$i]["correct"] = "true";   
            $score++;
        } else {
            $numWrong = $timeout ? $numWrong : $numWrong+1; //to compute average - know what to divide by
            $wrong += $timeout ? 0 : $_GET[$i."_time"]; //add response time to compute average
            $questions[$i]["correct"] = "false";   
        }
        
        $questions[$i]["response time"] = $timeout ? "0ms" : $_GET[$i."_time"]."ms";
        $total += $timeout ? 0 : $_GET[$i."_time"];
        $totalNum = $timeout ? $totalNum : $totalNum+1; 
    }
    $avgCorrect = round($correct/$numCorrect, 2);
    $avgWrong = round($wrong/$numWrong, 2);
    $avg = round($total/$totalNum, 2);
    $results["Score"] = $score." out of ".$numQuestions;
    $results["Questions"] = $questions;
    $results["Average Correct"] = $avgCorrect."ms";
    $results["Average Wrong"] = $avgWrong."ms";
    $results["Average Total"] = $avg."ms";
    $arr[] = $results;
    encodeJSON ($soundResponses, $arr);
?>
<script type="text/javascript">
    window.location = "<?php echo $subdir.'test/soundTest.php?done';?>";
</script>