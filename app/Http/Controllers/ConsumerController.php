<?php

namespace App\Http\Controllers;

use App\Consumer as Consumer;
use App\ConsumptionRecord as Record;
use App\Magic;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ConsumerController extends BaseController
{

    function index()
    {
        /* select * from magics left join (
             select magic_id,consumer_id
                 from consumers join consumption_records on consumers.id = consumer_id
                     where consumers.id = '2'
        ) as T on magics.id = T.magic_id; */

        $response = Magic::leftjoin('consumption_records', function ($join) {
            $join->on('magics.id', '=', 'consumption_records.magic_id')
                ->where('consumer_id', '=', Auth::user()->id);
        })->select('magics.id', 'magics.magic_name', 'magics.price', 'magics.level', 'consumption_records.magic_id', 'consumption_records.consumer_id')
            ->get();
        return response()->json($response);

    }


    function buy(Request $request)
    {

        $consumer = Auth::user();
        $magic = new Magic;
        $magic_id = $magic->getID($request['m_id']);
        $magic_name = $magic->getMagicName($request['m_id']);
        if ($magic_id != null) {
            $magic_price = $magic->getPrice($magic_id);
            $consumer->money -= $magic_price;
            $consumer->update(['money' => $consumer->money]);
            $create = Record::create([
                'consumer_id' => $consumer['id'],
                'magic_id' => $magic_id,
                'amount' => $magic_price
            ]);
            $result = $create->toArray();
            $message = "Magic $magic_name $$magic_price bought successfully.";
            if ($create)
                return $this->sendResponse($result, $message);
        } else {
            return "Magic item not found.";
        }


    }

    function register(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'unique:consumers'],
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
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), 500);
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
