<?php

namespace App\Service;

use App\Dto\TeamStats;

class RankingService
{
    public function calculate(array $matches): array
    {
        $teams = $this->buildStats($matches);

        return $this->resolveTie(array_values($teams), $matches);
    }

    private function buildStats(array $matches): array
    {
        $teams = [];

        foreach ($matches as $match) {
            $names = array_keys($match);
            [$a, $b] = $names;

            $scoreA = $match[$a];
            $scoreB = $match[$b];

            $teams[$a] ??= new TeamStats();
            $teams[$b] ??= new TeamStats();

            $teams[$a]->name = $a;
            $teams[$b]->name = $b;

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

    private function resolveTie(array $teams, array $matches): array
    {
        usort($teams, fn($a, $b) => $b->points <=> $a->points);

        $result = [];
        $groups = $this->groupByPoints($teams);

        foreach ($groups as $group) {
            if (count($group) === 1) {
                $result[] = $group[0];
                continue;
            }

            $mini = $this->miniTable($group, $matches);

            usort($mini, function ($a, $b) {
                return
                    ($b->points <=> $a->points)
                        ?: ($b->diff() <=> $a->diff())
                        ?: ($b->scored <=> $a->scored);
            });

            $subGroups = $this->groupByCriteria($mini);

            foreach ($subGroups as $sub) {
                if (count($sub) > 1) {
                    usort($sub, fn($a, $b) => ($b->diff() <=> $a->diff())
                        ?: ($b->scored <=> $a->scored)
                    );
                }

                foreach ($sub as $team) {
                    $result[] = $team;
                }
            }
        }

        return $result;
    }

    private function miniTable(array $teams, array $matches): array
    {
        $names = array_map(fn($t) => $t->name, $teams);
        $stats = [];

        foreach ($teams as $t) {
            $stats[$t->name] = new TeamStats();
            $stats[$t->name]->name = $t->name;
        }

        foreach ($matches as $match) {
            $keys = array_keys($match);
            [$a, $b] = $keys;

            if (!in_array($a, $names) || !in_array($b, $names)) {
                continue;
            }

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

    private function groupByPoints(array $teams): array
    {
        $groups = [];

        foreach ($teams as $team) {
            $groups[$team->points][] = $team;
        }

        return array_values($groups);
    }

    private function groupByCriteria(array $teams): array
    {
        $groups = [];

        foreach ($teams as $t) {
            $key = $t->points . '_' . $t->diff() . '_' . $t->scored;
            $groups[$key][] = $t;
        }

        return array_values($groups);
    }
}
