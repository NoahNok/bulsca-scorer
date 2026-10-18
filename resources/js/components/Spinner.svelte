<div class="spinner-wrap flex items-center justify-center">
    <div
        class="spinner-ring w-12 h-12 rounded-full flex items-center justify-center relative"
        aria-hidden="true"
    ></div>
</div>

<style>
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Spinner wrap (layout handled by Tailwind) */
    .spinner-wrap {
        transition:
            opacity 300ms ease,
            transform 300ms ease;
    }

    .spinner-ring::after {
        content: "";
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        border: 4px solid transparent;
        border-top-color: #3b82f6; /* blue-500 */
        border-left-color: #3b82f6;
        animation: spin 800ms linear infinite;
        box-sizing: border-box;
        display: block;
    }
</style>
