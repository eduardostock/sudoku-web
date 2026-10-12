<?php
require_once 'generate.php';

$difficulty = $_GET['dificuldade'] ?? 'medium';
list($board, $solution) = generateRandomSudoku($difficulty);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Sudoku</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <h1>Sudoku</h1>
    <p id="remaining">Restam 0 números a preencher</p>
    <div id="numberCounts">
        <!-- Preenchido dinamicamente pelo JavaScript -->
    </div>
    <div class="main">
        <table id="sudoku">
            <?php for ($i = 0; $i < 9; $i++): ?>
                <tr>
                    <?php for ($j = 0; $j < 9; $j++): ?>
                        <?php
                        $val = $board[$i][$j];
                        $solutionVal = $solution[$i][$j];
                        $isFixed = $val !== null;
                        ?>
                        <td>
                            <input
                                type="text"
                                maxlength="1"
                                value="<?= $isFixed ? $val : '' ?>"
                                data-row="<?= $i ?>"
                                data-col="<?= $j ?>"
                                data-solution="<?= $solutionVal ?>"
                                class="<?= $isFixed ? 'fixed' : '' ?>"
                                <?= $isFixed ? 'readonly' : '' ?> />
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </table>
        <div class="content">
            <form method="GET" style="margin-bottom: 15px;">
                <label for="dificuldade">Dificuldade:</label>
                <select name="dificuldade" id="dificuldade" onchange="this.form.submit()">
                    <option value="easy" <?= $difficulty === 'easy' ? 'selected' : '' ?>>Fácil</option>
                    <option value="medium" <?= $difficulty === 'medium' ? 'selected' : '' ?>>Médio</option>
                    <option value="hard" <?= $difficulty === 'hard' ? 'selected' : '' ?>>Difícil</option>
                </select>
            </form>
            <p id="errors">Erros: 0</p>
            <button onclick="location.reload()">Novo Jogo</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>