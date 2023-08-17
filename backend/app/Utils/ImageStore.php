<?php

namespace App\Utils;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageStore
{

    /**
     * This method is used to get a cache value
     * @param string $filePath The cache key
     * @return null|mixed
     */
    public static function has(String $filePath)
    {
        return Storage::exists($filePath);
    }


    /**
     * This method is used to get a cache value
     * @param string $filePath The cache key
     * @return null|mixed
     */
    public static function get(String $filePath)
    {
        return Storage::temporaryUrl($filePath, now()->addMinute(5));
    }

    /**
     * This method is used to put a value in bucket s3
     * @param File $file The file to be stored
     * @param string $filePath The file path to be stored
     * @return null|mixed
     * @throws Exception If the cache key does not exist
     */
    public static function put(String $filePath, Object $file)
    {
        // Uploads the image to S3 using the defined path and name
        try {
            Storage::put($filePath, $file);
        } catch(Exception $err) {
            Log::error('Erro ao salvar imagem no banco', [
                'erro' => $err
            ]);
            return throw new Exception("Error on save image"); 
        }

        return self::get($filePath);
    }

    /**
     * This method is used to put a value in bucket s3
     * @param File $file The file to be stored
     * @param string $filePath The file path to be stored
     * @return null|mixed
     * @throws Exception If the cache key does not exist
     */
    public static function update(String $filePath, Object $file)
    {
        // Checks if the previous image exists and deletes it before loading the new image
        if (self::has($filePath)) self::delete($filePath);

        // Upload the image to S3 using the defined path and name
        try {

            self::put($filePath, $file);

        } catch(Exception $err) {
            Log::error('Erro ao salvar imagem no banco', [
                'erro' => $err
            ]);

            return throw new Exception("Error on update image"); 
        }

        return self::get($filePath);
    }

         /**
     * This method is used to delete a value in cache
     * @param string $filePath The bucket s3 filepath
     * @return void
     */
    public static function delete(string $filePath): void
    {
        Storage::delete($filePath);
    }
}
<?php

namespace App\Utils;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageStore
{

    /**
     * This method is used to get a cache value
     * @param string $filePath The cache key
     * @return null|mixed
     */
    public static function has(String $filePath)
    {
        return Storage::exists($filePath);
    }


    /**
     * This method is used to get a cache value
     * @param string $filePath The cache key
     * @return null|mixed
     */
    public static function get(String $filePath)
    {
        return Storage::temporaryUrl($filePath, now()->addMinute(5));
    }

    /**
     * This method is used to put a value in bucket s3
     * @param File $file The file to be stored
     * @param string $filePath The file path to be stored
     * @return null|mixed
     * @throws Exception If the cache key does not exist
     */
    public static function put(String $filePath, Object $file)
    {
        // Uploads the image to S3 using the defined path and name
        try {
            Storage::put($filePath, $file);
        } catch(Exception $err) {
            Log::error('Erro ao salvar imagem no banco', [
                'erro' => $err
            ]);
            return throw new Exception("Error on save image"); 
        }

        return self::get($filePath);
    }

    /**
     * This method is used to put a value in bucket s3
     * @param File $file The file to be stored
     * @param string $filePath The file path to be stored
     * @return null|mixed
     * @throws Exception If the cache key does not exist
     */
    public static function update(String $filePath, Object $file)
    {
        // Checks if the previous image exists and deletes it before loading the new image
        if (self::has($filePath)) self::delete($filePath);

        // Upload the image to S3 using the defined path and name
        try {

            self::put($filePath, $file);

        } catch(Exception $err) {
            Log::error('Erro ao salvar imagem no banco', [
                'erro' => $err
            ]);

            return throw new Exception("Error on update image"); 
        }

        return self::get($filePath);
    }

         /**
     * This method is used to delete a value in cache
     * @param string $filePath The bucket s3 filepath
     * @return void
     */
    public static function delete(string $filePath): void
    {
        Storage::delete($filePath);
    }
}