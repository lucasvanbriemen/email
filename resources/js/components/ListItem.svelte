<script>
  import page from 'page';
  let { email, group } = $props();

  function handleClick(e) {
    e.preventDefault();
    email.has_read = true;
    page.show(`/${group}/${email.uuid}`);
  }

</script>

<a class="list-item" class:unread={!email.has_read} href="#{email.uuid}" onclick={handleClick}>
  <img src="/{email.sender.logo_url}" alt="{email.sender_name}" class="logo" />

  <div class="content">
    <h3>{email.subject}</h3>
    <div class="meta">
      {email.sender_name} - {email.created_at_human} 
    </div>
  </div>
</a>

<style lang="scss">
  @import '../../scss/components/list-item.scss';
</style>
