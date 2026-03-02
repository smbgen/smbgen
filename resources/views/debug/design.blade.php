<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLIENTBRIDGE Design Playground</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80px;
            border: 2px dashed #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .logo-container:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        .color-swatch {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s ease;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .color-swatch:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .font-demo {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .component-showcase {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            background: white;
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">smbgen</h1>
                    <p class="text-sm text-gray-600">Design Playground - Local Development Only</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-green-600">{{ app()->environment() }} environment</p>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Navigation Tabs -->
        <div class="mb-8">
            <nav class="flex space-x-8 border-b border-gray-200">
                <button class="tab-button active py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium" data-tab="logos">
                    Logo Variations
                </button>
                <button class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="colors">
                    Color Palettes
                </button>
                <button class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="typography">
                    Typography
                </button>
                <button class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="components">
                    Components
                </button>
                <button class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="layouts">
                    Layouts
                </button>
                <button class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="icons">
                    Icons & Graphics
                </button>
                <button class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="mockups">
                    Page Mockups
                </button>
            </nav>
        </div>

        <!-- Logo Variations Tab -->
        <div id="logos" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                
                <!-- Primary Logos -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Primary Logos</h3>
                    <div class="space-y-4">
                        <div class="logo-container bg-white">
                            <div class="text-center">
                                <h2 class="text-3xl font-black text-blue-600">smbgen</h2>
                                <p class="text-xs text-gray-500 mt-1">Professional Services Platform</p>
                            </div>
                        </div>
                        <div class="logo-container bg-slate-900">
                            <div class="text-center">
                                <h2 class="text-3xl font-black text-white">smbgen</h2>
                                <p class="text-xs text-gray-300 mt-1">Professional Services Platform</p>
                            </div>
                        </div>
                        <div class="logo-container bg-blue-600">
                            <div class="text-center">
                                <h2 class="text-3xl font-black text-white">smbgen</h2>
                                <p class="text-xs text-blue-100 mt-1">Professional Services Platform</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logo with Icons -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Logo with Icons</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">smbgen</h3>
                                    <p class="text-xs text-gray-500">Trusted Platform</p>
                                </div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">smbgen</h3>
                                    <p class="text-xs text-gray-500">Connect & Manage</p>
                                </div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">smbgen</h3>
                                    <p class="text-xs text-gray-500">Dashboard Portal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Minimal Logos -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Minimal Versions</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <h3 class="text-2xl font-light text-gray-800">clientbridge</h3>
                        </div>
                        <div class="logo-container">
                            <h3 class="text-2xl font-medium tracking-wider text-gray-900">CLIENT BRIDGE</h3>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-blue-600">CB</h3>
                                <p class="text-xs text-gray-400">smbgen</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logo Badges -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Badge Versions</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="bg-blue-600 text-white px-4 py-2 rounded-full">
                                <span class="text-sm font-bold">smbgen</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="border-2 border-blue-600 text-blue-600 px-4 py-2 rounded-lg">
                                <span class="text-sm font-bold">smbgen</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg">
                                <span class="text-sm font-bold">smbgen</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stacked Logos -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Stacked Versions</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900">CLIENT</h3>
                                <h3 class="text-xl font-bold text-blue-600">BRIDGE</h3>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-blue-600 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">CB</span>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900">smbgen</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Industry-Specific -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Industry Variants</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900">smbgen</h3>
                                <p class="text-xs text-blue-600 font-medium">CYBER SECURITY</p>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900">smbgen</h3>
                                <p class="text-xs text-green-600 font-medium">IT CONSULTING</p>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900">smbgen</h3>
                                <p class="text-xs text-purple-600 font-medium">BUSINESS SOLUTIONS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monogram Logos -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Monogram & Lettermarks</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto">
                                <span class="text-white font-black text-2xl">CB</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mx-auto">
                                <span class="text-white font-black text-2xl">CB</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="w-16 h-16 border-4 border-blue-600 rounded-full flex items-center justify-center mx-auto">
                                <span class="text-blue-600 font-black text-2xl">CB</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">C</span>
                                </div>
                                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">B</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Creative Typography -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Creative Typography</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <h3 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                CLIENTBRIDGE
                            </h3>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-xl font-black text-gray-900 tracking-widest">CLIENT</h3>
                                <div class="w-16 h-1 bg-blue-600 mx-auto my-1"></div>
                                <h3 class="text-xl font-black text-blue-600 tracking-widest">BRIDGE</h3>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <span class="text-3xl font-black text-gray-900">CLIENT</span>
                                <span class="text-3xl font-black text-blue-600">BRIDGE</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <h3 class="text-2xl font-light text-gray-800" style="font-family: 'Montserrat', sans-serif; letter-spacing: 0.2em;">
                                clientbridge
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Logo with Shapes -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Logos with Geometric Shapes</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-12 bg-blue-600 rounded-full"></div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">smbgen</h3>
                                </div>
                                <div class="w-4 h-12 bg-purple-600 rounded-full"></div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                                <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                                <h3 class="text-lg font-bold text-gray-900 ml-4">smbgen</h3>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="relative">
                                <div class="absolute -top-2 -left-2 w-6 h-6 border-l-4 border-t-4 border-blue-600 rounded-tl-lg"></div>
                                <h3 class="text-xl font-bold text-gray-900 px-4 py-2">smbgen</h3>
                                <div class="absolute -bottom-2 -right-2 w-6 h-6 border-r-4 border-b-4 border-purple-600 rounded-br-lg"></div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center">
                                <div class="w-16 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-l-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">CB</span>
                                </div>
                                <div class="bg-white px-4 py-2 rounded-r-lg border-2 border-l-0 border-gray-200">
                                    <span class="text-gray-900 font-semibold">smbgen</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Handwritten & Script Styles -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Script & Handwritten Styles</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <h3 class="text-2xl italic text-gray-800" style="font-family: cursive;">smbgen</h3>
                        </div>
                        <div class="logo-container">
                            <h3 class="text-xl text-blue-600" style="font-family: 'Brush Script MT', cursive; font-weight: bold;">
                                Client Bridge
                            </h3>
                        </div>
                        <div class="logo-container">
                            <div class="text-center">
                                <h3 class="text-lg italic text-gray-700 mb-1" style="font-family: serif;">smbgen</h3>
                                <div class="w-24 h-px bg-gradient-to-r from-transparent via-gray-400 to-transparent mx-auto"></div>
                                <p class="text-xs text-gray-500 mt-1" style="font-family: serif;">Professional Services</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logo Badges & Seals -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Badges & Seals</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="relative">
                                <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <div class="text-xs font-bold">CLIENT</div>
                                        <div class="text-lg font-black">★</div>
                                        <div class="text-xs font-bold">BRIDGE</div>
                                    </div>
                                </div>
                                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-8 border-transparent border-t-blue-600"></div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full w-20 h-20 flex items-center justify-center border-4 border-yellow-300 shadow-lg">
                                <div class="text-center text-white">
                                    <div class="text-xs font-bold">CB</div>
                                    <div class="text-xs">EST 2024</div>
                                </div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="relative bg-white border-4 border-blue-600 rounded-lg px-4 py-3">
                                <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 bg-blue-600 px-2 py-1 rounded-full">
                                    <span class="text-white text-xs font-bold">★</span>
                                </div>
                                <div class="text-center">
                                    <h4 class="text-sm font-bold text-blue-600">smbgen</h4>
                                    <p class="text-xs text-gray-600">Certified Professional</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Textured & 3D Effects -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Textured & 3D Effects</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <h3 class="text-2xl font-black text-gray-900 drop-shadow-lg" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                                CLIENTBRIDGE
                            </h3>
                        </div>
                        <div class="logo-container">
                            <h3 class="text-2xl font-bold text-blue-600 border-4 border-blue-600 px-4 py-2 bg-blue-50">
                                CLIENTBRIDGE
                            </h3>
                        </div>
                        <div class="logo-container">
                            <div class="bg-gray-900 px-6 py-3 rounded-lg shadow-xl">
                                <h3 class="text-xl font-bold text-white" style="text-shadow: 0 0 10px rgba(59, 130, 246, 0.8);">
                                    CLIENTBRIDGE
                                </h3>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="relative">
                                <h3 class="text-2xl font-black text-blue-600 relative z-10">smbgen</h3>
                                <div class="absolute top-1 left-1 text-2xl font-black text-gray-300 z-0">smbgen</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Size Variations -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Size Variations</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <h1 class="text-5xl font-black text-blue-600">smbgen</h1>
                        </div>
                        <div class="logo-container">
                            <h2 class="text-3xl font-bold text-gray-900">smbgen</h2>
                        </div>
                        <div class="logo-container">
                            <h3 class="text-xl font-semibold text-gray-800">smbgen</h3>
                        </div>
                        <div class="logo-container">
                            <h4 class="text-lg font-medium text-gray-700">smbgen</h4>
                        </div>
                        <div class="logo-container">
                            <h5 class="text-base font-normal text-gray-600">smbgen</h5>
                        </div>
                        <div class="logo-container">
                            <h6 class="text-sm text-gray-500">smbgen</h6>
                        </div>
                    </div>
                </div>

                <!-- Animated Style Concepts -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Animation Concepts</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="flex items-center space-x-1">
                                <span class="text-2xl font-bold text-gray-900 animate-pulse">CLIENT</span>
                                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
                                <span class="text-2xl font-bold text-blue-600 animate-pulse">BRIDGE</span>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="relative overflow-hidden">
                                <h3 class="text-2xl font-bold text-gray-900">smbgen</h3>
                                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 animate-pulse" style="width: 100%;"></div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex">
                                <div class="text-2xl font-bold text-blue-600 transform hover:scale-110 transition-transform cursor-pointer">CLIENT</div>
                                <div class="text-2xl font-bold text-purple-600 transform hover:scale-110 transition-transform cursor-pointer">BRIDGE</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Screen Hero Logos Section -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Full Screen Hero Logo Variations</h2>
                <div class="space-y-8">
                    
                    <!-- Hero Logo 1: Gradient Background with Large Text -->
                    <div class="w-full h-96 bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="text-center relative z-10">
                            <h1 class="text-7xl font-black text-white mb-4 tracking-wide">smbgen</h1>
                            <p class="text-2xl text-white/90 font-light tracking-wider">Professional Services Platform</p>
                            <div class="mt-6 w-32 h-1 bg-white/60 mx-auto rounded-full"></div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 1</div>
                    </div>

                    <!-- Hero Logo 2: Dark with Neon Effect -->
                    <div class="w-full h-96 bg-gray-900 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="text-center">
                            <h1 class="text-8xl font-black text-white mb-4" style="text-shadow: 0 0 30px #3b82f6, 0 0 60px #3b82f6;">smbgen</h1>
                            <p class="text-xl text-blue-400 font-medium tracking-widest">SECURE • RELIABLE • PROFESSIONAL</p>
                            <div class="mt-8 flex justify-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 2</div>
                    </div>

                    <!-- Hero Logo 3: Minimal White with Large Icon -->
                    <div class="w-full h-96 bg-white rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300 border-2 border-gray-100 shadow-2xl">
                        <div class="text-center">
                            <div class="w-32 h-32 bg-gradient-to-br from-blue-600 to-purple-600 rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-2xl">
                                <span class="text-5xl font-black text-white">CB</span>
                            </div>
                            <h1 class="text-6xl font-light text-gray-900 mb-4 tracking-widest">smbgen</h1>
                            <p class="text-lg text-gray-600 font-normal">Bridging Business & Technology</p>
                        </div>
                        <div class="absolute top-8 right-8 text-gray-400 text-sm">Hero Style 3</div>
                    </div>

                    <!-- Hero Logo 4: Split Screen Design -->
                    <div class="w-full h-96 rounded-2xl flex overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300 shadow-2xl relative">
                        <div class="w-1/2 bg-gray-900 flex items-center justify-center">
                            <div class="text-center text-white">
                                <h1 class="text-5xl font-black mb-2">CLIENT</h1>
                                <div class="w-16 h-1 bg-blue-500 mx-auto"></div>
                            </div>
                        </div>
                        <div class="w-1/2 bg-blue-600 flex items-center justify-center">
                            <div class="text-center text-white">
                                <h1 class="text-5xl font-black mb-2">BRIDGE</h1>
                                <div class="w-16 h-1 bg-white mx-auto"></div>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/70 text-sm">Hero Style 4</div>
                    </div>

                    <!-- Hero Logo 5: Geometric Pattern Background -->
                    <div class="w-full h-96 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute top-8 left-8 w-16 h-16 border-4 border-white rounded-full"></div>
                            <div class="absolute top-16 right-16 w-12 h-12 border-2 border-white rotate-45"></div>
                            <div class="absolute bottom-16 left-16 w-8 h-8 bg-white rounded-full"></div>
                            <div class="absolute bottom-8 right-8 w-20 h-20 border-2 border-white rounded-full"></div>
                            <div class="absolute top-1/2 left-1/4 w-6 h-6 bg-white rotate-45"></div>
                            <div class="absolute top-1/3 right-1/3 w-10 h-10 border border-white"></div>
                        </div>
                        <div class="text-center relative z-10">
                            <h1 class="text-7xl font-black text-white mb-4">smbgen</h1>
                            <p class="text-xl text-white/80 font-medium">Innovation Through Connection</p>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 5</div>
                    </div>

                    <!-- Hero Logo 6: Layered Typography -->
                    <div class="w-full h-96 bg-gray-50 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="text-center relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <h1 class="text-9xl font-black text-gray-200 select-none">smbgen</h1>
                            </div>
                            <div class="relative z-10">
                                <h1 class="text-6xl font-bold text-blue-600 mb-4">smbgen</h1>
                                <p class="text-lg text-gray-800 font-medium tracking-wide">PROFESSIONAL SERVICES PLATFORM</p>
                                <div class="mt-6 flex justify-center space-x-1">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                    <div class="w-8 h-2 bg-blue-600 rounded-full"></div>
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-gray-500 text-sm">Hero Style 6</div>
                    </div>

                    <!-- Hero Logo 7: Corporate Glass Effect -->
                    <div class="w-full h-96 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-purple-600/20"></div>
                        <div class="bg-white/10 backdrop-blur-md rounded-3xl px-16 py-12 border border-white/20 shadow-2xl">
                            <div class="text-center">
                                <h1 class="text-6xl font-black text-white mb-4">smbgen</h1>
                                <p class="text-lg text-white/90">Enterprise Solutions</p>
                                <div class="mt-6 w-24 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 mx-auto"></div>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 7</div>
                    </div>

                    <!-- Hero Logo 8: Bold Outlined Text -->
                    <div class="w-full h-96 bg-blue-600 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="text-center">
                            <h1 class="text-8xl font-black text-transparent mb-4" style="-webkit-text-stroke: 4px white; text-stroke: 4px white;">smbgen</h1>
                            <div class="bg-white px-8 py-3 rounded-full">
                                <p class="text-blue-600 font-bold text-lg">TRUSTED PLATFORM</p>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/70 text-sm">Hero Style 8</div>
                    </div>

                    <!-- Hero Logo 9: Stacked with Icons -->
                    <div class="w-full h-96 bg-gradient-to-br from-emerald-600 to-blue-600 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="text-center text-white">
                            <div class="flex justify-center space-x-6 mb-8">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <h1 class="text-7xl font-black mb-4">smbgen</h1>
                            <p class="text-xl font-light">Secure • Connected • Powerful</p>
                        </div>
                        <div class="absolute top-8 right-8 text-white/60 text-sm">Hero Style 9</div>
                    </div>

                    <!-- Hero Logo 10: Floating Elements -->
                    <div class="w-full h-96 bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-16 left-16 w-4 h-4 bg-blue-400 rounded-full animate-bounce"></div>
                        <div class="absolute top-24 right-20 w-3 h-3 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
                        <div class="absolute bottom-20 left-20 w-5 h-5 bg-pink-400 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
                        <div class="absolute bottom-16 right-16 w-6 h-6 bg-blue-300 rounded-full animate-bounce" style="animation-delay: 1.5s;"></div>
                        
                        <div class="text-center text-white">
                            <h1 class="text-7xl font-black mb-6">smbgen</h1>
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-8 py-4">
                                <p class="text-lg font-medium">Next Generation Platform</p>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 10</div>
                    </div>

                    <!-- Hero Logo 11: Retro Sunrise -->
                    <div class="w-full h-96 bg-gradient-to-b from-purple-900 via-pink-600 to-orange-400 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-64 h-32 bg-gradient-to-t from-yellow-400 to-transparent rounded-full opacity-60"></div>
                        
                        <div class="text-center text-white relative z-10">
                            <h1 class="text-8xl font-black mb-4" style="text-shadow: 2px 2px 0px rgba(0,0,0,0.5);">smbgen</h1>
                            <p class="text-xl font-bold tracking-wider">FUTURE FORWARD</p>
                        </div>
                        <div class="absolute top-8 right-8 text-white/70 text-sm">Hero Style 11</div>
                    </div>

                    <!-- Hero Logo 12: Minimalist Grid -->
                    <div class="w-full h-96 bg-white rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300 border border-gray-200">
                        <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(rgba(59,130,246,0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(59,130,246,0.3) 1px, transparent 1px); background-size: 40px 40px;"></div>
                        
                        <div class="text-center relative z-10">
                            <div class="inline-block border-4 border-blue-600 rounded-2xl px-12 py-8 mb-6">
                                <h1 class="text-5xl font-black text-blue-600">smbgen</h1>
                            </div>
                            <p class="text-gray-800 font-medium text-lg">PRECISION • CLARITY • EXCELLENCE</p>
                        </div>
                        <div class="absolute top-8 right-8 text-gray-400 text-sm">Hero Style 12</div>
                    </div>

                    <!-- Hero Logo 13: Holographic Effect -->
                    <div class="w-full h-96 bg-black rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/20 via-purple-500/20 to-pink-500/20 animate-pulse"></div>
                        
                        <div class="text-center">
                            <h1 class="text-8xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 mb-4 animate-pulse">
                                CLIENTBRIDGE
                            </h1>
                            <div class="flex justify-center space-x-4 mb-4">
                                <div class="w-16 h-0.5 bg-gradient-to-r from-cyan-400 to-transparent"></div>
                                <div class="w-16 h-0.5 bg-gradient-to-r from-purple-400 to-transparent"></div>
                                <div class="w-16 h-0.5 bg-gradient-to-r from-pink-400 to-transparent"></div>
                            </div>
                            <p class="text-white/80 text-lg font-light">DIGITAL TRANSFORMATION</p>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 13</div>
                    </div>

                    <!-- Hero Logo 14: Industrial Corporate -->
                    <div class="w-full h-96 bg-gradient-to-br from-slate-700 to-slate-900 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.1) 2px, rgba(255,255,255,0.1) 4px);"></div>
                        
                        <div class="text-center text-white">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg px-8 py-2 mb-8 inline-block shadow-2xl">
                                <span class="text-sm font-bold tracking-widest">ESTABLISHED 2024</span>
                            </div>
                            <h1 class="text-7xl font-black mb-4 tracking-wide">smbgen</h1>
                            <div class="bg-white/10 rounded-full px-8 py-3">
                                <p class="text-lg font-semibold">ENTERPRISE GRADE SOLUTIONS</p>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/50 text-sm">Hero Style 14</div>
                    </div>

                    <!-- Hero Logo 15: Artistic Brush Stroke -->
                    <div class="w-full h-96 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-600 rounded-2xl flex items-center justify-center relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-0 left-0 w-full h-full">
                            <svg viewBox="0 0 400 200" class="w-full h-full opacity-20">
                                <path d="M50,100 Q200,20 350,100 Q200,180 50,100" stroke="white" stroke-width="2" fill="none"/>
                                <path d="M80,120 Q180,60 320,120" stroke="white" stroke-width="1" fill="none" opacity="0.5"/>
                                <path d="M70,80 Q220,40 330,80" stroke="white" stroke-width="1" fill="none" opacity="0.3"/>
                            </svg>
                        </div>
                        
                        <div class="text-center text-white relative z-10">
                            <h1 class="text-7xl font-black mb-4 transform -rotate-2">smbgen</h1>
                            <div class="bg-white text-blue-600 rounded-full px-6 py-2 inline-block transform rotate-1">
                                <p class="font-bold">Creative Solutions</p>
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 text-white/60 text-sm">Hero Style 15</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Colors Tab -->
        <div id="colors" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                
                <!-- Primary Brand Colors -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Primary Brand Colors</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center">
                            <div class="color-swatch bg-blue-600 mx-auto mb-2" title="#2563eb"></div>
                            <p class="text-xs font-mono">#2563eb</p>
                            <p class="text-xs text-gray-600">Primary Blue</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-blue-500 mx-auto mb-2" title="#3b82f6"></div>
                            <p class="text-xs font-mono">#3b82f6</p>
                            <p class="text-xs text-gray-600">Light Blue</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-blue-700 mx-auto mb-2" title="#1d4ed8"></div>
                            <p class="text-xs font-mono">#1d4ed8</p>
                            <p class="text-xs text-gray-600">Dark Blue</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-blue-900 mx-auto mb-2" title="#1e3a8a"></div>
                            <p class="text-xs font-mono">#1e3a8a</p>
                            <p class="text-xs text-gray-600">Navy Blue</p>
                        </div>
                    </div>
                </div>

                <!-- Secondary Colors -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Secondary Colors</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center">
                            <div class="color-swatch bg-gray-900 mx-auto mb-2" title="#111827"></div>
                            <p class="text-xs font-mono">#111827</p>
                            <p class="text-xs text-gray-600">Charcoal</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-gray-600 mx-auto mb-2" title="#4b5563"></div>
                            <p class="text-xs font-mono">#4b5563</p>
                            <p class="text-xs text-gray-600">Steel Gray</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-gray-300 mx-auto mb-2" title="#d1d5db"></div>
                            <p class="text-xs font-mono">#d1d5db</p>
                            <p class="text-xs text-gray-600">Light Gray</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-white border-2 border-gray-200 mx-auto mb-2" title="#ffffff"></div>
                            <p class="text-xs font-mono">#ffffff</p>
                            <p class="text-xs text-gray-600">Pure White</p>
                        </div>
                    </div>
                </div>

                <!-- Accent Colors -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Accent Colors</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center">
                            <div class="color-swatch bg-green-600 mx-auto mb-2" title="#16a34a"></div>
                            <p class="text-xs font-mono">#16a34a</p>
                            <p class="text-xs text-gray-600">Success</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-red-600 mx-auto mb-2" title="#dc2626"></div>
                            <p class="text-xs font-mono">#dc2626</p>
                            <p class="text-xs text-gray-600">Error</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-yellow-500 mx-auto mb-2" title="#eab308"></div>
                            <p class="text-xs font-mono">#eab308</p>
                            <p class="text-xs text-gray-600">Warning</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-purple-600 mx-auto mb-2" title="#9333ea"></div>
                            <p class="text-xs font-mono">#9333ea</p>
                            <p class="text-xs text-gray-600">Premium</p>
                        </div>
                    </div>
                </div>

                <!-- Professional Palette -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Professional Palette</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center">
                            <div class="color-swatch bg-slate-800 mx-auto mb-2" title="#1e293b"></div>
                            <p class="text-xs font-mono">#1e293b</p>
                            <p class="text-xs text-gray-600">Corporate</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-indigo-600 mx-auto mb-2" title="#4f46e5"></div>
                            <p class="text-xs font-mono">#4f46e5</p>
                            <p class="text-xs text-gray-600">Professional</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-teal-600 mx-auto mb-2" title="#0d9488"></div>
                            <p class="text-xs font-mono">#0d9488</p>
                            <p class="text-xs text-gray-600">Trust</p>
                        </div>
                        <div class="text-center">
                            <div class="color-swatch bg-orange-600 mx-auto mb-2" title="#ea580c"></div>
                            <p class="text-xs font-mono">#ea580c</p>
                            <p class="text-xs text-gray-600">Energy</p>
                        </div>
                    </div>
                </div>

                <!-- Gradient Combinations -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Gradient Combinations</h3>
                    <div class="space-y-3">
                        <div class="h-16 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-semibold">Blue to Purple</span>
                        </div>
                        <div class="h-16 rounded-lg bg-gradient-to-r from-gray-900 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-semibold">Charcoal to Blue</span>
                        </div>
                        <div class="h-16 rounded-lg bg-gradient-to-r from-teal-500 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-semibold">Teal to Blue</span>
                        </div>
                    </div>
                </div>

                <!-- Color Usage Examples -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Usage Examples</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-blue-800 font-semibold">Information</h4>
                            <p class="text-blue-700 text-sm">Your appointment has been scheduled successfully.</p>
                        </div>
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h4 class="text-green-800 font-semibold">Success</h4>
                            <p class="text-green-700 text-sm">Payment processed successfully.</p>
                        </div>
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h4 class="text-red-800 font-semibold">Error</h4>
                            <p class="text-red-700 text-sm">Please check your input and try again.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Typography Tab -->
        <div id="typography" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Font Families -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Font Families</h3>
                    <div class="space-y-4">
                        <div class="font-demo" style="font-family: Inter;">
                            <h4 class="font-semibold">Inter (Primary)</h4>
                            <p class="text-gray-600">The quick brown fox jumps over the lazy dog</p>
                            <p class="text-sm text-gray-500">ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789</p>
                        </div>
                        <div class="font-demo" style="font-family: Poppins;">
                            <h4 class="font-semibold">Poppins (Headings)</h4>
                            <p class="text-gray-600">The quick brown fox jumps over the lazy dog</p>
                            <p class="text-sm text-gray-500">ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789</p>
                        </div>
                        <div class="font-demo" style="font-family: Montserrat;">
                            <h4 class="font-semibold">Montserrat (Modern)</h4>
                            <p class="text-gray-600">The quick brown fox jumps over the lazy dog</p>
                            <p class="text-sm text-gray-500">ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789</p>
                        </div>
                        <div class="font-demo" style="font-family: Roboto;">
                            <h4 class="font-semibold">Roboto (Clean)</h4>
                            <p class="text-gray-600">The quick brown fox jumps over the lazy dog</p>
                            <p class="text-sm text-gray-500">ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789</p>
                        </div>
                    </div>
                </div>

                <!-- Font Weights & Sizes -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Font Weights & Sizes</h3>
                    <div class="space-y-4">
                        <div>
                            <h1 class="text-4xl font-black text-gray-900">Heading 1 - Ultra Bold</h1>
                            <p class="text-sm text-gray-500">text-4xl font-black</p>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Heading 2 - Bold</h2>
                            <p class="text-sm text-gray-500">text-3xl font-bold</p>
                        </div>
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">Heading 3 - Semi Bold</h3>
                            <p class="text-sm text-gray-500">text-2xl font-semibold</p>
                        </div>
                        <div>
                            <h4 class="text-xl font-medium text-gray-900">Heading 4 - Medium</h4>
                            <p class="text-sm text-gray-500">text-xl font-medium</p>
                        </div>
                        <div>
                            <p class="text-base font-normal text-gray-900">Body Text - Regular</p>
                            <p class="text-sm text-gray-500">text-base font-normal</p>
                        </div>
                        <div>
                            <p class="text-sm font-light text-gray-600">Small Text - Light</p>
                            <p class="text-xs text-gray-500">text-sm font-light</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Components Tab -->
        <div id="components" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                
                <!-- Buttons -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Buttons</h3>
                    <div class="space-y-3">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            Primary Button
                        </button>
                        <button class="w-full border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-2 px-4 rounded-lg transition-colors">
                            Secondary Button
                        </button>
                        <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                            Tertiary Button
                        </button>
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            Success Button
                        </button>
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            Danger Button
                        </button>
                    </div>
                </div>

                <!-- Cards -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Cards</h3>
                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-2">Basic Card</h4>
                            <p class="text-gray-600 text-sm">Simple card with border and shadow.</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-lg p-4">
                            <h4 class="font-semibold mb-2">Gradient Card</h4>
                            <p class="text-blue-100 text-sm">Card with gradient background.</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-lg">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="font-semibold text-gray-900">Feature Card</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">Card with icon and content.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Elements -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Form Elements</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Input Field</label>
                            <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter text...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Dropdown</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option>Choose option...</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Textarea</label>
                            <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Enter message..."></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-700">Checkbox option</label>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Navigation</h3>
                    <div class="space-y-4">
                        <nav class="bg-gray-100 rounded-lg p-2">
                            <div class="flex space-x-1">
                                <a href="#" class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="#" class="text-gray-700 hover:bg-white px-3 py-2 rounded-md text-sm font-medium">Clients</a>
                                <a href="#" class="text-gray-700 hover:bg-white px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                            </div>
                        </nav>
                        
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <a href="#" class="border-b-2 border-blue-500 text-blue-600 py-2 px-1 text-sm font-medium">Active Tab</a>
                                <a href="#" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">Tab 2</a>
                                <a href="#" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">Tab 3</a>
                            </nav>
                        </div>
                        
                        <ul class="space-y-1">
                            <li><a href="#" class="bg-blue-50 text-blue-600 group flex items-center px-2 py-2 text-sm font-medium rounded-md">Current Page</a></li>
                            <li><a href="#" class="text-gray-700 hover:bg-gray-50 group flex items-center px-2 py-2 text-sm font-medium rounded-md">Other Page</a></li>
                            <li><a href="#" class="text-gray-700 hover:bg-gray-50 group flex items-center px-2 py-2 text-sm font-medium rounded-md">Another Page</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Alerts</h3>
                    <div class="space-y-3">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">Information alert message.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800">Success alert message.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">Error alert message.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges & Status -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Badges & Status</h3>
                    <div class="space-y-4">
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Default</span>
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Active</span>
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Pending</span>
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Error</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-600 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">Pill Badge</span>
                            <span class="bg-purple-600 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">Premium</span>
                            <span class="bg-gray-600 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">Standard</span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-700">Online</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-700">Away</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-700">Offline</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Icons & Graphics Tab -->
        <div id="icons" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                
                <!-- SVG Icons Collection -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Core Business Icons</h3>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Dashboard">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Dashboard</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Users">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Users</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Settings">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Settings</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Calendar">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Calendar</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Reports">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Reports</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Messages">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Messages</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Security">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Security</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer" title="Documents">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Documents</p>
                        </div>
                    </div>
                </div>

                <!-- Status & Action Icons -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Status & Action Icons</h3>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="text-center p-3 hover:bg-green-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Success</p>
                        </div>
                        <div class="text-center p-3 hover:bg-red-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Error</p>
                        </div>
                        <div class="text-center p-3 hover:bg-yellow-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Warning</p>
                        </div>
                        <div class="text-center p-3 hover:bg-blue-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Info</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Add</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Edit</p>
                        </div>
                        <div class="text-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2h4a2 2 0 002-2V3a2 2 0 012 2v6.5l-3.777-1.947a.5.5 0 00-.577.093L10 12.71l-1.646-1.064a.5.5 0 00-.577-.093L4 13.5V5z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Archive</p>
                        </div>
                        <div class="text-center p-3 hover:bg-red-50 rounded-lg cursor-pointer">
                            <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2h4a2 2 0 002-2V3a2 2 0 012 2v6.5l-3.777-1.947a.5.5 0 00-.577.093L10 12.71l-1.646-1.064a.5.5 0 00-.577-.093L4 13.5V5z"/>
                            </svg>
                            <p class="text-xs text-gray-600">Delete</p>
                        </div>
                    </div>
                </div>

                <!-- Logo Icon Combinations -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Logo + Icon Combinations</h3>
                    <div class="space-y-4">
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">smbgen</h3>
                                    <p class="text-xs text-blue-600">Secure Platform</p>
                                </div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">smbgen</h3>
                                    <p class="text-xs text-green-600">Client Management</p>
                                </div>
                            </div>
                        </div>
                        <div class="logo-container">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">smbgen</h3>
                                    <p class="text-xs text-purple-600">Analytics & Reports</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Illustration Elements -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Illustration Elements</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg text-center">
                            <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Client Portal</h4>
                            <p class="text-sm text-gray-600 mt-2">Secure client access</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg text-center">
                            <div class="w-16 h-16 bg-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Security</h4>
                            <p class="text-sm text-gray-600 mt-2">Enterprise protection</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg text-center">
                            <div class="w-16 h-16 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Analytics</h4>
                            <p class="text-sm text-gray-600 mt-2">Real-time insights</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-lg text-center">
                            <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Support</h4>
                            <p class="text-sm text-gray-600 mt-2">24/7 assistance</p>
                        </div>
                    </div>
                </div>

                <!-- Brand Patterns -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Brand Patterns</h3>
                    <div class="space-y-4">
                        <div class="h-20 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">Gradient Pattern</span>
                        </div>
                        <div class="h-20 bg-blue-600 rounded-lg relative overflow-hidden flex items-center justify-center">
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-2 left-2 w-4 h-4 border-2 border-white rounded-full"></div>
                                <div class="absolute top-6 right-8 w-2 h-2 bg-white rounded-full"></div>
                                <div class="absolute bottom-4 left-8 w-3 h-3 border border-white"></div>
                                <div class="absolute bottom-2 right-4 w-6 h-6 border-2 border-white rounded-full"></div>
                            </div>
                            <span class="text-white font-bold relative z-10">Geometric Pattern</span>
                        </div>
                        <div class="h-20 bg-gray-900 rounded-lg relative overflow-hidden flex items-center justify-center">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
                            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);"></div>
                            <span class="text-white font-bold relative z-10">Striped Overlay</span>
                        </div>
                    </div>
                </div>

                <!-- Logo Watermarks -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Watermark Styles</h3>
                    <div class="space-y-4">
                        <div class="h-32 bg-gray-100 rounded-lg relative overflow-hidden flex items-center justify-center">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-6xl font-black text-gray-200 rotate-45 select-none">smbgen</span>
                            </div>
                            <div class="relative z-10 bg-white p-4 rounded-lg shadow-sm">
                                <p class="text-gray-700">Sample content with watermark background</p>
                            </div>
                        </div>
                        <div class="h-32 bg-blue-50 rounded-lg relative overflow-hidden flex items-center justify-center">
                            <div class="absolute bottom-4 right-4">
                                <span class="text-blue-200 text-sm font-bold">smbgen</span>
                            </div>
                            <div class="text-center">
                                <h4 class="font-semibold text-gray-900">Document Title</h4>
                                <p class="text-gray-600 text-sm">Content area with corner watermark</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Mockups Tab -->
        <div id="mockups" class="tab-content hidden">
            <div class="space-y-8">
                
                <!-- Login Page Mockup -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Login Page Variations</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-100 rounded-lg p-6 min-h-96">
                            <div class="max-w-sm mx-auto">
                                <div class="text-center mb-8">
                                    <div class="w-16 h-16 bg-blue-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                        <span class="text-white font-bold text-xl">CB</span>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900">smbgen</h2>
                                    <p class="text-gray-600 text-sm">Welcome back</p>
                                </div>
                                <div class="space-y-4">
                                    <input type="email" placeholder="Email address" class="w-full p-3 border border-gray-300 rounded-lg">
                                    <input type="password" placeholder="Password" class="w-full p-3 border border-gray-300 rounded-lg">
                                    <button class="w-full bg-blue-600 text-white p-3 rounded-lg font-semibold">Sign In</button>
                                </div>
                                <p class="text-center text-sm text-gray-600 mt-4">
                                    <a href="#" class="text-blue-600 hover:underline">Forgot password?</a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg p-6 min-h-96 flex items-center">
                            <div class="w-full">
                                <div class="bg-white rounded-lg p-8 shadow-xl">
                                    <div class="text-center mb-6">
                                        <h2 class="text-2xl font-bold text-gray-900 mb-2">smbgen</h2>
                                        <p class="text-gray-600">Secure Client Portal</p>
                                    </div>
                                    <div class="space-y-4">
                                        <input type="email" placeholder="Email" class="w-full p-3 border border-gray-300 rounded-lg">
                                        <input type="password" placeholder="Password" class="w-full p-3 border border-gray-300 rounded-lg">
                                        <button class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white p-3 rounded-lg font-semibold">
                                            Access Portal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Variations -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Dashboard Layout Variations</h3>
                    <div class="space-y-6">
                        <!-- Sidebar Layout -->
                        <div class="bg-gray-100 rounded-lg p-4 min-h-64">
                            <div class="grid grid-cols-12 gap-4 h-full">
                                <div class="col-span-3 bg-gray-800 rounded-lg p-3">
                                    <div class="text-white font-bold text-sm mb-4">smbgen</div>
                                    <nav class="space-y-2">
                                        <div class="bg-blue-600 text-white p-2 rounded text-xs">Dashboard</div>
                                        <div class="text-gray-300 p-2 text-xs hover:bg-gray-700 rounded cursor-pointer">Clients</div>
                                        <div class="text-gray-300 p-2 text-xs hover:bg-gray-700 rounded cursor-pointer">Projects</div>
                                        <div class="text-gray-300 p-2 text-xs hover:bg-gray-700 rounded cursor-pointer">Reports</div>
                                        <div class="text-gray-300 p-2 text-xs hover:bg-gray-700 rounded cursor-pointer">Settings</div>
                                    </nav>
                                </div>
                                <div class="col-span-9 space-y-3">
                                    <div class="bg-white rounded-lg p-3 flex justify-between items-center">
                                        <h2 class="font-semibold">Dashboard Overview</h2>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full"></div>
                                            <span class="text-sm">Admin User</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-3">
                                        <div class="bg-white rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-blue-600">142</div>
                                            <div class="text-xs text-gray-600">Total Clients</div>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-green-600">28</div>
                                            <div class="text-xs text-gray-600">Active Projects</div>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-purple-600">$45k</div>
                                            <div class="text-xs text-gray-600">Monthly Revenue</div>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-orange-600">95%</div>
                                            <div class="text-xs text-gray-600">Satisfaction</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Top Navigation Layout -->
                        <div class="bg-gray-100 rounded-lg p-4 min-h-64">
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-3">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">CB</span>
                                                </div>
                                                <span class="font-bold">smbgen</span>
                                            </div>
                                            <nav class="flex space-x-6">
                                                <a href="#" class="text-blue-600 font-medium text-sm border-b-2 border-blue-600 pb-1">Dashboard</a>
                                                <a href="#" class="text-gray-600 text-sm hover:text-gray-900">Clients</a>
                                                <a href="#" class="text-gray-600 text-sm hover:text-gray-900">Projects</a>
                                                <a href="#" class="text-gray-600 text-sm hover:text-gray-900">Reports</a>
                                            </nav>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <button class="p-2 hover:bg-gray-100 rounded-lg">
                                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                                                </svg>
                                            </button>
                                            <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-white rounded-lg p-4 col-span-2">
                                        <h3 class="font-semibold mb-3 text-sm">Recent Activity</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span>New client onboarded</span>
                                                <span class="text-gray-500">2min ago</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span>Project milestone reached</span>
                                                <span class="text-gray-500">1hr ago</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span>Payment received</span>
                                                <span class="text-gray-500">3hr ago</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4">
                                        <h3 class="font-semibold mb-3 text-sm">Quick Stats</h3>
                                        <div class="space-y-3">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Revenue</span>
                                                <span class="text-sm font-semibold">$12.4k</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Clients</span>
                                                <span class="text-sm font-semibold">142</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Projects</span>
                                                <span class="text-sm font-semibold">28</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile App Mockups -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Mobile App Mockups</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="mx-auto">
                            <div class="w-64 h-96 bg-gray-900 rounded-3xl p-2">
                                <div class="w-full h-full bg-white rounded-2xl overflow-hidden">
                                    <div class="bg-blue-600 p-4 text-white">
                                        <div class="flex justify-between items-center mb-4">
                                            <h2 class="font-bold">smbgen</h2>
                                            <div class="w-8 h-8 bg-white/20 rounded-full"></div>
                                        </div>
                                        <p class="text-blue-100 text-sm">Welcome back, John</p>
                                    </div>
                                    <div class="p-4 space-y-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-gray-50 p-3 rounded-lg text-center">
                                                <div class="text-lg font-bold text-blue-600">12</div>
                                                <div class="text-xs text-gray-600">Projects</div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg text-center">
                                                <div class="text-lg font-bold text-green-600">3</div>
                                                <div class="text-xs text-gray-600">Messages</div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                                <span class="text-sm">Security Audit</span>
                                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">Active</span>
                                            </div>
                                            <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                                <span class="text-sm">Network Review</span>
                                                <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center text-sm text-gray-600 mt-2">Dashboard View</p>
                        </div>
                        
                        <div class="mx-auto">
                            <div class="w-64 h-96 bg-gray-900 rounded-3xl p-2">
                                <div class="w-full h-full bg-white rounded-2xl overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex justify-between items-center mb-6">
                                            <h2 class="font-bold text-gray-900">Messages</h2>
                                            <button class="text-blue-600">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                                <div class="w-10 h-10 bg-blue-600 rounded-full flex-shrink-0"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between">
                                                        <p class="text-sm font-medium text-gray-900 truncate">Admin Team</p>
                                                        <p class="text-xs text-gray-500">2min</p>
                                                    </div>
                                                    <p class="text-sm text-gray-600 truncate">Your security audit is complete...</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                                                <div class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between">
                                                        <p class="text-sm font-medium text-gray-900 truncate">Support</p>
                                                        <p class="text-xs text-gray-500">1hr</p>
                                                    </div>
                                                    <p class="text-sm text-gray-600 truncate">Thanks for your feedback...</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                                                <div class="w-10 h-10 bg-green-300 rounded-full flex-shrink-0"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between">
                                                        <p class="text-sm font-medium text-gray-900 truncate">Project Manager</p>
                                                        <p class="text-xs text-gray-500">3hr</p>
                                                    </div>
                                                    <p class="text-sm text-gray-600 truncate">Project update available...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center text-sm text-gray-600 mt-2">Messages View</p>
                        </div>
                        
                        <div class="mx-auto">
                            <div class="w-64 h-96 bg-gray-900 rounded-3xl p-2">
                                <div class="w-full h-full bg-white rounded-2xl overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex justify-between items-center mb-6">
                                            <h2 class="font-bold text-gray-900">Settings</h2>
                                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm font-medium">Profile</span>
                                                </div>
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm font-medium">Security</span>
                                                </div>
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm font-medium">Notifications</span>
                                                </div>
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center text-sm text-gray-600 mt-2">Settings View</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layouts Tab -->
        <div id="layouts" class="tab-content hidden">
            <div class="space-y-8">
                
                <!-- Dashboard Layout -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Dashboard Layout</h3>
                    <div class="bg-gray-100 rounded-lg p-4 min-h-96">
                        <div class="grid grid-cols-12 gap-4 h-full">
                            <!-- Sidebar -->
                            <div class="col-span-2 bg-gray-800 rounded-lg p-3">
                                <div class="text-white text-sm font-semibold mb-3">smbgen</div>
                                <div class="space-y-2">
                                    <div class="bg-blue-600 text-white text-xs p-2 rounded">Dashboard</div>
                                    <div class="text-gray-300 text-xs p-2">Clients</div>
                                    <div class="text-gray-300 text-xs p-2">Bookings</div>
                                    <div class="text-gray-300 text-xs p-2">Reports</div>
                                </div>
                            </div>
                            
                            <!-- Main Content -->
                            <div class="col-span-10 space-y-4">
                                <!-- Header -->
                                <div class="bg-white rounded-lg p-4 flex justify-between items-center">
                                    <h2 class="text-lg font-semibold">Dashboard</h2>
                                    <div class="bg-blue-600 text-white text-xs px-3 py-1 rounded">Admin</div>
                                </div>
                                
                                <!-- Stats Grid -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-blue-600">142</div>
                                        <div class="text-xs text-gray-600">Total Clients</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-green-600">28</div>
                                        <div class="text-xs text-gray-600">This Month</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-purple-600">$45k</div>
                                        <div class="text-xs text-gray-600">Revenue</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-orange-600">95%</div>
                                        <div class="text-xs text-gray-600">Satisfaction</div>
                                    </div>
                                </div>
                                
                                <!-- Content Area -->
                                <div class="bg-white rounded-lg p-4">
                                    <h3 class="font-semibold mb-3">Recent Activity</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span>New client registration</span>
                                            <span class="text-gray-500">2 min ago</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span>Booking completed</span>
                                            <span class="text-gray-500">5 min ago</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span>Payment received</span>
                                            <span class="text-gray-500">10 min ago</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Client Portal Layout -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Client Portal Layout</h3>
                    <div class="bg-gray-100 rounded-lg p-4 min-h-96">
                        <!-- Header -->
                        <div class="bg-white rounded-lg p-4 mb-4 flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">CB</span>
                                </div>
                                <span class="font-semibold">smbgen</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">Welcome, John Doe</span>
                                <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Navigation -->
                            <div class="bg-white rounded-lg p-4">
                                <h4 class="font-semibold mb-3">Quick Actions</h4>
                                <div class="space-y-2">
                                    <button class="w-full bg-blue-600 text-white text-sm py-2 rounded">Book Appointment</button>
                                    <button class="w-full border border-gray-300 text-gray-700 text-sm py-2 rounded">View Documents</button>
                                    <button class="w-full border border-gray-300 text-gray-700 text-sm py-2 rounded">Message Admin</button>
                                </div>
                            </div>
                            
                            <!-- Main Content -->
                            <div class="col-span-2 bg-white rounded-lg p-4">
                                <h4 class="font-semibold mb-3">Your Account</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-blue-50 p-3 rounded">
                                        <div class="text-lg font-bold text-blue-600">3</div>
                                        <div class="text-sm text-blue-700">Upcoming Appointments</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded">
                                        <div class="text-lg font-bold text-green-600">12</div>
                                        <div class="text-sm text-green-700">Documents</div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h5 class="font-medium mb-2">Recent Activity</h5>
                                    <div class="space-y-2 text-sm">
                                        <div>✓ Document uploaded: Security Assessment</div>
                                        <div>📅 Appointment scheduled for Dec 15</div>
                                        <div>💬 New message from admin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Landing Page Layout -->
                <div class="component-showcase">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Landing Page Layout</h3>
                    <div class="bg-gray-100 rounded-lg p-4 min-h-96">
                        <!-- Hero Section -->
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-6 mb-4">
                            <div class="text-center">
                                <h1 class="text-2xl font-bold mb-2">smbgen</h1>
                                <p class="mb-4">Professional Client Management Platform</p>
                                <button class="bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold">Get Started</button>
                            </div>
                        </div>
                        
                        <!-- Features Grid -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-1">Secure</h3>
                                <p class="text-xs text-gray-600">Enterprise-grade security</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-1">Connected</h3>
                                <p class="text-xs text-gray-600">Seamless communication</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-1">Organized</h3>
                                <p class="text-xs text-gray-600">Streamlined workflows</p>
                            </div>
                        </div>
                        
                        <!-- CTA Section -->
                        <div class="bg-white rounded-lg p-6 text-center">
                            <h3 class="text-lg font-semibold mb-2">Ready to get started?</h3>
                            <p class="text-gray-600 text-sm mb-4">Join thousands of professionals using CLIENTBRIDGE</p>
                            <div class="flex justify-center space-x-4">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Start Free Trial</button>
                                <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Learn More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.dataset.tab;
                
                // Remove active class from all buttons and contents
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Add active class to clicked button
                button.classList.add('active', 'border-blue-500', 'text-blue-600');
                button.classList.remove('border-transparent', 'text-gray-500');
                
                // Show target content
                document.getElementById(targetTab).classList.remove('hidden');
            });
        });

        // Copy color codes to clipboard
        document.querySelectorAll('.color-swatch').forEach(swatch => {
            swatch.addEventListener('click', () => {
                const color = swatch.title;
                navigator.clipboard.writeText(color).then(() => {
                    // Brief visual feedback
                    const original = swatch.style.transform;
                    swatch.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        swatch.style.transform = original;
                    }, 150);
                });
            });
        });

        // Make logo containers clickable for copying text
        document.querySelectorAll('.logo-container').forEach(container => {
            container.addEventListener('click', () => {
                const text = container.textContent.trim();
                navigator.clipboard.writeText(text).then(() => {
                    container.style.backgroundColor = '#f0f9ff';
                    setTimeout(() => {
                        container.style.backgroundColor = '';
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>