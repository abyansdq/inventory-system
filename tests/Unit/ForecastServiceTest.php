<?php
// tests/Unit/ForecastServiceTest.php

namespace Tests\Unit;

use App\Services\ForecastService;
use PHPUnit\Framework\TestCase;

class ForecastServiceTest extends TestCase
{
    private ForecastService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForecastService();
    }

    /** @test */
    public function it_calculates_wma_correctly(): void
    {
        // Data: [100, 120, 110], Bobot: [1, 2, 3]
        // WMA = (100×1 + 120×2 + 110×3) / (1+2+3)
        //     = (100 + 240 + 330) / 6
        //     = 670 / 6
        //     ≈ 111.67
        $result = $this->service->calculateWMA([100, 120, 110], [1, 2, 3]);

        $this->assertEqualsWithDelta(111.67, $result, 0.01);
    }

    /** @test */
    public function it_generates_correct_bobot_for_n3(): void
    {
        $bobot = $this->service->generateBobot(3);

        $this->assertEquals([1, 2, 3], $bobot);
    }

    /** @test */
    public function it_generates_correct_bobot_for_n4(): void
    {
        $bobot = $this->service->generateBobot(4);

        $this->assertEquals([1, 2, 3, 4], $bobot);
    }

    /** @test */
    public function it_gives_more_weight_to_recent_data(): void
    {
        // Data naik: [100, 150, 200]
        // WMA seharusnya lebih tinggi dari rata-rata biasa (150)
        $wma = $this->service->calculateWMA([100, 150, 200], [1, 2, 3]);
        $ma  = (100 + 150 + 200) / 3;

        $this->assertGreaterThan($ma, $wma);
    }

    /** @test */
    public function it_calculates_next_periode_correctly(): void
    {
        // Desember → Januari tahun depan
        $next = $this->service->getNextPeriodeFromYearMonth(2024, 12);

        $this->assertEquals(1, $next['bulan']);
        $this->assertEquals(2025, $next['tahun']);
    }

    /** @test */
    public function it_calculates_mid_year_periode(): void
    {
        // Juni → Juli
        $next = $this->service->getNextPeriodeFromYearMonth(2024, 6);

        $this->assertEquals(7, $next['bulan']);
        $this->assertEquals(2024, $next['tahun']);
    }
}