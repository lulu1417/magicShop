<?php

namespace App\Http\Controllers;

use App\Consumer;
use App\ConsumptionRecord as Record;
use App\Magic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class ConsumerController extends BaseController
{

    function index()
    {

    }

    function buy(Request $request)
    {


        $consumer = Auth::user();
        $consumer_id = $consumer->id;

        $magic = Magic::where('magic_name', $request->magic)->first();
        $magic_id = $magic->id;
        $magic_price = $magic->price;
        $consumer->money -= $magic_price;
        $consumer->update(['money' =>$consumer->money]);
        $create = Record::create([
            'consumer_id' => $consumer_id,
            'magic_id' => $magic_id,
        ]);
        $result = $create->toArray();
        $message = "Magic $request->magic $$magic_price bought successfully.";
        if ($create)
            return $this->sendResponse($result, $message);

    }

    function register(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'unique:owners'],
                'password' => ['required', 'string', 'min:3', 'max:12'],
            ]);
            $token = Str::random(10);
            $create = Consumer::create([
                'name' => $request['name'],
                'password' => $request['password'],
                'api_token' => $token,
                'money' => 2000
            ]);
            if ($create) {
                return "Register as a consumer, you got $2000, and your Token is $token.";
            }

        } catch (Exception $e) {
            $this->sendError($e, 'Registered failed.', 500);
        }

    }

    public function login(Request $request)
    {
        $consumer = Consumer::where('name', $request->name)->where('password', $request->password)->first();
        $token = Str::random(10);
        if ($consumer) {
            if ($consumer->update(['api_token' => $token])) { //update api_token
                return "login as a consumer, your api token is $token";
            }
        } else return "Wrong email or password！";
    }


}
