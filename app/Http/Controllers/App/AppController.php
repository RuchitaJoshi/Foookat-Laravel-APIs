<?php

namespace App\Http\Controllers\App;

use App\State;
use App\League;
use App\Version;
use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as IlluminateResponse;

use App\Http\Requests;

class AppController extends Controller
{

    /**
     * Handle a request to sync application data and user preferences.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSync()
    {
        $categories = Category::where('active', '=', 1)->select('id', 'name', 'order')->get();

        $states = State::with('cities')->get();

        $leagues = League::all();

        $versions = Version::all();

        return response()->json(['success' => TRUE, 'data' => ['categories' => $categories, 'states' => $states, 'storeTypes' => $leagues, 'versions' => $versions]], IlluminateResponse::HTTP_OK);
    }
}
