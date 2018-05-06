<?php

namespace Core\Interfaces\Http;

interface ResponseInterface
{
    /**
     * Send http response.
     *
     * @return void
     */
    public function send();

    /**
     * Set response status.
     *
     * @param int $statusCode
     *
     * @return Core\Http\Response
     */
    public function setStatusCode(int $statusCode);

    /**
     * Get application default response headers.
     *
     * @return array
     */
    public static function getDefaultHeaders();

    /**
     * Verify wheter status is valid.
     *
     * @param int $status Status is valid
     *
     * @return bool
     */
    public static function validStatus(int $status);

    /**
     * Verify wheter an HTTP status is success type.
     *
     * @param int $status HTTP status code
     *
     * @return bool
     */
    public static function isSuccessfull(int $status);

    /**
     * Verify wheter an HTTP status is reading type.
     *
     * @param int $status HTTP status code
     *
     * @return bool
     */
    public static function isReading(int $status);

    /**
     * Verify wheter an HTTP status is redirect type.
     *
     * @param int $status HTTP status code
     *
     * @return bool
     */
    public static function isRedirect(int $status);

    /**
     * Verify wheter an HTTP status is client error type.
     *
     * @param int $status HTTP status code
     *
     * @return bool
     */
    public static function isClientError(int $status);

    /**
     * Verify wheter an HTTP status is server error type.
     *
     * @param int $status HTTP status code
     *
     * @return bool
     */
    public static function isServerError(int $status);
}
