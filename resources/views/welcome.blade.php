<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberShoppe - Manage Your Business</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #f59e0b;
            --bg: #f8fafc;
            --text: #334155;
            --text-light: #64748b;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }

        /* Nav */
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; position: absolute; width: 100%; top: 0; left: 0; z-index: 10; }
        .logo { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .nav-links a { text-decoration: none; color: var(--text); margin-left: 20px; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: var(--accent); }
        .btn-nav { background: var(--primary); color: white !important; padding: 10px 25px; border-radius: 50px; }

        /* Hero */
        .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 0 20px; background: radial-gradient(circle at top right, #e2e8f0, transparent 40%), radial-gradient(circle at bottom left, #f1f5f9, transparent 40%); }
        .hero-content { max-width: 800px; animation: fadeInUp 1s ease; }
        h1 { font-size: 4rem; font-weight: 800; line-height: 1.1; margin-bottom: 20px; color: var(--primary); letter-spacing: -1px; }
        p.lead { font-size: 1.25rem; color: var(--text-light); margin-bottom: 40px; }
        
        .cta-group { display: flex; gap: 15px; justify-content: center; }
        .btn { display: inline-block; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2); }
        .btn-secondary { background: white; color: var(--primary); border: 1px solid #e2e8f0; }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }

        /* Features */
        .features { padding: 80px 50px; background: white; }
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-size: 2.5rem; margin-bottom: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; max-width: 1200px; margin: 0 auto; }
        .feature-card { padding: 30px; border-radius: 20px; background: var(--bg); transition: all 0.3s; border: 1px solid transparent; }
        .feature-card:hover { border-color: var(--accent); background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .icon { font-size: 2rem; margin-bottom: 20px; display: block; }
        
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <nav>
        <div class="logo">BarberShoppe.</div>
        <div class="nav-links">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn-nav">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="btn-nav">Get Started</a>
            @endauth
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>The Modern Way to <br>Manage Your Shop</h1>
            <p class="lead">Effortless scheduling, custom booking domains, and client management. All in one place.</p>
            <div class="cta-group">
                <a href="{{ route('register') }}" class="btn btn-primary">Start Free Trial</a>
                <a href="#features" class="btn btn-secondary">Learn More</a>
            </div>
            
            <div style="margin-top: 50px; opacity: 0.7;">
                <small>Demo Booking: <a href="{{ url('/book/joes-cuts') }}" style="color: var(--accent);">Try Joe's Cuts</a></small>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="section-title">
            <h2>Everything you need</h2>
        </div>
        <div class="grid">
            <div class="feature-card">
                <span class="icon">📅</span>
                <h3>Smart Scheduling</h3>
                <p>Avoid double bookings with our intelligent availability engine.</p>
            </div>
            <div class="feature-card">
                <span class="icon">🌍</span>
                <h3>Custom Domains</h3>
                <p>Connect your own domain (e.g., book.myshop.com) for a professional look.</p>
            </div>
            <div class="feature-card">
                <span class="icon">💈</span>
                <h3>Service Menu</h3>
                <p>Support for multiple services, durations, and pricing variations.</p>
            </div>
        </div>
    </section>

</body>
</html>
