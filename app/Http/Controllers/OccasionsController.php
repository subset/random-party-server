<?php namespace App\Http\Controllers;

use App\Models\Occasion;
use Illuminate\Http\Request;
use Illuminate\Support\Debug\Dumper;

class OccasionsController extends Controller
{
	public function index(Request $request)
	{
		$occasions = Occasion::where('month', '=', 11)->where('day', '=', 5)->get()->sortBy('prominence', SORT_REGULAR, true);
		return response()->json($occasions->take(10));
	}
}
