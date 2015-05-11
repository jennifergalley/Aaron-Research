<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);
    
    redirectToLogin();
    
    if (isset($_GET['del'])) {
        deleteResults ($_GET['del'], 'sound');
    } 
?>

<a href="<?php if (!empty($_GET['id'])) echo 'viewSoundResults.php'; else echo 'admin.php'; ?>" target="_self" class="back"><?php if (!empty($_GET['id'])) echo 'Test Results'; else echo 'Admin'; ?> &lt;&lt;</a>
 
<h1>Participant Results</h1>

<?php $results = decodeJSON($soundResponses); 
   $tests = decodeJSON ($soundTests); ?>
<?php if (!isset($_GET['id'])) : ?>
    <?php foreach ($results as $id => $r) : ?>
    <table class='view'>
        <tr>
            <td><b>Particpiant</b></td>
            <td><?php echo $r["participant"]; ?></td>
        </tr>
        <tr>
            <td><b>Test Block</b></td>
            <td><?php echo $r["test version"]; ?></td>
        </tr>
        <tr>
            <td><b>Date Taken</b></td>
            <td><?php echo $r["date"]; ?></td>
        </tr>
        <tr>
            <td><b>View This Result</b></td>
            <td><a href='?id=<?php echo $id; ?>'>View This Result</a></td>
        </tr>
    </table>
    <br>
    <hr>
    <br>
<?php 
    endforeach; 
    else : 
    $r = $results[$_GET['id']];
?>
    <table class='view'>
        <tr>
            <td><b>Particpiant</b></td>
            <td><?php echo $r["participant"]; ?></td>
        </tr>
        <tr>
            <td><b>Score</b></td>
            <td><?php echo $r["Score"]; ?></td>
        </tr>
        <tr>
            <td><b>Test Block</b></td>
            <td><?php echo $r["test version"]; ?></td>
        </tr>
        <tr>
            <td><b>Date Taken</b></td>
            <td><?php echo $r["date"]; ?></td>
        </tr>
        <tr>
            <td><b>Delete Results</b></td>
            <td><a id='delete' href="?del=<?php echo $_GET['id']; ?>" target="_self" 
                onclick="return confirm('Are you sure you want to delete this test result?');">Delete This Result</a></td>
        </tr>
    </table>
    
     <?php 
        $correct = $missed = 0;
        $RTCorrect = $RTIncorrect = $RTTotal = 0;
        $numBlocks = count($r["Block"]);
    
        foreach ($r["Block"] as $b => $block) : ?>
        <h2>Block <?php echo $b; ?></h2>
        <table class='view'>
            <tr>
                <td><b>Correct</b></td>
                <td><?php $correct += $block["Score"]; echo $block["Score"]; ?></td>
            </tr>
            <tr>
                <td><b>Missed</b></td>
                <td><?php $missed += $block["Missed"]; echo $block["Missed"]; ?></td>
            </tr>
            <tr>
                <td><b>RT Correct</b></td>
                <td><?php $RTCorrect += $block["Average Correct"]; echo $block["Average Correct"]; ?></td>
            </tr>
            <tr>
                <td><b>RT Incorrect</b></td>
                <td><?php $RTIncorrect += $block["Average Wrong"]; echo $block["Average Wrong"]; ?></td>
            </tr>
            <tr>
                <td><b>RT Total</b></td>
                <td><?php $RTTotal += $block["Average Total"]; echo $block["Average Total"]; ?></td>
            </tr>
        </table>
        <br/>
        <hr>
    <?php endforeach; ?>
    
    <h2>All Blocks</h2>
        <table class='view'>
            <tr>
                <td><b>Correct</b></td>
                <td><?php echo $correct; ?></td>
            </tr>
            <tr>
                <td><b>Missed</b></td>
                <td><?php echo $missed; ?></td>
            </tr>
            <tr>
                <td><b>RT Correct</b></td>
                <td><?php echo $RTCorrect / $numBlocks."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Incorrect</b></td>
                <td><?php echo $RTIncorrect / $numBlocks."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Total</b></td>
                <td><?php echo $RTTotal / $numBlocks."ms"; ?></td>
            </tr>
        </table>
        
<!--    <table class='view'>
        <tr>
            <th>Block</th>
            <th>Trial</th>
            <th>Response</th>
            <th>Correct?</th>
            <th>Response Time</th>
        </tr>
    <?php 
        $i = 1;
        $index = intval($r["test version"]);
        foreach ($tests[$index]["Block"] as $b => $block) :
            $num = count($block);
            for ($j = 1; $j <= $num; $j++) : 
                $question = $r["Questions"][$i]; ?>
                <tr>
                    <td><?php echo $b; ?></td>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo ucwords($question["answer"]); ?></td>
                    <td><?php echo ucwords($question["correct"]); ?></td>
                    <td><?php echo ucwords($question["response time"]); ?></td>
                </tr>
            <?php endfor; ?>
        <?php endforeach; ?>
    </table>
    <table class="view">
        <tr>
            <td>Average Response Time for Correct Responses</td>
            <td><?php echo $r["Average Correct"]; ?></td>
        </tr>
        <tr>
            <td>Average Response Time for Incorrect Responses</td>
            <td><?php echo $r["Average Wrong"]; ?></td>
        </tr>
        <tr>
            <td>Average Response Time Overall</td>
            <td><?php echo $r["Average Total"]; ?></td>
        </tr>
    </table>-->
<?php
    require_once ($footer);
    endif;
?>