window.onload = function () {

  $('#username_form').click(function() {
    $.ajax({
      url: 'test.php',
      type: 'POST' ,
      data: {
        username: $('#enter_username').val(),
        password: $('#enter_password').val()
      },
      error: function() {
        $('#output_div').html('My server doesn\'t like me');
      },
      dataType: 'json',
      success: function(json_arr) {
        // var $test = $('<h1>').text(data.talks[0].talk_title);
        // var $description = $('<p>').text(data.talks[0].talk_description);
        //var json_arr = $.parseJSON(response);
        $('#output_div').html(json_arr.username + json_arr.password);
        //   .append($title)
        //   .append($description);
      },
    });
  });
/*
  $('userForm').validate({
    rules: {
      enter_username: {
        required: true,
        minlengh: 3
      },
      enter_password: {
        required: true,
        minlength: 8
      }
    }
  });*/
}


  /*  var formVar = document.getElementById('username_form');

  formVar.onclick = function() {
    var divVar = document.getElementById('output_div');
    var tempstring = stringifyParam("username",document.getElementById('enter_username').value);
    var tempstring = tempstring + stringifyParam("password",document.getElementById('enter_password').value);

    var returnObject = new Object();
    var httpRequest = new XMLHttpRequest();

    httpRequest.onreadystatechange = function() {
      if (httpRequest.readyState === 4) {
        returnObject.codeDetail = httpRequest.statusText;
        returnObject.response = httpRequest.responseText;
        returnObject.code = httpRequest.status;
          if (returnObject.code >= 200 && returnObject.code < 300) {
            returnObject.success = true; }
          else {
            returnObject.success = false; }
        }
      else {
        returnObject.success = false;
      }
    };


    httpRequest.open('post', 'test.php', true);
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    httpRequest.send(stringifyParams(tempstring));

    divVar.innerHTML = tempstring;
  }; */


function stringifyParam(name, value) {
  var tmpstr = ''
  tmpstr += name;
  tmpstr += '=';
  tmpstr += value;
  tmpstr += '&';
  return tmpstr;
}
