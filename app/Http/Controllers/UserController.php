<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Acconut;
use Validator;
use App\User;
use App\Membership;
use AccountUtil;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $memberships = AccountUtil::current()->memberships()->with('user')->paginate();
        return view('user.index')->with('memberships', $memberships);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
          'name' => 'required|max:255',
          'email' => 'required|email|max:255',//|unique:users',
          'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
          return back()
                    ->withErrors($validator)
                    ->withInput();
        } else {
          $user = User::where('email', $data['email'])->first();
          $new_user = false;
          if(!$user) {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            $new_user = true;
          }

          $membership = Membership::where('account_id', AccountUtil::current()->id)->where('user_id', $user->id)->first();
          if(!$membership) {
            $membership = new Membership;
            $membership->user_id = $user->id;
            $membership->role = 'member';
            AccountUtil::current()->memberships()->save($membership);
            if($new_user) {
              $request->session()->flash('success', 'User added successfully');
            } else {
              $request->session()->flash('success', "User joined successfully, $user->email must use their password to login into account.");
            }
          } else {
            $request->session()->flash('danger', "User $user->email already exists");
          }

          return redirect('users');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // TODO:get the profile of the user and show the recent history of user
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
