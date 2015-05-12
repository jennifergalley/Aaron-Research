<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);

    redirectToLogin();
 
    if (!empty($_GET['del'])) {
        deleteTest ($_GET['del'], 'sound');
    } 
?>

<a href="<?php if (!empty($_GET['type'])) echo 'viewSoundTests.php'; else echo 'admin.php'; ?>" target="_self" class="back"><?php if (!empty($_GET['type'])) echo 'Tests'; else echo 'Admin'; ?> &lt;&lt;</a>

<h1>Test Blocks</h1>

<?php $tests = decodeJSON($soundTests); 
    if (empty($tests)) :
        echo "<h2>There are currently no test versions available.</h2>";
    else :
        if (empty($_GET['type'])) :
            foreach ($tests as $type => $t) : ?>
    <table class='view'>
        <tr>
            <td><b>Test Type</b></td>
            <td><?php echo ucwords($type); ?></td>
        </tr>
        <tr>
            <td><b>Date Created</b></td>
            <td><?php echo $t["Date"]; ?></td>
        </tr>
        <tr>
            <td><b>View Test</b></td>
            <td><a href="?type=<?php echo $type; ?>" target="_self">View This Test</a></td>
        </tr>
    </table>
    <br>
    <hr>
    <br>
<?php 
            endforeach; 
        else : 
            $type = $_GET['type'];
            $t = $tests[$type];    
?>
        <table class='view'>
        <tr>
            <td><b>Test Type</b></td>
            <td><?php echo ucwords($type); ?></td>
        </tr>
        <tr>
            <td><b>Date Created</b></td>
            <td><?php echo $t["Date"]; ?></td>
        </tr>
        <tr>
            <td><b>Delete Test</b></td>
            <td><a id='delete' href="?del=<?php echo $type; ?>" target="_self" 
                onclick="return confirm('Are you sure you want to delete this test?');">Delete This Test</a></td>
        </tr>
    </table>
    <table class='view'>
        <tr>
            <th>Block</th>
            <th>Trial</th>
            <th>Image</th>
            <th>Tone</th>
            <th>Right Answer</th>
        </tr>
        <?php foreach ($t["Block"] as $num => $trials) : 
            foreach ($trials as $n =>$question) : ?>
        <tr>
            <td><?php echo $num; ?></td>
            <td><?php echo $n; ?></td>
            <td><?php echo $question["image"]; ?></td>
            <td><?php echo !empty($question["tone"]) ? $question["tone"]."ms" : "None"; ?></td>
            <td><?php echo ucwords($t["Right Answers"][$n]); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
<?php    
    endif;
    endif;
    require_once ($footer);
?>