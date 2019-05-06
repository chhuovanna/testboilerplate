@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row">
        <div class="col">
            

            <div class="card">

                <div class="card-header">
                    <strong>@lang('strings.backend.dashboard.welcome') {{ $logged_in_user->name }}!</strong>
                </div><!--card-header-->
                <div class="card-body">
                    <div style="padding:10px"><a href="{!!route('movie.create')!!}"><button type="button" class="btn btn-success btn-sm pull-right">Add Movie</button></a></div>
                
                    <table id="movie" class="table table-hover table-condensed" style="width:100%">

                        <thead>

                            <tr>

                                <th>Id</th>

                                <th>Title</th>

                                <th>Director</th>

                                <th>Year</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                      </table>
                </div><!--card-body-->
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection

@push('after-scripts')

<script>
$(document).ready(function() {

    oTable = $('#movie').DataTable({

        "processing": true,

        "serverSide": true,

        "ajax": "{{ route('movie.getmovie') }}",

        "columns": [

            {data: 'mID', name: 'mID'},

            {data: 'title', name: 'title'},

            {data: 'director', name: 'director'},

            {data: 'year', name: 'year'},

           {data:'action', name: 'action', orderable: false, searchable: false}

        ]

    });


  
    $(document).off('click','.movie-delete');
    $(document).on('click','.movie-delete' , function(){

        var confirm_delete = confirm("Do you really want to delete this movie?");
        if (confirm_delete == true) {
            $.ajax({
                    type:"DELETE",
                    url:"movie/"+$(this).data('id'),
                    data:{ _token: $('meta[name="csrf-token"]').attr('content'), mID: $(this).data('id')},    
                    success: function (data) {
                        if(data == 1){
                            $('.col').prepend('</div><div class="alert alert-success alert-dismissible fade show success-msg" role="alert" >Deleted<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');            
                            oTable.ajax.reload(null, false);
                        }else{
                            $('.col').prepend('<div class="alert alert-warning alert-dismissible fade show fail-msg" role="alert" >Fail to delete<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                            console.log(data);
                        }
                        
                    },
                    error: function(data){
                        $('.col').prepend('<div class="alert alert-warning alert-dismissible fade show fail-msg" role="alert" >Fail to delete<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        console.log(data);
                    }
            }); 
        }
        
   });
});
</script>

@endpush

