<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Feedback;
use App\NotificationType;
use App\NotificationSound;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $widgets = $this->__widgets();
        return view('home', compact('widgets'));
    }

    /**
     * This will render all the widgets
     */
    private function __widgets()
    {
        return [
            'users' => User::count('id'),
            'feedbacks' => Feedback::count('id'),
            'notificationTypes' => NotificationType::count('id'),
            'notificationSounds' => NotificationSound::count('id'),
        ];
    }
}
