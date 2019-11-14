<?php

namespace App\Http\Controllers;

use App\Consumer as Consumer;
use App\ConsumptionRecord as Record;
use App\Magic;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            $money = Auth::user()->only('money');
            $response = Magic::leftjoin('consumption_records', function ($join) {
                $join->on('magics.id', '=', 'consumption_records.magic_id')
                    ->where('consumer_id', '=', Auth::user()->id);
            })->select('magics.id', 'magics.magic_name', 'magics.price', 'magics.level', 'magics.photo',
                'consumption_records.magic_id', 'consumption_records.amount')
                ->get();
            $response['money'] = $money;

            return response()->json($response);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }


    function buy(Request $request)
    {
        try {
            $money = Auth::user()->money;
            $magic = new Magic;
            $magic_id = $magic->getID($request['m_id']);
            $magic_name = $magic->getMagicName($request['m_id']);
            $level = $magic->getLevel($request['m_id']);
            $magic_price = $magic->getPrice($magic_id);
            $money -= $magic_price;
            if ($money > 0) {
                $create = Record::create([
                    'consumer_id' => Auth::user()->id,
                    'magic_id' => $magic_id,
                    'amount' => $magic_price,
                    'magic_name' => $magic_name,
                    'level' => $level,
                ]);
                Auth::user()->update(['money' => $money]);
                $result = $create->toArray();
                $result['message'] = "Magic $magic_name $$magic_price bought successfully.";
                if ($create)
                    return $this->sendResponse($result, 200);
            }else {
                return $this->sendError("Your money is not enough.", 400);
            }

        } catch (Exception $error){
            if (strpos($error->getMessage(), '23000') !== false) {
                $message = "You've bought the item！";
                return $this->sendError($message, 400);
            }

            return $this->sendError("Magic item not found.", 400);
        }


    }

    function wholesale(Request $request)
    {
        $magics = Magic::whereIn('id', $request->magics)
            ->select('id', 'magic_name', 'price', 'level')->get();
        if ($request->consumer->money > $magics->sum('price')) {
            $request->consumer->money -= $magics->sum('price');
        }
    }

    function register(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'unique:consumers'],
                'password' => ['required', 'string', 'min:4', 'max:12'],
            ]);
            $hashedPassword = Hash::make($request['password']);
            $create = Consumer::create([
                'name' => $request['name'],
                'password' => $hashedPassword,
                'api_token' => null,
                'money' => 2000
            ]);
            if ($create) {
                return response()->json("Register as a consumer.");
            }
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), 400);
        }

    }

    public function login(Request $request)
    {
        try {
            $consumer = new Consumer;
            $hashedPassword = $consumer->getPassword($request['name']);
            $token = Str::random(10);
            $consumer = $consumer->getConsumer($request['name']);
            if (Hash::check($request['password'], $hashedPassword)) {
                if ($consumer->update(['api_token' => $token])) { //update api_token
                    $response = $consumer;
                    $response['password'] = $request['password'];
                    return response()->json($response);
                }
            }else{
                return $this->sendError("Wrong password！", 400);
            }

        } catch (Exception $error) {
            if (strpos($error->getMessage(), 'non-object') !== false) {
                $message = "Consumer name not found！";
                return $this->sendError($message, 400);
            }
            return $this->sendError($error->getMessage(), 400);
        }

    }

    public function logout(Request $request)
    {
        try {
            $consumer = new Consumer;
            $consumer = $consumer->getConsumer($request['name']);
            if ($consumer->update(['api_token' => null])) { //update api_token
                $response = "You've logged out.";
                return response()->json($response);
            }

        } catch (Exception $error) {
            if (strpos($error->getMessage(), 'non-object') !== false) {
                $message = "Consumer name not found！";
                return $this->sendError($message, 400);
            }
            return $this->sendError($error->getMessage(), 400);
        }

    }


}
