<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AccessType;
use App\Models\File;
use App\Models\FileType;
use App\Models\Role;
use App\Models\User;
use App\Models\UsersFile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Add defaults
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);

        // Add defaults for File Type
        FileType::create(['name' => 'dir']);

        // Image Types
        FileType::create(['name' => 'png']);
        FileType::create(['name' => 'jpg']);
        FileType::create(['name' => 'jpeg']);
        FileType::create(['name' => 'gif']);
        FileType::create(['name' => 'webp']);
        FileType::create(['name' => 'svg']);

        // Video Types
        FileType::create(['name' => 'mp4']);
        FileType::create(['name' => 'avi']);
        FileType::create(['name' => 'mkv']);
        FileType::create(['name' => 'mov']);
        FileType::create(['name' => 'm4b']);
        FileType::create(['name' => 'flv']);
        FileType::create(['name' => 'webm']);

        // Audio Types
        FileType::create(['name' => 'mp3']);
        FileType::create(['name' => 'wav']);
        FileType::create(['name' => 'ogg']);
        FileType::create(['name' => 'aac']);
        FileType::create(['name' => 'flac']);
        FileType::create(['name' => 'wma']);        

        // File Types
        FileType::create(['name' => 'pdf']);
        FileType::create(['name' => 'doc']);
        FileType::create(['name' => 'docx']);
        FileType::create(['name' => 'ppt']);
        FileType::create(['name' => 'pptx']);
        FileType::create(['name' => 'txt']);
        FileType::create(['name' => 'zip']);
        FileType::create(['name' => 'rar']);
        
        // Other Types
        FileType::create(['name' => 'other']);
        // ________________________________________


        // AccessTypes
        AccessType::create(['name' => 'read']);
        AccessType::create(['name' => 'write']);

        // Users
        User::create([
            'login' => 'admin',
            'email' => 'admin@mail.ru',
            'password' => 'admin',
            'role_id' => 2
        ]);
        User::create([
            'login' => 'user',
            'email' => 'user@mail.ru',
            'password' => 'user',
        ]);

        // // Files
        // File::factory(20)->create();
        
        // // UsersFiles
        // UsersFile::factory(20)->create();
    }
}
