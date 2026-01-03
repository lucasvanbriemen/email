<script>
  import { onMount } from 'svelte';
  import theme from '../lib/theme.js';
  import api from '../lib/api.js';
  import page from 'page';
  import Header from './Header.svelte';
  import Dashboard from './pages/Dashboard.svelte';
  import EmailListing from './pages/EmailListing.svelte';

  let currentComponent;
  let params = {};

  const routes = {
    '/': Dashboard,
    '/:group': EmailListing,
    '/:group/:emailUuid': EmailListing,
  };

  onMount(() => {
    // Register all routes with page.js
    page('/', () => {
      page.redirect('/home');
    });

    page('/:group', ctx => {
      currentComponent = routes['/:group'];
      params = { group: ctx.params.group, emailUuid: null };
    });

    page('/:group/:emailUuid', ctx => {
      currentComponent = routes['/:group/:emailUuid'];
      params = { group: ctx.params.group, emailUuid: ctx.params.emailUuid };
    });

    // Catch all other routes and redirect to home
    page('*', () => {
      page.redirect('/home');
    });

    // Start the router
    page.start();

    theme.applyTheme();
  });
  
  window.api = api;
</script>

{#if !IS_MOBILE}
  <Header />
{/if}

{#if currentComponent}
  <svelte:component this={currentComponent} {...params} />
{/if}

{#if IS_MOBILE}
  <Header />
{/if}

<style>
  @import '../../scss/gloabal.scss';
</style>