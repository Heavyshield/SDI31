<?php session_start(); 


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="1_music/view/Bootstrap/css/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="1_music/view/responsive.css"/>
	</head>
	<body>
		<div class="container">
			<div class="row" id="firstRow">
				<div class="col-lg-12" id="title">
					<h1>MUSIQUE</h1>
				</div>
			</div>

			<div class="row" id="secondRow">
				<div class="col-lg-6" id="database">
					<h2>Bibliothèque</h2>
					<div class="col-lg-12" id="genres">Genres
					</div>
					<div class="col-lg-12" id="albums">Albums
					</div>
					<div class="col-lg-12" id="titles">Titres
					</div>
				</div>

				<div class="col-lg-6" id="playlist">
					<div class="col-lg-7" id="controller">
						<div class="btn-group">
				          <a class="btn btn-info" href="#">
				          	<span class="glyphicon glyphicon-fast-backward"></span>&nbsp;
				          </a>
				          <a class="btn btn-info" href="#">
				          	<span class="glyphicon glyphicon-backward"></span>&nbsp;
				          </a>
				          <a class="btn btn-warning" href="#" id="status">
				          	<span class="glyphicon glyphicon-play" ></span>&nbsp;
				          </a>
				          <a class="btn btn-info" href="#">
				          	<span class="glyphicon glyphicon-forward"></span>&nbsp;
				          </a>
				          <a class="btn btn-info" href="#">
				          	<span class="glyphicon glyphicon-fast-forward"></span>&nbsp;
				          </a>
				        </div>
					</div>
					<div class="col-lg-5" id="volume">
						<div class="btn-group">
						  <a class="btn btn-info" href="#">
						  	<span class="glyphicon glyphicon-volume-off"></span>&nbsp;
				          </a>
				          <a class="btn btn-info" href="#">
				          	<span class="glyphicon glyphicon-volume-down"></span>&nbsp;
				          </a>
				          <a class="btn btn-info" href="#">
				          	<span class="glyphicon glyphicon-volume-up"></span>&nbsp;
				          </a>
				           <a class="btn btn-warning" href="#" id="currentVolume">
				          	<span class="btn-warning "></span>&nbsp;
				          </a>
						</div>
					</div>
					<div class="col-lg-12">La playlist
						<div class="col-lg-12">Le titre en cour
						</div>
						<div class="col-lg-12">Les titres dans la playlist
						</div>
					</div>
				</div>


				
				</div>

		</div>



		<script src="jquery-2.1.4.js"></script>
		<script type="text/javascript" src="1_music/view/Bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="1_music/controller/ajax.js"></script>
	    <script type="text/javascript" src="1_music/controller/ajax.js"></script>

	</body>

</html>		
