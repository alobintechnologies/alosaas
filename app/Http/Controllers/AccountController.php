<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Account;
use AccountUtil;
use App\Membership;
use Validator;

class AccountController extends Controller
{

    public function create()
    {
      return view('account.register');
    }

    /**
     * @param Illuminate\Http\Request
     */
    public function store(Request $request)
    {
      $datas = $request->all();
      $user = $request->user();
      $validator = Validator::make($datas, [
        'subdomain' => 'required|max:100|unique:accounts'
      ]);

      if($validator->fails()) {
        return back()
                  ->withErrors($validator)
                  ->withInput();
      } else {
        $account = new Account;
        $account->subdomain = $datas['subdomain'];
        $account->save();

        $membership = new Membership;
        $membership->user_id = $user->id;
        $membership->role = 'owner';

        $account->memberships()->save($membership);
        $request->session()->flash('success', 'Account created successfully...');
      }

      return redirect('dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request)
    {
      $user = $request->user();
      $memberships = $user->memberships()->with('account')->get();
      return view('account.change')->with('memberships', $memberships);
    }

    public function switchAccount($subdomain, Request $request)
    {
      $user = $request->user();
      $currentAccount = Account::where('subdomain', $subdomain)->first();
      $membership = $user->memberships()->where('account_id', $currentAccount->id)->first();

      if($membership) {
        AccountUtil::current($currentAccount);
        return redirect('dashboard');
      }
      return redirect('/');
    }

    public function dashboard(Request $request)
    {
      //$request->session()->put('currentAccount', $account);
      $currentAccount = AccountUtil::current();
      return view('account.dashboard')->with('currentAccount', $currentAccount);
    }

}
