
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ roorURI }}favicon.ico">

    <title>{% block title %}{% endblock %}</title>
    {% block inheader %}
    {% endblock %}
    <!-- Bootstrap core CSS -->
    <link href="{{ roorURI }}css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ roorURI }}css/app.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ roorURI }}">My RSS</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li{% block rrsactive %}{% endblock %}><a href="{{ roorURI }}list">RSS List</a></li>
            <li{% block fltractive %}{% endblock %}><a href="{{ roorURI }}filter">Filters</a></li>
            <li{% block groupactive %}{% endblock %}><a href="{{ roorURI }}groups">Groups</a></li>
            <li{% block lastactive %}{% endblock %}><a href="{{ roorURI }}last">Last</a></li>
            <li><a href="getrss/all">RSS</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container">
      {% block contaner %}
      <div class="jumbotron">
            <div class="container">
              <h1>My RSS</h1>
              <p>Кучу RSS в один для вашего uTorrent</p>
              <p><a class="btn btn-primary btn-lg" role="button">Learn more »</a></p>
            </div>
          </div>
      {% endblock %}
    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="{{ roorURI }}js/bootstrap.min.js"></script>
    <script src="{{ roorURI }}js/app.js"></script>
  </body>
</html>