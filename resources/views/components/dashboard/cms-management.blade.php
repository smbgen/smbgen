@props(['formSubmissionsCount', 'pagesCount'])

<!-- CMS Management Widget -->
<div class="bg-gradient-to-br from-orange-600 dark:from-orange-600 to-orange-700 dark:to-orange-700 rounded-2xl p-6 shadow-xl">
    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
        <div class="bg-white/20 rounded-xl p-2">
            <i class="fas fa-edit text-white"></i>
        </div>
        CMS Management
    </h2>
    
    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
            <div class="text-white/80 text-sm mb-1">Form Submissions</div>
            <div class="text-white text-3xl font-bold">{{ $formSubmissionsCount }}</div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
            <div class="text-white/80 text-sm mb-1">Published Pages</div>
            <div class="text-white text-3xl font-bold">{{ $pagesCount }}</div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="space-y-2">
        <a href="{{ route('admin.leads.index') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-inbox"></i>
                Form Submissions
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        
        <a href="{{ route('admin.cms.index') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-file-alt"></i>
                Manage Pages
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        
        <a href="{{ route('admin.cms.create') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                Create New Page
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
    </div>
</div>
