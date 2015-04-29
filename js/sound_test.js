
    function playTone () {
        document.getElementById("tone").play();
    }
    
    var myTimeout;
    function showElem1 () {
        setTimeout(function() {
            var i = geti (); //get i
            var b = getBlock();
            show (b+"."+i); //show
            start = +new Date ();
            allowResponses (); //only allow response on response page
            
            //play sound
            if (tones[j-1] != "") { //if not empty
                setTimeout(function () { //delay tone
                    playTone(); //play tone
                }, tones[j-1]); //delay in ms
            }
            j = j+1;
            myTimeout = setTimeout(function() { //timeout after 2 seconds, call response with null input
                timeout ();
            }, 1000); //timeout after 1 second
        }, 500); //half a second between image + tone trials - ask aaron about this delay
    }
    
    function startAgain () {
        show("base");
        setTimeout(function() {
            hide ("base"); //hide base image
            showElem1();
        }, 500); //half a second between image + tone trials - ask aaron about this delay
    }
    
    function pauseBtwn () {
        setTimeout(function() {
            startAgain();
        }, 1000); //wait 1 second
    }
    
    function showPause () {
        show ("pause");
        allowResponses();
    }
    
    function response (e) {
        end = +new Date();
        var response_time = end - start;
        disallowResponses (); //only allow response on response page
        clearTimeout (myTimeout);
        var keycode = getResponse ();
        var i = geti (); //get i
        var b = getBlock();
        hide (b+"."+i); //hide elem
        var pause = document.getElementById("pause");
        if (pause.offsetParent !== null && keycode == 13) {
            setCookie ("elem", 1, 1);
            i = geti();
            increment ("block", (+b));
            hide ("pause");
        } else {
            setCookie("response."+b+"."+i, keycode, 1); //save response
            setCookie("response_time."+b+"."+i, response_time, 1); //save response time
            if ((+b) == blocks && i == numberQuestions[blocks-1]) {
                var url = "../results/saveSoundResponses.php?participant="+participant+"&testVersion="+testVersion+"&";
                var f = 1;
                for (k=1; k <= blocks; k++) {
                    for (h=1; h <= numberQuestions[k-1]; h++) {
                        url += f+"="+getCookie("response."+k+"."+h)+"&";
                        url += f+"_time="+getCookie("response_time."+k+"."+h)+"&";
                        f++;
                    }
                }
                window.location = url;
            } else {
                if (i == numberQuestions[b-1]) {
                    showPause();
                    return;
                }
            }
            increment ("elem", (+i)); //increment i
        }
        pauseBtwn ();
    }
    
    var start, end;
    var j = 1;
    setCookie ("elem", 1, 1); //set i initially to 1
    setCookie ("block", 1, 1); //set block initially to 1
    startAgain (); //show
    
