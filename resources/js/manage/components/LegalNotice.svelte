<!--
    Under a form that signs someone in or gives them an account. The Management app's copy of
    components/legal-notice.blade.php.
-->
<script lang="ts">
    import { page } from "@inertiajs/svelte";
</script>

<p class="text-[0.8125rem]/normal text-v2-muted">
    By continuing you agree to the
    <a class="v2-link-inline" href={page.props.legal.terms}>Terms of Service</a>
    and
    <a class="v2-link-inline" href={page.props.legal.privacy}>Privacy Policy</a>.
</p>
