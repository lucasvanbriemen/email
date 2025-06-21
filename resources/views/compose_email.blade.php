@vite(['resources/css/email/compose_email.scss', 'resources/js/email/compose_email.js'])

<div class="compose-email-wrapper">
    <div class="compose-email-background"></div>

    <div class="compose-email">
        @csrf
        <input type="text" name="to" value="" placeholder="To">
        <input type="text" name="cc" value="" placeholder="Cc">
        <input type="text" name="bcc" value="" placeholder="Bcc">
        <input type="text" name="subject" value="" placeholder="Subject">
        
        <input name="body" hidden class='email-body'></textarea>
        <div id="email-body-wysiwyg"></div>


        <button class='send-email'>Send</button>


        <script> 
        </script>
    </div>
</div>