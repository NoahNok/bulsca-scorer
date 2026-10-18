# League Objectives go to the v2 judge app only

Master added League Objectives (then called "restricted judges") to the legacy Blade Digital Judge, while `feature/even-better-judging` built the v2 Svelte judge app alongside it. Rather than keep both flows feature-matched, we port League Objectives to v2 only and freeze the legacy flow: it keeps master's partial implementation and gets crash fixes, but new judging features go to v2. This avoids building and testing every on-the-day feature twice, at the cost of legacy and v2 behaving differently until legacy is retired.
