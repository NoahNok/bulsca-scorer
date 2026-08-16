// src/lib/stores/appState.ts
import { writable } from 'svelte/store';
import type { Competition } from '@/types/base';


export enum AccessRole {
    Official = 'official',
    Referee = 'referee',
}

export type AppState = {
    activeCompetition?: Competition;
    activeRole: AccessRole;
};



function loadInitial(): AppState {
    try {
        const raw = localStorage.getItem('appState');
        return raw ? JSON.parse(raw) : { activeCompetition: undefined, activeRole: AccessRole.Official } as AppState;
    } catch {
        return { activeCompetition: undefined, activeRole: AccessRole.Official } as AppState;
    }
}

export const appState = writable<AppState>(loadInitial());


appState.subscribe((value) => {
    if (typeof localStorage !== 'undefined') {
        localStorage.setItem('appState', JSON.stringify(value));
    }
});
