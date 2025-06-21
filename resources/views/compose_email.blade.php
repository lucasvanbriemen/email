@vite(['resources/css/email/compose_email.scss', 'resources/js/email/compose_email.js'])

<div class="compose-email-wrapper">
    <div class="compose-email-background"></div>

    <div class="compose-email">
        @csrf
        <x-input type="email" name="to" />
        <x-input type="email" name="cc" label="cc" />
        <x-input type="email" name="bcc" label="bcc" />
        <x-input type="text" name="subject" label="subject" />

        <input name="body" hidden class='email-body'></textarea>
        <div id="email-body-wysiwyg"></div>


        <button class='send-email'>Send</button>
    </div>
</div>