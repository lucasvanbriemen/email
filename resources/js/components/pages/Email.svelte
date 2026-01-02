<script>
  import { onMount } from "svelte";

  let { uuid } = $props();

  onMount(async () => {
    console.log("Fetching email:", uuid);
  });
</script>

<article>
  {uuid}
</article>

<style>
  @import '../../../scss/pages/email-listing.scss';
</style>
