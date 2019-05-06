<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\movie;
use App\reviewer;
use App\rating;
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

		try {
			$movie->save();
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
		echo 'show';
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
			$res = Movie::destroy($id);
			if ($res)
				return 1;
			else
				return 0;
		}catch(\Exception $e){
			return 0;
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
		$movies = Movie::select(['mID', 'title', 'director', 'year'])->get();

        return Datatables::of($movies)
        				->addColumn('action', function ($movie) {
        										$html = '<a href="'.route('movie.edit', ['id' => $movie->mID]).'" class="btn btn-primary btn-sm"><i class="far fa-edit"></i> Edit</a>&nbsp;&nbsp;&nbsp;';
        										$html .= '<a data-id="'.$movie->mID.'" class="btn btn-danger btn-sm movie-delete"><i class="far fa-trash-alt"></i></i> Delete</a>' ;
                								return $html;
            								})
        				->make(true);
	}

}
