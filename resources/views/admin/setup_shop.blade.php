<!DOCTYPE html>
<html>
<head>
    <title>Setup Shop</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        form { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; }
        h1 { margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form method="POST" action="{{ route('admin.shop.store') }}">
        <h1>Setup Your Shop</h1>
        <p>Create your shop profile to get started.</p>
        @csrf
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        
        <div class="form-group">
            <label>Shop Name</label>
            <input type="text" name="name" required placeholder="e.g. Ace Barbers" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label>Slug (URL Identifier)</label>
            <input type="text" name="slug" required placeholder="ace-barbers" value="{{ old('slug') }}">
            <small>Your booking URL: {{ config('app.url') }}/book/<b>slug</b></small>
        </div>

        <div class="form-group">
            <label>Primary Color</label>
            <input type="color" name="primary_color" value="{{ old('primary_color', '#000000') }}" style="height: 40px;">
        </div>

        <button type="submit">Create Shop</button>
    </form>
</body>
</html>
