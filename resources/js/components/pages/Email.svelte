<script>
  import { onMount } from "svelte";

  let { uuid } = $props();
  let email = $state({});

  onMount(async () => {
    email = await api.get("/api/email/" + uuid);
  });
</script>

<article>
  <h1>{email.subject}</h1>
</article>

<style>
  @import '../../../scss/pages/email-listing.scss';
</style>
