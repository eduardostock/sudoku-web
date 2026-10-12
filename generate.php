<?php
function shuffleArray(&$arr)
{
    for ($i = count($arr) - 1; $i > 0; $i--) {
        $j = rand(0, $i);
        [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
    }
}

function isSafe(&$grid, $row, $col, $num)
{
    for ($i = 0; $i < 9; $i++) {
        if ($grid[$row][$i] === $num || $grid[$i][$col] === $num) return false;
    }
    $startRow = $row - $row % 3;
    $startCol = $col - $col % 3;
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            if ($grid[$startRow + $i][$startCol + $j] === $num) return false;
        }
    }
    return true;
}

function countSolutions(&$grid, &$count, $limit = 2)
{
    if ($count >= $limit) return;
    for ($row = 0; $row < 9; $row++) {
        for ($col = 0; $col < 9; $col++) {
            if ($grid[$row][$col] === null) {
                for ($num = 1; $num <= 9; $num++) {
                    if (isSafe($grid, $row, $col, $num)) {
                        $grid[$row][$col] = $num;
                        countSolutions($grid, $count, $limit);
                        $grid[$row][$col] = null;
                    }
                }
                return;
            }
        }
    }
    $count++;
}

function getBaseGrid()
{
    return [
        [1, 2, 3, 4, 5, 6, 7, 8, 9],
        [4, 5, 6, 7, 8, 9, 1, 2, 3],
        [7, 8, 9, 1, 2, 3, 4, 5, 6],
        [2, 3, 4, 5, 6, 7, 8, 9, 1],
        [5, 6, 7, 8, 9, 1, 2, 3, 4],
        [8, 9, 1, 2, 3, 4, 5, 6, 7],
        [3, 4, 5, 6, 7, 8, 9, 1, 2],
        [6, 7, 8, 9, 1, 2, 3, 4, 5],
        [9, 1, 2, 3, 4, 5, 6, 7, 8]
    ];
}

function shuffleRows(&$grid)
{
    for ($block = 0; $block < 9; $block += 3) {
        $rows = [$grid[$block], $grid[$block + 1], $grid[$block + 2]];
        shuffleArray($rows);
        $grid[$block] = $rows[0];
        $grid[$block + 1] = $rows[1];
        $grid[$block + 2] = $rows[2];
    }
}

function shuffleCols(&$grid)
{
    $transposed = array_map(null, ...$grid);
    for ($block = 0; $block < 9; $block += 3) {
        $cols = [$transposed[$block], $transposed[$block + 1], $transposed[$block + 2]];
        shuffleArray($cols);
        $transposed[$block] = $cols[0];
        $transposed[$block + 1] = $cols[1];
        $transposed[$block + 2] = $cols[2];
    }
    $grid = array_map(null, ...$transposed);
}

function shuffleNumbers(&$grid)
{
    $map = range(1, 9);
    shuffleArray($map);
    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            $grid[$i][$j] = $map[$grid[$i][$j] - 1];
        }
    }
}

function removeCellsWithUniqueSolution($grid, $removeCount = 40)
{
    $puzzle = $grid;
    $cells = [];
    for ($i = 0; $i < 81; $i++) $cells[] = [$i % 9, intdiv($i, 9)];
    shuffleArray($cells);
    $removed = 0;

    foreach ($cells as [$col, $row]) {
        if ($removed >= $removeCount) break;
        $backup = $puzzle[$row][$col];
        $puzzle[$row][$col] = null;

        $copy = $puzzle;
        $count = 0;
        countSolutions($copy, $count);

        if ($count !== 1) {
            $puzzle[$row][$col] = $backup;
        } else {
            $removed++;
        }
    }
    return $puzzle;
}

function generateRandomSudoku($difficulty = 'medium')
{
    $grid = getBaseGrid();
    shuffleRows($grid);
    shuffleCols($grid);
    shuffleNumbers($grid);

    // Define a quantidade de células a remover com base na dificuldade
    switch ($difficulty) {
        case 'easy':
            $removeCount = 30;
            break;
        case 'hard':
            $removeCount = 55;
            break;
        case 'medium':
        default:
            $removeCount = 40;
            break;
    }

    $puzzle = removeCellsWithUniqueSolution($grid, $removeCount);
    return [$puzzle, $grid];
}
