<?php
/*
Plugin Name: Map Area Checker
Description: Allow users to put their address to see it inside  or outside our area service
Author: Fabianus Yayan
Author URI: https://www.instagram.com/mumukemasanbaru/
Version: 1.0
Text Domain: mac
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MAC_PATH', plugin_dir_path(__FILE__));
define('MAC_URL', plugin_dir_url(__FILE__));
define('MAC_SHORTCODE', 'MAC_shortcode');

require_once MAC_PATH . 'inc/mac-admin.php';

/**
 * Map Area Checker class
 */
class Map_area_checker
{

    private $token_mapbox;
    private $inside_url;
    private $outside_url;
    private $map_json;

    /**
     * Construct
     */
    public function __construct()
    {

        new MAC_Admin();

        add_shortcode('MAC_shortcode', array($this, 'mac_shortcode'));
    }

    /**
     * shortcode callback
     */
    public function mac_shortcode()
    {
        $this->token_mapbox = get_option('mac_apikey');

        if(empty($this->token_mapbox)){
        	echo __('Token empty please get token <a href="https://account.mapbox.com/"> here</a>','mca'); return; 
        }


        $this->inside_url = get_option('mac_inside_url');
        $this->outside_url = get_option('mac_outside_url');
        $json_url = get_option('mac_zoneurl');
        $this->map_json = (!empty($json_url)) ? file_get_contents($json_url) : '';

        add_action('wp_footer', array($this, 'mac_footer_script'));

        ob_start();

        ?>
        <div class="mac-wrapper">
			<form id="locationForm">
			  <label for="location"><?php echo __('Location', 'mac'); ?></label>
			  <input type="text" id="location" name="location" placeholder="<?php echo __('Enter a location at least 3 character','mca'); ?>">
			  <input type="hidden" id="latitude" name="latitude">
			  <input type="hidden" id="longitude" name="longitude">
			  <ul id="suggestionList" class="suggestion-list"></ul>
			  <div id="loadingIndicator"><?php echo __('Loading...','mac'); ?></div>
			  <button type="button" onclick="checkPoint()"><?php echo __('Check Address','mac'); ?></button>
			  <p id="result"> </p>
			</form>
			<div id="map"></div>

		</div>


		<?php

        $content = ob_get_contents();
        ob_end_clean();
        echo $content;

    }

    /**
     * footer script
     */
    public function mac_footer_script()
    {
        ob_start();
        ?>

    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
		<script src="<?php echo MAC_URL; ?>/assets/js/mapbox.js"></script>
		<link href="<?php echo MAC_URL; ?>/assets/css/mapbox.css"rel="stylesheet"/>

		<script>
		  mapboxgl.accessToken = '<?php echo $this->token_mapbox; ?>';

			var map = new mapboxgl.Map({
			    container: 'map',
			    style: 'mapbox://styles/mapbox/streets-v11',
			    center: [144.9749006, -37.8155281],
			    zoom: 13
			});

			var melbournePolygon = <?php echo $this->map_json; ?>;

			map.on('load', function () {
			    map.addSource('melbourne', {
			        type: 'geojson',
			        data: melbournePolygon
			    });

			    map.addLayer({
			        id: 'melbourne-fill',
			        type: 'fill',
			        source: 'melbourne',
			        paint: {
			            'fill-color': '#0080ff',
			            'fill-opacity': 0.4
			        }
			    });

			    map.addLayer({
			        id: 'melbourne-outline',
			        type: 'line',
			        source: 'melbourne',
			        paint: {
			            'line-color': '#0080ff',
			            'line-width': 2
			        }
			    });
			});

			var typingTimer;
			var doneTypingInterval = 1000; // 2 seconds

			function geocode() {
			    clearTimeout(typingTimer);
			    var locationInput = document.getElementById('location').value;


			    if (locationInput.length < 3) { return; } var loadingIndicator = document.getElementById('loadingIndicator'); loadingIndicator.style.display = 'block'; typingTimer = setTimeout(function () {
			        fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(locationInput)}.json?proximity=-73,40.740121&access_token=<?php echo $this->token_mapbox; ?>`).then(response => response.json())
			        .then(data => {

			            populateSuggestions(data.features);

			            loadingIndicator.style.display = 'none';
			        })
			        .catch(error => {
			            console.error('Error:', error);

			            loadingIndicator.style.display = 'none';
			        });
			    }, doneTypingInterval);
			}

			function populateSuggestions(suggestions) {
			    var suggestionList = document.getElementById('suggestionList');
			    suggestionList.innerHTML = '';

			    suggestions.forEach(function (feature) {
			        var li = document.createElement('li');
			        li.textContent = feature.place_name;
			        li.addEventListener('click', function () {
			            document.getElementById('location').value = feature.place_name;


			            var latitude = document.getElementById('latitude');
			            var longitude = document.getElementById('longitude');

			            var coordinates = feature.geometry.coordinates;

			            latitude.value = coordinates[1];
			            longitude.value = coordinates[0];
			            suggestionList.innerHTML = '';
			            map.setCenter(coordinates);
			            new mapboxgl.Marker()
			                .setLngLat(coordinates)
			                .addTo(map);

			        });
			        suggestionList.appendChild(li);
			    });
			}


			document.getElementById('location').addEventListener('keyup', function () {
			    geocode();
			});


			function checkPoint() {
			    var latitude = parseFloat(document.getElementById('latitude').value);
			    var longitude = parseFloat(document.getElementById('longitude').value);

			    if (isNaN(latitude) || isNaN(longitude)) {
			        alert('Please enter valid coordinates.');
			        return;
			    }

			    var point = turf.point([longitude, latitude]);
			    var isInside = turf.booleanPointInPolygon(point, melbournePolygon.features[0].geometry);

			    var resultElement = document.getElementById('result');
			    resultElement.textContent = isInside ? 'Inside Melbourne CBD' : 'Outside Melbourne CBD';

			    console.log(isInside);

			    if (true === isInside) {
			        window.location.href = '<?php echo $this->inside_url; ?>';
			    } else {
			        window.location.href = '<?php echo $this->outside_url; ?>';
			    }

			}

		</script>

		<?php

        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
}

new Map_area_checker();