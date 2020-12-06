<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Prepare success response
     *
     * @param string $apiStatus
     * @param string $apiMessage
     * @param array $apiData
     * @param bool $convertNumeric - To specify whether to transform number strings into int type
     * @return Illuminate\Http\JsonResponse
     */
    public function success(
        string $apiStatus = '',
        string $apiMessage = '',
        array $apiData = [],
        bool $convertNumeric = true
    ): JsonResponse {
        $response['status'] = $apiStatus;

        if (!empty($apiData)) {
            $response['data'] = $apiData;
        }

        if ($apiMessage) {
            $response['message'] = $apiMessage;
        }

        return response()->json($response, $apiStatus, [], $convertNumeric ? JSON_NUMERIC_CHECK: null);
    }

    /**
     * Prepare error response
     *
     * @param string $statusCode
     * @param string $statusType
     * @param string $customErrorCode
     * @param string $customErrorMessage
     * @return Illuminate\Http\JsonResponse
     */
    public function error(
        string $statusCode = '',
        string $statusType = '',
        string $customErrorMessage = ''
    ): JsonResponse {
        $response['status'] = $statusCode;
        $response['type'] = $statusType;
        $response['message'] = $customErrorMessage;
        $data["errors"][] = $response;

        return response()->json($data, $statusCode, [], JSON_NUMERIC_CHECK);
    }
}
