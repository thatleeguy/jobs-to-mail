<?php namespace JobApis\JobsToMail\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use JobApis\JobsToMail\Http\Requests\CreateUser;
use JobApis\JobsToMail\Repositories\Contracts\UserRepositoryInterface;

class UsersController extends BaseController
{
    use ValidatesRequests;

    /**
     * UsersController constructor.
     */
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * Home page and signup form
     */
    public function index()
    {
        return view('users.welcome');
    }

    /**
     * Create new User.
     */
    public function create(CreateUser $request)
    {
        $data = $request->only(array_keys($request->rules()));

        if ($user = $this->users->create($data)) {
            $request->session()->flash(
                'alert-success',
                'A confirmation email has been sent. 
                    Once confirmed, you will start receiving jobs within 24 hours.'
            );
        } else {
            $request->session()->flash(
                'alert-danger',
                'Something went wrong and your job search was not saved.
                    Please try again.'
            );
        }
        return redirect('/');
    }

    /**
     * Confirm user account
     */
    public function confirm(Request $request, $token)
    {
        if ($this->users->confirm($token)) {
            $request->session()->flash(
                'alert-success',
                'Your email address has been confirmed. 
                    Look for new jobs in your inbox tomorrow.'
            );
        } else {
            $request->session()->flash(
                'alert-danger',
                'That token is invalid or expired. Please create a new job search.'
            );
        }
        return redirect('/');
    }

    /**
     * Unsubscribe user account
     *
     * @return string Json of all users
     */
    public function unsubscribe(Request $request, $userId)
    {
        if ($this->users->unsubscribe($userId)) {
            $request->session()->flash(
                'alert-success',
                'Your job search has been cancelled. If you\'d like to create a new search, fill out the form below.'
            );
        } else {
            $request->session()->flash(
                'alert-danger',
                'We couldn\'t unsubscribe you. Please try again.'
            );
        }
        return redirect('/');
    }
}
