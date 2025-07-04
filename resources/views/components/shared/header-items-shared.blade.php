<a href="{{ route('profile.show') }}">
    profile 
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="text-blue-500 hover:text-blue-700">
        logout
    </button>
</form>