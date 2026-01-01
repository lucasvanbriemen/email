<script>
  import { onMount } from 'svelte';

  let groups = $state([]);

  onMount(async () => {
    groups = await api.get('/api/mailbox/metadata');
  });
</script>

<div class="sidebar">
  <h1>groups</h1>
  {#each groups as group}
    <a href="/{group.path}">{group.name}</a>
  {/each}
</div>

<style>
  @import '../../../scss/pages/dashboard.scss';
</style>
