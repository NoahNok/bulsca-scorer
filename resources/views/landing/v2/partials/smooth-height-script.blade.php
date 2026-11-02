{{--
    x-smooth-height: eases an element's height to fit its one child as that changes size (a table swapped
    for a longer one, a row of chips shown or hidden), instead of jumping. The Blade counterpart of the
    Management app's SmoothHeight. Styles are .v2-smooth-height in v2.css. Inline and before Alpine's
    deferred script, so the directive is registered before Alpine starts.
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('smooth-height', (el, _, { cleanup }) => {
            const inner = el.firstElementChild;
            if (!inner || !window.ResizeObserver) return;

            el.classList.add('v2-smooth-height');
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
            let height = null;

            const observer = new ResizeObserver(() => {
                const next = inner.getBoundingClientRect().height;
                if (next === height) return;

                // The first size isn't animated, and nothing is with reduced motion
                if (height !== null && !reduced.matches) el.classList.add('is-resizing');
                el.style.height = `${next}px`;
                height = next;
            });

            el.addEventListener('transitionend', (event) => {
                if (event.target === el && event.propertyName === 'height') el.classList.remove('is-resizing');
            });

            observer.observe(inner);
            cleanup(() => observer.disconnect());
        });
    });
</script>
