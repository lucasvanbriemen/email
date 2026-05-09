<script>
  import { onMount, untrack } from "svelte";
  import { fly } from "svelte/transition";
  import page from "page";
  import ListItem from "../ListItem.svelte";
  import Email from "./Email.svelte";
  import SkeletonLoader from "../SkeletonLoader.svelte";

  let { group, emailUuid } = $props();
  let emailData = $state({});
  let emails = $state([]);
  let isLoading = $state(true);
  let previousGroup = $state(null);
  let searchQuery = $state("");
  let cleanupPullToRefresh;
  let cleanupSwipeToClose;

  onMount(async () => {
    previousGroup = group;
    getEmails();

    return () => {
      cleanupPullToRefresh?.();
      cleanupSwipeToClose?.();
    };
  });

  async function getEmails (pageNum = 1) {
    isLoading = true;

    let url = "/api/mailbox/" + group + "?page=" + pageNum;
    if (searchQuery.trim()) {
      url += "&search=" + encodeURIComponent(searchQuery);
    }

    emailData = await api.get(url);

    emails = emailData.data;

    emailData.data.forEach(email => {
      email.selected = false;
    });

    isLoading = false;
  }

  function handleSearch() {
    getEmails(1);
  }

  function getSelectedEmailForDetail() {
    if (!emailUuid) return null;
    return emails.find(email => email.uuid === emailUuid);
  }

  function goToPage(pageNum) {
    getEmails(pageNum);
  }

  function goToPreviousPage() {
    if (emailData.current_page > 1) {
      goToPage(emailData.current_page - 1);
    }
  }

  function goToNextPage() {
    if (emailData.current_page < emailData.last_page) {
      goToPage(emailData.current_page + 1);
    }
  }

  $effect(() => {
    if (group !== previousGroup) {
      previousGroup = group;
      searchQuery = "";
      getEmails(1);
    }
  });

  function goBack() {
    page.show(`/${group}`);
  }

  function groupEmailsByDate(emailList) {
    const groups = {};

    emailList.forEach(email => {
      const date = new Date(email.created_at);
      const dateKey = date.toDateString(); // "Sun Jan 23 2025"

      if (!groups[dateKey]) {
        groups[dateKey] = [];
      }
      groups[dateKey].push(email);
    });

    return groups;
  }

  function formatDateHeader(dateString) {
    const date = new Date(dateString);
    const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
    const month = date.toLocaleDateString('en-US', { month: 'short' });
    const day = date.getDate();

    return `${dayName} ${month} ${day}`;
  }
</script>

<main>
  <div class="email-list">

    {#if !isLoading && emailData.total > 0}
      <div class="pagination">
        <input
          type="text"
          placeholder="Search emails..."
          bind:value={searchQuery}
          onkeydown={(e) => e.key === 'Enter' && handleSearch()}
          class="search-input"
        />

        <button onclick={goToPreviousPage} disabled={emailData.current_page === 1}>
          Previous
        </button>

        <span class="pagination-info">
          Page {emailData.current_page} of {emailData.last_page} ({emailData.total} emails)
        </span>

        <button onclick={goToNextPage} disabled={emailData.current_page === emailData.last_page}>
          Next
        </button>
      </div>
    {/if}

    {#if isLoading}
      <SkeletonLoader type="list-item" count={5} />
    {:else}
      {#each Object.entries(groupEmailsByDate(emails)) as [dateKey, dateEmails]}
        <div class="date-group">
          <h3 class="date-header">{formatDateHeader(dateKey)}</h3>
          {#each dateEmails as email (email.uuid)}
            <div>
              <ListItem {email} {group} />
            </div>
          {/each}
        </div>
      {/each}
    {/if}
  </div>

  {#if emailUuid}
    <div class="email-view" transition:fly={{ x: 300, duration: 200 }}>
      <button class="go-back-btn" onclick={goBack}>
        Back
      </button>
      <Email uuid={emailUuid} parentEmail={getSelectedEmailForDetail()} />
    </div>
  {/if}
</main>

<style>
  @import '../../../scss/pages/email-listing.scss';
</style>
