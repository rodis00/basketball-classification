<?php

namespace App\Service;

use App\Dto\TeamStats;

class RankingService
{
    public function calculate(array $matches): array
    {
        $teams = $this->buildStats($matches);

        usort($teams, fn($a, $b) => $b->points <=> $a->points);

        return $this->resolveGroups(array_values($teams), $matches);
    }

    private function buildStats(array $matches): array
    {
        $teams = [];

        foreach ($matches as $match) {
            $names = array_keys($match);
            [$a, $b] = $names;
            $scoreA = $match[$a];
            $scoreB = $match[$b];

            foreach ([$a, $b] as $name) {
                if (!isset($teams[$name])) {
                    $teams[$name] = new TeamStats();
                    $teams[$name]->name = $name;
                }
            }

            $teams[$a]->scored += $scoreA;
            $teams[$a]->conceded += $scoreB;
            $teams[$b]->scored += $scoreB;
            $teams[$b]->conceded += $scoreA;

            if ($scoreA > $scoreB) {
                $teams[$a]->points += 2;
                $teams[$a]->wins++;
                $teams[$b]->points += 1;
                $teams[$b]->losses++;
            } else {
                $teams[$b]->points += 2;
                $teams[$b]->wins++;
                $teams[$a]->points += 1;
                $teams[$a]->losses++;
            }
        }

        return $teams;
    }

    private function resolveGroups(array $teams, array $matches): array
    {
        $result = [];
        $grouped = [];

        foreach ($teams as $t) {
            $grouped[$t->points][] = $t;
        }

        krsort($grouped);

        foreach ($grouped as $subgroup) {
            if (count($subgroup) === 1) {
                $result[] = $subgroup[0];
            } else {
                $resolved = $this->resolveTie($subgroup, $matches);

                foreach ($resolved as $miniTeam) {
                    foreach ($subgroup as $originalTeam) {
                        if ($originalTeam->name === $miniTeam->name) {
                            $result[] = $originalTeam;
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function resolveTie(array $teams, array $matches): array
    {
        if (count($teams) <= 1)
            return $teams;

        $mini = $this->miniTable($teams, $matches);

        usort($mini, fn($a, $b) => $b->points <=> $a->points);
        $groups = $this->groupBy($mini, fn($t) => $t->points);

        if (count($groups) > 1)
            return $this->processSubgroups($groups, $matches);

        usort($mini, fn($a, $b) => $b->diff() <=> $a->diff());
        $groups = $this->groupBy($mini, fn($t) => $t->diff());

        if (count($groups) > 1)
            return $this->processSubgroups($groups, $matches);

        usort($mini, fn($a, $b) => $b->scored <=> $a->scored);
        $groups = $this->groupBy($mini, fn($t) => $t->scored);

        if (count($groups) > 1)
            return $this->processSubgroups($groups, $matches);

        shuffle($mini);

        return $mini;
    }

    private function processSubgroups(array $groups, array $matches): array
    {
        $result = [];

        foreach ($groups as $subgroup) {
            $resolved = $this->resolveTie($subgroup, $matches);
            foreach ($resolved as $t) $result[] = $t;
        }

        return $result;
    }

    private function miniTable(array $teams, array $matches): array
    {
        $names = array_map(fn($t) => $t->name, $teams);
        $stats = [];

        foreach ($names as $name) {
            $stats[$name] = new TeamStats();
            $stats[$name]->name = $name;
        }

        foreach ($matches as $match) {
            [$a, $b] = array_keys($match);

            if (!isset($stats[$a], $stats[$b])) continue;

            $scoreA = $match[$a];
            $scoreB = $match[$b];

            $stats[$a]->scored += $scoreA;
            $stats[$a]->conceded += $scoreB;
            $stats[$b]->scored += $scoreB;
            $stats[$b]->conceded += $scoreA;

            if ($scoreA > $scoreB) {
                $stats[$a]->points += 2;
                $stats[$b]->points += 1;
            } else {
                $stats[$b]->points += 2;
                $stats[$a]->points += 1;
            }
        }
        return array_values($stats);
    }

    private function groupBy(array $teams, callable $callback): array
    {
        $groups = [];

        foreach ($teams as $t) {
            $val = $callback($t);
            $groups["$val"][] = $t;
        }

        return array_values($groups);
    }
}
