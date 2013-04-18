<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Team 04</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }

      .sidebar-nav {
        padding: 9px 0;
      }

      footer {
        border-top: 1px solid #e3e3e3;
        background-color: #f5f5f5;
        height: 30px;
        padding-top: 10px;
      }

      #list a {
        margin: 4px 0;
        text-align: left;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>
  </head>

  <body>

    <!-- Header -->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="brand" href="index.php">Team 04 - Wiki</a>

          <form class="navbar-search pull-right" action="index.php" method="GET">
            <input name="search" type="search" class="search-query" placeholder="Search">
          </form>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row-fluid">

        <!-- Sidebar -->
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Actions</li>
              <li><a href="index.php">Show all articles</a></li>
              <li><a href="create.php">Create new article</a></li>

              <?php
                foreach(View::getVariable('navigation') as $navigation) {
                  echo '<li><a href="' . $navigation['link'] . '">' . $navigation['text'] . '</a></li>';
                }
              ?>

              <li class="nav-header">Database</li>
              <li><a href="#">Generate random articles</a></li>
              <li><a href="#">Delete all articles</a></li>
            </ul>
          </div>
        </div>

        <!-- Content -->
        <div class="span9">
          <?php
            //Print message
            $message = static::getVariable('message');
            
            if(!is_null($message)) {
                echo '<div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        ' . $message . '
                      </div>';
            }

            //Print error
            $error = static::getVariable('error');
            
            if(!is_null($error)) {
                echo '<div class="alert alert-error">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        ' . $error . '
                      </div>';
            }
          ?>


          <div class="page-header">
            <h1><?php echo View::getVariable('title'); ?> <small><?php echo View::getVariable('subtitle'); ?></small></h1>
          </div>