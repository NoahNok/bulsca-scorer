/*
 * The v2 light/dark theme (docs/v2-ui-style-guide.md, "Dark mode"). Shared by the Blade v2 layout's
 * Alpine toggle (through window.v2Theme, set in basic-app.js) and the Management app's ThemeToggle.
 * landing/v2/partials/theme-script.blade.php applies the saved theme before the first paint.
 */

export const THEME_KEY = "v2-theme";

/** Whether dark is showing: the forced theme if the toggle has been used, otherwise the OS setting. */
export function isDark(): boolean {
    const forced = document.documentElement.dataset.theme;
    return forced ? forced === "dark" : matchMedia("(prefers-color-scheme: dark)").matches;
}

/**
 * Switch to the other theme and remember it. The new theme is revealed in a circle growing out of
 * `origin` (the toggle), falling back to an instant switch. `onChange` gets the new value when it applies.
 */
export function flipTheme(origin: Element, onChange: (dark: boolean) => void = () => {}): void {
    const dark = !isDark();
    let applied = false;
    const apply = () => {
        if (applied) return;
        applied = true;
        const theme = dark ? "dark" : "light";
        document.documentElement.dataset.theme = theme;
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (e) {}
        onChange(dark);
    };

    if (!document.startViewTransition || matchMedia("(prefers-reduced-motion: reduce)").matches) {
        return apply();
    }

    // big enough to reach the far corner
    const box = origin.getBoundingClientRect();
    const x = box.left + box.width / 2;
    const y = box.top + box.height / 2;
    const r = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
    // If the browser never gets round to the swap (e.g. a hidden tab), make it anyway
    setTimeout(apply, 400);
    document.startViewTransition(apply).ready.then(() => {
        document.documentElement.animate(
            { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${r}px at ${x}px ${y}px)`] },
            { duration: 500, easing: "cubic-bezier(0.4, 0, 0.2, 1)", pseudoElement: "::view-transition-new(root)" },
        );
    });
}
