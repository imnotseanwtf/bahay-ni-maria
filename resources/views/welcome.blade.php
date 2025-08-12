<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bahay ni Maria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, rgba(0, 120, 166, 0.9), rgba(93, 224, 230, 0.8), rgba(0, 120, 166, 0.9));
        }
        
        .nav-gradient {
            background: linear-gradient(90deg, #0078a6, #5de0e6, #0078a6);
        }
        
        .btn-gradient {
            background: linear-gradient(90deg, #5de0e6, #0078a6);
            box-shadow: 0px 2px 8px rgba(54, 122, 246, 0.3), inset 0px 1px 0px rgba(255, 255, 255, 0.2);
        }
        
        .hero-bg {
            background-image: 
                linear-gradient(135deg, rgba(0, 120, 166, 0.7), rgba(93, 224, 230, 0.6), rgba(0, 120, 166, 0.7)),
                url({{ asset('images/homepage.jpg') }});
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .text-shadow {
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
        }
        
        .nav-link-hover:hover {
            transform: translateY(-1px);
            transition: all 0.4s ease;
        }
        
        .hero-content {
            animation: fadeInUp 1.2s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .floating-cards {
            position: absolute;
            top: 20%;
            right: 5%;
            width: 300px;
        }
        
        .card-float {
            animation: float 8s ease-in-out infinite;
        }
        
        .card-float:nth-child(2) {
            animation-delay: 2.5s;
        }
        
        .card-float:nth-child(3) {
            animation-delay: 5s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        .smooth-rounded {
            border-radius: 20px;
        }
        
        .extra-smooth-rounded {
            border-radius: 25px;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="nav-gradient shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="text-white hover:text-gray-200 transition-colors duration-300">
                        <h3 class="text-2xl lg:text-3xl font-bold tracking-wide">
                            Bahay ni Maria
                        </h3>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-white hover:text-gray-200 nav-link-hover px-4 py-2 smooth-rounded font-medium text-lg transition-all duration-500">
                        Home
                    </a>
                    <a href="/about" class="text-white hover:text-gray-200 nav-link-hover px-4 py-2 smooth-rounded font-medium text-lg transition-all duration-500">
                        About Us
                    </a>
                    <a href="/auth/login" class="btn-gradient text-white px-8 py-3 extra-smooth-rounded font-semibold text-lg hover:scale-105 transition-transform duration-500 shadow-lg">
                        Login
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="flex flex-col space-y-4">
                    <a href="/" class="text-white hover:text-gray-200 px-4 py-3 smooth-rounded font-medium text-lg transition-all duration-500 bg-white/20">
                        Home
                    </a>
                    <a href="/about" class="text-white hover:text-gray-200 px-4 py-3 smooth-rounded font-medium text-lg transition-all duration-500">
                        About Us
                    </a>
                    <a href="/auth/login" class="btn-gradient text-white px-8 py-4 extra-smooth-rounded font-semibold text-lg text-center transition-all duration-500">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Floating Cards -->
        <div class="floating-cards hidden lg:block">
            <div class="card-float bg-white/15 backdrop-blur-md extra-smooth-rounded p-6 mb-6 shadow-2xl border border-white/20">
                <div class="text-white text-center">
                    <div class="text-3xl mb-3">🏠</div>
                    <h4 class="font-semibold text-lg mb-2">Caring Community</h4>
                    <p class="text-sm opacity-90 leading-relaxed">Building hope through compassionate care</p>
                </div>
            </div>
            <div class="card-float bg-white/15 backdrop-blur-md extra-smooth-rounded p-6 mb-6 shadow-2xl border border-white/20">
                <div class="text-white text-center">
                    <div class="text-3xl mb-3">🤗</div>
                    <h4 class="font-semibold text-lg mb-2">Safe Haven</h4>
                    <p class="text-sm opacity-90 leading-relaxed">A home where love knows no boundaries</p>
                </div>
            </div>
            <div class="card-float bg-white/15 backdrop-blur-md extra-smooth-rounded p-6 shadow-2xl border border-white/20">
                <div class="text-white text-center">
                    <div class="text-3xl mb-3">💝</div>
                    <h4 class="font-semibold text-lg mb-2">Inclusive Care</h4>
                    <p class="text-sm opacity-90 leading-relaxed">Embracing every individual with dignity</p>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="container mx-auto px-4 lg:px-8">
            <div class="hero-content text-center lg:text-left lg:max-w-4xl">
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold text-white mb-6 text-shadow leading-tight">
                    BAHAY NI MARIA
                </h1>
                <p class="text-lg sm:text-xl lg:text-2xl text-white mb-8 font-medium text-shadow leading-relaxed tracking-wide opacity-95">
                    HOME FOR THE ABANDONED ELDERLY AND <br class="hidden sm:block"> 
                    CHILDREN WITH SPECIAL NEEDS
                </p>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-10 left-10 w-24 h-24 bg-white/5 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-cyan-200/10 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute top-1/2 left-1/4 w-20 h-20 bg-blue-300/8 rounded-full blur-xl animate-pulse"></div>
    </div>

    <!-- Quick Stats Section -->
    <div class="bg-gradient-to-r from-blue-800 to-cyan-500 py-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                <div class="text-white">
                    <div class="text-5xl mb-4">👥</div>
                    <h3 class="text-4xl font-bold mb-3">50+</h3>
                    <p class="text-xl opacity-90 font-medium">Residents Cared For</p>
                </div>
                <div class="text-white">
                    <div class="text-5xl mb-4">🏆</div>
                    <h3 class="text-4xl font-bold mb-3">15</h3>
                    <p class="text-xl opacity-90 font-medium">Years of Service</p>
                </div>
                <div class="text-white">
                    <div class="text-5xl mb-4">🕐</div>
                    <h3 class="text-4xl font-bold mb-3">24/7</h3>
                    <p class="text-xl opacity-90 font-medium">Care & Support</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>

</html>