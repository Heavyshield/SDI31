var path="1_music/controller/module.php";
var lastStatus="pause";	
var lastVolume="";



//Chargement de la page HTML
$.ajax({
	url:"1_music/view/module.html",
	success: function(data) 
	{
		$('body').append(data);
	}
})


//Partie database-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


//Surcouche associe un genre aux albums grâces au titres et le stock dans une SESSION------------------------------------------------------------------------------------------------
$.ajax(
		{
		url: path,
		type:"POST",
		data: { req:'setAlbumsGenre'},
		dataType: "text",
		
			error:function(exception){alert('Exception setAlbumsGenre'+exception);}
		})


//Database affichage des listes de genres/d'albums/de titre
			$.ajax({
					url:path,
					type:"POST",
					data: {req:'listGenres'},
					dataType:"text",
					success:function(result)
					{
						$('#genres').empty().html(result);
						$(document).ready(function()
						{

							$('#genres').on("click","a",function()
							{
								//Aprés click chargement des albums correspondant au genre selectioné ---------
								$.ajax({
									url: path,
									type:"POST",
									data: {req:'getAlbumsByGenre_'+$(this).attr('id')},
									dataType:"text",
									//Chargement des albums correspondant au genre selectioné
									success:function(albums)
									{
										$('#albums').empty().html(albums);
										$(document).ready(function()
										{
											$('#albums').on("click","li",function()
											{
												//Aprés click chargement des titres correspondant au genre selectioné
												$.ajax({
													url: path,
													type:"POST",
													data: {req:'listTitles_'+$(this).attr('id')},
													dataType:"text",
													success:function(titles)
													{
														$('#titles').empty().html(titles);
													},
													error:function(exception){alert('Exception li title'+exception);}

												})
											})

										})
									},
									error:function(exception){alert('Exception li Genre'+exception);}

								})
							
							})

						})
					},
					error:function(exception){alert('Exception listgenre'+exception);}
				})


$(document).ready(function()
{
	$('.container').on("click","#bibliotheque","h2",function()
	{
		if ($(this).is(":visible")) 
			{
				$(this).hide();
			} else
			{
				$(this).show();
			};
	})
})



//affichage ou non de la bibliothèque
$(document).ready(function()
{
	$('body').on("click","#bibliotheque",function()
	{
		//console.log("click sur database");
		if ($("#bibliotheque").hasClass("hide")) 
			{
				$("#bibliotheque").removeClass("hide");
				$("#bibliotheque").addClass("visible");
				$("#database").show();
			} 
			else
				{
					$("#bibliotheque").removeClass("visible");
					$("#bibliotheque").addClass("hide");
					$("#database").hide();
				};
		
	})
})



//Partie playlist-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//gestion de l'ajout des albums correspondant au genre l'id du bouton étant l'id du genre
$(document).ready(function()
{
	$('body').on("click",".buttonAddGenre",function()
	{
		var element = $(this).attr('id');
		$.ajax(
			{
				url: path,
				type:"POST",
				data: { req:"addGenre_"+$(this).attr('id')},
				dataType: "text",
				success:function()
				{
					//$("#"+ element ).attr("src","1_music/view/images/player/done.png");
					$("#"+element).replaceWith("<a class='btn btn-danger buttonRemoveGenre' href='#' id="+element+"> <span class='glyphicon glyphicon-minus'></span> </a>");
				},

				error:function(exception){alert('Exception addGenre'+exception);}
			})
	})
})

//gestion de la supression du genre de la playlist
$(document).ready(function()
{
	$('body').on("click",".buttonRemoveGenre",function()
	{
		var element = $(this).attr('id');
		$.ajax(
		{
			url: path,
			type:"POST",
			data: {req:"removeGenre_"+$(this).attr('id')},
			dataType: "text",
			success:function()
			{
				$("#"+element).replaceWith("<a class='btn btn-warning buttonAddGenre' href='#' id="+element+"> <span class='glyphicon glyphicon-plus'></span> </a>")
			},
			error:function(exception){alert('Exception removeGenre'+exception);}
		})
	})
})

//gestion de l'ajout d'un album , l'id du bouton étant l'id de l'album
$(document).ready(function()
{
	$('body').on("click",".buttonAddAlbum",function()
	{
		var element = $(this).attr('id');
		$.ajax(
			{
				url: path,
				type:"POST",
				data: { req:"addAlbum_"+$(this).attr('id')},
				dataType: "text",
				success:function(playlist)
				{
					$("#"+ element).attr("src","1_music/view/images/player/done.png");


				},

				error:function(exception){alert('Exception addAlbum'+exception);}
			})
	})
})


//affichage ou non de la playlist actuel

$(document).ready(function()
{
	$('body').on("click","#playlist",function()
	{
		if ($("#playlist").hasClass("hide")) 
		{

				//console.log("click sur playlist");
			$.ajax(
			{
				url: path,
				type:"POST",
				data: { req:'getPlaylist'},
				dataType: "text",
				success:function(playlist)
				{
					$('#currentPlaylist').empty().html(playlist);
				},

				error:function(exception){alert('Exception getPlaylist'+exception);}
			})
				$("#playlist").removeClass("hide");
				$("#playlist").addClass("visible");

		} else{
			$('#currentPlaylist').empty();
			$("#playlist").removeClass("visible");
			$("#playlist").addClass("hide");
		};




	})

})

//gestion de la supression des titres à partir de la playlist
$(document).ready(function()
{
	$('body').on("click",".buttonDeleteTitle",function()
	{
		var element = $(this).parent().attr('id');
		$.ajax(
			{
				url: path,
				type:"POST",
				data: { req:"deleteTitle_"+$(this).parent().attr('id')},
				dataType: "text",
				success:function()
				{
					//$("#"+ element).attr("src","1_music/view/images/player/done.png");

				$("#"+ element).remove();
				},

				error:function(exception){alert('Exception deleteTitle'+exception);}
			})
	})
})




/*

//partie player--------------------------------------------------------------------------------------------------------------------------------
//Affichage du statut du player Bootstrap - et du son
$(document).ready(function()
{
	window.setInterval(function()
	{
		$.ajax(
		{
			url: path,
			type:"POST",
			data: { req:'playerStatus'},
			dataType: "text",
			success:function(status)
			{
				//console.log("la valeur de retour"+status);
				//console.log("le statut du server "+status);
				//console.log("le dernier statut "+lastStatus);
				//si le nouveau statut est différant de l'ancien (par défaut unknow)
				if (status!=lastStatus) 
					{

						switch(status)
						{
							case "play":
							$("#status").empty().append("<span class='glyphicon glyphicon-pause' ></span>&nbsp;");
							lastStatus = status;
							break;

							case "pause":
							$("#status").empty().append("<span class='glyphicon glyphicon-play' ></span>&nbsp;");
							lastStatus = status;
							break;

							case "stop":
							$("#status").empty().append("<span class='glyphicon glyphicon-play' ></span>&nbsp;");
							lastStatus = status;
							break;

							default:
							console.log("entré dans le case default");
							break;

						}
						
					}
					 else
					{

					};
			},
		})


		$.ajax({
			url: path,
			type:"POST",
			data: {req:'getVolume'},
			dataType: "text",
			success:function(volume)
			{
				if (volume!=lastVolume) 
					{
						
						$("#currentVolume").empty().append("<span class='btn-warning'>"+volume+"</span>&nbsp;");
						lastVolume = volume;
						
					} 
					else
					{
					};
					
			},
			error:function(exception){alert('Exception getVolume'+exception);}
		})

	},1000);
})

*/
$(document).ready(function()
{
	$(".glyphicons-play").on("click",function()
	{
		$.ajax({
			url: path,
			type:"POST",
			data: {req:'play'},
			error:function(exception){alert('Exception play'+exception);}
		})
	})
})

$(document).ready(function()
{
	$(".glyphicons-pause").on("click",function()
	{
		$.ajax({
			url: path,
			type:"POST",
			data: {req:'pause'},
			error:function(exception){alert('Exception pause'+exception);}
		})
	})
})

$(document).ready(function()
{
	$(".glyphicons-stop").on("click",function()
	{
		$.ajax({
			url: path,
			type:"POST",
			data: {req:'stop'},
			error:function(exception){alert('Exception play'+exception);}
		})
	})
})

//Partie réveil-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//rajout d'une alarm en dure
$(document).ready(function()
{
	$('body').on("click",".addAlarm",function()
	{
		$.ajax({
			url: path,
			type:"POST",
			data: {req:'setAlarm_0,1,2,3*8*30*10'},
			error:function(exception){alert('Exception setAlarm'+exception);}
		})
		$("#logoAlarm").remove();
		$("#alarm").append("<img id='logoAlarm' src='1_music/view/images/player/reveil.png' /> ")

	})
})

//suppresion d'une alarm en dure (la seul existante est dans une SESSION)
$(document).ready(function()
{
	$('body').on("click",".deleteAlarm",function()
	{
		$.ajax({
			url: path,
			type:"POST",
			data: {req:'deleteAlarm'},
			error:function(exception){alert('Exception setAlarm'+exception);}
		})

		$("#logoAlarm").remove();
	})
})