<?php namespace App\Http\Controllers;

use App\Models\Occasion;
use Illuminate\Http\Request;
use Illuminate\Support\Debug\Dumper;

class OccasionsController extends Controller
{
	public function index(Request $request)
	{
		$occasions = Occasion::all()->sortBy('prominence', SORT_REGULAR, true);
		return response()->json($occasions->take(100));
	}
}
