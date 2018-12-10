<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class UnprocessableEntityHttpException extends Exception
{
    public function __construct(string $message = "", int $code = 422)
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->message, 'status' => 'F'], $this->code);
        }

        return view('pages.error', ['msg' => $this->message]);
    }
}
