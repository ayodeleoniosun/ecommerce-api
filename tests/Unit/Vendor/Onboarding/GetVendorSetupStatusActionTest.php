<?php

namespace Tests\Unit\Vendor\Onboarding;

use App\Domain\Vendor\Onboarding\Actions\GetVendorSetupStatusAction;
use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\User\User;
use Mockery;

beforeEach(function () {
    $this->vendorContactRepo = Mockery::mock(VendorContactInformationRepositoryInterface::class);
    $this->vendorLegalRepo = Mockery::mock(VendorLegalInformationRepositoryInterface::class);
    $this->vendorPaymentRepo = Mockery::mock(VendorPaymentInformationRepositoryInterface::class);
    $this->vendorBusinessRepo = Mockery::mock(VendorBusinessInformationRepositoryInterface::class);
    $this->user = User::factory()->create();
});

it('should get vendor setup status', function () {
    $this->vendorContactRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $this->vendorBusinessRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $this->vendorPaymentRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $this->vendorLegalRepo->shouldReceive('isCompleted')
        ->once()
        ->with($this->user->id)
        ->andReturn(true);

    $status = new GetVendorSetupStatusAction(
        $this->vendorContactRepo,
        $this->vendorBusinessRepo,
        $this->vendorPaymentRepo,
        $this->vendorLegalRepo
    );

    $response = $status->execute($this->user->id);

    expect($response['completed_contact_information'])->toBe(true)
        ->and($response['completed_business_information'])->toBe(true)
        ->and($response['completed_payment_information'])->toBe(true)
        ->and($response['completed_legal_information'])->toBe(true);
});
