var audioCtx, audioLocked = false;
if (window.AudioContext)
  audioCtx = new AudioContext();
else if (window.webkitAudioContext)
  audioCtx = new webkitAudioContext();
else {
  alert("Your browser is too old! Please upgrade");
}

function resumeContext() {
  if (audioCtx && audioLocked) {
    // try resuming context
    audioCtx.resume();
    
    // play a beep sound
    var r = audioCtx.createOscillator();
    (r.start || r.noteOn).call(r);
    (r.stop || r.noteOff).call(r, audioCtx.currentTime+0.25);
    r.frequency.value = 440;
    var s = audioCtx.createGain();
    s.gain.value = 0.1;
    r.connect(s);
    s.connect(audioCtx.destination);
    audioLocked = false;
  }
}

function loadSound(where, success, fail) {
  var req = new XMLHttpRequest();

  req.open('GET', where, true);
  req.responseType = 'arraybuffer';

  req.onload = function(){
    if (req.status >= 200 && req.status <= 299) {
      var dat = req.response;
      audioCtx.decodeAudioData(dat, function(buffer){
        success(buffer);
      }, fail);
    }
    else {
      fail();
    }
  };
  req.onerror = fail;
  req.send();
}

addEventListener('load', function () {
  // Chrome learns the "bad" part of Safari!
  if (audioCtx && audioCtx.state === "suspended") {
    audioLocked = true;
  }
  if (audioLocked) {
    window.addEventListener('touchend', resumeContext); // for mobile
    window.addEventListener('click', resumeContext); // for desktop
  }
  var i = 0;
  var extensions = ['.ogg','.mp3'];
  function tryNext() {
    if (i < extensions.length) {
      loadSound(BASE_PATH + '/files/' + filename + extensions[i], decodeSuccess, tryNext);
      i++;
    }
    else {
      alert("No supported sound format found!");
    }
  }
  tryNext();
});
