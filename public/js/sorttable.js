function Sorttable() {
    lastClicked = null;
    document.querySelectorAll("table[sortable]").forEach((table) => {
        let headers = table
            .querySelector("tr[data-sortable-row]")
            .querySelectorAll("th");
        headers.forEach((header, i) => {
            if (header.hasAttribute("data-sortable")) {
                header.addEventListener("click", () => {
                    let rev = header.hasAttribute("reverse")
                        ? header.getAttribute("reverse") == "true"
                        : true;

                    rev = Boolean(rev);

                    header.setAttribute("reverse", !rev);
                    sortTable(table, i, rev);

                    if (lastClicked && lastClicked != header) {
                        lastClicked.removeAttribute("reverse");
                    }
                    lastClicked = header;
                });
            }
        });
    });
}

function sortTable(table, col, reverse) {
    var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
        tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
        i;

    reverse = -(+reverse || -1);

    tr = tr.sort((a, b) => {
        // sort rows via integer contents
        if (a.cells[col].textContent.trim().match(/^[0-9]+$/)) {
            return (
                reverse *
                (a.cells[col].textContent.trim() -
                    b.cells[col].textContent.trim())
            );
        }

        // sort rows
        return (
            reverse * // `-1 *` if want opposite order
            a.cells[col].textContent
                .trim() // using `.textContent.trim()` for test
                .localeCompare(b.cells[col].textContent.trim())
        );
    });

    for (i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
}
