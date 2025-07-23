@php
    $class = '';
    $contextMenu = 'single-message';

    if (!$email['has_read']) {
        $class = 'unread';
        $contextMenu .= ' unread';
    } else {
        $contextMenu .= ' read';
    }

    if ($email['is_starred']) {
        $class .= ' starred';
        $contextMenu .= ' starred';
    } else {
        $contextMenu .= ' unstarred';
    }

    if ($email['is_deleted']) {
        $contextMenu .= ' deleted';
    } else {
        $contextMenu .= ' not-deleted';
    }

@endphp

<div class='email-item {{ $class }}' data-email-id='{{ $email['uuid'] }}' data-path='{{ $pathToEmail }}' data-context-menu='{{ $contextMenu }}'>
    <div class='email-from'>
        <img src="{{ gravar($email['from'], 64) }}" alt="{{ $email['from'] }}" class='email-avatar'>
        {{ $email['from'] }}
    </div>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ readableTime($email['sent_at']) }}</p>
</div>

<script>
  // Step 1: Get CSRF cookie and start session
(async () => {
  await fetch('https://login.lucasvanbriemen.nl/sanctum/csrf-cookie', {
    credentials: 'include'
  });

  await fetch('https://login.lucasvanbriemen.nl/login', {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email: 'your-email@example.com',
      password: 'your-password'
    })
  });

  const response = await fetch('https://login.lucasvanbriemen.nl/api/user', {
    method: 'GET',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json'
    }
  });

  const data = await response.json();
  console.log(data);
})();

</script>