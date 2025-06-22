<header>
    <div class="flex items-center justify-around">
        <a href="{{ route('home') }}">
            home
        </a>
        @guest
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
        @endguest
        @auth
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-blue-500 hover:text-blue-700">
                logout
            </button>
        </form>
        @endauth
    </div>
      
</header>