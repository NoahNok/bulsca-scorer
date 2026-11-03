{{--
    A toast for a notice another host sent the visitor here with, as ?notice=<key> (the session cookie is per
    host, so app. can't flash one onto the main domain). Only the keys below show; the param is then dropped
    from the address so a reload or a shared link doesn't show it again. Same markup as the kit's toasts.
--}}
@php
    $notices = [
        'invite-invalid' => ['danger', 'That invite is no longer valid'],
    ];
    $notice = $notices[request()->query('notice')] ?? null;
@endphp

@if ($notice)
    <div class="v2-toasts" aria-live="polite" x-data="{
        shown: false,
        leaving: false,
        gone: false,
        dismiss() {
            if (this.leaving) return;
            this.leaving = true;
            setTimeout(() => this.gone = true, 300);
        }
    }" x-init="
        const url = new URL(location.href);
        url.searchParams.delete('notice');
        history.replaceState(history.state, '', url);
        setTimeout(() => shown = true, 20);
        setTimeout(() => dismiss(), 6000);
    ">
        <template x-if="!gone">
            <div class="v2-toast-slot" :class="{ 'is-in': shown && !leaving, 'is-leaving': leaving }">
                <div class="v2-toast-clip">
                    <div class="v2-toast is-{{ $notice[0] }}">
                        <svg class="v2-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" aria-hidden="true">
                            @if ($notice[0] === 'danger')
                                <path d="M12 8v5M12 16h.01" />
                                <circle cx="12" cy="12" r="9" />
                            @else
                                <path d="m5 12 5 5 9-10" />
                            @endif
                        </svg>
                        <span>{{ $notice[1] }}</span>
                        <button type="button" @click="dismiss()">Dismiss</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endif
