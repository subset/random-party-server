<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
	public function index(Request $request)
	{
		return response()->json([
			'meta' => [
				'hello' => 'Random Party / Server',
				'version' => $_ENV['APP_VERSION']
			],
			'links' => [
				'self' => $request->url(),
				'occasions' => route('occasions.index')
			]
		]);
	}
}
