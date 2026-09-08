
<?php
/*//////////////////////////////php info//////////////////////////////////////////////////////////
phpinfo();
die();

/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* /////////////////////diagnostic test for missing certificate info///////////////////////////////////
echo 'PHP: ' . PHP_VERSION . '<br>';
echo 'cURL: ' . curl_version()['version'] . '<br>';
echo 'CA info: ' . ini_get('curl.cainfo') . '<br>';
echo 'OpenSSL CA: ' . ini_get('openssl.cafile') . '<br>';
die();
//*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once 'apikeys.php';
// API key placeholders that must be filled in by users.
// You can find it on
// https://www.yelp.com/developers/v3/manage_app
$API_KEY = $YELP_API_KEY;
// Complain if credentials haven't been filled out.

//assert($API_KEY, "Please supply your API key.");
// API constants, you shouldn't have to change these.
$API_HOST = "https://api.yelp.com";
$SEARCH_PATH = "/v3/businesses/search";
$BUSINESS_PATH = "/v3/businesses/";  // Business ID will come after slash.
// Defaults for our simple example.
$DEFAULT_TERM = "dinner";
$DEFAULT_LOCATION = "San Diego, CA";
$SEARCH_LIMIT = 40;
/**
 * Makes a request to the Yelp API and returns the response
 *
 * @param    $host    The domain host of the API
 * @param    $path    The path of the API after the domain.
 * @param    $url_params    Array of query-string parameters.
 * @return   The JSON response from the request
 */
function request($host, $path, $url_params = array()) {
    // Send Yelp API Call
    try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');
        $url = $host . $path . "?" . http_build_query($url_params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $GLOBALS['CURL_AUTH'],
                "cache-control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);
        curl_close($curl);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }
    return $response;
}
/**
 * Query the Search API by a search term and location
 *
 * @param    $term        The search term passed to the API
 * @param    $location    The search location passed to the API
 * @return   The JSON response from the request
 */
function search($term, $location) {
    $url_params = array();

    $url_params['term'] = $term;
    $url_params['location'] = $location;
    $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];

    return request($GLOBALS['API_HOST'], $GLOBALS['SEARCH_PATH'], $url_params);
}
/**
 * Query the Business API by business_id
 *
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request
 */
function get_business($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id);

    return request($GLOBALS['API_HOST'], $business_path);
}
/**
 * Queries the API by the input values from the user
 *
 * @param    $term        The search term to query
 * @param    $location    The location of the business to query
 */


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function query_api($term, $location)
{

    $response = json_decode(search( $term, $location));
    //$business_id = $response->businesses[0]->id;
    //$coordinates = $response->businesses[0]->coordinates;
    /* $count = count($response->businesses); //may use for looping or display */
    // echo $coordinates;

    $business_info = array();

    foreach ($response->businesses as $business) {

        $fill_array['bizname'] = $business->name;
        $fill_array['image'] = $business->image_url;
        $fill_array['lat'] = $business->coordinates->latitude;
        $fill_array['long'] = $business->coordinates->longitude;
        $fill_array['address'] = $business->location->address1;
        $fill_array['zip'] = $business->location->zip_code;
        $fill_array['rating'] = $business->rating;

        array_push($business_info, $fill_array);
    }

    return json_encode($business_info);


    // $response = get_business($bearer_token, $business_id);
    /*
        echo json_encode(get_business($bearer_token, $business_id));
    */


}



/**
 * User input is handled here
 */

$term = $_POST['term'] ?: $GLOBALS['DEFAULT_TERM'];

//original from online version
//$location = $options['location'] ?: $GLOBALS['DEFAULT_LOCATION'];

//test for location
$location = $GLOBALS['DEFAULT_LOCATION'];



//query_api($term, $location);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * User input is handled here
 */
/*
$longopts  = array(
    "term::",
    "location::",
);

$options = getopt("", $longopts);
$term = $options['term'] ?: $GLOBALS['DEFAULT_TERM'];
$location = $options['location'] ?: $GLOBALS['DEFAULT_LOCATION'];
query_api($term, $location);

*/


echo '
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DreamBiz Location Finder</title>
	<meta name="description" content="DreamBiz helps new business owners by offering the best location for a new business. Extensive dataset creates an accurate model for finding the best traffic areas for you " />
	
	<link rel="canonical" href="https://dreambiz.today/" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="DreamBiz Location Finder" />
	<meta property="og:description" content="DreamBiz helps new business owners by offering the best location for a new business. Extensive dataset creates an accurate model for finding the best traffic areas for you " />
	<meta property="og:url" content="http://dreambiz.today/" />
	<meta property="og:site_name" content="DreamBiz" />
	<meta property="og:image" content="http://sagitarra.com/wp-content/uploads/2021/02/dreambiz-screen-shot.png" />
    <link rel="stylesheet" href="main.css"/>
    <link rel="stylesheet" href="dashboard-map-styles.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cabin">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">




</head>
<body>
<header class="dbiz-header" id="top">

    <nav class= "dropdown dbiz-nav">
        <ul>
            <li><a href="https://dreambiz.today/">HOME</a></li>
            <li class="dropdown" style="position:relative; z-index:600;">
                <a href="javascript:void(0)" class="dropbtn">SEARCH</a>
                <div class="dropdown-content">
                    <a href="https://dreambiz.today/search-coffee.html">coffee</a>
                    <a href="https://dreambiz.today/find-coffee.html">find</a>
                    <a href="https://dreambiz.today/compare-coffee.html">compare</a>
                    <a href="https://dreambiz.today/search-beer.html">beersearch</a>
                </div>
            </li>
            <li><a href="https://olsonb.carto.com/builder/52a15bec-c8e4-454f-b4ac-7987016498d8/embed">TECH</a></li>
            <li><a href="https://dreambiz.today/beer-land.html">FURTHER</a></li>

        </ul>
    </nav>

</header>


<div class="map-container">
    <div class="control-box">
      <div class="control-header">
      <h3>DREAMBIZ</h3>
      </div>
      <div class="controls">
       <div class="control-segment" id="industryfilter">
   <div class="control-segment-header">
   <p>INDUSTRY FILTER</p>
   <button id="editindustry" onclick="industryEdit()">Edit Filter</button>
   <button id="saveindustry" onclick="industrySend()">Submit</button>
   </div>
   <h4>  ' . $term . '</h4>
   </div>
    <div class="control-segment" id="storeproperties">
   <div class="control-segment-header">
   <p>STORE PROPERTIES</p>
   <button id="editproperties" onclick="propertiesEdit()">Edit Filter</button>
   <button id="saveproperties" onclick="propertiesSend()">Submit</button>
   </div>

  <div class="slider-box">
  <div class="display-slider-amount">
  <input type="text" class="amountone" id="priceone" readonly >
  <input type="text" class="amounttwo" id="pricetwo" readonly >
  </div>
  <div class="slider-slot">
  <div id="slider-price"></div>
  </div>
  <p>Price range per month (USD)</p>
</div>
<div class="slider-box">
  <div class="display-slider-amount">
  <input type="text" class="amountone" id="sizeone" readonly >
  <input type="text" class="amounttwo" id="sizetwo" readonly >
  </div>
  <div class="slider-slot">
  <div id="slider-size"></div>
  </div>
  <p>Store size in square feet</p>
</div>
</div>

   <div class="control-segment" id="activity">
   <div class="control-segment-header">
   <p>SD CITY ACTIVITY</p>
   <button id="editactivity" onclick="activityEdit()">Edit Filter</button>
   <button id="saveactivity" onclick="activitySend()">Submit</button>
   </div>

  <div class="slider-box">
  <div class="display-slider-amount">
  <input type="text" class="amountone" id="activityone" readonly >
  <input type="text" class="amounttwo" id="activitytwo" readonly >
  </div>
  <div class="slider-slot">
  <div id="slider-activity"></div>
  </div>

</div>

      <form class="activitypicker" action="">
  <input type="checkbox"  name="activity" value="pedestrianvolume" checked>   Pedestrian Volume<br>
  <input type="checkbox" onclick="toggleTrafficLayer()" name="activity" value="trafficvolume" >   Traffic Volume<br>
  <input type="checkbox" onclick="toggleSocialMediaLayer()" name="activity" value="socmediacoverage" checked>   Social Media Coverage<br>
  <input type="checkbox" name="activity" value="parkavailability" checked>   Parking Availability<br>

  <input type="checkbox" onclick="toggleTransitLayer()" name="activity" value="transitRoutes"> Transit Routes<br>
  <input type="checkbox" onclick="toggleBikeLayer()" name="activity" value="bikeRoutes"> Bicycle Routes<br>


</form>
</div>
<!--
<button onclick="toggleCompetitionShadow()">Toggle Heatmap</button>
-->
      </div>
    </div>

    <div class="map-box">
<div class="map-header"><p>' . $term . ' in San Diego</p></div>
       <div id="map"></div>
       <div id="content"></div>

    </div>
    </div>
</div>

</body>
    <script>



    var map, heatmap, socialmediaheatmap, trafficLayer, transitLayer, bikeLayer, popup, Popup;
      function initMap() {

             definePopupClass();//defines googles Popup class (below)

       var availablesite = getSiteList();//mock data from loopnet for available properties


        var dago = {lat: 32.715701, lng: -117.160130},//location variable hard coded to location of US Grant hotel - still the center of town! (a default central location for hackathon datasets)
         data =  ' . query_api($term, $location) . ',//data from yelp api search - location and term instantiated in above php code
         searchterm = "'.$term.'",
         pricetag = \'https://dreambiz.today/image/forleasetag.png\',
        availsizelow = 1000000000;
        var availsizehigh = 0;
        var availpricelow = 1000000000;
        var availpricehigh = 0;
        var filtersizelow = document.getElementById("sizeone").value;
        var filtersizehigh =  document.getElementById("sizetwo").value;
        var filterpricelow = document.getElementById("priceone").value;
        var filterpricehigh = document.getElementById("pricetwo").value;
        var infowindow = new google.maps.InfoWindow();
        var asinfowindow = new google.maps.InfoWindow();
        var twit = 1234.56; //for the twitter - social media heatmap
        var propertyposition;

             //////////////////////////////////creates the demand grid for twitter blue water heatmap////////////////////////////////////////////////
function generateDemandGrid(centerLat, centerLon, radiusMiles, spacingMiles) {

    var points = [];

    // Approximate miles per degree
    var milesPerLat = 69;
    var milesPerLon = 69 * Math.cos(centerLat * Math.PI / 180);

    var latStep = spacingMiles / milesPerLat;
    var lonStep = spacingMiles / milesPerLon;

    var latMin = centerLat - (radiusMiles / milesPerLat);
    var latMax = centerLat + (radiusMiles / milesPerLat);
    var lonMin = centerLon - (radiusMiles / milesPerLon);
    var lonMax = centerLon + (radiusMiles / milesPerLon);

    for (var lat = latMin; lat <= latMax; lat += latStep) {

        for (var lon = lonMin; lon <= lonMax; lon += lonStep) {

            var latDistance = (lat - centerLat) * milesPerLat;
            var lonDistance = (lon - centerLon) * milesPerLon;

            var distance = Math.sqrt(
                latDistance * latDistance +
                lonDistance * lonDistance
            );

            if (distance <= radiusMiles) {
                points.push({
                    lat: lat,
                    lon: lon
                });
            }
        }
    }

    return points;
}

// Create 1-mile circular demand area on 0.05-mile grid
var demandGrid = generateDemandGrid(
    dago.lat,
    dago.lng,
    1,
    0.05
);



       //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        map = new google.maps.Map(document.getElementById(\'map\'),{
          zoom: 15,
          center: dago,
          styles: [{featureType: \'landscape.natural\',
                    elementType: \'geometry.fill\',
                    stylers: [{ visibility: \'on\'},{color: \'#e0efef\'}]
                    },
                   {featureType: \'poi\',
                    elementType: \'geometry.fill\',
                    stylers: [{visibility: \'on\'}, {hue: \'#1900ff\'},{color: \'#c0e8e8\'}]
                    },
                   {featureType: \'road\',
                    elementType: \'geometry\',
                    stylers: [{lightness: 100},{visibility: \'simplified\'}]
                    },
                   {featureType: \'road\',
                    elementType: \'labels\',
                    stylers: [{visibility: \'off\'}]
                    },
                   {featureType: \'transit.line\',
                    elementType: \'geometry\',
                    stylers: [{visibility: \'on\'},{lightness: 100}]
                    },
                       {
              featureType: \'transit\',
              elementType: \'geometry\',
              stylers: [{color:\'#8f00b3\'}]
            },
            {
              featureType: \'transit.station\',
              elementType: \'labels.text.fill\',
              stylers: [{color: \'#520066\'}]
            },
                   {featureType: \'water\',
                    elementType: \'all\',
                    stylers: [{ color: \'#7dcdcd\'}]}]});

        /* TEST: one blue demand circle
new google.maps.Circle({
    map: map,
    center: {
        lat: 32.701208246376815,
        lng: -117.16013000000008
    },
    radius: 500,
    fillColor: \'#5A8BFF\',
    fillOpacity: 0.5,
    strokeOpacity: 0
});
//*/

//* PSEUDO DEMAND

var demandCenters = [
    { lat: 32.7157, lon: -117.1601, strength: 100 },
    { lat: 32.7205, lon: -117.1550, strength: 75 },
    { lat: 32.7100, lon: -117.1680, strength: 60 }
];

for (var gridIndex = 0; gridIndex < demandGrid.length; gridIndex++) {

    var point = demandGrid[gridIndex];
    var demand = 0;

    for (var centerIndex = 0; centerIndex < demandCenters.length; centerIndex++) {

        var center = demandCenters[centerIndex];

        var latDistance = (point.lat - center.lat) * 69;
        var lonDistance = (point.lon - center.lon) *
            (69 * Math.cos(point.lat * Math.PI / 180));

        var distance = Math.sqrt(
            latDistance * latDistance +
            lonDistance * lonDistance
        );

        demand += center.strength *
            Math.exp(-(distance * distance) / 0.15);
    }

    demand = Math.max(0, Math.min(100, demand));

    new google.maps.Circle({
        map: map,
        center: {
            lat: point.lat,
            lng: point.lon
        },
        radius: 40,
        fillColor: \'#5A8BFF\',
        fillOpacity: 0.05 + (demand / 100) * 0.45,
        strokeOpacity: 0
    });
}
//*/
      //creates array for heatmap to be filled with dark competitor clouds
      var heatmaparray = [];

      //for loop creates map pins from data array for business competitors collected from yelp API
      for(var i = 0; i < data.length; i += 1) {

        var rating = data[i].rating;
      //instantiate position variable from data array
         var pos = new google.maps.LatLng(data[i].lat,data[i].long);
         heatmaparray.push({location: pos, weight: rating});// syntax from google API reference -- {location: new google.maps.LatLng(37.782, -122.447), weight: 0.5},

         //get the rating value and change it to part of the stars image name - take out the decimal or add a zero
         var stars = "";
         switch(rating){
            case 5: stars = "50";
            break;
            case 4.5: stars = "45";
            break;
             case 4: stars = "40";
            break;
             case 3.5: stars = "35";
            break;
             case 3: stars = "30";
            break;
             case 2.5: stars = "25";
            break;
             case 2: stars = "20";
            break;
             case 1.5: stars = "15";
            break;
             case 1: stars = "10";
            break;
             case 0.5: stars = "05";
            break;
          default: stars = "35" //todo prolly needs a better response and maybe some logging
         }


         //instantiate variable string of html code for info window that pops up when map pin is clicked on
         var contentString = \'<div id="infowincontent">\'+
            \'<div id="infowinbiz"><p>\' + data[i].bizname + \'</p><p><span id="infowinbizsqft">\' +
             data[i].address +\'</span><span id="infowinbizprice">\' + \'possible competitor\' + \'</span></p></div>\' +
            \'<div id="infowinfsale">\' + \'<img src="image/\' + stars +\'star.png" alt="" style="padding:0 8px 0 0;"></div>\' +
            \'<div id="infowinimg"><img src="https://maps.googleapis.com/maps/api/streetview?size=260x165&location=\' + data[i].address + \',\' + data[i].zip + \'&key=AIzaSyBa--AD2C0bdNrO3JRpRm9J5OAV9acMU6w" alt=""></div>\' +
            \'<div id="infowingraf"><img src="image/pedchart.png"></div>\' +
            \'<div id="infowingrafdesc" >\' + \'Pedestrian volume\' + \'</div>\' +
            \'<div id="infowindetails" >\' +
            \'<div id="iwinstortraffic" ><div id="iwinstortrafficamt" ></div></div>\' +
             \'<div id="infowingrafdesc" >\' + \'Traffic \' + \'</div>\' +
             \'<div id="iwinstortraffic" ><div id="iwinstortrafficamt" style="width:34%" ></div></div>\' +
             \'<div id="infowingrafdesc" >\' + \'Transit\' + \'</div>\' +
             \'<div id="iwinstortraffic" ><div id="iwinstortrafficamt" style="width:82%"></div></div>\' +
             \'<div id="infowingrafdesc" >\' + \'Parking:\' + \'</div>\' +
                 \'<div id="infowinbutblkhide" onclick="hideDetails()"><p>Hide Details</p></div>\' +

             \'</div>\' +
            \'<div id="infowinbutblk" onclick="showDetails()"><p>Show Details</p></div>\'
            ;
         //creates map pin, gives it the specific info window content and binds the info window listener
         var marker = new google.maps.Marker({position: pos,map: map,icon: \'https://dreambiz.today/image/pin-comp.png\' });
         marker.content = contentString;
         var infoWindow = new google.maps.InfoWindow();
         google.maps.event.addListener(marker, \'click\', function () {
                                infoWindow.setContent(this.content);
                                infoWindow.open(this.getMap(), this);
                            });

                          // popup = new Popup( pos, document.getElementById(\'content\'));
                 //  popup.setMap(map);//googles setMap for popup that replaces info window

       }//end for loop

        //for loop creates map pins from availablesite array
       var socialmediaheatmaparray = [];

        var length = availablesite.length;
      for( i = 0; i < length; i += 1) {

         var arraysizelow = availablesite[i].size.low;
         var arraysizehigh = availablesite[i].size.high;
         var arraypricelow = availablesite[i].price.high * arraysizelow / 12;
         var arraypricehigh = availablesite[i].price.low * arraysizehigh / 12;
         var lat = availablesite[i].lat;
         var lon = availablesite[i].long;


      //run filters on availablesite array
       if ((filtersizelow <= arraysizelow  &&  filtersizehigh >= arraysizehigh)
       || ( filterpricelow <= arraypricehigh  &&  filterpricehigh >= arraypricelow  ))
      {

/*//////////////////////////////////////////testing demand grid ajax call//////////////////////////////////////////////////////////////////////////////////////////


            $.ajax({
    url: "twit2.php",
    type: "GET",
    data: {
        "term": "beer",
        "lat": dago.lat,
        "lon": dago.lon
    },
    success: function(result) {
        console.log("Twitter result:", result);
    },
    error: function(xhr, status, error) {
        console.log("Twitter error:", status, error);
    }
});
//*/////////////////////////////////////////////////end testing demand grid ajax call///////////////////////////////////////////////////////


/*////////////////////////////////////////////////////top of original ajax call///////////////////////////////////////////////////////////////

   $.ajax({
            url: "twit.php",
            type: "GET",
            data:  {"term": searchterm,"lat":lat,"lon":lon},

            success: function (twitterresult) {
                //create new row with answer  todo check for msg[0].value being null and handle it - used the catch below...prolly needs work
                if (twitterresult != null) {

                    try {
                        //do something with info

                      twit = twitterresult;
                      
                 
                      
                    } catch (e) {

                        //do something if try fails
                        console.log( "failed on try");
                    }
                }

                else {
                    //do something if returns null
                    console.log( "----------empty response -------");
                }
            }

        });
//*///////////////////////////////bottom of ajax call////////////////////////////////////////////////////////////////////////////////////


         //instantiate variable string of html code for info window that pops up when map pin is clicked on
         contentString = \'<div id="infowincontent">\'+
            \'<div id="infowinbiz"><p>\' + availablesite[i].address + \'<span id="infowinbiztype">\' + availablesite[i].type + \'</span></p><p><span id="infowinbizsqft">\' +
            availablesite[i].size.list + \'</span><span id="infowinbizprice">\' + availablesite[i].price.list + \'</span></p></div>\' +
            \'<div id="infowinfsale">\' + \'<img src="\' + pricetag +\'" alt="">"</div>\' +
            \'<div id="infowinimg"><img src="https://maps.googleapis.com/maps/api/streetview?size=260x165&location=\' + availablesite[i].address + \', 92101&key=AIzaSyCJCQuSHbstED_Gcm8S0QBb5AYMHx6auxg" alt=""></div>\' +
            \'<div id="infowingraf"><img src="image/pedchart.png"></div>\' +
            \'<div id="infowingrafdesc" >\' + \'Pedestrian volume\' + \'</div>\' +
              \'<div id="infowindetails" >\' +
            \'<div id="iwinstortraffic" ><div id="iwinstortrafficamt" ></div></div>\' +
             \'<div id="infowingrafdesc" >\' + \'Traffic \' + \'</div>\' +
             \'<div id="iwinstortraffic" ><div id="iwinstortrafficamt" style="width:34%" ></div></div>\' +
             \'<div id="infowingrafdesc" >\' + \'Transit\' + \'</div>\' +
             \'<div id="iwinstortraffic" ><div id="iwinstortrafficamt" style="width:82%"></div></div>\' +
             \'<div id="infowingrafdesc" >\' + \'Parking\' + \'</div>\' +
             \'<div id="infowingrafdesc" >\' + \'Description: \' + availablesite[i].description + \'</div>\' +
             \'<div id="infowingrafdesc" >\' + \'Contact:\' + availablesite[i].broker.name + \' \' + availablesite[i].broker.contact + \'</div>\' +
                 \'<div id="infowinbutblkhide" onclick="hideDetails()"><p>Hide Details</p></div>\' +

             \'</div>\' +
            \'<div id="infowinbutblk" onclick="showDetails()"><p>Show Details</p></div>\'
            ;
/*///////////////////////////pre-deprecation code with google maps marker and heat mapping/////////////////////////////////////
              //instantiate position variable from loopnet data array for making available property data points - still has heatmap weight from twitter
              //TODO move the heatmap and weight functions to a separate layer wherein heatmap covers the whole view and locations are actual twitter users tweet or home location
         propertyposition = new google.maps.LatLng(lat,lon);
         socialmediaheatmaparray.push({location: propertyposition, weight: twit});
         //creates map pin, gives it the specific info window content and binds the info window listener
         marker = new google.maps.Marker({position: propertyposition,map: map,icon: \'https://dreambiz.today/image/pin-grow.png\' });
         marker.content = contentString;
         infoWindow = new google.maps.InfoWindow();
         google.maps.event.addListener(marker, \'click\', function () {
                                infoWindow.setContent(this.content);
                                infoWindow.open(this.getMap(), this);
//*/////////////////////////////////////////////////////////////////////////////////////////////////////
//*/////////////////////////////////new code with heatmap array push removed///////////////////////////////////////////
                    // Available property location
            propertyposition = new google.maps.LatLng(lat, lon);

            // creates map pin, gives it the specific info window content
            // and binds the info window listener
            marker = new google.maps.Marker({
                position: propertyposition,
                map: map,
                icon: \'https://dreambiz.today/image/pin-grow.png\'
            });

            marker.content = contentString;
            infoWindow = new google.maps.InfoWindow();

            google.maps.event.addListener(marker, \'click\', function () {
                infoWindow.setContent(this.content);
                infoWindow.open(this.getMap(), this);
//*///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            });


      //  popup = new Popup(
          //  new google.maps.LatLng(lat, lon),
            //    document.getElementById(\'content\'));

     //   popup.setMap(map);//googles setMap for popup that replaces info window

                               // setTimeout(function () { GeocodeMarker.info.open(GoogleMap, GeocodeMarker); }, 300);//an attempt to slow loading to give twitter a chance to fill infowindow
       }//end filters
       }//end for loop


       document.getElementById("sizeone").value = filtersizelow;
       document.getElementById("sizetwo").value = filtersizehigh;
       document.getElementById("priceone").value = filterpricelow;
       document.getElementById("pricetwo").value = filterpricehigh;



         var socmedArray = new google.maps.MVCArray(socialmediaheatmaparray);

        /*  socialmediaheatmap = new google.maps.visualization.HeatmapLayer({
          data: socmedArray,
          radius: 100,
          gradient:["rgba(90, 139, 255, 0)", "rgba(90, 139, 255, 0.03)","rgba(90,139,255, 1)"],
          map: map
        });
        */
           var pointArray = new google.maps.MVCArray(heatmaparray);
         /*
          heatmap = new google.maps.visualization.HeatmapLayer({
          data: pointArray,
          radius: 90,
          gradient:[ "rgba(255, 0, 0, 0)","rgba(255, 0, 0, 0.3)","rgba(255, 0, 0, 1)"],
          map: map
        });
        */

       trafficLayer = new google.maps.TrafficLayer();
       // trafficLayer.setMap(map);

         transitLayer = new google.maps.TransitLayer();
        // transitLayer.setMap(map);

         bikeLayer = new google.maps.BicyclingLayer();
        // bikeLayer.setMap(map);


      }//end initMap()



/** Defines the Popup class. */
function definePopupClass() {
  /**
   * A customized popup on the map.
   * @param {!google.maps.LatLng} position
   * @param {!Element} content
   * @constructor
   * @extends {google.maps.OverlayView}
   */
  Popup = function(position, content) {
    this.position = position; console.log("position: " + position + "  and content:" + content);

    content.classList.add(\'infowincontent\');

    var pixelOffset = document.createElement(\'div\');
    pixelOffset.classList.add(\'popup-bubble-anchor\');
    pixelOffset.appendChild(content);

    this.anchor = document.createElement(\'div\');
    this.anchor.classList.add(\'popup-tip-anchor\');
    this.anchor.appendChild(pixelOffset);

    // Optionally stop clicks, etc., from bubbling up to the map.
    this.stopEventPropagation();
  };
  // NOTE: google.maps.OverlayView is only defined once the Maps API has
  // loaded. That is why Popup is defined inside initMap().
  Popup.prototype = Object.create(google.maps.OverlayView.prototype);

  /** Called when the popup is added to the map. */
  Popup.prototype.onAdd = function() {
    this.getPanes().floatPane.appendChild(this.anchor);
  };

  /** Called when the popup is removed from the map. */
  Popup.prototype.onRemove = function() {
    if (this.anchor.parentElement) {
      this.anchor.parentElement.removeChild(this.anchor);
    }
  };

  /** Called when the popup needs to draw itself. */
  Popup.prototype.draw = function() {
    var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
    // Hide the popup when it is far out of view.
    var display =
        Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
        \'block\' :
        \'none\';

    if (display === \'block\') {
      this.anchor.style.left = divPosition.x + \'px\';
      this.anchor.style.top = divPosition.y + \'px\';
    }
    if (this.anchor.style.display !== display) {
      this.anchor.style.display = display;
    }
  };

  /** Stops clicks/drags from bubbling up to the map. */
  Popup.prototype.stopEventPropagation = function() {
    var anchor = this.anchor;
    anchor.style.cursor = \'auto\';

    [\'click\', \'dblclick\', \'contextmenu\', \'wheel\', \'mousedown\', \'touchstart\',
     \'pointerdown\']
        .forEach(function(event) {
          anchor.addEventListener(event, function(e) {
            e.stopPropagation();
          });
        });
  };
  }//end function definePopupClass()


     function toggleSocialMediaLayer(){
        socialmediaheatmap.setMap(heatmap.getMap() ? null : map);
     }

      function toggleCompetitionShadow() {
        heatmap.setMap(heatmap.getMap() ? null : map);
      }

      function toggleTrafficLayer(){
      trafficLayer.setMap(trafficLayer.getMap() ? null : map);
      }

      function toggleTransitLayer(){
      transitLayer.setMap(transitLayer.getMap() ? null : map);
      }
      function toggleBikeLayer(){
      bikeLayer.setMap(bikeLayer.getMap() ? null : map);
      }


        $( function() {
    $( "#slider-price" ).slider({
      range: true,
      min: 0,
      max: 75000,
      values: [ 600, 30000 ],
      slide: function( event, ui ) {
        $( "#priceone" ).val(  ui.values[ 0 ] );
        $( "#pricetwo" ).val(  ui.values[ 1 ] );
      propertiesEdit();
      }
    });
   $( "#priceone" ).val( $( "#slider-price" ).slider( "values", 0 ));
    $( "#pricetwo" ).val($( "#slider-price" ).slider( "values", 1 ));

  } );

          $( function() {

    $( "#slider-size" ).slider({
      range: true,
      min: 100,
      max: 40000,
      values: [ 100, 10000 ],
      slide: function( event, ui ) {
        $( "#sizeone" ).val(  ui.values[ 0 ]); //this displays the slider output "ui.values[0]" in input text box "#sizeone"
        $( "#sizetwo" ).val(   ui.values[ 1 ]);
      propertiesEdit();
      }
    });
   $( "#sizeone" ).val(  $( "#slider-size" ).slider( "values", 0 ));//this displays the default values "values: [ 500,1100]" on load -now this is done at the bottom of mapInit()
    $( "#sizetwo" ).val(  $( "#slider-size" ).slider( "values", 1 ));

  } );

          $( function() {
    $( "#slider-activity" ).slider({
      range: true,
      min: 1,
      max: 24,
      values: [ 11, 16 ],
      slide: function( event, ui ) {
        $( "#activityone" ).val(  ui.values[ 0 ] );
        $( "#activitytwo" ).val(   ui.values[ 1 ] );
      activityEdit();
      }
    });
    $( "#activityone" ).val(  $( "#slider-activity" ).slider( "values", 0 ));
    $( "#activitytwo" ).val(  $( "#slider-activity" ).slider( "values", 1 ));

  } );

       function showDetails() {
        document.getElementById("infowindetails").style.display = "block";
        document.getElementById("infowinbutblk").style.display = "none";
       }

        function hideDetails() {
         document.getElementById("infowindetails").style.display = "none";
         document.getElementById("infowinbutblk").style.display = "block";
       }

       function industryEdit() {
       document.getElementById("saveindustry").style.display = "inline-block";
        document.getElementById("editindustry").style.display = "none";
        document.getElementById("industryfilter").style.border = "solid 3px #7dcdcd";
        document.getElementById("industryfilter").style.width = "98%";
    }
    function industrySend() {
       document.getElementById("saveindustry").style.display = "none";
        document.getElementById("editindustry").style.display = "inline-block";
        document.getElementById("industryfilter").style.border = "none";
        document.getElementById("industryfilter").style.width = "100%";
        initMap();
    }
      function propertiesEdit() {
       document.getElementById("saveproperties").style.display = "inline-block";
        document.getElementById("editproperties").style.display = "none";
        document.getElementById("storeproperties").style.border = "solid 3px #7dcdcd";
        document.getElementById("storeproperties").style.width = "98%";
    }
    function propertiesSend() {
       document.getElementById("saveproperties").style.display = "none";
        document.getElementById("editproperties").style.display = "inline-block";
        document.getElementById("storeproperties").style.border = "none";
        document.getElementById("storeproperties").style.width = "100%";
        initMap();
    }
      function activityEdit() {
       document.getElementById("saveactivity").style.display = "inline-block";
        document.getElementById("editactivity").style.display = "none";
        document.getElementById("activity").style.border = "solid 3px #7dcdcd";
        document.getElementById("activity").style.width = "98%";
    }
    function activitySend() {
       document.getElementById("saveactivity").style.display = "none";
        document.getElementById("editactivity").style.display = "inline-block";
        document.getElementById("activity").style.border = "none";
        document.getElementById("activity").style.width = "100%";
    }
    function getSiteList(){
     var availablesite = [
            { address: "520 5th Ave",
              lat : 32.7107619,
              long : -117.1604031,
              rent: "lease",
              price: {list:"Negotiable", low: null , high: null },
              size:  {list:"750 - 2,500 SF" ,low:750 ,high:2500} ,
              type: "Street Retail",
              broker:  {name:"Guy Gabriele" ,contact:"310-489-0027"} ,
              description:"2500 sqft of Retail or Restaurant space in the heart of the Gaslamp Quarter! High ceilings, modern look, and attractive storefront, along with potential patio-space make this property a perfect location for retail, coffee-shop or fast-casual eatery, a full-service restaurant, or for other uses. It is flexibly zoned to permit many uses. Centered amongst premier nightclubs, top notch retail, highly-rated restaurants, and 5 star hotels, this property is on the best block in the Gaslamp Quarter, and in one of San Diego County\'s premier locations. The Gaslamp Quarter is heavily foot trafficked, its\' convention center and many corporate headquarters create business and foot traffic in the morning and afternoon, and the vibrant tourist traffic and nightlife create business and foot traffic in the night. The Gaslamp Quarter is also home to many new and upcoming developments. These developments include San Diego\' s largest Mixed Use development housing the Ritz Carlton, Office Space, Lofts, and Retail, a Grauman\'s Chinese theater, and the Pendry Hotel. See attached flyer for more details and information."
              },
             {
              address: "308 G St",
              lat : 32.7127272,
               long : -117.1617891,
              rent: "lease",
              price: {list: "$36 SF/Year",low: 36, high: 36},
              size:  {list: "800 - 4,300 SF" ,low:800 ,high:4300} ,
              type: "Street Retail",
              broker:  {name:"Daniel Jones" ,contact:"619-471-7054"} ,
              description:"This building is a hotel with office/retail space on the ground floor and second floor.The Golden West Hotel has 50 rooms, built in 1913 and is directly adjacent to the Horton Plaza. Ample parking at Horton Plaza. Located in the Gaslamp quarter in downtown San Diego. It is located on the south side of Horton Plaza in downtown San Diego on G Street, just west of 4th Avenue."
            },
            {
              address:"1980 Kettner Blvd",
              lat : 32.7247484,
              long : -117.1699327,
              rent: "lease",
              price: {list: "$45 SF/Year", low:45, high:45},
              size:  {list: "1,403 SF" ,low:1403 ,high:1403} ,
              type: "Other Retail",
              broker:  {name:"Hans Strom" ,contact:"619-243-1244"} ,
              description:"Premier retail space located on the corner of Kettner Blvd and Fir St., in Broadstone, Little Italy. 14 ft ceilings, building parking. Current salon build-out, with 9 fully fixturized work stations. Space has ADA restroom and large break room. Located in the hottest neighborhood in San Diego, join the mix of tenants which include: Cafe Graditude, Bar Bodega, Adelman Fine Art, and Little Apple Boutique. Neighbors in the area include Underbelly, Bencotto, Monello, Influx, Prep Kitchen, Juniper & Ivy, the Crack Shack and many more."
            },
            {
              address: "460 16th Street",
              lat : 32.7100798,
              long : -117.1498009,
              rent: "lease",
              price: {list:"$30 SF/Year", low: 30 , high: 30 },
              size:  {list: "744 - 8,203 SF" ,low:744 ,high:8203} ,
              type: "Street Retail",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description:"Anchored in the block bounded by 15th, 16th, J Street, and Island, the retail is positioned to benefit from the desirable J Street corridor of trendy restaurants, charming cafes, and thriving businesses in the immediate trade area. PROPERTY DETAILS: Large retail floorplate to create synergy & flexible retail, restaurant and lease spaces. Attractive storefronts & aesthetics. Expansive window lines. Flexible for office uses. Located in East Village, the fastest growing neighborhood downtown"
            },
            {
              address: "1350 6th Ave",
               lat : 32.7194114,
               long : -117.1595539,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "967 - 1,989 SF" ,low:967 ,high:1989} ,
              type: "Street Retail",
              broker:  {name:"Jim Rinehart" ,contact:"858-523-2092"} ,
              description:"+/- 967-3,392 SF AVAILABLE Street level space. Move-in ready. Over $1.2 M in building renovations. Abundant parking available. Easy access to freeways : I-5 & Hwy 163 & Hwy 94 High traffic trade area ( Sixth Ave. one-way: 15,981 cars per day ) Captive audience: 3,000 cars per week at on-site parking garage. 15,000+ employees in a 5-block radius. Located in Downtown San Diego\'s Civic Core. Walking distance to Little Italy & The Gaslamp."
            },
            {
              address: "1501 India St",
              lat : 32.7212688,
               long : -117.1678509,
              rent: "lease",
              price: {list: "$83.33 SF/Year", low:83.33 , high: 83.33},
              size:  {list: "720 SF" ,low:720 ,high:720} ,
              type: "Nbrhood Ctr",
              broker:  {name:"Marina Rossi" ,contact:"949-310-2223"} ,
              description:"Prime Little Italy retail space. Located adjacent corner plaza with seating and fountain. Perfect for a cafe or sandwich shop (light food usage only). Currently a clothing store, will be vacated by Sep 2017. Space also includes one parking space in garage."
            },
            {
              address: "861 5th Ave",
              lat : 32.7144583,
               long : -117.159831,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "8,500 SF" ,low:8500 ,high:8500} ,
              type: "Free Std Bldg",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description:"861 Fifth Avenue represents a RARE opportunity to lease a freestanding retail building in the heart of the Gaslamp Quarter. Fantastic central location, located at SEC corner of 5th Avenue and E Street. Building has public parking that wraps around the site and is steps to Horton Plaza with over 3,000 public spaces. With unique exposed brick and a glass-wrapped exterior and over 160 feet of frontage on Fifth & E, this building is a great opportunity in the prominent Gaslamp Corner. Downtown\'s Historic Gaslamp Quarter is San Diego\'s number one tourist destination. More than 10 million visitors are drawn each year to experience the vibrant and unique atmosphere. Great freeway ingress/egress to Hwy 94, I-d and Hwy 163 and 16,500 cars pass the site daily (one way)."
            },
            {
              address: "550 15th St",
              lat : 32.7112564,
              long : -117.1506519,
              rent: "lease",
              price: {list:"$27.27 SF/Year", low:27.27, high:27.27},
              size:  {list: "1,100 SF" ,low:1100 ,high:1100} ,
              type: "Other Retail",
              broker:  {name:"Hans Strom" ,contact:"619-243-1244"} ,
              description:"Unique East Village Loft, zoned as Live/Work. Great street level retail space, best suited for owner/user. Full kitchen with modern, stainless steel appliances. Dedicated parking space, in secure garage. Restroom equipped with modern upgrades, including washer & dryer. Beautiful hardwood flooring throughout. Large outdoor private patio area. Ample closet and storage space inside. Located 1/2 block off Market Street with close proximity to cafes, restaurants, Petco Park, I-5 freeway and more."
               },
              {
              address: "939 5th Ave",
              lat : 32.7152481,
               long : -117.1600485,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "2,500 - 10,000 SF" ,low:2500 ,high:10000} ,
              type: "Free Std Bldg",
              broker:  {name:"Russ Merrill" ,contact:"619-276-1997"} ,
              description: "Available is a beautiful historical building in downtown San Diego in the heart of the Gaslamp district. Available is 4 - 2,500 SF floors or a total of 10,000 SF. Excellent Marquee Signage High Pedestrian traffic. Exposed ceilings on all floors with approx. 20 ft. ceiling height on the first floor. Excellent Restaurant Location. Close to central business district,hotels and shops. In the heart of the Gaslamp District in Downtown San Diego and is close to hotels, restaurants and the Horton Plaza and many office buildings."
               },
                {
              address: "542 5th Avenue",
              lat : 32.7109922,
              long : -117.1603856,
              rent: "lease",
              price: {list: "$95.40 SF/Year", low: 95.4, high:95.4},
              size:  {list: "1,350 SF" ,low:1350 ,high:1350} ,
              type: "Street Retail",
              broker:  {name:"Russ Merrill" ,contact:"619-276-1997"} ,
              description: "Fantastic frontage & signage on one of Gaslamp\'s best blocks. More than 10 million visitors each year to experience the vibrant and unique atmosphere. Fifth Avenue Gaslamp is one of the most sought after entertainment & retail corridors in Southern California. Steps to San Diego Convention Center and PETCO Park, home of the San Diego Padres. Adjacent to Hard Rock Hotel, new Pendry Hotel, Hiltop Gaslamp and many more."
               },
                {
              address: "520 Fifth Ave",
              lat : 32.7108001,
              long : -117.1602364,
              rent: "lease",
              price: {list: "$95.40 SF/Year", low: 95.4, high:95.4},
              size:  {list: "2,500 SF" ,low:2500 ,high:2500} ,
              type: "Street Retail",
              broker:  {name:"David Maxwell" ,contact:"858-677-5343"} ,
              description: "Fantastic frontage & signage on one of Gaslamp\'s best blocks. More than 10 million visitors each year to experience the vibrant and unique atmosphere. Fifth Avenue Gaslamp is one of the most sought after entertainment & retail corridors in Southern California. Steps to San Diego Convention Center and PETCO Park, home of the San Diego Padres. Adjacent to Hard Rock Hotel, new Pendry Hotel and Hilton Gaslamp and many more."
               },
                {
              address: "1485 E Street",
              lat : 32.7146069,
              long : -117.1505228,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "3,900 - 7,600 SF" ,low:3900 ,high:7600} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: " Breaking Ground 2019 / Delivery 2021- 701,800 SF creative office building. 264 residential units. 726 parking stalls- 26,900 SF of retail/restaurant space at the base for lease. Block C - Makers Quarter"
               },
               {
              address: "1023 4th Avenue",
              lat : 32.71595380000001,
               long : -117.1607874,
              rent: "lease",
              price: {list:"$27 SF/Year", low:27, high:27},
              size:  {list: "4,676 - 24,629 SF" ,low:4676 ,high:24629} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: " An unmatched corner restaurant lease opportunity in the San Diego market with 100 feet of street exposure on Broadway and nearly 100 feet on 4th Ave. Past sales records at this high profile corner restaurant have exceeded $12 Million annually. Draw from the 11 Million square feet of office tenants downtown for lunch and happy hour. Tap into the Gaslamp energy after 7pm with 10 Million visitors annually. Renderings are conceptual. Can be combined with 474 Broadway to achieve almost 15,000sf of Prime ground floor space with @10,000sf of lower level space. IMPROVEMENTS IN PLACE. 2AM Full Liquor License available to new tenant with Entertainment and offsite Beer sales. High volume, spacious kitchen with equipment available Oversized keg room / cold box. Elevator served. Capacity: 754.Potential for Sidewalk Seating on 4th. Paid parking lot within the complex. Private Event space fully finished with separate bar, capacity @ 174 in the basement. Potential for large blade sign on corner. For more information please visit www.nextwavecommercial.com"
              },
                {
              address: "1220 Third Ave",
              lat: 32.7182444,
              long : -117.162924,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "3,300 SF" ,low:3300 ,high:3300} ,
              type: "Street Retail",
              broker:  {name:"Chris Roberson" ,contact:"619-559-7002"} ,
              description: "Civic Center Plaza space now available! Space was previously home to the restaurant known as Downtown Johnny Brown\'s. Space is currently a 3,300 sqft shell with a 1,700 sqft courtyard that will need to be re-built out and paid for by the tenant- come design your very own dream restaurant/brewery/pub! Located in Downtown San Diego\'s Civic Center Plaza! This space is close to Little Italy, Horton Plaza Mall, and multiple City of San Diego employment centers."
                },
                 {
              address: "729 4th Ave",
             lat : 32.71305160000001,
              long : -117.1608509,
              rent: "lease",
              price: {list: "$26.35 SF/Year", low:26.35, high:26.35},
              size:  {list: "4,500 SF" ,low:4500 ,high:4500} ,
              type: "Street Retail",
              broker:  {name:"Surinder Singh" ,contact:"858-610-2011"} ,
              description: "Currently running and operating restaurant. Please do not disturb or talk to employees. Please call for more details.TYPE 47 FULL LIQUOR LICENSE and TYPE 58 Catering License . Equipment, furniture and license available for purchase for $250k. San Diego Gas lamp district"
                },
                 {
              address: "1566 Fifth Ave",
              lat : 32.721794,
              long : -117.160499,
              rent: "lease",
              price: {list:"$6-$21 SF/Year", low:6, high:21},
              size:  {list: "3,500 - 4,000 SF" ,low:3500 ,high:4000} ,
              type: "Street Retail",
              broker:  {name:"Serena Patterson" ,contact:"858-677-5307"} ,
              description: "± 4,000 SF Ground floor + ± 3,500 SF Basement ± 3,500 SF second floor creative office Traffic Counts:Fifth Avenue (one way): 15,981, Cedar Street (one way): 16,118"
                },
                 {
              address: "527-545 F St",
              lat : 32.7134353,
              long : -117.1594149,
              rent: "lease",
              price: {list:"$25.80-$45 SF/Year", low:25.80, high:45},
              size:  {list: "175 - 8,995 SF" ,low:175 ,high:8995} ,
              type: "Office/Retail",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description: "527-545 F Street represents a rare opportunity to lease a hard corner Gaslamp restaurant space with patio and/or modern office space. Fantastic central location, located at SW corner of 6th Avenue and F Street. The well-known freestanding building of George Hill at 6th & F Street is located near the Central Business District, Horton Plaza Mall, Balboa Theatre, Petco Park, booming East Village and many other Gaslamp entertainment destinations. "
                },
                {
              address: "705 Sixth Ave",
              lat: 32.7128143,
              long : -117.1589423,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "6,340 - 7,840 SF" ,low:6340 ,high:7840} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Nine-story, ±72,500 SF creative office building on the corner of 6th & G. Located in the heart of Downtown San Diego and ideally positioned at the connection of the Gaslamp Quarter with the booming East Village. Area tenants include Breakfast Republic, STK, Coin-Op, TCL Chinese Theater, Sugar Factory, Fogo de Chão, Neighborhood, Searsucker & Urban Outfitters. Close proximity to Petco Park, the San Diego Convention Center and Westfield Horton Plaza. Easy access to the 5, 94 & 163 freeways."
               },
                {
              address: "104 Broadway",
              lat : 32.7158964,
              long : -117.1634867,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "4,486 SF" ,low:4486 ,high:4486} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Redevelopment of the 25 story, 325,000 SF Executive Complex. Public onsite parking garage. Repositioned as one of San Diego\' s most exclusive Class A Office towers. Adjacent to Spreckels Theatre, Sofia Hotel, Chipotle, Tender Greens and Corner Bakery. Located on the Corner of 1st Street and Broadway in Downtown San Diego, CA"
               },
               {
              address: "900 Bayfront Court",
              lat : 32.7170428,
              long : -117.171821,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,307 - 4,122 SF" ,low:1307 ,high:4122} ,
              type: "Retail (Other)",
              broker:  {name:"Matt Moser" ,contact:"858-523-2096"} ,
              description: "Draw from four distinct target groups: tourists, residents, convention attendees and daytime business traffic. Within a 1-Mile Radius of San Diegos highest grossing Restaurants; The Fish Market, Ruth\'s Chris Steak House and Island Prime. Level 1 Approximately 1,379-8,255 SF of Retail Space. Two signed deals: Beach Hut Deli & Ryan Bros Coffee. Floor to Ceiling glass windows facing the bay. 16\' Ceiling Heights. Level 2 Approximately 3,815-7,937 SF of Retail Space. Includes 3,741 SF Terrace. Floor to Ceiling glass windows facing the bay. 16\' Ceiling Heights. Ocean View. Level 4 Approximately 4,240 SF of Retail Space. 4,182 SF Water Front Terrace. Floor to Ceiling glass windows facing the bay. Downtown San Diego, CA"
               },
                 {
              address: "835 5th Ave",
              lat :  32.7142084,
              long : -117.1598754,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "2,000 - 4,500 SF" ,low:2000 ,high:4500} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: ""
               },
               {
              address: "1453 4th Avenue",
              lat : 32.720609,
              long : -117.160827,
              rent: "lease",
              price: {list: "$32.21 SF/Year", low:32.21, high:32.21},
              size:  {list: "745 - 996 SF" ,low:745 ,high:996} ,
              type: "Street Retail",
              broker:  {name:"David Maxwell" ,contact:"858-677-5343"} ,
              description: "Newly Constructed Building. Location: Urban Colombia-Core / Downtown. Size: 745 SF + 251 SF PATIO. Accessibility: FWY I-5 / HWY 163. High Traffic Area w/ Sixth Avenue (one way): 15,981 Traffic Count ADT A Street (one way): 18,995 Traffic Count. Core Businesses and Residential Population. Surrounding communities include; Little Italy, Gaslamp, City Walk and the Smart Corner"
               },
               {
              address: "753 15th Street",
              lat : 32.7135129,
              long: -117.1499801,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "4,792 - 15,895 SF" ,low:4792 ,high:15895} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Breaking ground Fall 2017 / Delivery Early 2019. 293 Units / 28 stories. Luxury apartment tower. 15,895 SF retail box with 4,792 SF of additional retail/restaurant space facing park. ±6,000 SF pocket park. MAKERS QUARTER is a 5 1/2 city block redevelopment project in the Upper East Village of Downtown San Diego. Upon completion, the project will deliver over 800 new residential units, almost 1,000,000 SF of office space, and over 175,000 SF of retail and restaurant space."
               },
               {
              address: "1514 F Street",
              lat : 32.713968,
              long : -117.150093,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "3,190 - 5,040 SF" ,low:3190 ,high:5040} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Under Construction / Delivery Q1 2018 ~42,000 SF of creative office. 8,230 SF of retail/restaurant. MAKERS QUARTER is a 5 1/2 city block redevelopment project in the Upper East Village of Downtown San Diego. Upon completion, the project will deliver over 800 new residential units, almost 1,000,000 SF of office space, and over 175,000 SF of retail and restaurant space."
               },
                 {
              address: "1531 Broadway",
              lat : 32.715365,
              long : -117.14978,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,500 - 31,000 SF" ,low:1500 ,high:31000} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Historic warehouse base. Ground floor grocery / retail box available. Second floor theatre / restaurant space available. Additional Broadway retail space available. ±187,000 SF office tower above south half. 247 unit residential tower above north half. 836 parking stalls. MAKERS QUARTER is a 5 1/2 city block redevelopment project in the Upper East Village of Downtown San Diego. Upon completion, the project will deliver over 800 new residential units, almost 1,000,000 SF of office space, and over 175,000 SF of retail and restaurant space."
               },
                {
              address: "1220 J St",
              lat : 32.709601,
              long : -117.15344,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "5,900 SF" ,low:5900 ,high:5900} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "5,900 SF existing building. Approximately 900 SF of mezzanine. Conducive for roll-ups or accordion windows. Built in 1945. In San Diego\' s hottest neighborhood, East Village. Situated on the corner of J Street and Park Blvd. Walking distance to the new San Diego Central Library and Petco Park. Approximately two blocks from the proposed NFL/Convention Center"
               },
               {
              address: "1330 Market St",
              lat : 32.7117906,
              long : -117.1524204,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "5,469 SF" ,low:5469 ,high:5469} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Urban street level restaurant. Close proximity to Petco Park, Thomas Jefferson School of Law, San Diego Convention Center, San Diego Central Library, and freeway access to 5, 94, and 163. 1330 Market Street, San Diego, CA 92101"
               },
               {
              address: "474 Broadway",
              lat : 32.71595660000001,
              long : -117.1604461,
              rent: "lease",
              price: {list:"$27 SF/Year", low:27, high:27},
              size:  {list: "4,676 - 5,574 SF" ,low:4676 ,high:5574} ,
              type: "Other Retail",
              broker:  {name:"Nate Benedetto" ,contact:"619-326-4400"} ,
              description: "An excellent, second generation restaurant lease opportunity (Formerly Ra Sushi) with high visibility signage viewable from two of the most high profile intersections in the downtown market. Existing kitchen & utility infrastructure should provide significant cost savings for a new tenant. Paid parking garage within the complex. Draw from the 11 Million square feet of office tenants downtown for lunch and happy hour. Tap into the Gaslamp energy after 7pm with 10 Million visitors annually. Few locations in San Diego can match this opportunity for both lunch and dinner traffic."
               },
               {
              address: "1470 Kettner Blvd",
              lat : 32.7205599,
              long : -117.1694463,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "5,193 - 10,416 SF" ,low:5193 ,high:10416} ,
              type: "Street Retail",
              broker:  {name:"Jim Rinehart" ,contact:"858-523-2092"} ,
              description: "Located in the New, Luxury , High Rise Apartment Building, Ariel Suites. Approximately 10,416 SF retail space. Patio Balcony on second floor. An internal Elevator and Stairwells exist to connect from street level. Generous Tenant Improvement Allowance Available. Ample power to the space: 800amp 480v service. 2 inch water services Approximately 2.5 million BTU gas service.JOIN THE LITTLE ITALY RETAIL RENAISSANCE. 48 Square Blocks: The Nations Largest Little Italy. Forbes Top 10 Millennial Neighborhoods. Over 1,200 Re6idential Units in Development. Celebrated as San Diego\'s\' Restaurant Hot Spot"
               } ,
               {
              address: "440 J Street",
              lat : 32.709545,
              long : -117.1606566,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "5,690 SF" ,low:5690 ,high:5690} ,
              type: "Restaurant",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Fully fixturized restaurant opportunity. Recently built out kitchen and dinning area. Entrance on J St. between 5th and 4th Ave. Public parking available on site. 57,000 SF of retail/restaurant space in the historic Gaslamp Quarter. At base of a 220-unit condominium development. Ceiling heights from 15-22 feet high. 2 blocks from San Diego convention center with over 800,000 attendees. 2 blocks to the 42,000 seat Petco Park with over 2.4 Million attendees last year. Adjacent to the newly completed 317 room Pendry Hotel by Montage. Co-tenants include Oceanaire Seafood, Bice Restorante, Bank of America, Sketchers, Oakley and Reebok. Area tenants include Water Grill, Morton\' s Steakhouse, Nobu, Donovan\' s, Flemmings, Lou & Mickeys, Fluxx, Omnia, Oxford Social Club, Lionfish and many more. 440 J STREET, SAN DIEGO, CA 92101"
              },
                {
              address: "215 West Market Street",
              lat : 32.711185,
              long : -117.1649687,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,684 SF" ,low:1684 ,high:1684} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Rare opportunity to purchase or lease ground floor commercial space with on-site parking in Downtown, San Diego. At the base of Pinnacle Museum Tower with 179 luxury condo units. Available for immediate occupancy. Co-tenants include Richard Walker\' s Pancake House, Rate Rabbit & Alexander Salazar Fine Art. Adjacent to the New Children\' s Museum with almost 200,000 visitors annually. Located in Downtown San Diego and the affluent Marina District. Surrounded by nearly 4,000 high profile urban homes. Two blocks from the Waterfront and Ralph\' s grocery, 3 blocks from the Convention Center and Horton Plaza Mall and only one block from a Trolley Station. 3 blocks from Seaport Village and The Headquarters with tenants Seasons 52, Cheesecake Factory, Eddie V\' s, and Puesto. Other area tenants include Richard Walker\' s Pancake House, The Lion\' s Share, The New Children\' s Museum, LION Coffee, Nutrition Zone, Mak Cleaners, Postal Annex, Kansas City Barbeque and Morton\' s Steakhouse. Walking distance to the Gaslamp District, East Village and Little Italy"
               },
               {
              address: "1455 Market St",
              lat : 32.711391,
              long : -117.1508213,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,500 - 7,000 SF" ,low:1500 ,high:7000} ,
              type: "Street Retail",
              broker:  {name:"Branden Key" ,contact:"858-717-7808"} ,
              description: "Spacious building available for a Suitable tenant. 21,000 sq ft total available.Located conveniently off of a ton of foot traffic and curbside parking!!"
               },
               {
               address: "701 16th St",
              lat : 32.7129343,
              long : -117.1492376,
              rent: "lease",
              price: {list:"$27 SF/Year", low:27, high:27},
              size:  {list: "2,036 SF" ,low:2036 ,high:2036} ,
              type: "Street Retail",
              broker:  {name:"Peter Wright" ,contact:"619-243-8450"} ,
              description: "The historic Snowflake Bakery building has one suite becoming available.. and its the best ground floor suite in the property. Currently a lacrosse retail store, this space would work great for a retail or creative office user. Incredible natural light, exposed ceilings, 12 foot ceilings and high street exposure. Parking is available as well. Low NNNS ($.17 psf). East Village, IDEA district, downtown, easy freeway access to 94 & I5, minutes to the airport"
             },
               {
               address: "520 W. Ash Street",
              lat : 32.72002,
              long : -117.1676624,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "2,500 - 4,344 SF" ,low:2500 ,high:4344} ,
              type: "Street Retail",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description: "LUMA, in one of San Diego county\' s most desirable neighborhoods the booming Little Italy neighborhood of downtown San Diego. The LUMA retail will accompany 220 residential units in the latest Residences by LMC, an innovator in multifamily residential living, focusing on neighborhood, culture and community. Located at the northwest corner of West Ash St. & Columbia St., the retail is perfectly positioned to benefit from the thriving Little Italy neighborhood of trendy restaurants, charming cafes and boutiques, along with its proximity to the Core Business District."
             },
                {
               address: "233 A St",
              lat : 32.7186273,
              long : -117.1622946,
              rent: "lease",
              price: {list:"$17.40-$31.80 SF/Year", low:17.40, high:31.80},
              size:  {list: "845 - 11,371 SF" ,low:845 ,high:11371} ,
              type: "Office Bldg",
              broker:  {name:"Teresa Stein" ,contact:"619-233-3852"} ,
              description: "The Building is a fourteen-story high-rise commercial building. Ocean views are available on upper floors. Once the tallest building in the Downtown area, the Centre City Building was designed by award winning architect, Frank W. Stevenson. Built in 1927 with an eye-catching gabled rooftop and uniquely styled rosette windows, the Centre City Building is a distinctive edifice on the San Diego skyline and is designated by the City of San Diego as an historical landmark. The Centre City Building is conveniently located in the core of Downtown San Diego between 2nd and 3rd Avenue on A Street at the heart of the city\'s transportation system. The Downtown area serves as the government and corporate hub for the region, housing the Civic Center, City Hall, County Courthouse, and Small Business Administration. The area is replete with financial institutions, retail shopping opportunities, restaurants, hotels and entertainment centers."
             },
               {
               address: "1050 Columbia St",
              lat : 32.7164429,
              long : -117.1675675,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,460 - 2,535 SF" ,low:1460 ,high:2535} ,
              type: "Retail Pad",
              broker:  {name:"Matthew Peckham" ,contact:"858-875-4671"} ,
              description: " ± 1,460 sq. ft. ground floor space available immediately - Potential to add ± 1,075 sq. ft. of additional space for total of +/- 2,535 sq. ft. (see floor plan on next page) - Located on C Street between India and Columbia Streets - Situated on the ground floor of a 600-stall parking structure - Located in the heart of Columbia District just outside the Core Business District - Frontage on the trolley line and one block - Easy access to the 5, 163 and 94 Freeways, Amtrak and Coaster"
             },
              {
               address: "1643 6th Ave",
              lat : 32.7225308,
              long : -117.1590267,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "936 SF" ,low:936 ,high:936} ,
              type: "Street Retail",
              broker:  {name:"James Pieri, Jr." ,contact:"619-972-2214"} ,
              description: "AVAILABLE end of July, 2017. The Mills at Cortez Hill is a full block residential development located at a strategic entry into Central Business District of Downtown San Diego and Uptown. Cortez Hill has houses 2,297 residential units in 18 buildings and is home to nine hotels offering a total 1,093 rooms. Mills is one a few buildings within the neighborhood to offer ground floor retail. With limited retail in the surrounding area, the property is perfectly situated to serve not only downtown residents, but uptown Bankers Hill to the north. Cortez Hill represents one of the first neighborhoods in San Diego to have combined commercial and residential living, emerging in the 1920s as the center of fashionable entertainment. Views include Balboa Park, San Diego Bay, the Pacific Ocean and the urban scene below. The hill\' s topography separates it from downtown\' s hustle, yet its closeness makes it a very desirable address for those who want to live on the cusp of an active urban center."
             },
               {
               address: "911 6th Avenue",
              lat : 32.7148555,
              long : -117.1589629,
              rent: "lease",
              price: {list:"$33.96 SF/Year", low:33.96, high:33.96},
              size:  {list: "530 SF" ,low:530 ,high:530} ,
              type: "Street Retail",
              broker:  {name:"Rick Marcus, CCIM" ,contact:"760-747-8899"} ,
              description: "Rent: $1,500.00/month.530 square feet. Historic retail/residential storefront. Nearby bus and trolley services. 99 walk score with excellent transit, walkability and bikeability options. Nearby shopping, restaurants, cafes. Located in the heart of Downtown San Diego just two blocks to Horton Plaza on 6th Avenue off Broadway."
             },
               {
               address: "2305-2317 India St",
              lat : 32.7286765,
              long : -117.1701472,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "3,000 - 8,000 SF" ,low:3000 ,high:8000} ,
              type: "Street Retail",
              broker:  {name:"Douglas W. Hamm" ,contact:"952-240-2602"} ,
              description: "Rare single story free standing corner building w/high ceilings. 100 feet of street frontage along coveted India St. Prime North Little Italy Corner Location. One Block From Ballast Point Brewing Co., Crack Shack, Juniper and Ivy, Herb and Wood, Birdrock Coffee and adjacent to James Coffee."
             },
              {
               address: "460-480 5th Ave",
              lat : 32.7100826,
              long : -117.1603021,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "800 - 7,983 SF" ,low:800 ,high:7983} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "PRIME RETAIL AND RESTAURANT OPPORTUNITY IN THE HEART OF THE GASLAMP *Do not disturb current tenant. - 57,000 SF of street retail space in project-At the base of a 220 unit condominium development -Ideally located 2 blocks from convention center and 3 blocks from Petco Park -10 Million Visitors Annually to Gaslamp Quarter-11,786 Hotel Rooms Downtown -2.4 Million Petco Park Annual Attendees -172 Events Held at the Convention Center -Great retail synergies with 5th Ave. Gaslamp tenants including Urban Outfitters, Sketchers, Reebok, Oakley, Quicksilver, MAC Cosmetics & Lucky Brand Jeans"
             },
             {
               address: "964 5th Ave",
              lat : 32.7154254,
              long : -117.1604507,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "9,500 SF" ,low:9500 ,high:9500} ,
              type: "Other Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "-Up to 9,500 SF Divisible / Up to 15,000 SF of basement - Historical Granger Building- Main and Main corner location at the intersection of 5th and Broadway-Ideal Flagship location"
             },
               {
               address: "2155 Kettner Blvd",
              lat : 32.72681439999999,
              long : -117.1705803,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "873 - 1,662 SF" ,low:873 ,high:1662} ,
              type: "Other Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Ground Floor Retail & Restaurant Space on Kettner Blvd. 7,938 SF At the base of new six-story, 130 unit, luxury apartment project. Estimated completion - Summer 2017"
             },
              {
               address: "1680 India St",
              lat : 32.7228915,
              long : -117.168499,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "3,000 SF" ,low:3000 ,high:3000} ,
              type: "Other Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Ground Floor Corner Retail Space. 3,000 SF + Patio. Located at the Main & Main intersection of Little Italy @ India & Date Streets. Directly across the street from the new Piazza Famiglia opening Summer of 17"
             },
             {
               address: "337-343 13th St",
              lat : 32.7089845,
              long : -117.15255,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "6,435 SF" ,low:6435 ,high:6435} ,
              type: "Restaurant",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "6,435 SF Corner Restaurant Opportunity in the East Village - 2,435 SF Fully Restored Historic Mexican Presbyterian Church -1,235 SF Ground Floor -1,200 SF Basement with Elevator - ~4,000 SF of Exclusive Patio - Delivered Restaurant Ready with a Warm Restaurant Shell - Adjacent to the 19 story Alexan San Diego residential project, featuring 313 luxury apartments -Situated in the up and coming East Village neighborhood and adjacent to Alexan San Diego with 313 luxury apartments and 19 stories - Located on the corner of J Street and 13th Street - Walking distance to the new San Diego Central Library and Petco Park - Approximately one block from the proposed NFL/Convention Center"
             },
              {
              address: "888 W Ash St",
              lat : 32.7200207,
              long : -117.1708095,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,000 - 3,600 SF" ,low:1000 ,high:3600} ,
              type: "Street Retail",
              broker:  {name:"Chris Roberson" ,contact:"619-559-7002"} ,
              description: "*3000 sqft of Ground Floor *600 sqft of Mezzanine *Current space can be divided into 2 separate spaces *Do Not Disturb Tenants. Call Broker for showings. Located on Pacific Highway, this one of a kind space overlooks the Waterfront Park and has an amazing view of the bay."
             },
             {
              address: "367 15th Street",
              lat : 32.7092391,
              long : -117.1500831,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "5,000 SF" ,low:5000 ,high:5000} ,
              type: "Other Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "5,000 SF Freestanding Building and potential for large patio located at the corner of 15th and J. Exceptional East Village Location. Ideal retail/restaurant space. 1 block to Fault Line Park at 14th Street and Island Avenue. 2 blocks to the proposed Charger Stadium Site. Exposure to over 2,500 people across the street. Convenient access to I-5, I-94, & I-163 Freeways. Over 3,300 existing residential units, 2,000 residential units under construction and 2,000 hotel rooms within 6 blocks. 2 public parking structures and multiple surface lots within 8 blocks. Thomas Jefferson School of Law now open (1,000 students and faculty) Close proximity to public transportation, Petco Park, San Diego\' s Main Public Library, San Diego City College Expansion and Makers Quarter development Area Tenants include Bottega Americano, The Mission, Stella Public House, Halcyon Coffee Bar Lounge, Basic, Sol Cal Cafe, Half Door Brewing Co., Bub\' s, and Tiltled Kilt"
             },
              {
              address: "615 8th Ave",
              lat : 32.7118187,
              long : -117.157165,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "2,900 SF" ,low:2900 ,high:2900} ,
              type: "Other Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: " 2,900 SF basement space. Located underneath Bootlegger and Lucky D\' s Hostel. Situated at the corner of 8th Avenue and Market Street in San Diego\' s East Village neighborhood. Across the street from a new development, which will include a Whole Foods, Ritz Carlton and approximately 15,000SF of class A office space"
             },
              {
              address: "1953 India St",
              lat : 32.7251877,
              long : -117.1686075,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "7,500 SF" ,low:7500 ,high:7500} ,
              type: "Restaurant",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description: "Turn-Key Operation, immediately ready for new operator. Type 47 Full Liquor License Available. Over $2M invested in build-out. Below Market lease rate. Prime India Street location, in the heart of booming Little Italy, one of the most desirable restaurant areas in all of San Diego County."
             },
              {
              address: "202 Park Boulevard",
              lat : 32.7076593,
              long : -117.1549195,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,415 - 10,399 SF" ,low:1415 ,high:10399} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "An abundance of restaurant and retail space (approximately 55,000 SF)positioned below 713 residential units, and highlighted by a 12,000 SF open air plaza leading directly into Petco Park -Encompassing 3.5-acres, the property is bound by Park Blvd, Imperial Avenue and 12th Street. Located directly adjacent to Petco Park at the convergence of the Gaslamp District, Marina and East Village neighborhoods, this well positioned project is easily accessible by walking, driving, biking or public transportation."
             },
              {
              address: "1450 Market Street",
              lat : 32.7121206,
              long : -117.150719,
              rent: "lease",
              price: {list:"$25.80 SF/Year", low:25.80, high:25.80},
              size:  {list: "950 - 4,400 SF" ,low:950 ,high:4400} ,
              type: "Street Retail",
              broker:  {name:"ECP Commercial" ,contact:"619-442-9200"} ,
              description: "Take a Video Tour Today! https://youtu.be/9MN6CQK3YEE - 950-4,400 SF of retail space available for Lease - Prime East Village Retail Location, situated on the ground floor of a 241-unit residential project constructed in 2014 - Available space includes a highly-visible corner restaurant opportunity with 18-foot ceilings - Multiple demising options available - flexible sizes - Positioned in the heart of numerous completed, underway and planned East Village development projects - Office use permitted - Call for details - Lease Rate: $2.15/SF NNN - Development Highlights - Immediate Vicinity - East Village Green 4.1 Acre Public Amenity Site Directly Across the Street from FORM 15 - Makers Quarter Employment Hub 6 block development 1 Million SF of Office, 800 Residential Units, and 175,000 SF of Retail - Numerous other planned or in-progress projects, bringing new residential, office, hotel and retail product to the exciting East Village Neighborhood"
             },
             {
              address: "805 W Cedar Street",
              lat : 32.72178170000001,
              long : -117.17046,
              rent: "lease",
              price: {list:"$27-$36 SF/Year", low:27, high:36},
              size:  {list: "1,800 - 11,200 SF" ,low:1800 ,high:11200} ,
              type: "Free Std Bldg",
              broker:  {name:"Ben Tashakorian" ,contact:"858-373-3176"} ,
              description: "Marcus & Millichap is pleased to present for lease this 11,200 square foot retail offering in Little Italy. This two-story building is currently divided into two suites, but the current owner is looking for a visionary long-term tenant and is amenable to transforming the space into a long-term business venue in one of San Diego\' s most highly walkable, and transit-accessible locations. Also for lease is a 6,200 square foot industrial warehouse in the downtown industrial submarket of San Diego. This open floorplan allows for imaginative buildouts to facilitate a restaurant, brewery, or wine bar in a growing hotspot of Little Italy. This is the perfect opportunity for an entrepreneur to bring a new social gathering concept to one of San Diego\' s most highly walkable, and transit-accessible locations. These properties are located by Little Italy\'s trolley station, adjacent to a new San Diego Fire Station, three blocks from the San Diego Harbor as well as several new and planned condominium developments."
             },
              {
              address: "200 Harbor Drive",
              lat : 32.7089166,
              long : -117.1625216,
              rent: "lease",
              price: {list:"$27-$35.40 SF/Year", low:27, high:35.40},
              size:  {list: "835 - 2,535 SF" ,low:835 ,high:2535} ,
              type: "Street Retail",
              broker:  {name:"David Maxwell" ,contact:"858-677-5343"} ,
              description: "Ground floor retail in one of Downtown San Diego\'s premier luxury residential towers. Tenants in the building include Morton\'s the Steakhouse. Directly across from the 282-room Hilton Gaslamp Hotel, Steps to 5th Avenue, and Padres\' PETCO Park. Across the street from San Diego Convention Center, Grand Hyatt Hotel, Marriott and Seaport Village."
             },
             {
              address: "376 5th Avenue",
              lat : 32.7092363,
              long : -117.1602212,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "5,000 - 15,000 SF" ,low:5000 ,high:15000} ,
              type: "Free Std Bldg",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "3 story retail building -15,000 SF (5,000 SF x 3 levels)-Main & Main corner location @ 5th Ave & J St -Unparalleled visibility -Historic building -Ideal flagship retail location -Seeking a multi-level retailer or restaurant. Located in the heart of San Diego\' s historic Gaslamp Quarter with over 10 million visitors per year. Area Tenants include Urban Outfitters, Water Grill, Nobu, Morton\' s, Donovan\' s, Flemings, Lou & Mickey\' s, Omnia, Mary Jane\' s, Old Spaghetti Factory, Café Sevilla, Union -Kitchen & Tap, Fluxx, Tin Fish, and many more iconic Gaslamp tenants. -Prominent corner location at 5th Avenue and J Street -Situated 2 blocks from the historic Gaslamp sign and across the street from the Pendry Hotel by Montage (317 rooms)-Over 22,000 residential units and 2,000 hotel rooms within 4 blocks -8 public parking structures and multiple surface lots within 3 blocks -2 blocks to the 300,000 SF Sempra Energy Headquarters -2 blocks to the San Diego Convention Center with over 800,000 convention attendees in 2014 -2 blocks to 42,000 seat PETCO Park with over 2.4 Million attendees last year -3 blocks to Westfield Horton Plaza with an estimated 8.5 million visitors per year"
             },
               {
              address: "425 Market",
              lat : 32.71133,
              long : -117.1604829,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "700 - 2,521 SF" ,low:700 ,high:2521} ,
              type: "Street Retail",
              broker:  {name:"David Maxwell" ,contact:"858-677-5343"} ,
              description: "Highly desirable retail space south of Market, totaling more than 4,500 SF. Previous food use improvements and enclosed patio area. Fantastic frontage and ceiling heights"
             },
             {
              address: "403 13th St",
              lat : 32.7095908,
              long : -117.152535,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "2,000 - 6,000 SF" ,low:2000 ,high:6000} ,
              type: "Restaurant",
              broker:  {name:"Douglas W. Hamm" ,contact:"952-240-2602"} ,
              description: "One of the last true creative spaces in Downtown San Diego. 25 foot ceiling heights, sliding doors, brick walls and private loading/unloading access via rear driveway. Prime East Village across from Mission Cafe. 1 block from Pinnacle Park and the new Library. Across the street from the Trammel Crow Alexan project and Library tower luxury high rise (both breaking ground shortly)."
             },
             {
              address: "812 12th Avenue",
              lat : 32.7139687,
              long : -117.1539126,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "1,700 - 4,700 SF" ,low:1700 ,high:4700} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Corner retail space with F Street Frontage -At the base of a 99 residential unit mixed use project -Located at the corner of F Street and 11th Street in the downtown San Diego neighborhood of East Village -Just a few blocks from the Thomas Jefferson School of Law and Petco Park"
             },
              {
              address: "1429 Island Avenue",
              lat : 32.7102771,
              long : -117.1516832,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "956 - 2,648 SF" ,low:956 ,high:2648} ,
              type: "Street Retail",
              broker:  {name:"Michael Burton" ,contact:"858-875-4685"} ,
              description: "Restaurant/Retail/Office Opportunities available June 2015 occupancy -One of a kind site next to a 60,000 SF public park opening Spring 2015 -Onsite development to include two mixed-use towers containing 965 residential units and 17,000 SF of retail -Exposure to over 1,800 people within the block -Located in the thriving East Village Community at 14th and Island Avenue -Pinnacle on the Park stands as the tallest residential tower and fourth tallest building in San Diego -Co-tenancy with the newly opened Stella Public House and Halcyon Coffee Bar Lounge expanded from Texas -Area tenants include Bottega Americano, The Mission, Fit Athletic, Table No. 10, Bub\' s, Tilted Kilt and Massage Envy -Three blocks from site of proposed three million SF Upper East Village Project -Walking distance to PETCO Park, Convention Center and Gaslamp Quarter -Close proximity to the new central library and Thomas Jefferson School of Law (1,000 students and faculty)-Two blocks from Trolley Station and Park Boulevard -Convenient access to I-5, 94 and 163 freeways -Blocks from site of proposed Chargers Stadium"
             },
             {
              address: "530 Market Street",
              lat : 32.7116747,
              long : -117.159555,
              rent: "lease",
              price: {list:"$17.40 SF/Year", low:17.40, high:17.40},
              size:  {list: "8,300 SF" ,low:8300 ,high:8300} ,
              type: "Street Retail",
              broker:  {name:"David Maxwell" ,contact:"858-677-5343"} ,
              description: "Fantastic central Gaslamp basement space ideal for lounge, club, museum, haunted house. Elevator access Prime signage opportunity along Market Street. Unique architecture and historic location Abundant parking surface lots and Park-it-On Market Structure. Market Street between Fifth & Sixth Avenue. Fifth & Market is one of the Gaslamp Quarters most visible blocks."
             },
             {
              address: "3498 Pacific Hwy",
              lat : 32.7365099,
              long : -117.1788009,
              rent: "lease",
              price: {list:"Negotiable",low: null, high:null},
              size:  {list: "7,900 SF" ,low:7900 ,high:7900} ,
              type: "Restaurant",
              broker:  {name:"Michael Spilky" ,contact:"858-764-4223"} ,
              description: "A flexible 7,900 square foot restaurant space, sitting on prime real estate with unobstructed views of the city skyline, the bay and airport runway where diners can watch all of the action of the planes. Major branding and signage opportunities are available and over 360,000 vehicles per day will see the Restaurant\'s branding Signage off the heavily traveled 5 freeway. There is an exclusive elevator that will move patrons up to the space and a dedicated parking lot with valet service available. 180 feet of floor-to-ceiling glass lined frontage and an opportunity for an outdoor patio. San Diego Starts Right Here - this is the center of the city This location is easy to access from the 5, 8 and 163 freeways with high visibility off of Interstate 5. Its close proximity to Downtown, Little Italy , Hillcrest, Point Loma, Mission Hills and many other affluent neighborhoods allows it to be at the forefront of San Diego\'s best dining communities. A daytime population of over 252,423 within the trade area, this location is also surrounded by dense affluent residential communities. Call for more details. This location is not at the airport but directly off the freeway exit @ Sassafras & Pacific Coast HWY - Restaurant space is located on the top level of the brand new Consolidated Rental Car Center under construction with very high end finishes. See brochure & renderings for complete details."
             },
               {
              address: "10th, 11th, Park & K",
              lat :32.7076747,
              long : -117.1549011,
              rent: "lease",
              price: {list:"$36 SF/Year", low:36, high:36},
              size:  {list: "1,021 - 4,369 SF" ,low:1021 ,high:4369} ,
              type: "Other Retail",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description: "Excellent retail, restaurant or creative office in a 223-unit mixed-use,two-tower residential project. Directly across the street fromPETCO Park in the Ballpark/East Village neighborhood of Downtown San Diego. Within one block from 2,149 public parking spaces at neighboring Tailgate Park and Padres Parkade at 10th and J Street. Across the street from the Downtown Central Library and Thomas Jefferson School of Law. Blocks to Gaslamp Quarter and Convention Center."
             },
              {
              address: "252 Broadway",
              lat : 32.7160099,
              long : -117.1622659,
              rent: "lease",
              price: {list:"$31.73-$60 SF/Year", low:31.73, high:60},
              size: {list: "1,500 - 4,000 SF",low:1500,high:4000},
              type: "Street Retail",
              broker:  {name:"David Maxwell" ,contact:"858-677-5343"} ,
              description: "Situated on the ground floor of a 224 room hotel on Broadway between 2nd and 3rd Avenue. Located in the heart of the Core Business District, directly across from Westfield\' s Horton Plaza. Steps to the historic Gaslamp Quarter, San Diego\' s #1 tourist destination."
             },
               {
              address: "1251 9th Avenue",
              lat : 32.7183521,
              long : -117.1561101,
              rent: "lease",
              price: {list:"$21-$23.40 SF/Year", low:21, high:23.40},
              size: {list: "2,000 - 6,000 SF",low:2000,high:6000},
              type: "Street Retail",
              broker:  {name:"Bill Shrader" ,contact:"858-677-5324"} ,
              description: "Highly prized retail space in the tallest residential building in downtown. Prime signage opportunity along both Tenth Ave and B St, one of two main gateways into downtown from Interstate 5 South. Situated on the ground floor of a 689-unit condominium project, with over 2,878 units within a short walk. Phenomenal location as Hwy 163 enters downtown San Diego, with easy access to I-5 and Hwy 94. One block over Symphony Towers and 701 B Street, both Class-A Office Towers. Ideal location to serve strong daytime CBD population with nearly 130,000 employees in a one-mile radius, and growing Cortez Hill and East Village residential neighborhoods."
             }

        ];
     return availablesite;

    }//end getSiteList()

    </script>
    <script async defer

    src="https://maps.googleapis.com/maps/api/js?key=' . $GLOBALS['GOOGLE_API_KEY'] . '&libraries=visualization&callback=initMap">
    </script>

</html>
';