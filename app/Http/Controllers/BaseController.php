<?php

namespace App\Http\Controllers;
class BaseController
{
    public function sendResponse($result, $message)
    {
        $response = [
//            'success' => true,
            'data' => $result,
//            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        return response()->json($response, $code);
    }
}
