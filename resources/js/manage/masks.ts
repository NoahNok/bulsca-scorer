import type { MaskInputOptions, MaskTokens } from "maska";

/** mm:ss.hh with an optional thousandth, as times are typed and saved */
export const TIME_MASK = "99:59.99#";

export const TIME_TOKENS: MaskTokens = {
    "5": { pattern: /[0-5]/ },
    "9": { pattern: /[0-9]/ },
    "#": { pattern: /[0-9]/, optional: true },
};

/** A time and nothing else (a seed time), the colon and point filled in as it's typed */
export const timeMask: MaskInputOptions = { mask: TIME_MASK, tokens: TIME_TOKENS, eager: true };
