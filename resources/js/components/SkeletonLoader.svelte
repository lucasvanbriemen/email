<script>
  let { type = 'list-item', count = 3 } = $props();
</script>

{#if type === 'list-item'}
  {#each Array(count) as _}
    <div class="skeleton skeleton-list-item">
      <div class="skeleton-avatar"></div>
      <div class="skeleton-content">
        <div class="skeleton-text skeleton-title"></div>
        <div class="skeleton-text skeleton-meta"></div>
      </div>
    </div>
  {/each}
{:else if type === 'email-header'}
  <div class="skeleton skeleton-email-header">
    <div class="skeleton-text skeleton-title" style="width: 60%;"></div>
    <div class="skeleton-text skeleton-meta" style="width: 40%; margin-top: 0.5rem;"></div>
    <div class="skeleton-text skeleton-meta" style="width: 35%; margin-top: 0.5rem;"></div>
  </div>
{:else if type === 'email-body'}
  <div class="skeleton skeleton-email-body">
    <div class="skeleton-text" style="margin-bottom: 1rem; height: 1.25rem;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem; width: 90%;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem;"></div>
    <div class="skeleton-text" style="margin-bottom: 1rem;"></div>
    <div class="skeleton-text" style="width: 60%;"></div>
  </div>
{/if}

<style lang="scss">
  @keyframes shimmer {
    0% {
      background-position: -1000px 0;
    }
    100% {
      background-position: 1000px 0;
    }
  }

  .skeleton {
    background: linear-gradient(
      90deg,
      var(--background-color-one) 0%,
      var(--background-color-two) 50%,
      var(--background-color-one) 100%
    );
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
  }

  .skeleton-list-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: calc(0.5rem - 4px) calc(1rem - 4px);
    background-color: var(--background-color-one);
    border-radius: 1rem;
    border: 2px solid var(--background-color-one);
    margin-bottom: 0.5rem;

    .skeleton-avatar {
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .skeleton-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;

      .skeleton-title {
        width: 60%;
        height: 1rem;
      }

      .skeleton-meta {
        width: 40%;
        height: 0.75rem;
      }
    }
  }

  .skeleton-email-header {
    background-color: var(--background-color-one);
    padding: 1rem;
    border-radius: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;

    .skeleton-title {
      width: 100%;
      height: 1.5rem;
    }

    .skeleton-meta {
      width: 100%;
      height: 0.75rem;
    }
  }

  .skeleton-email-body {
    border-radius: 1rem;
    background-color: white;
    padding: 1rem 0.5rem;
    width: calc(100% - 1rem);

    .skeleton-text {
      width: 100%;
      height: 1rem;
      border-radius: 0.25rem;
    }
  }

  .skeleton-text {
    width: 100%;
    height: 1rem;
    border-radius: 0.25rem;
    background: linear-gradient(
      90deg,
      var(--background-color-one) 0%,
      var(--background-color-two) 50%,
      var(--background-color-one) 100%
    );
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
  }
</style>
