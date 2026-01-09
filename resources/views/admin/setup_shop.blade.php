<!DOCTYPE html>
<html>
<head>
    <title>Setup Shop</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f2f5; padding: 20px; }
        form { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 450px; }
        h1 { margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; }
        .logo-preview-container { text-align: center; margin-bottom: 20px; }
        #logo-preview { max-width: 100px; max-height: 100px; display: none; margin: 10px auto; border-radius: 8px; }
    </style>
</head>
<body>
    <form method="POST" action="{{ route('admin.shop.store') }}" enctype="multipart/form-data">
        <h1>Setup Your Shop</h1>
        <p>Create your shop profile to get started.</p>
        @csrf
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        
        <div class="form-group">
            <label>Brand Logo</label>
            <input type="file" name="logo" accept="image/*" onchange="document.getElementById('logo-preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('logo-preview').style.display='block';">
            <img id="logo-preview" src="#" alt="Logo Preview">
        </div>

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

        <div class="form-group">
            <label>Timezone</label>
            <select name="timezone" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                @foreach(DateTimeZone::listIdentifiers() as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', 'Asia/Kolkata') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Create Shop</button>
    </form>
</body>
</html>
