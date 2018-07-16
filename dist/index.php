<!DOCTYPE html>
<html>
<meta charset="utf-8">
<head>
	<title>NearHere</title>

	<script type="text/javascript" src="src/jquery-3.3.1.min.js"></script>
	
	<script type="text/javascript" src="https://getuikit.com/assets/uikit/dist/js/uikit.js?nc=1832"></script>
	<script type="text/javascript" src="https://getuikit.com/assets/uikit/dist/js/uikit-icons.js?nc=1832"></script>

	<link rel="stylesheet" type="text/css" href="https://getuikit.com/assets/uikit/dist/css/uikit.min.css">
	<link href='https://fonts.googleapis.com/css?family=Aguafina Script' rel='stylesheet'>

	<script type="text/javascript">
		jQuery(document).ready(function($){

			$('#partnerlist').hide();
			$('#nx-error').hide();

			$('body').on('keyup','#userplz',function(){
				var value = $(this).val();
				numeric = checkNumeric(value);
				if(numeric){
					console.log('ok');
					if(value > 999){
						if($('#nx-error').is(":visible")){
							$('#nx-error').slideUp('slow');
							//$('#nx-error>span').html(''); 
						};
						console.log('Postleitzahl?');
					}else{
						console.log('zu kurz');
						setTimeout(function(){
							if($('#nx-error').is(":hidden")){
								$('#nx-error>span').html('Eintrag zu kurz'); 
								$('#nx-error').slideDown('slow');
							} 
						}, 50);
					}

				}else{
					console.log('keine Nummer');
				}
				console.log(numeric);
			});

			$('#searchplz').click(function(){
				$('#partnerlist').fadeOut('slow', function(){
					$(this).empty();
					var plz = $('#userplz').val();
					console.log(plz);
					getCoordinates(plz);
				});
				
				return false;
			});

			function checkNumeric(value){

				if(isNaN(value)){
					//console.log(value + " is not a number <br/>");
					return false;
				 }else{
					//console.log(value + " is a number <br/>");
					return true;
				 }

			}


			function getCoordinates(plz){
				console.log('Abfrage für '+plz+' gestartet');

				$.ajax({
				    type: "POST",
				    url: 'helper/getCoordinates.php',
				    data: {zipcode: plz},
				    success: function(data){
				    	if(data['lat'] && data['lon']){
				    		getPartners(data['lat'],data['lon']);
				    	}else if (data['error']){
				    		console.log(data['error']);
				    	}else{
				    		console.log('An error occured');
				    	}
				    }
				});


			};

			function getPartners(lat,lon){
				console.log('Searching next Partner for '+lat+' / '+lon);
				

				$.ajax({
				    type: "POST",
				    url: 'helper/getPartners.php',
				    data: {usr_lat: lat, usr_lon: lon},
				    success: function(data){
				    	buildView(data);
				    }
				});

			}

			function buildView(partners){
				console.log(partners);
				for (var i = 0; i < 3; i++) {
					console.log(partners[i]);

					var distance 	= ''+partners[i]['distance'];
    				var shortdist 	= distance.substring(0, 4);

					var partner = '<li><a class="uk-accordion-title uk-text-large" href="#">'+partners[i]['details']['name']+'<br/><span class="uk-text-small">Im Umkreis von ca. '+shortdist+'km</span></a>'
								+ '<div class="uk-accordion-content">'
								+ '<div class="uk-margin-small-left">'
								+ '<h4 class="uk-h6 uk-margin-remove-bottom">Anschrift:</h4>'
			        				+'<ul class="uk-list uk-margin-small-top">'
		        						+'<li><b>'+partners[i]['details']["name"]+'</b></li>'
		        						+'<li>'+partners[i]['details']["street"]+'</li>'
		        						+'<li>'+partners[i]['details']["zip"]+' '+partners[i]['details']["town"]+'</li>'
		        					+'</ul>'
		        					+'<table class="uk-table">';
				        		if(partners[i]['details']["phone"]){
				        			partner += '<tr class="uk-table-small"><td><span class="uk-icon" uk-icon="receiver"</td><td>'+partners[i]['details']["phone"]+'</td></tr>';
				        		}
				        		if(partners[i]['details']["mail"]){
				        			partner += '<tr class="uk-table-small"><td><span class="uk-icon" uk-icon="mail"</td><td>'+partners[i]['details']["mail"]+'</td></tr>';
				        		}
				        		if(partners[i]['details']["web"]){
				        			partner += '<tr class="uk-table-small"><td><span class="uk-icon" uk-icon="world"</td><td>'+partners[i]['details']["web"]+'</td></tr>';
				        		}
		        						
		        				partner +='</table>'
								+ '</div></div></li>';
					$('#partnerlist').append(partner);
					
				}
				$('#partnerlist').fadeIn('slow');
			}

			

		});
	</script>

	<style type="text/css">
		.nx-text sup {
			font-family: 'Aguafina Script';
		}
		.uk-list > li:nth-child(n+2){
			margin-top:0;
		}
		.nx-section{
			/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#1e87f0+0,1e87f0+26,753dce+100 */
			background: #1e87f0; /* Old browsers */
			background: -moz-linear-gradient(45deg,  #1e87f0 0%, #1e87f0 26%, #753dce 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(45deg,  #1e87f0 0%,#1e87f0 26%,#753dce 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(45deg,  #1e87f0 0%,#1e87f0 26%,#753dce 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e87f0', endColorstr='#753dce',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
		}

	</style>





</head>

<body>

	<div class="uk-section uk-section-primary nx-section uk-padding">
		<div class="uk-container uk-text-center uk-flex uk-flex-center">
			<div class="uk-width-1-2">
				<h1 class="uk-heading-primary uk-margin-remove-bottom">Nearhere DB</h1>
				<h2 class="nx-text uk-text-right uk-margin-remove-top">Suchen &amp; Finden <sup>beta</sup></h2>
			</div>
		</div>
	</div>
	<div class="uk-section uk-section-default uk-padding">
		<div class="uk-container uk-container-large">
			<div class="uk-child-width-1-3@m" uk-grid uk-scrollspy="target: > div; cls:uk-animation-fade; delay: 500; repeat: true;">
				<div class="" uk-scrollspy="cls: uk-animation-slide-left; repeat: false">
					<div class="uk-card uk-card-primary uk-card-body">
						<h3><span style="opacity:0.3" class="uk-icon" uk-icon="icon:search; ratio: 2.4;"></span> Nächsten Standort finden</h3>
						<form>
						    <fieldset class="uk-fieldset">
						    	<div class="uk-child-width-1-2" uk-grid>
							        <div class="uk-margin">
							            <input id="userplz" class="uk-input" type="number" placeholder="Postleitzahl">
							        </div>
							        <div>
							        	<button id="searchplz" class="uk-width-1-1 uk-button uk-button-default">Suchen</button>
								    </div>
								</div>
						    </fieldset>
						</form>
					</div>
					<div class="uk-position-relative">
						<div id="nx-error" class="uk-card uk-alert uk-alert-danger uk-margin-remove-bottom uk-margin-remove-top" style="min-height: 5em">
							<!-- Error Meldung -->
							<span></span>
						</div>
					</div>
					<div class="uk-card uk-card-default uk-card-body" style="min-height: 25em">
						<ul id="partnerlist" uk-accordion>
					    
						</ul>
					</div>
				</div>
				<div class="uk-width-2-3 uk-padding uk-padding-remove-top uk-height-large">
					
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		setInterval(function() { $.get('', function() { /* do your aditional task here*/ }); }, 62000);
	</script>

</body>
</html>