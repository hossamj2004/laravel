<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
	  .map-container{
	  	height:700px;
		width:900px;
		float:left;
	  }
	  .list{
	    width:500px;
	  	float:left;
	  }
	  .item {
		padding-top: 5px;
		padding-bottom: 10px;
		padding-left: 30px;
		font-size: 17px;
		font-weight: 400;
	  }
    </style>
  </head>
  <body>
  <div class="list">
		<div class="item" > <h3>Messages :</h3>
		  <select class="sentiment-select" >
			<option value="all_items">All</option>
			<option>Neutral</option>
			<option>Positive</option>
			<option>Negative</option> 
		  </select>
		</div>
		<?php foreach($messages as $message ){ ?>
			<div class="<?php echo $message['sentiment'] ?>  all_items item">
				<?php echo $message['date'] ?>
				<a class="item-link" data-messageid="<?php echo $message['messageid'] ?>" style="color:<?php echo $message['color'] ?>" href="javascript:void(0)" >
					<?php echo $message['message'] ?> 			    
				</a><br>
			</div>
		<?php } ?>
	</div>
  	<div class="map-container">
    	<div id="map"></div>
	</div>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYCTaWEia3TFN9qJhm9x8jZSH1nCULaoM&callback=initMap&libraries=places" async defer></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
	var map;
	var service;
	var infowindow;
	function initMap()
	{
		var mapCenter = new google.maps.LatLng(30,30);
		map = new google.maps.Map(document.getElementById('map'), {
			center: mapCenter,
			zoom: 1
			});
		<?php foreach($messages as $message )
		{
			?>
			mapHelper.getLocationFromString( "<?php echo $message['message'] ?>" ,<?php echo json_encode($message )?> );
			<?php } ?>
	}
	$(document).ready(function(){
		$('.item-link').click(function(){
			map.setZoom(5);map.panTo(mapHelper.markersArr[$(this).data('messageid')].position);
		});
		$('.sentiment-select').click(function(){
			$('.all_items').hide();
			$('.all_items').each(function(){
				console.log($(this).find('a').data('messageid'));
				mapHelper.markersArr[$(this).find('a.item-link').data('messageid')].setVisible(false);
			});
			$('.'+$(this).val()).show();
			$('.'+$(this).val()).each(function(){
				mapHelper.markersArr[$(this).find('a.item-link').data('messageid')].setVisible(true);
			});
		}
		);
	}
	);
	var mapHelper = {
		markersArr:[],
		getLocationFromString:function (string,message)
		{
			var address = string;
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({'address': address}, function (results, status)
			{
				if (status == google.maps.places.PlacesServiceStatus.OK && typeof results != 'undefined')
				{
					for (var i = 0; i < results.length; i++)
					{
						var place = results[i];
						mapHelper.createMarker(results[i],this);
						break;
					}
				}
				}.bind(message));
		}
		,
		createMarker:function (place,message)
		{
		    var infowindow = new google.maps.InfoWindow({
	          content: message.date+" <br> "+message.message,
	        });
			mapHelper.markersArr[message.messageid] = new google.maps.Marker({
				position: place.geometry.location,
				map: map,
				title:message.date+" "+message.message,
				icon:'http://maps.google.com/mapfiles/ms/icons/' +( message.color ) + '-dot.png',
				opacity: message.opacity
				});
			mapHelper.markersArr[message.messageid].addListener('click', function() {
         		 infowindow.open(map, mapHelper.markersArr[message.messageid]);
				 
        	});
		}
	}
	
	
	</script>
  </body>
</html>