if (typeof core_url !== "undefined") {
  var idx = core_url.indexOf("/utilities");

  if (idx !== -1) {
    var path = core_url.substring(idx);
    core_url = BASE_URL + path;
  }
}
