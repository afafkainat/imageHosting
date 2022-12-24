@component('mail::message')

   Reset Your Password
    {{ $user->name }},
    @component('mail::button',
     ['url' => '/'])
        Click here
    @endcomponent
    </a>
    </p>
    Thanks to choose our application
    <br>{{ config('app.name') }}
@endcomponent

