{{--
    The catalogue of shared v2 components (resources/css/v2.css), in use. Only the sheet's own layout
    (k- classes) is styled here. Nothing here submits anywhere.
--}}
<style>
    .k-kit {
        padding: 3rem 0 4rem;
        border-top: 1px solid var(--v2-border);
    }

    .k-kit>h2 {
        font-size: 1.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .k-intro {
        color: var(--v2-muted);
        margin: 0.5rem 0 2rem;
        max-width: 40rem;
    }

    .k-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 22rem), 1fr));
    }

    .k-panel {
        background: var(--v2-card);
        border: 1px solid var(--v2-border);
        border-radius: 1rem;
        padding: 1.5rem;
    }

    .k-panel>h3 {
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .k-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    .k-row+.k-row {
        margin-top: 0.75rem;
    }

    .k-panel p {
        color: var(--v2-muted);
        line-height: 1.6;
    }
</style>

<section class="k-kit" aria-labelledby="k-kit" x-data="{
    toasts: [],
    modal: false,
    tab: 'Heats',
    notify: true,
    toast(kind, message) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, kind, message, shown: false, leaving: false });
        setTimeout(() => this.dismiss(id), 4000);
    },
    // x-for drops removed items straight away, so mark the toast as leaving, let it animate out, then remove it
    dismiss(id) {
        const t = this.toasts.find(t => t.id === id);
        if (!t || t.leaving) return;
        t.leaving = true;
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 300);
    }
}">
    <h2 id="k-kit">Theme samples</h2>
    <p class="k-intro">How the site's buttons, fields and messages could look in this theme. These are samples
        only: nothing here saves.</p>

    <div class="k-grid">
        <div class="k-panel">
            <h3>Buttons</h3>
            <div class="k-row">
                <button type="button" class="v2-btn v2-btn-primary">Save changes</button>
                <button type="button" class="v2-btn">Cancel</button>
            </div>
            <div class="k-row">
                <button type="button" class="v2-btn v2-btn-accent">Create competition</button>
                <button type="button" class="v2-btn v2-btn-danger">Delete event</button>
            </div>
            <div class="k-row">
                <button type="button" class="v2-btn v2-btn-ghost">Export CSV</button>
                <button type="button" class="v2-btn v2-btn-primary v2-btn-sm">Add team</button>
                <button type="button" class="v2-btn" disabled>Publish results</button>
            </div>
        </div>

        <div class="k-panel">
            <h3>Links</h3>
            <div class="k-row">
                <a href="#k-kit" class="v2-link">All competitions</a>
                <a href="#k-kit" class="v2-link-muted">Terms</a>
            </div>
            <p style="margin-top: 1rem">Results are provisional until the referee signs them off. <a href="#k-kit"
                    class="v2-link-inline">Read how appeals work</a> before the deadline.</p>
            <h3 style="margin-top: 1.5rem">Status</h3>
            <div class="k-row">
                <span class="v2-badge">Final</span>
                <span class="v2-badge is-provisional">Provisional</span>
                <span class="v2-badge is-live"><span class="v2-dot is-live" aria-hidden="true"></span> Live</span>
                <span class="v2-badge is-success">Approved</span>
                <span class="v2-badge is-danger">DQ</span>
            </div>
        </div>

        <div class="k-panel">
            <h3>Fields</h3>
            <div class="v2-field">
                <label for="k-name">Competition name</label>
                <input id="k-name" class="v2-input" type="text" placeholder="e.g. Warwick Lifesaving Championships">
            </div>
            <div class="v2-field">
                <label for="k-lanes">Lanes</label>
                <select id="k-lanes" class="v2-input">
                    <option>6 lanes</option>
                    <option selected>8 lanes</option>
                    <option>10 lanes</option>
                </select>
                <span class="v2-hint">How many lanes the pool can run at once.</span>
            </div>
            <div class="v2-field">
                <label for="k-email">Organiser email</label>
                <input id="k-email" class="v2-input is-invalid" type="email" value="organiser@" aria-invalid="true"
                    aria-describedby="k-email-error">
                <span id="k-email-error" class="v2-error">Enter a full email address, like name@club.org.</span>
            </div>
            <div class="v2-field">
                <label for="k-notes">Notes for officials</label>
                <textarea id="k-notes" class="v2-input" placeholder="Parking, start times, anything officials should know"></textarea>
            </div>
        </div>

        <div class="k-panel">
            <h3>Choices</h3>
            <label class="v2-check"><input type="checkbox" checked> Show heats to competitors</label>
            <label class="v2-check"><input type="checkbox"> Show draws to competitors</label>

            <div style="margin-top: 1.25rem" role="radiogroup" aria-label="Results">
                <label class="v2-check"><input type="radio" name="k-results" checked> Keep results private</label>
                <label class="v2-check"><input type="radio" name="k-results"> Publish results as provisional</label>
                <label class="v2-check"><input type="radio" name="k-results"> Publish final results</label>
            </div>

            {{-- A label around the switch, so clicking the text toggles it too --}}
            <label class="v2-check" style="margin-top: 1.25rem">
                <button type="button" class="v2-switch" role="switch" :aria-checked="notify.toString()"
                    @click="notify = !notify"></button>
                <span>Email me when a DQ is submitted</span>
            </label>

            <h3 style="margin-top: 1.5rem">Tabs</h3>
            <div class="v2-tabs" role="tablist">
                <span class="v2-tabs-pill" aria-hidden="true"
                    :style="`transform: translateX(${['Heats', 'Draws', 'Results'].indexOf(tab) * 100}%)`"></span>
                <template x-for="name in ['Heats', 'Draws', 'Results']">
                    <button type="button" role="tab" :aria-selected="(tab === name).toString()" @click="tab = name"
                        x-text="name"></button>
                </template>
            </div>
            <p style="margin-top: 0.75rem" x-text="'Showing ' + tab.toLowerCase() + ' for this competition.'"></p>
        </div>

        <div class="k-panel">
            <h3>Alerts</h3>
            <div class="v2-alert is-info" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 11v5M12 8h.01" />
                </svg>
                <div><strong>Heats are ready</strong>Check them before you share them with teams.</div>
            </div>
            <div class="v2-alert is-success" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m5 12 5 5 9-10" />
                </svg>
                <div><strong>Results published</strong>Competitors can see them now.</div>
            </div>
            <div class="v2-alert is-danger" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 9v4M12 17h.01" />
                    <path d="M10.3 3.9 2.4 18a2 2 0 0 0 1.7 3h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                </svg>
                <div><strong>Two teams share lane 4</strong>Move one of them before you generate heats.</div>
            </div>
        </div>

        <div class="k-panel">
            <h3>Toasts</h3>
            <div class="v2-toast is-success">
                <svg class="v2-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    aria-hidden="true">
                    <path d="m5 12 5 5 9-10" />
                </svg>
                <span>Team added</span>
            </div>
            <div class="v2-toast is-danger">
                <svg class="v2-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    aria-hidden="true">
                    <path d="M12 8v5M12 16h.01" />
                    <circle cx="12" cy="12" r="9" />
                </svg>
                <span>You can send the invite again in 5 minutes</span>
            </div>
            <div class="k-row" style="margin-top: 1.25rem">
                <button type="button" class="v2-btn" @click="toast('success', 'Changes saved')">Show success
                    toast</button>
                <button type="button" class="v2-btn" @click="toast('danger', 'Couldn\'t save: the event has results')">Show
                    error toast</button>
            </div>

            <h3 style="margin-top: 1.5rem">Modal</h3>
            <button type="button" class="v2-btn" @click="modal = true">Show modal</button>
        </div>
    </div>

    <div class="v2-toasts" aria-live="polite">
        <template x-for="t in toasts" :key="t.id">
            <div class="v2-toast-slot" :class="{ 'is-in': t.shown && !t.leaving, 'is-leaving': t.leaving }"
                x-init="setTimeout(() => t.shown = true, 20)">
                <div class="v2-toast-clip">
                    <div class="v2-toast" :class="'is-' + t.kind">
                        <svg x-show="t.kind === 'success'" class="v2-toast-icon" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="m5 12 5 5 9-10" />
                        </svg>
                        <svg x-show="t.kind === 'danger'" class="v2-toast-icon" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="M12 8v5M12 16h.01" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                        <span x-text="t.message"></span>
                        <button type="button" @click="dismiss(t.id)">Dismiss</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="v2-modal-backdrop" x-show="modal" x-cloak x-transition.opacity @click.self="modal = false"
        @keydown.escape.window="modal = false">
        {{-- x-trap keeps Tab inside and focuses the first button, the safe one --}}
        <div class="v2-modal" role="dialog" aria-modal="true" aria-labelledby="k-modal-title"
            x-trap.noscroll="modal">
            <h3 id="k-modal-title">Delete 100m Rescue Medley?</h3>
            <p>This removes the event and every time recorded in it. Results that use it will need regenerating. You
                can't undo this.</p>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" @click="modal = false">Keep event</button>
                <button type="button" class="v2-btn v2-btn-danger"
                    @click="modal = false; toast('success', 'Event deleted')">Delete event</button>
            </div>
        </div>
    </div>
</section>
