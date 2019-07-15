var lastCommentId = 0;
var currentFloor = 1;
var username = username;
var _GET = {};
(function () {
  var search = location.search.substring(1).split('&');
  for (var i = 0; i < search.length; i++) {
    var s = search[i];
    var r = s.indexOf('=');
    if (r !== -1) _GET[s.substring(0, r)] = s.substring(r+1);
    else _GET[s] = '';
  }
})();
var songId = _GET['id'];
function getNewComments() {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if(xhr.readyState === 4) {
      if (xhr.status === 200) {
        var resp = xhr.responseText;
        var s = JSON.parse(resp);
        appendComments(s);
      }
      setTimeout(getNewComments, 10000);
    }
  };
  xhr.onerror = function () {
    setTimeout(getNewComments, 10000);
  };
  xhr.open('GET', BASE_PATH + '/comment.php?idafter=' + lastCommentId + '&song=' + songId);
  xhr.send();
}

function setText(span, txt) {
  if ('textContent' in span) span.textContent = txt;
  else span.innerText = txt;
}

function createSpan(txt, cls) {
  var s = document.createElement('span');
  s.className = cls;
  setText(s, txt);
  return s;
}

function deleteComment(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', BASE_PATH + '/deleteComment.php?id=' + id + '&song=' + songId);
  xhr.send();
  var d = document.getElementById('comment_' + id);
  var e = d.firstChild;
  while (e) {
    var en = e.nextSibling;
    if (e.className === "floor") ;
    else if (e.className === "username") {
      setText(e, Translation['comment deleted']);
    }
    else e.parentNode.removeChild(e);
    e = en;
  }
  return void 0;
}

function convertDate(str) {
  function pad(x) {
    if (x < 10) return "0" + x;
    return "" + x;
  }
  var m = str.match(/^(\d{4})-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/);
  if (!m) return "";
  var d = new Date(Date.UTC(+m[1], m[2]-1, +m[3], +m[4], +m[5], +m[6]));
  return d.getFullYear() +
    '-' + pad(d.getMonth() + 1) +
    '-' + pad(d.getDate()) +
    ' ' + pad(d.getHours()) +
    ':' + pad(d.getMinutes()) +
    ':' + pad(d.getSeconds());
}

function appendComments(json) {
  for (var i = 0; i < json.length; i++) {
    var div = document.createElement('div');
    div.className = 'comment';
    div.id = 'comment_' + json[i].id;
    div.appendChild(createSpan(currentFloor+'. ', 'floor'));
    if (json[i].deleted === 1) {
      div.appendChild(createSpan(Translation['comment deleted'], 'deleted'));
    }
    else {
      div.appendChild(createSpan(json[i].user, 'username'));
      var cmt = document.createElement('div');
      cmt.className = 'comment-text';
      setText(cmt, json[i].comment);
      div.appendChild(cmt);
      div.appendChild(createSpan(convertDate(json[i].time), 'time'));
      if (json[i].user === username) {
        var del = document.createElement('a');
        del.href = 'javascript:deleteComment(' + json[i].id + ')';
        setText(del, Translation['delete']);
        div.appendChild(del);
      }
    }
    lstComment.appendChild(div);
    lastCommentId = json[i].id;
    currentFloor += 1;
  }
}

onload = getNewComments;
