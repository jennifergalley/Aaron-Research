function showInstructions() {
    if (typeTest == "practice") {
        var instr = getCookie("instr");
        show("instructions" + instr);
    } else {
        show("startOver");
    }
    allowResponses();
}

function playTone () {
    document.getElementById("tone").play();
}

var myTimeout;
function showElem1 () {
    setTimeout(function() {
        var i = getCookie ("elem"); //get i
        var b = getCookie ("block");
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
        myTimeout = setTimeout(function() { //timeout, call response with null input
            timeout ();
        }, 750); //timeout
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
    var i = getCookie ("elem"); //get i
    var b = getCookie("block");
    var instr = getCookie ("instr");
    
    if (instr <= numInstructions) { //if cycling through instructions
        if (typeTest == "practice") {
            hide("instructions" + instr); //hide instructions
            if (keycode == 37) { //left
                if (+instr > 1) { //can't go left any more than 1
                    setCookie("instr",(+instr) - 1, 1); //decrement instructions page
                } //else, do nothing - stay at this page
                showInstructions();
            } else if (keycode == 39) { //right
                increment("instr", (+instr));
                if (+instr < numInstructions) {
                    showInstructions();
                } else {
                    startAgain (); //start test
                }
            } else { //invalid key pressed
                showInstructions();
            }
        } else {
            hide("startOver");
            if (keycode == 32) { //spacebar - redo instructions 
                var url = "soundTest.php?n=" + participant;
                if (all) {
                    url += "&all";
                }
                window.location = url; //start over
            } else if (keycode == 13) { //enter - continue
                setCookie ("instr", numInstructions+1, 1); //avoid instructions
                startAgain (); //start test
            } else { //invalid input
                showInstructions();
            }
        }
        return;
    }
    
    hide (b+"."+i); //hide elem
    var pause = document.getElementById("pause");
    
    if (pause.offsetParent !== null && keycode == 13) {
        setCookie ("elem", 1, 1);
        i = getCookie ("elem");
        increment ("block", (+b));
        hide ("pause");
    } else {
        setCookie("response."+b+"."+i, keycode, 1); //save response
        setCookie("response_time."+b+"."+i, response_time, 1); //save response time
        if ((+b) == blocks && i == numberQuestions[blocks - 1]) {
            if (typeTest == "practice") { //practice test - redirect to real test
                var url = "soundTest.php?n=" + participant + "&test";
                if (all) {
                    url += "&all";
                }
            } else { //real test - save the results
                var url = "../results/saveSoundResponses.php?participant="+participant+"&";
                var f = 1;
                for (k=1; k <= blocks; k++) {
                    for (h=1; h <= numberQuestions[k-1]; h++) {
                        url += f+"="+getCookie("response."+k+"."+h)+"&";
                        url += f+"_time="+getCookie("response_time."+k+"."+h)+"&";
                        f++;
                    }
                }
                url += "all="+all;
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
setCookie("instr", 1, 1); //set instructions initially to 1
showInstructions();

