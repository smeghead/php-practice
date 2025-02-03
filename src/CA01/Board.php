<?php

declare(strict_types=1);

namespace Smeghead\PhpPractice\CA01;

final class Board {
    /** @var list<list<Cell>> */
    private array $matrix = [];
    private int $xLength;
    private int $yLength;

    /**
     * @param list<string> $lines
     */
    public function __construct(array $lines) {
        foreach ($lines as $y => $line) {
            $row = [];
            foreach (str_split($line) as $x => $char) {
                $row[] = new Cell($x, $y, $char);
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
        if ($x > $this->xLength) {
            return $wall;
        }
        if ($y < 0) {
            return $wall;
        }
        if ($y > $this->yLength) {
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
    }

    public function setFixed(int $x, int $y): void {
        $cell = $this->getCell($x, $y);
        if ($cell->isWall) {
            return;
        }
        $this->getCell($x, $y)->fixed = true;
    }

    public function getUnfixedSmallestCell(): ?Cell {
        $cell = new Cell(-1, -1, '.');
        $cell->value = PHP_INT_MAX;
        foreach (range(0, $this->xLength - 1) as $x) {
            foreach (range(0, $this->yLength - 1) as $y) {
                $target = $this->getCell($x, $y);
                if ($target->isWall) {
                    continue;
                }
                if ($target->fixed) {
                    continue;
                }
                if ($cell->value > $target->value) {
                    $cell = $target;
                }
            }
        }
        if ($cell->value === PHP_INT_MAX) {
            return null;
        }
        return $cell;
    }
}
