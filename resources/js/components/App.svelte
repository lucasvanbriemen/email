<script>
  import { onMount } from 'svelte';
  import theme from '../lib/theme.js';
  import api from '../lib/api.js';
  import page from 'page';
  import Dashboard from './pages/Dashboard.svelte';
  import About from './pages/About.svelte';

  let currentComponent;
  let params = {};

  const routes = {
    '/': Dashboard,
    '/about': About,
  };

  onMount(() => {
    // Register all routes with page.js
    page('/', () => {
      currentComponent = routes['/'];
      params = {};
    });

    page('/about', () => {
      currentComponent = routes['/about'];
      params = {};
    });

    // Catch all other routes and render the default component
    page('*', () => {
      currentComponent = routes['/'];
      params = {};
    });

    // Start the router
    page.start();

    theme.applyTheme();
    window.api = api;
  });
</script>

{#if currentComponent}
  <svelte:component this={currentComponent} {params} />
{/if}
