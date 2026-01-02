<script>
  import { onMount } from "svelte";
  import SkeletonLoader from "../SkeletonLoader.svelte";

  let { uuid, parentEmail } = $props();
  let email = $state({});
  let attachments = $state([]);
  let isLoading = $state(true);
  let iframeEl;
  let iframeHeight = $state("auto");

  onMount(async () => {
    await loadEmail();
  });

  async function loadEmail () {
    isLoading = true;
    email = await api.get("/api/email/" + uuid);

    // Update parent email object if provided
    if (parentEmail) {
      parentEmail.has_read = true;
    }

    // Load attachments
    try {
      const attachmentsData = await api.get(`/api/email/${uuid}/attachments`);
      attachments = attachmentsData.attachments || [];
    } catch (e) {
      attachments = [];
    }

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

  function isBodyContent(filename) {
    // Check if this attachment was used as the email body
    // The backend loads .html and .txt files as body when html_body is empty
    const ext = filename.split('.').pop()?.toLowerCase();
    return ext === 'html' || ext === 'txt';
  }

  function getDisplayableAttachments() {
    // Filter out attachments that are likely body content
    // If email body is from file content, don't show those files again
    return attachments.filter(attachment => !isBodyContent(attachment.name));
  }

  $effect(() => {
    loadEmail();
  });
</script>

{#if isLoading}
  <article>
    <SkeletonLoader type="email-header" />
    <SkeletonLoader type="email-body" />
  </article>
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

    {#if getDisplayableAttachments().length > 0}
      <div class="attachments">
        <div class="attachments-header">Attachments ({getDisplayableAttachments().length})</div>
        <div class="attachments-list">
          {#each getDisplayableAttachments() as attachment}
            <a href="/{attachment.path}" class="attachment" target="_blank" rel="noopener noreferrer">
              {attachment.name}
            </a>
          {/each}
        </div>
      </div>
    {/if}
  </article>
{/if}

<style>
  @import '../../../scss/pages/email.scss';
</style>
