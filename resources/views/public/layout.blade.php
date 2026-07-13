<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jude Health') - St. Jude General Hospital</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-gray-800 antialiased font-sans">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Brand Logo -->
                <a href="/" class="flex items-center space-x-3">
                    <svg class="h-10 w-10 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                    </svg>
                    <span class="text-xl font-bold uppercase tracking-wider text-brand-900">Jude Health</span>
                </a>

                <!-- Nav links -->
                <nav class="hidden md:flex space-x-6 text-sm font-semibold text-gray-600">
                    <a href="/" class="hover:text-brand-600 transition-colors">Home</a>
                    <a href="/doctors" class="hover:text-brand-600 transition-colors">Doctors</a>
                    <a href="/services" class="hover:text-brand-600 transition-colors">Services</a>
                    <a href="/departments" class="hover:text-brand-600 transition-colors">Departments</a>
                    <a href="/about" class="hover:text-brand-600 transition-colors">About Us</a>
                    <a href="/contact" class="hover:text-brand-600 transition-colors">Contact</a>
                    <a href="/faq" class="hover:text-brand-600 transition-colors">FAQs</a>
                    <a href="/news" class="hover:text-brand-600 transition-colors">News</a>
                </nav>

                <!-- Login / Register buttons -->
                <div class="flex items-center space-x-3">
                    <a href="/auth/login" class="text-sm font-semibold text-gray-700 hover:text-brand-600 transition-colors">Sign In</a>
                    <a href="/auth/register" class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-xs transition-colors">Join Portal</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Dynamic Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Area -->
    <footer class="bg-slate-900 text-white mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1 -->
                <div class="space-y-4">
                    <span class="text-lg font-bold text-brand-600 tracking-wider">Jude General Hospital</span>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Providing premier clinical diagnostics, multi-specialty trauma operating theaters, and compassionate patient ward care.
                    </p>
                </div>
                <!-- Col 2 -->
                <div class="space-y-4">
                    <span class="text-sm font-bold uppercase tracking-wider text-slate-400">Services</span>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="/services" class="hover:text-white transition-colors">Emergency Medicine</a></li>
                        <li><a href="/services" class="hover:text-white transition-colors">Cardiology Surgery</a></li>
                        <li><a href="/services" class="hover:text-white transition-colors">Clinical Neurology</a></li>
                        <li><a href="/services" class="hover:text-white transition-colors">Child Pediatrics</a></li>
                    </ul>
                </div>
                <!-- Col 3 -->
                <div class="space-y-4">
                    <span class="text-sm font-bold uppercase tracking-wider text-slate-400">Quick Links</span>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="/about" class="hover:text-white transition-colors">About St. Jude</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="/faq" class="hover:text-white transition-colors">FAQ Support</a></li>
                        <li><a href="/terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="/privacy" class="hover:text-white transition-colors">Confidentiality Policy</a></li>
                    </ul>
                </div>
                <!-- Col 4 -->
                <div class="space-y-4">
                    <span class="text-sm font-bold uppercase tracking-wider text-slate-400">Emergency Support</span>
                    <p class="text-xl font-extrabold text-blue-400">+1 (555) 999-9111</p>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Available 24 hours a day, 365 days a year for urgent clinical admissions.
                    </p>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-6 text-center text-xs text-slate-500">
                &copy; 2026 St. Jude General Hospital. All rights reserved. Registered under clinical compliance frameworks.
            </div>
        </div>
    </footer>

</body>
</html>
