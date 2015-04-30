window.onload = function () {
  logged_in_check();

  fill_side_column();

  $('#addAudiobookButton').click(function() {
    logged_in_check();
    if (loggedIn === false) {document.location.reload(true);}
    $('#newAudiobook').removeClass("hide");
  });

  $(document).on('click', '.side_element' , function() {
    display_book($(this).val());
  });
  
  $(document).on('click', '#add_review' , function() {
    add_review();
  });

  $(document).on('click', '#add_review_submit' , function() {
    submit_review();
  });

  $('#username_form').click(function() {
    $(".notification").empty();
    if(verify_username($('#enter_username').val()) === 'false' ) {
      $('#username_error').append('The entered username isn\'t registered.');
    }
    else if(verify_password($('#enter_password').val()) === 'false') {
      $('#password_error').append('The entered password doesn\'t match.');
    }
    else {
      $.ajax({
        url: 'login.php',
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
    }
  });


  $('#submit_new_book').click( function() {

    $.ajax({
      url: 'add_book.php',
      type: 'POST' ,
      data: {
        name: $('#enter_title').val(),
        author: $('#enter_author').val(),
        narrator: $('#enter_narrator').val(),
        ISBN: $('#enter_ISBN').val(),
        length_hr: $('#enter_length_hr').val(),
        length_min: $('#enter_length_min').val(),
        date_published: $('#enter_date').val(),
        description: $('#enter_description').val(),
      },
      error: function(error) {
        $('#output_div').html('My server doesn\'t like me');
      },
      dataType: 'text',
      success: function output(text) {
        document.location.reload(true);
      }, 
    });
  });

  $('#logout').click(function() {
      $.ajax({
        url: 'logout.php',
        type: 'POST' ,
        error: function(error) {
          $('#output_div').html('My server doesn\'t like me');
        },
        dataType: 'text',
        success: function output(json) {
          document.location.reload(true);
        }, 
      });
  });

}

var num_of_books = 0;
var loggedIn = null;
var user_name = null;
var current_book = null;
var current_book_name = null;

function add_review() {
  $('#main_audiobook').html( '' +
    '<form id="add_review_form">' +
      '<Label>Add your review for ' + current_book_name + ' below:</label><br>' +
      '<input type="text" id="add_review_text"><br>' +
      '<input type="button" id="add_review_submit" value="submit">' +
    '</form'
  );
}

function submit_review() {
  $.ajax({
    url: 'submit_review.php',
    type: 'POST' ,
    data: {
      audiobook_id: current_book,
      user: user_name,
      review: document.getElementById("add_review_text").value
    },
    error: function(error) {
      $('#output_div').html('submit review failed');
    },
    dataType: 'text',
    success: function output(text) {
      $('#output_div').html(text);
      display_book(current_book_name);
    },
  });
}


function display_book(book) {
  $.ajax({
    url: 'display_book.php',
    type: 'POST' ,
    data: {title: book},
    error: function(error) {
      $('#output_div').html('display book failed');
    },
    dataType: 'json',
    success: function output(json) {
      current_book = json.id;
      current_book_name = json.name;

      var main_audiobook_string =''+
        '<table id="main_audiobook_table">' +
          '<tr><td id="book_title">Title: ' + json.name + '</td></tr>' +
          '<tr><td id="book_author">Author: ' + json.author + '</td></tr>' +
          '<tr><td id="book_narrator">Narrator: ' + json.narrator + '</td></tr>' +
          '<tr><td id="book_date_published">Date Published: ' + json.date_published + '</td></tr>';
          if(json.ISBN.length > 5){
          main_audiobook_string += '<tr><td id="book_ISBN">ISBN: ' + 
            json.ISBN + '</td></tr>';
          }

          if (json.length_hr >= 1) {
            main_audiobook_string += '<tr><td id="book_length">Length: ' + json.length_hr + ' hours '+
            json.length_min + ' minutes'+'</td></tr>';
          }
          if (json.description.length > 1) {
            main_audiobook_string += '<tr><td id="book_description">description: ' + 
            json.description + '</td></tr>';
          }
          main_audiobook_string += '</table>';

      $('#main_audiobook').html(main_audiobook_string);
    display_reviews(json.id);
    }, 
  });
}

function display_reviews(book_id) {
  $.ajax({
    url: 'get_reviews.php',
    type: 'POST' ,
    data: {title_id: book_id},
    error: function(error) {
      $('#output_div').html('get reviews failed failed');
    },
    dataType: 'json',
    success: function output(json) {
      $('#main_audiobook').append('<br><p id="review_title">Reviews</p>'); 
      if(loggedIn === true) {
        $('#main_audiobook').append('<input id="add_review" type="button" value="Add Review">');
      } 
      for(var i = 0; i < json.length; i++){
        $('#main_audiobook').append('<div class="review_div"><p class="username_review">' + 
            'submitted by: '+
            json[i].user_name + '</p>' + 
            '<p class="review">' + json[i].review + '</p></div><br>');
      }
    }, 
  });
}

function logged_in_check() {
  $.ajax({
    url: 'logged_in_check.php',
    type: 'POST' ,
    async: false,
    error: function(error) {
      $('#output_div').html('My server doesn\'t like me');
    },
    dataType: 'text',
    success: function output(text) {
      if(text != 'false') {
        $(".not_logged_in").addClass("hide");
        $(".logged_in").removeClass("hide");
        loggedIn = true;
        user_name = text;
      } 
      else {
        $(".not_logged_in").removeClass("hide");
        $(".logged_in").addClass("hide");
        loggedIn = false;
      }
    }, 
  });
}

function fill_side_column() {
  $.ajax({
    url: 'side_column.php',
    type: 'POST' ,
    error: function(error) {
      $('#output_div').html('Failed to get side column');
    },
    dataType: 'json',
    success: function output(json) {
    
      $('#side_column').append('<form>');
      for(var i = 0; i < json.length; i++) {
        $('#side_column').append('<input class="side_element" id="'+json[i]+'" value="'+json[i]+
          '" type="button" ></input><br>');
      }
      $('#side_column').append('</form>');
    }, 
  });
}

function verify_username(name) {
  var username_result = 'false';
  $.ajax({
    url: 'check_user.php',
    type: 'POST' ,
    async: false,
    data: {
      username: $('#enter_username').val(),
    },
    error: function(error) {
      $('#output_div').html('My server doesn\'t like me');
    },
    dataType: 'text',
    success: function output(text) {
      if(text == 'true') {
        username_result = 'true';
      } 
      else {
//      $('#output_div').html(text);
      }
    }, 
  });
  return username_result;
}

function verify_password(pwd) {
  var password_result = 'false';
  $.ajax({
    url: 'check_password.php',
    type: 'POST' ,
    async: false,
    data: {
      username: $('#enter_username').val(),
      password: $('#enter_password').val()
    },
    error: function(error) {
      $('#output_div').html('My server doesn\'t like me');
    },
    dataType: 'text',
    success: function output(text) {
      if(text == 'true') {
        password_result = 'true';
      } 
      else {
//      $('#output_div').html(text);
      }
    }, 
  });
  return password_result;
}

function output_user_pass (json_arr) {
  if (json_arr.success == false)
  {
    $('#output_div').html(json_arr.output);
  }
  else {
   // $('#output_div').html(json_arr.output);
    document.location.reload(true);
  }
}
