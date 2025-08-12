<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - Bahay ni Maria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .nav-gradient {
            background: linear-gradient(90deg, #0078a6, #5de0e6, #0078a6);
        }
        
        .btn-gradient {
            background: linear-gradient(90deg, #5de0e6, #0078a6);
            box-shadow: 0px 2px 8px rgba(54, 122, 246, 0.3), inset 0px 1px 0px rgba(255, 255, 255, 0.2);
        }
        
        .hero-about-bg {
            background-image: 
                linear-gradient(135deg, rgba(0, 120, 166, 0.6), rgba(93, 224, 230, 0.5), rgba(0, 120, 166, 0.6)),
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
        
        .smooth-rounded {
            border-radius: 20px;
        }
        
        .extra-smooth-rounded {
            border-radius: 25px;
        }
        
        .section-fade-in {
            animation: fadeInUp 1s ease-out;
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
        
        .card-hover:hover {
            transform: translateY(-10px);
            transition: all 0.5s ease;
        }
        
        .mission-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .charism-card {
            background: linear-gradient(135deg, rgba(0, 120, 166, 0.9), rgba(93, 224, 230, 0.8));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .value-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
            backdrop-filter: blur(15px);
            border: 1px solid rgba(0, 120, 166, 0.2);
            transition: all 0.5s ease;
        }
        
        .value-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 120, 166, 0.2);
        }
        
        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 30px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #0078a6, #5de0e6);
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px rgba(0, 120, 166, 0.2);
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 7px;
            top: 24px;
            width: 2px;
            height: calc(100% + 6px);
            background: linear-gradient(180deg, #0078a6, #5de0e6);
            opacity: 0.3;
        }
        
        .timeline-item:last-child::after {
            display: none;
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
                    <a href="/about" class="text-white hover:text-gray-200 nav-link-hover px-4 py-2 smooth-rounded font-medium text-lg transition-all duration-500 bg-white/20">
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
                    <a href="/" class="text-white hover:text-gray-200 px-4 py-3 smooth-rounded font-medium text-lg transition-all duration-500">
                        Home
                    </a>
                    <a href="/about" class="text-white hover:text-gray-200 px-4 py-3 smooth-rounded font-medium text-lg transition-all duration-500 bg-white/20">
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
    <div class="hero-about-bg min-h-screen flex items-center justify-center relative">
        <div class="container mx-auto px-4">
            <div class="text-center section-fade-in">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 text-shadow leading-tight">
                    About Us
                </h1>
                <p class="text-lg sm:text-xl lg:text-2xl text-white mb-8 font-medium text-shadow leading-relaxed max-w-4xl mx-auto">
                    Dedicated to serving God and the Church through compassionate care for the abandoned elderly and children with special needs
                </p>
                <div class="flex justify-center">
                    <div class="w-24 h-1 bg-white rounded-full opacity-80"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission & Vision Section -->
    <div class="py-20 bg-gradient-to-br from-blue-50 to-cyan-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Mission Card -->
                <div class="mission-card extra-smooth-rounded p-8 lg:p-10 shadow-2xl card-hover">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">🎯</div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Mission & Vision</h2>
                    </div>
                    <div class="text-gray-700 leading-relaxed">
                        <p class="text-lg font-medium mb-4 text-center italic text-blue-800">
                            "Fullness of life, from crib to grave, for the abandoned"
                        </p>
                        <p class="text-base leading-relaxed">
                            This is the essential character of the Missionary Sisters of Our Lady of Fatima. Therefore it shall not be changed in substance and must continue to give inspiration to the form of life and various apostolate of the congregation now and always.
                        </p>
                    </div>
                </div>

                <!-- Charism Card -->
                <div class="charism-card extra-smooth-rounded p-8 lg:p-10 shadow-2xl card-hover">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">💫</div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Our Charism</h2>
                    </div>
                    <div class="text-white leading-relaxed">
                        <p class="text-base leading-relaxed">
                            Imbued with the charism of the founder and the Superior General, and the simplicity of the Blessed Virgin Mary, every member desires the institute's goal of serving God and the Church in the person of the abandoned elderly, older persons with special needs and children.
                        </p>
                        <p class="text-base leading-relaxed mt-4">
                            Preventive approach to the problem of the poor and the abandoned constitutes our related ministries through education and formation of families.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Values Section -->
    <div class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Our Core Values</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    The principles that guide our daily mission and shape our approach to caring for those in need
                </p>
            </div>
            
            <div class="values-grid">
                <div class="value-card extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">🤲</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Compassion</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We approach every individual with deep empathy, understanding their unique needs and providing care with genuine love and kindness.
                    </p>
                </div>

                <div class="value-card extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">🙏</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Faith</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        Our work is grounded in faith and guided by the teachings of the Catholic Church, inspired by the Blessed Virgin Mary.
                    </p>
                </div>

                <div class="value-card extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">🏠</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Family</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We create a warm, family-like environment where every resident feels loved, valued, and truly at home.
                    </p>
                </div>

                <div class="value-card extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">🌟</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Dignity</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We honor the inherent dignity of every person, regardless of age, condition, or circumstances.
                    </p>
                </div>

                <div class="value-card extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">💕</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Service</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We dedicate ourselves wholeheartedly to serving others, following the example of Christ's selfless love.
                    </p>
                </div>

                <div class="value-card extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">🌈</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Hope</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We bring hope to those who have been abandoned, showing them that they are not forgotten and are deeply loved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Who We Serve Section -->
    <div class="py-20 bg-gradient-to-br from-cyan-50 to-blue-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Who We Serve</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Our doors are open to those who need us most, providing comprehensive care and support
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white/80 backdrop-blur-sm extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">👴👵</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Abandoned Elderly</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We provide a loving home for elderly individuals who have been abandoned or have no family support, ensuring they receive the care, dignity, and companionship they deserve in their golden years.
                    </p>
                </div>

                <div class="bg-white/80 backdrop-blur-sm extra-smooth-rounded p-8 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">🧒💙</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Children with Special Needs</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-center">
                        We offer specialized care and support for children with special needs, creating an environment where they can grow, learn, and thrive with the love and attention they require.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Approach Section -->
    <div class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Our Approach</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    We believe in a holistic approach to care that addresses not just physical needs, but emotional, spiritual, and social well-being
                </p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="timeline-item">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Preventive Care</h3>
                    <p class="text-gray-600 leading-relaxed">
                        We focus on preventing problems before they occur through education, family formation, and early intervention programs.
                    </p>
                </div>

                <div class="timeline-item">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Holistic Support</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Our care encompasses physical health, emotional well-being, spiritual growth, and social connections.
                    </p>
                </div>

                <div class="timeline-item">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Family-Centered Environment</h3>
                    <p class="text-gray-600 leading-relaxed">
                        We create a warm, family-like atmosphere where residents feel truly at home and part of a loving community.
                    </p>
                </div>

                <div class="timeline-item">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Community Integration</h3>
                    <p class="text-gray-600 leading-relaxed">
                        We work to integrate our residents into the broader community, fostering connections and meaningful relationships.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="py-20 bg-gradient-to-r from-blue-800 to-cyan-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">Join Us in Our Mission</h2>
            <p class="text-lg text-white opacity-90 mb-8 max-w-2xl mx-auto">
                Whether through volunteering, donations, or simply spreading awareness, you can help us continue our work of bringing hope and love to those who need it most.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="bg-white text-blue-800 px-8 py-4 extra-smooth-rounded font-semibold text-lg hover:scale-105 transition-transform duration-500 shadow-xl">
                    Get Involved
                </button>
                <button class="bg-white/20 backdrop-blur-md text-white px-8 py-4 extra-smooth-rounded font-semibold text-lg hover:bg-white/30 transition-all duration-500 shadow-xl border border-white/30">
                    Contact Us
                </button>
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

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('section-fade-in');
                }
            });
        }, observerOptions);

        // Observe all sections
        document.querySelectorAll('.value-card, .timeline-item').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>