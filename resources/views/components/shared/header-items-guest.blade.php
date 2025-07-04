<a href="{{ route('login') }}">
    login
</a>
<a href="{{ route('register') }}">
    register
</a>
<a href="{{ route('redirect',['provider' => 'google']) }}">
    login google redirect
</a>
<a href="{{ route('callback',['provider' => 'google']) }}">
    login google callback
</a>