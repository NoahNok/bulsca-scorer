<script lang="ts">
    import { page } from "@inertiajs/svelte";

    const user = $derived(page.props.auth.user);
</script>

<p>{user.name}</p>
