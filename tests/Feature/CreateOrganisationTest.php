<?php

namespace Tests\Feature;

use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * New organisation (Management): a name and an optional logo, stored as WebP. You become its owner.
 */
class CreateOrganisationTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->organiser = User::factory()->create();
    }

    private function create(array $fields)
    {
        return $this->actingAs($this->organiser)->post($this->appUrl('/organisation'), ['name' => 'Cardiff ULSC', ...$fields]);
    }

    public function test_the_page_renders(): void
    {
        $this->actingAs($this->organiser)->get($this->appUrl('/organisation/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Manage/Organisations/Create'));
    }

    public function test_creating_one_makes_you_its_owner_and_opens_it(): void
    {
        $response = $this->create([]);

        $org = Organisation::sole();
        $response->assertRedirect(route('orgs.show', $org));
        $this->assertSame(['Cardiff ULSC', null], [$org->name, $org->logo]);
        $this->assertTrue($this->organiser->can('access', [$org, 'owner']));
    }

    public function test_a_logo_is_stored_as_webp_scaled_to_fit_with_its_transparency(): void
    {
        $this->create(['logo' => $this->transparentPng(1600, 400)])->assertSessionHasNoErrors();

        $path = Organisation::sole()->logo;
        $this->assertStringEndsWith('.webp', $path);
        $file = Storage::disk('public')->path($path);
        [$width, $height, $type] = getimagesize($file);
        $this->assertSame([512, 128, IMAGETYPE_WEBP], [$width, $height, $type]);

        $image = imagecreatefromwebp($file);
        $this->assertSame(127, imagecolorsforindex($image, imagecolorat($image, 0, 0))['alpha']);
    }

    public function test_small_logos_are_not_upscaled(): void
    {
        $this->create(['logo' => UploadedFile::fake()->image('logo.jpg', 200, 300)]);

        [$width, $height] = getimagesize(Storage::disk('public')->path(Organisation::sole()->logo));
        $this->assertSame([200, 300], [$width, $height]);
    }

    public function test_only_png_jpeg_and_webp_logos_are_accepted(): void
    {
        $this->create(['logo' => UploadedFile::fake()->image('logo.gif')])->assertSessionHasErrors('logo');

        $this->assertSame(0, Organisation::count());
    }

    public function test_changing_the_logo_on_the_edit_page_stores_webp_and_deletes_the_old_one(): void
    {
        $this->create(['logo' => UploadedFile::fake()->image('one.png', 300, 300)]);
        $org = Organisation::sole();
        $old = $org->logo;

        $this->actingAs($this->organiser)
            ->postJson($this->appUrl('/organisation/' . $org->id), [
                '_method' => 'PATCH',
                'name' => 'Cardiff ULSC',
                'logo' => UploadedFile::fake()->image('two.jpg', 300, 300),
            ])
            ->assertOk();

        $new = $org->fresh()->logo;
        $this->assertNotSame($old, $new);
        $this->assertStringEndsWith('.webp', $new);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($new);
    }

    private function transparentPng(int $width, int $height): UploadedFile
    {
        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));

        $path = tempnam(sys_get_temp_dir(), 'logo') . '.png';
        imagepng($image, $path);

        return new UploadedFile($path, 'logo.png', 'image/png', null, true);
    }

    public function test_an_inertia_visit_is_sent_on_to_the_organisation_page(): void
    {
        $this->actingAs($this->organiser)
            ->post($this->appUrl('/organisation'), ['name' => 'Cardiff ULSC'], ['X-Inertia' => 'true'])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', route('orgs.show', Organisation::sole()));
    }

    public function test_a_logo_that_cannot_be_read_leaves_no_organisation_behind(): void
    {
        // A PNG's signature, then nothing GD can decode
        $path = tempnam(sys_get_temp_dir(), 'logo') . '.png';
        file_put_contents($path, "\x89PNG\r\n\x1a\n" . str_repeat("\0", 64));

        $this->create(['logo' => new UploadedFile($path, 'logo.png', 'image/png', null, true)])
            ->assertSessionHasErrors('logo');

        $this->assertSame(0, Organisation::count());
    }

    public function test_names_are_unique(): void
    {
        $this->create([]);

        $this->create([])->assertSessionHasErrors(['name' => 'An organisation with this name already exists.']);
    }
}
