<?php

namespace App\Application\Http\Onboarding\Controller;

use App\Application\Actions\Onboarding\CreateSellerBusinessInformation;
use App\Application\Actions\Onboarding\CreateSellerContactInformation;
use App\Application\Actions\Onboarding\CreateSellerLegalInformation;
use App\Application\Actions\Onboarding\CreateSellerPaymentInformation;
use App\Application\Actions\Onboarding\GetSellerSetupStatus;
use App\Application\Http\Onboarding\Requests\SellerBusinessInformationRequest;
use App\Application\Http\Onboarding\Requests\SellerContactInformationRequest;
use App\Application\Http\Onboarding\Requests\SellerLegalInformationRequest;
use App\Application\Http\Onboarding\Requests\SellerPaymentInformationRequest;
use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Onboarding\Dtos\CreateSellerBusinessInformationDto;
use App\Domain\Onboarding\Dtos\CreateSellerContactInformationDto;
use App\Domain\Onboarding\Dtos\CreateSellerLegalInformationDto;
use App\Domain\Onboarding\Dtos\CreateSellerPaymentInformationDto;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController
{
    public function __construct(
        private readonly CreateSellerContactInformation $createSellerContactInformation,
        private readonly CreateSellerBusinessInformation $createSellerBusinessInformation,
        private readonly CreateSellerLegalInformation $createSellerLegalInformation,
        private readonly CreateSellerPaymentInformation $createSellerPaymentInformation,
        private readonly GetSellerSetupStatus $setupStatus,
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

    public function contact(SellerContactInformationRequest $request): JsonResponse
    {
        $contact = CreateSellerContactInformationDto::fromRequest($request);

        try {
            $data = $this->createSellerContactInformation->execute($contact);

            return ApiResponse::success('Seller contact information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function business(SellerBusinessInformationRequest $request): JsonResponse
    {
        $business = CreateSellerBusinessInformationDto::fromRequest($request);

        try {
            $data = $this->createSellerBusinessInformation->execute($business);

            return ApiResponse::success('Seller business information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function legal(SellerLegalInformationRequest $request): JsonResponse
    {
        $legal = CreateSellerLegalInformationDto::fromRequest($request);

        try {
            $data = $this->createSellerLegalInformation->execute($legal);

            return ApiResponse::success('Seller legal information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function payment(SellerPaymentInformationRequest $request): JsonResponse
    {
        $payment = CreateSellerPaymentInformationDto::fromRequest($request);

        try {
            $data = $this->createSellerPaymentInformation->execute($payment);

            return ApiResponse::success('Seller payment information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
