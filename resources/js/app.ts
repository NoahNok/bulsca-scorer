
import { createInertiaApp } from "@inertiajs/svelte";
import JudgeLayout from "./layout/JudgeLayout.svelte";
//import "./bootstrap";

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

const appName = "DigitalJudge";

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        return [JudgeLayout]
    },
})





