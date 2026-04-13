<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
        <div class="bg-gradient-to-r from-indigo-600 dark:from-indigo-500 to-purple-600 dark:to-purple-500 rounded-xl p-2">
            <i class="fas fa-users-cog text-white"></i>
        </div>
        User Administration
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <a href="{{ route('admin.users.index') }}" 
           class="bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl p-4 border border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-200 group">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-100 dark:bg-indigo-500/20 rounded-lg p-3 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-500/30 transition-colors">
                    <i class="fas fa-users text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-semibold">All Users</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">View all users</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.create') }}" 
           class="bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl p-4 border border-gray-300 dark:border-gray-600 hover:border-green-400 dark:hover:border-green-500 transition-all duration-200 group">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 dark:bg-green-500/20 rounded-lg p-3 group-hover:bg-green-200 dark:group-hover:bg-green-500/30 transition-colors">
                    <i class="fas fa-user-plus text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-semibold">New User</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">Create user account</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.edit', auth()->user()) }}" 
           class="bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl p-4 border border-gray-300 dark:border-gray-600 hover:border-purple-400 dark:hover:border-purple-500 transition-all duration-200 group">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 dark:bg-purple-500/20 rounded-lg p-3 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                    <i class="fas fa-user-edit text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-semibold">Edit Users</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">Manage user details</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.google-oauth') }}" 
           class="bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl p-4 border border-gray-300 dark:border-gray-600 hover:border-orange-400 dark:hover:border-orange-500 transition-all duration-200 group">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 dark:bg-orange-500/20 rounded-lg p-3 group-hover:bg-orange-200 dark:group-hover:bg-orange-500/30 transition-colors">
                    <i class="fab fa-google text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-semibold">Google OAuth</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">OAuth integration</div>
                </div>
            </div>
        </a>

        <a href="{{ route('profile.edit') }}" 
           class="bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl p-4 border border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-200 group">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 dark:bg-blue-500/20 rounded-lg p-3 group-hover:bg-blue-200 dark:group-hover:bg-blue-500/30 transition-colors">
                    <i class="fas fa-user-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-semibold">Your Profile</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">Edit your account</div>
                </div>
            </div>
        </a>
    </div>
</div>
