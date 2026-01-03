<script>
  import { onMount } from 'svelte';
  import Icon from './Icon.svelte';
  import page from 'page';

  let groups = $state([]);

  onMount(async () => {
    groups = await api.get('/api/mailbox/metadata');
  });

  function onSegmentChange(event) {
    page.show(`/${event.detail.value}`);
  }
</script>

<ion-segment on:ionChange={onSegmentChange}>
  {#each groups as group}
    <ion-segment-button value={group.path}>
      <ion-label>{group.name}</ion-label>
    </ion-segment-button>
  {/each}
</ion-segment>

<!-- <header class:is-mobile={IS_MOBILE}>
  <a class="logo" href="/">
    <Icon name="logo" size="2rem" />
    <span class="title">Email</span>
  </a>

  <div class="separator"></div>

  {#each groups as group}
    <a href="/{group.path}">{group.name}</a>
  {/each}
</header> -->

<style>
  @import '../../scss/header.scss';
</style>
