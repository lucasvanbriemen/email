<script>
  import { onMount } from "svelte";

  let { uuid } = $props();
  let email = $state({});
  let isLoading = $state(true);

  onMount(async () => {
    await loadEmail();
  });

  async function loadEmail () {
    isLoading = true;
    email = await api.get("/api/email/" + uuid);
    isLoading = false;
  }

  function getIframeProps(email) {
    if (email.sender.email === "ntfy@ltvb.nl") {
      return { src: email.html_body };
    }

    return { srcDoc: email.html_body };
  }

  $effect(() => {
    loadEmail();
  });
</script>

{#if isLoading}
  <p>Loading email...</p>
{:else}
  <article>
    <div class="header">
      <h2>{email.subject}</h2>
      <p>{email.sender.name} ({email.sender.email})</p>
      <p>{email.to} ({email.created_at_human})</p>
    </div>

    <iframe {...getIframeProps(email)} frameborder="0" width="100%" height="600px"></iframe>
  </article>
{/if}

<style>
  @import '../../../scss/pages/email.scss';
</style>
