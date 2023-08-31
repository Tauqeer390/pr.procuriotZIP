<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Venue</title>
    <!-- Favicon-->
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">
    {{-- ///////////////////////////////////////////////////////// --}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

    {{-- //////////////////////////////////////////////////// --}}
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{ asset('/assets/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{ asset('/assets/plugins/node-waves/waves.css') }}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{ asset('/assets/plugins/animate-css/animate.css') }}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{ asset('/assets/css/style.css') }}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{ asset('/assets/css/themes/all-themes.css') }}" rel="stylesheet" />

    <style>
        .form-group .form-control {
            padding-left: 10px;
        }

        .btn_blue {
            background-color: #6860FF;
            border-color: #6860FF;
            color: white;
            width: 70px;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            float: right;
        }

        .btn_blue:hover {
            color: white;
        }

        #geomap {
            width: 100%;
            height: 500px;
            margin-top: 1%;
        }

        .page-loader-wrapper {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body class="theme-red">

    <!-- Top Bar -->
    @include('components.navbar')
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        @include('components.sidebar')
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        @include('components.rightsidebar')
        <!-- #END# Right Sidebar -->
    </section>

    <section class="content" style="margin-top: 0px !important; margin-bottom:20px;">
        <div id="lodaer"></div>
        <div class="container-fluid">
            <div class="block-header">
                @if (Session::has('success'))
                    @php
                        $message = Session::get('success');
                        Session::forget('success');
                    @endphp
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="result"></div>
                <form id="add_menue_form" enctype="multipart/form-data">
                    <span id="form_result1"></span>
                    <span id="form_result2"></span>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Venue Name</label>
                                <div class="form-line">
                                    <input type="text" name="name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Venue Description</label>
                                <div class="form-line">
                                    <input type="text" name="description" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Venue Category</label>
                                <select name="category" class="form-control">
                                    <option value=''>Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value={{ $cat->id }}>{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Floor Number</label>
                                <div class="form-line">
                                    <input type="text" id="floor_number" name="floor_number" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="longitude" name="longitude[]">
                    <input type="hidden" id="latitude" name="latitude[]">
                    <input type="hidden" id="place_name" name="place_name[]">



                    <div class="row">
                        <div class="col-md-12">
                            <div id="geomap"></div>
                        </div>
                    </div>

            </div>



            <div class="col-md-12 text-lg-right d-flex">
                <button type="submit" class="btn add_category_btn btn_blue">Add</button>

                <div class="col md-6  text-lg-left d-flex"></div>
                <label class="form-label">Add GeoJson File</label>
                <input type="file" name="geoJson_file" id="geoJson_file" accept="geojson">

            </div>
        </div>
        </form>

    </section>

    <!-- Jquery Core Js -->
    <script src="{{ asset('/assets/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{ asset('/assets/plugins/bootstrap/js/bootstrap.js') }}"></script>

    <!-- Select Plugin Js -->

    <!-- Slimscroll Plugin Js -->
    <script src="{{ asset('/assets/plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{ asset('/assets/plugins/node-waves/waves.js') }}"></script>

    <!-- Custom Js -->
    <script src="{{ asset('/assets/js/admin.js') }}"></script>

    <!-- Demo Js -->
    {{-- <script src="{{ asset('/assets/js/demo.js') }}"></script> --}}

    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzf7KnzVx3iLASRh25OP_bYgTpUD-dIW8&libraries=places">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>


    <script>
        $(document).ready(function() {
            // Initialize variables
            var geocoder;
            var map;
            var marker;
            var markerData = [];

            // Load Google Map
            function initialize(lat, lng, file) {
                var initialLat = lat;
                var initialLong = lng;
                initialLat = initialLat ? initialLat : 36.169648;
                initialLong = initialLong ? initialLong : -115.141000;

                const center = {
                    lat: 50.064192,
                    lng: -130.605469
                };

                var latlng = new google.maps.LatLng(initialLat, initialLong);
                var options = {
                    zoom: 19,
                    center: latlng,
                    componentRestrictions: {
                        country: "us"
                    },
                    strictBounds: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                };

                map = new google.maps.Map(document.getElementById("geomap"), options);

                geocoder = new google.maps.Geocoder();

                marker = new google.maps.Marker({
                    map: map,
                    draggable: false,
                    position: latlng,
                });

                google.maps.event.addListener(map, "click", function(event) {
                    if (marker) {
                        marker.setMap(null); // Remove the pin marker from the map
                        addPin(event.latLng);
                    }
                });

                function addPin(location) {
                    var pinMarker = new google.maps.Marker({
                        position: location,
                        map: map
                    });

                    var infoWindow;

                    function openInfoWindow() {
                        if (infoWindow) {
                            infoWindow.close();
                        }

                        infoWindow = new google.maps.InfoWindow({
                            content: "Latitude: " + location.lat() + "<br>Longitude: " + location.lng()
                        });

                        var locationNameInput = document.createElement("input");
                        locationNameInput.type = "text";
                        locationNameInput.name = "place_name[]";
                        locationNameInput.placeholder = "Enter location name";
                        locationNameInput.style.marginTop = "5px";

                        var saveButton = document.createElement("button");
                        saveButton.innerHTML = "Save";
                        saveButton.style.marginTop = "5px";
                        saveButton.addEventListener("click", function() {
                            var locationName = locationNameInput.value;
                            infoWindow.setContent("Location Name: " + locationName + "<br>Latitude: " +
                                location.lat() +
                                "<br>Longitude: " + location.lng());

                            // Append the latitude, longitude, and place name to the hidden input fields
                            var latitudeInput = document.getElementById("latitude");
                            var longitudeInput = document.getElementById("longitude");
                            var placeNameInput = document.getElementById("place_name");

                            latitudeInput.value = latitudeInput.value ? latitudeInput.value + ',' + location
                                .lat() :
                                location.lat();
                            longitudeInput.value = longitudeInput.value ? longitudeInput.value + ',' +
                                location.lng() :
                                location.lng();
                            placeNameInput.value = placeNameInput.value ? placeNameInput.value + ',' +
                                locationName :
                                locationName;

                            // Save location along with latitude and longitude in markerData array
                            markerData.push({
                                lat: location.lat(),
                                lng: location.lng(),
                                placeName: locationName
                            });
                        });

                        var deleteButton = document.createElement("button");
                        deleteButton.innerHTML = "Remove";
                        deleteButton.style.marginTop = "5px";
                        deleteButton.addEventListener("click", function() {
                            pinMarker.setMap(null); // Remove the pin marker from the map
                            infoWindow.close(); // Close the info window
                        });

                        var container = document.createElement("div");
                        container.appendChild(locationNameInput);
                        container.appendChild(saveButton);
                        container.appendChild(deleteButton);

                        infoWindow.setContent(container);
                        infoWindow.open(map, pinMarker);
                    }

                    google.maps.event.addListener(pinMarker, "click", openInfoWindow);

                }

                // Read the GeoJSON file
                var reader = new FileReader();
                reader.onload = function(e) {
                    var geoJsonData;
                    try {
                        geoJsonData = JSON.parse(e.target.result);
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                        return;
                    }
                    // Create a GeoJSON layer and add it to the map
                    map.data.addGeoJson(geoJsonData);

                };
                var blob = new Blob([file], {
                    type: 'application/json'
                });
                reader.readAsText(blob);
                // Inside the reader.onload function
            }

            function addGeoJsonToMap(geoJsonData) {
                map.data.addGeoJson(geoJsonData);
            }

            // $('#geoJson_file').change(function() {
            //     var fileInput = this.files[0];
            //     if (fileInput) {
            //         var reader = new FileReader();
            //         reader.onload = function(event) {
            //             var geojson = JSON.parse(event.target.result);
            //             initialize(geojson.features[0].properties.Lat, geojson.features[0]
            //                 .properties.Long,
            //                 fileInput);
            //         };
            //         reader.readAsText(fileInput);
            //     } else {
            //         console.log('No file selected.');
            //     }
            // });

            $('#geoJson_file').change(function() {
                var fileInput = this.files[0];
                if (fileInput) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        var geojson = JSON.parse(event.target.result);
                        initialize(geojson.features[0].properties.Lat, geojson.features[0]
                            .properties.Long,
                            fileInput);

                        var bounds = new google.maps.LatLngBounds();

                        geojson.features.forEach(function(feature) {
                            if (feature.geometry.type === "Point") {
                                // Handle Point geometry
                                var lat = feature.properties.Lat;
                                var lng = feature.properties.Long;
                                var location = new google.maps.LatLng(lat, lng);
                                bounds.extend(location);
                                // ...
                            } else if (feature.geometry.type === "LineString") {
                                // Handle LineString geometry
                                var coordinates = feature.geometry.coordinates;
                                // console.log(coordinates);
                                var path = coordinates.map(function(coord) {
                                    return {
                                        lat: coord[1],
                                        lng: coord[0]
                                    };
                                });

                                // Extend the bounds with the LineString coordinates
                                for (var i = 0; i < path.length; i++) {
                                    bounds.extend(path[i]);
                                }
                                var line = new google.maps.Polyline({
                                    path: path,
                                    geodesic: true,
                                    strokeColor: feature.properties.stroke,
                                    strokeOpacity: feature.properties["stroke-opacity"],
                                    strokeWeight: feature.properties["stroke-width"]
                                });

                                line.setMap(map);

                            }
                        });

                        map.fitBounds(bounds);
                    };
                    reader.readAsText(fileInput);
                } else {
                    console.log('No file selected.');
                }
            });


            // Form Submission
            $('#add_menue_form').on('submit', function(event) {

                htmlLoader = '';
                htmlLoader += '<div class="page-loader-wrapper">';
                htmlLoader += '<div class="loader">';
                htmlLoader += '<div class="preloader">';
                htmlLoader += '  <div class="spinner-layer pl-red">';
                htmlLoader += '    <div class="circle-clipper left">';
                htmlLoader += '      <div class="circle"></div>';
                htmlLoader += '     </div>';
                htmlLoader += '     <div class="circle-clipper right">';
                htmlLoader += '       <div class="circle"></div>';
                htmlLoader += '   </div>';
                htmlLoader += '  </div>';
                htmlLoader += ' </div>';
                htmlLoader += '  <p">Please wait...</p>';
                htmlLoader += ' </div>';
                htmlLoader += '</div>';

                $('#lodaer').append(htmlLoader);
                event.preventDefault();
                // Create a new FormData object
                var formData = new FormData(this);
                var allMarkers = JSON.stringify(markerData);
                formData.append('markerData', allMarkers);
                $.ajax({
                    url: "/add/venue",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: "json",
                    success: function(data) {

                        var html = '';
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            }
                        });

                        if (data.errors) {
                            $('#lodaer').html('');

                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                            $('#form_result2').html(null);
                            $('#form_result1').html(null);
                            $('#form_result2').html(html);
                        }
                        if (data.success) {
                            window.location.href = "/venues/list";
                        }
                    }
                });
            });

            // Initialize Google Map
            initialize();
        });
    </script>
</body>

</html>
