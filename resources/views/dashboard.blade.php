<x-app-layout>
    
    @vite(['resources/css/dashboard.scss', 'resources/js/theme.js'])


    <div class='left-panel'>
        <div class='linked-accounts'>
            @foreach ($credentials as $credential)
                <div class='linked-account'>
                    <img src='{{ gravar($credential->username, 64) }}'>
                    {{ $credential->username }}
                    <hr>
                </div>
            @endforeach
        </div>
        <canvas id="email-count"></canvas>
    </div>

    <div class='new-emails'>
        @foreach ($emails as $email)
            @foreach ($credentials as $credential)
                @if ($email->credential_id == $credential->id)
                    @php $email->from = $credential->username @endphp
                @endif
            @endforeach

            @include('email_listing', [
        'email' => $email,
        'class' => 'message',
        'uuid' => uniqid('email-')
    ])
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <script>
        const xValues = [
            @foreach ($credentials as $credential)
                "{{ $credential->username }}",
            @endforeach
        ];
        const yValues = [
            @foreach ($credentials as $credential)
                {{ $emails->where('credential_id', $credential->id)->count() }},
            @endforeach
        ];
        const barColors = [
            "#2e60b1",
            "#b12e60",
            "#60b12e",
            "#b1602e",
        ];
        
        new Chart("email-count", {
          type: "pie",
          data: {
            labels: xValues,
            datasets: [{
              backgroundColor: barColors,
              data: yValues
            }]
          },
          options: {
            title: {
              display: true,
              text: "Email count per account"
            }
          }
        });
        </script>

</x-app-layout>