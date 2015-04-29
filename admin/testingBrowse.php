<?php
    if ($handle = opendir('gs://aarons-tests/images/')) {
        while (false !== ($entry = readdir($handle))) {
            echo "<img src='http://storage.googleapis.com/aarons-tests/images/$entry' style='max-height:100px;max-width:100px'>\n";
        }
        closedir($handle);
    }
?>