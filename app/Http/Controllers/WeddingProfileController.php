<?php

namespace App\Http\Controllers;

use App\Models\WeddingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WeddingProfileController extends Controller
{
    public function edit()
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $sections = $profile->sections()
            ->orderBy('sort_order')
            ->get();

        $liveStreamingSection = $sections->firstWhere('section_key', 'live_streaming');
        $weddingGiftSection = $sections->firstWhere('section_key', 'wedding_gift');
        $weddingWishSection = $sections->firstWhere('section_key', 'wedding_wish');
        $quoteSection = $sections->firstWhere('section_key', 'quote');
        $storySection = $sections->firstWhere('section_key', 'story');
        $gallerySection = $sections->firstWhere('section_key', 'gallery');
        $videoSection = $sections->firstWhere('section_key', 'video');
        $saveDateSection = $sections->firstWhere('section_key', 'save_the_date');
        $agendaSection = $sections->firstWhere('section_key', 'agenda');
        $rsvpSection = $sections->firstWhere('section_key', 'rsvp');
        $coverSection = $sections->firstWhere('section_key', 'cover');
        $coupleSection = $sections->firstWhere('section_key', 'couple');
        $footerSection = $sections->firstWhere('section_key', 'footer');

        return view('profiles.edit', compact(
            'profile',
            'sections',
            'liveStreamingSection',
            'weddingGiftSection',
            'weddingWishSection',
            'quoteSection',
            'storySection',
            'gallerySection',
            'videoSection',
            'saveDateSection',
            'agendaSection',
            'rsvpSection',
            'coverSection',
            'coupleSection',
            'footerSection',
        ));
    }

    public function update(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $validated = $request->validate([
            'groom_name' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string'],
            'opening_text' => ['nullable', 'string'],
            'story' => ['nullable', 'string'],
        ]);

        $validated['slug'] = $validated['slug']
            ? Str::slug($validated['slug'])
            : Str::slug($validated['groom_name'] . '-dan-' . $validated['bride_name']);

        $profile->update($validated);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil wedding berhasil diperbarui.');
    }

    public function updateSections(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'integer', 'exists:invitation_sections,id'],
            'sections.*.sort_order' => ['nullable', 'integer', 'min:1', 'max:999'],
            'sections.*.is_active' => ['nullable'],
        ]);

        foreach ($validated['sections'] as $sectionInput) {
            $section = $profile->sections()
                ->where('id', $sectionInput['id'])
                ->first();

            if (! $section) {
                continue;
            }

            $section->update([
                'is_active' => array_key_exists('is_active', $sectionInput),
                'sort_order' => $sectionInput['sort_order'] ?? $section->sort_order,
            ]);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Pengaturan section undangan berhasil diperbarui.');
    }

    public function updateLiveStreamingSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'thumbnail_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=640,min_height=360',
            ],
            'video_id' => ['nullable', 'string', 'max:255'],
            'watch_url' => ['nullable', 'url', 'max:500'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'live_streaming')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $thumbnail = $currentContent['thumbnail'] ?? 'maxresdefault(2).jpg';

        if (! empty($validated['thumbnail'])) {
            $thumbnail = $validated['thumbnail'];
        }

        if ($request->hasFile('thumbnail_file')) {
            $thumbnail = $request->file('thumbnail_file')->store('live-streaming', 'public');
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['title'] ?: 'Live Streaming',
                'description' => $validated['description'] ?? '',
                'thumbnail' => $thumbnail ?: 'maxresdefault(2).jpg',
                'video_id' => $validated['video_id'] ?: '',
                'watch_url' => $validated['watch_url'] ?: '',
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Live Streaming berhasil diperbarui.');
    }

    public function updateWeddingGiftSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'accounts' => ['nullable', 'array'],
            'accounts.*.id' => ['nullable', 'string', 'max:100'],
            'accounts.*.bank' => ['nullable', 'string', 'max:255'],
            'accounts.*.bank_short' => ['nullable', 'string', 'max:255'],
            'accounts.*.number' => ['nullable', 'string', 'max:255'],
            'accounts.*.name' => ['nullable', 'string', 'max:255'],

            'physical_gift.title' => ['nullable', 'string', 'max:255'],
            'physical_gift.description' => ['nullable', 'string'],
            'physical_gift.recipient' => ['nullable', 'string', 'max:255'],
            'physical_gift.phone' => ['nullable', 'string', 'max:255'],
            'physical_gift.address' => ['nullable', 'string'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'wedding_gift')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $accounts = collect($validated['accounts'] ?? [])
            ->map(function ($account, $index) {
                $bank = trim($account['bank'] ?? '');
                $number = trim($account['number'] ?? '');
                $name = trim($account['name'] ?? '');

                if ($bank === '' && $number === '' && $name === '') {
                    return null;
                }

                $bankShort = trim($account['bank_short'] ?? '');

                return [
                    'id' => trim($account['id'] ?? '') ?: 'bank_' . ($index + 1),
                    'bank' => $bank,
                    'bank_short' => $bankShort ?: $bank,
                    'number' => $number,
                    'name' => $name,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        if (count($accounts) === 0) {
            $accounts = $currentContent['accounts'] ?? [];
        }

        $physicalGift = $validated['physical_gift'] ?? [];

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['title'] ?: 'Wedding Gift',
                'description' => $validated['description'] ?? '',
                'accounts' => $accounts,
                'physical_gift' => [
                    'title' => $physicalGift['title'] ?? 'Send us a gift',
                    'description' => $physicalGift['description'] ?? '',
                    'recipient' => $physicalGift['recipient'] ?? '',
                    'phone' => $physicalGift['phone'] ?? '',
                    'address' => $physicalGift['address'] ?? '',
                ],
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Wedding Gift berhasil diperbarui.');
    }

    public function updateWeddingWishSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:255'],

            'wishes' => ['nullable', 'array'],
            'wishes.*.name' => ['nullable', 'string', 'max:255'],
            'wishes.*.date' => ['nullable', 'string', 'max:255'],
            'wishes.*.message' => ['nullable', 'string'],
            'wishes.*.verified' => ['nullable'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'wedding_wish')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $wishes = collect($validated['wishes'] ?? [])
            ->map(function ($wish) {
                $name = trim($wish['name'] ?? '');
                $date = trim($wish['date'] ?? '');
                $message = trim($wish['message'] ?? '');

                if ($name === '' && $date === '' && $message === '') {
                    return null;
                }

                return [
                    'name' => $name ?: 'Guest',
                    'date' => $date ?: now()->format('d M Y, H:i'),
                    'message' => $message,
                    'verified' => array_key_exists('verified', $wish),
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        if (count($wishes) === 0) {
            $wishes = $currentContent['wishes'] ?? [];
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['title'] ?: 'Wedding Wish',
                'guest_name' => $validated['guest_name'] ?: 'Katsudoto',
                'placeholder' => $validated['placeholder'] ?: 'Give your wish',
                'button_text' => $validated['button_text'] ?: 'Send',
                'wishes' => $wishes,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Wedding Wish berhasil diperbarui.');
    }

    public function updateQuoteSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'quote_text' => ['nullable', 'string'],
            'quote_source' => ['nullable', 'string', 'max:255'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'quote')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'text' => $validated['quote_text']
                    ?: '“Love is not about finding someone to live with, but finding someone you can’t imagine life without.”',
                'source' => $validated['quote_source'] ?? '',
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Quote berhasil diperbarui.');
    }

    public function updateStorySection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'story_title' => ['nullable', 'string', 'max:255'],

            'story_items' => ['nullable', 'array'],
            'story_items.*.title' => ['nullable', 'string', 'max:255'],
            'story_items.*.date' => ['nullable', 'string', 'max:255'],
            'story_items.*.description' => ['nullable', 'string'],
            'story_items.*.existing_image' => ['nullable', 'string', 'max:500'],
            'story_items.*.image_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=720,min_height=960',
            ],
        ], [
            'story_items.*.image_file.image' => 'File story harus berupa gambar.',
            'story_items.*.image_file.mimes' => 'Format foto story harus JPG, JPEG, PNG, atau WEBP.',
            'story_items.*.image_file.max' => 'Ukuran foto story maksimal 4 MB.',
            'story_items.*.image_file.dimensions' => 'Ukuran foto story minimal 720×960 px. Gunakan foto portrait agar tidak banyak terpotong.',
        ]);

        $section = $profile->sections()
            ->where('section_key', 'story')
            ->firstOrFail();

        $currentContent = $section->content ?? [];
        $inputItems = $request->input('story_items', []);

        $items = [];

        foreach ($inputItems as $index => $item) {
            $title = trim($item['title'] ?? '');
            $date = trim($item['date'] ?? '');
            $description = trim($item['description'] ?? '');
            $existingImage = trim($item['existing_image'] ?? '');

            $uploadedFile = $request->file("story_items.$index.image_file");

            if ($title === '' && $date === '' && $description === '' && ! $uploadedFile && $existingImage === '') {
                continue;
            }

            $imagePath = $existingImage ?: 'bg-cover.png';

            if ($uploadedFile) {
                $imagePath = $uploadedFile->store('story', 'public');
            }

            $items[] = [
                'title' => $title ?: 'Our Moment',
                'date' => $date,
                'description' => $description,
                'image' => $imagePath,
            ];
        }

        if (count($items) === 0) {
            $items = $currentContent['items'] ?? [];
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['story_title'] ?: 'Our Story',
                'items' => $items,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Our Story berhasil diperbarui.');
    }

    public function updateGallerySection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'gallery_title' => ['nullable', 'string', 'max:255'],

            'gallery_items' => ['nullable', 'array'],
            'gallery_items.*.caption' => ['nullable', 'string', 'max:255'],
            'gallery_items.*.existing_image' => ['nullable', 'string', 'max:500'],
            'gallery_items.*.image_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=720,min_height=720',
            ],
        ], [
            'gallery_items.*.image_file.image' => 'File gallery harus berupa gambar.',
            'gallery_items.*.image_file.mimes' => 'Format foto gallery harus JPG, JPEG, PNG, atau WEBP.',
            'gallery_items.*.image_file.max' => 'Ukuran foto gallery maksimal 4 MB.',
            'gallery_items.*.image_file.dimensions' => 'Ukuran foto gallery minimal 720×720 px.',
        ]);

        $section = $profile->sections()
            ->where('section_key', 'gallery')
            ->firstOrFail();

        $currentContent = $section->content ?? [];
        $inputItems = $request->input('gallery_items', []);

        $items = [];

        foreach ($inputItems as $index => $item) {
            $caption = trim($item['caption'] ?? '');
            $existingImage = trim($item['existing_image'] ?? '');

            $uploadedFile = $request->file("gallery_items.$index.image_file");

            if ($caption === '' && ! $uploadedFile && $existingImage === '') {
                continue;
            }

            $imagePath = $existingImage ?: 'bg-cover.png';

            if ($uploadedFile) {
                $imagePath = $uploadedFile->store('gallery', 'public');
            }

            $items[] = [
                'caption' => $caption,
                'image' => $imagePath,
            ];
        }

        if (count($items) === 0) {
            $items = $currentContent['items'] ?? [];
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['gallery_title'] ?: 'Portraits of Us',
                'items' => $items,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Gallery berhasil diperbarui.');
    }

    public function updateVideoSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'video_title' => ['nullable', 'string', 'max:255'],
            'video_description' => ['nullable', 'string'],
            'youtube_video_id' => ['nullable', 'string', 'max:120'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'video')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $youtubeUrl = trim($validated['youtube_url'] ?? '');
        $videoId = trim($validated['youtube_video_id'] ?? '');

        if ($videoId === '' && $youtubeUrl !== '') {
            $host = parse_url($youtubeUrl, PHP_URL_HOST) ?: '';
            $path = parse_url($youtubeUrl, PHP_URL_PATH) ?: '';
            $queryString = parse_url($youtubeUrl, PHP_URL_QUERY) ?: '';

            parse_str($queryString, $query);

            if (!empty($query['v'])) {
                $videoId = $query['v'];
            } elseif (str_contains($host, 'youtu.be')) {
                $videoId = trim($path, '/');
            } elseif (str_contains($path, '/embed/')) {
                $videoId = trim(str_replace('/embed/', '', $path), '/');
            } elseif (str_contains($path, '/shorts/')) {
                $videoId = trim(str_replace('/shorts/', '', $path), '/');
            }

            $videoId = strtok($videoId, '?');
            $videoId = strtok($videoId, '&');
        }

        if ($videoId === '') {
            $videoId = $currentContent['youtube_video_id'] ?? 'kfsqKYsxs0o';
        }

        $embedUrl = 'https://www.youtube.com/embed/' . $videoId . '?rel=0&modestbranding=1';

        if ($youtubeUrl === '') {
            $youtubeUrl = 'https://www.youtube.com/watch?v=' . $videoId;
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['video_title'] ?: 'Our Footage',
                'description' => $validated['video_description'] ?? '',
                'youtube_video_id' => $videoId,
                'youtube_url' => $youtubeUrl,
                'embed_url' => $embedUrl,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Video berhasil diperbarui.');
    }

    public function updateSaveDateSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'event_date' => ['nullable', 'date'],
            'save_date_title' => ['nullable', 'string', 'max:255'],
            'save_date_button_text' => ['nullable', 'string', 'max:255'],
            'calendar_title' => ['nullable', 'string', 'max:255'],
            'calendar_details' => ['nullable', 'string'],
            'calendar_duration_minutes' => ['nullable', 'integer', 'min:30', 'max:720'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'save_the_date')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $profile->update([
            'event_date' => $validated['event_date'] ?? null,
        ]);

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['save_date_title'] ?: 'Save the date',
                'button_text' => $validated['save_date_button_text'] ?: 'Add to Calendar',
                'calendar_title' => $validated['calendar_title']
                    ?: trim($profile->groom_name . ' & ' . $profile->bride_name . ' Wedding'),
                'calendar_details' => $validated['calendar_details']
                    ?: 'You are invited to our wedding ceremony.',
                'calendar_duration_minutes' => $validated['calendar_duration_minutes'] ?: 180,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Save The Date berhasil diperbarui.');
    }

    public function updateAgendaSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'agenda_title' => ['nullable', 'string', 'max:255'],
            'agenda_day' => ['nullable', 'string', 'max:255'],
            'agenda_date' => ['nullable', 'string', 'max:255'],
            'agenda_same_location' => ['nullable'],

            'agenda_maps_button_text' => ['nullable', 'string', 'max:255'],

            'activities' => ['nullable', 'array'],
            'activities.*.title' => ['nullable', 'string', 'max:255'],
            'activities.*.time' => ['nullable', 'string', 'max:255'],
            'activities.*.hall' => ['nullable', 'string', 'max:255'],
            'activities.*.address' => ['nullable', 'string'],
            'activities.*.city' => ['nullable', 'string', 'max:255'],
            'activities.*.maps_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'agenda')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $activities = collect($request->input('activities', []))
            ->map(function ($activity, $index) {
                $title = trim($activity['title'] ?? '');
                $time = trim($activity['time'] ?? '');
                $hall = trim($activity['hall'] ?? '');
                $address = trim($activity['address'] ?? '');
                $city = trim($activity['city'] ?? '');
                $mapsUrl = trim($activity['maps_url'] ?? '');

                if (
                    $title === '' &&
                    $time === '' &&
                    $hall === '' &&
                    $address === '' &&
                    $city === '' &&
                    $mapsUrl === ''
                ) {
                    return null;
                }

                return [
                    'title' => $title ?: 'Acara ' . ($index + 1),
                    'time' => $time,
                    'hall' => $hall,
                    'address' => $address,
                    'city' => $city,
                    'maps_url' => $mapsUrl ?: 'https://maps.google.com',
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        if (count($activities) === 0) {
            $activities = $currentContent['activities'] ?? [
                [
                    'title' => 'Akad Nikah',
                    'time' => '09:00 - 10:00',
                    'hall' => 'Mason Pine Hotel',
                    'address' => 'Jl. Parahyangan Raya No.KM 1, RW.8, Cipeundeuy, Kec. Padalarang, Jawa Barat',
                    'city' => 'Kabupaten Bandung Barat',
                    'maps_url' => 'https://maps.google.com/?cid=16992323544258832489',
                ],
                [
                    'title' => 'Resepsi',
                    'time' => '11:00 - 14:00',
                    'hall' => 'Mason Pine Hotel',
                    'address' => 'Jl. Parahyangan Raya No.KM 1, RW.8, Cipeundeuy, Kec. Padalarang, Jawa Barat',
                    'city' => 'Kabupaten Bandung Barat',
                    'maps_url' => 'https://maps.google.com/?cid=16992323544258832489',
                ],
            ];
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['agenda_title'] ?: "It's Wedding Day",
                'day' => $validated['agenda_day'] ?: 'Saturday,',
                'date' => $validated['agenda_date'] ?: '31 January 2026',
                'same_location' => $request->boolean('agenda_same_location'),
                'maps_button_text' => $validated['agenda_maps_button_text'] ?: 'View Maps',
                'activities' => $activities,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Agenda berhasil diperbarui.');
    }

    public function updateDressCodeSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'dress_title' => ['nullable', 'string', 'max:255'],
            'dress_description' => ['nullable', 'string'],
            'dress_note' => ['nullable', 'string'],

            'dress_men_label' => ['nullable', 'string', 'max:255'],
            'dress_women_label' => ['nullable', 'string', 'max:255'],
            'dress_men_style_label' => ['nullable', 'string', 'max:255'],
            'dress_women_style_label' => ['nullable', 'string', 'max:255'],

            'dress_colors' => ['nullable', 'array', 'size:4'],
            'dress_colors.*' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'dress_colors.size' => 'Dresscode harus memiliki 4 warna.',
            'dress_colors.*.regex' => 'Kode warna harus memakai format HEX, contoh: #7F0404.',
        ]);

        $section = $profile->sections()
            ->where('section_key', 'agenda')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $colors = collect($validated['dress_colors'] ?? [])
            ->map(fn ($color) => strtoupper(trim($color ?? '')))
            ->filter(fn ($color) => preg_match('/^#[0-9A-F]{6}$/', $color))
            ->values()
            ->toArray();

        if (count($colors) !== 4) {
            $colors = $currentContent['dress_code']['colors'] ?? [
                '#7F0404',
                '#1C3106',
                '#D2CEAE',
                '#E6E3E4',
            ];
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'dress_code' => [
                    'title' => $validated['dress_title'] ?: 'Dresscode',
                    'description' => $validated['dress_description']
                        ?: 'Wear a long gown or formal gown, black tie or bow tie',
                    'note' => $validated['dress_note']
                        ?: 'We kindly ask that guests please attend wearing our wedding colors.',

                    'men_label' => $validated['dress_men_label'] ?: 'Men',
                    'women_label' => $validated['dress_women_label'] ?: 'Women',
                    'men_style_label' => $validated['dress_men_style_label'] ?: 'Formal',
                    'women_style_label' => $validated['dress_women_style_label'] ?: 'Formal',

                    'colors' => $colors,
                ],
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Dresscode berhasil diperbarui.');
    }

    public function updateRsvpSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'rsvp_title' => ['nullable', 'string', 'max:255'],
            'rsvp_description' => ['nullable', 'string'],

            'rsvp_status_question' => ['nullable', 'string', 'max:255'],
            'rsvp_attend_text' => ['nullable', 'string', 'max:255'],
            'rsvp_not_attend_text' => ['nullable', 'string', 'max:255'],

            'rsvp_session_question' => ['nullable', 'string', 'max:255'],
            'rsvp_event_1_label' => ['nullable', 'string', 'max:255'],
            'rsvp_event_2_label' => ['nullable', 'string', 'max:255'],
            'rsvp_all_events_text' => ['nullable', 'string', 'max:255'],

            'rsvp_total_question' => ['nullable', 'string', 'max:255'],
            'rsvp_max_attendance' => ['nullable', 'integer', 'min:1', 'max:20'],

            'rsvp_submit_text' => ['nullable', 'string', 'max:255'],
            'rsvp_change_text' => ['nullable', 'string', 'max:255'],

            'rsvp_success_attend_title' => ['nullable', 'string', 'max:255'],
            'rsvp_success_attend_caption' => ['nullable', 'string'],
            'rsvp_success_not_attend_title' => ['nullable', 'string', 'max:255'],
            'rsvp_success_not_attend_caption' => ['nullable', 'string'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'rsvp')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => $validated['rsvp_title'] ?: 'RSVP',
                'description' => $validated['rsvp_description']
                    ?: 'Please confirm your attendance to help us prepare the best seat for you.',

                'status_question' => $validated['rsvp_status_question'] ?: 'Apakah kamu datang?',
                'attend_text' => $validated['rsvp_attend_text'] ?: 'Hadir',
                'not_attend_text' => $validated['rsvp_not_attend_text'] ?: 'Tidak Hadir',

                'session_question' => $validated['rsvp_session_question'] ?: 'Acara mana yang akan Anda hadiri?',
                'events' => [
                    [
                        'value' => 'akad',
                        'label' => $validated['rsvp_event_1_label'] ?: 'Akad Nikah',
                    ],
                    [
                        'value' => 'resepsi',
                        'label' => $validated['rsvp_event_2_label'] ?: 'Resepsi',
                    ],
                ],
                'all_events_text' => $validated['rsvp_all_events_text'] ?: 'Hadir Semua',

                'total_question' => $validated['rsvp_total_question'] ?: 'Jumlah tamu yang datang termasuk kamu?',
                'max_attendance' => $validated['rsvp_max_attendance'] ?: 10,

                'submit_text' => $validated['rsvp_submit_text'] ?: 'Send RSVP',
                'change_text' => $validated['rsvp_change_text'] ?: 'Change',

                'success_attend_title' => $validated['rsvp_success_attend_title'] ?: 'Will Attend',
                'success_attend_caption' => $validated['rsvp_success_attend_caption']
                    ?: 'Yeay, Thank you for the attendance. See you there ;)',

                'success_not_attend_title' => $validated['rsvp_success_not_attend_title'] ?: 'Unable to Attend',
                'success_not_attend_caption' => $validated['rsvp_success_not_attend_caption']
                    ?: 'Thank you for confirming. Your wishes mean a lot to us.',
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten RSVP berhasil diperbarui.');
    }

    public function updateCoverSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'cover_opening_button_text' => ['nullable', 'string', 'max:255'],
            'cover_label' => ['nullable', 'string', 'max:255'],
            'cover_couple_name' => ['nullable', 'string', 'max:255'],
            'cover_hashtag' => ['nullable', 'string', 'max:255'],

            'cover_loader_enabled' => ['nullable'],
            'cover_loader_mark_type' => ['nullable', 'string', 'in:initial,logo'],
            'cover_loader_mark' => ['nullable', 'string', 'max:50'],
            'cover_loading_text' => ['nullable', 'string', 'max:255'],

            'cover_opening_subtitle' => ['nullable', 'string', 'max:255'],
            'cover_opening_greeting_prefix' => ['nullable', 'string', 'max:100'],
            'cover_opening_video' => ['nullable', 'string', 'max:255'],
            'cover_opening_poster' => ['nullable', 'string', 'max:255'],

            'cover_logo_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=300,min_height=300',
            ],

            'cover_main_image_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:10240',
                'dimensions:min_width=720,min_height=960',
            ],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'cover')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $logoPath = $currentContent['logo'] ?? null;
        $mainImagePath = $currentContent['main_image'] ?? null;

        if ($request->hasFile('cover_logo_file')) {
            $logoPath = $request->file('cover_logo_file')->store('cover', 'public');
        }

        if ($request->hasFile('cover_main_image_file')) {
            $mainImagePath = $request->file('cover_main_image_file')->store('cover', 'public');
        }

        $defaultCoupleName = trim(($profile->groom_name ?? 'Ansel') . ' & ' . ($profile->bride_name ?? 'Varo'));

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'opening_button_text' => $validated['cover_opening_button_text'] ?: 'Open Invitation',
                'label' => $validated['cover_label'] ?: 'Wedding Invitation',
                'couple_name' => $validated['cover_couple_name'] ?: $defaultCoupleName,
                'hashtag' => $validated['cover_hashtag'] ?: '#AnselVaroInLove',

                'loader_enabled' => $request->boolean('cover_loader_enabled'),
                'loader_mark_type' => $validated['cover_loader_mark_type'] ?? 'initial',
                'loader_mark' => $validated['cover_loader_mark'] ?: '',
                'loading_text' => $validated['cover_loading_text'] ?: 'One moment...',

                'opening_subtitle' => $validated['cover_opening_subtitle'] ?: 'The Wedding Of',
                'greeting_prefix' => $validated['cover_opening_greeting_prefix'] ?: 'Hai',
                'opening_video' => $validated['cover_opening_video'] ?: 'vid-comp.mp4',
                'opening_poster' => $validated['cover_opening_poster'] ?: 'bg-cover.png',

                'logo' => $logoPath,
                'main_image' => $mainImagePath,
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Cover berhasil diperbarui.');
    }

    public function updateCoupleSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'couple_title' => ['nullable', 'string', 'max:255'],
            'couple_description' => ['nullable', 'string'],
            'couple_bride_first' => ['nullable'],

            'groom_name' => ['nullable', 'string', 'max:255'],
            'groom_parents' => ['nullable', 'string'],
            'groom_instagram' => ['nullable', 'string', 'max:255'],
            'groom_instagram_url' => ['nullable', 'url', 'max:1000'],
            'groom_photo_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=720,min_height=720',
            ],

            'bride_name' => ['nullable', 'string', 'max:255'],
            'bride_parents' => ['nullable', 'string'],
            'bride_instagram' => ['nullable', 'string', 'max:255'],
            'bride_instagram_url' => ['nullable', 'url', 'max:1000'],
            'bride_photo_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=720,min_height=720',
            ],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'couple')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $groomPhoto = $currentContent['groom']['photo'] ?? null;
        $bridePhoto = $currentContent['bride']['photo'] ?? null;

        if ($request->hasFile('groom_photo_file')) {
            $groomPhoto = $request->file('groom_photo_file')->store('couple', 'public');
        }

        if ($request->hasFile('bride_photo_file')) {
            $bridePhoto = $request->file('bride_photo_file')->store('couple', 'public');
        }

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'order' => $request->boolean('couple_bride_first')
                    ? 'bride_first'
                    : 'groom_first',

                'title' => ($validated['couple_title'] ?? null)
                    ?: 'Two souls intertwined, a love that will bind',

                'description' => ($validated['couple_description'] ?? null)
                    ?: 'They say that some souls are simply meant to find each other. Ours did, and with each shared moment, our connection has grown into a love that will forever bind us. We are so excited to celebrate this beautiful journey with you as we exchange our vows.',

                'groom' => [
                    'name' => ($validated['groom_name'] ?? null) ?: 'Varo Brian',
                    'parents' => ($validated['groom_parents'] ?? null)
                        ?: "The Son of <br> Mr. Lerry Brian <br> & Mrs. Lenny Diah",
                    'instagram' => ($validated['groom_instagram'] ?? null) ?: '@katsudoto',
                    'instagram_url' => ($validated['groom_instagram_url'] ?? null)
                        ?: 'https://www.instagram.com/katsudoto',
                    'photo' => $groomPhoto ?: 'thumb-lg-718214-1200-1200-1761643646-157cb5cea53a4db6e8d9c77e.webp',
                ],

                'bride' => [
                    'name' => ($validated['bride_name'] ?? null) ?: 'Ansel Ginny',
                    'parents' => ($validated['bride_parents'] ?? null)
                        ?: "The Daughter of <br> Mr. Darwin Davidson <br> & Mrs. Jenny Smith",
                    'instagram' => ($validated['bride_instagram'] ?? null) ?: null,
                    'instagram_url' => ($validated['bride_instagram_url'] ?? null) ?: null,
                    'photo' => $bridePhoto ?: 'thumb-lg-718213-1200-1200-1761643619-f6200e0577ad7d5431553861.webp',
                ],
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Couple berhasil diperbarui.');
    }

    public function updateFooterSection(Request $request)
    {
        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $profile->ensureDefaultSections();

        $validated = $request->validate([
            'footer_title' => ['nullable', 'string', 'max:255'],
            'footer_description' => ['nullable', 'string'],
        ]);

        $section = $profile->sections()
            ->where('section_key', 'footer')
            ->firstOrFail();

        $currentContent = $section->content ?? [];

        $section->update([
            'content' => array_replace_recursive($currentContent, [
                'title' => ($validated['footer_title'] ?? null) ?: 'Thank You',
                'description' => ($validated['footer_description'] ?? null)
                    ?: 'With love and gratitude, thank you for celebrating with us.',
            ]),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Konten Footer berhasil diperbarui.');
    }
}