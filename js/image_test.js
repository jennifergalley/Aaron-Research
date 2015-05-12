    
    function showStartPage () {
        show("start");
        allowResponses(); //allow responses
    }
    
    function hideStartPage () {
        var i = geti ();
        hide("start");
        increment ("elem", (+i)); //increment i to 1
        setTimeout(function () {
            showBaseImage();
        }, 2000); //pause for 2s after they hit spacebar
    }
    
    function showBaseImage () {
        show ("base"); //show base image
        setTimeout(function() {
            hideBaseImage(); //hide base image
        }, 250); //hide base image after 250 ms
    }
    
    function hideBaseImage () {
        hide ("base"); //hide base image
        showImage();
    }
    
    function showImage () {
        var i = geti ();
        var b = getBlock();
        show (b+"."+i); //show the image
        allowResponses(); //allow responses
        start = +new Date ();
    }
    
    function hideImage () {
        var i = geti ();
        var b = getBlock();
        hide (b+"."+i); //hide 
    }
    
    function showWrong (pause) { //show the X for incorrect response
        show("wrong");
        setTimeout(function() {
            hideWrong();
        }, 1000); //show X for 1000 ms
    }
    
    function hideWrong () { //hide the X for incorrect response
        hide("wrong");
    }
    
    function showPause () { //show blank screen for 1000 ms, then start over at base image
        setTimeout(function() {
            showBaseImage();
        }, 1000); //blank screen for 1000 ms
    }
    
    function pause(numberMillis) { 
        var now = new Date(); 
        var exitTime = now.getTime() + numberMillis; 
        while (true) { 
            now = new Date(); 
            if (now.getTime() > exitTime) 
                return; 
        } 
    }
    
    function response (e) {
        disallowResponses (); //only allow response on specific pages
        var keycode = getResponse ();
        var i = geti ();
        var b = getBlock();
        
        if (i == 0) { //if they hit spacebar from the start page
            if (keycode == 32) {
                hideStartPage(); //hide start page and launch the test
            } else {
                showStartPage(); //return to start page, and allow responses
            }
            return; //don't continue executing this function
        }
        
        if (keycode == 37 || keycode == 39) {
            end = +new Date();
            var response_time = end - start;
            hideImage ();
            
            setCookie("response."+b+"."+i, keycode, 1); //save response
            setCookie("response_time."+b+"."+i, response_time, 1); //save response time
            
            
            //check if it was the last question, and if so, save all the responses
            if ((+b) == blocks && i == numberQuestions[blocks-1]) {
                var url = "../results/saveImageResponses.php?participant="+participant+"&";
                var t = 1;
                for (b=1; b <= blocks; b++) {
                    for (q=1; q <= numberQuestions[b-1]; q++) {
                        url += t+"="+getCookie("response."+b+"."+q)+"&";
                        url += t+"_time="+getCookie("response_time."+b+"."+q)+"&";
                        t++;
                    }
                }
                window.location = url;
            } else {
                if (i == numberQuestions[b-1]) { //last question in this block
                    return; //stop?
                }
            }
            
            //check if response was correct
            var question = (+i) - (60 * ((+b)-1)); //question number in the current block (out of 60)
            if ((keycode == 37 && correctAnswers[(+i)-1] != "left") || (keycode == 39 && correctAnswers[(+i)-1] != "right")) { //37 = left, 39 = right
                show("wrong");
                setTimeout(function() {
                    hideWrong();
                    if (question == switchAfter) {
                        show("switch"); //show switch text
                        setTimeout(function() {
                            hide("switch"); //hide switch text
                            increment ("elem", (+i)); //increment i 
                            showPause();
                        }, switchDuration); //hide after the specified number of milliseconds
                    } else {
                        increment ("elem", (+i)); //increment i 
                        showPause();
                    }
                }, 1000); //show X for 1000 ms
            } else {
                if (question == switchAfter) {
                    show("switch"); //show switch text
                    setTimeout(function() {
                        hide("switch"); //hide switch text
                        increment ("elem", (+i)); //increment i 
                        showPause();
                    }, switchDuration); //hide after the specified number of milliseconds
                } else {
                    increment ("elem", (+i)); //increment i 
                    showPause();
                }
            }
        } else {
            showImage();
        }
    }

    var end, start;
    setCookie ("elem", 0, 1); //set i initially to 0
    setCookie ("block", 1, 1); //set block initially to 1
    showStartPage (); //show
    
