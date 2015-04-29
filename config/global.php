<?php
    global $rootdir;
    $rootdir = "../";
    //$rootdir = "/Users/stell_000/Documents/GitHub/ForAaronsResearch/";

    global $subdir;
    $subdir = "/";
    //$subdir = "/ForAaronsResearch/";

    global $header;
    $header = $rootdir."config/header.php";

    global $footer;
    $footer = $rootdir."config/footer.php";

    global $functions;
    $functions = $rootdir."functions/functions.php";
    require_once ($functions);

    global $modules;
    $modules = $rootdir."config/modules.php";
    require_once ($modules);
    
    global $datadir;
    $datadir = "gs://aaron-landau-images/";
    
    global $soundTests;
    $soundTests = $datadir."sound_tests.json";
    
    global $imageTests;
    $imageTests = $datadir."image_tests.json";
    
    global $soundResponses;
    $soundResponses = $datadir."sound_responses.json";
    
    global $imageResponses;
    $imageResponses = $datadir."image_responses.json";
    
    global $images;
    $images = $datadir."images/";
    
    global $imageURL;
    $imageURL = "http://storage.googleapis.com/aaron-landau-images/";
    
    date_default_timezone_set("America/Los_Angeles");
?>