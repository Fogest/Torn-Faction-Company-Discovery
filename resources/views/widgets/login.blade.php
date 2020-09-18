<form method="POST" action="/login" class="api-login-form">
    @csrf
    <label>
        Company Manager? Plop that API key in for some extra control <input type="text" placeholder="Torn API Key" name="api_key" required>
    </label>
</form>
