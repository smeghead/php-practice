<?php

declare(strict_types=1);

namespace Smeghead\PhpPractice\CA01;

final class Board {
    /** @var list<list<Cell>> */
    private array $matrix = [];
    private int $xLength;
    private int $yLength;

    private array $unfixedCells = [];

    /**
     * @param list<string> $lines
     */
    public function __construct(array $lines) {
        foreach ($lines as $y => $line) {
            $row = [];
            foreach (str_split($line) as $x => $char) {
                $row[] = new Cell($x, $y, $char);
                if ($char === 'A') {
                    $this->unfixedCells[sprintf('%d-%d', $x, $y)] = 0;
                }
            }
            $this->matrix[] = $row;
        }
        $this->xLength = count($this->matrix[0]);
        $this->yLength = count($this->matrix);
    }

    public function getCell(int $x, int $y): Cell {
        $wall = new Cell($x, $y, '#');
        if ($x < 0) {
            return $wall;
        }
        if ($x >= $this->xLength) {
            return $wall;
        }
        if ($y < 0) {
            return $wall;
        }
        if ($y >= $this->yLength) {
            return $wall;
        }
        return $this->matrix[$y][$x];
    }

    /**
     * @return list<Cell>
     */
    public function getNeighborhoodCells(int $x, int $y): array {
        $cell = $this->getCell($x, $y);
        if ($cell->isWall) {
            return [];
        }
        $neighbors = [
            $this->getCell($x + 1, $y),
            $this->getCell($x - 1, $y),
            $this->getCell($x, $y + 1),
            $this->getCell($x, $y - 1),
        ];
        return array_values(array_filter($neighbors, fn(Cell $c) => ! $c->isWall));
    }

    public function setValue(int $x, int $y, int $value): void {
        $cell = $this->getCell($x, $y);
        if ($cell->isWall) {
            return;
        }
        $this->getCell($x, $y)->value = $value;
        $this->unfixedCells[sprintf('%d-%d', $x, $y)] = $value;
    }

    public function setFixed(int $x, int $y): void {
        $cell = $this->getCell($x, $y);
        if ($cell->isWall) {
            return;
        }
        $this->getCell($x, $y)->fixed = true;
        unset($this->unfixedCells[sprintf('%d-%d', $x, $y)]);
    }

    public function getUnfixedSmallestCell(): ?Cell {
        asort($this->unfixedCells);
        if (count($this->unfixedCells) === 0) {
            return null;
        }
        $key = array_keys($this->unfixedCells)[0];
        if (preg_match('/([0-9]+)-([0-9]+)/', $key, $matches)) {
            return $this->getCell(intval($matches[1]), intval($matches[2]));
        }
        return null;
    }

    private function searchCell(Callable $fn): ?Cell {
        foreach (range(0, $this->xLength - 1) as $x) {
            foreach (range(0, $this->yLength - 1) as $y) {
                $target = $this->getCell($x, $y);
                if ($fn($target)) {
                    return $target;
                }
            }
        }
        return null;
    }

    public function spreadGoal(): void {
        $goal = $this->searchCell(fn($c) => $c->isGoal);

        $moveFns = [
            fn($c) => $this->getCell($c->x + 1, $c->y),
            fn($c) => $this->getCell($c->x - 1, $c->y),
            fn($c) => $this->getCell($c->x, $c->y + 1),
            fn($c) => $this->getCell($c->x, $c->y - 1),
        ];
        foreach ($moveFns as $fn) {
            $cell = $goal;
            while ($cell->isWall === false) {
                $cell->isGoal = true;
                $cell = $fn($cell);
            }
        }        
    }

    public function seek(): ?int {
        $smallest = $this->getUnfixedSmallestCell();
        if ($smallest == null) {
            return -1;
        }
        $this->setFixed($smallest->x, $smallest->y);
        if ($smallest->isGoal) {
            return $smallest->value;
        }

        $neighbors = $this->getNeighborhoodCells($smallest->x, $smallest->y);
        foreach ($neighbors as $n) {
            if ($n->value > $smallest->value + 1) {
                $this->setValue($n->x, $n->y, $smallest->value + 1);
                if ($n->isGoal) {
                    return $n->value;
                }
            }
        }
        return null;
    }

    public function start(): int {
        $this->spreadGoal();

        while (true) {
            $answer = $this->seek();
            if ($answer !== null) {
                return $answer;
            }
        }
        return -1;
    }
}
