<script>
  import { onMount, untrack } from "svelte";

  let { group } = $props();
  let emailData = $state([]);
  let isLoading = $state(true);

  onMount(async () => {
    getEmails();
  });

  async function getEmails () {
    isLoading = true;
    console.log("Fetching emails for group:", group);
    emailData = await api.get("/api/mailbox/" + group);
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
  <h1>About Page</h1>
  <p>This is the about page</p>
  <a href="/">Go to Dashboard</a>
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
