<?php

namespace App\Tests\Service;

use App\Service\RankingService;
use PHPUnit\Framework\TestCase;

class RankingServiceTest extends TestCase
{
    private RankingService $service;

    protected function setUp(): void
    {
        $this->service = new RankingService();
    }

    private function names(array $result): array
    {
        return array_map(fn($t) => $t->name, $result);
    }

    public function testExample1(): void
    {
        $matches = [
            ["A" => 100, "B" => 55],
            ["B" => 100, "C" => 95],
            ["A" => 90, "C" => 85],
            ["B" => 80, "D" => 75],
            ["A" => 75, "D" => 80],
            ["C" => 60, "D" => 55],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["A", "B", "C", "D"], $this->names($result));
    }

    public function testExample2(): void
    {
        $matches = [
            ["A" => 100, "B" => 55],
            ["B" => 100, "C" => 85],
            ["A" => 90, "C" => 85],
            ["B" => 75, "D" => 80],
            ["A" => 120, "D" => 75],
            ["C" => 65, "D" => 55],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["A", "B", "C", "D"], $this->names($result));
    }

    public function testExample3(): void
    {
        $matches = [
            ["A" => 85, "B" => 90],
            ["B" => 100, "C" => 95],
            ["A" => 55, "C" => 100],
            ["B" => 75, "D" => 85],
            ["A" => 75, "D" => 120],
            ["C" => 65, "D" => 55],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["C", "D", "B", "A"], $this->names($result));
    }

    public function testExample4(): void
    {
        $matches = [
            ["A" => 85, "B" => 90],
            ["B" => 100, "C" => 90],
            ["A" => 55, "C" => 100],
            ["B" => 75, "D" => 85],
            ["A" => 75, "D" => 120],
            ["C" => 65, "D" => 55],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["B", "C", "D", "A"], $this->names($result));
    }

    public function testExample5(): void
    {
        $matches = [
            ["A" => 100, "B" => 55],
            ["B" => 110, "F" => 90],
            ["A" => 85, "C" => 90],
            ["C" => 55, "D" => 60],
            ["A" => 120, "D" => 75],
            ["C" => 90, "E" => 75],
            ["A" => 80, "E" => 100],
            ["C" => 105, "F" => 75],
            ["A" => 85, "F" => 80],
            ["D" => 70, "E" => 45],
            ["B" => 100, "C" => 95],
            ["D" => 65, "F" => 60],
            ["B" => 80, "D" => 75],
            ["E" => 75, "F" => 80],
            ["B" => 75, "E" => 80],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["A", "B", "D", "C", "E", "F"], $this->names($result));
    }

    public function testExample6(): void
    {
        $matches = [
            ["A" => 71, "B" => 65],
            ["B" => 95, "F" => 90],
            ["A" => 85, "C" => 86],
            ["C" => 95, "D" => 100],
            ["A" => 77, "D" => 75],
            ["C" => 82, "E" => 75],
            ["A" => 80, "E" => 86],
            ["C" => 105, "F" => 75],
            ["A" => 85, "F" => 80],
            ["D" => 68, "E" => 67],
            ["B" => 88, "C" => 87],
            ["D" => 65, "F" => 60],
            ["B" => 80, "D" => 75],
            ["E" => 80, "F" => 75],
            ["B" => 75, "E" => 76],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["C", "A", "B", "E", "D", "F"], $this->names($result));
    }

    public function testExample7(): void
    {
        $matches = [
            ["A" => 73, "B" => 71],
            ["B" => 95, "F" => 90],
            ["A" => 85, "C" => 86],
            ["C" => 95, "D" => 96],
            ["A" => 77, "D" => 75],
            ["C" => 82, "E" => 75],
            ["A" => 90, "E" => 96],
            ["C" => 105, "F" => 75],
            ["A" => 85, "F" => 80],
            ["D" => 68, "E" => 67],
            ["B" => 88, "C" => 87],
            ["D" => 80, "F" => 75],
            ["B" => 80, "D" => 79],
            ["E" => 80, "F" => 75],
            ["B" => 79, "E" => 80],
        ];

        $result = $this->service->calculate($matches);

        $this->assertEquals(["C", "B", "D", "E", "A", "F"], $this->names($result));
    }
}
