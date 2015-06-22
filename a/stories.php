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
        <li><a href="authors.php">Authors</a></li>
        <li class="active"><a href="stories.php">Stories<span class="sr-only">(current)</span></a></li>
        <li><a href="schedule.php">Launch Schedule</a></li>
        <li><a href="redis.php">Redis</a></li>
        <li><a href="gcm.php">GCM</a></li>
      </ul>
      
    </div>



    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="overflow-y: initial !important">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Modal title</h4>
          </div>
          <div class="modal-body"><!-- <div class="te"></div> --></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

      <button type="button" class="btn btn-primary btn-lg pull-right" data-toggle='modal' data-target='#addStoryModal'>+ Add Story</button>
      <h2 class="sub-header">Story Details</h2>
      <div class="table-responsive">
        <table class="table table-condensed" style="overflow: auto; width: 1600px">
          <thead>
            <tr>
              <th></th>
              <th>Story ID</th>
              <th>Title</th>
              <th>Views</th>
              <th>Authors</th>
              <th>Cover Pic</th>
              <th>Language</th>
              <th>Filepath</th>
              <th>Updated At</th>
              <th>Created At</th>
            </tr>
          </thead>


          <?php
            
            require_once('../helpers.php');
            init();
            $mysql_conn = get_mysql_conn('stories');
            
            //This the query I ran in the terminal : SELECT s.story_id, s.title, a.author_id, a.firstname, a.lastname FROM story AS s, authors AS a, story_authors AS s_a WHERE s.story_id = s_a.story_id AND a.author_id = s_a.author_id

            if($details_query = mysqli_prepare($mysql_conn, "SELECT s.story_id, s.cover_pic, s.title, s.views, s.lang, s.filepath, s.created_at, s.updated_at, a.author_id, a.firstname, a.lastname FROM story AS s, authors AS a, story_authors AS s_a WHERE s.story_id = s_a.story_id AND a.author_id = s_a.author_id ORDER BY s.story_id, s.lang")){
              
              mysqli_stmt_execute($details_query);
              mysqli_stmt_bind_result($details_query, $db_story_id, $db_cover_pic, $db_title, $db_views, $db_lang, $db_filepath, $db_created_at, $db_updated_at, $db_author_id, $db_author_fname, $db_author_lname);
              $details_query_res = array();
              while(mysqli_stmt_fetch($details_query)){
                if(isset($details_query_res[$db_story_id])){
                  array_push($details_query_res[$db_story_id]["author_ids"], $db_author_id);
                  array_push($details_query_res[$db_story_id]["author_names"], $db_author_fname . " " . $db_author_lname);
                }else{
                  $details_query_res[$db_story_id]["story_id"] = $db_story_id;
                  $details_query_res[$db_story_id]["title"] = $db_title;
                  $details_query_res[$db_story_id]["author_ids"] = array();
                  array_push($details_query_res[$db_story_id]["author_ids"], $db_author_id);
                  $details_query_res[$db_story_id]["author_names"] = array();
                  array_push($details_query_res[$db_story_id]["author_names"], $db_author_fname . " " . $db_author_lname);
                  $details_query_res[$db_story_id]["cover_pic"] = $db_cover_pic;
                  $details_query_res[$db_story_id]["views"] = $db_views;
                  $details_query_res[$db_story_id]["lang"] = $db_lang;
                  $details_query_res[$db_story_id]["filepath"] = $db_filepath;
                  $details_query_res[$db_story_id]["created_at"] = $db_created_at;
                  $details_query_res[$db_story_id]["updated_at"] = $db_updated_at;
                }
              }
              mysqli_stmt_close($details_query);
              //var_dump($details_query_res);
              foreach ($details_query_res as $res_story_id => $res_story_data) {
                echo "<tr>".PHP_EOL; 
                echo "\t<td><button type='button' class='btn btn-primary btn-xs'>Edit</button></td>".PHP_EOL;
                echo "\t<td> $res_story_id </td>".PHP_EOL;
                echo "\t<td class='col-md-2'>" . $res_story_data["title"] . "</td>".PHP_EOL;
                echo "\t<td>" . $res_story_data["views"] . "</td>".PHP_EOL;
                $author_data = "";
                for($i = 0; $i < count($res_story_data["author_ids"]); $i++){
                  $author_data .= "<a href='#' data-toggle='tooltip' data-placement='auto' title='ID : " . $res_story_data["author_ids"][$i] . "'> " . $res_story_data["author_names"][$i] . "</a><br/>";
                }
                echo "\t<td class='col-md-1'> $author_data </td>".PHP_EOL;
                echo "\t<td><a href='" . $res_story_data["cover_pic"] . "' target='blank_'>View</td>".PHP_EOL;
                echo "\t<td>" . $res_story_data["lang"] . "</td>".PHP_EOL;
                $clickable_filepath = end(explode("/var/www/html/story_data/", $res_story_data["filepath"]));
                //echo "Hi : " . $res_story_data["filepath"] . " <br/>";
                echo "\t<td> <a href='http://ec2-54-200-93-245.us-west-2.compute.amazonaws.com/story_data/$clickable_filepath' target='blank_'>Read</td>".PHP_EOL;
                echo "\t<td class='col-md-2'>" . $res_story_data["created_at"] . "</td>".PHP_EOL;
                echo "\t<td class='col-md-2'>" . $res_story_data["updated_at"] . "</td>".PHP_EOL;
                echo "</tr>".PHP_EOL;
              }
              // echo "<tr>".PHP_EOL;
              //   echo "<td><button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#editStoryModal'
              //                 data-authorid='$db_author_id' data-firstname='$db_firstname' data-lastname='$db_lastname' data-gender='$db_gender'
              //                 data-age='$db_age' data-email='$db_email' data-profilelink='$db_profile_link' data-imglink='$db_img_link' data-desc='$db_desc'
              //                 data-addr='$db_addr' data-createdat='$db_created_at'>Edit</button></td>".PHP_EOL;
              //   echo "<td class='col-md-1'>$db_story_id</td>".PHP_EOL;
              //   echo "<td class='col-md-3'><b>$db_title</b></td>".PHP_EOL;
              //   echo "<td class='col-md-1'><a href='$db_cover_pic' target='blank_'>View</a></td>".PHP_EOL;
              //   echo "<td class='col-md-1'>$db_views</td>".PHP_EOL;
              //   echo "<td class='col-md-1'>$db_lang</td>".PHP_EOL;
              //   $clickable_filepath = end(explode("/var/www/html/story_data/", $db_filepath));
              //   echo $clickable_filepath;
              //   echo "<td class='col-md-1'><a class='open-modal' data-toggle='modal' href='http://ec2-54-200-93-245.us-west-2.compute.amazonaws.com/story_data/$clickable_filepath' data-target='#myModal'>Read</a></td>".PHP_EOL;
              //   echo "<td class='col-md-2'>$db_created_at</td>".PHP_EOL;
              //   echo "<td class='col-md-2'>$db_updated_at</td>".PHP_EOL;
              //   echo "</tr>".PHP_EOL;
            }
            close_mysql_conn($mysql_conn);
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>



  <!-- <a href='#' class="open-modal" data-remote='http://ec2-54-200-93-245.us-west-2.compute.amazonaws.com/story_data/1004_e.txt'>First link being presented here for your clicking pleasure
  </a>
  <br/>
  <a href='#' class="open-modal" data-remote='http://fiddle.jshell.net/bHmRB/40/show/'>This is just some lang text in order to make it clickable</a>
  <div id="modal"></div> -->

</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

 <script>
$(function(){    
//     $("a.open-modal").click(function(e){
//         e.preventDefault();
//         var modal=$("#myModal");
//         modal.empty();
//         modal.append("<div class='modal fade modal-dialog' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'></div>");
//         $('.modal-dialog').modal({
//             remote:$(e.currentTarget).attr("data-remote")
//         });
//     });
$('body').on('hidden.bs.modal', '.modal', function () {
  $(this).removeData('bs.modal');
});
});
</script>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>

</body>
</html>
