<?php
    session_start();
    require_once ("../config/global.php"); 
    
    function encodeJSON ($file, $json) {
        $fp = fopen ($file, "w");
        fwrite ($fp, json_encode ($json, (JSON_PRETTY_PRINT | JSON_FORCE_OBJECT)));
        fclose ($fp);
    }
    
    function decodeJSON ($file) {
        $json = file_get_contents($file);
        $json = json_decode($json, true);
        return $json;
    }
    
    function deleteTest ($version, $type) {
        global $soundTests, $imageTests;
        if ($type == "sound") $file = $soundTests;
        else $file = $imageTests;
        $tests = decodeJSON ($file);
        unset($tests[$version]);
        encodeJSON($file, $tests);
    }
    
    function deleteResults ($identifier, $type) {
        global $soundResponses, $imageResponses;
        if ($type == "sound") $file = $soundResponses;
        else $file = $imageResponses;
        $results = decodeJSON ($file);
        unset($results[$identifier]);
        encodeJSON($file, $results);
    }
    
?>

<script type='text/javascript'>
    function redirect (url) {
        window.location.href = url;
    }
</script>