<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Shared\Enum\UserEnum;
use App\Infrastructure\Models\SellerBusinessInformation;
use App\Infrastructure\Models\SellerContactInformation;
use App\Infrastructure\Models\SellerLegalInformation;
use App\Infrastructure\Models\SellerPaymentInformation;
use App\Infrastructure\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => UserEnum::ACTIVE->value,
    ]);

    $this->actingAs($this->user, 'sanctum');
});

describe('create seller contact information', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'contact_name' => 'John Doe',
        ];

        $response = $this->postJson('/api/seller/setup/contact', $payload);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The contact phone number field is required.');
    });

    it('should throw an error if contact email already exist', function () {
        $sellerContactInformation = SellerContactInformation::factory()->create([
            'email' => 'johndoe@xyz.com',
        ]);

        $payload = [
            'contact_name' => 'John Doe',
            'contact_phone_number' => '012345678',
            'contact_email' => $sellerContactInformation->email,
            'country' => 'Nigeria',
            'state' => 'Oyo',
            'city' => 'Ibadan',
            'address' => 'Bodija, Ibadan',
        ];

        $response = $this->postJson('/api/seller/setup/contact', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CONFLICT);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Email address exist for another seller');
    });

    it('should throw an error if contact phone number already exist', function () {
        $sellerContactInformation = SellerContactInformation::factory()->create([
            'phone_number' => '012345678',
        ]);

        $payload = [
            'contact_name' => 'John Doe',
            'contact_phone_number' => $sellerContactInformation->phone_number,
            'contact_email' => 'johndoe@xyz.com',
            'country' => 'Nigeria',
            'state' => 'Oyo',
            'city' => 'Ibadan',
            'address' => 'Bodija, Ibadan',
        ];

        $response = $this->postJson('/api/seller/setup/contact', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CONFLICT);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Phone number exist for another seller');
    });

    it('should create or update a contact details', function () {
        $payload = [
            'contact_name' => 'John Doe',
            'contact_phone_number' => '012345678',
            'contact_email' => 'johndoe@xyz.com',
            'country' => 'Nigeria',
            'state' => 'Oyo',
            'city' => 'Ibadan',
            'address' => 'Bodija, Ibadan',
        ];

        $response = $this->postJson('/api/seller/setup/contact', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'name',
                    'email',
                    'phone_number',
                    'country',
                    'state',
                    'city',
                    'address',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Seller contact information successfully updated')
            ->and($content->data->name)->toBe($payload['contact_name'])
            ->and($content->data->email)->toBe($payload['contact_email'])
            ->and($content->data->phone_number)->toBe($payload['contact_phone_number'])
            ->and($content->data->country)->toBe($payload['country'])
            ->and($content->data->state)->toBe($payload['state'])
            ->and($content->data->city)->toBe($payload['city'])
            ->and($content->data->address)->toBe($payload['address']);
    });
});

describe('create seller business information', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'company_name' => 'Company name',
        ];

        $response = $this->postJson('/api/seller/setup/business', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The description field is required.');
    });

    it('should throw an error if business name already exist', function () {
        $sellerBusinessInformation = SellerBusinessInformation::factory()->create([
            'name' => 'Company name',
        ]);

        $payload = [
            'company_name' => $sellerBusinessInformation->name,
            'description' => 'This is the description',
            'registration_number' => '1234567',
            'tax_identification_number' => '123456789',
        ];

        $response = $this->postJson('/api/seller/setup/business', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CONFLICT);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Business name exist for another seller');
    });

    it('should throw an error if registration number already exist', function () {
        $sellerBusinessInformation = SellerBusinessInformation::factory()->create([
            'registration_number' => '1234567',
        ]);

        $payload = [
            'company_name' => 'Company name',
            'description' => 'This is the description',
            'registration_number' => $sellerBusinessInformation->registration_number,
            'tax_identification_number' => '123456789',
        ];

        $response = $this->postJson('/api/seller/setup/business', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CONFLICT);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Registration number exist for another seller');
    });

    it('should throw an error if tax identification number already exist', function () {
        $sellerBusinessInformation = SellerBusinessInformation::factory()->create([
            'tax_identification_number' => '123456789',
        ]);

        $payload = [
            'company_name' => 'Company name',
            'description' => 'This is the description',
            'registration_number' => '1234456',
            'tax_identification_number' => $sellerBusinessInformation->tax_identification_number,
        ];

        $response = $this->postJson('/api/seller/setup/business', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CONFLICT);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Tax identification number exist for another seller');
    });

    it('should create or update a seller business record even if certificate is not uploaded', function () {
        $payload = [
            'company_name' => 'Company name',
            'description' => 'This is the description',
            'registration_number' => '1234456',
            'tax_identification_number' => '12345678',
        ];

        $response = $this->postJson('/api/seller/setup/business', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'name',
                    'description',
                    'registration_number',
                    'tax_identification_number',
                    'status',
                    'certificate_path',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Seller business information successfully updated')
            ->and($content->data->name)->toBe($payload['company_name'])
            ->and($content->data->description)->toBe($payload['description'])
            ->and($content->data->registration_number)->toBe($payload['registration_number'])
            ->and($content->data->tax_identification_number)->toBe($payload['tax_identification_number'])
            ->and($content->data->certificate_path)->toBe(null);
    });
});

describe('create seller legal information', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'fullname' => 'John Doe',
        ];

        $response = $this->postJson('/api/seller/setup/legal', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The email field is required.');
    });

    it('should throw an error if email already exist', function () {
        $sellerLegalInformation = SellerLegalInformation::factory()->create([
            'email' => 'johndoe@xyz.com',
        ]);

        $payload = [
            'fullname' => 'John Doe',
            'email' => $sellerLegalInformation->email,
            'legal_certificate_path' => UploadedFile::fake()->image('legal-certificate.jpg'),
        ];

        $response = $this->postJson('/api/seller/setup/legal', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CONFLICT);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Legal email address exist for another seller');
    });

    it('should create or update a seller legal record', function () {
        $payload = [
            'fullname' => 'John Doe',
            'email' => 'johndoe@xyz.com',
            'legal_certificate_path' => UploadedFile::fake()->image('legal-certificate.jpg'),
        ];

        $response = $this->postJson('/api/seller/setup/legal', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'fullname',
                    'email',
                    'certificate_path',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Seller legal information successfully updated')
            ->and($content->data->fullname)->toBe($payload['fullname'])
            ->and($content->data->email)->toBe($payload['email']);
    });
});

describe('create seller payment information', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'account_name' => 'John Doe',
        ];

        $response = $this->postJson('/api/seller/setup/payment', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The account number field is required.');
    });

    it('should create or update seller payment information', function () {
        $payload = [
            'account_name' => 'John Doe',
            'account_number' => '01234567',
            'bank_name' => 'Test Bank',
            'bank_code' => '033',
            'swift_code' => '123456',
        ];

        $response = $this->postJson('/api/seller/setup/payment', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'account_name',
                    'account_number',
                    'bank_code',
                    'bank_name',
                    'swift_code',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Seller payment information successfully updated')
            ->and($content->data->account_name)->toBe($payload['account_name'])
            ->and($content->data->account_number)->toBe($payload['account_number'])
            ->and($content->data->bank_code)->toBe($payload['bank_code'])
            ->and($content->data->bank_name)->toBe($payload['bank_name'])
            ->and($content->data->swift_code)->toBe($payload['swift_code']);
    });
});

describe('get seller setup status', function () {
    it('should return an array of of seller setup status', function () {
        SellerContactInformation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        SellerBusinessInformation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        SellerLegalInformation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        SellerPaymentInformation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/seller/setup/status');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'completed_business_information',
                    'completed_contact_information',
                    'completed_legal_information',
                    'completed_payment_information',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Setup status successfully retrieved')
            ->and($content->data->completed_contact_information)->toBe(true)
            ->and($content->data->completed_business_information)->toBe(true)
            ->and($content->data->completed_legal_information)->toBe(true)
            ->and($content->data->completed_payment_information)->toBe(true);
    });
});
