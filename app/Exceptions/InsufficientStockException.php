<?php
// app/Exceptions/InsufficientStockException.php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected string $itemName;
    protected int    $requested;
    protected int    $available;

    public function __construct(
        string $itemName,
        int    $requested,
        int    $available,
        string $message = ''
    ) {
        $this->itemName  = $itemName;
        $this->requested = $requested;
        $this->available = $available;

        $message = $message ?: "Stok tidak mencukupi untuk barang '{$itemName}'. "
            . "Diminta: {$requested}, Tersedia: {$available}.";

        parent::__construct($message, 422);
    }

    public function getItemName(): string  { return $this->itemName; }
    public function getRequested(): int    { return $this->requested; }
    public function getAvailable(): int    { return $this->available; }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message'   => $this->getMessage(),
                'item'      => $this->itemName,
                'requested' => $this->requested,
                'available' => $this->available,
            ], 422);
        }

        return back()->withErrors(['stock' => $this->getMessage()]);
    }
}