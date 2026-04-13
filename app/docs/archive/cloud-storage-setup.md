# Cloud Storage Setup Guide

This application supports multiple cloud storage providers for file management. Choose the option that best fits your infrastructure.

## Local Development (Default)

For local development with Laravel Herd or Valet:
```env
FILESYSTEM_DRIVER=local
```

Files are stored in:
- Private: `storage/app/private/`
- Public: `storage/app/public/`

## Option 1: Google Cloud Storage (GCS)

### Prerequisites
1. Install the Google Cloud Storage adapter:
```bash
composer require league/flysystem-google-cloud-storage
```

2. Create a Google Cloud Storage bucket:
   - Go to [Google Cloud Console](https://console.cloud.google.com)
   - Navigate to Cloud Storage > Buckets
   - Create a new bucket (e.g., `smbgen-files`)
   - Set appropriate location and storage class

3. Create a Service Account:
   - Go to IAM & Admin > Service Accounts
   - Create a new service account
   - Grant "Storage Object Admin" role
   - Create and download a JSON key file

### Configuration

Add to your `.env`:
```env
FILESYSTEM_DRIVER=gcs

GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_STORAGE_BUCKET=smbgen-files
GOOGLE_CLOUD_KEY_FILE=/path/to/service-account.json
```

### Production Best Practices (GCS)
- Use Workload Identity or Application Default Credentials instead of key files
- Set up bucket lifecycle policies to manage old files
- Enable versioning for important files
- Configure CORS if serving files directly to browsers
- Use Cloud CDN for public files

## Option 2: AWS S3

### Prerequisites
1. Install the AWS S3 adapter:
```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

2. Create an S3 bucket in AWS Console
3. Create IAM user with S3 permissions

### Configuration

Add to your `.env`:
```env
FILESYSTEM_DRIVER=s3

AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=smbgen-files
```

### Production Best Practices (S3)
- Use IAM roles instead of access keys when possible
- Set up S3 lifecycle policies
- Enable versioning
- Configure CloudFront CDN for better performance
- Use S3 Intelligent-Tiering for cost optimization

## Option 3: S3-Compatible Storage

Many providers offer S3-compatible APIs (DigitalOcean Spaces, Cloudflare R2, MinIO, etc.):

```env
FILESYSTEM_DRIVER=s3

AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_ENDPOINT=https://your-endpoint.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

## Laravel Cloud Deployment

For Laravel Cloud, the platform typically provides:
- Automatic S3 bucket provisioning
- Pre-configured credentials
- CDN integration

Check your Laravel Cloud dashboard for specific configuration values.

## Testing Cloud Storage

After configuring, test file uploads:

```bash
php artisan tinker

# Test upload
Storage::disk('private')->put('test.txt', 'Hello Cloud!');

# Verify it exists
Storage::disk('private')->exists('test.txt');

# Read it back
Storage::disk('private')->get('test.txt');

# Clean up
Storage::disk('private')->delete('test.txt');
```

## Migration from Local to Cloud

To migrate existing files:

1. **Backup your files first!**
2. Use this Artisan command to sync files:

```bash
# Sync private files
php artisan storage:sync local private

# Sync public files
php artisan storage:sync public public_cloud
```

3. Update your `.env` to use cloud storage
4. Test thoroughly before removing local files

## Cost Optimization

### Google Cloud Storage
- Use Standard class for frequently accessed files
- Use Nearline/Coldline for archives
- Set up lifecycle rules to automatically transition old files
- Estimated: ~$0.02/GB/month (Standard)

### AWS S3
- Use S3 Standard for active files
- Use S3 Glacier for long-term archives
- Enable S3 Intelligent-Tiering
- Estimated: ~$0.023/GB/month (Standard)

## Security Considerations

1. **Private Files**: Always stored with private visibility
2. **Public Files**: Only accessible via signed URLs or public paths
3. **Access Control**: Enforced at application level (authentication required)
4. **Encryption**: Enable bucket encryption at rest
5. **HTTPS Only**: All transfers use TLS

## Troubleshooting

### "Class 'League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter' not found"
Install the adapter: `composer require league/flysystem-google-cloud-storage`

### "Could not load the default credentials"
For GCS, ensure your service account JSON is accessible and the path is correct.

### Files not appearing
Check bucket permissions and ensure the service account has proper roles.

### Slow uploads/downloads
Consider using a CDN (Cloud CDN for GCS, CloudFront for S3).

## Support

For issues specific to:
- **Google Cloud Storage**: [GCS Documentation](https://cloud.google.com/storage/docs)
- **AWS S3**: [S3 Documentation](https://docs.aws.amazon.com/s3/)
- **Laravel Storage**: [Laravel Docs](https://laravel.com/docs/filesystem)
