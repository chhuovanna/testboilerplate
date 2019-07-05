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
		$movie = Movie::with('thumbnail')->with('photos')->find($id);
		return view('movieupdate',['movie'=>$movie]);
	}
	public function update(Request $request, $id) {
		$movie = Movie::find($id);
		$movie->mID = $request->get('mid');
		$movie->title = $request->get('title');
		$movie->year = $request->get('year');
		$movie->director = $request->get('director');

		$validateData = $request->validate([
			'thumbnail_id' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
			,'photos[]' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

		
		try {

			// test if thumbnail is updated or not
			if($request->hasFile('thumbnail_id')){
				$file = $request->file('thumbnail_id');
				$thumbnail = new Image();
				$thumbnail->file_name = rand(1111,9999).time().'.'.$file->getClientOriginalExtension();
				$thumbnail->location = 'images\thumbnail'; 
				
				$file->move(public_path($thumbnail->location),$thumbnail->file_name);
				$thumbnail->save();//save new thumbnail


				$old_thumbnail = $movie->thumbnail; // Keep the old thumbnail for removing if it exists
				$movie->thumbnail_id = $thumbnail->image_id;	//change the thumbnail to the new one


			}



			$movie->save(); //save the update of movie
			
			if(isset($old_thumbnail)){
				//remove old thumbnail from harddisk
				$file = public_path($movie->thumbnail->location).'\\'.$movie->thumbnail->file_name;
				if ( File::exists($file)) {
					File::delete($file);
				}

				$movie->thumbnail->delete(); //delete the old thumbnail if user add a new one
			}



			$db_old_photos = $movie->photos;//get old photos from db
			if($db_old_photos){// if there is any old photos in db
				$old_photos = $request->get('old_photos'); //get the list of old photos after use update
				

				foreach($db_old_photos as $db_old_photo){

					//test if user has deleted all old photos, we remove it from db and hard disk
					//or test if some old photos are deleted by user, we remove it form db and hard disk
					if (!$old_photos or ($old_photos && !in_array($db_old_photo->image_id, $old_photos))){

						if($db_old_photo->delete()){
							//remove old thumbnail from harddisk
							$file = public_path($db_old_photo->location).'\\'.$db_old_photo->file_name;
							if ( File::exists($file)) {
								File::delete($file);
							}
						}
					}
				}
			}


			

			//test if user has upload other photos or not
			if($request->hasFile('photos')){



				//get the array of photos
				$photos = $request->file('photos');



				foreach ($photos as $file) {
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

			//delete movie from database
			$res['movie'] = Movie::destroy($id);
			if ($thumbnail){
				$file = public_path($thumbnail->location).'\\'.$thumbnail->file_name;

			

				//test if the thumbnail file exists or not
				if ( File::exists($file)) {
					//delete the file from the folder
				   if(File::delete($file)){
				   		//delete the thumbnail of the movie from database;
						$res['thumbnail'] = $thumbnail->delete();
				   }
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
		$movies = Movie::select(['movie.mID', 'title', 'director', 'year', 'avgstars', 'temp1.file_name', 'temp1.location'])
		->leftJoin(DB::raw('(select rating.mID as movie, avg(stars) as avgstars from rating group by movie) as temp'), 'temp.movie','movie.mID')
		//->leftJoin('image', 'image.image_id','=', 'movie.thumbnail_id')
		->leftJoin(DB::raw('(select image_id, location, file_name from image) as temp1'), 'temp1.image_id', 'movie.thumbnail_id')
        ;

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

		$thumbnail = Movie::find($mid)->thumbnail;
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

            $source = $source = asset(str_replace('\\','/',$thumbnail->location)) . "/" . $thumbnail->file_name;
            //add thumbnail to the list too
            $html .="<a href='" . $source . "'  ><img src='" . $source ."' height='40' width='40' ></a>";

            //list of photos must be in the div with id lightgallery, so in view we can apply the lightgallery library on it
            $html = "<div id='lightgallery'>" . $html . "</div>";
			return [1, $html];
		}else
			return [0];
	}

	public function home(){

		$movies = Movie::getMoviesWithThumbnail();
		return view('frontend.index',['movies'=>$movies]);
	}

	public function getmoviemore(Request $request){

		$movies = Movie::getMoviesWithThumbnail($request->get('offset'));

		if(sizeof($movies) > 0){
			$items = array();
			
			foreach ($movies as $movie){
				$html = "";
				$html .= <<<eot
				<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women">
					<!-- Block2 -->
					<div class="block2">
						<div class="block2-pic hov-img0">
eot;
							if($movie->file_name){
								$location = asset($movie->location);
								$html .= <<<eot
							
							<img src="$location/$movie->file_name" alt="IMG-PRODUCT">
eot;
							}else{
								$location = asset('images/thumbnail');
								$html .= <<<eot

							<img src="$location/default.png" alt="IMG-PRODUCT">
eot;
							}
							$location = asset('cozastore');
							$html .= <<<eot
							<a href="javascript:void(0);" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1" data-mid="$movie->mID">
								Quick View
							</a>
						</div>

						<div class="block2-txt flex-w flex-t p-t-14">
							<div class="block2-txt-child1 flex-col-l ">
								<a href="product-detail.html" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
									$movie->title
								</a>

								<span class="stext-105 cl3">
									$movie->year
								</span>

								<span class="stext-105 cl3">
									$movie->director
								</span>
							</div>

 							<div class="block2-txt-child2 flex-r p-t-3">
								<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
									<img class="icon-heart1 dis-block trans-04" src="$location/images/icons/icon-heart-01.png" alt="ICON">
									<img class="icon-heart2 dis-block trans-04 ab-t-l" src="$location/images/icons/icon-heart-02.png" alt="ICON">
								</a>
							</div>
 						</div>
					</div>
				</div>
eot;
				$items[] = $html;
			}
				

			//return [1,$html];
			return [1,$items];
		}
		else
			return [0];
	}

	public function getmoviedetail(Request $request){
		$movie = Movie::with('photos')->with('thumbnail')->find($request->get('mid'));
		if(isset($movie->thumbnail)){
			$location = asset(str_replace('\\','/',$movie->thumbnail->location));
			$movie->thumbnail->location = $location;
		}else{
			$movie->thumbnail_id = asset('images/thumbnail').'/default.png';
		}

		if(isset($movie->photos)){
			$size = sizeof($movie->photos);
			for($i = 0 ; $i < $size; $i ++){
				$location = asset(str_replace('\\','/',$movie->photos[$i]->location));
				$movie->photos[$i]->location = $location;
			}
			
		}
		return [1,$movie];
	}
}
