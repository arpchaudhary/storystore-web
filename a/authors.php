<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>Storyboard</title>

  <!-- Bootstrap core CSS -->
  <!--<link href="../../dist/css/bootstrap.min.css" rel="stylesheet"> -->

  <link rel="stylesheet" href="css/bootstrap.min.css">

  <!-- Custom styles for this template -->
  <link href="css/dashboard.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
  <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
<!--    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>-->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>

  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Storyboard</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="dashboard.php">Overview</a></li>
          <li><a href="authors.php">Authors</a></li>
          <li><a href="stories.php">Stories</a></li>
          <li><a href="schedule.php">Launch Schedule</a></li>
          <li><a href="redis.php">Redis</a></li>
          <li><a href="gcm.php">GCM</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-3 col-md-2 sidebar">
        <ul class="nav nav-sidebar">
          <li><a href="dashboard.php">Overview</a></li>
          <li class="active"><a href="authors.php">Authors<span class="sr-only">(current)</span></a></li>
          <li><a href="stories.php">Stories</a></li>
          <li><a href="schedule.php">Launch Schedule</a></li>
          <li><a href="redis.php">Redis</a></li>
          <li><a href="gcm.php">GCM</a></li>
        </ul>
      </div>
      <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <?php
          require_once("../helpers.php");
          init();

          function isEmailInDb($mysql_conn, $email){
            $db_email_check = FALSE;
            if($mysql_conn != NULL) {
              if($email_check_query = mysqli_prepare($mysql_conn, "SELECT EXISTS( SELECT 1 FROM authors WHERE email = ?)")){
                mysqli_stmt_bind_param($email_check_query, "s", $email);
                mysqli_stmt_execute($email_check_query);
                mysqli_stmt_bind_result($email_check_query, $db_email_check);
                mysqli_stmt_fetch($email_check_query);
                mysqli_stmt_close($email_check_query);
              }
            } 
            return $db_email_check;
          }

          function getAuthorId($mysql_conn, $email){
            $db_author_id = 0;
            if($mysql_conn != NULL) {
              if($author_id_check_query = mysqli_prepare($mysql_conn, "SELECT author_id FROM authors WHERE email = ? LIMIT 1")){
                mysqli_stmt_bind_param($author_id_check_query, "s", $email);
                mysqli_stmt_execute($author_id_check_query);
                mysqli_stmt_bind_result($author_id_check_query, $db_author_id);
                mysqli_stmt_fetch($author_id_check_query);
                mysqli_stmt_close($author_id_check_query);
              }
            } 
            return $db_author_id;
          }

          function sanityFormCheck(){
            //Lets be optimistic and consider the form to be valid
            $form_status = TRUE;
            //General check for forms : edit or add
            if(empty($_POST['firstname'])){
              show_error("First name field invalid");
              $form_status = FALSE;
            }
            
            if(empty($_POST['lastname'])) {
              show_error("Last name field invalid");
              $form_status = FALSE;
            }

            if(empty($_POST['email'])) {
              show_error("Email field invalid");
              $form_status = FALSE;
            }
            
            if(!empty($_POST['age']) && intval($_POST['age']) <= 0) {
              show_error("Age field invalid");
              $form_status = FALSE;
            }

            return $form_status;
          }

          $mysql_conn = get_mysql_conn('stories');
          if($mysql_conn != NULL){
            if( !empty( $_POST['addauthor-submit'] ) ) {
              if(sanityFormCheck()){
                if(isEmailInDb($mysql_conn, $_POST['email'])){
                  show_error("Email ID already in database.");
                }else{
                  //This seems like a valid entry. Get the maximum ID and insert the entries in the database.
                  $f_firstname = isset($_POST['firstname']) ? $_POST['firstname'] : "";
                  $f_lastname = isset($_POST['lastname']) ? $_POST['lastname'] : "";
                  $f_email = isset($_POST['email']) ? $_POST['email'] : "";
                  $f_link = isset($_POST['link']) ? $_POST['link'] : "";
                  $f_age = isset($_POST['age']) ? $_POST['age'] : "0";
                  $f_gender = isset($_POST['gender']) ? $_POST['gender'] : "M";
                  $f_desc = isset($_POST['desc']) ? $_POST['desc'] : "";
                  if($add_author_query = mysqli_prepare($mysql_conn, "INSERT INTO authors(author_id, firstname, lastname, gender, age, email, link, description, created_at) SELECT MAX(author_id) + 1, ?,?,?,?,?,?,?, NOW() FROM authors")){                      
                    mysqli_stmt_bind_param($add_author_query, "sssdsss", $f_firstname, $f_lastname, $f_gender, $f_age, $f_email, $f_link, $f_desc);
                    mysqli_stmt_execute($add_author_query);
                    $add_author_query_err = mysqli_stmt_error($add_author_query);
                    if($add_author_query_err)
                      show_error($add_author_query_err);
                    else
                      show_success("$f_firstname $f_lastname has been added as a new author");
                    mysqli_stmt_close($add_author_query);
                  }//end of author query                      
                } 
              }//end of form check
            } else if( !empty($_POST['editauthor-submit'])) {
              if(sanityFormCheck()) {
                if(empty($_POST['author_id'])){
                  show_error("Invalid author ID. Contact admin");
                  
                }else{

                  $f_author_id = trim($_POST['author_id']);
                  $f_gen_author_id = getAuthorId($mysql_conn, $_POST['email']);
                  if($f_gen_author_id != $f_author_id){
                    show_error("Email ID already in database.");
                  
                  } else {

                    $f_firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : "";
                    $f_lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : "";
                    $f_email = isset($_POST['email']) ? trim($_POST['email']) : "";
                    $f_profile_link = isset($_POST['profile_link']) ? trim($_POST['profile_link']) : "";
                    $f_img_link = isset($_POST['img_link']) ? trim($_POST['img_link']) : "";
                    $f_age = isset($_POST['age']) ? intval($_POST['age']) : 0;
                    $f_gender = isset($_POST['gender']) ? trim($_POST['gender']) : "M";
                    $f_desc = isset($_POST['desc']) ? trim($_POST['desc']) : "";
                    $f_addr = isset($_POST['addr']) ? trim($_POST['addr']) : "";

                    if($edit_author_query = mysqli_prepare($mysql_conn, "UPDATE authors SET firstname=?, lastname=?, email=?, link=?, image=?, age=?, gender=?, description=?, address=? WHERE author_id = ?")){
                      mysqli_stmt_bind_param($edit_author_query, "sssssdssss", $f_firstname, $f_lastname, $f_email, $f_profile_link, $f_img_link, $f_age, $f_gender, $f_desc, $f_addr, $f_author_id);
                      mysqli_stmt_execute($edit_author_query);
                      $edit_author_query_err = mysqli_stmt_error($edit_author_query);
                      if($edit_author_query_err)
                        show_error("Details could not be saved. Reason : $edit_author_query_err. Contact Admin");
                      else
                        show_success("$f_firstname $f_lastname details have been updated");
                      mysqli_stmt_close($edit_author_query);
                    }else{
                      show_error("Error in edit author query formation.");
                    }
                  }
                }
              }
            }//end of form-submit check
          }else{
            show_error("Backend connection seems down. Contact Admin");
          }
        ?>

        <!-- <h1 class="page-header">Add an Author</h1> -->
        <button type="button" class="btn btn-primary btn-lg pull-right" data-toggle='modal' data-target='#addAuthorModal'>+ Add Author</button>
        
        <div class="modal fade" id="addAuthorModal" tabindex="-1" role="dialog" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" style="overflow-y: initial !important">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Add New Author</h4>
            </div>
            <div class="modal-body">
              <form name="addauthor" class="form-horizontal" role="form" method="post" action="authors.php">
               

                <div class="form-group">
                  <label for="firstname" class="col-sm-2 control-label">First Name</label>
                  <div class="col-sm-3"><input type="text" class="form-control" id="firstname" name="firstname" placeholder="Field Mandatory"></div>
            
                  <label for="lastname" class="col-sm-2 control-label">Last Name</label>
                  <div class="col-sm-4"><input type="text" class="form-control" id="lastname" name="lastname" placeholder="Field Mandatory"></div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-2 control-label">Email</label>
                  <div class="col-sm-9"><input type="text" class="form-control" id="email" name="email" placeholder="Field Mandatory"></div>
                </div>
                <div class="form-group">
                  <label for="age" class="col-sm-2 control-label">Age</label>
                  <div class="col-sm-2">
                    <input type="number" class="form-control" id="age" name="age">
                  </div>

                  <label for="gender" class="col-sm-3 control-label">Gender</label>
                  <label class="radio-inline"><input type="radio" name="gender" value="M" checked="checked">M</label>
                  <label class="radio-inline"><input type="radio" name="gender" value="F">F</label>
                </div>

                <div class="form-group">
                  <label for="profile_link" class="col-sm-2 control-label">Profile Link</label>
                  <div class="col-sm-9"><input type="text" class="form-control" id="profile_link" name="profile_link"></div>
                </div>

                <div class="form-group">
                  <label for="img_link" class="col-sm-2 control-label">Image Link</label>
                  <div class="col-sm-9"><input type="text" class="form-control" id="img_link" name="img_link"></div>
                </div>

                <div class="form-group">
                  <label for="desc" class="col-sm-2 control-label">Description</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label for="addr" class="col-sm-2 control-label">Address</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="addr" name="addr" rows="2"></textarea>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="reset" class="btn btn-danger">Clear</button>
                  <button type="submit" name="addauthor-submit" value="add author submit" class="btn btn-success">Save Author</button>
                </div>
              </form>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div>
        

        <div class="modal fade" id="editAuthorModal" tabindex="-1" role="dialog" aria-labelledby="editAuthorModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" style="overflow-y: initial !important">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
              <form name="editauthor" class="form-horizontal" role="form" method="post" action="authors.php">
                <div class="form-group">
                  <label for="author_id" class="col-sm-2 control-label">Author ID</label>
                  <div class="col-sm-2"><input type="text" class="form-control" id="author_id" name="author_id" readonly></div>
                </div>

                <div class="form-group">
                  <label for="firstname" class="col-sm-2 control-label">First Name</label>
                  <div class="col-sm-3"><input type="text" class="form-control" id="firstname" name="firstname" placeholder="Field Mandatory"></div>
            
                  <label for="lastname" class="col-sm-2 control-label">Last Name</label>
                  <div class="col-sm-4"><input type="text" class="form-control" id="lastname" name="lastname" placeholder="Field Mandatory"></div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-2 control-label">Email</label>
                  <div class="col-sm-9"><input type="text" class="form-control" id="email" name="email" placeholder="Field Mandatory"></div>
                </div>
                <div class="form-group">
                  <label for="age" class="col-sm-2 control-label">Age</label>
                  <div class="col-sm-2">
                    <input type="number" class="form-control" id="age" name="age">
                  </div>

                  <label for="gender" class="col-sm-3 control-label">Gender</label>
                  <label class="radio-inline"><input type="radio" name="gender" value="M" checked="checked">M</label>
                  <label class="radio-inline"><input type="radio" name="gender" value="F">F</label>
                </div>

                <div class="form-group">
                  <label for="profile_link" class="col-sm-2 control-label">Profile Link</label>
                  <div class="col-sm-9"><input type="text" class="form-control" id="profile_link" name="profile_link"></div>
                </div>

                <div class="form-group">
                  <label for="img_link" class="col-sm-2 control-label">Image Link</label>
                  <div class="col-sm-9"><input type="text" class="form-control" id="img_link" name="img_link"></div>
                </div>

                <div class="form-group">
                  <label for="desc" class="col-sm-2 control-label">Description</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label for="addr" class="col-sm-2 control-label">Address</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="addr" name="addr" rows="2"></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label for="desc" class="col-sm-2 control-label">Created At</label>
                  <div class="col-sm-3">
                    <input class="form-control" id="created_at" name="created_at" placeholder="" disabled></input>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="submit" name="editauthor-submit" value="edit author submit" class="btn btn-success">Submit</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-danger">Delete</button>
                </div>
              </form>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div>
      <h1 class="page-header">Author Details</h1>
      <div class="table-responsive">
        <table class="table table-condensed" style="overflow: auto; width: 1600px">
          <thead>
            <tr>                
              <th></th>
              <th>ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Gender</th>
              <th>Age</th>
              <th>Email</th>
              <th>Profile</th>
              <th>Image</th>
              <th>Description</th>
              <th>Address</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $mysql_conn = get_mysql_conn('stories');
              if($mysql_conn != NULL) {
                if($query = mysqli_prepare($mysql_conn, "SELECT author_id, firstname, lastname, gender, age, email, link, image, description, address, created_at FROM authors ORDER BY author_id ASC")) {                      mysqli_stmt_execute($query);
                  mysqli_stmt_bind_result($query, $db_author_id, $db_firstname, $db_lastname, $db_gender, $db_age, $db_email, $db_profile_link, $db_img_link, $db_desc, $db_addr, $db_created_at);
                  while(mysqli_stmt_fetch($query)) {
                    echo "<tr>".PHP_EOL;
                    echo "<td><button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#editAuthorModal'
                              data-authorid='$db_author_id' data-firstname='$db_firstname' data-lastname='$db_lastname' data-gender='$db_gender'
                              data-age='$db_age' data-email='$db_email' data-profilelink='$db_profile_link' data-imglink='$db_img_link' data-desc='$db_desc'
                              data-addr='$db_addr' data-createdat='$db_created_at'>Edit</button></td>".PHP_EOL;
                    echo "<td>$db_author_id</td>".PHP_EOL;
                    echo "<td class='col-md-1'>$db_firstname</td>".PHP_EOL;
                    echo "<td class='col-md-1'>$db_lastname</td>".PHP_EOL;
                    echo "<td>$db_gender</td>".PHP_EOL;
                    echo "<td>$db_age</td>".PHP_EOL;
                    echo "<td class='col-md-1'>$db_email</td>".PHP_EOL;
                    $db_link_text = ($db_profile_link == NULL || $db_profile_link == "") ? "N/A" : "<a href='$db_profile_link' target='blank_'>View</a>";
                    echo "<td>$db_link_text</td>".PHP_EOL;
                    $db_imglink_text = ($db_img_link == NULL || $db_img_link == "") ? "N/A" : "<a href='$db_img_link' target='blank_'>View</a>";
                    echo "<td>$db_imglink_text</td>".PHP_EOL;
                    echo "<td class='col-md-3'>$db_desc</td>".PHP_EOL;
                    echo "<td class='col-md-4'>$db_addr</td>".PHP_EOL;
                    echo "<td class='col-md-2'>$db_created_at</td>".PHP_EOL;
                    echo "</tr>".PHP_EOL;
                  }
                  mysqli_stmt_close($query);
                }
              }
              close_mysql_conn($mysql_conn);
            ?>
          </tbody>
        </table>
      </div>
    </div>

       
    <!-- Bootstrap core JavaScript
    ================================================== -->
  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <script>
      $('#editAuthorModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var d_author_id = button.data('authorid') // Extract info from data-* attributes
        var d_firstname = button.data('firstname')
        var d_lastname = button.data('lastname')
        var d_gender = button.data('gender')
        var d_age = button.data('age')
        var d_email = button.data('email')
        var d_profile = button.data('profilelink')
        var d_img = button.data('imglink')
        var d_desc = button.data('desc')
        var d_addr = button.data('addr')
        var d_created_at = button.data('createdat')
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-title').text('Edit Mode :  ' + d_firstname + ' ' + d_lastname)
        modal.find('.modal-body #author_id').val(d_author_id)
        modal.find('.modal-body #firstname').val(d_firstname)
        modal.find('.modal-body #lastname').val(d_lastname)
        
        modal.find('.modal-body #gender').val(d_gender)
        modal.find('.modal-body #age').val(d_age)
        modal.find('.modal-body #email').val(d_email)
        modal.find('.modal-body #profile_link').val(d_profile)
        modal.find('.modal-body #img_link').val(d_img)
        modal.find('.modal-body #desc').val(d_desc)
        modal.find('.modal-body #addr').val(d_addr)
        modal.find('.modal-body #created_at').val(d_created_at)
      })
    </script>

  </body>
</html>
