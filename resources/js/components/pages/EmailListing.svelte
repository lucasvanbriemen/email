<script>
  import { onMount, untrack } from "svelte";
  import ListItem from "../ListItem.svelte";

  let { group, emailUuid } = $props();
  let emailData = $state([]);
  let emails = $state([]);
  let isLoading = $state(true);

  onMount(async () => {
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
    void group

    untrack(() => {
      getEmails();
    });
  });
</script>

<main>
  {#if isLoading}
    <p>Loading...</p>
  {:else}
    {#each emails as email}
      <ListItem {email} />
    {/each}
  {/if}
</main>

<style>
  @import '../../../scss/pages/email-listing.scss';
</style>
