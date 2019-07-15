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
  return true;
}
