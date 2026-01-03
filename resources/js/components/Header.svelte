<script>
  import { onMount } from 'svelte';
  import { currentGroup } from '../stores/currentGroup.js';
  import Icon from './Icon.svelte';

  let groups = $state([]);

  onMount(async () => {
    groups = await api.get('/api/mailbox/metadata');
  });
</script>

<header class:is-mobile={IS_MOBILE}>
  <a class="logo" href="/">
    <Icon name="logo" size="2rem" />
    <span class="title">Email</span>
  </a>

  <div class="separator"></div>

  {#each groups as group}
    <a href="/{group.path}" class:active={$currentGroup == group.path}>{group.name}</a>
  {/each}
</header>

<style>
  @import '../../scss/header.scss';
</style>
