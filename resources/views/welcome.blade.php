<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberShoppe | The Ultimate Shop Management Platform</title>
    <meta name="description" content="Elevate your barber shop with BarberShoppe. Smart scheduling, custom booking domains, and powerful client management in one premium platform.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: #4896bf;
            --primary-dark: #2677a5;
            --secondary: #ac48bf;
            --accent: #f59e0b;
        }

        body { 
            font-family: 'Instrument Sans', sans-serif;
            overflow-x: hidden;
        }

        h1, h2, h3, .font-display {
            font-family: 'Outfit', sans-serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .mesh-gradient {
            background-color: #ffffff;
            background-image: 
                radial-gradient(at 0% 0%, rgba(72, 150, 191, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(172, 72, 191, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(72, 150, 191, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(172, 72, 191, 0.15) 0px, transparent 50%);
        }

        .hero-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(72, 150, 191, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
            z-index: -1;
        }

        .bento-card {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .bento-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: rgba(72, 150, 191, 0.3);
        }

        .btn-premium {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-premium::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            transform: scale(0);
            transition: transform 0.6s ease;
        }

        .btn-premium:hover::after {
            transform: scale(1);
        }

        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 selection:bg-primary-100 selection:text-primary-900">

    <!-- Navigation -->
    <nav class="glass-nav fixed top-0 w-full z-[100] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black text-slate-900 font-display tracking-tight">Barber<span class="text-primary-500">Shoppe</span></span>
                </div>

                <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="#features" class="hover:text-primary-500 transition-colors">Features</a>
                    <a href="#how-it-works" class="hover:text-primary-500 transition-colors">How it works</a>
                    <a href="#demo" class="hover:text-primary-500 transition-colors">Demo</a>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn-premium px-4 sm:px-6 py-2 sm:py-2.5 bg-slate-900 text-white rounded-full font-bold text-xs sm:text-sm shadow-xl shadow-slate-900/20 hover:shadow-slate-900/30 whitespace-nowrap">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-xs sm:text-sm text-slate-600 hover:text-slate-900 px-2 sm:px-4 py-2">Log In</a>
                        <a href="{{ route('register') }}" class="btn-premium px-4 sm:px-6 py-2 sm:py-2.5 bg-primary-500 text-white rounded-full font-bold text-xs sm:text-sm shadow-xl shadow-primary-500/20 hover:shadow-primary-500/30 whitespace-nowrap">Start Trial</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden mesh-gradient">
        <div class="hero-glow"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="text-left">
                    <h1 class="text-6xl lg:text-7xl font-black text-[#1e445d] leading-[1.1] mb-8 font-display reveal" style="transition-delay: 100ms">
                        The Modern Way to <span class="text-gradient">Manage Your Shop.</span>
                    </h1>
                    <p class="text-xl text-slate-600 leading-relaxed mb-10 max-w-xl reveal" style="transition-delay: 200ms">
                        Effortless scheduling, custom booking domains, and powerful client management. Everything to run your shop at peak performance.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center reveal" style="transition-delay: 300ms">
                        <a href="{{ route('register') }}" class="btn-premium inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold text-lg shadow-2xl shadow-slate-900/20 group">
                            Start Your 7-Day Free Trial
                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <a href="#demo" class="inline-flex items-center justify-center px-8 py-4 bg-white text-slate-900 border border-slate-200 rounded-2xl font-bold text-lg hover:bg-slate-50 transition-colors">
                            Demo
                        </a>
                    </div>
                </div>
                <div class="relative reveal" style="transition-delay: 400ms">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary-500/20 to-secondary-500/20 blur-3xl rounded-[3rem] -z-10"></div>
                    
                    <!-- Decorative CSS Dashboard Mockup -->
                    <div class="w-full aspect-[4/3] bg-white/40 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 p-6 floating relative overflow-hidden">
                        <div class="flex items-center gap-2 mb-8">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2 space-y-4">
                                <div class="h-32 bg-slate-900/5 rounded-2xl w-full"></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="h-24 bg-primary-100/30 rounded-2xl"></div>
                                    <div class="h-24 bg-secondary-100/30 rounded-2xl"></div>
                                </div>
                                <div class="h-40 bg-slate-900/5 rounded-2xl w-full"></div>
                            </div>
                            <div class="col-span-1 space-y-4">
                                <div class="h-full bg-slate-900/5 rounded-2xl w-full"></div>
                            </div>
                        </div>
                        
                        <!-- Floating Accent -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-primary-500/10 blur-3xl rounded-full"></div>
                    </div>
                    
                    <!-- Floating Stat Badge -->
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-2xl border border-slate-100 flex items-center gap-4 animate-bounce-slow">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Revenue Growth</div>
                            <div class="text-xl font-black text-slate-900">+24.8%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Bento Grid -->
    <section id="features" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal">
                <h2 class="text-4xl lg:text-5xl font-black text-[#1e445d] mb-6 font-display">Tools crafted for elite barbers.</h2>
                <p class="text-lg text-slate-600">We tackle the administrative headache so you can focus on the artistry of the cut.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                <!-- Large Feature -->
                <div class="md:col-span-4 bento-card p-10 flex flex-col justify-between group reveal">
                    <div>
                        <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold mb-4">Smart Dynamic Scheduling</h3>
                        <p class="text-slate-600 text-lg max-w-md mb-8">Out intelligent engine syncs between stylists, handles breaks, and prevents double bookings automatically. Clients see real-time availability on your bespoke booking page.</p>
                    </div>
                    <div class="relative rounded-2xl overflow-hidden h-64 border border-slate-100 bg-slate-50 p-6">
                         <!-- CSS Schedule Visualization -->
                         <div class="space-y-3">
                            <div class="flex gap-4">
                                <div class="w-16 h-4 bg-primary-100 rounded-full"></div>
                                <div class="flex-1 h-12 bg-white rounded-xl border border-primary-100/50 shadow-sm p-2 flex items-center px-4">
                                    <div class="w-32 h-2 bg-slate-100 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-16 h-4 bg-slate-100 rounded-full"></div>
                                <div class="flex-1 h-32 bg-white rounded-xl border border-slate-100 shadow-sm p-4 relative overflow-hidden">
                                     <div class="absolute inset-0 bg-primary-500/5"></div>
                                     <div class="w-full h-full border-2 border-dashed border-primary-200 rounded-lg flex items-center justify-center">
                                        <div class="text-primary-400 font-bold text-xs uppercase tracking-widest">Dynamic Time Slots</div>
                                     </div>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>

                <!-- Custom Domains Feature -->
                <div class="md:col-span-2 bento-card p-10 group reveal" style="transition-delay: 100ms">
                    <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.14L3 18l1.708-2.897M16.5 12c0-1.269-.54-2.412-1.406-3.215m-4.14 8.213A4.47 4.47 0 0110 16.5c0-2.485 2.015-4.5 4.5-4.5S19 14.015 19 16.5a4.47 4.47 0 01-1.037 2.897L15.313 19.5h-1.312l-1.313-1.313a4.47 4.47 0 01-.187-1.187z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-slate-900">Custom Domains</h3>
                    <p class="text-slate-600 leading-relaxed">Ditch the generic subdomains. Connect your own brand (e.g., book.yourshop.com) for a truly premium client experience.</p>
                    <div class="mt-8 pt-8 border-t border-slate-100 flex items-center gap-3">
                         <div class="w-8 h-8 rounded-full bg-green-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                         </div>
                         <span class="text-sm font-bold text-slate-500">SSL Included</span>
                    </div>
                </div>

                <!-- Medium Feature -->
                <div class="md:col-span-3 bento-card p-10 group reveal" style="transition-delay: 200ms">
                    <div class="w-14 h-14 bg-secondary-50 rounded-2xl flex items-center justify-center text-secondary-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Revenue Analytics</h3>
                    <p class="text-slate-600 leading-relaxed">Track weekly performance, top-performing stylists, and appointment trends at a glance. Make data-driven decisions to grow profits.</p>
                </div>

                <!-- Medium Feature -->
                <div class="md:col-span-3 bento-card p-10 group reveal" style="transition-delay: 300ms">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Client Portal</h3>
                    <p class="text-slate-600 leading-relaxed">Give your clients a frictionless way to view, reschedule, or cancel their bookings without picking up the phone.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- How It Works -->
    <section id="how-it-works" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal">
                <h2 class="text-4xl lg:text-5xl font-black text-[#1e445d] mb-6 font-display">Simple setup. <span class="text-primary-500">Powerful results.</span></h2>
                <p class="text-lg text-slate-600">Get your shop online and ready for business in less than 10 minutes. No technical skills required.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Step 1 -->
                <div class="relative group reveal" style="transition-delay: 100ms">
                    <div class="absolute -top-6 -left-6 text-9xl font-black text-slate-50 opacity-[0.03] select-none group-hover:text-primary-500/10 transition-colors">01</div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center mb-8 shadow-xl shadow-slate-900/20 group-hover:-translate-y-1 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-slate-900">Configure Your Shop</h3>
                        <p class="text-slate-600 leading-relaxed">Add your services, pricing, and staff members. Set individual schedules and break times with ease.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative group reveal" style="transition-delay: 200ms">
                    <div class="absolute -top-6 -left-6 text-9xl font-black text-slate-50 opacity-[0.03] select-none group-hover:text-primary-500/10 transition-colors">02</div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-primary-500 text-white rounded-2xl flex items-center justify-center mb-8 shadow-xl shadow-primary-500/20 group-hover:-translate-y-1 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-slate-900">Launch Your Brand</h3>
                        <p class="text-slate-600 leading-relaxed">Connect a custom domain or use your free BarberShoppe booking URL. Customize colors to match your shop's vibe.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative group reveal" style="transition-delay: 300ms">
                    <div class="absolute -top-6 -left-6 text-9xl font-black text-slate-50 opacity-[0.03] select-none group-hover:text-primary-500/10 transition-colors">03</div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-secondary-500 text-white rounded-2xl flex items-center justify-center mb-8 shadow-xl shadow-secondary-500/20 group-hover:-translate-y-1 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-slate-900">Take Bookings</h3>
                        <p class="text-slate-600 leading-relaxed">Everything is live! Clients can now book appointments 24/7. Manage everything from your unified shop dashboard.</p>
                    </div>
                </div>
            </div>
            
            <!-- Connection Line (Desktop) -->
            <div class="hidden md:block absolute top-[28rem] left-[10%] right-[10%] h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent -z-10"></div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
            <div class="bg-white rounded-[3rem] p-12 lg:p-20 shadow-2xl shadow-slate-200 border border-slate-100 flex flex-col lg:flex-row items-center gap-16 reveal">
                <div class="lg:w-1/2">
                    <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-8 font-display">Experience the client journey.</h2>
                    <p class="text-lg text-slate-600 mb-10">Don't take our word for it. Try out a live booking page as if you were a customer at 'Joe's Cuts'. It's fast, mobile-first, and incredibly smooth.</p>
                    <div class="flex flex-col gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex-shrink-0 flex items-center justify-center text-primary-600 font-bold">1</div>
                            <p class="text-slate-700 font-medium">Click the demo link below to open a sample shop.</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex-shrink-0 flex items-center justify-center text-primary-600 font-bold">2</div>
                            <p class="text-slate-700 font-medium">Select a service and pick a stylist.</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex-shrink-0 flex items-center justify-center text-primary-600 font-bold">3</div>
                            <p class="text-slate-700 font-medium">Confirm the booking in under 30 seconds.</p>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 flex flex-col items-center">
                    <a href="{{ url('/book/joes-cuts') }}" target="_blank" class="group relative block">
                        <div class="absolute inset-0 bg-primary-500 rounded-full blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="relative w-48 h-48 lg:w-64 lg:h-64 bg-slate-900 rounded-full flex flex-col items-center justify-center text-center p-8 transition-transform group-hover:scale-105 active:scale-95 duration-500">
                            <svg class="w-16 h-16 text-primary-400 mb-4 group-hover:rotate-12 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                            <span class="text-white font-black text-xl leading-tight">LIVE DEMO</span>
                            <span class="text-slate-400 text-sm font-bold mt-1">CLICK TO TRY</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 relative overflow-hidden bg-slate-900">
        <div class="absolute top-0 left-0 w-full h-full opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary-600 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary-600 rounded-full blur-[120px]"></div>
        </div>
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10 reveal">
            <h2 class="text-5xl lg:text-7xl font-black text-white mb-8 font-display">Ready to level up?</h2>
            <p class="text-xl text-slate-300 mb-12">Join the barbers who have reclaimed their time and improved their professional image with BarberShoppe.</p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                 <a href="{{ route('register') }}" class="btn-premium px-10 py-5 bg-primary-500 text-white rounded-2xl font-black text-xl shadow-2xl shadow-primary-500/20">Start Your Free Trial</a>
                 <a href="{{ route('login') }}" class="px-10 py-5 bg-white/10 text-white border border-white/20 rounded-2xl font-black text-xl hover:bg-white/20 transition-all">Sign In to Account</a>
            </div>
            <p class="mt-8 text-slate-500 font-bold">No credit card required. Cancel anytime.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-white pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-8">
                        <span class="text-2xl font-black font-display">BarberShoppe</span>
                    </div>
                    <p class="text-slate-400 max-w-sm mb-8 leading-relaxed">The all-in-one platform for modern barbers and salons to manage schedule, clients, and growth with elegance and ease.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-white uppercase tracking-widest text-sm">Product</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li><a href="#features" class="hover:text-primary-400 transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-primary-400 transition-colors">How It Works</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-slate-500 text-sm">© {{ date('Y') }} BarberShoppe by Hulmify. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Simple scroll reveal animation
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

        // Sticky nav effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('py-4', 'shadow-lg');
                nav.classList.remove('h-20');
            } else {
                nav.classList.remove('py-4', 'shadow-lg');
                nav.classList.add('h-20');
            }
        });
    </script>

</body>
</html>
