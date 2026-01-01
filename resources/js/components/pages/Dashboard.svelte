<script>
  import { onMount } from 'svelte';

  let groups = $state([]);

  onMount(async () => {
    groups = await api.get(route('mailbox.metadata'));
    console.log(groups);
  });
</script>

<div>
  <h1>groups</h1>
  {#each groups as group}
    <a href="/{group.path}">{group.name}</a>
  {/each}
</div>

<style>
 
</style>
