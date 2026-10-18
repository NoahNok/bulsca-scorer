<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $official;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->official = User::factory()->create(['judge_only' => true]);
    }

    private function upload(UploadedFile $file)
    {
        return $this->actingAs($this->official)
            ->from(route('judge.account'))
            ->post(route('judge.account.photo'), ['photo' => $file]);
    }

    public function test_uploading_saves_a_small_square_webp_and_jpeg(): void
    {
        $this->upload(UploadedFile::fake()->image('me.jpg', 1200, 800))
            ->assertRedirect(route('judge.account'));

        $stem = $this->official->fresh()->profile_photo;
        $this->assertStringStartsWith('avatars/' . $this->official->id . '-', $stem);

        foreach (['webp' => IMAGETYPE_WEBP, 'jpg' => IMAGETYPE_JPEG] as $extension => $type) {
            $path = Storage::disk('public')->path("$stem.$extension");
            [$width, $height, $actualType] = getimagesize($path);

            $this->assertSame([192, 192, $type], [$width, $height, $actualType]);
        }
    }

    public function test_pngs_are_accepted(): void
    {
        $this->upload(UploadedFile::fake()->image('me.png', 300, 600))->assertSessionHasNoErrors();

        $this->assertNotNull($this->official->fresh()->profile_photo);
    }

    public function test_replacing_the_photo_deletes_the_old_files(): void
    {
        $this->upload(UploadedFile::fake()->image('one.jpg', 400, 400));
        $old = $this->official->fresh()->profile_photo;

        $this->upload(UploadedFile::fake()->image('two.jpg', 400, 400));
        $new = $this->official->fresh()->profile_photo;

        $this->assertNotSame($old, $new);
        Storage::disk('public')->assertMissing(["$old.webp", "$old.jpg"]);
        Storage::disk('public')->assertExists(["$new.webp", "$new.jpg"]);
    }

    public function test_removing_the_photo_deletes_the_files(): void
    {
        $this->upload(UploadedFile::fake()->image('me.jpg', 400, 400));
        $stem = $this->official->fresh()->profile_photo;

        $this->actingAs($this->official)
            ->from(route('judge.account'))
            ->delete(route('judge.account.photo.destroy'))
            ->assertRedirect(route('judge.account'));

        $this->assertNull($this->official->fresh()->profile_photo);
        Storage::disk('public')->assertMissing(["$stem.webp", "$stem.jpg"]);
    }

    public function test_deleting_the_user_deletes_their_photo(): void
    {
        $this->upload(UploadedFile::fake()->image('me.jpg', 400, 400));
        $stem = $this->official->fresh()->profile_photo;

        $this->official->delete();

        Storage::disk('public')->assertMissing(["$stem.webp", "$stem.jpg"]);
    }

    public function test_files_that_are_not_images_are_rejected(): void
    {
        $this->upload(UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'))
            ->assertSessionHasErrors('photo');

        $this->assertNull($this->official->fresh()->profile_photo);
    }

    public function test_photos_over_10mb_are_rejected(): void
    {
        $this->upload(UploadedFile::fake()->image('huge.jpg')->size(11 * 1024))
            ->assertSessionHasErrors('photo');
    }

    public function test_the_account_page_includes_the_photo(): void
    {
        $this->upload(UploadedFile::fake()->image('me.jpg', 400, 400));

        $this->actingAs($this->official)
            ->get(route('judge.account'))
            ->assertInertia(fn ($page) => $page
                ->where('photo', $this->official->fresh()->profilePhotoUrls()));
    }

    public function test_guests_cannot_upload_a_photo(): void
    {
        $this->post(route('judge.account.photo'), ['photo' => UploadedFile::fake()->image('me.jpg')])
            ->assertRedirect();

        $this->assertEmpty(Storage::disk('public')->allFiles());
    }
}
