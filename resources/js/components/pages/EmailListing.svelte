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
  let currentPage = $state(1);
  let searchQuery = $state("");
  let selectedEmail = $state(null);

  onMount(async () => {
    previousGroup = group;
    getEmails();
  });

  async function getEmails (pageNum = 1) {
    isLoading = true;
    console.log("Fetching emails for group:", group, "page:", pageNum, "search:", searchQuery);

    let url = "/api/mailbox/" + group + "?page=" + pageNum;
    if (searchQuery.trim()) {
      url += "&search=" + encodeURIComponent(searchQuery);
    }

    emailData = await api.get(url);

    emails = emailData.data;
    currentPage = pageNum;

    emailData.data.forEach(email => {
      email.selected = false;
    });

    isLoading = false;
  }

  function handleSearch() {
    currentPage = 1;
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
      currentPage = 1;
      searchQuery = "";
      getEmails(1);
    }
  });

  function goBack() {
    page.show(`/${group}`);
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
      {#each emails as email (email.uuid)}
        <ListItem {email} {group} />
      {/each}
    {/if}
  </div>

  {#if emailUuid}
    <div class="email-view" transition:fly={{ x: 300, duration: 300 }}>
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
