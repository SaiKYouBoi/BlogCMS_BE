<?php
require_once 'includes/header.php';



if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (login($email, $password)) {
        redirect('index.php', 'Login successful!');
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<div class="min-h-[85vh] flex items-center justify-center bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8">
        <img src="../css/loginnn.png" alt="" class="m-auto">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="/blogcms/public/posts.php" class="font-medium text-[#7A8F6A] hover:text-[#5F6B5C]">
                    browse articles as guest
                </a>
            </p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="remember" value="true">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-[#d5c7b6] focus:border-[#d5c7b6] focus:z-10 sm:text-sm"
                           placeholder="Email address" value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-[#d5c7b6] focus:border-[#d5c7b6] focus:z-10 sm:text-sm"
                           placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-[#7A8F6A] hover:text-[#5F6B5C]">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-[#8A6F4E] hover:bg-[#6d5538] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d5c7b6]">
                    Sign in
                </button>
            </div>
        </form>
        </div>
</div>
<?php require_once 'includes/footer.php'; ?>