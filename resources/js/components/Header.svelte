<script>
  import { onMount } from 'svelte';
  import Icon from './Icon.svelte';

  let groups = $state([]);
  let currentPath = window.location.pathname;
  let activeGroup = $state('work')

  onMount(async () => {
    groups = await api.get('/api/mailbox/metadata');

    groups.forEach(group => {
      if (currentPath.includes(group.path)) {
        activeGroup = group.path;
      }
    });
  });
</script>

<header class:is-mobile={IS_MOBILE}>
  <a class="logo" href="/">
    <Icon name="logo" size="2rem" />
    <span class="title">Email</span>
  </a>

  <div class="separator"></div>

  {#each groups as group}
    <a href="/{group.path}" class:active={activeGroup == group.path}>{group.name}</a>
  {/each}
</header>

<style>
  @import '../../scss/header.scss';
</style>
