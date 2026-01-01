<script>
  import { onMount } from 'svelte';
  import Icon from './Icon.svelte';

  let groups = $state([]);

  onMount(async () => {
    groups = await api.get('/api/mailbox/metadata');
  });
</script>

<header>
  <a class="logo" href="/">
    <Icon name="logo" />
    <span class="title">Email</span>
  </a>

  <div class="separator"></div>

  {#each groups as group}
    <a href="/{group.path}">{group.name}</a>
  {/each}
</header>

<style>
  @import '../../scss/header.scss';
</style>
