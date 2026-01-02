<script>
  import { onMount, untrack } from "svelte";
  import { fly } from "svelte/transition";
  import ListItem from "../ListItem.svelte";
  import Email from "./Email.svelte";
  import SkeletonLoader from "../SkeletonLoader.svelte";

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
  <div class="email-list">
    {#if isLoading}
      <SkeletonLoader type="list-item" count={5} />
    {:else}
      {#each emails as email}
        <ListItem {email} {group} />
      {/each}
    {/if}
  </div>

  {#if emailUuid}
    <div class="email-view" transition:fly={{ x: 300, duration: 300 }}>
      <Email uuid={emailUuid} />
    </div>
  {/if}
</main>

<style>
  @import '../../../scss/pages/email-listing.scss';
</style>
