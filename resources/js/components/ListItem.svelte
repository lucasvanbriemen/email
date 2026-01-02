<script>
  import page from 'page';
  let { email, group } = $props();

  function handleClick(e) {
    e.preventDefault();
    email.has_read = true;
    page.show(`/${group}/${email.uuid}`);
  }

</script>

<a class="list-item" class:unread={!email.has_read} href="#{email.uuid}" on:click={handleClick}>
  <img src="/{email.sender_logo_url}" alt="{email.sender_name}" class="logo" />

  <div class="content">
    <h3>{email.sender_name}</h3>
    <div class="meta">
      {email.subject}
      <span class="time">{email.created_at_human}</span>
    </div>
  </div>
</a>

<style lang="scss">
  @import '../../scss/components/list-item.scss';
</style>
