<script>
  import Icon from "./Icon.svelte";
  import page from 'page';
  let { email, group } = $props();

  function subTitle() {
    return `opened ${email.created_at} by ${email.sender_name}`;
  }

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
      {subTitle()}
    </div>
  </div>
</a>

<style lang="scss">
  @import '../../scss/components/list-item.scss';
</style>
