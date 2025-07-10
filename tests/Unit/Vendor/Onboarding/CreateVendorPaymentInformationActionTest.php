<?php

namespace Tests\Unit\Vendor\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorPaymentInformationAction;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorPaymentInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\Vendor\VendorPaymentInformation;
use Mockery;

beforeEach(function () {
    $this->vendorPaymentRepo = Mockery::mock(VendorPaymentInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->vendorPaymentDto = new CreateVendorPaymentInformationDto(
        $this->user->id,
        'John Doe',
        '0123456789',
        '033',
        'Central Bank of Nigeria',
        'O12345'
    );

    $this->contactInformation = VendorPaymentInformation::factory()->create([
        'user_id' => $this->vendorPaymentDto->getUserId(),
        'account_name' => $this->vendorPaymentDto->getAccountName(),
        'account_number' => $this->vendorPaymentDto->getAccountNumber(),
        'bank_code' => $this->vendorPaymentDto->getBankCode(),
        'bank_name' => $this->vendorPaymentDto->getBankName(),
        'swift_code' => $this->vendorPaymentDto->getSwiftCode(),
    ]);

    $this->createVendorPaymentInformation = new CreateVendorPaymentInformationAction($this->vendorPaymentRepo);
});

it('should throw an exception if payment information exist for another vendor', function () {
    $this->vendorPaymentRepo->shouldReceive('findOtherPayment')
        ->once()
        ->with(
            $this->vendorPaymentDto->getAccountNumber(),
            $this->vendorPaymentDto->getBankCode(),
            $this->vendorPaymentDto->getUserId(),
        )
        ->andReturn($this->contactInformation);

    $this->createVendorPaymentInformation->execute($this->vendorPaymentDto);
})->throws(ConflictHttpException::class, 'Payment information exist for another vendor');

it('should create a new vendor contact information if not exist', function () {
    $this->vendorPaymentRepo->shouldReceive('findOtherPayment')
        ->once()
        ->with(
            $this->vendorPaymentDto->getAccountNumber(),
            $this->vendorPaymentDto->getBankCode(),
            $this->vendorPaymentDto->getUserId(),
        )
        ->andReturn(null);

    $this->vendorPaymentRepo->shouldReceive('create')
        ->once()
        ->with($this->vendorPaymentDto)
        ->andReturn($this->contactInformation);

    $response = $this->createVendorPaymentInformation->execute($this->vendorPaymentDto);

    expect($response)->toBeInstanceOf(VendorPaymentInformation::class)
        ->and($response->user_id)->toBe($this->vendorPaymentDto->getUserId())
        ->and($response->account_name)->toBe($this->vendorPaymentDto->getAccountName())
        ->and($response->account_number)->toBe($this->vendorPaymentDto->getAccountNumber())
        ->and($response->bank_code)->toBe($this->vendorPaymentDto->getBankCode())
        ->and($response->bank_name)->toBe($this->vendorPaymentDto->getBankName())
        ->and($response->swift_code)->toBe($this->vendorPaymentDto->getSwiftCode());
});
