<!--
    Under a form that signs someone in or gives them an account. The Management app's copy of
    components/legal-notice.blade.php.
-->
<script lang="ts">
    import { page } from "@inertiajs/svelte";
</script>

<p class="l-notice">
    By continuing you agree to the
    <a class="v2-link-inline" href={page.props.legal.terms}>Terms of Service</a>
    and
    <a class="v2-link-inline" href={page.props.legal.privacy}>Privacy Policy</a>.
</p>

<style>
    .l-notice {
        font-size: 0.8125rem;
        line-height: 1.5;
        color: var(--v2-muted);
    }
</style>
