<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);
    
    // redirectToLogin();
    
    if (isset($_GET['del'])) {
        deleteResults ($_GET['del'], 'image');
    } 
?>

<a href="<?php if (isset($_GET['id'])) echo 'viewImageResults.php'; else echo 'admin.php'; ?>" target="_self" class="back"><?php if (isset($_GET['id'])) echo 'Test Results'; else echo 'Admin'; ?> &lt;&lt;</a>
        
<h1>Participant Results</h1>

<?php $results = decodeJSON($imageResponses); 
   $tests = decodeJSON ($imageTests); ?>

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
        $correctTotal = $correctFirst = $correctSecond = 0;
        $RTCorrect = $RTIncorrect = $RTTotal = 0;
        $RTC40 = $RTC60 = $RTIC40 = $RTIC60 = $RTT40 = $RTT60 = 0;
        $numBlocks = count($r["Block"]);
    
        foreach ($r["Block"] as $b => $block) : ?>
        <h2>Block <?php echo $b; ?></h2>
        <table class='view'>
            <tr>
                <th></th>
                <th>Total</th>
                <th>1 - 40</th>
                <th>41 - 60</th>
            </tr>
            <tr>
                <td><b>Correct</b></td>
                <td><?php $correctTotal += $block["Score"]; echo $block["Score"]; ?></td>
                <td><?php $correctFirst += $block["Score First 40"]; echo $block["Score First 40"]; ?></td>
                <td><?php $correctSecond += $block["Score Second 20"]; echo $block["Score Second 20"]; ?></td>
            </tr>
            <tr>
                <td><b>RT Correct</b></td>
                <td><?php $RTCorrect += $block["Average Correct"]; echo $block["Average Correct"]."ms"; ?></td>
                <td><?php $RTC40 += $block["RT Correct First 40"]; echo $block["RT Correct First 40"]."ms"; ?></td>
                <td><?php $RTC60 += $block["RT Correct Second 20"]; echo $block["RT Correct Second 20"]."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Incorrect</b></td>
                <td><?php $RTIncorrect += $block["Average Wrong"]; echo $block["Average Wrong"]."ms"; ?></td>
                <td><?php $RTIC40 += $block["RT Incorrect First 40"]; echo $block["RT Incorrect First 40"]."ms"; ?></td>
                <td><?php $RTIC60 += $block["RT Incorrect Second 20"]; echo $block["RT Incorrect Second 20"]."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Total</b></td>
                <td><?php $RTTotal += $block["Average Total"]; echo $block["Average Total"]."ms"; ?></td>
                <td><?php $RTT40 += $block["RT Total First 40"]; echo $block["RT Total First 40"]."ms"; ?></td>
                <td><?php $RTT60 += $block["RT Total Second 20"]; echo $block["RT Total Second 20"]."ms"; ?></td>
            </tr>
        </table>
        <br/>
        <hr>
    <?php endforeach; ?>
    
    <h2>All Blocks</h2>
        <table class='view'>
            <tr>
                <th></th>
                <th>Total</th>
                <th>1 - 40</th>
                <th>41 - 60</th>
            </tr>
            <tr>
                <td><b>Correct</b></td>
                <td><?php echo $correctTotal; ?></td>
                <td><?php echo $correctFirst; ?></td>
                <td><?php echo $correctSecond; ?></td>
            </tr>
            <tr>
                <td><b>RT Correct</b></td>
                <td><?php echo $RTCorrect / $numBlocks."ms"; ?></td>
                <td><?php echo $RTC40 / $numBlocks."ms"; ?></td>
                <td><?php echo $RTC60 / $numBlocks."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Incorrect</b></td>
                <td><?php echo $RTIncorrect / $numBlocks."ms"; ?></td>
                <td><?php echo $RTIC40 / $numBlocks."ms"; ?></td>
                <td><?php echo $RTIC60 / $numBlocks."ms"; ?></td>
            </tr>
            <tr>
                <td><b>RT Total</b></td>
                <td><?php echo $RTTotal / $numBlocks."ms"; ?></td>
                <td><?php echo $RTT40 / $numBlocks."ms"; ?></td>
                <td><?php echo $RTT60 / $numBlocks."ms"; ?></td>
            </tr>
        </table>
    
<?php
    endif;
    require_once ($footer);
?>