<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\WeddingEvent;
use App\Models\InvitationSection;

class WeddingProfile extends Model
{
    protected $fillable = [
        'groom_name',
        'bride_name',
        'slug',
        'event_date',
        'venue_name',
        'venue_address',
        'opening_text',
        'story',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(WeddingEvent::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(InvitationSection::class);
    }

    public function ensureDefaultSections(): void
    {
        $defaultSections = [
            [
                'section_key' => 'cover',
                'section_label' => 'Cover',
                'sort_order' => 1,
                'is_active' => true,
                'content' => [],
            ],
            [
                'section_key' => 'couple',
                'section_label' => 'Couple',
                'sort_order' => 2,
                'is_active' => true,
                'content' => [],
            ],
            [
                'section_key' => 'quote',
                'section_label' => 'Quote',
                'sort_order' => 3,
                'is_active' => true,
                'content' => [
                    'title' => '',
                    'text' => 'And of His signs is that He created for you from yourselves mates that you may find tranquility in them.',
                    'source' => 'QS. Ar-Rum: 21',
                ],
            ],
            [
                'section_key' => 'story',
                'section_label' => 'Love Story',
                'sort_order' => 4,
                'is_active' => true,
                'content' => [],
            ],
            [
                'section_key' => 'gallery',
                'section_label' => 'Gallery',
                'sort_order' => 5,
                'is_active' => true,
                'content' => [
                    'title' => 'Gallery',
                    'images' => [],
                ],
            ],
            [
                'section_key' => 'video',
                'section_label' => 'Video',
                'sort_order' => 6,
                'is_active' => true,
                'content' => [
                    'title' => 'Our Video',
                    'video_url' => '',
                ],
            ],
            [
                'section_key' => 'save_the_date',
                'section_label' => 'Save The Date',
                'sort_order' => 7,
                'is_active' => true,
                'content' => [],
            ],
            [
                'section_key' => 'agenda',
                'section_label' => 'Agenda',
                'sort_order' => 8,
                'is_active' => true,
                'content' => [],
            ],
            [
                'section_key' => 'rsvp',
                'section_label' => 'RSVP',
                'sort_order' => 9,
                'is_active' => true,
                'content' => [
                    'title' => 'RSVP',
                    'description' => 'Please confirm your attendance.',
                ],
            ],
            [
                'section_key' => 'live_streaming',
                'section_label' => 'Live Streaming',
                'sort_order' => 10,
                'is_active' => true,
                'content' => [
                    'title' => 'Live Streaming',
                    'description' => '',
                    'thumbnail' => 'maxresdefault(2).jpg',
                    'video_id' => 'tUXguBEeRCE',
                    'watch_url' => 'https://youtu.be/tUXguBEeRCE?si=oVEheAY_-SVHUP67',
                ],
            ],
            [
                'section_key' => 'wedding_gift',
                'section_label' => 'Wedding Gift',
                'sort_order' => 11,
                'is_active' => true,
                'content' => [
                    'title' => 'Wedding Gift',
                    'description' => 'Your blessing and coming to our wedding are enough for us. However, if you want to give a gift we provide a Digital Envelope to make it easier for you. thank you',
                    'accounts' => [
                        [
                            'id' => 'bri',
                            'bank' => 'BANK BRI (002)',
                            'bank_short' => 'BANK BRI',
                            'number' => '02122333214',
                            'name' => 'Varo',
                        ],
                        [
                            'id' => 'mandiri',
                            'bank' => 'BANK MANDIRI (008)',
                            'bank_short' => 'BANK MANDIRI',
                            'number' => '0011002230',
                            'name' => 'Ansel',
                        ],
                    ],
                    'physical_gift' => [
                        'title' => 'Send us a gift',
                        'description' => 'Silahkan kirimkan hadiah kepada kedua mempelai',
                        'recipient' => 'Ansel',
                        'phone' => '082365144995',
                        'address' => 'Jalan Kenangan Raya',
                    ],
                ],
            ],
            [
                'section_key' => 'wedding_wish',
                'section_label' => 'Wedding Wish',
                'sort_order' => 12,
                'is_active' => true,
                'content' => [
                    'guest_name' => 'Katsudoto',
                    'wishes' => [
                        [
                            'name' => 'Katsudoto',
                            'date' => '17 Jan 2026, 11:00',
                            'message' => 'May your marriage be filled with all the right ingredients: a heap of love, a dash of humor, a touch of romance and a spoonful of understanding. May your joy last forever. Congratulation!🕊️',
                            'verified' => true,
                        ],
                    ],
                ],
            ],
            [
                'section_key' => 'footer',
                'section_label' => 'Footer',
                'sort_order' => 13,
                'is_active' => true,
                'content' => [],
            ],
        ];

        foreach ($defaultSections as $section) {
            $this->sections()->firstOrCreate(
                [
                    'section_key' => $section['section_key'],
                ],
                [
                    'section_label' => $section['section_label'],
                    'sort_order' => $section['sort_order'],
                    'is_active' => $section['is_active'],
                    'content' => $section['content'],
                ]
            );
        }
    }
}