@include('components.head')


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <Title>Categories</Title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<link rel="stylesheet" href="{{asset('/build/css/intlTellnput2.css')}}">
{{--
<link rel="stylesheet" href="{{asset('/build/css/demo.css')}}">--}}
<style>
    #geomap {
        width: 100%;
        height: 400px;
    }

    .dropdown-toggle {
        display: none;
    }

    .ub66 {
        width: 100px !important;
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

    <section class="content">

        <div class="container-fluid">
               @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block" style="background-color: #176a17">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong id="message">{{ $message }}</strong>
                    <span id="message1"></span>
                </div>
                @endif
            <div class="row">
                <div class="col-md-4">
                    <h4 style="font-weight: bold;font-size: 18px;">Categories</h4>
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4" style="text-align: right;">
                    {{-- <button class="btn" id="fa-plus"
                        style="float: right;background-color: #6860FF;font-size: 14px;color: white;">Add
                        Admin</button>--}}
                    <a href="{{ route('categories.create') }}" class="btn"
                        style="float: right;background-color: #6860FF;font-size: 14px;color: white;">Add Category</a>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="body" style="margin-top: 1%;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>

                                            <th style="width: 14%" scope="col">Category Image</th>
                                            <th style="width: 14%" scope="col">Category Name</th>

                                            <th style="width: 22%" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($categories))
                                        @foreach ($categories as $category)
                                        <tr>
                                            <td > <img src="{{ $category->image  }}" style="max-width:30%" alt=""> </td>
                                            <td>{{ $category->category_name }}</td>
                                            {{-- <td class="image-show" data-toggle="modal" data-caption="test caption text"data-target="#trslphotos"
                                                 data-image = "{{ $category->image }}"> <strong>Show Image</strong>  </td> --}}

                                            <td style="width: 22%">
                                                {{--                                                <i class="fa fa-edit" title="Edit" id="{{$value->id}}" style="color: darkgray;font-size: 20px;"></i>--}}
                                                <a href="{{route("categories.edit",$category->id)}}"
                                                   title="Edit" style="font-size: 20px;color: darkgray !important;"> <i class="fa fa-edit"></i> </a>

                                                   <i class="fa fa-trash btn_delete_cat" title="Delete" onclick="deleteCard({{  $category->id }})" id="{{$category->id}}"   data-cat-id="{{ $category->id }}" style="color: darkgray;font-size: 20px;"></i>
                                            </td>
                                            </tr>
                                            @endforeach
                                            @endif

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</section>

<!-- modal -->
<div class="modal fade" id="trslphotos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"><img src="" class="img-responsive"></div>
        </div>
    </div>
</div>
<!-- ./modal -->
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
                    <p>Are you sure to delete this Category?</p>
                    <input type="hidden" id="set_org_id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn custom-confirmation" style="color: #6860FF;color: white;">Ok</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
<script>

    function show(params) {
            console.log(params);
    }
//     $('#trslphotos').on('shown.bs.modal', function (a, b,c) {
//         console.log("asd");
//  var clickedImageUrl = a.relatedTarget
//  console.log( a.relatedTarget);
// //   displayPhotos(clickedImageUrl);
// })

    $(document).ready(function() {
         $(".image-show").click(function () {
        var id = $(this).attr('data-image');
         $('.modal-body img').attr('src',id);

    });
        $('#set_org_id').val(null);
        $(".table").DataTable({
            "ordering": false
        });

        $('#spnPhoneStatus').html(null);
        $('#spnPhoneStatus2').html(null);

    });
    function deleteCard(card_id) {
        var index = "{{ route("categories.index") }}";
         var card_id = card_id;

        let url = "{{ route('categories.destroy',':id') }}";
        url = url.replace(':id', card_id);
             $('.confirmation_modal').modal('show');
        $('.custom-confirmation').click(function () {
                   $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",

                        },
                        success: function (data) {
                             window.location = index;
                        },
                        error: function (response) {
                            alert('Something went wrong');
                        }
                    });
                });
            }
</script>

@include('components.footer')
