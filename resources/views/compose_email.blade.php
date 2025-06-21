@vite(['resources/css/email/compose_email.scss', 'resources/js/email/compose_email.js'])

<div class="compose-email-wrapper">
    <div class="compose-email-background"></div>

    <div class="compose-email">
        @csrf

        <input type='hidden' name='credential_id' class='email-credential-id' value='{{ $selectedCredential->id }}'/>

        <x-input type="email" name="to" class='email-to'/>
        <x-input type="email" name="cc" label="cc" class='email-cc'/>
        <x-input type="text" name="subject" label="subject" class='email-subject'/>

        <input name="body" hidden class='email-body'></textarea>
        <div id="email-body-wysiwyg"></div>

        <button class='send-email'>Send</button>
    </div>
</div>