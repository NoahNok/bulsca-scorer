<!--
    An Official's role as a .v2-badge, Referee picked out in teal so they're easy to spot in a list.
    `roles` is Competition::$officialRoles, the role keys to their names.
-->
<script lang="ts">
    let { role, roles }: { role: string; roles: Record<string, string> } = $props();
</script>

<span class={["v2-badge whitespace-nowrap", role === "referee" && "is-live"]}>{roles[role] ?? role}</span>
