const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, 'translate.js');
const lines = fs.readFileSync(filePath, 'utf8').split(/\r?\n/);

console.log(`Total lines: ${lines.length}`);

// Target block to move: lines 1901 to 2359 (1-based) -> 1900 to 2358 (0-based)
const startLine = 1900;
const endLine = 2358;

// Verify start and end
console.log(`Line ${startLine + 1}: ${lines[startLine]}`); // Should be applyNow...
console.log(`Line ${endLine + 1}: ${lines[endLine]}`);     // Should be closing bracket of documents

// Extract lines
const extractedLines = lines.slice(startLine, endLine + 1);

// Indent check (optional, but good for cleanliness)
// The extracted lines seem to have indentation. I might need to adjust it if nesting level changes.
// Currently inside services -> crewing (3 levels deep? zh -> services -> crewing).
// Moving to zh -> crewing (2 levels deep).
// So I should de-indent by 2 spaces.
const adjustedLines = extractedLines.map(line => {
    if (line.startsWith('            ')) {
        return line.replace('            ', '          ');
    }
    return line;
});

// Construct new block
const newBlock = [
    '        // Crewing Page Detailed Translations',
    '        crewing: {',
    ...adjustedLines,
    '        },',
    ''
];

// Split original lines into parts
// Part 1: 0 to 1880 (inclusive) - before Services Highlights
// Part 2: New Block
// Part 3: 1881 to 1899 (inclusive) - Services Highlights start up to crewing description
// Part 4: Skip 1900 to 2358
// Part 5: 2359 to end

// Wait, startLine is 1900.
// Part 3 ends at 1899.

const insertIndex = 1880; // Insert before line 1881

const finalLines = [
    ...lines.slice(0, insertIndex),
    ...newBlock,
    ...lines.slice(insertIndex, startLine),
    ...lines.slice(endLine + 1)
];

console.log(`New total lines: ${finalLines.length}`);

fs.writeFileSync(filePath, finalLines.join('\n'));
console.log('Fixed translate.js structure.');
