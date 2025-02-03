<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Smeghead\PhpPractice\CA01\Board;

final class BoardTest extends TestCase {

    public function test盤の初期化(): void
    {
        $lines = [
            "A....",
            ".#.#.",
            "....B",
        ];

        $sut = new Board($lines);
        $this->assertSame(0, $sut->getCell(0, 0)->value);
        $this->assertSame(false, $sut->getCell(0, 0)->fixed);
        $this->assertSame(false, $sut->getCell(0, 0)->isGoal);
        $this->assertSame(true, $sut->getCell(4, 2)->isGoal);
    }

    public function test近隣のセルを取得する(): void
    {
        $lines = [
            "A....",
            ".#.#.",
            "....B",
        ];

        $sut = new Board($lines);
        $neighbors = $sut->getNeighborhoodCells(0, 0);

        $this->assertSame(2, count($neighbors));
        $this->assertSame(1, $neighbors[0]->x);
        $this->assertSame(0, $neighbors[0]->y);
        $this->assertSame(0, $neighbors[1]->x);
        $this->assertSame(1, $neighbors[1]->y);
    }

    public function testFixしていない最小のセルを取得する(): void
    {
        $lines = [
            "A....",
            ".#.#.",
            "....B",
        ];

        $sut = new Board($lines);
        $cell = $sut->getUnfixedSmallestCell();

        $this->assertSame(0, $cell->x);
        $this->assertSame(0, $cell->y);
    }

    public function testFixしていない最小のセルを取得する_2手目(): void
    {
        $lines = [
            "A....",
            ".#.#.",
            "....B",
        ];

        $sut = new Board($lines);
        $sut->setFixed(0, 0);
        $sut->setValue(1, 0, 1);
        $sut->setValue(0, 1, 1);
        $cell = $sut->getUnfixedSmallestCell();

        $this->assertSame(0, $cell->x);
        $this->assertSame(1, $cell->y);
    }

    public function testFixしていない最小のセルを取得する_3手目(): void
    {
        $lines = [
            "A....",
            ".#.#.",
            "....B",
        ];

        $sut = new Board($lines);
        $sut->setFixed(0, 0);
        $sut->setValue(1, 0, 1);
        $sut->setValue(0, 1, 1);
        $sut->setFixed(0, 1);
        $cell = $sut->getUnfixedSmallestCell();

        $this->assertSame(1, $cell->x);
        $this->assertSame(0, $cell->y);
    }

    // public function testSpreadGoal(): void
    // {
    //     $lines = [
    //         "A....",
    //         ".#.#.",
    //         ".#..B",
    //     ];

    //     $sut = new Board($lines);
    //     $sut->spreadGoal();

    //     $this->assertSame(true, $sut->getCell(4, 0)->isGoal);
    //     $this->assertSame(true, $sut->getCell(4, 1)->isGoal);
    //     $this->assertSame(true, $sut->getCell(4, 2)->isGoal);
    //     $this->assertSame(false, $sut->getCell(0, 2)->isGoal);
    //     $this->assertSame(false, $sut->getCell(1, 2)->isGoal);
    //     $this->assertSame(true, $sut->getCell(2, 2)->isGoal);
    //     $this->assertSame(true, $sut->getCell(3, 2)->isGoal);

    // }
}
