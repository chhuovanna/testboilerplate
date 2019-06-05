

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

                                <th>Photo</th>

                                <th>Title</th>

                                <th>Director</th>

                                <th>Year</th>

                                <th>Rate</th>                                

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

            {data: 'file_name', name: 'image',orderable: false, searchable: false,
                render:function ( data, type, row, meta ) {
                    if (data){
                        var source = "{{ asset('images/thumbnail') }}"+"/"+data;
                        return '<img src="'+source+'" height="42" width="42" class="thumbnail" data-id="'+row.mID+'">';
                    }else{
                        return '<i class="fa fa-film fa-3x" aria-hidden="true"></i>';
                    }
                }
            },

            {data: 'title', name: 'title',
                render:function ( data, type, row ) {
                    return type === 'display' && data && data.length > 50 ? '<span title="'+data+'">'+data.substr( 0, 20 )+'...</span>' : data; 
                }
            },

            {data: 'director', name: 'director',
                render:function ( data, type, row ) {
                    return type === 'display' && data && data.length > 50 ? '<span title="'+data+'">'+data.substr( 0, 20 )+'...</span>' : data; 
                }
            },

            {data: 'year', name: 'year'},
            {data:'avgstars', name:'avgstars'},
            {data:'action', name: 'action', orderable: false, searchable: false},
        

        ],
        "order":[[0,'desc']]

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
                        if(data[0] == 1){
                            $('.col').prepend('</div><div class="alert alert-success alert-dismissible fade show success-msg" role="alert" >Deleted<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');            
                            oTable.ajax.reload(null, false);
                        }else{
                            $('.col').prepend('<div class="alert alert-warning alert-dismissible fade show fail-msg" role="alert" >Fail to delete. '+data[1]+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
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


    $(document).off('click','.thumbnail');
    $(document).on('click','.thumbnail' , function(){

       // alert( $(this).data('id'));
            $.ajax({
                    type:"GET",
                    url:"movie/getphotos",
                    data:{ mID: $(this).data('id')},    
                    success: function (data) {
                        if(data[0] == 1){
                            var i;
                            var html = "";
                            var source = "";
                            var eleclass = "";
                            for (i=0; i< data[1].length ; i++){
                                source = "{{asset('images/photos')}}" + "/" + data[1][i].file_name;
                                if (i==0)
                                    eleclass="class='start'";
                                else
                                    eleclass = "";
                                html = html+ "<a href='" + source+ "' " + eleclass + " ><img src='" + source+"' height='40' width='40' ></a>";
                            }

                            html = "<div id='lightgallery'>" + html + "</div>";
                            $('.col').append(html);
                            var $lg = $("#lightgallery");
                            $lg.lightGallery({
                                mode: 'lg-slide-circular', 
                            }); 
                            $lg.on('onCloseAfter.lg', function (event){
                                $(this).data('lightGallery').destroy(true);
                                $('#lightgallery').remove();
                            });
                            $('.start').click();
                            console.log(html);
                        }
                    },
                    error: function(data){
                        console.log(data);
                    }
            }); 

        
   });
});
</script>

@endpush

@push('after-styles')
<style type="text/css"> 
    .lg-backdrop.in {
     opacity: 0.5 !important; 
}
</style>
@endpush

