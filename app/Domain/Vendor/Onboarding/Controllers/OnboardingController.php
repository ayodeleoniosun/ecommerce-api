<?php

namespace App\Domain\Vendor\Onboarding\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorBusinessInformation;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorContactInformation;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorLegalInformation;
use App\Domain\Vendor\Onboarding\Actions\CreateVendorPaymentInformation;
use App\Domain\Vendor\Onboarding\Actions\GetVendorSetupStatus;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorBusinessInformationDto;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorContactInformationDto;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorLegalInformationDto;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorPaymentInformationDto;
use App\Domain\Vendor\Onboarding\Requests\VendorBusinessInformationRequest;
use App\Domain\Vendor\Onboarding\Requests\VendorContactInformationRequest;
use App\Domain\Vendor\Onboarding\Requests\VendorLegalInformationRequest;
use App\Domain\Vendor\Onboarding\Requests\VendorPaymentInformationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController
{
    public function __construct(
        private readonly CreateVendorContactInformation $createVendorContactInformation,
        private readonly CreateVendorBusinessInformation $createVendorBusinessInformation,
        private readonly CreateVendorLegalInformation $createVendorLegalInformation,
        private readonly CreateVendorPaymentInformation $createVendorPaymentInformation,
        private readonly GetVendorSetupStatus $setupStatus,
    ) {}

    public function status(Request $request): JsonResponse
    {
        try {
            $data = $this->setupStatus->execute(auth()->user()->id);

            return ApiResponse::success('Setup status successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function contact(VendorContactInformationRequest $request): JsonResponse
    {
        $contact = CreateVendorContactInformationDto::fromRequest($request);

        try {
            $data = $this->createVendorContactInformation->execute($contact);

            return ApiResponse::success('Vendor contact information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function business(VendorBusinessInformationRequest $request): JsonResponse
    {
        $business = CreateVendorBusinessInformationDto::fromRequest($request);

        try {
            $data = $this->createVendorBusinessInformation->execute($business);

            return ApiResponse::success('Vendor business information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function legal(VendorLegalInformationRequest $request): JsonResponse
    {
        $legal = CreateVendorLegalInformationDto::fromRequest($request);

        try {
            $data = $this->createVendorLegalInformation->execute($legal);

            return ApiResponse::success('Vendor legal information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function payment(VendorPaymentInformationRequest $request): JsonResponse
    {
        $payment = CreateVendorPaymentInformationDto::fromRequest($request);

        try {
            $data = $this->createVendorPaymentInformation->execute($payment);

            return ApiResponse::success('Vendor payment information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
