<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);

    redirectToLogin();
 
    if (!empty($_GET['del'])) {
        deleteTest ($_GET['del'], 'image');
    }
?>

<a href="<?php if (!empty($_GET['type'])) echo 'viewImageTests.php'; else echo 'admin.php'; ?>" target="_self" class="back"><?php if (!empty($_GET['type'])) echo 'Tests'; else echo 'Admin'; ?> &lt;&lt;</a>

<h1>Test Versions</h1>

<?php $tests = decodeJSON($imageTests); 
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
        else:
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
            <td><a id='delete' href="viewImageTests.php?del=<?php echo $type; ?>" target="_self" 
                onclick="return confirm('Are you sure you want to delete this test?');">Delete This Test</a></td>
        </tr>
    </table>
    
<?php 
    foreach ($t["Block"] as $num => $trials) : ?>
        <h2>Block <?php echo $num; ?></h2>
        
        <!-- Switch Text -->
        <table>
            <tr> 
                <td>Switch</td>
                <td>After</td>
                <td><?php echo $trials["Switch"]["after"]; ?></td>
                <td>Text</td>
                <td><?php echo $trials["Switch"]["text"]; ?></td>
                <td>Duration (ms)</td>
                <td><?php echo $trials["Switch"]["duration"]; ?></td>
            </tr>
        </table>
        
    <table class='view'>
        <tr>
            <th>Trial</th>
            <th>First Character</th>
            <th>First Color</th>
            <th>Second Character</th>
            <th>Second Color</th>
            <th>Right Answer</th>
        </tr>
        
        <?php 
            foreach ($trials as $n => $question) : 
                if ($n != "Switch") :
        ?>
                    <!-- Characters and Colors -->
                    <tr> 
                        <td><?php echo $n; ?></td>
                        <td><?php echo $question["left"]["character"]; ?></td>
                        <td><?php echo $question["left"]["color"]; ?></td>
                        <td><?php echo $question["right"]["character"]; ?></td>
                        <td><?php echo $question["right"]["color"]; ?></td>
                        <td><?php echo ucwords($t["Right Answers"][$n]); ?></td>
                    </tr>
            <?php 
                endif;
            endforeach; ?>
    </table>
    <?php endforeach; ?>
<?php 
    endif;
    endif;
    require_once ($footer);
?>