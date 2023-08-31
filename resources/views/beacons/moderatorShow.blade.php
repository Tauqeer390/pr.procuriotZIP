@include('components.head')


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzf7KnzVx3iLASRh25OP_bYgTpUD-dIW8&libraries=places"></script>

<link rel="stylesheet" href="{{asset('/build/css/intlTellnput2.css')}}">

<title>Beacon Products </title>
<style>


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
            <div class="col-md-12">
                <h3><b>Beacon Products</b></h3>
            </div>
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
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body" style="margin-top: 1%;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Product Title</th>
                                    <th>Product  Image</th>
                                    <th>Product Description</th>
                                    <th> Product Tagline</th>
                                    <th> Product Price</th>
                                    <th> Product Status</th>
                                    <th>Action</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $key=>$value)
                                    <tr class="active">

                                        <td>{{$value->title}}</td>
                                        <td><img src="{{$value->image}}" style="width: 100px;height: 50px;"> </td>
                                        <td>{{$value->description}}</td>
                                        <td>{{$value->tagline}}</td>
                                        <td>{{$value->price}}</td>
                                        <td>{{$value->status}}</td>
                                        <td>
                                            {{--                                            <button class="btn btn-info becon_edit_btn" id="{{$value->id}}">Edit</button>--}}
                                            <i class="fa fa-edit product_edit_btn" title="Edit" id="{{$value->id}}" style="color: darkgray;font-size: 20px;"></i></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal edit_gateway_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Gateway</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="upate_gateway_form">
                        <span id="form_result3"></span>
                        @csrf
                        <input type="hidden" id="gateway_id" name="gateway_id">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Gateway Title</label>
                            <input type="text" name="edit_gateway_title" style="border: 2px solid lightblue;" class="form-control" id="edit_gateway_title" aria-describedby="emailHelp">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Mac Address</label>
                            <input type="text" name="edit_gateway_mac_address" style="border: 2px solid lightblue;" class="form-control" id="edit_gateway_mac_address" aria-describedby="emailHelp">
                        </div>
                        <div class="form-group">

                            <label for="title">Venue</label>
                            <select name="edit_gateway_venue_id" id="edit_location" class="form-control" style="border: 2px solid lightblue;">
                                <option value=''>Select venue</option>
                                {{--                                @foreach ($locations as $value)--}}
                                {{--                                    <option value="{{ $value->id }}">{{ $value->name }}</option>--}}
                                {{--                                @endforeach--}}
                            </select>

                        </div>
                            <div class="modal-footer">
                             <div class="row">

                                <div class="col-md-12 text-lg-right d-flex">
                                    <button type="submit" class="btn  btn_blue">Update</button>

                                    <button class="btn text-white btn_blue"  data-dismiss="modal">Close </button>
                                </div>
                        </div>
                        </div>

                    </form>
                </div>
            </div>
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

                            <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">UUID</label>
                                <input type="text" name="edit_beacon_uuid" class="form-control" id="edit_beacon_uuid" >
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Vendor Title</label>
                                <input type="text" name="edit_beacon_title" class="form-control" id="edit_beacon_title" >
                            </div>
                        </div>



                        <div class="form-group form-float">
                            <div class="form-line">
                                <label for="exampleInputEmail1">Mac Address</label>
                                <input type="text" name="edit_beacon_mac_address"  class="form-control" id="edit_beacon_mac_address" >
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
                                <label for="exampleInputEmail1"> Vendor Image</label>
                                <input type="file" class="form-control" name="edit_beacon_image"  id="edit_beacon_image">
                            </div>
                        </div>


                        <div class="modal-footer">
                            <div class="row">

                                <div class="col-md-12 text-lg-right d-flex">
                                    <button class="btn text-white btn_blue"  data-dismiss="modal">Close </button>
                                    <button type="submit" class="btn  btn_blue">Update</button>
                                </div>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal response_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align: center;">
                    <p style="color: #6860FF;">Data updated successfully</p>
                </div>

            </div>
        </div>
    </div>

</section>

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

                                    <button class="btn text-white btn_blue"  data-dismiss="modal">Close </button>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $(".table").DataTable({
            "ordering": false
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
                    $('#product_id').val(data.data.id);
                    $('#edit_product_title').val(data.data.title);
                    $('#edit_product_tagline').val(data.data.tagline);
                    $('#edit_product_price').val(data.data.price);
                    $('#edit_product_description').val(data.data.description);
                    $(".edit_product_modal").modal('show');
                }
            }
        })
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

    $(".becon_edit_btn").click(function(){

        var beacon_id = $(this).attr("id");

        $.ajax({
            url: "/edit/beacon/"+beacon_id,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
                        if(data.errors)
                {
                    html = '<div class="alert alert-danger">';
                    for(var count = 0; count < data.errors.length; count++)
                    {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                    console.log(html);
                    $('#form_result2').html(html);
                    $(".update_button").attr("disabled", false);
                }
                if(data.data){
            $('#edit_beacon_uuid').val(data.data.uuid);
                    $('#beacon_id').val(data.data.id);
                    $('#edit_beacon_title').val(data.data.title);
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

    $(".btn_delete_gateway").click(function(){

        var gateway_id = $(this).attr("id");

        if (confirm("If you delete this gateway then the devices linked with this will also delete, Are you sure to delete ?")) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/delete/gateway/"+gateway_id,
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    if(data.response){
                        alert(data.response);
                        // var url = '/dashboard';
                        var url = '/venues/create';
                        $(location).prop('href', url);
                    }
                }
            })
        }
        return false;
    });

    $(".btn_delete_beacon").click(function(){
        var beacon_id = $(this).attr("id");
        if (confirm("Are you sure to delete ?")) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/delete/beacon/"+beacon_id,
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    if(data.response){
                        alert(data.response);
                        window.location.reload();
                    }
                }
            })
        }
        return false;

    });

</script>


@include('components.footer')


