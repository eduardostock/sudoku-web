const inputs = document.querySelectorAll("#sudoku input");
let errorCount = 0;

function updateCounter() {
    let emptyCount = 0;
    inputs.forEach(input => {
        if (!input.classList.contains("fixed") && input.value.trim() === '') {
            emptyCount++;
        }
    });
    document.getElementById("remaining").textContent =
        `Restam ${emptyCount} números a preencher`;
    updateNumberCounts(); // chamada extra
}

function updateNumberCounts() {
    const solutionCounts = Array(9).fill(0); // Total necessário de cada número
    const placedCorrectly = Array(9).fill(0); // Já colocados corretamente

    inputs.forEach(input => {
        if (!input.classList.contains("fixed")) {
            const solutionVal = parseInt(input.dataset.solution);
            if (!isNaN(solutionVal)) {
                solutionCounts[solutionVal - 1]++;

                if (input.value === input.dataset.solution) {
                    placedCorrectly[solutionVal - 1]++;
                }
            }
        }
    });

    let html = '';
    let completed = true;

    for (let i = 1; i <= 9; i++) {
        const totalToPlace = solutionCounts[i - 1];
        const alreadyPlaced = placedCorrectly[i - 1];
        const remaining = totalToPlace - alreadyPlaced;

        if (remaining > 0) {
            html += `
                <div class="count-card">
                    <div class="number">${i}</div>
                    <div class="remaining">${remaining}</div>
                </div>
            `;
            completed = false;
        }
    }

    if (completed) {
        html += `
            <div class="game-complete">
                Parabéns! Você completou o Sudoku corretamente!
            </div>
        `;
    }

    document.getElementById("numberCounts").innerHTML = html;
}


function highlightBlock(row, col) {
    const blockRow = Math.floor(row / 3);
    const blockCol = Math.floor(col / 3);
    inputs.forEach(input => {
        const r = +input.dataset.row;
        const c = +input.dataset.col;
        const br = Math.floor(r / 3);
        const bc = Math.floor(c / 3);
        input.classList.toggle("block-highlight", br === blockRow && bc === blockCol);
    });
}

function highlightLineAndColumn(row, col) {
    inputs.forEach(input => {
        const r = +input.dataset.row;
        const c = +input.dataset.col;
        input.classList.toggle("line-highlight", r === row || c === col);
    });
}

function validateInput(currentInput) {
    const val = currentInput.value.trim();
    const row = +currentInput.dataset.row;
    const col = +currentInput.dataset.col;
    currentInput.classList.remove("invalid");

    if (!/^[1-9]$/.test(val)) {
        if (val !== '') currentInput.classList.add("invalid");
        return;
    }

    let hasConflict = false;
    inputs.forEach(input => {
        const r = +input.dataset.row;
        const c = +input.dataset.col;
        if (input !== currentInput && input.value === val) {
            if (r === row || c === col ||
                (Math.floor(r / 3) === Math.floor(row / 3) &&
                    Math.floor(c / 3) === Math.floor(col / 3))) {
                hasConflict = true;
            }
        }
    });

    if (hasConflict || val !== currentInput.dataset.solution) {
        currentInput.classList.add("invalid");
        currentInput.classList.remove("fixed");
        currentInput.removeAttribute("readonly"); // continua editável
        errorCount++;
        document.getElementById("errors").textContent = `Erros: ${errorCount}`;
    } else {
        currentInput.classList.add("fixed");
        currentInput.setAttribute("readonly", "true"); // bloqueia input
    }
}

function checkComplete() {
    return [...inputs].every(input =>
        input.value === input.dataset.solution && !input.classList.contains("invalid")
    );
}

inputs.forEach(input => {
    if (!input.classList.contains("fixed")) {
        input.addEventListener("keydown", e => {
            if (
                ["Backspace", "Delete", "Tab"].includes(e.key) || e.key.startsWith("Arrow")
            ) return;
            if (!/^[1-9]$/.test(e.key)) e.preventDefault();
        });

        input.addEventListener("input", () => {
            input.value = input.value.replace(/[^1-9]/g, '').substring(0, 1);
            validateInput(input);
            updateCounter();
        });
    }

    input.addEventListener("focus", () => {
        const row = +input.dataset.row;
        const col = +input.dataset.col;
        const value = input.value;

        highlightBlock(row, col);
        highlightLineAndColumn(row, col);

        // Destacar todos com o mesmo valor
        inputs.forEach(i => {
            if (value && i.value === value) {
                i.classList.add("same-value");
            }
        });
    });

    input.addEventListener("blur", () => {
        inputs.forEach(i => {
            i.classList.remove("block-highlight", "line-highlight", "same-value");
        });
    });
});

window.addEventListener("DOMContentLoaded", updateCounter);
