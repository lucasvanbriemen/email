<script>
  import Icon from "./Icon.svelte";
  import page from 'page';
  let { email, group } = $props();

  function handleClick(e) {
    e.preventDefault();
    page.show(`/${group}/${email.uuid}`);
  }

</script>

<a class="list-item" href="#{email.uuid}" on:click={handleClick}>
  <Icon name=logo size="1.5rem"/>

  <div class="content">
    <h3>{email.subject}</h3>
    <div class="meta">
      {email.sender_name} {email.created_at_human}
    </div>
  </div>
</a>

<style lang="scss">
  @import '../../scss/components/list-item.scss';
</style>
