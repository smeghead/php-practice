<?php

declare(strict_types=1);

namespace Smeghead\PhpPractice\CA01;

final class Cell {
    public int $x;
    public int $y;
    public bool $isWall = false;
    public int $value = PHP_INT_MAX;
    public bool $fixed = false;
    public bool $isGoal = false;

    public function __construct(int $x, int $y, string $mark) {
        $this->x = $x;
        $this->y = $y;
        switch ($mark) {
            case 'A':
                $this->value = 0;
                break;
            case 'B':
                $this->isGoal = true;
                break;
            case '.':
                break;
            case '#':
                $this->isWall = true;
                break;
            default:
                throw new \Exception('unknown mark.');                        
        }
    }
}
