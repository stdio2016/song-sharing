// copied from my project, speech2sing
var clips = document.querySelector('#selClips');
var canvas = document.getElementById('canvas');
var soundBuffer = null;
var soundFile;
var waveArray = [[]];
var MaxZoomLevel = 12;
var zoomLevel = MaxZoomLevel;
var zoomPan = 0;
var zoomPanV = 0;
var yAxisZoom = 1;
var playStartTime = 0;
var playingSound = false;
var selected = {start: 0.5, end: 1};
var marks = [];
var timeUnit = 0.1;

function drawLoading() {
  canvas.width = canvas.clientWidth;
  var height = canvas.height;
  canvasCtx = canvas.getContext('2d');
  canvasCtx.font = height/3 + "px aries";
  canvasCtx.fillStyle = "red";
  var msg = 'Loading...';
  canvasCtx.fillText(msg, height/2, height * (1/2 + 1/6*0.6));
}
drawLoading();

function decodeSuccess(audioBuf) {
  stopSound();
  soundBuffer = audioBuf;
  buildWaveArray();
  // zoom to fit
  zoomLevel = Math.log2(waveArray[0].length / canvas.width) | 0;
  if (soundBuffer.duration < 1) {
    selected.start = soundBuffer.duration / 2;
    selected.end = soundBuffer.duration;
  }
  needUpdate(true);
}

// build a wave table to make drawing faster
function buildWaveArray() {
  var arr = soundBuffer.getChannelData(0), arr2;
  waveArray = [arr.slice(0)];
  var len = arr.length >> 1;
  var lv = 0;
  arr2 = new Float32Array(len * 2);
  for (i = 0; i < len * 2; i += 2) {
    arr2[i] = Math.max(arr[i], arr[i+1]);
    arr2[i+1] = Math.min(arr[i], arr[i+1]);
  }
  lv++;
  len = Math.ceil(len/2);
  while (lv < MaxZoomLevel) {
    arr2 = new Float32Array(len * 2);
    for (var i = 0; i < len*2; i += 2) {
      if (i*2+3 < arr.length) {
        arr2[i] = Math.max(arr[i*2], arr[i*2+2]);
        arr2[i+1] = Math.min(arr[i*2+1], arr[i*2+3]);
      }
      else {
        arr2[i] = arr[i*2];
        arr2[i+1] = arr[i*2+1];
      }
    }
    waveArray.push(arr2);
    arr = arr2;
    len = Math.ceil(len/2);
    lv++;
  }
  
  var range = 0;
  for (var i = 0; i < arr.length; i++) {
    range = Math.max(range, Math.abs(arr[i]));
  }
  yAxisZoom = Math.min(1 / range, 1000);
  var n = Math.floor(soundBuffer.duration / timeUnit) + 1;
  marks = [];
  for (var i = 0; i < n; i++) {
    marks.push(" ");
  }
  marks.push("-");
  marks.push("-");
}

var mouse = {x: 0, y: 0, vx: 0, px: 0, dragging: false, t: 0, dragBar: 0};
var visualizeCallbackId = 0;

function needUpdate(must) {
  if (playingSound || zoomPanV || must) {
    if (!visualizeCallbackId) {
      visualizeCallbackId = requestAnimationFrame(redraw);
    }
  }
}

function fixZoomRange() {
  zoomLevel = Math.min(zoomLevel, waveArray.length - 1);
  if (zoomLevel < 0) zoomLevel = 0;
  zoomPan = Math.min(zoomPan, waveArray[zoomLevel].length/2 - canvas.width);
  if (zoomPan < 0) zoomPan = 0;
}

function fixSelectRange(final) {
  if (!soundBuffer) return ;
  if (final && selected.start > selected.end) {
    var tmp = selected.start;
    selected.start = selected.end;
    selected.end = tmp;
  }
  var len = waveArray[0].length / soundBuffer.sampleRate;
  selected.start = Math.min(Math.max(0, selected.start), len);
  selected.end = Math.min(Math.max(0, selected.end), len);
}

function timeToCanvasPos(t) {
  var smpRate = soundBuffer ? soundBuffer.sampleRate : audioCtx.sampleRate;
  return t * smpRate / Math.pow(2, zoomLevel+1) - zoomPan;
}

function canvasPosToTime(pos) {
  var smpRate = soundBuffer ? soundBuffer.sampleRate : audioCtx.sampleRate;
  return (pos + zoomPan) / smpRate * Math.pow(2, zoomLevel+1) ;
}

function redraw() {
  canvas.width = canvas.clientWidth;
  if (!soundBuffer) {
    drawLoading();
    visualizeCallbackId = 0;
    needUpdate();
  }
  var width = canvas.width;
  var height = canvas.height;
  
  var ctx = canvas.getContext('2d');
  if (!mouse.dragging) {
    if (zoomPanV > 0) {
      zoomPanV -= width/600;
      if (zoomPanV < 0) zoomPanV = 0;
    }
    if (zoomPanV < 0) {
      zoomPanV += width/600;
      if (zoomPanV > 0) zoomPanV = 0;
    }
    zoomPan += zoomPanV|0;
  }
  fixZoomRange();
  var wav = waveArray[zoomLevel];
  
  ctx.globalCompositeOperation = 'lighter';
  ctx.strokeStyle = '#080';
  ctx.beginPath();
  ctx.moveTo(0, height/2);
  for (var i = 0; i < width; i++) {
    var j = i + zoomPan | 0;
    if (j*2 >= wav.length) break;
    var x = i;
    ctx.lineTo(x, (1 - wav[j*2] * yAxisZoom) * height/2);
    ctx.lineTo(x, (1 - wav[j*2+1] * yAxisZoom) * height/2);
  }
  ctx.stroke();
  if (selected) {
    var x1 = timeToCanvasPos(selected.start);
    var x2 = timeToCanvasPos(selected.end);
    ctx.fillStyle = '#044';
    ctx.fillRect(x1, 0, x2 - x1, height);
  }
  if (playingSound) {
    var t = audioCtx.currentTime - playStartTime;
    var x = timeToCanvasPos(t);
    ctx.fillStyle = '#f0f';
    ctx.fillRect(x - 2, 0, 4, height);
  }
  ctx.globalCompositeOperation = 'source-over';
  if (selected) {
    ctx.fillStyle = 'red';
    ctx.fillRect(x1-2, 0, 4, height);
    ctx.fillStyle = 'blue';
    ctx.fillRect(x2-2, 0, 4, height);
  }
  drawMarks();
  visualizeCallbackId = 0;
  needUpdate();
}

function zoomIn() {
  if (zoomLevel > 0) {
    zoomLevel--;
    zoomPan *= 2;
    needUpdate(true);
  }
}
function zoomOut() {
  if (zoomLevel < MaxZoomLevel-1) {
    zoomLevel++;
    zoomPan >>= 1;
    needUpdate(true);
  }
}
function selectVisible() {
  selected.start = canvasPosToTime(0);
  selected.end = canvasPosToTime(canvas.width);
  fixZoomRange();
  fixSelectRange(true);
  needUpdate(true);
}
function playRange(from, duration) {
  if (!soundBuffer || duration <= 0) return ;
  stopSound();
  fixZoomRange();
  var node = audioCtx.createBufferSource();
  node.buffer = soundBuffer;
  node.connect(audioCtx.destination);
  
  var start = audioCtx.currentTime;
  playStartTime = start - from;
  playingSound = node;
  node.start(start, from, duration);
  node.onended = function () {
    playingSound = null;
  };
  needUpdate();
}

function stopSound() {
  if (playingSound) {
    playingSound.onended = function () {};
    try {
      playingSound.stop();
    }
    catch (x) {
      console.error(x);
    }
    finally {
      playingSound = null;
    }
  }
}

function playSelected() {
  if (selected) playRange(selected.start, selected.end - selected.start);
}

function playVisible() {
  if (!soundBuffer) return ;
  var unit = Math.pow(2, zoomLevel+1) / soundBuffer.sampleRate;
  playRange(zoomPan * unit, canvas.width * unit);
}

function canvasMouseDown(e){
  e.preventDefault();
  if (e.button == 0) {
    var newx = e.offsetX;
    mouse.x = newx;
    mouse.vx = 0;
    mouse.px = newx;
    if (Math.abs(newx - timeToCanvasPos(selected.start)) < 10) {
      mouse.dragBar = 1;
    }
    else if (Math.abs(newx - timeToCanvasPos(selected.end)) < 10) {
      mouse.dragBar = 2;
    }
    else mouse.dragging = true;
    mouse.t = e.timeStamp;
  }
  needUpdate();
}

function canvasMouseMove(e){
  e.preventDefault();
  var newx = e.offsetX;
  if ((e.buttons & 1) && mouse.dragging) {
    zoomPan -= (newx - mouse.x);
    mouse.x = newx;
    var t = e.timeStamp;
    var exp = Math.exp(-16/60);
    var newpx = mouse.px * exp + newx * (1 - exp);
    mouse.vx = newpx - mouse.px;
    mouse.px = newpx;
    mouse.t = t;
  }
  else if ((e.buttons & 1) && mouse.dragBar) {
    var unit = Math.pow(2, zoomLevel+1) / soundBuffer.sampleRate;
    if (mouse.dragBar == 1)
      selected.start += (newx - mouse.x) * unit;
    if (mouse.dragBar == 2)
      selected.end += (newx - mouse.x) * unit;
    fixSelectRange();
  }
  mouse.x = newx;
  needUpdate(true);
}
function canvasMouseUp(e){
  e.preventDefault();
  if (e.button == 0 && mouse.dragging) {
    if (e.timeStamp - mouse.t < 200) zoomPanV = -mouse.vx;
    else zoomPanV = 0;
    mouse.dragging = false;
  }
  fixSelectRange(true);
  needUpdate(true);
}
canvas.addEventListener('mousedown', canvasMouseDown);
canvas.addEventListener('mousemove', canvasMouseMove);
canvas.addEventListener('mouseup', canvasMouseUp);

var nofire = false;
function ignore () {}
function canvasTouchStart(e) {
  e.preventDefault();
  var touch = e.touches;
  if (touch.length != 1) {
    nofire = true;
    return;
  }
  else{
    nofire = false;
  }
  canvasMouseDown({
    timeStamp: e.timeStamp,
    preventDefault: ignore,
    button: 0,
    offsetX: touch[0].clientX - canvas.offsetLeft,
    offsetY: touch[0].clientY - canvas.offsetTop
  });
}
function canvasTouchMove(e) {
  e.preventDefault();
  var touch = e.touches;
  if (nofire || touch.length != 1) return;
  canvasMouseMove({
    timeStamp: e.timeStamp,
    preventDefault: ignore,
    buttons: 1,
    offsetX: touch[0].clientX - canvas.offsetLeft,
    offsetY: touch[0].clientY - canvas.offsetTop
  });
}
function canvasTouchEnd(e) {
  e.preventDefault();
  var touch = e.touches;
  if (nofire || touch.length!=0) return;
  touch = e.changedTouches;
  canvasMouseUp({
    timeStamp: e.timeStamp,
    preventDefault: ignore,
    button: 0
  });
}
canvas.addEventListener('touchstart', canvasTouchStart, false);
canvas.addEventListener('touchmove', canvasTouchMove, false);
canvas.addEventListener('touchend', canvasTouchEnd, false);
window.addEventListener('resize', needUpdate.bind(true));

var selections = [sel1,sel2,sel3,sel4,sel5,sel6,sel7];
var markName = {
  M: ['Modal', '#f00'],
  I: ['Mix', '#a70'],
  H: ['Head', '#070'],
  F: ['False', '#07b'],
  Y: ['Fry', '#707'],
  '-': ['-', 'white'],
  ' ': ['unknow', 'black']
}
function markSelected() {
  var t0 = Math.floor(selected.start / timeUnit);
  var t1 = Math.ceil(selected.end / timeUnit);
  var cho = " ";
  for (var i = 0; i < selections.length; i++) {
    if (selections[i].checked) {
      cho = selections[i].value;
    }
  }
  for (var i = t0; i < t1; i++) {
    marks[i] = cho;
  }
  needUpdate(true);
}

function drawMarks() {
  var smpRate = soundBuffer ? soundBuffer.sampleRate : audioCtx.sampleRate;
  var i = Math.floor(canvasPosToTime(0) / timeUnit);
  var ctx = canvasMark.getContext('2d');
  var dist = smpRate / Math.pow(2, zoomLevel+1) * timeUnit;
  var x = timeToCanvasPos(i * timeUnit);
  var width = canvasMark.clientWidth;
  var height = canvasMark.clientHeight;
  canvasMark.width = width;
  canvasMark.height = height;
  ctx.font = height/1.5 + "px aries";
  var toDraw = [];
  var prev = null;
  while (x < width) {
    var type = marks[i];
    if (type) {
      var xx = Math.max(x, 0);
      if (prev && prev.type === type) {
        ;
      }
      else {
        prev = {type: type, begin: xx};
        toDraw.push(prev);
      }
    }
    i += 1;
    x += dist;
  }
  toDraw.push({type: '-', begin: x - dist});
  for (var i = 1; i < toDraw.length; i++) {
    var type = toDraw[i-1].type;
    var x1 = toDraw[i-1].begin;
    var x2 = toDraw[i].begin;
    ctx.fillStyle = markName[type][1];
    ctx.fillRect(x1, 0, x2, height);
    var txt = markName[type][0];
    var mw = ctx.measureText(txt).width;
    if (x2 > x1 + mw) {
      ctx.fillStyle = 'white';
      ctx.fillText(txt, x1, height * (1/2 + 1/6*0.6));
    }
  }
}

function canvasMarkMouseMove(e) {
  e.preventDefault();
  var newx = e.offsetX;
  var cho = " ";
  for (var i = 0; i < selections.length; i++) {
    if (selections[i].checked) {
      cho = selections[i].value;
    }
  }
  if ((e.buttons & 1) && true) {
    var t = Math.floor(canvasPosToTime(newx) / timeUnit);
    if (t < marks.length - 2) {
      marks[t] = cho;
      needUpdate(true);
    }
  }
}
function canvasMarkTouchStart(e) {
  e.preventDefault();
  var touch = e.touches;
  if (touch.length != 1) {
    nofire = true;
    return;
  }
  else{
    nofire = false;
  }
}
function canvasMarkTouchMove(e) {
  e.preventDefault();
  var touch = e.touches;
  if (nofire || touch.length != 1) return;
  canvasMarkMouseMove({
    timeStamp: e.timeStamp,
    preventDefault: ignore,
    buttons: 1,
    offsetX: touch[0].clientX - canvasMark.offsetLeft,
    offsetY: touch[0].clientY - canvasMark.offsetTop
  });
}
canvasMark.addEventListener('mousedown', canvasMarkMouseMove, false);
canvasMark.addEventListener('mousemove', canvasMarkMouseMove, false);
canvasMark.addEventListener('touchstart', canvasMarkTouchStart, false);
canvasMark.addEventListener('touchmove', canvasMarkTouchMove, false);
