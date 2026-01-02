<script>
  import { onMount } from "svelte";

  let { uuid } = $props();
  let email = $state({});

  onMount(async () => {
    email = await api.get("/api/email/" + uuid);
  });
</script>

<article>
  <header>
    <h1>{email.subject}</h1>
  </header>

  <iframe srcdoc={email.html_body} frameborder="0" width="100%" height="600px"></iframe>
</article>

<style>
  @import '../../../scss/pages/email.scss';
</style>
