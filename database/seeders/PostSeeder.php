<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Claim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        $user3 = User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create sample posts
        $posts = [
            [
                'user_id' => $user1->id,
                'title' => 'Lost iPhone 14 Pro',
                'description' => 'Lost my black iPhone 14 Pro near Central Park. It has a blue case with my initials "JD" on it. Very important as it contains family photos. Please contact me if found.',
                'category' => 'electronics',
                'location' => 'Central Park, New York',
                'date_lost_found' => now()->subDays(2),
                'type' => 'lost',
                'status' => 'active',
            ],
            [
                'user_id' => $user2->id,
                'title' => 'Found Golden Retriever',
                'description' => 'Found a friendly golden retriever wandering around Main Street. The dog is wearing a red collar but no tags. Very well-behaved and seems to be house-trained. Currently staying at my place.',
                'category' => 'pet',
                'location' => 'Main Street, Downtown',
                'date_lost_found' => now()->subDays(1),
                'type' => 'found',
                'status' => 'active',
            ],
            [
                'user_id' => $user3->id,
                'title' => 'Lost Car Keys',
                'description' => 'Lost my car keys somewhere between the university library and the parking lot. Keys have a Toyota keychain and a small flashlight attached. Desperately need them back!',
                'category' => 'keys',
                'location' => 'University Campus',
                'date_lost_found' => now()->subHours(6),
                'type' => 'lost',
                'status' => 'active',
            ],
            [
                'user_id' => $user1->id,
                'title' => 'Found Wallet',
                'description' => 'Found a brown leather wallet on the bus. Contains ID, credit cards, and some cash. No contact information visible. Turned it in to the bus company but posting here too.',
                'category' => 'other',
                'location' => 'Bus Route 42',
                'date_lost_found' => now()->subDays(3),
                'type' => 'found',
                'status' => 'resolved',
            ],
            [
                'user_id' => $user2->id,
                'title' => 'Lost Diamond Ring',
                'description' => 'Lost my grandmother\'s diamond engagement ring at the beach. It\'s a vintage piece with a small diamond in a gold setting. Extremely sentimental value. Reward offered.',
                'category' => 'jewelry',
                'location' => 'Sunset Beach',
                'date_lost_found' => now()->subDays(5),
                'type' => 'lost',
                'status' => 'still_missing',
            ],
            [
                'user_id' => $user3->id,
                'title' => 'Found Prescription Glasses',
                'description' => 'Found a pair of prescription glasses in a black case near the coffee shop. The glasses have a thin metal frame. Someone must be looking for these!',
                'category' => 'other',
                'location' => 'Coffee Corner, 5th Avenue',
                'date_lost_found' => now()->subHours(12),
                'type' => 'found',
                'status' => 'active',
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create($postData);

            // Add some comments
            if (rand(0, 1)) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $user1->id,
                    'message' => 'I hope you find it soon! I\'ll keep an eye out.',
                ]);
            }

            if (rand(0, 1)) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $user2->id,
                    'message' => 'Have you checked with local authorities?',
                ]);
            }

            // Add claims for found items
            if ($post->type === 'found' && rand(0, 1)) {
                Claim::create([
                    'post_id' => $post->id,
                    'user_id' => $user1->id,
                    'message' => 'I think this might be mine! I lost something similar in that area.',
                    'contact_info' => 'john@example.com',
                    'status' => 'pending',
                ]);
            }
        }
    }
}