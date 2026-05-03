<?php

namespace App\Dto;

class TeamStats
{
    public string $name;

    public int $points = 0;
    public int $scored = 0;
    public int $conceded = 0;

    public int $wins = 0;
    public int $losses = 0;

    public function diff(): int
    {
        return $this->scored - $this->conceded;
    }
}
