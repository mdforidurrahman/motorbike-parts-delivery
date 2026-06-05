<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MotoLink') }} - Motorcycle Parts Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Custom CSS for landing page */
                .company-name {
                    font-size: 24px;
                    font-weight: bold;
                    background: linear-gradient(135deg, #f53003, #ff8c00);
                    -webkit-background-clip: text;
                    background-clip: text;
                    color: transparent;
                }
                
                /* Logo Image Style */
                .logo-image {
                    width: 40px;
                    height: 40px;
                    object-fit: contain;
                    border-radius: 8px;
                }
                
                /* Footer Styles */
                .footer {
                    margin-top: 60px;
                    padding: 30px 0;
                    border-top: 1px solid rgba(0,0,0,0.1);
                    text-align: center;
                    width: 100%;
                }
                
                .dark .footer {
                    border-top-color: rgba(255,255,255,0.1);
                }
                
                .creator-info {
                    display: flex;
                    justify-content: center;
                    gap: 30px;
                    flex-wrap: wrap;
                    margin-top: 20px;
                }
                
                .creator-card {
                    text-align: center;
                    padding: 10px;
                }
                
                .creator-card i {
                    font-size: 24px;
                    color: #f53003;
                    margin-bottom: 8px;
                }
                
                .creator-name {
                    font-weight: 600;
                    margin-bottom: 4px;
                    color: #1b1b18;
                }
                
                .dark .creator-name {
                    color: #FFFFFF !important;
                }
                
                .creator-designation {
                    font-size: 12px;
                    color: #706f6c;
                }
                
                .dark .creator-designation {
                    color: #CCCCCC !important;
                }
                
                .social-links {
                    margin-top: 20px;
                }
                
                .social-links a {
                    color: #706f6c;
                    margin: 0 10px;
                    font-size: 18px;
                    transition: color 0.3s;
                }
                
                .dark .social-links a {
                    color: #FFFFFF !important;
                }
                
                .social-links a:hover {
                    color: #f53003 !important;
                }
                
                .version {
                    font-size: 12px;
                    margin-top: 20px;
                }
                
                .version p {
                    color: #706f6c;
                    margin: 5px 0;
                }
                
                .dark .version p {
                    color: #DDDDDD !important;
                }
                
                .dark .version strong {
                    color: #f53003 !important;
                }
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        
        <!-- Header -->
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- Logo from URL --}}
                    <img src="https://smsunlight.com/wp-content/uploads/2024/08/Main-Design-file-illustrator-2023-222.png" 
                         alt="SM Sunlight Group Logo" 
                         class="logo-image"width="40" height="40"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/40x40?text=Logo';">
                    
                    <div>
                        <h1 class="company-name">SM Sunlight Group</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bike Parts Management System</p>
                    </div>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                <i class="fas fa-sign-in-alt"></i> Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    <i class="fas fa-user-plus"></i> Register
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    
                    <div class="mb-6">
                        <span class="inline-block px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300 rounded-full text-xs mb-3">
                            <i class="fas fa-rocket"></i> Welcome to
                        </span>
                        <h1 class="text-2xl font-bold mb-2">SM Sunlight Group</h1>
                        <h2 class="text-xl font-semibold mb-4 text-[#f53003]">Bike Parts Management System</h2>
                        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">
                            Your complete solution for motorcycle parts ordering, delivery management, 
                            and inventory tracking. Connect small shops with contracted suppliers seamlessly.
                        </p>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Key Features:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Real-time order tracking</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Automated delivery management</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Digital wallet & payment system</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Area-wise rider assignment</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Inventory & stock management</span>
                            </li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#f53003]">50+</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Outlets</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#f53003]">20+</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Riders</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#f53003]">1000+</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Orders Completed</div>
                        </div>
                    </div>

                    <div class="flex gap-3 flex-wrap">
                        @guest
                            <a href="{{ route('register') }}" class="inline-block bg-[#1b1b18] hover:bg-black text-white px-6 py-2 rounded-md text-sm transition">
                                <i class="fas fa-user-plus"></i> Get Started
                            </a>
                            <a href="{{ route('login') }}" class="inline-block border border-gray-300 hover:border-black dark:border-gray-600 dark:hover:border-white px-6 py-2 rounded-md text-sm transition">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-block bg-[#1b1b18] hover:bg-black text-white px-6 py-2 rounded-md text-sm transition">
                                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right Side - Illustration with Logo SVG --}}
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden flex items-center justify-center">
                    
                    {{-- Your Company Logo Large --}}
                    <img src="https://smsunlight.com/wp-content/uploads/2024/08/Main-Design-file-illustrator-2023-222.png" 
                         alt="SM Sunlight Group Logo" 
                         class="w-3/4 h-auto object-contain p-4"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/300x300?text=SM+Sunlight+Group';">
                    
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                </div>
            </main>
        </div>

        <!-- Footer -->
        <footer class="footer w-full lg:max-w-4xl max-w-[335px]">
            <div class="creator-info">
                <div class="creator-card">
                    <i class="fas fa-user-circle"></i>
                    <div class="creator-name">Md. Foridur Rahman</div>
                    <div class="creator-designation">Software Engineer (Web & Mobile)</div>
                </div>
                <div class="creator-card">
                    <i class="fas fa-code"></i>
                    <div class="creator-name">SM Sunlight Group</div>
                    <div class="creator-designation">Development Team</div>
                </div>
                <div class="creator-card">
                    <i class="fas fa-project-diagram"></i>
                    <div class="creator-name">Project Manager</div>
                    <div class="creator-designation">System Administration</div>
                </div>
            </div>
            
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
                <a href="#" target="_blank"><i class="fab fa-github"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
            </div>
            
            <div class="version">
                <p>Version 1.0.0 | &copy; 2026 SM Sunlight Group. All rights reserved.</p>
                <p>Developed by <strong>SM Sunlight Group</strong> | Powered by Laravel</p>
            </div>
        </footer>

    </body>
</html>