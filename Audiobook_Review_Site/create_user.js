window.onload = function () {

  $('#username_form').click(function() {
    $.ajax({
      url: 'create_user.php',
      type: 'POST' ,
      data: {
        username: $('#enter_username').val(),
        password: $('#enter_password').val()
      },
      error: function(error) {
        $('#output_div').html('My server doesn\'t like me');
      },
      dataType: 'json',
      success: function output(json) {
        output_user_pass(json);
      }, 
    });
  });
}

function output_user_pass (json_arr) {
  if (json_arr.success == false)
  {
    $('#output_div').html(json_arr.output);
  }
  else {
    $('#output_div').html(json_arr.output);
    if(json_arr.success == "true") {
      window.location.assign("final.html");
    }
  }
}

