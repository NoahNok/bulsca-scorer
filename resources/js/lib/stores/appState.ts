// src/lib/stores/appState.ts
import { writable } from 'svelte/store';
import type { Competition } from '@/types/base';

export type AppState = {
    activeCompetition?: Competition;
};



function loadInitial(): AppState {
    try {
        const raw = localStorage.getItem('appState');
        return raw ? JSON.parse(raw) : { activeCompetition: undefined } as AppState;
    } catch {
        return {} as AppState;
    }
}

export const appState = writable<AppState>(loadInitial());


appState.subscribe((value) => {
    if (typeof localStorage !== 'undefined') {
        localStorage.setItem('appState', JSON.stringify(value));
    }
});
