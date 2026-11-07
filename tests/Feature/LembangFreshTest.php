<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Harvest\Models\Harvest;
use App\Modules\Logistics\Models\LogisticsShipment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class LembangFreshTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_public_pages_can_be_rendered(): void
    {
        $this->get(route('home'))->assertStatus(200);
        $this->get(route('gis.index'))->assertStatus(200);
        $this->get(route('matching.index'))->assertStatus(200);
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('register'))->assertStatus(200);
    }

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('harvests.index'))->assertRedirect(route('login'));
        $this->get(route('logistics.index'))->assertRedirect(route('login'));
        $this->get(route('profile'))->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_for_all_roles(): void
    {
        $roles = ['admin', 'petani', 'pembeli', 'mitra'];
        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            $this->assertNotNull($user, "User with role {$role} must exist");
            $response = $this->actingAs($user)->get(route('dashboard'));
            $response->assertStatus(200);
            $response->assertSee($user->name);
        }
    }

    public function test_user_authentication_flow(): void
    {

        $email = 'petani_baru_' . time() . '@lembangfresh.test';
        $registerResponse = $this->post(route('register.post'), [
            'name' => 'Petani Baru Lembang',
            'email' => $email,
            'role' => 'petani',
            'phone' => '081234567899',
            'address' => 'Desa Cikidang No. 99, Lembang',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $registerResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret123', $user->password));

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));
        $this->assertGuest();

        $loginResponse = $this->post(route('login.post'), [
            'email' => $email,
            'password' => 'secret123',
        ]);
        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_role_based_authorization(): void
    {
        $petani = User::where('role', 'petani')->first();
        $mitra = User::where('role', 'mitra')->first();
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($petani)->get(route('logistics.index'))->assertStatus(403);

        $this->actingAs($mitra)->get(route('harvests.index'))->assertStatus(403);

        $this->actingAs($admin)->get(route('harvests.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('logistics.index'))->assertStatus(200);
    }

    public function test_synchronous_crud_with_file_upload_and_deletion(): void
    {
        $petani = User::where('role', 'petani')->first();

        $photo = UploadedFile::fake()->image('wortel_super.jpg', 600, 400);

        $storeResponse = $this->actingAs($petani)->post(route('harvests.store'), [
            'commodity' => 'Wortel Super Test Unit',
            'variety' => 'Super Brastagi',
            'quantity' => 200,
            'price_per_kg' => 6000,
            'grade' => 'A',
            'harvest_date' => '2026-11-01',
            'ready_date' => '2026-11-02',
            'photo' => $photo,
            'description' => 'Test unit harvest',
        ]);

        $storeResponse->assertRedirect(route('harvests.index'));

        $harvest = Harvest::where('commodity', 'Wortel Super Test Unit')->first();
        $this->assertNotNull($harvest);
        $this->assertNotNull($harvest->photo_path);
        Storage::disk('public')->assertExists($harvest->photo_path);

        $this->actingAs($petani)->get(route('harvests.show', $harvest->id))->assertStatus(200);

        $newPhoto = UploadedFile::fake()->image('wortel_baru.jpg', 600, 400);
        $oldPath = $harvest->photo_path;

        $updateResponse = $this->actingAs($petani)->put(route('harvests.update', $harvest->id), [
            'commodity' => 'Wortel Super Updated',
            'variety' => 'Super Brastagi',
            'quantity' => 180,
            'price_per_kg' => 6500,
            'grade' => 'A',
            'harvest_date' => '2026-11-01',
            'ready_date' => '2026-11-02',
            'status' => 'tersedia',
            'photo' => $newPhoto,
        ]);

        $updateResponse->assertRedirect(route('harvests.index'));
        $harvest->refresh();
        $this->assertEquals('Wortel Super Updated', $harvest->commodity);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($harvest->photo_path);

        $currentPath = $harvest->photo_path;
        $deleteResponse = $this->actingAs($petani)->delete(route('harvests.destroy', $harvest->id));
        $deleteResponse->assertRedirect(route('harvests.index'));

        $this->assertDatabaseMissing('harvests', ['id' => $harvest->id]);
        Storage::disk('public')->assertMissing($currentPath);
    }

    public function test_ajax_crud_with_document_upload_and_reopen(): void
    {
        $mitra = User::where('role', 'mitra')->first();

        $document = UploadedFile::fake()->image('surat_jalan_test.png', 800, 600);

        $response = $this->actingAs($mitra)->postJson(route('logistics.api.store'), [
            'sender_name' => 'Kebun Cikidang Pak Agus',
            'receiver_name' => 'Resto Sindang Reret Lembang',
            'commodity_info' => 'Wortel & Tomat (250 kg)',
            'weight_kg' => 250,
            'distance_km' => 3.8,
            'shipping_method' => 'mitra_pengangkut',
            'status' => 'diproses',
            'document_file' => $document,
            'notes' => 'Catatan tes unit',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $shipmentId = $response->json('data.id');
        $this->assertNotNull($shipmentId);

        $shipment = LogisticsShipment::find($shipmentId);
        $this->assertNotNull($shipment);
        $this->assertNotNull($shipment->document_path);
        Storage::disk('public')->assertExists($shipment->document_path);

        $readResponse = $this->actingAs($mitra)->getJson(route('logistics.api.show', $shipmentId));
        $readResponse->assertStatus(200);
        $readResponse->assertJsonPath('success', true);
        $readResponse->assertJsonPath('data.sender_name', 'Kebun Cikidang Pak Agus');

        $downloadResponse = $this->actingAs($mitra)->get(route('logistics.download', $shipmentId));
        $downloadResponse->assertStatus(200);

        $updateResponse = $this->actingAs($mitra)->postJson(route('logistics.api.update', $shipmentId), [
            'sender_name' => 'Kebun Cikidang Pak Agus (Updated)',
            'receiver_name' => 'Resto Sindang Reret Lembang',
            'commodity_info' => 'Wortel & Tomat (250 kg)',
            'weight_kg' => 250,
            'distance_km' => 3.8,
            'shipping_method' => 'mitra_pengangkut',
            'status' => 'dikirim',
        ]);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('success', true);

        $docPath = $shipment->document_path;
        $deleteResponse = $this->actingAs($mitra)->deleteJson(route('logistics.api.destroy', $shipmentId));
        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJsonPath('success', true);

        $this->assertDatabaseMissing('logistics_shipments', ['id' => $shipmentId]);
        Storage::disk('public')->assertMissing($docPath);
    }

    public function test_order_creation_with_mitra_integrates_with_logistics(): void
    {
        $buyer = User::where('role', 'pembeli')->first();
        $harvest = Harvest::where('status', 'tersedia')->first() ?? Harvest::first();
        if (!$harvest) {
            $farmer = User::where('role', 'petani')->first();
            $harvest = Harvest::create([
                'farmer_id' => $farmer->id,
                'commodity' => 'Wortel Brastagi Super',
                'grade' => 'A',
                'marketing_channel' => 'Pasar Modern',
                'quantity' => 100,
                'price_per_kg' => 12000,
                'harvest_date' => now()->toDateString(),
                'status' => 'tersedia',
            ]);
        } else {
            $harvest->update(['quantity' => 100, 'status' => 'tersedia']);
        }

        $initialQuantity = $harvest->quantity;

        $response = $this->actingAs($buyer)->post(route('order.create', $harvest->id), [
            'quantity' => 15,
            'delivery_method' => 'mitra_pengangkut',
        ]);

        $response->assertRedirect(route('dashboard'));

        $order = \App\Models\Order::where('buyer_id', $buyer->id)
            ->where('harvest_id', $harvest->id)
            ->latest()
            ->first();

        $this->assertNotNull($order);
        $this->assertEquals(15, $order->quantity);
        $this->assertEquals('mitra_pengangkut', $order->delivery_method);

        $shipment = LogisticsShipment::where('notes', 'like', '%' . $order->order_number . '%')->first();
        $this->assertNotNull($shipment);
        $this->assertEquals(15, $shipment->weight_kg);
        $this->assertEquals('mitra_pengangkut', $shipment->shipping_method);

        $harvest->refresh();
        $this->assertEquals($initialQuantity - 15, $harvest->quantity);
    }
}

