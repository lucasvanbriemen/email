<script>
  import { onMount } from 'svelte';

  let metadata = $state({});

  onMount(async () => {
    metadata = await api.get(route('mailbox.metadata'));
  });
</script>

<div>
  <h1>{message}</h1>
  <p>Welcome to the dashboard</p>
  <a href="/about">Go to About</a>
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
