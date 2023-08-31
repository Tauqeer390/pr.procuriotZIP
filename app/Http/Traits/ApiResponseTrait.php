<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Validator;

trait ApiResponseTrait
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }


    public function validateRequest($request,$rules)
    {
         $res = Validator::make($request->all(), $rules);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $errors;
        }
        else{
            return true;
        }
    }
}
