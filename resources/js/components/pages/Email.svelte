<script>
  import { onMount } from "svelte";

  let { uuid } = $props();
  let email = $state({});
  let isLoading = $state(true);
  let iframeEl;
  let iframeHeight = $state("auto");

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

    return { srcDoc: `<style>body{font-family: sans-serif; margin: 0; padding: 0;} html{margin: 0; padding: 0;}</style><base target='_top'>${email.html_body}` };
  }

  function handleIframeLoad() {
    if (iframeEl?.contentDocument?.body) {
      const height = iframeEl.contentDocument.body.scrollHeight;
      iframeHeight = `${height}px`;
    }
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

    <iframe
      bind:this={iframeEl}
      {...getIframeProps(email)}
      frameborder="0"
      width="100%"
      style="height: {iframeHeight}; border: none;"
      onload={handleIframeLoad}
    ></iframe>
  </article>
{/if}

<style>
  @import '../../../scss/pages/email.scss';
</style>
