<?php namespace App\Http\Controllers;

use App\Models\Occasion;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OccasionsController extends Controller
{
	public function index(Request $request)
	{
		if (!$request->has('month') && !$request->has('day'))
		{
			abort(Response::HTTP_FORBIDDEN, 'Requests should always query for a specific day and month');
		}

		$occasions = Occasion
			::where('month', '=', $request->input('month'))
			->where('day', '=', $request->input('day'))
			->get()
			->sortBy('prominence', SORT_REGULAR, true)
		;
		return response()->json(['occasions' => $occasions]);
	}
}
