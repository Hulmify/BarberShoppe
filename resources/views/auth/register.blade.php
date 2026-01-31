<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BarberShoppe</title>
    @include('partials.pwa')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; font-family: 'Outfit', sans-serif; background: #f1f5f9; display: flex; height: 100vh; align-items: center; justify-content: center; }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #ffffff;
        }

        h1 { margin-bottom: 5px; color: #0f172a; }
        p { color: #64748b; margin-bottom: 30px; font-size: 0.95rem; }

        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.9rem; color: #334155; }
        input { 
            width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; 
            font-family: inherit; font-size: 1rem; transition: border-color 0.2s; box-sizing: border-box; 
        }
        input:focus { border-color: #0f172a; outline: none; }

        button {
            width: 100%;
            background: #0f172a;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 10px;
        }
        button:hover { transform: translateY(-2px); }

        .links { margin-top: 20px; font-size: 0.9rem; }
        .links a { color: #64748b; text-decoration: none; }
        .links a:hover { color: #0f172a; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1>Get Started</h1>
        <p>Create your shop account</p>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #ef4444; padding: 10px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; text-align: left;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="john@example.com" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
                <small style="color: #64748b; font-size: 0.75rem;">Min 8 chars, mixed case, symbol & number.</small>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit">Create Account</button>
        </form>

        <div class="links">
            <a href="{{ route('login') }}">Already have an account? Sign In</a>
        </div>
    </div>
</body>
</html>
