<?php

use App\Http\Controllers\WeddingSyncV2StatusController;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\WeddingProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\WeddingEventController;
use App\Http\Controllers\AnselmaPreviewController;
use App\Http\Controllers\SyncController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('login.store');
});

Route::post('/logout', [AdminAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    Route::resource('wedding-events', WeddingEventController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::get('/wedding-profile', [WeddingProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/wedding-profile', [WeddingProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/wedding-profile/sections', [WeddingProfileController::class, 'updateSections'])
        ->name('profile.sections.update');

    Route::put('/wedding-profile/sections/live-streaming', [WeddingProfileController::class, 'updateLiveStreamingSection'])
        ->name('profile.sections.live-streaming.update');

    Route::put('/wedding-profile/sections/wedding-gift', [WeddingProfileController::class, 'updateWeddingGiftSection'])
        ->name('profile.sections.wedding-gift.update');
    
    Route::put('/wedding-profile/sections/wedding-wish', [WeddingProfileController::class, 'updateWeddingWishSection'])
        ->name('profile.sections.wedding-wish.update');

    Route::put('/wedding-profile/sections/quote', [WeddingProfileController::class, 'updateQuoteSection'])
        ->name('profile.sections.quote.update');

    Route::put('/wedding-profile/sections/story', [WeddingProfileController::class, 'updateStorySection'])
        ->name('profile.sections.story.update');

    Route::put('/wedding-profile/sections/gallery', [WeddingProfileController::class, 'updateGallerySection'])
        ->name('profile.sections.gallery.update');

    Route::put('/wedding-profile/sections/video', [WeddingProfileController::class, 'updateVideoSection'])
        ->name('profile.sections.video.update');

    Route::put('/wedding-profile/sections/save-the-date', [WeddingProfileController::class, 'updateSaveDateSection'])
        ->name('profile.sections.save-date.update');

    Route::put('/wedding-profile/sections/agenda', [WeddingProfileController::class, 'updateAgendaSection'])
        ->name('profile.sections.agenda.update');

    Route::put('/wedding-profile/sections/dress-code', [WeddingProfileController::class, 'updateDressCodeSection'])
        ->name('profile.sections.dress-code.update');

    Route::put('/wedding-profile/sections/rsvp', [WeddingProfileController::class, 'updateRsvpSection'])
        ->name('profile.sections.rsvp.update');

    Route::put('/wedding-profile/sections/cover', [WeddingProfileController::class, 'updateCoverSection'])
        ->name('profile.sections.cover.update');

    Route::put('/wedding-profile/sections/couple', [WeddingProfileController::class, 'updateCoupleSection'])
        ->name('profile.sections.couple.update');

    Route::put('/wedding-profile/sections/footer', [WeddingProfileController::class, 'updateFooterSection'])
        ->name('profile.sections.footer.update');

    Route::resource('budget-items', BudgetItemController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::resource('guests', GuestController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::patch('/guests/{guest}/mark-sent', [GuestController::class, 'markSent'])
        ->name('guests.markSent');

    Route::delete('/guests/{guest}/tracking', [GuestController::class, 'resetTracking'])
        ->name('guests.resetTracking');

    Route::resource('checklists', ChecklistController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::patch('/checklists/{checklist}/toggle', [ChecklistController::class, 'toggle'])
        ->name('checklists.toggle');

    Route::get('/sync', [SyncController::class, 'index'])
        ->name('sync.index');

    Route::post('/sync/run', [SyncController::class, 'run'])
        ->name('sync.run');

    Route::post('/sync/export-all-web-to-staging', [SyncController::class, 'exportAllWebToStaging'])
        ->name('sync.exportAllWebToStaging');

    Route::patch('/sync/{syncDifference}/export-web-to-staging', [SyncController::class, 'exportWebToStaging'])
        ->name('sync.exportWebToStaging');

    Route::post('/sync/apply-all-sheet-to-web', [SyncController::class, 'applyAllSheetToWeb'])
        ->name('sync.applyAllSheetToWeb');

    Route::patch('/sync/{syncDifference}/apply-sheet-to-web', [SyncController::class, 'applySheetToWeb'])
        ->name('sync.applySheetToWeb');

    Route::patch('/sync/{syncDifference}/resolve', [SyncController::class, 'resolve'])
        ->name('sync.resolve');

    Route::patch('/sync/{syncDifference}/ignore', [SyncController::class, 'ignore'])
        ->name('sync.ignore');

});

Route::get('/u/{slug}', [PublicInvitationController::class, 'show'])
    ->name('invitation.show');
Route::get('/u/{slug}/g/{code}', [PublicInvitationController::class, 'showGuest'])
    ->name('invitation.guest');

Route::post('/u/{slug}/g/{code}/track', [PublicInvitationController::class, 'trackActivity'])
    ->name('invitation.guest.track');
    
Route::post('/u/{slug}/g/{code}/rsvp', [PublicInvitationController::class, 'submitRsvp'])
    ->name('invitation.rsvp');

Route::get('/templates/anselma-preview', [AnselmaPreviewController::class, 'show'])
    ->name('templates.anselma.preview');

Route::view('/templates/anselma-original-cover', 'templates.anselma.original-cover')
    ->name('templates.anselma.original-cover');

Route::get('/sync-v2-status', [WeddingSyncV2StatusController::class, 'index'])->name('sync-v2.status');
