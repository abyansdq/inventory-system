<?php
// app/Exceptions/InsufficientDataException.php

namespace App\Exceptions;

use Exception;

class InsufficientDataException extends Exception
{
    protected int    $required;
    protected int    $available;
    protected string $context;

    public function __construct(
        string $context   = '',
        int    $required  = 0,
        int    $available = 0,
        string $message   = ''
    ) {
        $this->context   = $context;
        $this->required  = $required;
        $this->available = $available;

        $message = $message ?: "Data tidak mencukupi untuk {$context}. "
            . "Dibutuhkan minimal {$required} data, tersedia {$available}.";

        parent::__construct($message, 422);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message'   => $this->getMessage(),
                'context'   => $this->context,
                'required'  => $this->required,
                'available' => $this->available,
            ], 422);
        }

        return back()->withErrors(['data' => $this->getMessage()]);
    }
}