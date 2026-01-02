<script>
  import { onMount, untrack } from "svelte";
  import ListItem from "../ListItem.svelte";

  let { group } = $props();
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

<div>
  {#if isLoading}
    <p>Loading...</p>
  {:else}
    {#each emails as email}
      <ListItem {email} />
    {/each}
  {/if}
</div>

<style>
  div {
    padding: 2rem;
  }

  a {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 0.25rem;
  }

  a:hover {
    background-color: #0056b3;
  }
</style>
