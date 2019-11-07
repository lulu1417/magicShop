<?php

namespace App\Http\Controllers;

use App\Magic;
use App\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Str;

class ShopController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'unique:owners'],
                'password' => ['required', 'string', 'min:3', 'max:12'],
            ]);
            $token = Str::random(10);
            $create = Owner::create([
                'name' => $request['name'],
                'password' => $request['password'],
                'api_token' => $token,
            ]);
            if ($create) {
                return response()->json("Register as a shop owner, your Token is $token.");
            }

        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

    }

    public function login(Request $request)
    {
        $owner = Owner::where('name', $request->name)->where('password', $request->password)->first();
        $token = Str::random(10);
        if ($owner) {
            if ($owner->update(['api_token' => $token])) { //update api_token
                return "login as a shop owner, your api token is $token";
            }
        } else return "Wrong email or password！";
    }


    public function index()
    {
        return Magic::all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public
    function store(Request $request)
    {

        try {
            if (Auth::user()) {
                $request->validate([
                    'magic_name' => ['required', 'unique:magics'],
                    'level' => ['required', 'numeric'],
                    'price' => ['required', 'numeric', 'max:100000'],
                ]);

                $create = Magic::create([
                    'magic_name' => $request['magic_name'],
                    'level' => $request['level'],
                    'price' => $request['price'],
                ]);
                $result = $create->toArray();
                $message = "Magic create successfully！";
                if ($create) {
                    return $this->sendResponse($result, $message);
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $magic = Magic::where('id', $id);
        try {
            if (Auth::user()) {
                $request->validate([
                    'magic_name' => ['required'],
                    'level' => ['required', 'numeric'],
                    'price' => ['required', 'numeric'],
                ]);
                $result = $request->toArray();
                if ($magic->update($request->all())) {
                    return $this->sendResponse($result, 'Magic updated successfully.');
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $magic = Magic::find($id);
        try {
            if ($magic->delete()) {
                return response()->json("Magic item $id delete successfully.");
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
