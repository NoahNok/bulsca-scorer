function analyze(elementRows) {
    let rows = [];
    let eRows = [];

    elementRows.forEach((row) => {
        let nrow = [];
        let eRow = [];
        let total = row.children.length;
        for (let i = 2; i < total - 4; i++) {
            if (row.children[i].hasAttribute("data-total-row")) {
                continue;
            }

            nrow.push(parseInt(row.children[i].innerHTML));
            eRow.push(row.children[i]);
        }
        rows.push(nrow);
        eRows.push(eRow);
    });
    console.log(rows);

    let totalCols = rows[0].length;
    let colMeans = [];
    let colSD = [];

    for (let i = 0; i < totalCols; i++) {
        let colTotal = 0;
        rows.forEach((r) => {
            colTotal += r[i];
        });
        let mean = colTotal / rows.length;
        colMeans.push(mean);

        let sdTotal = 0;

        rows.forEach((r) => {
            let colVal = r[i];
            let diff = colVal - mean;
            diff = diff * diff;
            sdTotal += diff;
        });

        colSD.push(Math.sqrt(sdTotal / rows.length));
    }

    //console.log(colMeans);
    //console.log(colSD);

    // Now lets compute a matrix with values representing how above or below to mean you were in terms of how many SD

    let matrix = [];
    let largestValue = 0;
    let smallestValue = 0;
    let colId = 0;

    rows.forEach((row) => {
        let matrixRow = [];
        colId = 0;
        row.forEach((value) => {
            let v = (value - colMeans[colId]) / colSD[colId];

            if (v > largestValue) largestValue = v;
            if (v < smallestValue) smallestValue = v;

            matrixRow.push(v);

            colId++;
        });
        matrix.push(matrixRow);
    });
    //console.log(matrix);

    // Now lets loop this matrix any apply colours to cells based on values, we will need to normalise

    let rowId = 0;
    let colIdd = 0;
    matrix.forEach((row) => {
        colIdd = 0;
        row.forEach((value) => {
            let norm =
                (value * 2 - smallestValue) / (largestValue - smallestValue);
            let colour = mixGR(norm);

            if (isNaN(norm)) colour = colorMixer([0, 255, 0], [255, 0, 0], 0.5);

            //console.log(norm);
            eRows[rowId][colIdd].style.backgroundColor = colour;
            colIdd++;
        });
        rowId++;
    });
}

function mixGR(amount) {
    return colorMixer([0, 255, 0], [255, 0, 0], amount);
}

//colorChannelA and colorChannelB are ints ranging from 0 to 255
function colorChannelMixer(colorChannelA, colorChannelB, amountToMix) {
    var channelA = colorChannelA * amountToMix;
    var channelB = colorChannelB * (1 - amountToMix);
    return parseInt(channelA + channelB);
}
//rgbA and rgbB are arrays, amountToMix ranges from 0.0 to 1.0
//example (red): rgbA = [255,0,0]
function colorMixer(rgbA, rgbB, amountToMix) {
    var r = colorChannelMixer(rgbA[0], rgbB[0], amountToMix);
    var g = colorChannelMixer(rgbA[1], rgbB[1], amountToMix);
    var b = colorChannelMixer(rgbA[2], rgbB[2], amountToMix);
    return "rgb(" + r + "," + g + "," + b + ")";
}
