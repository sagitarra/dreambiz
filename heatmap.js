/**
 * Created by artda on 9/9/2026.
 */


function generateHeatmapGrid(centerLat, centerLon, radiusMiles, resolution) {

    var points = [];

    var milesPerLat = 69;
    var milesPerLon = 69 * Math.cos(centerLat * Math.PI / 180);

    var latStep = resolution / milesPerLat;
    var lonStep = resolution / milesPerLon;

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

function renderHeatmap(heatmapGrid, activityCenter, falloff, color) {

    for (var heatmapGridIndex = 0; heatmapGridIndex < heatmapGrid.length; heatmapGridIndex++) {


            var heatmapGridPoint = heatmapGrid[heatmapGridIndex];
            var activity = 0;

            for (var centerIndex = 0; centerIndex < activityCenter.length; centerIndex++) {

                var center = activityCenter[centerIndex];


                var latDistance = (heatmapGridPoint.lat - center.lat) * 69;
                var lonDistance = (heatmapGridPoint.lon - center.lon) *
                    (69 * Math.cos(heatmapGridPoint.lat * Math.PI / 180));

                var distance = Math.sqrt(
                    latDistance * latDistance +
                    lonDistance * lonDistance
                );
                //adjust the gradient curve for activity centers so squares farther away from center are lighter
                activity += center.strength * Math.exp(-(distance * distance) / falloff);
            }

            //brackets the activity weight to match RGB gradient
            activity = Math.max(0.00, Math.min(1.00, activity));
            //adjusts cellSize for best resolution and rendering speed
            var cellSize = 0.05;
            //uses trigonometry to make lon dimensions the same at any latitude so the squares stay square
            var cellLat = cellSize / 69;
            var cellLon = cellSize / (69 * Math.cos(heatmapGridPoint.lat * Math.PI / 180));

            new google.maps.Rectangle({
                map: map,
                bounds: {
                    north: heatmapGridPoint.lat + cellLat / 2,
                    south: heatmapGridPoint.lat - cellLat / 2,
                    east: heatmapGridPoint.lon + cellLon / 2,
                    west: heatmapGridPoint.lon - cellLon / 2
                },
                fillColor: color,
                fillOpacity: (activity ),
                strokeOpacity: 0
            });



    }

}

    function renderActivityGrid (heatmapGrid, activitylat, activitylong, falloff, color, opacityRate) {

            var activity = 0;

        for (var centerIndex = 0; centerIndex < heatmapGrid.length; centerIndex++) {

            var center = heatmapGrid[centerIndex];


            var latDistance = (center.lat - activitylat) * 69;
            var lonDistance = (center.lon - activitylong) *
                (69 * Math.cos(center.lat * Math.PI / 180));

            var distance = Math.sqrt(
                latDistance * latDistance +
                lonDistance * lonDistance
            );
            //adjust the gradient curve for activity centers so squares farther away from center are lighter
            activity = opacityRate * Math.exp(-(distance * distance) / falloff);


            //brackets the activity weight to match RGB gradient
            activity = Math.max(0.00, Math.min(1.00, activity));
            //adjusts cellSize for best resolution and rendering speed
            var cellSize = 0.05;
            //uses trigonometry to make lon dimensions the same at any latitude so the squares stay square
            var cellLat = cellSize / 69;
            var cellLon = cellSize / (69 * Math.cos(center.lat * Math.PI / 180));

            new google.maps.Rectangle({
                map: map,
                bounds: {
                    north: center.lat + cellLat / 2,
                    south: center.lat - cellLat / 2,
                    east: center.lon + cellLon / 2,
                    west: center.lon - cellLon / 2
                },
                fillColor: color,
                fillOpacity: (activity * 0.62),
                strokeOpacity: 0
            });

        }

    }