<?php
include 'navbar1.php'
// Simplified contact page without email functionality
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Our Basketball Team</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <style>
        .bg-basketball {
            background-color: #1c2a38;
            background-image: linear-gradient(135deg, #1c2a38 0%, #d72641 100%);
        }
        .input-focus:focus {
            border-color: #d72641 !important;
            box-shadow: 0 0 0 3px rgba(215, 38, 65, 0.2) !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-basketball text-white text-center py-6">
                <h1 class="text-3xl font-bold">Contact the ABAHUB Team</h1>
                <p class="mt-2">We'd love to hear from you!</p>
            </div>

            <div class="p-6 md:flex">
                <!-- Form Area Replaced with Background Image -->
                <div class="md:w-2/3 md:pr-6 relative">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('images/court\ 1.jpg');">
                        <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                            <div class="text-center text-white p-6">
                                <h2 class="text-2xl font-bold mb-4">Connect with us</h2>
                                <p class="mb-4">Want to get in touch? Reach out to us through our social media channels or contact information.</p>
                                <div class="flex justify-center space-x-4">
                                    <a href="#social-links" class="bg-d72641 text-white px-4 py-2 rounded hover:bg-opacity-90 transition">
                                        View Contact Info
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media and Contact Info -->
                <div class="md:w-1/3 mt-6 md:mt-0 md:pl-6 border-t md:border-t-0 md:border-l border-gray-200 pt-6 md:pt-0">
                    <div class="text-center">
                        <h2 class="text-xl font-bold mb-4 text-red-600">Check Out Our Socials</h2>
                        
                        <div class="space-y-4">
                    <div id="social-links" class="social-links">
                        <a href="https://snapchat.com/t/OPC4xQCZ" target="_blank" 
                            class="text-blue-600 hover:text-blue-800 block mb-2">
                            <i class="fab fa-snapchat inline-block mr-2"></i> Snapchat
                        </a>
                        <a href="https://www.instagram.com/ashesi.basketball" target="_blank" 
                            class="text-pink-600 hover:text-pink-800 block mb-2">
                            <i class="fab fa-instagram inline-block mr-2"></i> Instagram
                        </a>
                        <a href= "https://x.com/AshesiBballm" target="_blank" 
                            class="text-blue-400 hover:text-blue-600 block mb-2">
                            <i class="fab fa-twitter inline-block mr-2"></i> Twitter
                        </a>
                    </div>

                            <div class="contact-info mt-6">
                                <h3 class="font-semibold text-red-600 mb-2">Contact Information</h3>
                                <p class="text-sm">
                                    <i class="fas fa-envelope inline-block mr-2 text-gray-600"></i> 
                                    abahub@gmail.com
                                </p>
                                <p class="text-sm">
                                    <i class="fas fa-phone inline-block mr-2 text-gray-600"></i> 
                                    (+233) 59-52662-61
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Removed JavaScript validation since form is disabled
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Add any additional interactions or tracking
            console.log('Contact page loaded');
        });
    </script>
</body>
</html>
<?php
include 'footer.php';
?>