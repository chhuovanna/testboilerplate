@php
    if (isset($movie)){
        $mid = $movie->mID;
        $title = $movie->title;
        $year = $movie->year;
        $director = $movie->director;
        
        if(isset($movie->thumbnail)){
            $source = asset(str_replace('\\','/',$movie->thumbnail->location)) . "/" . $movie->thumbnail->file_name;
        }
        

        if(isset($movie->photos)){
            foreach($movie->photos as $photo){
                $photos_source[$photo->image_id] = asset(str_replace('\\','/',$photo->location)) . "/" . $photo->file_name;
            }
        }
    }else{
        $mid = null;
        $title = null;
        $year = null;
        $director = null;
    }
@endphp
<div class="row mt-4">
    <div class="col">
        <div class="form-group row">
            {{ html()->label('MID')
                ->class('col-md-2 form-control-label')
                ->for('mid') }}

            <div class="col-md-3">
                {{ html()->input('number','mid', $mid)
                    ->class('form-control')
                    ->placeholder('mid')
                    ->attribute('min', 1)
                    ->required()
                    ->autofocus() }}
            </div><!--col-->
        </div><!--form-group-->

        <div class="form-group row">
            {{ html()->label('Title')
                ->class('col-md-2 form-control-label')
                ->for('title') }}

            <div class="col-md-3">
                {{ html()->text('title',$title)
                    ->class('form-control')
                    ->placeholder('title')
                    ->required() }}
            </div><!--col-->
        </div><!--form-group-->
        <div class="form-group row">
            {{ html()->label('Released Year')
                ->class('col-md-2 form-control-label')
                ->for('year') }}

            <div class="col-md-3">
                {{ html()->input('number','year',$year)
                    ->class('form-control')
                    ->placeholder('realeased year')
                    ->attributes(['min'=> 1, 'max' => 9999])
                     }}
            </div><!--col-->
        </div><!--form-group-->
        <div class="form-group row">
            {{ html()->label('Director')
                ->class('col-md-2 form-control-label')
                ->for('director') }}

            <div class="col-md-3">
                {{ html()->text('director',$director)
                    ->class('form-control')
                    ->placeholder('director') }}
            </div><!--col-->
        </div><!--form-group-->

        <div class="form-group row">
            {{ html()->label('Thumbnail')
                ->class('col-md-2 form-control-label')
                ->for('thumbnail_id') }}


            <div class="col-md-3">
            {{ html()->input('file','thumbnail_id')
                    ->class('form-control')
                    ->placeholder('Thumbnail')
                    
                }}
                

           </div>
           @if(isset($source))

           <div class="old-thumbnail">

                <div class="alert alert-primary alert-dismissible fade show" role="alert" >
                    <input type="hidden" name="old_thumbnail" value="1">
                  <img src="{{$source}}"  class="img-thumbnail" width="100px" height="100px">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
            </div>
            @endif
        </div><!--form-group-->

        <div class="form-group row">
            {{ html()->label('Photos')
                ->class('col-md-2 form-control-label')
                ->for('photos') }}

            <div class="col-md-3">


                {{ html()->input('file','photos[]')
                        ->class('form-control')
                        ->placeholder('image')
                        ->attributes(['multiple'=>'true'])

                    }}

            </div><!--col-->

            @if(isset($photos_source))

           <div class="old-photo">
                @foreach ($photos_source as $image_id => $photo_source)
                <div class="alert alert-primary alert-dismissible fade show" role="alert" >
                    <input type="hidden" name="old_photos[]" value={{$image_id}}>
                  <img src="{{$photo_source}}"  class="img-thumbnail" width="100px" height="100px">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endforeach
            </div>
            @endif

        </div><!--form-group-->
    </div><!--col-->
</div><!--row-->
