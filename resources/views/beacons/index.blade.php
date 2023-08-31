@include('components.head')
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.4.2/bootstrap-tagsinput.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.4.2/bootstrap-tagsinput.min.js"></script>
  </head>
<title>Beacon Products</title>
<style>
    .filter-option {
        display: none;
    }
    .bs-caret {
        display: none;
    }
    .caret {
        display: none;
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
</style>
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

<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-4" style="text-align: right;">

            </div>
            <div class="col-md-4" style="text-align: center;">
                <h5 style="color: #6860FF;">Vendor information</h5>
            </div>

            <div class="col-md-4" style="text-align: right;">
                <a href="/vendors/{{$beacon->gateway_id}}"> <i class="material-icons" style="color: #6860FF;">arrow_back</i></a>
            </div>
        </div>
        <div class="body table-responsive">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th style="width: 33%;">Vendor Title</th>
                    <th style="width: 33%;text-align: center;">Mac Address</th>
                    <th style="width: 33%;text-align: right;">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr class="active">
                    <td style="width: 33%;">{{ $beacon->title }}</td>
                    <td style="width: 33%;text-align: center;">{{ $beacon->mac_address }}</td>
                    <td style="width: 33%;text-align: right;">

                        <i class="fas fa-edit edit_beacon_form" id="{{$beacon->id}}" style="color: darkgray;font-size: 20px"></i>
                        <i class="fas fa-trash btn_delete_beacon" id="{{$beacon->id}}" style="color: darkgray;font-size: 20px"></i>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        @if(Session::has('success'))
                @php
                $message = Session::get('success');
                Session::forget('success');
                    @endphp
               <div class="alert alert-success alert-block" style="background-color: #2b982b">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
        <div class="row">
            <div class="col-md-4" style="text-align: right;">
            </div>
            <div class="col-md-4" style="text-align: center;">
                <h5 style="color: #6860FF;">Products information</h5>
            </div>

            <div class="col-md-4" style="text-align: right;">
                <button class="btn btn-sm add-product" id="ub" style="border-radius: 5%;background-color: #6860FF;color: white;font-size: 14px;">Add Product</button>
            </div>
        </div>

        <div class="body table-responsive">
            <table class="table table-condensed beacons_table">
                <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Product Image</th>
                    <th>Product Tagline</th>
                    <th>Product Description</th>
                    <th>Product Price</th>
                    <th>Product Status</th>
                    {{-- <th>Distance</th> --}}
                    <th>Action</th>

                </tr>
                </thead>
                <tbody>
                       @foreach($products as $key=>$value)
                       <tr class="active">
                        <td>{{$value->title}}</td>
                        <td>@if(isset($value->image))<img src="{{$value->image}}" style="width: 100px;height: 50px;"> @else No Image @endif </td>
                        <td>{{$value->tagline}}</td>
                        <td>{{$value->description}}</td>
                        <td>{{$value->price}}</td>
                        <td>{{ucfirst($value->status)}}</td>
                        {{-- <td>{{$value->distance}}</td> --}}
                        <td>
                            {{--                            <button class="btn btn-info btn-sm becon_edit_btn" id="{{$value->id}}" style="padding-left: 12%;">Edit</button>--}}

                            <i class="fas fa-edit product_edit_btn" id="{{$value->id}}" style="color: darkgray;font-size: 20px"></i>

                            {{--                            <button class="btn btn-danger btn-sm btn_delete_beacon" id="{{$value->id}}">delete</button>   </td>--}}

                            <i class="fas fa-trash btn_delete_product" id="{{$value->id}}" style="color: darkgray;font-size: 20px"></i>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

   <div class="modal edit_beacon_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Beacon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="upate_beacon_form">
                         <span id="form_result2"></span>
                        @csrf
                        <input type="hidden" id="beacon_id" name="beacon_id">

                        {{-- <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">UUID</label>
                                <input type="text" name="edit_beacon_uuid" class="form-control" id="edit_beacon_uuid" >
                            </div>
                        </div> --}}
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Vendor Title</label>
                                <input type="text" name="edit_beacon_title" class="form-control" id="edit_beacon_title" >
                            </div>
                        </div>


                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Mac Address</label>
                                <input type="text" name="edit_beacon_mac_address" class="form-control" id="edit_beacon_mac_address" >
                            </div>
                        </div>


                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Vendor Tagline</label>
                                <input type="text" name="edit_beacon_tagline"  class="form-control" id="edit_beacon_tagline" >
                            </div>
                        </div>


                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Vendor Description</label>
                                <input type="text" name="edit_beacon_description"  class="form-control" id="edit_beacon_description" >
                            </div>
                        </div>


                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Vendor Image</label>
                                <input type="file" class="form-control" name="edit_beacon_image"   id="edit_beacon_image">
                            </div>
                        </div>

                        <div class="modal-footer">
                             <div class="row">

                                <div class="col-md-12 text-lg-right d-flex">
                                    <button type="submit" class="btn  btn_blue">Update</button>

                                    {{-- <button class="btn text-white btn_blue"  data-dismiss="modal">Close </button> --}}
                                </div>
                        </div>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="submit" class="btn" style="color: white;font-size: 14px;background-color: #6860FF;">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal edit_product_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="upate_product_form">
                         <span id="form_result2"></span>
                        @csrf
                        <input type="hidden" id="product_id" name="product_id">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Product Title</label>
                                <input type="text" name="edit_product_title" class="form-control" id="edit_product_title" >
                            </div>
                        </div>
                           <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Tag</label><br>
                     <input id="edit_product_tags" class="form-control" name="edit_product_tags"  data-role="tagsinput" type="text">
                        </div>
                    </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Product Tagline</label>
                                <input type="text" name="edit_product_tagline"  class="form-control" id="edit_product_tagline" >
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Product Price</label>
                                <input type="number" name="edit_product_price"  class="form-control" id="edit_product_price" >
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Product Description</label>
                                <input type="text" name="edit_product_description"  class="form-control" id="edit_product_description" >
                            </div>
                        </div>
                        {{-- <div class="form-group form-float">
                            <div class="form-line">
                                <label for="title">Distance</label>
                                <select name="edit_product_distance" class="edit_product_distance" style="width: 550px; height: 33px;" >
                                    @foreach($distances as $distance)
                                        <option value="{{$distance}}">{{$distance}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Product Image</label>
                                <input type="file" class="form-control" name="edit_product_image"   id="edit_product_image">
                            </div>
                        </div>

                        <div class="modal-footer">
                             <div class="row">

                                <div class="col-md-12 text-lg-right d-flex">
                                    <button type="submit" class="btn  btn_blue">Update</button>

                                    {{-- <button class="btn text-white btn_blue"  data-dismiss="modal">Close </button> --}}
                                </div>
                        </div>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="submit" class="btn" style="color: white;font-size: 14px;background-color: #6860FF;">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal confirmation_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure to delete this product ?</p>
                    <input type="hidden" id="set_org_id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn custom-confirmation" style="background-color: #6860FF;color: white;font-size: 14px;">Ok</button>
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal confirmation_modal2" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure to delete this beacon ?</p>
                    <input type="hidden" id="set_org_id2">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn custom-confirmation2" style="background-color: #6860FF;color: white;font-size: 14px;">Ok</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal response_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p style="text-align: center;color: #6860FF;">Data Updated Successfully</p>
                </div>
            </div>
        </div>
    </div>

</section>

<div class="modal add_product_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


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


                <span id="form_result"></span>
                <form id="addRoductForm">
                    @csrf
                    <input type="hidden" id="hidden_beacon_id" value="{{$beacon->id}}" name="hidden_beacon_id">
                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Title</label>
                            <input type="text" class="form-control" name="title" placeholder="Enter Product Title"  >
                        </div>
                    </div>


                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Tag</label><br>
                     <input id="product-tags" class="form-control" name="product_tags"   data-role="tagsinput" type="text">
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Tagline</label>
                            <input type="text" class="form-control" name="tag_line" placeholder="Enter tag line"  >
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Price</label>
                            <input type="number" class="form-control" name="price" placeholder="Enter Price"  >
                        </div>
                    </div>


                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Description</label>
                            <input type="text" class="form-control" name="description"  placeholder="Enter description" >
                        </div>
                    </div>


                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Image</label>
                            <input type="file" class="form-control"  id="myFile" name="image"  >
                        </div>
                    </div>


                        <div class="modal-footer">
                             <div class="row">

                                <div class="col-md-12 text-lg-right d-flex">
                                    <button type="submit" class="btn  btn_blue">Add</button>

                                    {{-- <button class="btn text-white btn_blue"  data-dismiss="modal">Close </button> --}}
                                </div>
                        </div>
                        </div>

                    {{-- <div class="modal-footer"> --}}

                        {{-- <button type="submit" class="btn" style="background-color: #6860FF;color: white;font-size: 14px;">Add</button> --}}
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                    {{-- </div> --}}
                </form>

            </div>
        </div>
    </div>





    {{--    <form id="delete_gateway_form">--}}
    {{--        <input type="hidden" id="gateway">--}}
    {{--    </form>--}}


</div>
<script src="http://timschlechter.github.io/bootstrap-tagsinput/examples/bootstrap-2.3.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function(){
        $('#set_org_id').val(null);
        $('#set_org_id2').val(null);
        $(".add-product").click(function(){
            $(".add_product_modal").modal('show');
            $('#form_result').html(null);
        });
    });


      $(".product_edit_btn").click(function(){

        var product_id = $(this).attr("id");

        $.ajax({
            url: "/get/beacon-product/"+product_id,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
                if(data.data){
                    console.log(data.data.tags);
                    $('#product_id').val(data.data.id);
                    $('#edit_product_title').val(data.data.title);
                    $('#edit_product_tags').val(data.data.tags);
                    $('#edit_product_tagline').val(data.data.tagline);
                    $('#edit_product_price').val(data.data.price);
                    $('#edit_product_description').val(data.data.description);
                    $(".edit_product_modal").modal('show');
                }
            }
        })
    });
    $('#addRoductForm').on('submit', function (event) {

        event.preventDefault();
        $.ajax({
            url: "/add/beacon-product",
            method: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {

                var html = '';
                if(data.errors)
                {
                    html = '<div class="alert alert-danger">';
                    for(var count = 0; count < data.errors.length; count++)
                    {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }

                if(data.success)
                {

                 window.location.reload();
                }
                $('#form_result').html(html);

            }
        })
    });



    $('#upate_beacon_form').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: "/update/beacon",
            method: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
                          if(data.errors)
                {
                    console.log("asd");
                    html = '<div class="alert alert-danger">';
                    for(var count = 0; count < data.errors.length; count++)
                    {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';

                    $('#form_result2').html(null);
                    $('#form_result1').html(null);
                    $('#form_result2').html(html);
                    $(".update_button").attr("disabled", false);
                }
                if (data.response) {
                    // alert(data.response);
                    window.location.reload();
                }
            }
        });
    });
    $('#upate_product_form').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: "/update/beacon-product",
            method: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
                          if(data.errors)
                {
                    console.log("asd");
                    html = '<div class="alert alert-danger">';
                    for(var count = 0; count < data.errors.length; count++)
                    {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';

                    $('#form_result2').html(null);
                    $('#form_result1').html(null);
                    $('#form_result2').html(html);
                    $(".update_button").attr("disabled", false);
                }
                if (data.response) {
                    // alert(data.response);
                    window.location.reload();
                }
            }
        });
    });


    $(".edit_beacon_form").click(function(){
        var beacon_id = $(this).attr("id");
      $.ajax({
            url: "/edit/beacon/"+beacon_id,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {

                if(data.data){

                    console.log(data.data.distance);

                    $('#beacon_id').val(data.data.id);
                    $('#edit_beacon_title').val(data.data.title);
                    $('#edit_beacon_uuid').val(data.data.uuid);
                    $('#edit_beacon_mac_address').val(data.data.mac_address);
                    $('#edit_beacon_tagline').val(data.data.tagline);
                    $('#edit_beacon_description').val(data.data.description);

                    $('.edit_beacon_distance option[value="'+data.data.distance+'"]').prop('selected',true);

                    // $('#gender option[value="' +  obj.data.member_gender +'"]').prop("selected", true);

                    $(".edit_beacon_modal").modal('show');
                }
            }
        })
    });




    $(document).on("click",".custom-confirmation",function() {
        var get_orgn_id = $('#set_org_id').val();
        if(get_orgn_id!=null){
            $.ajax({
                headers:
                    { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "/delete/beacon-product/"+get_orgn_id,
                success: function(data)
                {
                     window.location.reload();
                }
            });
        }else{
            alert('something went wrong');
        }
    });

    // $(".btn_delete_beacon").click(function(){
    //     var beacon_id = $(this).attr("id");
    //     if (confirm("Are you sure to delete ?")) {
    //         $.ajax({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             url: "https://pr.procuriot.com/delete/beacon/"+beacon_id,
    //             contentType: false,
    //             cache: false,
    //             processData: false,
    //             dataType: "json",
    //             success: function (data) {
    //                 if(data.response){
    //                     alert(data.response);
    //                     window.location.reload();
    //                 }
    //             }
    //         })
    //     }
    //     return false;
    //
    // });

    $(".btn_delete_beacon").click(function(){

        $('#set_org_id2').val(null);

        var org_id2 = $(this).attr("id");

        $('#set_org_id2').val(org_id2);

        $('.confirmation_modal2').modal('show');

    });
    $(".btn_delete_product").click(function(){

        $('#set_org_id').val(null);

        var org_id2 = $(this).attr("id");
        console.log(org_id2);
        $('#set_org_id').val(org_id2);

        $('.confirmation_modal').modal('show');

    });
    $(document).on("click",".custom-confirmation2",function() {
        var get_orgn_id = $('#set_org_id2').val();
        if(get_orgn_id!=null){
            $.ajax({
                headers:
                    { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "/delete/beacon/"+get_orgn_id,
                success: function(data)
                {
                    // $('.response_modal2').modal('show');
                    window.location.reload();
                }
            });
        }else{
            alert('something went wrong');
        }
    });
</script>


@include('components.footer')


