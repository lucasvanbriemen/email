<script>
  import { onMount } from "svelte";

  let { uuid } = $props();
  let email = $state({});
  let isLoading = $state(true);

  onMount(async () => {
    email = await api.get("/api/email/" + uuid);
    isLoading = false;
  });

  function getIframeProps(email) {
    if (email.sender === "ntfy@ltvb.nl") {
      return { src: email.html_body };
    }

    return { srcDoc: email.html_body };
  }
</script>

{#if isLoading}
  <p>Loading email...</p>
{:else}
  <article>
    <header>
      <h1>{email.subject}</h1>
      <h1>{email.sender.name}</h1>
    </header>

    <iframe {...getIframeProps(email)} frameborder="0" width="100%" height="600px"></iframe>
  </article>
{/if}

<style>
  @import '../../../scss/pages/email.scss';
</style>
