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

function saveSegments() {
  var arr = [];
  for (var i in segments) {
    var seg = segments[i];
    arr.push({
      start: seg.start,
      end: seg.end,
      name: seg.name,
      type: document.getElementById('segmentType'+i).value
    });
  }
  xhr = new XMLHttpRequest();
  xhr.open('POST', 'mark.php');
  var fd = new FormData();
  fd.append('id', songId);
  fd.append('marks', JSON.stringify(arr));
  xhr.send(fd);
  xhr.onload = function () {
    if (xhr.status === 200) {
      alert('save success');
    }
    else {
      alert('save failed!');
    }
  }
  xhr.onerror = function () {
    alert('save failed');
  }
}

function loadTemplate() {
  xhr = new XMLHttpRequest();
  xhr.open('GET', BASE_PATH+'/mark.php?id='+songId+'&user='+author);
  xhr.send();
  xhr.onload = function () {
    if (xhr.status === 200) {
      initInterface(JSON.parse(xhr.response));
    }
  }
}

function loadSegments() {
  xhr = new XMLHttpRequest();
  xhr.open('GET', BASE_PATH+'/mark.php?id='+songId+'&user='+username);
  xhr.send();
  xhr.onload = function () {
    if (xhr.status === 200) {
      initInterface(JSON.parse(xhr.response));
    }
    else if (xhr.status === 404) {
      loadTemplate();
    }
  }
}
loadSegments();
