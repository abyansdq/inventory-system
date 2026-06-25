<?php
// tests/Unit/EoqServiceTest.php

namespace Tests\Unit;

use App\Services\EoqService;
use PHPUnit\Framework\TestCase;

class EoqServiceTest extends TestCase
{
    private EoqService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EoqService();
    }

    /** @test */
    public function it_calculates_eoq_correctly(): void
    {
        // D=1200, S=150000, H=500
        // EOQ = sqrt((2 × 1200 × 150000) / 500)
        //     = sqrt(360000000 / 500)
        //     = sqrt(720000)
        //     = 848.53
        $eoq = $this->service->calculateEOQ(1200, 150000, 500);

        $this->assertEqualsWithDelta(848.53, $eoq, 0.1);
    }

    /** @test */
    public function it_calculates_safety_stock_correctly(): void
    {
        // Max=12, Avg=8, LT=3
        // SS = (12 - 8) × 3 = 12
        $ss = $this->service->calculateSafetyStock(12, 8, 3);

        $this->assertEquals(12, $ss);
    }

    /** @test */
    public function it_calculates_rop_correctly(): void
    {
        // d=8, L=3, SS=12
        // ROP = (8 × 3) + 12 = 36
        $rop = $this->service->calculateROP(8, 3, 12);

        $this->assertEquals(36, $rop);
    }

    /** @test */
    public function it_returns_zero_for_invalid_holding_cost(): void
    {
        $eoq = $this->service->calculateEOQ(1200, 150000, 0);

        $this->assertEquals(0, $eoq);
    }
}