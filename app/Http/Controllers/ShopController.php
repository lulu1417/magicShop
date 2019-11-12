<?php

namespace App\Http\Controllers;

use App\Magic;
use App\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            $hashedPassword = Hash::make($request['password']);

            $create = Owner::create([
                'name' => $request['name'],
                'password' => $hashedPassword,
                'api_token' => null,
            ]);
            if ($create) {
                return response()->json("Register as a shop owner.");
            }

        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

    }

    public function login(Request $request)
    {
        try {
            $owner = new Owner;
            $hashedPassword = $owner->getPassword($request['name']);
            $token = Str::random(10);
            $owner = $owner->getOwner($request['name']);
            if (Hash::check($request['password'], $hashedPassword)) {
                if ($owner->update(['api_token' => $token])) { //update api_token
                    $response = [
                        'name' => $request->name,
                        'password' => $request->password,
                        'api_token' => $token,
                    ];
                    return response()->json($response);
                }
            }
            return $this->sendError("Wrong password or name", 400);

        } catch
        (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    public function index()
    {
        try {
            if (Magic::all())
                return Magic::all();
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

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
                    'photo' => ['sometimes', 'mimes:jpg,jpeg,bmp,png'],
                ]);

                $create = Magic::create([
                    'magic_name' => $request['magic_name'],
                    'level' => $request['level'],
                    'price' => $request['price'],
                    'photo' => $request['photo'],
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
    public
    function update(Request $request, $id)
    {
        try {
            $magic = Magic::find($id);
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

        try {
            $magic = Magic::find($id);
            if ($magic->delete()) {
                return response()->json("Magic item $id delete successfully.");
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
