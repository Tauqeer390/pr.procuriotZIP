<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Update Categories</title>
    <!-- Favicon-->
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">
    {{--/////////////////////////////////////////////////////////--}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

    {{--    ////////////////////////////////////////////////////--}}
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{asset('/assets/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{asset('/assets/plugins/node-waves/waves.css')}}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{asset('/assets/plugins/animate-css/animate.css')}}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{asset('/assets/css/style.css')}}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{asset('/assets/css/themes/all-themes.css')}}" rel="stylesheet" />

    <style>
        #geomap {
            width: 100%;
            height: 200px;
            margin-top: 1%;
        }

        .dropdown-toggle {
            display: none;
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
        .ub66 {
            width: 100px !important;
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

    <section class="content" style="margin-top: 0px !important;">
        <div class="container-fluid">
            <div class="block-header">
                @if ($message = Session::get('success'))
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



                <form id="update_categoryForm" method="post" action="{{ route("categories.update",$category->id) }}" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <span id="send_link_response" style="color: #6860FF;"></span>
                        </div>
                    </div>
                     {{ csrf_field() }}
{{ method_field('patch')}}
                   <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Category Name</label>
                            <div class="form-line">
                                <input type="text" name="name" value="{{ $category->category_name }}"  class="form-control">
                            </div>
                              <span class="text-danger" id="nameError"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Category Image</label>
                            <div class="form-line">
                                 <input type="file" name="image" class="form-control">
                            </div>
                              <span class="text-danger" id="imageError"></span>
                        </div>
                    </div>

                </div>
                <div class="row mt-2" style="margin-top: 1%;">
    <div class="col-md-12 text-lg-right d-flex">
        <button type="submit" class="btn add_category_btn btn_blue">Update</button>
        <a href="{{route("categories.index")}}" class="btn text-white btn_blue ">Back</a>
    </div>
</div>
                    {{-- <div class="row" style="margin-top: 1%;">

                        <div class="col-md-12" style="text-align: right;">
                            <button type="submit" class="btn add_category_btn"
                                style="background-color: #6860FF;color: white;margin-right: 1%;">Update</button>
                            <a href="{{route("categories.index")}}" class="btn"
                                style="float: right;margin-right: 1%; width: 6%;background-color: #6860FF;color: white;">Back</a>
                        </div>
                    </div> --}}

                </form>


                <!-- display google map -->


                <!-- display selected location information -->
            </div>
        </div>
    </section>

    <!-- Jquery Core Js -->
    <script src="{{asset('/assets/plugins/jquery/jquery.min.js')}}"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{asset('/assets/plugins/bootstrap/js/bootstrap.js')}}"></script>

    <!-- Select Plugin Js -->
    <script src="{{asset('/assets/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="{{asset('/assets/plugins/jquery-slimscroll/jquery.slimscroll.js')}}"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{asset('/assets/plugins/node-waves/waves.js')}}"></script>

    <!-- Custom Js -->
    <script src="{{asset('/assets/js/admin.js')}}"></script>

    <!-- Demo Js -->
    <script src="{{asset('/assets/js/demo.js')}}"></script>

    {{--<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzf7KnzVx3iLASRh25OP_bYgTpUD-dIW8&libraries=places&components=country:US"></script>--}}



    <script>
        var geocoder;
        var map;
        var marker;

        $(document).ready(function () {
            var category_id = "{{ $category->id }}";
            var route = "{{ route('categories.update',':id') }}";
            route = route.replace(':id', category_id);
                $('#update_categoryForm').submit(function(evt) {
                    $.ajax({
                    url: route,
                    type: "PUT",
                    enctype: 'multipart/form-data',
                   data: new FormData(this),
                    success: function (data) {

                        },

                    error: function (response) {
                    var res = JSON.parse(response.responseText);
                        $('#nameError').text(res.errors.name);
                        $('#imageError').text(res.errors.image);
                    },
                })
                }),
                event.preventDefault();



        });
    </script>

</body>

</html>
