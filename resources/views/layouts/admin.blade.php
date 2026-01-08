<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --primary-hover: #1e293b;
            --accent: #f59e0b;
            --bg: #f8fafc;
            --text: #334155;
            --text-light: #64748b;
            --sidebar-w: 260px;
            --success: #10b981;
            --danger: #ef4444;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; outline: none; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

        /* Sidebar */
        aside {
            width: var(--sidebar-w);
            background: white;
            border-right: 1px solid #e2e8f0;
            padding: 30px 20px;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 100;
            overflow-y: auto;
        }
        
        .brand { font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 40px; display: block; text-decoration: none; padding-left: 10px; }
        
        .nav-item {
            display: flex; align-items: center; padding: 12px 15px;
            color: #64748b; text-decoration: none; border-radius: 10px;
            margin-bottom: 5px; font-weight: 500; transition: all 0.2s;
        }
        .nav-item:hover { background: #f1f5f9; color: var(--primary); }
        .nav-item.active { background: #f1f5f9; color: var(--primary); font-weight: 600; }

        /* Main Content */
        main {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            padding: 40px;
            min-height: 100vh;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }
        
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
        h1 { font-size: 2rem; color: var(--primary); font-weight: 700; margin: 0; }
        
        .card { 
            background: white; border-radius: 16px; padding: 30px; border: 1px solid #e2e8f0; margin-bottom: 30px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); 
            width: 100%; /* Ensure card fits */
            overflow-x: auto; /* Allow table scroll inside card */
        }
        
        /* ... existing styles ... */

        /* Responsive */
        @media (max-width: 768px) {
            aside { transform: translateX(-100%); transition: transform 0.3s; }
            aside.open { transform: translateX(0); }
            main { margin-left: 0; width: 100%; padding: 20px; }
            .mobile-toggle { display: block; position: fixed; bottom: 20px; right: 20px; z-index: 200; background: var(--primary); color: white; padding: 15px; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        }
    </style>
</head>
<body>

    <aside>
        <a href="{{ route('admin.dashboard') }}" class="brand">BarberShoppe.</a>
        
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.appointments.index') }}" class="nav-item {{ request()->routeIs('admin.appointments*') ? 'active' : '' }}">Appointments</a>
            <a href="{{ route('admin.customers.index') }}" class="nav-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">Customers</a>
            <a href="{{ route('admin.services.index') }}" class="nav-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}">Services</a>
            <a href="{{ route('admin.availability.index') }}" class="nav-item {{ request()->routeIs('admin.availability*') ? 'active' : '' }}">Schedule & Holidays</a>
            <a href="{{ route('admin.shop.edit') }}" class="nav-item {{ request()->routeIs('admin.shop*') ? 'active' : '' }}">Shop Settings</a>
        </nav>

        <form action="{{ route('logout') }}" method="POST" style="margin-top: auto;">
            @csrf
            <button type="submit" class="nav-item" style="width: 100%; border: none; background: none; font-family: inherit; font-size: inherit; cursor: pointer; color: var(--danger);">
                Sign Out
            </button>
        </form>
    </aside>

    <main>
        <header>
            <h1>@yield('header')</h1>
        </header>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="list-style: none;">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
