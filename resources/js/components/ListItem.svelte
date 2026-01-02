<script>
  import Icon from "./Icon.svelte";
  let { email } = $props();

  function itemUrl(number) {
    const base = window.location.href;
    return `${base}/${number}`;
  }

  function subTitle() {
    return `opened ${email.created_at} by ${email.sender_name}`;
  }

</script>

<a class="list-item" href="{itemUrl(email.uuid)}">
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
