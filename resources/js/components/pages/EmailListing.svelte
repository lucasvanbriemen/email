<script>
  import { onMount, untrack } from "svelte";
  import ListItem from "../ListItem.svelte";

  let { group, emailUuid } = $props();
  let emailData = $state([]);
  let emails = $state([]);
  let isLoading = $state(true);
  let previousGroup = $state(null);

  onMount(async () => {
    previousGroup = group;
    getEmails();
  });

  async function getEmails () {
    isLoading = true;
    console.log("Fetching emails for group:", group);
    emailData = await api.get("/api/mailbox/" + group);

    emails = emailData.data;

    isLoading = false;
  }

  $effect(() => {
    if (group !== previousGroup) {
      previousGroup = group;
      getEmails();
    }
  });
</script>

<main>
  <div>
    {#if isLoading}
      <p>Loading...</p>
    {:else}
      {#each emails as email}
        <ListItem {email} {group} />
      {/each}
    {/if}
  </div>

  <div>
    {#if emailUuid}
      {emailUuid}
    {/if}
  </div>
</main>

<style>
  @import '../../../scss/pages/email-listing.scss';
</style>
