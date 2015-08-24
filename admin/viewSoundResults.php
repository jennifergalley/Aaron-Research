<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);
    
    redirectToLogin();
    
    if (isset($_GET['del'])) {
        deleteResults ($_GET['del'], 'sound');
    } 
?>

<a href="<?php if (isset($_GET['id'])) echo 'viewSoundResults.php'; else echo 'admin.php'; ?>" target="_self" class="back"><?php if (isset($_GET['id'])) echo 'Test Results'; else echo 'Admin'; ?> &lt;&lt;</a>
 
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
            <td><b>Type of Test</b></td>
            <td><?php echo ucwords($r["type"]); ?></td>
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
            <td><b>Type of Test</b></td>
            <td><?php echo ucwords($r["type"]); ?></td>
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
        $correct125 = $correct200 = 0;
        $RTCorrect = $RTIncorrect = $RTTotal = 0;
        $RTIncorrect125 = $RTIncorrect200 = 0;
        $numIncorrect125 = $numIncorrect200 = 0;
        $numBlocks = count($r["Block"]);
    
        foreach ($r["Block"] as $b => $block) : ?>
        <h2>Block <?php echo $b; ?></h2>
        <table class='view'>
            <tr>
                <th></th>
                <th><b>Total</b></th>
                <th><b>125ms</b></th>
                <th><b>200ms</b></th>
            </tr>
            <tr>
                <td><b>Correct</b></td>
                <td><?php $correct += $block["Score"]["total"]; echo $block["Score"]["total"]; ?></td>
                <td><?php $correct125 += $block["Score"]["125"]; echo $block["Score"]["125"]; ?></td>
                <td><?php $correct200 += $block["Score"]["200"]; echo $block["Score"]["200"]; ?></td>
            </tr>
            <tr>
                <td><b>Missed</b></td>
                <td><?php $missed += $block["Missed"]; echo $block["Missed"]; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><b>RT Correct</b></td>
                <td><?php $RTCorrect += $block["Average Correct"]; echo $block["Average Correct"]."ms"; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><b>RT Incorrect</b></td>
                <td><?php $RTIncorrect += $block["Average Wrong"]["total"]; echo $block["Average Wrong"]["total"]."ms"; ?></td>
                <td><?php 
                    if ($block["Average Wrong"]["125"] != "0") {
                        $numIncorrect125++;
                    }
                    $RTIncorrect125 += $block["Average Wrong"]["125"]; 
                    echo $block["Average Wrong"]["125"]."ms"; ?></td>
                <td><?php 
                    if ($block["Average Wrong"]["200"] != "0") {
                        $numIncorrect200++;
                    }
                    $RTIncorrect200 += $block["Average Wrong"]["200"]; 
                    echo $block["Average Wrong"]["200"]."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Total</b></td>
                <td><?php $RTTotal += $block["Average Total"]; echo $block["Average Total"]."ms"; ?></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <br/>
        <hr>
    <?php endforeach; ?>
    
    <h2>All Blocks</h2>
        <table class='view'>
            <tr>
                <th></th>
                <th><b>Total</b></th>
                <th><b>125ms</b></th>
                <th><b>200ms</b></th>
            </tr>
            <tr>
                <td><b>Correct</b></td>
                <td><?php echo $correct; ?></td>
                <td><?php echo $correct125; ?></td>
                <td><?php echo $correct200; ?></td>
            </tr>
            <tr>
                <td><b>Missed</b></td>
                <td><?php echo $missed; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><b>RT Correct</b></td>
                <td><?php echo $RTCorrect / $numBlocks."ms"; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><b>RT Incorrect</b></td>
                <td><?php echo $RTIncorrect / $numBlocks."ms"; ?></td>
                <td><?php 
                    if ($numIncorrect125 > 0)
                        echo $RTIncorrect125 / $numIncorrect125."ms"; 
                    else
                        echo "0ms";
                    ?></td>
                <td><?php 
                    if ($numIncorrect200 > 0)
                        echo $RTIncorrect200 / $numIncorrect200."ms";
                    else
                        echo "0ms";
                   ?></td>
            </tr>
            <tr>
                <td><b>RT Total</b></td>
                <td><?php echo $RTTotal / $numBlocks."ms"; ?></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        
<?php
    require_once ($footer);
    endif;
?>