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

        try {
            $response = Magic::leftjoin('consumption_records', function ($join) {
                $join->on('magics.id', '=', 'consumption_records.magic_id')
                    ->where('consumer_id', '=', Auth::user()->id);
            })->select('magics.id', 'magics.magic_name', 'magics.price', 'magics.level',
                'consumption_records.magic_id', 'consumption_records.consumer_id', 'consumption_records.amount')
                ->get();
            return response()->json($response);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    function buy(Request $request)
    {
        try {
            $consumer = Auth::user();
            $magic = new Magic;
            $magic_id = $magic->getID($request['m_id']);
            $magic_name = $magic->getMagicName($request['m_id']);
            $level = $magic->getLevel($request['m_id']);
            if ($magic_id != null) {
                $magic_price = $magic->getPrice($magic_id);
                $consumer->money -= $magic_price;
                if ($consumer->money > 0) {
                    $create = Record::create([
                        'consumer_id' => $consumer['id'],
                        'magic_id' => $magic_id,
                        'amount' => $magic_price,
                        'magic_name' => $magic_name,
                        'level' => $level,
                    ]);
                    $consumer->update(['money' => $consumer->money]);
                    $result = $create->toArray();
                    $message = "Magic $magic_name $$magic_price bought successfully.";
                    if ($create)
                        return $this->sendResponse($result, $message);
                } else {
                    return response()->json("Your money is not enough.");
                }

            } else {
                return response()->json("Magic item not found.");
            }
        } catch (Exception $error) {
            if (strpos($error->getMessage(), '23000') !== false) {
                $message = "You've bought the item！";
                return $this->sendError($error->getMessage(), $message);
            }
            return $this->sendError($error->getMessage(), 500);
        }


    }

    function register(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'unique:consumers'],
                'password' => ['required', 'string', 'min:4', 'max:12'],
            ]);
            $create = Consumer::create([
                'name' => $request['name'],
                'password' => $request['password'],
                'api_token' => null,
                'money' => 2000
            ]);
            if ($create) {
                return response()->json("Register as a consumer.");
            }
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), 500);
        }

    }

    public function login(Request $request)
    {
        try {
            $consumer = Consumer::where('name', $request->name)->where('password', $request->password)->first();
            $token = Str::random(10);
            if ($consumer) {
                if ($consumer->update(['api_token' => $token])) { //update api_token
                    $response = [
                        'name' => $request->name,
                        'password' => $request->password,
                        'api_token' => $token,
                    ];
                    return response()->json($response);
                }
            }

        } catch (Exception $e) {
            return $this->sendError("Wrong password or name", 400);
        }

    }


}
