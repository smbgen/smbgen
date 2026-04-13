@props([])

@php
    // Check if cloud storage is configured for private and public files
    $privateConfig = config('filesystems.disks.private');
    $publicCloudConfig = config('filesystems.disks.public_cloud');
    
    $isPrivateCloudConfigured = isset($privateConfig['driver']) && $privateConfig['driver'] === 's3' && 
                               !empty($privateConfig['key']) && !empty($privateConfig['secret']) && !empty($privateConfig['bucket']);
    
    $isPublicCloudConfigured = isset($publicCloudConfig['driver']) && $publicCloudConfig['driver'] === 's3' && 
                              !empty($publicCloudConfig['key']) && !empty($publicCloudConfig['secret']) && !empty($publicCloudConfig['bucket']);
@endphp

@if(!$isPrivateCloudConfigured && !$isPublicCloudConfigured)
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded text-yellow-800 shadow-sm">
        <div class="flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="font-semibold">Storage Configuration</p>
                <p class="mt-1 text-sm">
                    Files are currently stored locally on the server. Cloud storage can be configured for better reliability and scalability.
                </p>
                <div class="mt-3 text-xs text-yellow-700">
                    <strong>Private Files:</strong> Local File System<br>
                    <strong>Public Files:</strong> Local File System
                </div>
            </div>
        </div>
    </div>
@else
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded text-green-800 shadow-sm">
        <div class="flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="font-semibold">Cloud Storage Configured</p>
                <p class="mt-1 text-sm">
                    Files are stored in cloud storage for enhanced reliability and accessibility.
                </p>
                <div class="mt-3 text-xs text-green-700">
                    <strong>Private Files:</strong> {{ $isPrivateCloudConfigured ? 'Cloud Storage (' . ($privateConfig['bucket'] ?? 'N/A') . ')' : 'Local File System' }}<br>
                    <strong>Public Files:</strong> {{ $isPublicCloudConfigured ? 'Cloud Storage (' . ($publicCloudConfig['bucket'] ?? 'N/A') . ')' : 'Local File System' }}
                </div>
            </div>
        </div>
    </div>
@endif