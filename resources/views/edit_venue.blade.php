    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>Update Venue</title>
        <!-- Favicon-->
        <link rel="icon" href="../../favicon.ico" type="image/x-icon">
        {{-- ///////////////////////////////////////////////////////// --}}
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

        {{--    //////////////////////////////////////////////////// --}}
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
            type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

        <!-- Bootstrap Core Css -->
        <link href="{{ asset('/assets/plugins/bootstrap/css/bootstrap.css') }}"
            rel="stylesheet">
        <!-- Waves Effect Css -->
        <link href="{{ asset('/assets/plugins/node-waves/waves.css') }}" rel="stylesheet" />
        <!-- Animation Css -->
        <link href="{{ asset('/assets/plugins/animate-css/animate.css') }}" rel="stylesheet" />
        <!-- Custom Css -->
        <link href="{{ asset('/assets/css/style.css') }}" rel="stylesheet">
        <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
        <link href="{{ asset('/assets/css/themes/all-themes.css') }}" rel="stylesheet" />

        <style>
            #geomap {
                width: 100%;
                height: 300px;
                margin-top: 1%;
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

            .form-group .form-control {
                padding-left: 10px;
            }
        </style>
    </head>

    <body class="theme-red">
        <!-- Page Loader -->
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="preloader">
                    <div class="spinner-layer pl-r">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p style="color: white">Please wait...</p>
            </div>
        </div>
        <!-- #END# Page Loader -->
        <!-- Overlay For Sidebars -->
        <div class="overlay"></div>
        <!-- #END# Overlay For Sidebars -->
        <!-- Search Bar -->
        <div class="search-bar">
            <div class="search-icon">
                <i class="material-icons">search</i>
            </div>
            <input type="text" placeholder="START TYPING...">
            <div class="close-search">
                <i class="material-icons">close</i>
            </div>
        </div>
        <!-- #END# Search Bar -->
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
            <div class="container-fluid">
                <div class="block-header">
                    <!-- Form for editing the venue details -->
                    <form id="update_menue_form" enctype="multipart/form-data">
                        @csrf
                        <!-- Display existing venue details -->
                        <div class="row">
                            <!-- ... Display other input fields for editing the venue details ... -->
                            <!-- Venue Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Venue Name</label>
                                    <div class="form-line">
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $data->name }}">
                                        <input type="text" value="{{ $venue_id }}" name="venuID" id="venuID"
                                            hidden>
                                    </div>
                                </div>
                            </div>
                            <!-- Venue Description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Venue Description</label>
                                    <div class="form-line">
                                        <input type="text" name="description" class="form-control"
                                            value="{{ $data->description }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Venue Category</label>
                                    <select name="category" class="form-control"required="">
                                        <option value=''>Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value={{ $cat->id }}
                                                @if ($data->category == $cat->id) selected @endif>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Floor Number</label>
                                    <div class="form-line">
                                        <input type="text" id="floor_number" name="floor_number" class="form-control"
                                            value="{{ $data->floor_number }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="latitude" name="latitude[]" value="{{ $data->latitude }}">
                        <input type="hidden" id="longitude" name="longitude[]" value="{{ $data->longitude }}">
                        <input type="hidden" id="place_name" name="place_name[]" value="{{ $data->place_name }}">

                        <!-- Map and GeoJSON Data Display -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="geomap"></div>
                            </div>
                        </div>
                        <!-- Display existing GeoJSON data, if available -->
                        <div class="col-md-12 text-lg-right d-flex" style="margin-top: 5px">
                            <button type="submit" class="btn add_category_btn btn_blue">Update</button>
                            <a href="{{ url('/venues/list') }}" class="btn text-white btn_blue ">Back</a>
                            <div class="col md-6  text-lg-left d-flex"></div>
                            <label class="form-label">Add GeoJson File</label>
                            <input type="file" name="geoJson_file" id="geoJson_file"
                                value="{{ $data->geoJson_file }}">
                        </div>
                </div>
                </form>
            </div>
            </div>
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
        <script src="{{ asset('/assets/js/demo.js') }}"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzf7KnzVx3iLASRh25OP_bYgTpUD-dIW8&libraries=places">
        </script>
        {{-- <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzf7KnzVx3iLASRh25OP_bYgTpUD-dIW8&libraries=places&components=country:US"></script> --}}
        <script>
            $('#geoJson_file').change(function() {
                var fileInput = this.files[0];
                if (fileInput) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        var geojson = JSON.parse(event.target.result);
                        // console.log(geojson.features[0].properties.Lat, geojson.features[0].properties.Long);
                        initialize(geojson.features[0].properties.Lat, geojson.features[0].properties.Long,
                            fileInput);
                        // Perform further operations with the parsed GeoJSON object
                    };
                    reader.readAsText(fileInput);
                } else {
                    console.log('No file selected.');
                }
            });
            $(document).ready(function() {
                ajaxRequestID();
            });

            // AJAX request function to get the GeoJSON file URL from the server
            function ajaxRequestID() {
                var VarIDVenu = $('#venuID').val();
                $.ajax({
                    url: '/getEditVEN',
                    data: {
                        'is_EditFTN': true,
                        'VarIDVenu': VarIDVenu,
                    },
                    success: function(data) {
                        var geoJsonPath = data.geoJson_file;
                        if (geoJsonPath) {
                            var geoJsonUrl = '{{ asset('') }}' + geoJsonPath;
                            loadGeoJsonFromFile(geoJsonUrl);
                        }
                    },
                });
            }

            // function loadGeoJsonFromFile(geoJsonUrl) {
            //     $.getJSON(geoJsonUrl, function(geoJsonData) {

            //             initialize(geoJsonData.features[0].properties.Lat, geoJsonData.features[0].properties.Long, null);


            //             addGeoJsonToMap(geoJsonData);
            //         })
            //         .fail(function(xhr, status, error) {
            //             console.error('Error loading GeoJSON:', error);
            //         });
            // }

            // function loadGeoJsonFromFile(geoJsonUrl) {
            //     $.getJSON(geoJsonUrl, function(geoJsonData) {

            //         var geometryType = geoJsonData.features[0].geometry.type;
            //         var bounds = new google.maps.LatLngBounds();

            //         if (geometryType === 'LineString') {
            //             var coordinates = geoJsonData.features[0].geometry.coordinates;
            //             var path = coordinates.map(function(coord) {
            //                 return {
            //                     lat: coord[1],
            //                     lng: coord[0]
            //                 };
            //             });
            //             // Extend the bounds with the LineString coordinates
            //             for (var i = 0; i < path.length; i++) {
            //                 bounds.extend(path[i]);
            //             }
            //             var line = new google.maps.Polyline({
            //                 path: path,
            //                 geodesic: true,
            //                 strokeColor: geoJsonData.features[0].properties.stroke,
            //                 strokeOpacity: geoJsonData.features[0].properties['stroke-opacity'],
            //                 strokeWeight: geoJsonData.features[0].properties['stroke-width']
            //             });
            //             console.log(line);

            //             line.setMap(map);
            //         } else {
            //             var lat = geoJsonData.features[0].properties.Lat;
            //             var lng = geoJsonData.features[0].properties.Long;
            //         }

            //         initialize(lat, lng, null);
            //         addGeoJsonToMap(geoJsonData);
            //     }).fail(function(xhr, status, error) {
            //         console.error('Error loading GeoJSON:', error);
            //     });
            // }

            function showLoader() {
                $('.page-loader-wrapper').fadeIn();
            }

            function hideLoader() {
                $('.page-loader-wrapper').fadeOut();
            }


            function loadGeoJsonFromFile(geoJsonUrl) {
                showLoader(); // Show the loader while loading
                $.getJSON(geoJsonUrl).done(function(geoJsonData) {
                        var geometryType = geoJsonData.features[0].geometry.type;

                        if (geometryType === 'LineString') {
                            var coordinates = geoJsonData.features[0].geometry.coordinates;
                            var lat = (coordinates[0][1] + coordinates[1][1]) / 2;
                            var lng = (coordinates[0][0] + coordinates[1][0]) / 2;
                        } else if (geometryType === 'Polygon') {
                            var coordinates = geoJsonData.features[0].geometry.coordinates[0];
                            var lat = coordinates[0][1];
                            var lng = coordinates[0][0];
                        } else {
                            var lat = geoJsonData.features[0].properties.Lat;
                            var lng = geoJsonData.features[0].properties.Long;
                        }

                        initialize(lat, lng, null);
                        addGeoJsonToMap(geoJsonData);
                    }).fail(function(xhr, status, error) {
                        console.error('Error loading GeoJSON:', error);
                    })
                    .always(function() {
                        hideLoader(); // Hide the loader regardless of success or failure
                    });
            }


            var geocoder;
            var map;
            var marker;
            var markersData = <?php echo !empty($data->markers) ? $data->markers : '[]'; ?>;

            function initialize(lat, lng, file) {
                var latlng = new google.maps.LatLng(lat, lng);
                var options = {
                    zoom: 19,
                    center: new google.maps.LatLng(lat, lng),
                    componentRestrictions: {
                        country: "us"
                    },
                    strictBounds: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                };
                map = new google.maps.Map(document.getElementById("geomap"), options);
                // geocoder = new google.maps.Geocoder();

                // Loop through the markersData array and add markers to the map
                for (let i = 0; i < markersData.length; i++) {
                    const marker = new google.maps.Marker({
                        map: map,
                        draggable: false,
                        position: markersData[i],
                    });

                    // Create an info window content string
                    const infoContent = `
                <div>
                    <strong>Location Name: </strong> ${markersData[i].placeName}<br>
                    <strong>Latitude:</strong> ${markersData[i].lat}<br>
                    <strong>Longitude:</strong> ${markersData[i].lng}
                </div>`;

                    // Create an info window for each marker
                    const infoWindow = new google.maps.InfoWindow({
                        content: infoContent
                    });

                    // Add a click event listener to show the info window when marker is clicked
                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });
                }

                google.maps.event.addListener(map, "click", function(event) {
                    addPin(event.latLng);
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

                            markersData.push({
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

                    markersData.push({
                        lat: location.lat(),
                        lng: location.lng(),
                        placeName: locationName
                    });

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
            }

            function addGeoJsonToMap(geoJsonData) {
                map.data.addGeoJson(geoJsonData);
            }

            $('#update_menue_form').on('submit', function(event) {

                event.preventDefault();
                // Create a new FormData object
                var formData = new FormData(this);
                var allMarkers = JSON.stringify(markersData);
                formData.append('markersData', allMarkers);

                $.ajax({
                    url: "{{ url('/update/venue2') }}",
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
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        if (data.errors) {
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
                            window.location.href = '/venues/list';
                        }
                    }
                })
            });
        </script>


    </body>

    </html>
