<script>
  import { onMount } from 'svelte';
  import Icon from '../Icon.svelte';

  let groups = $state([]);

  onMount(async () => {
    groups = await api.get('/api/mailbox/metadata');
  });
</script>

<main>
  
</main>

<style>
</style>
