var localStorage = localStorage || {};
function checkNoEmptyComment(event) {
  if (localStorage.songs_RateLimit) {
    var limit = +localStorage.songs_RateLimit;
    if (+new Date() < limit + 10000) {
      alert(Translation['comment too fast']);
      return false;
    }
  }
  var target = event.target || event.srcElement;
  var cmt = target.comment.value;
  if (/^\s*$/.test(cmt)) {
    alert(Translation['comment cannot be empty']);
    return false;
  }
  localStorage.songs_RateLimit = +new Date();
  submitComment(target);
  return false;
}

function submitComment(form) {
  var cmt = form.comment.value;
  var song = form.song.value;
  var xhr = new XMLHttpRequest();
  xhr.open("POST", BASE_PATH + "/makeComment.php");
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.withCredentials = true;
  xhr.timeout = 10000;
  form.ok.disabled = true;
  xhr.onreadystatechange = function () {
    if(xhr.readyState === 4) {
      if (xhr.status !== 200) {
        alert(Translation['comment failed']);
      }
      else {
        form.comment.value = "";
      }
      form.ok.disabled = false;
      getNewComments();
    }
  }
  xhr.ontimeout = function () {
    form.ok.disabled = false;
    alert(Translation['comment failed']);
    getNewComments();
  };
  xhr.send("song="+song+"&comment="+encodeURIComponent(cmt));
}
