{{-- $store.live: polls live.data every 5s and works out each team's SERC state and estimated start time.
     <x-live.serc-order> sets `groups` (team ids per tank, in draw order). --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('live', {
            url: @js(route('live.data', $comp->id)),
            connected: null,
            finished: {},
            avgTime: 360,
            startTime: 0,
            groups: [],
            sercCount: 2,
            now: new Date(),

            init() {
                this.poll();
                setInterval(() => this.poll(), 5000);
                setInterval(() => this.now = new Date(), 1000);
            },

            async poll() {
                try {
                    const response = await fetch(this.url);
                    if (!response.ok) throw new Error(response.status);
                    const data = await response.json();

                    this.finished = data.sercsFinished ?? {};
                    this.avgTime = +data.avgTime;
                    this.startTime = +data.sercStartTime;
                    this.connected = true;
                } catch (e) {
                    this.connected = false;
                }
            },

            // How many SERCs the team has finished
            status(teamId) {
                return +(this.finished[teamId] ?? 0);
            },

            // 'waiting', 'part' (done the dry SERC, not the wet) or 'done'
            state(teamId) {
                const done = this.status(teamId);
                if (done == 0) return 'waiting';
                return done >= this.sercCount ? 'done' : 'part';
            },

            // Unfinished teams in draw order, one avgTime apart from the start time, but never earlier than now
            get estimates() {
                let time = new Date(Math.max(this.startTime, this.now.getTime()));
                const estimates = {};

                this.groups.flat().forEach(teamId => {
                    if (this.status(teamId) > 0) return;
                    estimates[teamId] = this.clock(time);
                    time = new Date(time.getTime() + this.avgTime * 1000);
                });

                return estimates;
            },

            // The first unfinished team in each tank
            get upNext() {
                return this.groups.map(group => group.find(teamId => this.status(teamId) == 0)).filter(Boolean);
            },

            label(teamId) {
                const state = this.state(teamId);
                if (state == 'done') return this.sercCount > 1 ? 'Finished (Both)' : 'Finished';
                if (state == 'part') return 'Finished (Dry)';
                return 'Est: ' + (this.connected === null ? '–' : this.estimates[teamId] ?? '–');
            },

            clock(date) {
                return date.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },
        });
    });
</script>
