<?php
/**
 * Default Home Controller provided by Laravel
 * \class HomeController
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * \brief Default Home Controller provided by Laravel
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
}
