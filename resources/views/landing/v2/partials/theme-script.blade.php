{{--
    Applies a theme picked with the toggle before anything paints, so the page doesn't flash the other one.
    Goes in the <head> of every v2 page: the Blade layout and the Management app's root view.
    The key matches THEME_KEY in resources/js/lib/theme.ts.
--}}
<script>
    try {
        const theme = localStorage.getItem('v2-theme');
        if (theme === 'light' || theme === 'dark') document.documentElement.dataset.theme = theme;
    } catch (e) {}
</script>
