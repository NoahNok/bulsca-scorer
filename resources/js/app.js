
import "./bootstrap";

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();


function dynamicInput(input) {
    const mirror = document.createElement('div');
    mirror.style.position = 'absolute';
    mirror.style.top = '-9999px';
    mirror.style.left = '-9999px';
    mirror.style.whiteSpace = 'pre';
    mirror.style.visibility = 'hidden';

    document.body.appendChild(mirror);

    const updateWidth = () => {
        const style = getComputedStyle(input);
        mirror.style.font = style.font;
        mirror.style.padding = style.padding;
        // mirror.style.border = style.border;
        // mirror.style.letterSpacing = style.letterSpacing;
        // mirror.style.boxSizing = style.boxSizing;

        mirror.textContent = input.value || input.placeholder || '';
        input.style.width = `${mirror.offsetWidth + 3}px`; // Slight buffer
    };

    input.addEventListener('input', updateWidth);
    input.addEventListener('click', updateWidth)
    updateWidth(); // Initial sizing
}

window.dynamicInput = dynamicInput


