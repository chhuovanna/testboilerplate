@extends('backend.layouts.app')

@section('title', 'Add movie')

@section('content')
{{ html()->form('POST', route('movie.store'))->class('form-horizontal')->acceptsFiles()->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        Movie Managment
                        <small class="text-muted">Add Movie</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

             @include('backend.layouts.moviepartialform')
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('movie.index'), 'Cancel') }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit('Submit') }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
{{ html()->form()->close() }}
@endsection

@push('after-scripts')

<script>
 
</script>

@endpush
