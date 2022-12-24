@component('mail::message')

   Welcome to our application
    {{ $user->name }},
    @component('mail::button',
     ['url' => '/'])
        Click to Verify your email address
    @endcomponent
    </a>
    </p>
    Thanks,
    <br>{{ config('app.name') }}
@endcomponent
