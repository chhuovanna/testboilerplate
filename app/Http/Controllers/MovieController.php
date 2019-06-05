<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // for deleting file
use App\movie;
use App\reviewer;
use App\rating;
use App\image;
use Datatables;

use DB;


class MovieController extends Controller
{
	public function index() {
		return view('movieindex');
	}
	public function create() {
		return view('moviecreate');
	}
	public function store(Request $request) {
		$movie = new Movie();
		$movie->mID = $request->get('mid');
		$movie->title = $request->get('title');
		$movie->year = $request->get('year');
		$movie->director = $request->get('director');

		$validateData = $request->validate([
			'thumbnail_id' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
			,'photos[]' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

		//get file from input
		$file = $request->file('thumbnail_id');
		$thumbnail = new Image();
		$thumbnail->file_name = rand(1111,9999).time().'.'.$file->getClientOriginalExtension();
		$thumbnail->location = 'images\thumbnail'; //thumbnail is stored in public/images/thumbnail
		
		try {
			$thumbnail->save();
			//movie the file to it's location on server
			$file->move(public_path($thumbnail->location),$thumbnail->file_name);

			//thumbnail of movie
			$movie->thumbnail_id = $thumbnail->image_id;
			$movie->save();

			//test if user has upload other photos or not
			if($request->hasFile('photos')){

				//get the array of photos
				$photos = $request->file('photos');


				foreach ($photos as $key => $file) {
					$photo = new Image();
					$photo->file_name = rand(1111,9999).time().'.'.$file->getClientOriginalExtension();

					//photos are stored on server in folder public/images/photos
					$photo->location = 'images\photos';
					
					//photo belongs to movie
					$photo->mID = $request->get('mid');
					$photo->save();
					$file->move(public_path($photo->location),$photo->file_name);
				}
			}
			return redirect()->route('movie.index')->withFlashSuccess('Movie is added');
		}
		catch (\Exception $e) {
			return redirect()
            ->back()
            ->withInput($request->all())
            ->withFlashDanger("Movie can't be added. ". $e->getMessage());

		}
	}
	public function show($id) {
		echo 'showlalal';
	}
	public function edit($id) {
		$movie = Movie::find($id);
		return view('movieupdate',['movie'=>$movie]);
	}
	public function update(Request $request, $id) {
		$movie = Movie::find($id);
		$movie->mID = $request->get('mid');
		$movie->title = $request->get('title');
		$movie->year = $request->get('year');
		$movie->director = $request->get('director');
		try{
			$movie->save();
			return redirect()->route('movie.index')->withFlashSuccess('Movie is updated');
		}catch(\Exception $e){
			return redirect()
            ->back()
            ->withInput($request->all())
            ->withFlashDanger("Movie can't be updated. ". $e->getMessage());
		}

	}
	public function destroy($id) {
		

		try{

			//to get the array of photos of the movie
			$photos = Movie::find($id)->photos;
			$res['photos'] = true;

			foreach ($photos as $photo) {
				$file = public_path($photo->location).'\\'.$photo->file_name;
				if ( File::exists($file)) {
					
					if(File::delete($file)){//delete the file from the folder
						$res['photos'] = $res['photos'] && $photo->delete(); //delete the file from database
					}

				}				
			}


			
			//to get thumbnail of the movie to be deleted. in Movie model, there is function called thumbnail
			$thumbnail = Movie::find($id)->thumbnail;


			$file = public_path($thumbnail->location).'\\'.$thumbnail->file_name;

			//delete movie from database
			$res['movie'] = Movie::destroy($id);

			//test if the thumbnail file exists or not
			if ( File::exists($file)) {
				//delete the file from the folder
			   if(File::delete($file)){
			   		//delete the thumbnail of the movie from database;
					$res['thumbnail'] = $thumbnail->delete();
			   }
			}
			
			if ($res['movie'] )
				return [1];
			else
				return [0];
		}catch(\Exception $e){
			return [0,$e->getMessage()];
		}

	}

	public function getform(){
		$movies = movie::all();
		$reviewers = reviewer::all();
		return view('movierate', [ 'movies' => $movies, 'reviewers' => $reviewers  ]);
	}

	public function saverating(Request $request){
		
		$rating = new Rating();
		$rating->mID = $request->get('mid');
		$rating->rID = $request->get('rid');
		$rating->stars = $request->get('stars');
		$rating->ratingDate = date('Y-m-d');
		try {
			$rating->save();
			return redirect()->route('movie.rate')->withFlashSuccess('Rating is added');
		}
		catch (\Exception $e) {
			return redirect()
            ->back()
            ->withInput($request->all())
            ->withFlashDanger("Rating can't be added. ". $e->getMessage());
		}
	}



	public function showrate(){
		$movies = movie::all();
		return view('movieshowrate', [ 'movies' => $movies]);
	}

	public function getrating(Request $request){
		$mid = $request->input('mid');
		$ratings = Rating::getRating($mid);
		if (sizeof($ratings) > 0){
			$stars = 0;
			$body = "";

			foreach ($ratings as $rating) {
				$stars += $rating->stars;
				$body .= <<<EOF
	<tr>
		
		<td>$rating->name</td>
		<td>$rating->stars</td>
		<td>$rating->ratingDate</td>
	</tr>
EOF;
			}

			$stars = $stars/sizeof($ratings);
			$html = <<<EOF
<br><label class='col-md-4 form-control-label'>Average stars : $stars</label><br><br>
<table clas="table">
	<thead>
		<tr>
			<th scope="col">reviewer</th>
			<th scope="col">stars</th>
			<th scope="col">ratingDate</th>
		</tr>
	</thead>
	<tbody>
	$body
	</tdbody>
</table>

EOF;
			return $html;
		}else{
			return "No Rating";
		}
	}

	public function getmovie(){
		//$movies = Movie::select(['mID', 'title', 'director', 'year']);
		$movies = Movie::select(['movie.mID', 'title', 'director', 'year', DB::raw("AVG(stars) as avgstars"), 'image.file_name', 'image.location'])
		->leftJoin('rating', 'movie.mID', '=', 'rating.mID')
		->leftJoin('image','thumbnail_id', '=', 'image.image_id')
        ->groupBy('movie.mID');;

        return Datatables::of($movies)
        				->addColumn('action', function ($movie) {
        										$html = '<a href="'.route('movie.edit', ['id' => $movie->mID]).'" class="btn btn-primary btn-sm"><i class="far fa-edit"></i></a>&nbsp;&nbsp;&nbsp;';
        										$html .= '<a data-id="'.$movie->mID.'" class="btn btn-danger btn-sm movie-delete"><i class="far fa-trash-alt"></i></a>&nbsp;&nbsp;&nbsp;' ;
        										$html .= '<a data-id="'.$movie->mID.'"  class="btn btn-info btn-sm movie-rate-info"><i class="fa fa-search" aria-hidden="true"></i></i></a>' ;
        										
                								return $html;
            								})
        				->make(true);
	}

	public function getphotos(Request $request){
		$mid = $request->get('mID');

		//get the list of photos of movie using relationship defined in model
		$photos = Movie::find($mid)->photos;
		if (sizeof($photos) > 0){

			$html = "";
            $source = "";
            $eleclass = "";
            $i=0;
            foreach ($photos as $photo) {
                
                //get url of each photo        
                $source = asset(str_replace('\\','/',$photo->location)) . "/" . $photo->file_name;
                if ($i==0){
                	//set class start to the first photo, so we can use js to click it 
                    $eleclass = "class='start'";
                    $i = 1;
                }
                else
                    $eleclass = "";
                //html code for each photo html element
                $html .=  "<a href='" . $source . "' " . $eleclass . " ><img src='" . $source ."' height='40' width='40' ></a>";

            }

            //list of photos must be in the dive with id lightgallery, so in view we can apply the lightgallery library on it
            $html = "<div id='lightgallery'>" . $html . "</div>";
			return [1, $html];
		}else
			return [0];
	}

}
