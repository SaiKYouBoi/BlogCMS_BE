    </div> <!-- Close container from header.php -->
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-bold mb-4">
                        <i class="fas fa-blog mr-2"></i>BlogCMS
                    </h3>
                    <p class="text-gray-400">
                        A powerful content management system for bloggers and content creators. 
                        Share your thoughts, insights, and expertise with the world.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="/blogcms/public/posts.php" class="text-gray-400 hover:text-white">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>All Posts
                            </a>
                        </li>
                        
                            <li>
                                <a href="/blogcms/dashboard.php" class="text-gray-400 hover:text-white">
                                    <i class="fas fa-chevron-right mr-2 text-xs"></i>Dashboard
                                </a>
                            </li>
                            
                                <li>
                                    <a href="/blogcms/author/my_posts.php" class="text-gray-400 hover:text-white">
                                        <i class="fas fa-chevron-right mr-2 text-xs"></i>My Posts
                                    </a>
                                </li>
                          
                            <li>
                                <a href="/blogcms/login.php" class="text-gray-400 hover:text-white">
                                    <i class="fas fa-chevron-right mr-2 text-xs"></i>Login
                                </a>
                            </li>
                        
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>
                            <i class="fas fa-envelope mr-2"></i>contact@blogcms.com
                        </li>
                        <li>
                            <i class="fas fa-phone mr-2"></i>+1 (555) 123-4567
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt mr-2"></i>123 Blog Street, Digital City
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> BlogCMS. All rights reserved.</p>
                <p class="text-sm mt-2">Built with PHP, MySQL, and Tailwind CSS</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="/blogcms/js/main.js"></script>
</body>
</html>