<script>
  import page from 'page';
  let { email, group } = $props();

  async function handleClick(e) {
    e.preventDefault();
    email.selected = true;
    // Mark email as read via API when viewing
    email.has_read = true;
    page.show(`/${group}/${email.uuid}`);
  }

</script>

<a class="list-item" class:unread={!email.has_read} class:is-mobile={IS_MOBILE} href="#{email.uuid}" onclick={handleClick} class:selected={email.selected}>
  <img src="{currentDomain}/{email.sender.image_path}" alt="{email.sender_name}" class="logo" />

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
