@php
    if (isset($movie)){
        $mid = $movie->mID;
        $title = $movie->title;
        $year = $movie->year;
        $director = $movie->director;
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
                    ->required()
                }}

            </div><!--col-->
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
        </div><!--form-group-->
    </div><!--col-->
</div><!--row-->
